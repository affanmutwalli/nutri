<?php
$selected = "change_password.php";
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header('Location: index.php');
    exit();
}

$error_msg = '';
$success_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $current_password_hash = $_POST['current_p'] ?? '';
    $new_password_hash = $_POST['new_p'] ?? '';
    
    // Validation
    if (empty($current_password_hash) || empty($new_password_hash)) {
        $error_msg = 'All fields are required.';
    } elseif (strlen($new_password_hash) != 128) {
        $error_msg = 'Invalid password format.';
    } elseif ($new_password !== $confirm_password) {
        $error_msg = 'New password and confirmation do not match.';
    } else {
        // Get current user data
        $user_email = $_SESSION['email'] ?? '';
        if (empty($user_email)) {
            $error_msg = 'Session error. Please login again.';
        } else {
            // Verify current password
            $stmt = $mysqli->prepare("SELECT id, password, salt FROM members WHERE email = ?");
            $stmt->bind_param('s', $user_email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $expected_current = hash('sha512', $current_password_hash . $user['salt']);
                
                if ($user['password'] === $expected_current) {
                    // Current password is correct, update to new password
                    $new_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
                    $final_new_password = hash('sha512', $new_password_hash . $new_salt);
                    
                    $update_stmt = $mysqli->prepare("UPDATE members SET password = ?, salt = ? WHERE email = ?");
                    $update_stmt->bind_param('sss', $final_new_password, $new_salt, $user_email);
                    
                    if ($update_stmt->execute()) {
                        $success_msg = 'Password changed successfully!';
                        // Log the password change
                        error_log("OMS Password changed for user: " . $user_email);
                    } else {
                        $error_msg = 'Error updating password. Please try again.';
                        error_log("OMS Password change failed for user: " . $user_email . " - " . $mysqli->error);
                    }
                } else {
                    $error_msg = 'Current password is incorrect.';
                    error_log("OMS Failed password change attempt for user: " . $user_email . " - incorrect current password");
                }
            } else {
                $error_msg = 'User not found.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password | MyNutrify OMS</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    
    <script src="js/sha512.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="includes/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="dashboard.php" class="brand-link">
            <span class="brand-text font-weight-light">MyNutrify OMS</span>
        </a>
        <div class="sidebar">
            <?php include("components/sidebar.php"); ?>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Change Password</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Change Password</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-lock"></i> Change Your Password
                                </h3>
                            </div>
                            
                            <form id="changePasswordForm" method="post" novalidate>
                                <div class="card-body">
                                    <?php if (!empty($error_msg)): ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_msg); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($success_msg)): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-key"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Password should be at least 6 characters long and contain at least one number, one lowercase and one uppercase letter.
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-key"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="button" class="btn btn-primary" onclick="validateAndHashPasswords()">
                                        <i class="fas fa-save"></i> Change Password
                                    </button>
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2024 MyNutrify.</strong> All rights reserved.
    </footer>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
// Debug: Check if SHA512 is loaded
console.log('SHA512 function available:', typeof hex_sha512);

function validateAndHashPasswords() {
    console.log('validateAndHashPasswords called');
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Validation
    if (currentPassword === '' || newPassword === '' || confirmPassword === '') {
        alert('All fields are required.');
        return false;
    }
    
    if (newPassword.length < 6) {
        alert('New password must be at least 6 characters long.');
        return false;
    }
    
    // Password strength validation
    const passwordRegex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
    if (!passwordRegex.test(newPassword)) {
        alert('Password must contain at least one number, one lowercase and one uppercase letter.');
        return false;
    }
    
    if (newPassword !== confirmPassword) {
        alert('New password and confirmation do not match.');
        return false;
    }
    
    if (currentPassword === newPassword) {
        alert('New password must be different from current password.');
        return false;
    }
    
    // Hash passwords
    if (typeof hex_sha512 === 'function') {
        // Create hidden fields for hashed passwords
        const form = document.getElementById('changePasswordForm');
        
        // Remove any existing hidden fields
        const existingCurrentP = form.querySelector('input[name="current_p"]');
        const existingNewP = form.querySelector('input[name="new_p"]');
        if (existingCurrentP) existingCurrentP.remove();
        if (existingNewP) existingNewP.remove();
        
        // Add hashed current password
        const currentPField = document.createElement('input');
        currentPField.type = 'hidden';
        currentPField.name = 'current_p';
        currentPField.value = hex_sha512(currentPassword);
        form.appendChild(currentPField);
        
        // Add hashed new password
        const newPField = document.createElement('input');
        newPField.type = 'hidden';
        newPField.name = 'new_p';
        newPField.value = hex_sha512(newPassword);
        form.appendChild(newPField);
        
        // Clear plain text passwords
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('confirm_password').value = '';

        // Show loading state
        const submitBtn = document.querySelector('button[onclick="validateAndHashPasswords()"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';
        submitBtn.disabled = true;

        // Submit the form
        document.getElementById('changePasswordForm').submit();
        return true;
    } else {
        alert('Password hashing function not available. Please refresh the page.');
        return false;
    }
}
</script>
</body>
</html>

<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$mysqli = $obj->connection();
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

$selected = "affiliate_applications.php";
$page = "affiliate_applications.php";

// Handle status updates
if (isset($_POST['update_status'])) {
    $application_id = (int)$_POST['application_id'];
    $new_status = $_POST['status'];
    $review_notes = trim($_POST['review_notes'] ?? '');
    
    $update_query = "UPDATE affiliate_applications SET application_status = ?, review_notes = ?, updated_at = NOW()";
    $params = [$new_status, $review_notes];
    $param_types = 'ss';
    
    if ($new_status === 'approved') {
        $update_query .= ", approval_date = NOW()";
    }
    
    $update_query .= " WHERE id = ?";
    $params[] = $application_id;
    $param_types .= 'i';
    
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $stmt->close();
    
    // Send notification email to applicant
    if ($new_status === 'approved' || $new_status === 'rejected') {
        $app_stmt = $mysqli->prepare("SELECT name, email FROM affiliate_applications WHERE id = ?");
        $app_stmt->bind_param('i', $application_id);
        $app_stmt->execute();
        $app_result = $app_stmt->get_result()->fetch_assoc();
        $app_stmt->close();
        
        if ($app_result) {
            $subject = $new_status === 'approved' ? 'Affiliate Application Approved!' : 'Affiliate Application Update';
            $message = $new_status === 'approved' 
                ? "Congratulations! Your affiliate application has been approved. You'll receive login details shortly."
                : "Thank you for your application. After review, we're unable to approve it at this time. " . $review_notes;
            
            // Uncomment to send notification emails
            // mail($app_result['email'], $subject, $message, "From: affiliates@mynutrify.com");
        }
    }
}

// Pagination
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page_num - 1) * $limit;

// Filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_conditions = [];
$params = [];
$param_types = '';

if ($status_filter && $status_filter !== 'all') {
    $where_conditions[] = "application_status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR company LIKE ? OR website LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    $param_types .= 'sssss';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM affiliate_applications $where_clause";
$count_stmt = $mysqli->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_applications = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = ceil($total_applications / $limit);

// Get applications
$query = "SELECT * FROM affiliate_applications $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $mysqli->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get statistics
$stats = [];
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN application_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN application_status = 'under_review' THEN 1 ELSE 0 END) as review_count,
    SUM(CASE WHEN application_status = 'approved' THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN application_status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_count
    FROM affiliate_applications";
$stats_result = $mysqli->query($stats_query);
if ($stats_result) {
    $stats = $stats_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Affiliate Applications | Nutrify Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        .status-pending { background-color: #ffc107; color: #000; }
        .status-under_review { background-color: #17a2b8; color: #fff; }
        .status-approved { background-color: #28a745; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
        .experience-preview { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .website-link { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include("components/navbar.php"); ?>
    <?php include("components/sidebar.php"); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Affiliate Applications</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Affiliate Applications</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $stats['total'] ?? 0; ?></h3>
                                <p>Total Applications</p>
                            </div>
                            <div class="icon"><i class="fas fa-handshake"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $stats['pending_count'] ?? 0; ?></h3>
                                <p>Pending</p>
                            </div>
                            <div class="icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?php echo $stats['review_count'] ?? 0; ?></h3>
                                <p>Under Review</p>
                            </div>
                            <div class="icon"><i class="fas fa-search"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $stats['approved_count'] ?? 0; ?></h3>
                                <p>Approved</p>
                            </div>
                            <div class="icon"><i class="fas fa-check"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo $stats['rejected_count'] ?? 0; ?></h3>
                                <p>Rejected</p>
                            </div>
                            <div class="icon"><i class="fas fa-times"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3><?php echo $stats['today_count'] ?? 0; ?></h3>
                                <p>Today</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Applications</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="under_review" <?php echo $status_filter === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, company, website..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="affiliate_applications.php" class="btn btn-secondary">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Applications Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Affiliate Applications (<?php echo $total_applications; ?> total)</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($applications)): ?>
                            <div class="alert alert-info">No affiliate applications found.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Applicant</th>
                                            <th>Contact</th>
                                            <th>Website</th>
                                            <th>Traffic</th>
                                            <th>Experience</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $app): ?>
                                            <tr>
                                                <td><?php echo $app['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($app['name']); ?></strong>
                                                    <?php if ($app['company']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($app['company']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($app['phone']); ?></div>
                                                    <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($app['email']); ?></div>
                                                </td>
                                                <td>
                                                    <div class="website-link">
                                                        <a href="<?php echo htmlspecialchars($app['website']); ?>" target="_blank" title="<?php echo htmlspecialchars($app['website']); ?>">
                                                            <?php echo htmlspecialchars($app['website']); ?>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($app['traffic_range']); ?></td>
                                                <td>
                                                    <div class="experience-preview" title="<?php echo htmlspecialchars($app['marketing_experience']); ?>">
                                                        <?php echo htmlspecialchars(substr($app['marketing_experience'], 0, 100)) . (strlen($app['marketing_experience']) > 100 ? '...' : ''); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge status-<?php echo $app['application_status']; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $app['application_status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y H:i', strtotime($app['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="viewApplication(<?php echo $app['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="reviewApplication(<?php echo $app['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include("components/footer.php"); ?>
</div>

<!-- Application View Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Affiliate Application Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="applicationContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Review Application</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="application_id" id="reviewApplicationId">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Review Notes</label>
                        <textarea name="review_notes" class="form-control" rows="4" placeholder="Add notes about your review decision..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
function viewApplication(applicationId) {
    $.ajax({
        url: 'get_affiliate_application.php',
        type: 'GET',
        data: { id: applicationId },
        success: function(response) {
            $('#applicationContent').html(response);
            $('#applicationModal').modal('show');
        },
        error: function() {
            alert('Error loading application details');
        }
    });
}

function reviewApplication(applicationId) {
    $('#reviewApplicationId').val(applicationId);
    $('#reviewModal').modal('show');
}
</script>

</body>
</html>

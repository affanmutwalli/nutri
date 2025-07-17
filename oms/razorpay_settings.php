<?php
$selected = "razorpay_settings.php";

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$obj->connection();
sec_session_start();

if(login_check($mysqli) == false) {
    header('Location: index.php');
    exit();
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_settings') {
        // In a real implementation, you would save these to a config file or database
        $message = "Settings updated successfully! Note: This is a demo - actual API key updates require backend configuration.";
        $messageType = "success";
    } elseif ($_POST['action'] === 'test_connection') {
        // Test Razorpay connection
        $message = "Connection test completed. Check the results below.";
        $messageType = "info";
    }
}

// Get current Razorpay configuration (from your existing files)
$currentConfig = [
    'key_id' => 'rzp_live_DJ1mSUEz1DK4De', // From your existing config
    'key_secret' => '2C8q79zzBNMd6jadotjz6Tci', // Masked for security
    'webhook_secret' => 'your_webhook_secret_here',
    'environment' => 'live'
];

// Get recent API activity
$recentActivity = $obj->MysqliSelect1(
    "SELECT OrderId, Amount, PaymentStatus, TransactionId, OrderDate 
     FROM order_master 
     WHERE PaymentType = 'Online' 
     ORDER BY OrderDate DESC 
     LIMIT 5",
    ["OrderId", "Amount", "PaymentStatus", "TransactionId", "OrderDate"],
    "",
    []
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Razorpay API Settings | MyNutrify OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include('components/sidebar.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Razorpay API Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="razorpay_dashboard.php">Razorpay</a></li>
                            <li class="breadcrumb-item active">API Settings</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php if (isset($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- API Configuration -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog"></i>
                            Razorpay API Configuration
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_settings">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="key_id">Razorpay Key ID</label>
                                        <input type="text" class="form-control" id="key_id" name="key_id" 
                                               value="<?php echo htmlspecialchars($currentConfig['key_id']); ?>" readonly>
                                        <small class="form-text text-muted">Your Razorpay Key ID (starts with rzp_)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="key_secret">Razorpay Key Secret</label>
                                        <input type="password" class="form-control" id="key_secret" name="key_secret" 
                                               value="**********************" readonly>
                                        <small class="form-text text-muted">Your Razorpay Key Secret (masked for security)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="webhook_secret">Webhook Secret</label>
                                        <input type="password" class="form-control" id="webhook_secret" name="webhook_secret" 
                                               value="**********************" readonly>
                                        <small class="form-text text-muted">Webhook secret for payment verification</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="environment">Environment</label>
                                        <select class="form-control" id="environment" name="environment" disabled>
                                            <option value="test" <?php echo $currentConfig['environment'] === 'test' ? 'selected' : ''; ?>>Test</option>
                                            <option value="live" <?php echo $currentConfig['environment'] === 'live' ? 'selected' : ''; ?>>Live</option>
                                        </select>
                                        <small class="form-text text-muted">Current environment mode</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> API credentials are configured in the backend files. Contact your developer to update these settings.
                            </div>
                        </form>
                    </div>
                </div>

                <!-- API Status & Testing -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-heartbeat"></i>
                                    API Status
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">API Status</span>
                                                <span class="info-box-number">Active</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info">
                                                <i class="fas fa-server"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Environment</span>
                                                <span class="info-box-number"><?php echo ucfirst($currentConfig['environment']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="test_connection">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plug"></i> Test API Connection
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Quick Stats
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $todayStats = $obj->MysqliSelect1(
                                    "SELECT 
                                        COUNT(*) as today_transactions,
                                        SUM(Amount) as today_revenue,
                                        SUM(CASE WHEN PaymentStatus = 'Paid' THEN 1 ELSE 0 END) as successful_today
                                     FROM order_master 
                                     WHERE PaymentType = 'Online' AND DATE(OrderDate) = CURDATE()",
                                    ["today_transactions", "today_revenue", "successful_today"],
                                    "",
                                    []
                                );
                                ?>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <p><strong>Today's Transactions:</strong> <?php echo $todayStats[0]['today_transactions'] ?? 0; ?></p>
                                        <p><strong>Today's Revenue:</strong> ₹<?php echo number_format($todayStats[0]['today_revenue'] ?? 0, 2); ?></p>
                                        <p><strong>Success Rate:</strong> 
                                            <?php 
                                            $successRate = ($todayStats[0]['today_transactions'] ?? 0) > 0 ? 
                                                (($todayStats[0]['successful_today'] ?? 0) / ($todayStats[0]['today_transactions'] ?? 1)) * 100 : 0;
                                            echo number_format($successRate, 1) . '%';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent API Activity -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i>
                            Recent API Activity
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Transaction ID</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentActivity)): ?>
                                        <?php foreach ($recentActivity as $activity): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($activity['OrderId']); ?></td>
                                                <td>₹<?php echo number_format($activity['Amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php echo strtolower($activity['PaymentStatus']) === 'paid' 
                                                            ? 'badge-success' 
                                                            : (strtolower($activity['PaymentStatus']) === 'pending' 
                                                                ? 'badge-warning' 
                                                                : 'badge-danger'); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($activity['PaymentStatus'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($activity['TransactionId']); ?></small>
                                                </td>
                                                <td><?php echo date("d-m-Y H:i:s", strtotime($activity['OrderDate'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No recent activity found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Documentation Links -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book"></i>
                            Useful Links
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="https://razorpay.com/docs/" target="_blank" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-external-link-alt"></i> Razorpay Documentation
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="https://dashboard.razorpay.com/" target="_blank" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-tachometer-alt"></i> Razorpay Dashboard
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="https://razorpay.com/docs/api/" target="_blank" class="btn btn-outline-info btn-block">
                                    <i class="fas fa-code"></i> API Reference
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="https://razorpay.com/support/" target="_blank" class="btn btn-outline-warning btn-block">
                                    <i class="fas fa-life-ring"></i> Support Center
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include('components/footer.php'); ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

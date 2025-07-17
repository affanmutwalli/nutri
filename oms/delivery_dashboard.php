<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

// Set $conn for compatibility with DeliveryManager
$conn = $mysqli;

require_once '../includes/DeliveryManager.php';

$selected = "delivery_dashboard.php";
$page = "delivery_dashboard.php";

// Get delivery statistics
$stats = [];

// Total shipped orders (with waybill)
$shippedQuery = "SELECT COUNT(*) as count FROM order_master WHERE OrderStatus = 'Shipped' AND Waybill IS NOT NULL AND Waybill != ''";
$result = mysqli_query($conn, $shippedQuery);
$stats['total_shipped'] = mysqli_fetch_assoc($result)['count'];

// Pending orders (without waybill)
$pendingOrdersQuery = "SELECT COUNT(*) as count FROM order_master WHERE (Waybill IS NULL OR Waybill = '') AND OrderStatus NOT IN ('Cancelled', 'Refunded', 'Shipped', 'Delivered')";
$result = mysqli_query($conn, $pendingOrdersQuery);
$stats['pending_shipment'] = mysqli_fetch_assoc($result)['count'];

// Today's shipments
$todayShippedQuery = "SELECT COUNT(*) as count FROM order_master WHERE OrderStatus = 'Shipped' AND DATE(CreatedAt) = CURDATE() AND Waybill IS NOT NULL AND Waybill != ''";
$result = mysqli_query($conn, $todayShippedQuery);
$stats['today_shipped'] = mysqli_fetch_assoc($result)['count'];

// Delivery logs count
$logsQuery = "SELECT COUNT(*) as count FROM delivery_logs";
$result = mysqli_query($conn, $logsQuery);
$stats['total_logs'] = mysqli_fetch_assoc($result)['count'] ?? 0;

// Recent delivery activities
$recentActivitiesQuery = "SELECT dl.order_id, dl.action, dl.status, dl.created_at,
                                 COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName
                          FROM delivery_logs dl
                          LEFT JOIN order_master om ON dl.order_id COLLATE utf8mb4_general_ci = om.OrderId COLLATE utf8mb4_general_ci
                          LEFT JOIN customer_master cm ON om.CustomerId COLLATE utf8mb4_general_ci = cm.CustomerId COLLATE utf8mb4_general_ci AND om.CustomerType = 'Registered'
                          LEFT JOIN direct_customers dc ON om.CustomerId COLLATE utf8mb4_general_ci = dc.CustomerId COLLATE utf8mb4_general_ci AND om.CustomerType = 'Direct'
                          ORDER BY dl.created_at DESC
                          LIMIT 10";
$recentActivitiesResult = mysqli_query($conn, $recentActivitiesQuery);
$recentActivities = [];
if ($recentActivitiesResult) {
    while ($row = mysqli_fetch_assoc($recentActivitiesResult)) {
        $recentActivities[] = $row;
    }
}

// Check automation status
$automationEnabled = false;
$autoShipEnabled = false;

try {
    // Check if automation is enabled
    $autoQuery = "SELECT config_value FROM delivery_config WHERE config_key = 'auto_accept_orders'";
    $autoResult = mysqli_query($conn, $autoQuery);
    if ($autoResult && $row = mysqli_fetch_assoc($autoResult)) {
        $automationEnabled = ($row['config_value'] == '1');
    }

    $shipQuery = "SELECT config_value FROM delivery_config WHERE config_key = 'auto_ship_orders'";
    $shipResult = mysqli_query($conn, $shipQuery);
    if ($shipResult && $row = mysqli_fetch_assoc($shipResult)) {
        $autoShipEnabled = ($row['config_value'] == '1');
    }
} catch (Exception $e) {
    error_log("Error checking automation status: " . $e->getMessage());
}

// Check Delhivery configuration status
try {
    $deliveryManager = new DeliveryManager($conn);
    $isDelhiveryConfigured = $deliveryManager->isDelhiveryConfigured();
    $configValidation = $deliveryManager->validateConfiguration();
} catch (Exception $e) {
    $isDelhiveryConfigured = false;
    $configValidation = ['delhivery' => ['status' => 'error', 'message' => $e->getMessage()]];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS | Delivery Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div id="loading"></div>
    <div class="wrapper">
        <?php include('components/sidebar.php');?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Delivery Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Delivery Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <!-- System Status -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card <?php echo ($automationEnabled && $autoShipEnabled) ? 'card-success' : 'card-warning'; ?>">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-robot"></i>
                                        <?php echo ($automationEnabled && $autoShipEnabled) ? 'System Running Automatically' : 'Manual Mode Active'; ?>
                                    </h3>
                                    <?php if (!$automationEnabled || !$autoShipEnabled): ?>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-success btn-sm" onclick="enableFullAutomation()">
                                            <i class="fas fa-play"></i> Enable Automation
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if ($automationEnabled && $autoShipEnabled): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i>
                                            <strong>All systems automated!</strong> Orders are being processed automatically.
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Manual mode:</strong> Orders require manual processing.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $stats['pending_shipment']; ?></h3>
                                    <p>Pending Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <a href="all_orders.php" class="small-box-footer">View All Orders <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo $stats['today_shipped']; ?></h3>
                                    <p>Shipped Today</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <a href="todays_order.php" class="small-box-footer">View Today's Orders <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Essential Actions Only -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Order Management</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <a href="all_orders.php" class="btn btn-block btn-primary btn-lg">
                                                <i class="fas fa-list"></i> View All Orders
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="delivery_tracking.php" class="btn btn-block btn-info btn-lg">
                                                <i class="fas fa-search"></i> Track Orders
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <button onclick="processAllPendingOrders()" class="btn btn-block btn-success btn-lg">
                                                <i class="fas fa-shipping-fast"></i> Ship Pending Orders
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </section>
        </div>

        <?php include("components/footer.php"); ?>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <script>
    function enableFullAutomation() {
        if (confirm('Enable full automation? Orders will be automatically accepted and shipped.')) {
            $.post('automation_handler.php', {
                action: 'enable_automation'
            }, function(response) {
                if (response.success) {
                    alert('Full automation enabled successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }
    }

    function processAllPendingOrders() {
        if (confirm('Process all pending orders for shipment?')) {
            $('#loading').show();
            $.post('automation_handler.php', {
                action: 'process_all_pending'
            }, function(response) {
                $('#loading').hide();
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }
    }

    // Simple page ready function
    $(document).ready(function() {
        console.log('Delivery Dashboard loaded');
    });
    </script>
</body>
</html>

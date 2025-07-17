<?php
$selected = "razorpay_dashboard.php";

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



// Get payment statistics
$today = date('Y-m-d');
$thisMonth = date('Y-m');

// Today's payments
$todayPayments = $obj->MysqliSelect1(
    "SELECT COUNT(*) as count, SUM(Amount) as total FROM order_master WHERE PaymentType = 'Online' AND DATE(OrderDate) = ?",
    ["count", "total"],
    "s",
    [$today]
);

// This month's payments
$monthPayments = $obj->MysqliSelect1(
    "SELECT COUNT(*) as count, SUM(Amount) as total FROM order_master WHERE PaymentType = 'Online' AND DATE_FORMAT(OrderDate, '%Y-%m') = ?",
    ["count", "total"],
    "s",
    [$thisMonth]
);

// Payment status breakdown (Online orders only - no more pending online orders!)
$paymentStatus = $obj->MysqliSelect1(
    "SELECT
        SUM(CASE WHEN PaymentStatus = 'Paid' THEN 1 ELSE 0 END) as paid_count,
        SUM(CASE WHEN PaymentStatus = 'Pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN PaymentStatus = 'Failed' THEN 1 ELSE 0 END) as failed_count,
        SUM(CASE WHEN PaymentStatus = 'Paid' THEN Amount ELSE 0 END) as paid_amount,
        SUM(CASE WHEN PaymentStatus = 'Pending' THEN Amount ELSE 0 END) as pending_amount
     FROM order_master WHERE PaymentType = 'Online'",
    ["paid_count", "pending_count", "failed_count", "paid_amount", "pending_amount"],
    "",
    []
);

// Recent transactions
$recentTransactions = $obj->MysqliSelect1(
    "SELECT OrderId, Amount, PaymentStatus, TransactionId, OrderDate, CustomerType 
     FROM order_master 
     WHERE PaymentType = 'Online' 
     ORDER BY OrderDate DESC 
     LIMIT 10",
    ["OrderId", "Amount", "PaymentStatus", "TransactionId", "OrderDate", "CustomerType"],
    "",
    []
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Razorpay Dashboard | MyNutrify OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include('components/sidebar.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Razorpay Payment Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Razorpay Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Improved Payment Flow Notice -->
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Improved Payment Flow!</h5>
                    Online orders are now created in the database <strong>only after successful payment</strong>.
                    This eliminates pending online orders and keeps your database clean. COD orders are still created immediately.
                </div>

                <!-- Payment Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>₹<?php echo number_format($todayPayments[0]['total'] ?? 0, 2); ?></h3>
                                <p>Today's Online Revenue</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $todayPayments[0]['count'] ?? 0; ?></h3>
                                <p>Today's Online Orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>₹<?php echo number_format($monthPayments[0]['total'] ?? 0, 2); ?></h3>
                                <p>This Month's Revenue</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $paymentStatus[0]['pending_count'] ?? 0; ?></h3>
                                <p>Pending Online Orders</p>
                                <small>Should be 0 (Orders created only after payment)</small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Overview -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Payment Status Overview</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="text-success">
                                            <h4><?php echo $paymentStatus[0]['paid_count'] ?? 0; ?></h4>
                                            <p>Successful</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="text-warning">
                                            <h4><?php echo $paymentStatus[0]['pending_count'] ?? 0; ?></h4>
                                            <p>Pending</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="text-danger">
                                            <h4><?php echo $paymentStatus[0]['failed_count'] ?? 0; ?></h4>
                                            <p>Failed</p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Successful Amount:</strong><br>
                                        ₹<?php echo number_format($paymentStatus[0]['paid_amount'] ?? 0, 2); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Pending Amount:</strong><br>
                                        ₹<?php echo number_format($paymentStatus[0]['pending_amount'] ?? 0, 2); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <a href="razorpay_transactions.php" class="btn btn-primary btn-block">
                                            <i class="fas fa-list"></i> View All Transactions
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <a href="razorpay_payments.php" class="btn btn-success btn-block">
                                            <i class="fas fa-check-circle"></i> Payment Status
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <a href="razorpay_refunds.php" class="btn btn-warning btn-block">
                                            <i class="fas fa-undo"></i> Manage Refunds
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <a href="razorpay_analytics.php" class="btn btn-info btn-block">
                                            <i class="fas fa-chart-bar"></i> Analytics
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Online Transactions</h3>
                        <div class="card-tools">
                            <a href="razorpay_transactions.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
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
                                        <th>Date</th>
                                        <th>Customer Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentTransactions)): ?>
                                        <?php foreach ($recentTransactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($transaction['OrderId']); ?></td>
                                                <td>₹<?php echo number_format($transaction['Amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php echo strtolower($transaction['PaymentStatus']) === 'paid' 
                                                            ? 'badge-success' 
                                                            : (strtolower($transaction['PaymentStatus']) === 'pending' 
                                                                ? 'badge-warning' 
                                                                : 'badge-danger'); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($transaction['PaymentStatus'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($transaction['TransactionId']); ?></small>
                                                </td>
                                                <td><?php echo date("d-m-Y H:i", strtotime($transaction['OrderDate'])); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['CustomerType']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No recent transactions found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

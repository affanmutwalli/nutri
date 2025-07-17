<?php
$selected = "razorpay_payments.php";

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

// Handle payment status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $orderId = $_POST['order_id'];
        $newStatus = $_POST['new_status'];
        
        $result = $obj->fInsertNew(
            "UPDATE order_master SET PaymentStatus = ? WHERE OrderId = ?",
            "ss",
            [$newStatus, $orderId]
        );
        
        if ($result) {
            $message = "Payment status updated successfully for Order ID: $orderId";
            $messageType = "success";
        } else {
            $message = "Failed to update payment status";
            $messageType = "danger";
        }
    }
}

// Get payment statistics
$paymentStats = $obj->MysqliSelect1(
    "SELECT 
        COUNT(*) as total_payments,
        SUM(CASE WHEN PaymentStatus = 'Paid' THEN 1 ELSE 0 END) as paid_count,
        SUM(CASE WHEN PaymentStatus = 'Pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN PaymentStatus = 'Failed' THEN 1 ELSE 0 END) as failed_count,
        SUM(CASE WHEN PaymentStatus = 'Paid' THEN Amount ELSE 0 END) as paid_amount,
        SUM(CASE WHEN PaymentStatus = 'Pending' THEN Amount ELSE 0 END) as pending_amount,
        SUM(CASE WHEN PaymentStatus = 'Failed' THEN Amount ELSE 0 END) as failed_amount
     FROM order_master WHERE PaymentType = 'Online'",
    ["total_payments", "paid_count", "pending_count", "failed_count", "paid_amount", "pending_amount", "failed_amount"],
    "",
    []
);

// Get pending payments that need attention (should be empty for online orders now!)
$pendingPayments = $obj->MysqliSelect1(
    "SELECT OrderId, Amount, PaymentStatus, TransactionId, OrderDate, CustomerType, CustomerId
     FROM order_master
     WHERE PaymentType = 'Online' AND PaymentStatus = 'Pending'
     ORDER BY OrderDate DESC
     LIMIT 20",
    ["OrderId", "Amount", "PaymentStatus", "TransactionId", "OrderDate", "CustomerType", "CustomerId"],
    "",
    []
);

// Get failed payments
$failedPayments = $obj->MysqliSelect1(
    "SELECT OrderId, Amount, PaymentStatus, TransactionId, OrderDate, CustomerType, CustomerId 
     FROM order_master 
     WHERE PaymentType = 'Online' AND PaymentStatus = 'Failed'
     ORDER BY OrderDate DESC 
     LIMIT 20",
    ["OrderId", "Amount", "PaymentStatus", "TransactionId", "OrderDate", "CustomerType", "CustomerId"],
    "",
    []
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment Status Management | MyNutrify OMS</title>
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
                        <h1 class="m-0 text-dark">Payment Status Management</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="razorpay_dashboard.php">Razorpay</a></li>
                            <li class="breadcrumb-item active">Payment Status</li>
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

                <!-- Payment Statistics -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $paymentStats[0]['paid_count'] ?? 0; ?></h3>
                                <p>Successful Payments</p>
                                <small>₹<?php echo number_format($paymentStats[0]['paid_amount'] ?? 0, 2); ?></small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $paymentStats[0]['pending_count'] ?? 0; ?></h3>
                                <p>Pending Payments</p>
                                <small>₹<?php echo number_format($paymentStats[0]['pending_amount'] ?? 0, 2); ?></small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo $paymentStats[0]['failed_count'] ?? 0; ?></h3>
                                <p>Failed Payments</p>
                                <small>₹<?php echo number_format($paymentStats[0]['failed_amount'] ?? 0, 2); ?></small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $paymentStats[0]['total_payments'] ?? 0; ?></h3>
                                <p>Total Online Payments</p>
                                <small>All Time</small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Payments -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle text-info"></i>
                            Pending Online Payments (Should be Empty)
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-info">Improved Flow: Orders created only after payment</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pendingPayments)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Amount</th>
                                            <th>Transaction ID</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingPayments as $payment): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($payment['OrderId']); ?></strong></td>
                                                <td>₹<?php echo number_format($payment['Amount'], 2); ?></td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($payment['TransactionId']); ?></small>
                                                </td>
                                                <td><?php echo date("d-m-Y H:i", strtotime($payment['OrderDate'])); ?></td>
                                                <td><?php echo htmlspecialchars($payment['CustomerType']); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="updatePaymentStatus('<?php echo htmlspecialchars($payment['OrderId']); ?>', 'Paid')">
                                                            <i class="fas fa-check"></i> Mark Paid
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="updatePaymentStatus('<?php echo htmlspecialchars($payment['OrderId']); ?>', 'Failed')">
                                                            <i class="fas fa-times"></i> Mark Failed
                                                        </button>
                                                        <a href="order_details.php?OrderId=<?php echo urlencode($payment['OrderId']); ?>" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                No pending payments found. All payments are processed!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Failed Payments -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-times-circle text-danger"></i>
                            Recent Failed Payments
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($failedPayments)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Amount</th>
                                            <th>Transaction ID</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($failedPayments as $payment): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($payment['OrderId']); ?></strong></td>
                                                <td>₹<?php echo number_format($payment['Amount'], 2); ?></td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($payment['TransactionId']); ?></small>
                                                </td>
                                                <td><?php echo date("d-m-Y H:i", strtotime($payment['OrderDate'])); ?></td>
                                                <td><?php echo htmlspecialchars($payment['CustomerType']); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="updatePaymentStatus('<?php echo htmlspecialchars($payment['OrderId']); ?>', 'Paid')">
                                                            <i class="fas fa-check"></i> Mark Paid
                                                        </button>
                                                        <button class="btn btn-sm btn-warning" 
                                                                onclick="updatePaymentStatus('<?php echo htmlspecialchars($payment['OrderId']); ?>', 'Pending')">
                                                            <i class="fas fa-clock"></i> Mark Pending
                                                        </button>
                                                        <a href="order_details.php?OrderId=<?php echo urlencode($payment['OrderId']); ?>" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No recent failed payments found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include('components/footer.php'); ?>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Payment Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" id="modal_order_id">
                    <input type="hidden" name="new_status" id="modal_new_status">
                    
                    <p>Are you sure you want to update the payment status for Order ID: <strong id="modal_order_display"></strong>?</p>
                    <p>New Status: <strong id="modal_status_display"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
function updatePaymentStatus(orderId, newStatus) {
    $('#modal_order_id').val(orderId);
    $('#modal_new_status').val(newStatus);
    $('#modal_order_display').text(orderId);
    $('#modal_status_display').text(newStatus);
    $('#updateStatusModal').modal('show');
}
</script>
</body>
</html>

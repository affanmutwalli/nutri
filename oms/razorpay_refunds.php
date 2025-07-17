<?php
$selected = "razorpay_refunds.php";

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

// Handle refund actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'initiate_refund') {
        $orderId = $_POST['order_id'];
        $transactionId = $_POST['transaction_id'];
        $refundAmount = $_POST['refund_amount'];
        $refundReason = $_POST['refund_reason'];

        // Here you would integrate with Razorpay API to initiate actual refund
        // For now, we'll just show a success message (no database changes)
        $message = "Refund request noted for Order ID: $orderId. Amount: ₹$refundAmount. Reason: $refundReason. Please process this manually in Razorpay dashboard.";
        $messageType = "success";
    }
}

// Get refund statistics (using existing order_master table only)
$refundStats = [
    ['total_refunds' => 0, 'completed_refunds' => 0, 'pending_refunds' => 0, 'failed_refunds' => 0, 'total_refunded' => 0]
];

// Get eligible orders for refund (Paid orders)
$eligibleOrders = $obj->MysqliSelect1(
    "SELECT OrderId, Amount, PaymentStatus, TransactionId, OrderDate, CustomerType 
     FROM order_master 
     WHERE PaymentType = 'Online' AND PaymentStatus = 'Paid' AND TransactionId != 'NA'
     AND OrderId NOT IN (SELECT order_id FROM refund_requests WHERE status IN ('Completed', 'Initiated'))
     ORDER BY OrderDate DESC 
     LIMIT 20",
    ["OrderId", "Amount", "PaymentStatus", "TransactionId", "OrderDate", "CustomerType"],
    "",
    []
);

// Get recent refund requests (using existing order_master table only)
// Note: In a real implementation, you would track refunds separately
$recentRefunds = [];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Refund Management | MyNutrify OMS</title>
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
                        <h1 class="m-0 text-dark">Refund Management</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="razorpay_dashboard.php">Razorpay</a></li>
                            <li class="breadcrumb-item active">Refunds</li>
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

                <!-- Refund Statistics -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $refundStats[0]['total_refunds'] ?? 0; ?></h3>
                                <p>Total Refund Requests</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-undo"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $refundStats[0]['completed_refunds'] ?? 0; ?></h3>
                                <p>Completed Refunds</p>
                                <small>₹<?php echo number_format($refundStats[0]['total_refunded'] ?? 0, 2); ?></small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $refundStats[0]['pending_refunds'] ?? 0; ?></h3>
                                <p>Pending Refunds</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo $refundStats[0]['failed_refunds'] ?? 0; ?></h3>
                                <p>Failed Refunds</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Eligible Orders for Refund -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill-wave text-success"></i>
                            Orders Eligible for Refund
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($eligibleOrders)): ?>
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
                                        <?php foreach ($eligibleOrders as $order): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($order['OrderId']); ?></strong></td>
                                                <td>₹<?php echo number_format($order['Amount'], 2); ?></td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($order['TransactionId']); ?></small>
                                                </td>
                                                <td><?php echo date("d-m-Y", strtotime($order['OrderDate'])); ?></td>
                                                <td><?php echo htmlspecialchars($order['CustomerType']); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-warning" 
                                                                onclick="initiateRefund('<?php echo htmlspecialchars($order['OrderId']); ?>', '<?php echo htmlspecialchars($order['TransactionId']); ?>', '<?php echo $order['Amount']; ?>')">
                                                            <i class="fas fa-undo"></i> Initiate Refund
                                                        </button>
                                                        <a href="order_details.php?OrderId=<?php echo urlencode($order['OrderId']); ?>" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View Order
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
                                No orders eligible for refund at the moment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Refund Requests -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history text-primary"></i>
                            Recent Refund Requests
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentRefunds)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Refund Amount</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentRefunds as $refund): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($refund['order_id']); ?></strong></td>
                                                <td>₹<?php echo number_format($refund['refund_amount'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($refund['refund_reason']); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php echo strtolower($refund['status']) === 'completed' 
                                                            ? 'badge-success' 
                                                            : (strtolower($refund['status']) === 'initiated' 
                                                                ? 'badge-warning' 
                                                                : 'badge-danger'); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($refund['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date("d-m-Y H:i", strtotime($refund['created_at'])); ?></td>
                                                <td>
                                                    <a href="order_details.php?OrderId=<?php echo urlencode($refund['order_id']); ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View Order
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No refund requests found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include('components/footer.php'); ?>
</div>

<!-- Initiate Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Initiate Refund</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="initiate_refund">
                    <input type="hidden" name="order_id" id="refund_order_id">
                    <input type="hidden" name="transaction_id" id="refund_transaction_id">
                    
                    <div class="form-group">
                        <label>Order ID</label>
                        <input type="text" class="form-control" id="refund_order_display" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Refund Amount</label>
                        <input type="number" step="0.01" name="refund_amount" id="refund_amount" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Refund Reason</label>
                        <select name="refund_reason" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="Customer Request">Customer Request</option>
                            <option value="Product Defect">Product Defect</option>
                            <option value="Order Cancellation">Order Cancellation</option>
                            <option value="Duplicate Payment">Duplicate Payment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Initiate Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
function initiateRefund(orderId, transactionId, amount) {
    $('#refund_order_id').val(orderId);
    $('#refund_transaction_id').val(transactionId);
    $('#refund_order_display').val(orderId);
    $('#refund_amount').val(amount);
    $('#refundModal').modal('show');
}
</script>
</body>
</html>

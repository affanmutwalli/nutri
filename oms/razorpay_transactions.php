<?php
$selected = "razorpay_transactions.php";

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

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

// Filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchFilter = isset($_GET['search']) ? $_GET['search'] : '';

// Build WHERE clause
$whereConditions = ["PaymentType = 'Online'"];
$params = [];
$types = "";

if (!empty($statusFilter)) {
    $whereConditions[] = "PaymentStatus = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

if (!empty($dateFilter)) {
    $whereConditions[] = "DATE(OrderDate) = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

if (!empty($searchFilter)) {
    $whereConditions[] = "(OrderId LIKE ? OR TransactionId LIKE ?)";
    $params[] = "%$searchFilter%";
    $params[] = "%$searchFilter%";
    $types .= "ss";
}

$whereClause = implode(" AND ", $whereConditions);

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM order_master WHERE $whereClause";
$totalResult = $obj->MysqliSelect1($countQuery, ["total"], $types, $params);
$totalRecords = $totalResult[0]['total'] ?? 0;
$totalPages = ceil($totalRecords / $limit);

// Get transactions
$query = "SELECT OrderId, Amount, PaymentStatus, PaymentType, TransactionId, OrderDate, CustomerType, CustomerId 
          FROM order_master 
          WHERE $whereClause 
          ORDER BY OrderDate DESC 
          LIMIT $limit OFFSET $offset";

$transactions = $obj->MysqliSelect1($query, 
    ["OrderId", "Amount", "PaymentStatus", "PaymentType", "TransactionId", "OrderDate", "CustomerType", "CustomerId"], 
    $types, 
    $params
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>All Transactions | MyNutrify OMS</title>
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
                        <h1 class="m-0 text-dark">All Razorpay Transactions</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="razorpay_dashboard.php">Razorpay</a></li>
                            <li class="breadcrumb-item active">All Transactions</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Filters -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Transactions</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Payment Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="Paid" <?php echo $statusFilter === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="Pending" <?php echo $statusFilter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Failed" <?php echo $statusFilter === 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($dateFilter); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Search (Order ID / Transaction ID)</label>
                                        <input type="text" name="search" class="form-control" placeholder="Enter Order ID or Transaction ID" value="<?php echo htmlspecialchars($searchFilter); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="razorpay_transactions.php" class="btn btn-secondary">Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Transactions (<?php echo $totalRecords; ?> total)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" onclick="exportTransactions()">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Amount</th>
                                        <th>Payment Status</th>
                                        <th>Transaction ID</th>
                                        <th>Date & Time</th>
                                        <th>Customer Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($transactions)): ?>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($transaction['OrderId']); ?></strong>
                                                </td>
                                                <td>
                                                    <strong>₹<?php echo number_format($transaction['Amount'], 2); ?></strong>
                                                </td>
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
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($transaction['TransactionId']); ?>
                                                    </small>
                                                    <?php if (!empty($transaction['TransactionId']) && $transaction['TransactionId'] !== 'NA'): ?>
                                                        <button class="btn btn-xs btn-outline-primary ml-1" onclick="copyToClipboard('<?php echo htmlspecialchars($transaction['TransactionId']); ?>')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo date("d-m-Y", strtotime($transaction['OrderDate'])); ?><br>
                                                    <small class="text-muted"><?php echo date("H:i:s", strtotime($transaction['OrderDate'])); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo htmlspecialchars($transaction['CustomerType']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="order_details.php?OrderId=<?php echo urlencode($transaction['OrderId']); ?>" 
                                                           class="btn btn-sm btn-primary" title="View Order">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($transaction['PaymentStatus'] === 'Paid'): ?>
                                                            <button class="btn btn-sm btn-warning" 
                                                                    onclick="initiateRefund('<?php echo htmlspecialchars($transaction['OrderId']); ?>', '<?php echo htmlspecialchars($transaction['TransactionId']); ?>')" 
                                                                    title="Initiate Refund">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No transactions found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="text-muted">
                                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalRecords); ?> of <?php echo $totalRecords; ?> entries
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-end">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($statusFilter); ?>&date=<?php echo urlencode($dateFilter); ?>&search=<?php echo urlencode($searchFilter); ?>">Previous</a>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($statusFilter); ?>&date=<?php echo urlencode($dateFilter); ?>&search=<?php echo urlencode($searchFilter); ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($page < $totalPages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($statusFilter); ?>&date=<?php echo urlencode($dateFilter); ?>&search=<?php echo urlencode($searchFilter); ?>">Next</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        <?php endif; ?>
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

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Transaction ID copied to clipboard!');
    });
}

function exportTransactions() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = 'razorpay_export.php?' + params.toString();
}

function initiateRefund(orderId, transactionId) {
    if (confirm('Are you sure you want to initiate a refund for Order ID: ' + orderId + '?')) {
        window.location.href = 'razorpay_refunds.php?action=initiate&order_id=' + orderId + '&transaction_id=' + transactionId;
    }
}
</script>
</body>
</html>

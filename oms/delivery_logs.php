<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

// Set $conn for compatibility
$conn = $mysqli;

$selected = "delivery_logs.php";
$page = "delivery_logs.php";

// Pagination
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 50;
$offset = ($page_num - 1) * $records_per_page;

// Filters
$order_filter = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($order_filter)) {
    $where_conditions[] = "dl.order_id LIKE ?";
    $params[] = "%$order_filter%";
    $param_types .= 's';
}

if (!empty($status_filter)) {
    $where_conditions[] = "dl.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if (!empty($date_filter)) {
    $where_conditions[] = "DATE(dl.created_at) = ?";
    $params[] = $date_filter;
    $param_types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM delivery_logs dl $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
} else {
    $total_records = mysqli_fetch_assoc(mysqli_query($conn, $count_query))['total'];
}

$total_pages = ceil($total_records / $records_per_page);

// Get logs with explicit collation handling
$logs_query = "SELECT dl.*, om.Amount,
                      COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName
               FROM delivery_logs dl
               LEFT JOIN order_master om ON dl.order_id COLLATE utf8mb4_general_ci = om.OrderId COLLATE utf8mb4_general_ci
               LEFT JOIN customer_master cm ON om.CustomerId COLLATE utf8mb4_general_ci = cm.CustomerId COLLATE utf8mb4_general_ci AND om.CustomerType = 'Registered'
               LEFT JOIN direct_customers dc ON om.CustomerId COLLATE utf8mb4_general_ci = dc.CustomerId COLLATE utf8mb4_general_ci AND om.CustomerType = 'Direct'
               $where_clause
               ORDER BY dl.created_at DESC
               LIMIT $records_per_page OFFSET $offset";

if (!empty($params)) {
    $logs_stmt = $conn->prepare($logs_query);
    $logs_stmt->bind_param($param_types, ...$params);
    $logs_stmt->execute();
    $logs_result = $logs_stmt->get_result();
} else {
    $logs_result = mysqli_query($conn, $logs_query);
}

$logs = [];
if ($logs_result) {
    while ($row = mysqli_fetch_assoc($logs_result)) {
        $logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS | Delivery Logs</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
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
                            <h1 class="m-0">Delivery Logs</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="delivery_dashboard.php">Delivery</a></li>
                                <li class="breadcrumb-item active">Logs</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    
                    <!-- Filters -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <form method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Order ID</label>
                                            <input type="text" class="form-control" name="order_id" 
                                                   value="<?php echo htmlspecialchars($order_filter); ?>" 
                                                   placeholder="Search by Order ID">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" name="status">
                                                <option value="">All Statuses</option>
                                                <option value="success" <?php echo $status_filter === 'success' ? 'selected' : ''; ?>>Success</option>
                                                <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="date" class="form-control" name="date" 
                                                   value="<?php echo htmlspecialchars($date_filter); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search"></i> Filter
                                                </button>
                                                <a href="delivery_logs.php" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Clear
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Logs Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Delivery Logs (<?php echo $total_records; ?> total)</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($logs)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Provider</th>
                                            <th>Action</th>
                                            <th>Status</th>
                                            <th>Response</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo $log['id']; ?></td>
                                            <td>
                                                <a href="order_details.php?OrderId=<?php echo $log['order_id']; ?>">
                                                    <?php echo htmlspecialchars($log['order_id']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($log['CustomerName'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?php echo htmlspecialchars($log['provider']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    echo $log['status'] === 'success' ? 'success' : 
                                                        ($log['status'] === 'failed' ? 'danger' : 'warning'); 
                                                ?>">
                                                    <?php echo htmlspecialchars($log['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($log['response'])): ?>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-toggle="modal" data-target="#responseModal" 
                                                        onclick="showResponse('<?php echo htmlspecialchars(addslashes($log['response'])); ?>')">
                                                    View Response
                                                </button>
                                                <?php else: ?>
                                                <span class="text-muted">No response</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d-m-Y H:i:s', strtotime($log['created_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <div class="row mt-3">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info">
                                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_records); ?> 
                                        of <?php echo $total_records; ?> entries
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        <ul class="pagination">
                                            <?php if ($page_num > 1): ?>
                                            <li class="paginate_button page-item previous">
                                                <a href="?page=<?php echo $page_num - 1; ?>&<?php echo http_build_query($_GET); ?>" 
                                                   class="page-link">Previous</a>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                                            <li class="paginate_button page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                                                <a href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>" 
                                                   class="page-link"><?php echo $i; ?></a>
                                            </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($page_num < $total_pages): ?>
                                            <li class="paginate_button page-item next">
                                                <a href="?page=<?php echo $page_num + 1; ?>&<?php echo http_build_query($_GET); ?>" 
                                                   class="page-link">Next</a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No delivery logs found.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </section>
        </div>

        <?php include("components/footer.php"); ?>
    </div>

    <!-- Response Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">API Response</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <pre id="responseContent" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <script>
    function showResponse(response) {
        document.getElementById('responseContent').textContent = response;
    }
    </script>
</body>
</html>

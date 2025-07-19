<?php
session_start();
require_once '../database/dbconnection.php';

// Simple admin check
if (!isset($_SESSION['admin_logged_in'])) {
    if (!isset($_SESSION['CustomerId'])) {
        header('Location: ../login.php');
        exit;
    }
}

$obj = new main();
$mysqli = $obj->connection();

$selected = "contact_messages.php";
$page = "contact_messages.php";

// Handle status updates
if (isset($_POST['update_status'])) {
    $message_id = (int)$_POST['message_id'];
    $new_status = $_POST['status'];
    
    $stmt = $mysqli->prepare("UPDATE contact_messages SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('si', $new_status, $message_id);
    $stmt->execute();
    $stmt->close();
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
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    $param_types .= 'sssss';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM contact_messages $where_clause";
$count_stmt = $mysqli->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_messages = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = ceil($total_messages / $limit);

// Get messages
$query = "SELECT * FROM contact_messages $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $mysqli->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get statistics
$stats = [];
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
    SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
    SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied_count,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_count
    FROM contact_messages";
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
    <title>Contact Messages | Nutrify Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <style>
        .status-new { background-color: #ffc107; color: #000; }
        .status-read { background-color: #17a2b8; color: #fff; }
        .status-replied { background-color: #28a745; color: #fff; }
        .message-preview { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include("includes/header.php"); ?>
    <?php include("includes/sidebar.php"); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contact Messages</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Contact Messages</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $stats['total'] ?? 0; ?></h3>
                                <p>Total Messages</p>
                            </div>
                            <div class="icon"><i class="fas fa-envelope"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $stats['new_count'] ?? 0; ?></h3>
                                <p>New Messages</p>
                            </div>
                            <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $stats['replied_count'] ?? 0; ?></h3>
                                <p>Replied</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?php echo $stats['today_count'] ?? 0; ?></h3>
                                <p>Today's Messages</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Messages</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                                    <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, subject..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="contact_messages.php" class="btn btn-secondary">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Messages Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Contact Messages (<?php echo $total_messages; ?> total)</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($messages)): ?>
                            <div class="alert alert-info">No contact messages found.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($messages as $msg): ?>
                                            <tr>
                                                <td><?php echo $msg['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                                                </td>
                                                <td>
                                                    <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($msg['phone']); ?></div>
                                                    <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($msg['email']); ?></div>
                                                </td>
                                                <td><?php echo htmlspecialchars($msg['subject'] ?: 'No Subject'); ?></td>
                                                <td>
                                                    <div class="message-preview" title="<?php echo htmlspecialchars($msg['message']); ?>">
                                                        <?php echo htmlspecialchars(substr($msg['message'], 0, 100)) . (strlen($msg['message']) > 100 ? '...' : ''); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge status-<?php echo $msg['status']; ?>">
                                                        <?php echo ucfirst($msg['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y H:i', strtotime($msg['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="viewMessage(<?php echo $msg['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                                            Status
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                                <button type="submit" name="update_status" value="new" class="dropdown-item">Mark as New</button>
                                                                <input type="hidden" name="status" value="new">
                                                            </form>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                                <button type="submit" name="update_status" value="read" class="dropdown-item">Mark as Read</button>
                                                                <input type="hidden" name="status" value="read">
                                                            </form>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                                <button type="submit" name="update_status" value="replied" class="dropdown-item">Mark as Replied</button>
                                                                <input type="hidden" name="status" value="replied">
                                                            </form>
                                                        </div>
                                                    </div>
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

    <?php include("includes/footer.php"); ?>
</div>

<!-- Message View Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Contact Message Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
function viewMessage(messageId) {
    $.ajax({
        url: 'get_contact_message.php',
        type: 'GET',
        data: { id: messageId },
        success: function(response) {
            $('#messageContent').html(response);
            $('#messageModal').modal('show');
        },
        error: function() {
            alert('Error loading message details');
        }
    });
}
</script>

</body>
</html>

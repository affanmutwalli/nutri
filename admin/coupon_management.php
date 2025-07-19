<?php
session_start();
require_once '../database/dbconnection.php';
require_once '../includes/CouponSystem.php';

// Initialize database connection
$obj = new main();
$mysqli = $obj->connection();

// Simple admin check
if (!isset($_SESSION['admin_logged_in'])) {
    if (!isset($_SESSION['CustomerId'])) {
        header('Location: ../login.php');
        exit;
    }
}

$couponSystem = new CouponSystem($mysqli);
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_coupon':
                $result = createCoupon($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            case 'update_coupon':
                $result = updateCoupon($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            case 'toggle_status':
                $result = toggleCouponStatus($_POST['coupon_id']);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            case 'bulk_action':
                $result = handleBulkAction($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
        }
    }
}

function createCoupon($data) {
    global $mysqli;
    
    try {
        // Validate coupon code uniqueness
        $checkQuery = "SELECT id FROM enhanced_coupons WHERE coupon_code = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("s", $data['coupon_code']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['message' => 'Coupon code already exists!', 'type' => 'error'];
        }
        
        $query = "INSERT INTO enhanced_coupons 
                  (coupon_code, coupon_name, description, discount_type, discount_value, 
                   max_discount_amount, minimum_order_amount, usage_limit_total, usage_limit_per_customer, 
                   customer_type, valid_from, valid_until, is_active, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'admin')";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssdddiisss", 
            $data['coupon_code'], $data['coupon_name'], $data['description'],
            $data['discount_type'], $data['discount_value'], $data['max_discount_amount'],
            $data['minimum_order_amount'], $data['usage_limit_total'], $data['usage_limit_per_customer'],
            $data['customer_type'], $data['valid_from'], $data['valid_until']
        );
        
        if ($stmt->execute()) {
            return ['message' => 'Coupon created successfully!', 'type' => 'success'];
        } else {
            return ['message' => 'Error creating coupon: ' . $mysqli->error, 'type' => 'error'];
        }
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

function updateCoupon($data) {
    global $mysqli;
    
    try {
        $query = "UPDATE enhanced_coupons SET 
                  coupon_name = ?, description = ?, discount_type = ?, discount_value = ?, 
                  max_discount_amount = ?, minimum_order_amount = ?, usage_limit_total = ?, 
                  usage_limit_per_customer = ?, customer_type = ?, valid_from = ?, valid_until = ?
                  WHERE id = ?";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssdddiissi", 
            $data['coupon_name'], $data['description'], $data['discount_type'], $data['discount_value'],
            $data['max_discount_amount'], $data['minimum_order_amount'], $data['usage_limit_total'],
            $data['usage_limit_per_customer'], $data['customer_type'], $data['valid_from'], 
            $data['valid_until'], $data['coupon_id']
        );
        
        if ($stmt->execute()) {
            return ['message' => 'Coupon updated successfully!', 'type' => 'success'];
        } else {
            return ['message' => 'Error updating coupon: ' . $mysqli->error, 'type' => 'error'];
        }
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

function toggleCouponStatus($couponId) {
    global $mysqli;
    
    try {
        $query = "UPDATE enhanced_coupons SET is_active = NOT is_active WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $couponId);
        
        if ($stmt->execute()) {
            return ['message' => 'Coupon status updated successfully!', 'type' => 'success'];
        } else {
            return ['message' => 'Error updating coupon status: ' . $mysqli->error, 'type' => 'error'];
        }
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

function handleBulkAction($data) {
    global $mysqli;
    
    if (empty($data['selected_coupons'])) {
        return ['message' => 'No coupons selected!', 'type' => 'error'];
    }
    
    $couponIds = implode(',', array_map('intval', $data['selected_coupons']));
    
    try {
        switch ($data['bulk_action']) {
            case 'activate':
                $query = "UPDATE enhanced_coupons SET is_active = 1 WHERE id IN ($couponIds)";
                break;
            case 'deactivate':
                $query = "UPDATE enhanced_coupons SET is_active = 0 WHERE id IN ($couponIds)";
                break;
            case 'delete':
                $query = "DELETE FROM enhanced_coupons WHERE id IN ($couponIds)";
                break;
            default:
                return ['message' => 'Invalid bulk action!', 'type' => 'error'];
        }
        
        if ($mysqli->query($query)) {
            $action = ucfirst($data['bulk_action']);
            return ['message' => "Bulk $action completed successfully!", 'type' => 'success'];
        } else {
            return ['message' => 'Error performing bulk action: ' . $mysqli->error, 'type' => 'error'];
        }
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

// Get coupons with pagination and filtering
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$whereClause = "WHERE 1=1";
$params = [];
$types = "";

if (isset($_GET['status']) && $_GET['status'] !== '') {
    $whereClause .= " AND is_active = ?";
    $params[] = $_GET['status'];
    $types .= "i";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $whereClause .= " AND (coupon_code LIKE ? OR coupon_name LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM enhanced_coupons $whereClause";
$totalCoupons = 0;

try {
    if (!empty($params)) {
        $stmt = $mysqli->prepare($countQuery);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $mysqli->query($countQuery);
    }
    
    if ($result) {
        $totalCoupons = $result->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    error_log("Error getting coupon count: " . $e->getMessage());
}

$totalPages = ceil($totalCoupons / $limit);

// Get coupons
$coupons = [];
$query = "SELECT * FROM enhanced_coupons $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

try {
    if (!empty($params)) {
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $mysqli->query($query);
    }
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $coupons[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error getting coupons: " . $e->getMessage());
}

// Get coupon for editing if requested
$editCoupon = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    try {
        $stmt = $mysqli->prepare("SELECT * FROM enhanced_coupons WHERE id = ?");
        $stmt->bind_param("i", $_GET['edit']);
        $stmt->execute();
        $result = $stmt->get_result();
        $editCoupon = $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Error getting coupon for edit: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Management - My Nutrify CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #229954, #27ae60);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .coupon-code {
            font-family: monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-gift"></i> Rewards CMS
                    </h4>
                    
                    <nav class="nav flex-column">
                        <a href="rewards_cms_dashboard.php" class="nav-link">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a href="#" class="nav-link active">
                            <i class="fas fa-ticket-alt me-2"></i> Coupon Management
                        </a>
                        <a href="rewards_management.php" class="nav-link">
                            <i class="fas fa-gift me-2"></i> Rewards Management
                        </a>
                        <a href="customer_points.php" class="nav-link">
                            <i class="fas fa-users me-2"></i> Customer Points
                        </a>
                        <a href="analytics_reports.php" class="nav-link">
                            <i class="fas fa-chart-bar me-2"></i> Analytics & Reports
                        </a>
                        <a href="bulk_operations.php" class="nav-link">
                            <i class="fas fa-tasks me-2"></i> Bulk Operations
                        </a>
                        <a href="system_settings.php" class="nav-link">
                            <i class="fas fa-cog me-2"></i> System Settings
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1><i class="fas fa-ticket-alt"></i> Coupon Management</h1>
                            <p class="text-muted">Create, manage, and track coupon performance</p>
                        </div>
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#couponModal">
                                <i class="fas fa-plus"></i> Create New Coupon
                            </button>
                        </div>
                    </div>
                    
                    <!-- Messages -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filters -->
                    <div class="content-card">
                        <div class="filter-section">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Search Coupons</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                                           placeholder="Search by code or name...">
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="coupon_management.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Bulk Actions -->
                        <form method="POST" id="bulkForm">
                            <input type="hidden" name="action" value="bulk_action">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <select name="bulk_action" class="form-select d-inline-block w-auto me-2">
                                        <option value="">Bulk Actions</option>
                                        <option value="activate">Activate Selected</option>
                                        <option value="deactivate">Deactivate Selected</option>
                                        <option value="delete">Delete Selected</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary" onclick="return confirmBulkAction()">Apply</button>
                                </div>
                                <div>
                                    <span class="text-muted">Total: <?php echo $totalCoupons; ?> coupons</span>
                                </div>
                            </div>
                            
                            <!-- Coupons Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Discount</th>
                                            <th>Min Order</th>
                                            <th>Usage</th>
                                            <th>Valid Until</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($coupons as $coupon): ?>
                                            <tr>
                                                <td><input type="checkbox" name="selected_coupons[]" value="<?php echo $coupon['id']; ?>"></td>
                                                <td><span class="coupon-code"><?php echo htmlspecialchars($coupon['coupon_code']); ?></span></td>
                                                <td><?php echo htmlspecialchars($coupon['coupon_name']); ?></td>
                                                <td>
                                                    <?php if ($coupon['discount_type'] === 'fixed'): ?>
                                                        ₹<?php echo number_format($coupon['discount_value'], 2); ?>
                                                    <?php else: ?>
                                                        <?php echo $coupon['discount_value']; ?>%
                                                        <?php if ($coupon['max_discount_amount']): ?>
                                                            (Max: ₹<?php echo number_format($coupon['max_discount_amount'], 2); ?>)
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>₹<?php echo number_format($coupon['minimum_order_amount'], 2); ?></td>
                                                <td>
                                                    <?php echo $coupon['current_usage_count']; ?>
                                                    <?php if ($coupon['usage_limit_total']): ?>
                                                        / <?php echo $coupon['usage_limit_total']; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($coupon['valid_until'])); ?></td>
                                                <td>
                                                    <?php if ($coupon['is_active'] && strtotime($coupon['valid_until']) > time()): ?>
                                                        <span class="status-badge status-active">Active</span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-inactive">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" onclick="editCoupon(<?php echo $coupon['id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="toggle_status">
                                                            <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                                            <button type="submit" class="btn btn-outline-warning" 
                                                                    onclick="return confirm('Toggle coupon status?')">
                                                                <i class="fas fa-toggle-<?php echo $coupon['is_active'] ? 'on' : 'off'; ?>"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Coupon pagination">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coupon Modal -->
    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">
                        <i class="fas fa-ticket-alt"></i> <span id="modalTitle">Create New Coupon</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="couponForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="create_coupon">
                        <input type="hidden" name="coupon_id" id="couponId">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="coupon_code" class="form-label">Coupon Code *</label>
                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" required
                                           style="text-transform: uppercase;" placeholder="e.g., SAVE20">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="coupon_name" class="form-label">Coupon Name *</label>
                                    <input type="text" class="form-control" id="coupon_name" name="coupon_name" required
                                           placeholder="e.g., Save 20% Off">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"
                                      placeholder="Brief description of the coupon offer"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount_type" class="form-label">Discount Type *</label>
                                    <select class="form-control" id="discount_type" name="discount_type" required onchange="toggleDiscountFields()">
                                        <option value="fixed">Fixed Amount (₹)</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="discount_value" class="form-label">Discount Value *</label>
                                    <input type="number" class="form-control" id="discount_value" name="discount_value"
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4" id="maxDiscountField" style="display: none;">
                                <div class="mb-3">
                                    <label for="max_discount_amount" class="form-label">Max Discount (₹)</label>
                                    <input type="number" class="form-control" id="max_discount_amount" name="max_discount_amount"
                                           step="0.01" min="0" placeholder="Optional for percentage">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="minimum_order_amount" class="form-label">Minimum Order Amount (₹)</label>
                                    <input type="number" class="form-control" id="minimum_order_amount" name="minimum_order_amount"
                                           step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_type" class="form-label">Customer Type</label>
                                    <select class="form-control" id="customer_type" name="customer_type">
                                        <option value="all">All Customers</option>
                                        <option value="new">New Customers Only</option>
                                        <option value="existing">Existing Customers Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="usage_limit_total" class="form-label">Total Usage Limit</label>
                                    <input type="number" class="form-control" id="usage_limit_total" name="usage_limit_total"
                                           min="1" placeholder="Leave empty for unlimited">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="usage_limit_per_customer" class="form-label">Usage Limit per Customer</label>
                                    <input type="number" class="form-control" id="usage_limit_per_customer" name="usage_limit_per_customer"
                                           min="1" value="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valid_from" class="form-label">Valid From *</label>
                                    <input type="datetime-local" class="form-control" id="valid_from" name="valid_from" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valid_until" class="form-label">Valid Until *</label>
                                    <input type="datetime-local" class="form-control" id="valid_until" name="valid_until" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <span id="submitText">Create Coupon</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default dates
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            const nextMonth = new Date(now.getTime() + 30 * 24 * 60 * 60 * 1000);

            document.getElementById('valid_from').value = now.toISOString().slice(0, 16);
            document.getElementById('valid_until').value = nextMonth.toISOString().slice(0, 16);
        });

        // Toggle discount fields based on type
        function toggleDiscountFields() {
            const discountType = document.getElementById('discount_type').value;
            const maxDiscountField = document.getElementById('maxDiscountField');

            if (discountType === 'percentage') {
                maxDiscountField.style.display = 'block';
            } else {
                maxDiscountField.style.display = 'none';
                document.getElementById('max_discount_amount').value = '';
            }
        }

        // Edit coupon function
        function editCoupon(couponId) {
            window.location.href = '?edit=' + couponId;
        }

        // Select all checkboxes
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_coupons[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Confirm bulk action
        function confirmBulkAction() {
            const selectedCoupons = document.querySelectorAll('input[name="selected_coupons[]"]:checked');
            const bulkAction = document.querySelector('select[name="bulk_action"]').value;

            if (selectedCoupons.length === 0) {
                alert('Please select at least one coupon.');
                return false;
            }

            if (!bulkAction) {
                alert('Please select a bulk action.');
                return false;
            }

            const actionText = bulkAction.charAt(0).toUpperCase() + bulkAction.slice(1);
            return confirm(`Are you sure you want to ${actionText.toLowerCase()} ${selectedCoupons.length} selected coupon(s)?`);
        }

        // Auto-generate coupon code
        document.getElementById('coupon_name').addEventListener('input', function() {
            const name = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 10);
            if (name && !document.getElementById('coupon_code').value) {
                document.getElementById('coupon_code').value = name;
            }
        });

        <?php if ($editCoupon): ?>
        // Populate form for editing
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('couponModal'));
            modal.show();

            // Populate form fields
            document.getElementById('modalTitle').textContent = 'Edit Coupon';
            document.getElementById('formAction').value = 'update_coupon';
            document.getElementById('submitText').textContent = 'Update Coupon';
            document.getElementById('couponId').value = '<?php echo $editCoupon['id']; ?>';
            document.getElementById('coupon_code').value = '<?php echo htmlspecialchars($editCoupon['coupon_code']); ?>';
            document.getElementById('coupon_code').readOnly = true;
            document.getElementById('coupon_name').value = '<?php echo htmlspecialchars($editCoupon['coupon_name']); ?>';
            document.getElementById('description').value = '<?php echo htmlspecialchars($editCoupon['description']); ?>';
            document.getElementById('discount_type').value = '<?php echo $editCoupon['discount_type']; ?>';
            document.getElementById('discount_value').value = '<?php echo $editCoupon['discount_value']; ?>';
            document.getElementById('max_discount_amount').value = '<?php echo $editCoupon['max_discount_amount']; ?>';
            document.getElementById('minimum_order_amount').value = '<?php echo $editCoupon['minimum_order_amount']; ?>';
            document.getElementById('customer_type').value = '<?php echo $editCoupon['customer_type']; ?>';
            document.getElementById('usage_limit_total').value = '<?php echo $editCoupon['usage_limit_total']; ?>';
            document.getElementById('usage_limit_per_customer').value = '<?php echo $editCoupon['usage_limit_per_customer']; ?>';
            document.getElementById('valid_from').value = '<?php echo date('Y-m-d\TH:i', strtotime($editCoupon['valid_from'])); ?>';
            document.getElementById('valid_until').value = '<?php echo date('Y-m-d\TH:i', strtotime($editCoupon['valid_until'])); ?>';

            toggleDiscountFields();
        });
        <?php endif; ?>
    </script>
</body>
</html>

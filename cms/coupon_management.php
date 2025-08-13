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

require_once '../includes/setup_rewards_database.php';
require_once '../includes/CouponSystem.php';

// Auto-setup database tables
autoSetupRewardsSystem($mysqli);

$selected = "coupon_management.php";
$page = "coupon_management.php";

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
            case 'toggle_shine':
                $result = toggleCouponShine($_POST['coupon_id']);
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

function toggleCouponShine($couponId) {
    global $mysqli;

    try {
        // First, check if IsShining column exists, if not add it
        $checkColumn = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                       WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = 'enhanced_coupons'
                       AND COLUMN_NAME = 'IsShining'";

        $result = $mysqli->query($checkColumn);
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            // Add IsShining column if it doesn't exist
            $mysqli->query("ALTER TABLE enhanced_coupons ADD COLUMN IsShining TINYINT(1) DEFAULT 0 COMMENT 'Highlight this coupon in the dropdown'");
        }

        $query = "UPDATE enhanced_coupons SET IsShining = NOT COALESCE(IsShining, 0) WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $couponId);

        if ($stmt->execute()) {
            // Get the updated status
            $selectQuery = "SELECT CouponCode, COALESCE(IsShining, 0) as IsShining FROM enhanced_coupons WHERE id = ?";
            $selectStmt = $mysqli->prepare($selectQuery);
            $selectStmt->bind_param("i", $couponId);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $status = $row['IsShining'] ? 'enabled' : 'disabled';
                return ['message' => "Shining feature {$status} for coupon {$row['CouponCode']}!", 'type' => 'success'];
            } else {
                return ['message' => 'Coupon shining status updated successfully!', 'type' => 'success'];
            }
        } else {
            return ['message' => 'Error updating coupon shining status: ' . $mysqli->error, 'type' => 'error'];
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
$page_num = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page_num - 1) * $limit;

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
$query = "SELECT *, COALESCE(IsShining, 0) as IsShining FROM enhanced_coupons $whereClause ORDER BY COALESCE(IsShining, 0) DESC, created_at DESC LIMIT $limit OFFSET $offset";

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
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Coupon Management | My Nutrify CMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include('components/header_links.php');?>
  <style>
    .coupon-code {
      font-family: monospace;
      background: #f8f9fa;
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: bold;
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

    /* Shining Animations */
    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.05); opacity: 0.9; }
    }

    @keyframes shimmer {
      0% { background-position: -200px 0; }
      100% { background-position: 200px 0; }
    }

    .shine-row {
      background: linear-gradient(135deg, #fff5e6 0%, #ffe0b3 100%);
      position: relative;
    }

    .shine-row::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      animation: shimmer 3s ease-in-out infinite;
    }

    .btn-shine {
      background: linear-gradient(135deg, #ff9500, #ffb84d) !important;
      border-color: #ff9500 !important;
      color: white !important;
    }

    .btn-shine:hover {
      background: linear-gradient(135deg, #e6850e, #e6a347) !important;
      transform: scale(1.05);
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <?php include('components/navbar.php');?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include('components/sidebar.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">
              <i class="fas fa-ticket-alt"></i> Coupon Management
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="rewards_dashboard.php">Rewards</a></li>
              <li class="breadcrumb-item active">Coupon Management</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Messages -->
        <?php if (!empty($message)): ?>
          <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <!-- Main Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Manage Coupons</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#couponModal">
                <i class="fas fa-plus"></i> Create New Coupon
              </button>
            </div>
          </div>
          
          <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
              <div class="col-md-12">
                <form method="GET" class="form-inline">
                  <div class="form-group mr-3">
                    <input type="text" class="form-control" name="search" 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                           placeholder="Search coupons...">
                  </div>
                  <div class="form-group mr-3">
                    <select class="form-control" name="status">
                      <option value="">All Status</option>
                      <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : ''; ?>>Active</option>
                      <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search"></i> Filter
                  </button>
                  <a href="coupon_management.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                  </a>
                </form>
              </div>
            </div>

            <!-- Bulk Actions -->
            <form method="POST" id="bulkForm">
              <input type="hidden" name="action" value="bulk_action">
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-inline">
                    <select name="bulk_action" class="form-control mr-2">
                      <option value="">Bulk Actions</option>
                      <option value="activate">Activate Selected</option>
                      <option value="deactivate">Deactivate Selected</option>
                      <option value="delete">Delete Selected</option>
                    </select>
                    <button type="submit" class="btn btn-secondary" onclick="return confirmBulkAction()">Apply</button>
                  </div>
                </div>
                <div class="col-md-6 text-right">
                  <span class="text-muted">Total: <?php echo $totalCoupons; ?> coupons</span>
                </div>
              </div>
              
              <!-- Coupons Table -->
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="selectAll"></th>
                      <th>Code</th>
                      <th>Name</th>
                      <th>Discount</th>
                      <th>Min Order</th>
                      <th>Usage</th>
                      <th>Valid Until</th>
                      <th>Status</th>
                      <th>✨ Shining</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                      <?php
                      $isShining = isset($coupon['IsShining']) ? $coupon['IsShining'] : 0;
                      $rowClass = $isShining ? 'shine-row' : '';
                      ?>
                      <tr class="<?php echo $rowClass; ?>">
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
                          <?php
                          $isShining = isset($coupon['IsShining']) ? $coupon['IsShining'] : 0;
                          if ($isShining): ?>
                            <span class="status-badge" style="background: linear-gradient(135deg, #ff9500, #ffb84d); color: white; animation: pulse 2s ease-in-out infinite;">✨ Shining</span>
                          <?php else: ?>
                            <span class="status-badge" style="background: #6c757d; color: white;">Regular</span>
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
                            <form method="POST" class="d-inline">
                              <input type="hidden" name="action" value="toggle_shine">
                              <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                              <?php
                              $isShining = isset($coupon['IsShining']) ? $coupon['IsShining'] : 0;
                              $shineButtonClass = $isShining ? 'btn-warning' : 'btn-outline-secondary';
                              $shineButtonStyle = $isShining ? 'background: linear-gradient(135deg, #ff9500, #ffb84d); border-color: #ff9500; animation: pulse 1.5s ease-in-out infinite;' : '';
                              ?>
                              <button type="submit" class="btn <?php echo $shineButtonClass; ?>"
                                      style="<?php echo $shineButtonStyle; ?>"
                                      onclick="return confirm('Toggle shining feature for this coupon?')"
                                      title="<?php echo $isShining ? 'Remove shining effect' : 'Make this coupon shine'; ?>">
                                <i class="fas fa-star"></i>
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
                    <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
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
    </section>
  </div>

  <!-- Footer -->
  <?php include('components/footer.php');?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>

<!-- Coupon Modal -->
<div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="couponModalLabel">
          <i class="fas fa-ticket-alt"></i> <span id="modalTitle">Create New Coupon</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="couponForm">
        <div class="modal-body">
          <input type="hidden" name="action" id="formAction" value="create_coupon">
          <input type="hidden" name="coupon_id" id="couponId">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="coupon_code">Coupon Code *</label>
                <input type="text" class="form-control" id="coupon_code" name="coupon_code" required
                       style="text-transform: uppercase;" placeholder="e.g., SAVE20">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="coupon_name">Coupon Name *</label>
                <input type="text" class="form-control" id="coupon_name" name="coupon_name" required
                       placeholder="e.g., Save 20% Off">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2"
                      placeholder="Brief description of the coupon offer"></textarea>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="discount_type">Discount Type *</label>
                <select class="form-control" id="discount_type" name="discount_type" required onchange="toggleDiscountFields()">
                  <option value="fixed">Fixed Amount (₹)</option>
                  <option value="percentage">Percentage (%)</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="discount_value">Discount Value *</label>
                <input type="number" class="form-control" id="discount_value" name="discount_value"
                       step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-md-4" id="maxDiscountField" style="display: none;">
              <div class="form-group">
                <label for="max_discount_amount">Max Discount (₹)</label>
                <input type="number" class="form-control" id="max_discount_amount" name="max_discount_amount"
                       step="0.01" min="0" placeholder="Optional for percentage">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="minimum_order_amount">Minimum Order Amount (₹)</label>
                <input type="number" class="form-control" id="minimum_order_amount" name="minimum_order_amount"
                       step="0.01" min="0" value="0">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="customer_type">Customer Type</label>
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
              <div class="form-group">
                <label for="usage_limit_total">Total Usage Limit</label>
                <input type="number" class="form-control" id="usage_limit_total" name="usage_limit_total"
                       min="1" placeholder="Leave empty for unlimited">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="usage_limit_per_customer">Usage Limit per Customer</label>
                <input type="number" class="form-control" id="usage_limit_per_customer" name="usage_limit_per_customer"
                       min="1" value="1" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="valid_from">Valid From *</label>
                <input type="datetime-local" class="form-control" id="valid_from" name="valid_from" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="valid_until">Valid Until *</label>
                <input type="datetime-local" class="form-control" id="valid_until" name="valid_until" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <span id="submitText">Create Coupon</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include('components/footer_links.php');?>

<script>
// Set default dates
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
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

<?php if ($editCoupon): ?>
// Populate form for editing
$(document).ready(function() {
    $('#couponModal').modal('show');

    // Populate form fields
    $('#modalTitle').text('Edit Coupon');
    $('#formAction').val('update_coupon');
    $('#submitText').text('Update Coupon');
    $('#couponId').val('<?php echo $editCoupon['id']; ?>');
    $('#coupon_code').val('<?php echo htmlspecialchars($editCoupon['coupon_code']); ?>').prop('readonly', true);
    $('#coupon_name').val('<?php echo htmlspecialchars($editCoupon['coupon_name']); ?>');
    $('#description').val('<?php echo htmlspecialchars($editCoupon['description']); ?>');
    $('#discount_type').val('<?php echo $editCoupon['discount_type']; ?>');
    $('#discount_value').val('<?php echo $editCoupon['discount_value']; ?>');
    $('#max_discount_amount').val('<?php echo $editCoupon['max_discount_amount']; ?>');
    $('#minimum_order_amount').val('<?php echo $editCoupon['minimum_order_amount']; ?>');
    $('#customer_type').val('<?php echo $editCoupon['customer_type']; ?>');
    $('#usage_limit_total').val('<?php echo $editCoupon['usage_limit_total']; ?>');
    $('#usage_limit_per_customer').val('<?php echo $editCoupon['usage_limit_per_customer']; ?>');
    $('#valid_from').val('<?php echo date('Y-m-d\TH:i', strtotime($editCoupon['valid_from'])); ?>');
    $('#valid_until').val('<?php echo date('Y-m-d\TH:i', strtotime($editCoupon['valid_until'])); ?>');

    toggleDiscountFields();
});
<?php endif; ?>
</script>

</body>
</html>

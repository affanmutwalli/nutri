<?php
session_start();
require_once '../database/dbconnection.php';
require_once '../includes/RewardsSystem.php';

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

$rewardsSystem = new RewardsSystem($mysqli);
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'adjust_points':
                $result = adjustCustomerPoints($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            case 'bulk_points':
                $result = bulkPointsAdjustment($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
        }
    }
}

function adjustCustomerPoints($data) {
    global $mysqli, $rewardsSystem;
    
    try {
        $customerId = intval($data['customer_id']);
        $points = intval($data['points']);
        $reason = trim($data['reason']);
        $type = $data['adjustment_type']; // 'add' or 'deduct'
        
        if ($points <= 0) {
            return ['message' => 'Points must be greater than 0', 'type' => 'error'];
        }
        
        if (empty($reason)) {
            return ['message' => 'Reason is required', 'type' => 'error'];
        }
        
        if ($type === 'add') {
            $success = $rewardsSystem->awardPoints($customerId, $points, $reason, 'admin_adjustment');
            $action = 'added';
        } else {
            // For deduction, we use negative points
            $success = $rewardsSystem->redeemPoints($customerId, $points, $reason, 'admin_adjustment');
            $action = 'deducted';
        }
        
        if ($success) {
            return ['message' => "Successfully {$action} {$points} points!", 'type' => 'success'];
        } else {
            return ['message' => 'Error adjusting points. Please try again.', 'type' => 'error'];
        }
        
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

function bulkPointsAdjustment($data) {
    global $mysqli, $rewardsSystem;
    
    try {
        $points = intval($data['bulk_points']);
        $reason = trim($data['bulk_reason']);
        $type = $data['bulk_type']; // 'add' or 'deduct'
        $customerType = $data['customer_filter']; // 'all', 'active', 'tier'
        
        if ($points <= 0) {
            return ['message' => 'Points must be greater than 0', 'type' => 'error'];
        }
        
        if (empty($reason)) {
            return ['message' => 'Reason is required', 'type' => 'error'];
        }
        
        // Build customer query based on filter
        $whereClause = "WHERE 1=1";
        if ($customerType === 'active') {
            $whereClause .= " AND IsActive = 'Y'";
        } elseif ($customerType === 'tier') {
            $tier = $data['tier_filter'];
            // Get customers by tier - this would need to join with customer_points
            $whereClause .= " AND CustomerId IN (SELECT customer_id FROM customer_points WHERE tier_level = '$tier')";
        }
        
        $query = "SELECT CustomerId FROM customer_master $whereClause";
        $result = $mysqli->query($query);
        
        $successCount = 0;
        $errorCount = 0;
        
        while ($row = $result->fetch_assoc()) {
            $customerId = $row['CustomerId'];
            
            if ($type === 'add') {
                $success = $rewardsSystem->awardPoints($customerId, $points, $reason, 'bulk_admin_adjustment');
            } else {
                $success = $rewardsSystem->redeemPoints($customerId, $points, $reason, 'bulk_admin_adjustment');
            }
            
            if ($success) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        
        $action = $type === 'add' ? 'added' : 'deducted';
        return ['message' => "Bulk operation completed! {$action} {$points} points for {$successCount} customers. Errors: {$errorCount}", 'type' => 'success'];
        
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

// Get customers with points data
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$whereClause = "WHERE cm.IsActive = 'Y'";
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $whereClause .= " AND (cm.Name LIKE ? OR cm.Email LIKE ? OR cm.Phone LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if (isset($_GET['tier']) && !empty($_GET['tier'])) {
    $whereClause .= " AND cp.tier_level = ?";
    $params[] = $_GET['tier'];
    $types .= "s";
}

// Get total count
$countQuery = "SELECT COUNT(*) as total 
               FROM customer_master cm 
               LEFT JOIN customer_points cp ON cm.CustomerId = cp.customer_id 
               $whereClause";
$totalCustomers = 0;

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
        $totalCustomers = $result->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    error_log("Error getting customer count: " . $e->getMessage());
}

$totalPages = ceil($totalCustomers / $limit);

// Get customers with points
$customers = [];
$query = "SELECT cm.CustomerId, cm.Name, cm.Email, cm.Phone, cm.CreatedAt,
                 COALESCE(cp.total_points, 0) as total_points,
                 COALESCE(cp.lifetime_points, 0) as lifetime_points,
                 COALESCE(cp.points_redeemed, 0) as points_redeemed,
                 COALESCE(cp.tier_level, 'Bronze') as tier_level
          FROM customer_master cm 
          LEFT JOIN customer_points cp ON cm.CustomerId = cp.customer_id 
          $whereClause 
          ORDER BY cp.total_points DESC, cm.CreatedAt DESC 
          LIMIT $limit OFFSET $offset";

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
            $customers[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error getting customers: " . $e->getMessage());
}

// Get summary statistics
$stats = [
    'total_customers' => 0,
    'total_points_awarded' => 0,
    'total_points_redeemed' => 0,
    'average_points' => 0
];

try {
    $result = $mysqli->query("SELECT COUNT(*) as total FROM customer_master WHERE IsActive = 'Y'");
    if ($result) $stats['total_customers'] = $result->fetch_assoc()['total'];
    
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'customer_points'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT SUM(lifetime_points) as total_awarded, SUM(points_redeemed) as total_redeemed, AVG(total_points) as avg_points FROM customer_points");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_points_awarded'] = $row['total_awarded'] ?? 0;
            $stats['total_points_redeemed'] = $row['total_redeemed'] ?? 0;
            $stats['average_points'] = round($row['avg_points'] ?? 0, 1);
        }
    }
} catch (Exception $e) {
    error_log("Error getting stats: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Points Management - My Nutrify CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .tier-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .tier-bronze { background: #cd7f32; color: white; }
        .tier-silver { background: #c0c0c0; color: #333; }
        .tier-gold { background: #ffd700; color: #333; }
        .tier-platinum { background: #e5e4e2; color: #333; }
        .points-display {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
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
                        <a href="coupon_management.php" class="nav-link">
                            <i class="fas fa-ticket-alt me-2"></i> Coupon Management
                        </a>
                        <a href="rewards_management.php" class="nav-link">
                            <i class="fas fa-gift me-2"></i> Rewards Management
                        </a>
                        <a href="#" class="nav-link active">
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
                            <h1><i class="fas fa-users"></i> Customer Points Management</h1>
                            <p class="text-muted">Manage customer points, tiers, and adjustments</p>
                        </div>
                        <div>
                            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">
                                <i class="fas fa-plus-minus"></i> Adjust Points
                            </button>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkPointsModal">
                                <i class="fas fa-users-cog"></i> Bulk Operations
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
                    
                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-primary"><?php echo number_format($stats['total_customers']); ?></div>
                                <div class="text-muted">Total Customers</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-success"><?php echo number_format($stats['total_points_awarded']); ?></div>
                                <div class="text-muted">Points Awarded</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-warning"><?php echo number_format($stats['total_points_redeemed']); ?></div>
                                <div class="text-muted">Points Redeemed</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-info"><?php echo number_format($stats['average_points'], 1); ?></div>
                                <div class="text-muted">Avg Points/Customer</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters and Customer List -->
                    <div class="content-card">
                        <div class="filter-section">
                            <form method="GET" class="row g-3">
                                <div class="col-md-5">
                                    <label for="search" class="form-label">Search Customers</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                                           placeholder="Search by name, email, or phone...">
                                </div>
                                <div class="col-md-3">
                                    <label for="tier" class="form-label">Tier</label>
                                    <select class="form-control" id="tier" name="tier">
                                        <option value="">All Tiers</option>
                                        <option value="Bronze" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Bronze') ? 'selected' : ''; ?>>Bronze</option>
                                        <option value="Silver" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Silver') ? 'selected' : ''; ?>>Silver</option>
                                        <option value="Gold" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Gold') ? 'selected' : ''; ?>>Gold</option>
                                        <option value="Platinum" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Platinum') ? 'selected' : ''; ?>>Platinum</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="customer_points.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Customer Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Contact</th>
                                        <th>Current Points</th>
                                        <th>Lifetime Points</th>
                                        <th>Redeemed</th>
                                        <th>Tier</th>
                                        <th>Member Since</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($customer['Name']); ?></strong>
                                                <br><small class="text-muted">ID: <?php echo $customer['CustomerId']; ?></small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($customer['Email']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($customer['Phone']); ?></small>
                                            </td>
                                            <td>
                                                <span class="points-display">
                                                    <i class="fas fa-coins"></i> <?php echo number_format($customer['total_points']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($customer['lifetime_points']); ?></td>
                                            <td><?php echo number_format($customer['points_redeemed']); ?></td>
                                            <td>
                                                <span class="tier-badge tier-<?php echo strtolower($customer['tier_level']); ?>">
                                                    <?php echo $customer['tier_level']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($customer['CreatedAt'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="adjustPoints(<?php echo $customer['CustomerId']; ?>, '<?php echo htmlspecialchars($customer['Name']); ?>')">
                                                    <i class="fas fa-edit"></i> Adjust
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Customer pagination">
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

    <!-- Adjust Points Modal -->
    <div class="modal fade" id="adjustPointsModal" tabindex="-1" aria-labelledby="adjustPointsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adjustPointsModalLabel">
                        <i class="fas fa-plus-minus"></i> Adjust Customer Points
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="adjust_points">
                        <input type="hidden" name="customer_id" id="adjustCustomerId">

                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <div id="customerInfo" class="form-control-plaintext bg-light p-2 rounded"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="adjustment_type" class="form-label">Action *</label>
                                    <select class="form-control" id="adjustment_type" name="adjustment_type" required>
                                        <option value="add">Add Points</option>
                                        <option value="deduct">Deduct Points</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="points" class="form-label">Points *</label>
                                    <input type="number" class="form-control" id="points" name="points" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason *</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required
                                      placeholder="Provide a reason for this adjustment..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Apply Adjustment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Points Modal -->
    <div class="modal fade" id="bulkPointsModal" tabindex="-1" aria-labelledby="bulkPointsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkPointsModalLabel">
                        <i class="fas fa-users-cog"></i> Bulk Points Operation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="bulk_points">

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This operation will affect multiple customers. Please review carefully before proceeding.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulk_type" class="form-label">Action *</label>
                                    <select class="form-control" id="bulk_type" name="bulk_type" required>
                                        <option value="add">Add Points</option>
                                        <option value="deduct">Deduct Points</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulk_points" class="form-label">Points *</label>
                                    <input type="number" class="form-control" id="bulk_points" name="bulk_points" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_filter" class="form-label">Apply To *</label>
                            <select class="form-control" id="customer_filter" name="customer_filter" required onchange="toggleTierFilter()">
                                <option value="all">All Customers</option>
                                <option value="active">Active Customers Only</option>
                                <option value="tier">Specific Tier</option>
                            </select>
                        </div>

                        <div class="mb-3" id="tierFilterDiv" style="display: none;">
                            <label for="tier_filter" class="form-label">Select Tier</label>
                            <select class="form-control" id="tier_filter" name="tier_filter">
                                <option value="Bronze">Bronze</option>
                                <option value="Silver">Silver</option>
                                <option value="Gold">Gold</option>
                                <option value="Platinum">Platinum</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="bulk_reason" class="form-label">Reason *</label>
                            <textarea class="form-control" id="bulk_reason" name="bulk_reason" rows="3" required
                                      placeholder="Provide a reason for this bulk operation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to perform this bulk operation? This cannot be undone.')">
                            <i class="fas fa-users-cog"></i> Execute Bulk Operation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function adjustPoints(customerId, customerName) {
            document.getElementById('adjustCustomerId').value = customerId;
            document.getElementById('customerInfo').textContent = `${customerName} (ID: ${customerId})`;

            const modal = new bootstrap.Modal(document.getElementById('adjustPointsModal'));
            modal.show();
        }

        function toggleTierFilter() {
            const customerFilter = document.getElementById('customer_filter').value;
            const tierFilterDiv = document.getElementById('tierFilterDiv');

            if (customerFilter === 'tier') {
                tierFilterDiv.style.display = 'block';
                document.getElementById('tier_filter').required = true;
            } else {
                tierFilterDiv.style.display = 'none';
                document.getElementById('tier_filter').required = false;
            }
        }
    </script>
</body>
</html>

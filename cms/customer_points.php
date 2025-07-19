<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("../database/dbconnection.php");
$obj = new main();
$mysqli = $obj->connection();
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

require_once '../includes/RewardsSystem.php';

$selected = "customer_points.php";
$page = "customer_points.php";

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
$page_num = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page_num - 1) * $limit;

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
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Customer Points Management | My Nutrify CMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include('components/header_links.php');?>
  <style>
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
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      padding: 5px 12px;
      border-radius: 15px;
      font-weight: bold;
      font-size: 0.9rem;
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
              <i class="fas fa-users"></i> Customer Points Management
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="rewards_dashboard.php">Rewards</a></li>
              <li class="breadcrumb-item active">Customer Points</li>
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

        <!-- Statistics -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo number_format($stats['total_customers']); ?></h3>
                <p>Total Customers</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo number_format($stats['total_points_awarded']); ?></h3>
                <p>Points Awarded</p>
              </div>
              <div class="icon">
                <i class="fas fa-coins"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo number_format($stats['total_points_redeemed']); ?></h3>
                <p>Points Redeemed</p>
              </div>
              <div class="icon">
                <i class="fas fa-gift"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner">
                <h3><?php echo number_format($stats['average_points'], 1); ?></h3>
                <p>Avg Points/Customer</p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Customer Points Management</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#adjustPointsModal">
                <i class="fas fa-plus-minus"></i> Adjust Points
              </button>
              <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#bulkPointsModal">
                <i class="fas fa-users-cog"></i> Bulk Operations
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
                           placeholder="Search customers...">
                  </div>
                  <div class="form-group mr-3">
                    <select class="form-control" name="tier">
                      <option value="">All Tiers</option>
                      <option value="Bronze" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Bronze') ? 'selected' : ''; ?>>Bronze</option>
                      <option value="Silver" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Silver') ? 'selected' : ''; ?>>Silver</option>
                      <option value="Gold" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Gold') ? 'selected' : ''; ?>>Gold</option>
                      <option value="Platinum" <?php echo (isset($_GET['tier']) && $_GET['tier'] === 'Platinum') ? 'selected' : ''; ?>>Platinum</option>
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search"></i> Filter
                  </button>
                  <a href="customer_points.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                  </a>
                </form>
              </div>
            </div>

            <!-- Customer Table -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
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

<!-- Adjust Points Modal -->
<div class="modal fade" id="adjustPointsModal" tabindex="-1" role="dialog" aria-labelledby="adjustPointsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adjustPointsModalLabel">
          <i class="fas fa-plus-minus"></i> Adjust Customer Points
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="action" value="adjust_points">
          <input type="hidden" name="customer_id" id="adjustCustomerId">

          <div class="form-group">
            <label>Customer</label>
            <div id="customerInfo" class="form-control-plaintext bg-light p-2 rounded"></div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="adjustment_type">Action *</label>
                <select class="form-control" id="adjustment_type" name="adjustment_type" required>
                  <option value="add">Add Points</option>
                  <option value="deduct">Deduct Points</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="points">Points *</label>
                <input type="number" class="form-control" id="points" name="points" min="1" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="reason">Reason *</label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required
                      placeholder="Provide a reason for this adjustment..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Apply Adjustment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bulk Points Modal -->
<div class="modal fade" id="bulkPointsModal" tabindex="-1" role="dialog" aria-labelledby="bulkPointsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkPointsModalLabel">
          <i class="fas fa-users-cog"></i> Bulk Points Operation
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
              <div class="form-group">
                <label for="bulk_type">Action *</label>
                <select class="form-control" id="bulk_type" name="bulk_type" required>
                  <option value="add">Add Points</option>
                  <option value="deduct">Deduct Points</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="bulk_points">Points *</label>
                <input type="number" class="form-control" id="bulk_points" name="bulk_points" min="1" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="customer_filter">Apply To *</label>
            <select class="form-control" id="customer_filter" name="customer_filter" required onchange="toggleTierFilter()">
              <option value="all">All Customers</option>
              <option value="active">Active Customers Only</option>
              <option value="tier">Specific Tier</option>
            </select>
          </div>

          <div class="form-group" id="tierFilterDiv" style="display: none;">
            <label for="tier_filter">Select Tier</label>
            <select class="form-control" id="tier_filter" name="tier_filter">
              <option value="Bronze">Bronze</option>
              <option value="Silver">Silver</option>
              <option value="Gold">Gold</option>
              <option value="Platinum">Platinum</option>
            </select>
          </div>

          <div class="form-group">
            <label for="bulk_reason">Reason *</label>
            <textarea class="form-control" id="bulk_reason" name="bulk_reason" rows="3" required
                      placeholder="Provide a reason for this bulk operation..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to perform this bulk operation? This cannot be undone.')">
            <i class="fas fa-users-cog"></i> Execute Bulk Operation
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include('components/footer_links.php');?>

<script>
function adjustPoints(customerId, customerName) {
    document.getElementById('adjustCustomerId').value = customerId;
    document.getElementById('customerInfo').textContent = `${customerName} (ID: ${customerId})`;

    $('#adjustPointsModal').modal('show');
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

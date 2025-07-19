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

$selected = "rewards_management.php";
$page = "rewards_management.php";

$rewardsSystem = new RewardsSystem($mysqli);
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_reward':
                $result = createReward($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            case 'update_reward':
                $result = updateReward($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            case 'toggle_status':
                $result = toggleRewardStatus($_POST['reward_id']);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
        }
    }
}

function createReward($data) {
    global $mysqli;
    
    try {
        $query = "INSERT INTO rewards_catalog 
                  (reward_name, reward_description, points_required, reward_type, reward_value, 
                   minimum_order_amount, max_redemptions_per_customer, total_redemptions_limit,
                   is_active, valid_from, valid_until, terms_conditions) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssissdiiisss", 
            $data['reward_name'], $data['reward_description'], $data['points_required'],
            $data['reward_type'], $data['reward_value'], $data['minimum_order_amount'],
            $data['max_redemptions_per_customer'], $data['total_redemptions_limit'],
            $data['valid_from'], $data['valid_until'], $data['terms_conditions']
        );
        
        if ($stmt->execute()) {
            return ['message' => 'Reward created successfully!', 'type' => 'success'];
        } else {
            return ['message' => 'Error creating reward: ' . $mysqli->error, 'type' => 'error'];
        }
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

function updateReward($data) {
    global $mysqli;
    
    try {
        $query = "UPDATE rewards_catalog SET 
                  reward_name = ?, reward_description = ?, points_required = ?, reward_type = ?, 
                  reward_value = ?, minimum_order_amount = ?, max_redemptions_per_customer = ?, 
                  total_redemptions_limit = ?, valid_from = ?, valid_until = ?, terms_conditions = ?
                  WHERE id = ?";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssissdiiissi", 
            $data['reward_name'], $data['reward_description'], $data['points_required'],
            $data['reward_type'], $data['reward_value'], $data['minimum_order_amount'],
            $data['max_redemptions_per_customer'], $data['total_redemptions_limit'],
            $data['valid_from'], $data['valid_until'], $data['terms_conditions'], $data['reward_id']
        );
        
        if ($stmt->execute()) {
            return ['message' => 'Reward updated successfully!', 'type' => 'success'];
        } else {
            return ['message' => 'Error updating reward: ' . $mysqli->error, 'type' => 'error'];
        }
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

function toggleRewardStatus($rewardId) {
    global $mysqli;
    
    try {
        $query = "UPDATE rewards_catalog SET is_active = NOT is_active WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $rewardId);
        
        if ($stmt->execute()) {
            return ['message' => 'Reward status updated successfully!', 'type' => 'success'];
        } else {
            return ['message' => 'Error updating reward status: ' . $mysqli->error, 'type' => 'error'];
        }
    } catch (Exception $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
}

// Get rewards with pagination
$page_num = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12;
$offset = ($page_num - 1) * $limit;

$whereClause = "WHERE 1=1";
$params = [];
$types = "";

if (isset($_GET['status']) && $_GET['status'] !== '') {
    $whereClause .= " AND is_active = ?";
    $params[] = $_GET['status'];
    $types .= "i";
}

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $whereClause .= " AND reward_type = ?";
    $params[] = $_GET['type'];
    $types .= "s";
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM rewards_catalog $whereClause";
$totalRewards = 0;

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
        $totalRewards = $result->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    error_log("Error getting reward count: " . $e->getMessage());
}

$totalPages = ceil($totalRewards / $limit);

// Get rewards
$rewards = [];
$query = "SELECT * FROM rewards_catalog $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

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
            $rewards[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error getting rewards: " . $e->getMessage());
}

// Get reward for editing if requested
$editReward = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    try {
        $stmt = $mysqli->prepare("SELECT * FROM rewards_catalog WHERE id = ?");
        $stmt->bind_param("i", $_GET['edit']);
        $stmt->execute();
        $result = $stmt->get_result();
        $editReward = $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Error getting reward for edit: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Rewards Management | My Nutrify CMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include('components/header_links.php');?>
  <style>
    .reward-card {
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      transition: transform 0.2s;
      height: 100%;
    }
    .reward-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .reward-type-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    .type-coupon {
      background: #e3f2fd;
      color: #1976d2;
    }
    .type-discount {
      background: #f3e5f5;
      color: #7b1fa2;
    }
    .type-freebie {
      background: #e8f5e8;
      color: #388e3c;
    }
    .points-required {
      background: linear-gradient(135deg, #ff9800, #f57c00);
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-weight: bold;
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
              <i class="fas fa-gift"></i> Rewards Management
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="rewards_dashboard.php">Rewards</a></li>
              <li class="breadcrumb-item active">Rewards Management</li>
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
            <h3 class="card-title">Rewards Catalog</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rewardModal">
                <i class="fas fa-plus"></i> Create New Reward
              </button>
            </div>
          </div>
          
          <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
              <div class="col-md-12">
                <form method="GET" class="form-inline">
                  <div class="form-group mr-3">
                    <select class="form-control" name="type">
                      <option value="">All Types</option>
                      <option value="coupon" <?php echo (isset($_GET['type']) && $_GET['type'] === 'coupon') ? 'selected' : ''; ?>>Coupon</option>
                      <option value="discount" <?php echo (isset($_GET['type']) && $_GET['type'] === 'discount') ? 'selected' : ''; ?>>Discount</option>
                      <option value="freebie" <?php echo (isset($_GET['type']) && $_GET['type'] === 'freebie') ? 'selected' : ''; ?>>Freebie</option>
                    </select>
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
                  <a href="rewards_management.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                  </a>
                </form>
              </div>
            </div>

            <!-- Rewards Grid -->
            <div class="row">
              <?php foreach ($rewards as $reward): ?>
                <div class="col-md-6 col-lg-4">
                  <div class="reward-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h5 class="mb-0"><?php echo htmlspecialchars($reward['reward_name']); ?></h5>
                      <span class="reward-type-badge type-<?php echo $reward['reward_type']; ?>">
                        <?php echo ucfirst($reward['reward_type']); ?>
                      </span>
                    </div>
                    
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($reward['reward_description']); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <span class="points-required">
                        <i class="fas fa-coins"></i> <?php echo number_format($reward['points_required']); ?> Points
                      </span>
                      <span class="text-success font-weight-bold">
                        ₹<?php echo number_format($reward['reward_value'], 2); ?>
                      </span>
                    </div>
                    
                    <div class="small text-muted mb-3">
                      <?php if ($reward['minimum_order_amount'] > 0): ?>
                        <div>Min Order: ₹<?php echo number_format($reward['minimum_order_amount'], 2); ?></div>
                      <?php endif; ?>
                      <div>Redeemed: <?php echo $reward['current_redemptions']; ?>
                        <?php if ($reward['total_redemptions_limit']): ?>
                          / <?php echo $reward['total_redemptions_limit']; ?>
                        <?php endif; ?>
                      </div>
                      <div>Valid until: <?php echo $reward['valid_until'] ? date('d M Y', strtotime($reward['valid_until'])) : 'No expiry'; ?></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="badge badge-<?php echo $reward['is_active'] ? 'success' : 'secondary'; ?>">
                        <?php echo $reward['is_active'] ? 'Active' : 'Inactive'; ?>
                      </span>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editReward(<?php echo $reward['id']; ?>)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" class="d-inline">
                          <input type="hidden" name="action" value="toggle_status">
                          <input type="hidden" name="reward_id" value="<?php echo $reward['id']; ?>">
                          <button type="submit" class="btn btn-outline-warning" 
                                  onclick="return confirm('Toggle reward status?')">
                            <i class="fas fa-toggle-<?php echo $reward['is_active'] ? 'on' : 'off'; ?>"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            
            <?php if (empty($rewards)): ?>
              <div class="text-center py-5">
                <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No rewards found</h4>
                <p class="text-muted">Create your first reward to get started!</p>
                <button class="btn btn-primary" data-toggle="modal" data-target="#rewardModal">
                  <i class="fas fa-plus"></i> Create New Reward
                </button>
              </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
              <nav aria-label="Reward pagination">
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

<!-- Reward Modal -->
<div class="modal fade" id="rewardModal" tabindex="-1" role="dialog" aria-labelledby="rewardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rewardModalLabel">
          <i class="fas fa-gift"></i> <span id="modalTitle">Create New Reward</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="rewardForm">
        <div class="modal-body">
          <input type="hidden" name="action" id="formAction" value="create_reward">
          <input type="hidden" name="reward_id" id="rewardId">

          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label for="reward_name">Reward Name *</label>
                <input type="text" class="form-control" id="reward_name" name="reward_name" required
                       placeholder="e.g., ₹50 Discount Coupon">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="reward_type">Reward Type *</label>
                <select class="form-control" id="reward_type" name="reward_type" required>
                  <option value="coupon">Coupon</option>
                  <option value="discount">Direct Discount</option>
                  <option value="freebie">Free Product</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="reward_description">Description *</label>
            <textarea class="form-control" id="reward_description" name="reward_description" rows="2" required
                      placeholder="Brief description of the reward"></textarea>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="points_required">Points Required *</label>
                <input type="number" class="form-control" id="points_required" name="points_required"
                       min="1" required placeholder="e.g., 500">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="reward_value">Reward Value (₹) *</label>
                <input type="number" class="form-control" id="reward_value" name="reward_value"
                       step="0.01" min="0" required placeholder="e.g., 50.00">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="minimum_order_amount">Min Order Amount (₹)</label>
                <input type="number" class="form-control" id="minimum_order_amount" name="minimum_order_amount"
                       step="0.01" min="0" value="0" placeholder="0 for no minimum">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="max_redemptions_per_customer">Max Redemptions per Customer</label>
                <input type="number" class="form-control" id="max_redemptions_per_customer" name="max_redemptions_per_customer"
                       min="1" value="1" placeholder="1">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="total_redemptions_limit">Total Redemptions Limit</label>
                <input type="number" class="form-control" id="total_redemptions_limit" name="total_redemptions_limit"
                       min="1" placeholder="Leave empty for unlimited">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="valid_from">Valid From</label>
                <input type="date" class="form-control" id="valid_from" name="valid_from">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="valid_until">Valid Until</label>
                <input type="date" class="form-control" id="valid_until" name="valid_until">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="terms_conditions">Terms & Conditions</label>
            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3"
                      placeholder="Optional terms and conditions for this reward"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <span id="submitText">Create Reward</span>
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
    const today = new Date().toISOString().split('T')[0];
    const nextYear = new Date();
    nextYear.setFullYear(nextYear.getFullYear() + 1);

    document.getElementById('valid_from').value = today;
    document.getElementById('valid_until').value = nextYear.toISOString().split('T')[0];
});

// Edit reward function
function editReward(rewardId) {
    window.location.href = '?edit=' + rewardId;
}

<?php if ($editReward): ?>
// Populate form for editing
$(document).ready(function() {
    $('#rewardModal').modal('show');

    // Populate form fields
    $('#modalTitle').text('Edit Reward');
    $('#formAction').val('update_reward');
    $('#submitText').text('Update Reward');
    $('#rewardId').val('<?php echo $editReward['id']; ?>');
    $('#reward_name').val('<?php echo htmlspecialchars($editReward['reward_name']); ?>');
    $('#reward_description').val('<?php echo htmlspecialchars($editReward['reward_description']); ?>');
    $('#points_required').val('<?php echo $editReward['points_required']; ?>');
    $('#reward_type').val('<?php echo $editReward['reward_type']; ?>');
    $('#reward_value').val('<?php echo $editReward['reward_value']; ?>');
    $('#minimum_order_amount').val('<?php echo $editReward['minimum_order_amount']; ?>');
    $('#max_redemptions_per_customer').val('<?php echo $editReward['max_redemptions_per_customer']; ?>');
    $('#total_redemptions_limit').val('<?php echo $editReward['total_redemptions_limit']; ?>');
    $('#valid_from').val('<?php echo $editReward['valid_from'] ? date('Y-m-d', strtotime($editReward['valid_from'])) : ''; ?>');
    $('#valid_until').val('<?php echo $editReward['valid_until'] ? date('Y-m-d', strtotime($editReward['valid_until'])) : ''; ?>');
    $('#terms_conditions').val('<?php echo htmlspecialchars($editReward['terms_conditions']); ?>');
});
<?php endif; ?>
</script>

</body>
</html>

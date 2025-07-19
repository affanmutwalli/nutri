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
require_once '../includes/CouponSystem.php';

$selected = "rewards_dashboard.php";
$page = "rewards_dashboard.php";

$rewardsSystem = new RewardsSystem($mysqli);
$couponSystem = new CouponSystem($mysqli);

// Get comprehensive statistics
$stats = [
    'total_customers' => 0,
    'active_customers' => 0,
    'total_points_awarded' => 0,
    'total_points_redeemed' => 0,
    'active_coupons' => 0,
    'total_coupons_used' => 0,
    'total_rewards' => 0,
    'monthly_redemptions' => 0,
    'top_customers' => [],
    'recent_activities' => []
];

try {
    // Customer statistics
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_master WHERE IsActive = 'Y'");
    if ($result) $stats['active_customers'] = $result->fetch_assoc()['count'];
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_master");
    if ($result) $stats['total_customers'] = $result->fetch_assoc()['count'];
    
    // Points statistics
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'points_transactions'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COALESCE(SUM(points_amount), 0) as total FROM points_transactions WHERE transaction_type = 'earned'");
        if ($result) $stats['total_points_awarded'] = $result->fetch_assoc()['total'];
        
        $result = $mysqli->query("SELECT COALESCE(SUM(ABS(points_amount)), 0) as total FROM points_transactions WHERE transaction_type = 'redeemed'");
        if ($result) $stats['total_points_redeemed'] = $result->fetch_assoc()['total'];
        
        $result = $mysqli->query("SELECT COUNT(*) as count FROM points_transactions WHERE transaction_type = 'redeemed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        if ($result) $stats['monthly_redemptions'] = $result->fetch_assoc()['count'];
    }
    
    // Coupon statistics
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM enhanced_coupons WHERE is_active = 1 AND valid_until > NOW()");
        if ($result) $stats['active_coupons'] = $result->fetch_assoc()['count'];
    }
    
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'coupon_usage'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM coupon_usage");
        if ($result) $stats['total_coupons_used'] = $result->fetch_assoc()['count'];
    }
    
    // Rewards statistics
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'rewards_catalog'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM rewards_catalog WHERE is_active = 1");
        if ($result) $stats['total_rewards'] = $result->fetch_assoc()['count'];
    }
    
    // Top customers by points
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'customer_points'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("
            SELECT cp.total_points, cp.lifetime_points, cm.Name, cm.Email 
            FROM customer_points cp 
            JOIN customer_master cm ON cp.customer_id = cm.CustomerId 
            WHERE cm.IsActive = 'Y'
            ORDER BY cp.total_points DESC 
            LIMIT 5
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats['top_customers'][] = $row;
            }
        }
    }
    
} catch (Exception $e) {
    error_log("Error getting dashboard stats: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Rewards Dashboard | My Nutrify CMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include('components/header_links.php');?>
  <style>
    .stat-card {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 1rem;
      transition: transform 0.3s;
    }
    .stat-card:hover {
      transform: translateY(-2px);
    }
    .stat-number {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }
    .stat-label {
      color: #6c757d;
      font-size: 0.9rem;
    }
    .quick-action-card {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 1rem;
      transition: all 0.3s;
      text-decoration: none;
      color: inherit;
    }
    .quick-action-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      text-decoration: none;
      color: inherit;
    }
    .quick-action-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: #007bff;
    }
    .top-customer {
      background: white;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .points-badge {
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
              <i class="fas fa-gift"></i> Rewards & Coupons Dashboard
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Rewards Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-primary"><?php echo number_format($stats['active_customers']); ?></div>
              <div class="stat-label">Active Customers</div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-success"><?php echo number_format($stats['total_points_awarded']); ?></div>
              <div class="stat-label">Points Awarded</div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-warning"><?php echo number_format($stats['active_coupons']); ?></div>
              <div class="stat-label">Active Coupons</div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-info"><?php echo number_format($stats['total_coupons_used']); ?></div>
              <div class="stat-label">Coupons Used</div>
            </div>
          </div>
        </div>

        <!-- Secondary Stats -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-danger"><?php echo number_format($stats['total_points_redeemed']); ?></div>
              <div class="stat-label">Points Redeemed</div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-secondary"><?php echo number_format($stats['total_rewards']); ?></div>
              <div class="stat-label">Active Rewards</div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-dark"><?php echo number_format($stats['monthly_redemptions']); ?></div>
              <div class="stat-label">Monthly Redemptions</div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stat-card">
              <div class="stat-number text-success">
                <?php echo $stats['total_points_awarded'] > 0 ? number_format(($stats['total_points_redeemed'] / $stats['total_points_awarded']) * 100, 1) : 0; ?>%
              </div>
              <div class="stat-label">Redemption Rate</div>
            </div>
          </div>
        </div>

        <!-- Quick Actions and Top Customers -->
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <a href="coupon_management.php" class="quick-action-card">
                      <div class="quick-action-icon">
                        <i class="fas fa-ticket-alt"></i>
                      </div>
                      <h5>Manage Coupons</h5>
                      <p class="text-muted">Create & manage discount coupons</p>
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="rewards_management.php" class="quick-action-card">
                      <div class="quick-action-icon">
                        <i class="fas fa-gift"></i>
                      </div>
                      <h5>Manage Rewards</h5>
                      <p class="text-muted">Setup points-based rewards</p>
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="customer_points.php" class="quick-action-card">
                      <div class="quick-action-icon">
                        <i class="fas fa-users"></i>
                      </div>
                      <h5>Customer Points</h5>
                      <p class="text-muted">Manage customer points & tiers</p>
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="rewards_analytics.php" class="quick-action-card">
                      <div class="quick-action-icon">
                        <i class="fas fa-chart-bar"></i>
                      </div>
                      <h5>View Reports</h5>
                      <p class="text-muted">Analytics & performance reports</p>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-crown"></i> Top Customers by Points</h3>
              </div>
              <div class="card-body">
                <?php if (!empty($stats['top_customers'])): ?>
                  <?php foreach ($stats['top_customers'] as $customer): ?>
                    <div class="top-customer">
                      <div>
                        <strong><?php echo htmlspecialchars($customer['Name']); ?></strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($customer['Email']); ?></small>
                      </div>
                      <div class="text-end">
                        <span class="points-badge"><?php echo number_format($customer['total_points']); ?> pts</span><br>
                        <small class="text-muted">Lifetime: <?php echo number_format($customer['lifetime_points']); ?></small>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="text-muted text-center">No customer data available yet.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- System Status -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> System Status</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon bg-success"><i class="fas fa-database"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Rewards System</span>
                        <span class="info-box-number">Active</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon bg-info"><i class="fas fa-cogs"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Auto Points</span>
                        <span class="info-box-number">Enabled</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon bg-warning"><i class="fas fa-ticket-alt"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Coupon System</span>
                        <span class="info-box-number">Active</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Customer Tiers</span>
                        <span class="info-box-number">4 Levels</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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

<?php include('components/footer_links.php');?>
</body>
</html>

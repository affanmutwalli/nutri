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

$selected = "rewards_analytics.php";
$page = "rewards_analytics.php";

// Get analytics data
$analytics = [
    'overview' => [
        'total_customers' => 0,
        'active_customers' => 0,
        'total_points_awarded' => 0,
        'total_points_redeemed' => 0,
        'active_coupons' => 0,
        'total_coupons_used' => 0,
        'redemption_rate' => 0
    ],
    'monthly_trends' => [],
    'top_coupons' => [],
    'tier_distribution' => [],
    'recent_activities' => []
];

try {
    // Overview statistics
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_master WHERE IsActive = 'Y'");
    if ($result) $analytics['overview']['active_customers'] = $result->fetch_assoc()['count'];
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_master");
    if ($result) $analytics['overview']['total_customers'] = $result->fetch_assoc()['count'];
    
    // Points statistics
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'points_transactions'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COALESCE(SUM(points_amount), 0) as total FROM points_transactions WHERE transaction_type = 'earned'");
        if ($result) $analytics['overview']['total_points_awarded'] = $result->fetch_assoc()['total'];
        
        $result = $mysqli->query("SELECT COALESCE(SUM(ABS(points_amount)), 0) as total FROM points_transactions WHERE transaction_type = 'redeemed'");
        if ($result) $analytics['overview']['total_points_redeemed'] = $result->fetch_assoc()['total'];
        
        // Calculate redemption rate
        if ($analytics['overview']['total_points_awarded'] > 0) {
            $analytics['overview']['redemption_rate'] = round(($analytics['overview']['total_points_redeemed'] / $analytics['overview']['total_points_awarded']) * 100, 1);
        }
        
        // Monthly trends (last 6 months)
        $result = $mysqli->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN transaction_type = 'earned' THEN points_amount ELSE 0 END) as points_awarded,
                SUM(CASE WHEN transaction_type = 'redeemed' THEN ABS(points_amount) ELSE 0 END) as points_redeemed
            FROM points_transactions 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month DESC
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $analytics['monthly_trends'][] = $row;
            }
        }
        
        // Recent activities
        $result = $mysqli->query("
            SELECT pt.*, cm.Name as customer_name
            FROM points_transactions pt
            JOIN customer_master cm ON pt.customer_id = cm.CustomerId
            ORDER BY pt.created_at DESC
            LIMIT 10
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $analytics['recent_activities'][] = $row;
            }
        }
    }
    
    // Coupon statistics
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM enhanced_coupons WHERE is_active = 1 AND valid_until > NOW()");
        if ($result) $analytics['overview']['active_coupons'] = $result->fetch_assoc()['count'];
        
        // Top performing coupons
        $result = $mysqli->query("
            SELECT coupon_code, coupon_name, current_usage_count, discount_type, discount_value
            FROM enhanced_coupons 
            WHERE current_usage_count > 0
            ORDER BY current_usage_count DESC
            LIMIT 5
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $analytics['top_coupons'][] = $row;
            }
        }
    }
    
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'coupon_usage'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM coupon_usage");
        if ($result) $analytics['overview']['total_coupons_used'] = $result->fetch_assoc()['count'];
    }
    
    // Tier distribution
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'customer_points'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("
            SELECT tier_level, COUNT(*) as count
            FROM customer_points cp
            JOIN customer_master cm ON cp.customer_id = cm.CustomerId
            WHERE cm.IsActive = 'Y'
            GROUP BY tier_level
            ORDER BY count DESC
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $analytics['tier_distribution'][] = $row;
            }
        }
    }
    
} catch (Exception $e) {
    error_log("Error getting analytics data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Rewards Analytics | My Nutrify CMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include('components/header_links.php');?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .analytics-card {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 1.5rem;
    }
    .metric-card {
      text-align: center;
      padding: 1rem;
      border-radius: 8px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      margin-bottom: 1rem;
    }
    .metric-number {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }
    .metric-label {
      font-size: 0.9rem;
      opacity: 0.9;
    }
    .activity-item {
      padding: 10px;
      border-left: 4px solid #007bff;
      margin-bottom: 10px;
      background: #f8f9fa;
      border-radius: 0 5px 5px 0;
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
              <i class="fas fa-chart-bar"></i> Rewards Analytics & Reports
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="rewards_dashboard.php">Rewards</a></li>
              <li class="breadcrumb-item active">Analytics</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Overview Metrics -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo number_format($analytics['overview']['active_customers']); ?></h3>
                <p>Active Customers</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo number_format($analytics['overview']['total_points_awarded']); ?></h3>
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
                <h3><?php echo number_format($analytics['overview']['total_coupons_used']); ?></h3>
                <p>Coupons Used</p>
              </div>
              <div class="icon">
                <i class="fas fa-ticket-alt"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo $analytics['overview']['redemption_rate']; ?>%</h3>
                <p>Redemption Rate</p>
              </div>
              <div class="icon">
                <i class="fas fa-percentage"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Points Trends (Last 6 Months)</h3>
              </div>
              <div class="card-body">
                <canvas id="trendsChart" height="100"></canvas>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-layer-group"></i> Customer Tier Distribution</h3>
              </div>
              <div class="card-body">
                <?php if (!empty($analytics['tier_distribution'])): ?>
                  <?php foreach ($analytics['tier_distribution'] as $tier): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="tier-badge tier-<?php echo strtolower($tier['tier_level']); ?>">
                        <?php echo $tier['tier_level']; ?>
                      </span>
                      <span class="font-weight-bold"><?php echo number_format($tier['count']); ?> customers</span>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="text-muted">No tier data available yet.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Coupons and Recent Activities -->
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-trophy"></i> Top Performing Coupons</h3>
              </div>
              <div class="card-body">
                <?php if (!empty($analytics['top_coupons'])): ?>
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Code</th>
                          <th>Name</th>
                          <th>Usage</th>
                          <th>Discount</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($analytics['top_coupons'] as $coupon): ?>
                          <tr>
                            <td><code><?php echo htmlspecialchars($coupon['coupon_code']); ?></code></td>
                            <td><?php echo htmlspecialchars($coupon['coupon_name']); ?></td>
                            <td><span class="badge badge-primary"><?php echo $coupon['current_usage_count']; ?></span></td>
                            <td>
                              <?php if ($coupon['discount_type'] === 'fixed'): ?>
                                ₹<?php echo number_format($coupon['discount_value'], 2); ?>
                              <?php else: ?>
                                <?php echo $coupon['discount_value']; ?>%
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <p class="text-muted">No coupon usage data available yet.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock"></i> Recent Points Activities</h3>
              </div>
              <div class="card-body">
                <?php if (!empty($analytics['recent_activities'])): ?>
                  <div style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($analytics['recent_activities'] as $activity): ?>
                      <div class="activity-item">
                        <div class="d-flex justify-content-between">
                          <div>
                            <strong><?php echo htmlspecialchars($activity['customer_name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($activity['description']); ?></small>
                          </div>
                          <div class="text-right">
                            <span class="badge badge-<?php echo $activity['transaction_type'] === 'earned' ? 'success' : 'warning'; ?>">
                              <?php echo $activity['transaction_type'] === 'earned' ? '+' : '-'; ?><?php echo abs($activity['points_amount']); ?> pts
                            </span>
                            <br>
                            <small class="text-muted"><?php echo date('d M Y', strtotime($activity['created_at'])); ?></small>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <p class="text-muted">No recent activities available.</p>
                <?php endif; ?>
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

<script>
// Trends Chart
const ctx = document.getElementById('trendsChart').getContext('2d');
const trendsData = <?php echo json_encode(array_reverse($analytics['monthly_trends'])); ?>;

const labels = trendsData.map(item => {
    const date = new Date(item.month + '-01');
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
});

const pointsAwarded = trendsData.map(item => parseInt(item.points_awarded));
const pointsRedeemed = trendsData.map(item => parseInt(item.points_redeemed));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Points Awarded',
            data: pointsAwarded,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }, {
            label: 'Points Redeemed',
            data: pointsRedeemed,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>

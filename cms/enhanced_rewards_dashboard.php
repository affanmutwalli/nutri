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
require_once '../includes/RewardsSystem.php';

// Auto-setup database tables
autoSetupRewardsSystem($mysqli);

$selected = "enhanced_rewards_dashboard.php";
$page = "enhanced_rewards_dashboard.php";

$rewardsSystem = new RewardsSystem($mysqli);

// Get comprehensive statistics
$stats = [
    'total_customers' => 0,
    'active_customers' => 0,
    'total_points_awarded' => 0,
    'total_points_redeemed' => 0,
    'active_rewards' => 0,
    'total_redemptions' => 0,
    'monthly_points_awarded' => 0,
    'monthly_redemptions' => 0,
    'top_customers' => [],
    'recent_activities' => [],
    'tier_distribution' => [],
    'popular_rewards' => []
];

try {
    // Total customers with points
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_points");
    if ($result) {
        $stats['total_customers'] = $result->fetch_assoc()['count'];
    }

    // Active customers (with points > 0)
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_points WHERE total_points > 0");
    if ($result) {
        $stats['active_customers'] = $result->fetch_assoc()['count'];
    }

    // Total points awarded
    $result = $mysqli->query("SELECT SUM(lifetime_points) as total FROM customer_points");
    if ($result) {
        $stats['total_points_awarded'] = $result->fetch_assoc()['total'] ?? 0;
    }

    // Total points redeemed
    $result = $mysqli->query("SELECT SUM(points_redeemed) as total FROM customer_points");
    if ($result) {
        $stats['total_points_redeemed'] = $result->fetch_assoc()['total'] ?? 0;
    }

    // Active rewards
    $result = $mysqli->query("SELECT COUNT(*) as count FROM rewards_catalog WHERE is_active = 1");
    if ($result) {
        $stats['active_rewards'] = $result->fetch_assoc()['count'];
    }

    // Total redemptions
    $result = $mysqli->query("SELECT SUM(current_redemptions) as total FROM rewards_catalog");
    if ($result) {
        $stats['total_redemptions'] = $result->fetch_assoc()['total'] ?? 0;
    }

    // Monthly points awarded (current month)
    $result = $mysqli->query("
        SELECT SUM(points_amount) as total 
        FROM points_transactions 
        WHERE transaction_type = 'earned' 
        AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    if ($result) {
        $stats['monthly_points_awarded'] = $result->fetch_assoc()['total'] ?? 0;
    }

    // Monthly redemptions (current month)
    $result = $mysqli->query("
        SELECT SUM(points_amount) as total 
        FROM points_transactions 
        WHERE transaction_type = 'redeemed' 
        AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    if ($result) {
        $stats['monthly_redemptions'] = $result->fetch_assoc()['total'] ?? 0;
    }

    // Top customers by points
    $result = $mysqli->query("
        SELECT cp.*, cm.Name, cm.Email 
        FROM customer_points cp 
        JOIN customer_master cm ON cp.customer_id = cm.CustomerId 
        ORDER BY cp.total_points DESC 
        LIMIT 10
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['top_customers'][] = $row;
        }
    }

    // Recent activities
    $result = $mysqli->query("
        SELECT pt.*, cm.Name as customer_name 
        FROM points_transactions pt 
        JOIN customer_master cm ON pt.customer_id = cm.CustomerId 
        ORDER BY pt.created_at DESC 
        LIMIT 15
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['recent_activities'][] = $row;
        }
    }

    // Tier distribution
    $result = $mysqli->query("
        SELECT tier_level, COUNT(*) as count 
        FROM customer_points 
        GROUP BY tier_level 
        ORDER BY FIELD(tier_level, 'Bronze', 'Silver', 'Gold', 'Platinum')
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['tier_distribution'][] = $row;
        }
    }

    // Popular rewards
    $result = $mysqli->query("
        SELECT reward_name, current_redemptions, points_required 
        FROM rewards_catalog 
        WHERE is_active = 1 
        ORDER BY current_redemptions DESC 
        LIMIT 10
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['popular_rewards'][] = $row;
        }
    }

} catch (Exception $e) {
    error_log("Error getting rewards statistics: " . $e->getMessage());
}

// Calculate derived metrics
$stats['redemption_rate'] = $stats['total_points_awarded'] > 0 ? 
    round(($stats['total_points_redeemed'] / $stats['total_points_awarded']) * 100, 2) : 0;

$stats['avg_points_per_customer'] = $stats['total_customers'] > 0 ? 
    round($stats['total_points_awarded'] / $stats['total_customers'], 0) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Enhanced Rewards Dashboard | My Nutrify CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stats-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stats-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .tier-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .tier-bronze { background-color: #cd7f32; color: white; }
        .tier-silver { background-color: #c0c0c0; color: black; }
        .tier-gold { background-color: #ffd700; color: black; }
        .tier-platinum { background-color: #e5e4e2; color: black; }
        
        .activity-item {
            padding: 10px;
            border-left: 3px solid #007bff;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        
        .activity-earned { border-left-color: #28a745; }
        .activity-redeemed { border-left-color: #dc3545; }
        .activity-bonus { border-left-color: #ffc107; }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php include("components/navbar.php"); ?>
    <?php include("components/sidebar.php"); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Enhanced Rewards Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Enhanced Rewards Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="stats-card primary">
                            <div class="stats-number"><?php echo number_format($stats['total_customers']); ?></div>
                            <div class="stats-label">Total Customers</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="stats-card success">
                            <div class="stats-number"><?php echo number_format($stats['active_customers']); ?></div>
                            <div class="stats-label">Active Customers</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="stats-card info">
                            <div class="stats-number"><?php echo number_format($stats['total_points_awarded']); ?></div>
                            <div class="stats-label">Total Points Awarded</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="stats-card warning">
                            <div class="stats-number"><?php echo number_format($stats['total_points_redeemed']); ?></div>
                            <div class="stats-label">Points Redeemed</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-bolt mr-1"></i>
                                    Quick Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="rewards_management.php" class="btn btn-primary btn-block">
                                            <i class="fas fa-gift mr-2"></i>Manage Rewards
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="customer_points.php" class="btn btn-success btn-block">
                                            <i class="fas fa-coins mr-2"></i>Customer Points
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="rewards_settings.php" class="btn btn-info btn-block">
                                            <i class="fas fa-cog mr-2"></i>Settings
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="rewards_reports.php" class="btn btn-warning btn-block">
                                            <i class="fas fa-chart-bar mr-2"></i>Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Row -->
                <div class="row">
                    <!-- Top Customers -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-crown mr-1"></i>
                                    Top Customers by Points
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($stats['top_customers'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>Tier</th>
                                                    <th>Points</th>
                                                    <th>Lifetime</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($stats['top_customers'] as $customer): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($customer['Name']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($customer['Email']); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="tier-badge tier-<?php echo strtolower($customer['tier_level']); ?>">
                                                            <?php echo $customer['tier_level']; ?>
                                                        </span>
                                                    </td>
                                                    <td><strong><?php echo number_format($customer['total_points']); ?></strong></td>
                                                    <td><?php echo number_format($customer['lifetime_points']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No customer data available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-history mr-1"></i>
                                    Recent Activities
                                </h3>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <?php if (!empty($stats['recent_activities'])): ?>
                                    <?php foreach ($stats['recent_activities'] as $activity): ?>
                                    <div class="activity-item activity-<?php echo $activity['transaction_type']; ?>">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?php echo htmlspecialchars($activity['customer_name']); ?></strong>
                                                <br>
                                                <small>
                                                    <?php
                                                    $action = $activity['transaction_type'] == 'earned' ? 'earned' : 'redeemed';
                                                    echo ucfirst($action) . ' ' . number_format($activity['points_amount']) . ' points';
                                                    ?>
                                                </small>
                                                <?php if (!empty($activity['description'])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($activity['description']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right">
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No recent activities.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <!-- Tier Distribution -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    Customer Tier Distribution
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($stats['tier_distribution'])): ?>
                                    <canvas id="tierChart" width="400" height="200"></canvas>
                                <?php else: ?>
                                    <p class="text-muted">No tier data available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Rewards -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-star mr-1"></i>
                                    Popular Rewards
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($stats['popular_rewards'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Reward</th>
                                                    <th>Points</th>
                                                    <th>Redemptions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($stats['popular_rewards'] as $reward): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($reward['reward_name']); ?></td>
                                                    <td><?php echo number_format($reward['points_required']); ?></td>
                                                    <td>
                                                        <span class="badge badge-success">
                                                            <?php echo number_format($reward['current_redemptions']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No reward data available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Performance -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    This Month's Performance
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Points Awarded This Month</span>
                                                <span class="info-box-number"><?php echo number_format($stats['monthly_points_awarded']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger">
                                                <i class="fas fa-minus"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Points Redeemed This Month</span>
                                                <span class="info-box-number"><?php echo number_format($stats['monthly_redemptions']); ?></span>
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

    <?php include("components/footer.php"); ?>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/chart.js/Chart.min.js"></script>
<script src="plugins/sparklines/sparkline.js"></script>
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script src="dist/js/pages/dashboard.js"></script>
<script src="dist/js/demo.js"></script>

<script>
// Tier Distribution Chart
<?php if (!empty($stats['tier_distribution'])): ?>
var ctx = document.getElementById('tierChart').getContext('2d');
var tierChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [<?php echo "'" . implode("','", array_column($stats['tier_distribution'], 'tier_level')) . "'"; ?>],
        datasets: [{
            data: [<?php echo implode(',', array_column($stats['tier_distribution'], 'count')); ?>],
            backgroundColor: [
                '#cd7f32', // Bronze
                '#c0c0c0', // Silver
                '#ffd700', // Gold
                '#e5e4e2'  // Platinum
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'bottom'
        }
    }
});
<?php endif; ?>

// Auto-refresh every 5 minutes
setTimeout(function() {
    location.reload();
}, 300000);
</script>

</body>
</html>

                <!-- Secondary Stats -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h3><?php echo $stats['active_rewards']; ?></h3>
                                <p>Active Rewards</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-gift"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3><?php echo number_format($stats['total_redemptions']); ?></h3>
                                <p>Total Redemptions</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-info">
                            <div class="inner">
                                <h3><?php echo $stats['redemption_rate']; ?>%</h3>
                                <p>Redemption Rate</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-warning">
                            <div class="inner">
                                <h3><?php echo number_format($stats['avg_points_per_customer']); ?></h3>
                                <p>Avg Points/Customer</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-friends"></i>
                            </div>
                        </div>
                    </div>
                </div>

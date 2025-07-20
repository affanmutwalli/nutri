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

$selected = "rewards_reports.php";
$page = "rewards_reports.php";

// Date range handling
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // First day of current month
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Today
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'overview';

// Generate comprehensive reports
$reports = [
    'overview' => [],
    'customer_activity' => [],
    'reward_performance' => [],
    'points_flow' => [],
    'tier_analysis' => []
];

try {
    // Overview Report
    $reports['overview'] = [
        'total_customers' => 0,
        'new_customers' => 0,
        'points_awarded' => 0,
        'points_redeemed' => 0,
        'net_points' => 0,
        'total_redemptions' => 0,
        'avg_points_per_customer' => 0,
        'redemption_rate' => 0
    ];

    // Total customers
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_points");
    if ($result) {
        $reports['overview']['total_customers'] = $result->fetch_assoc()['count'];
    }

    // New customers in date range
    $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM customer_points WHERE created_at BETWEEN ? AND ?");
    $stmt->bind_param("ss", $startDate, $endDate . ' 23:59:59');
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $reports['overview']['new_customers'] = $result->fetch_assoc()['count'];
    }

    // Points awarded in date range
    $stmt = $mysqli->prepare("
        SELECT SUM(points_amount) as total 
        FROM points_transactions 
        WHERE transaction_type = 'earned' 
        AND created_at BETWEEN ? AND ?
    ");
    $stmt->bind_param("ss", $startDate, $endDate . ' 23:59:59');
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $reports['overview']['points_awarded'] = $result->fetch_assoc()['total'] ?? 0;
    }

    // Points redeemed in date range
    $stmt = $mysqli->prepare("
        SELECT SUM(points_amount) as total 
        FROM points_transactions 
        WHERE transaction_type = 'redeemed' 
        AND created_at BETWEEN ? AND ?
    ");
    $stmt->bind_param("ss", $startDate, $endDate . ' 23:59:59');
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $reports['overview']['points_redeemed'] = $result->fetch_assoc()['total'] ?? 0;
    }

    // Calculate derived metrics
    $reports['overview']['net_points'] = $reports['overview']['points_awarded'] - $reports['overview']['points_redeemed'];
    $reports['overview']['avg_points_per_customer'] = $reports['overview']['total_customers'] > 0 ? 
        round($reports['overview']['points_awarded'] / $reports['overview']['total_customers'], 0) : 0;
    $reports['overview']['redemption_rate'] = $reports['overview']['points_awarded'] > 0 ? 
        round(($reports['overview']['points_redeemed'] / $reports['overview']['points_awarded']) * 100, 2) : 0;

    // Customer Activity Report
    $stmt = $mysqli->prepare("
        SELECT 
            cp.customer_id,
            cm.Name,
            cm.Email,
            cp.total_points,
            cp.lifetime_points,
            cp.points_redeemed,
            cp.tier_level,
            COUNT(pt.id) as transaction_count,
            MAX(pt.created_at) as last_activity
        FROM customer_points cp
        JOIN customer_master cm ON cp.customer_id = cm.CustomerId
        LEFT JOIN points_transactions pt ON cp.customer_id = pt.customer_id 
            AND pt.created_at BETWEEN ? AND ?
        GROUP BY cp.customer_id
        ORDER BY cp.total_points DESC
        LIMIT 50
    ");
    $stmt->bind_param("ss", $startDate, $endDate . ' 23:59:59');
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reports['customer_activity'][] = $row;
    }

    // Reward Performance Report
    $stmt = $mysqli->prepare("
        SELECT 
            rc.*,
            COALESCE(redemption_stats.redemptions_in_period, 0) as period_redemptions,
            COALESCE(redemption_stats.points_used, 0) as period_points_used
        FROM rewards_catalog rc
        LEFT JOIN (
            SELECT 
                reward_id,
                COUNT(*) as redemptions_in_period,
                SUM(points_used) as points_used
            FROM reward_redemptions 
            WHERE created_at BETWEEN ? AND ?
            GROUP BY reward_id
        ) redemption_stats ON rc.id = redemption_stats.reward_id
        ORDER BY period_redemptions DESC, rc.current_redemptions DESC
    ");
    $stmt->bind_param("ss", $startDate, $endDate . ' 23:59:59');
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reports['reward_performance'][] = $row;
    }

    // Points Flow Report (Daily breakdown)
    $stmt = $mysqli->prepare("
        SELECT 
            DATE(created_at) as date,
            transaction_type,
            SUM(points_amount) as total_points,
            COUNT(*) as transaction_count
        FROM points_transactions 
        WHERE created_at BETWEEN ? AND ?
        GROUP BY DATE(created_at), transaction_type
        ORDER BY date DESC, transaction_type
    ");
    $stmt->bind_param("ss", $startDate, $endDate . ' 23:59:59');
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reports['points_flow'][] = $row;
    }

    // Tier Analysis
    $result = $mysqli->query("
        SELECT 
            tier_level,
            COUNT(*) as customer_count,
            AVG(total_points) as avg_points,
            SUM(total_points) as total_points,
            AVG(lifetime_points) as avg_lifetime_points
        FROM customer_points 
        GROUP BY tier_level
        ORDER BY FIELD(tier_level, 'Bronze', 'Silver', 'Gold', 'Platinum')
    ");
    while ($row = $result->fetch_assoc()) {
        $reports['tier_analysis'][] = $row;
    }

} catch (Exception $e) {
    error_log("Error generating reports: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Rewards Reports | My Nutrify CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <style>
        .report-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .report-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .report-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .report-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .report-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-label {
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
        
        .export-btn {
            margin-left: 10px;
        }
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
                        <h1 class="m-0 text-dark">Rewards Reports</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="enhanced_rewards_dashboard.php">Rewards</a></li>
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Filters -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter mr-1"></i>
                            Report Filters
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?php echo $startDate; ?>" required>
                                </div>

                <?php if ($reportType == 'overview'): ?>
                <!-- Overview Report -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="report-card">
                            <div class="report-number"><?php echo number_format($reports['overview']['total_customers']); ?></div>
                            <div class="report-label">Total Customers</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="report-card success">
                            <div class="report-number"><?php echo number_format($reports['overview']['points_awarded']); ?></div>
                            <div class="report-label">Points Awarded</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="report-card warning">
                            <div class="report-number"><?php echo number_format($reports['overview']['points_redeemed']); ?></div>
                            <div class="report-label">Points Redeemed</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="report-card info">
                            <div class="report-number"><?php echo $reports['overview']['redemption_rate']; ?>%</div>
                            <div class="report-label">Redemption Rate</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Period Summary</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Date Range:</strong></td>
                                        <td><?php echo date('M j, Y', strtotime($startDate)) . ' - ' . date('M j, Y', strtotime($endDate)); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>New Customers:</strong></td>
                                        <td><?php echo number_format($reports['overview']['new_customers']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Net Points:</strong></td>
                                        <td><?php echo number_format($reports['overview']['net_points']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Avg Points/Customer:</strong></td>
                                        <td><?php echo number_format($reports['overview']['avg_points_per_customer']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                            <div class="card-body">
                                <a href="enhanced_rewards_dashboard.php" class="btn btn-primary btn-block">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="rewards_management.php" class="btn btn-success btn-block">
                                    <i class="fas fa-gift mr-2"></i>Manage Rewards
                                </a>
                                <a href="customer_points.php" class="btn btn-info btn-block">
                                    <i class="fas fa-users mr-2"></i>Customer Points
                                </a>
                                <a href="rewards_settings.php" class="btn btn-warning btn-block">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($reportType == 'customer_activity'): ?>
                <!-- Customer Activity Report -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-1"></i>
                            Customer Activity Report
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Tier</th>
                                        <th>Current Points</th>
                                        <th>Lifetime Points</th>
                                        <th>Points Redeemed</th>
                                        <th>Transactions</th>
                                        <th>Last Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports['customer_activity'] as $customer): ?>
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
                                        <td><?php echo number_format($customer['total_points']); ?></td>
                                        <td><?php echo number_format($customer['lifetime_points']); ?></td>
                                        <td><?php echo number_format($customer['points_redeemed']); ?></td>
                                        <td><?php echo number_format($customer['transaction_count']); ?></td>
                                        <td>
                                            <?php
                                            echo $customer['last_activity'] ?
                                                date('M j, Y', strtotime($customer['last_activity'])) :
                                                'No activity';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($reportType == 'reward_performance'): ?>
                <!-- Reward Performance Report -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gift mr-1"></i>
                            Reward Performance Report
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reward</th>
                                        <th>Type</th>
                                        <th>Points Required</th>
                                        <th>Total Redemptions</th>
                                        <th>Period Redemptions</th>
                                        <th>Period Points Used</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports['reward_performance'] as $reward): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($reward['reward_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($reward['description']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo ucfirst($reward['reward_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($reward['points_required']); ?></td>
                                        <td><?php echo number_format($reward['current_redemptions']); ?></td>
                                        <td><?php echo number_format($reward['period_redemptions']); ?></td>
                                        <td><?php echo number_format($reward['period_points_used']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $reward['is_active'] ? 'success' : 'danger'; ?>">
                                                <?php echo $reward['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($reportType == 'points_flow'): ?>
                <!-- Points Flow Report -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Points Flow Report
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction Type</th>
                                        <th>Total Points</th>
                                        <th>Transaction Count</th>
                                        <th>Avg Points/Transaction</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports['points_flow'] as $flow): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y', strtotime($flow['date'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $flow['transaction_type'] == 'earned' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($flow['transaction_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($flow['total_points']); ?></td>
                                        <td><?php echo number_format($flow['transaction_count']); ?></td>
                                        <td><?php echo number_format($flow['total_points'] / $flow['transaction_count'], 1); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($reportType == 'tier_analysis'): ?>
                <!-- Tier Analysis Report -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-layer-group mr-1"></i>
                            Tier Analysis Report
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($reports['tier_analysis'] as $tier): ?>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5>
                                            <span class="tier-badge tier-<?php echo strtolower($tier['tier_level']); ?>">
                                                <?php echo $tier['tier_level']; ?> Tier
                                            </span>
                                        </h5>
                                        <p><strong><?php echo number_format($tier['customer_count']); ?></strong> customers</p>
                                        <p>Avg Points: <strong><?php echo number_format($tier['avg_points']); ?></strong></p>
                                        <p>Total Points: <strong><?php echo number_format($tier['total_points']); ?></strong></p>
                                        <p>Avg Lifetime: <strong><?php echo number_format($tier['avg_lifetime_points']); ?></strong></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tier</th>
                                        <th>Customer Count</th>
                                        <th>Percentage</th>
                                        <th>Avg Current Points</th>
                                        <th>Total Points</th>
                                        <th>Avg Lifetime Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalCustomers = array_sum(array_column($reports['tier_analysis'], 'customer_count'));
                                    foreach ($reports['tier_analysis'] as $tier):
                                        $percentage = $totalCustomers > 0 ? round(($tier['customer_count'] / $totalCustomers) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="tier-badge tier-<?php echo strtolower($tier['tier_level']); ?>">
                                                <?php echo $tier['tier_level']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($tier['customer_count']); ?></td>
                                        <td><?php echo $percentage; ?>%</td>
                                        <td><?php echo number_format($tier['avg_points']); ?></td>
                                        <td><?php echo number_format($tier['total_points']); ?></td>
                                        <td><?php echo number_format($tier['avg_lifetime_points']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include("components/footer.php"); ?>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
function exportReport() {
    // Get current parameters
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var reportType = $('#report_type').val();

    // Create export URL
    var exportUrl = 'export_rewards_report.php?start_date=' + startDate +
                   '&end_date=' + endDate + '&report_type=' + reportType + '&format=csv';

    // Open export in new window
    window.open(exportUrl, '_blank');
}

// Date range picker
$(document).ready(function() {
    // Set max date to today
    var today = new Date().toISOString().split('T')[0];
    $('#start_date, #end_date').attr('max', today);

    // Quick date range buttons
    $('.card-body form').append(`
        <div class="col-12 mt-3">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Today</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">This Week</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">This Month</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('quarter')">This Quarter</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('year')">This Year</button>
            </div>
        </div>
    `);
});

function setDateRange(period) {
    var today = new Date();
    var startDate, endDate = today.toISOString().split('T')[0];

    switch(period) {
        case 'today':
            startDate = endDate;
            break;
        case 'week':
            var weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
            startDate = weekStart.toISOString().split('T')[0];
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            break;
        case 'quarter':
            var quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            break;
    }

    $('#start_date').val(startDate);
    $('#end_date').val(endDate);
}
</script>

</body>
</html>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?php echo $endDate; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="report_type">Report Type</label>
                                    <select class="form-control" id="report_type" name="report_type">
                                        <option value="overview" <?php echo $reportType == 'overview' ? 'selected' : ''; ?>>Overview</option>
                                        <option value="customer_activity" <?php echo $reportType == 'customer_activity' ? 'selected' : ''; ?>>Customer Activity</option>
                                        <option value="reward_performance" <?php echo $reportType == 'reward_performance' ? 'selected' : ''; ?>>Reward Performance</option>
                                        <option value="points_flow" <?php echo $reportType == 'points_flow' ? 'selected' : ''; ?>>Points Flow</option>
                                        <option value="tier_analysis" <?php echo $reportType == 'tier_analysis' ? 'selected' : ''; ?>>Tier Analysis</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search mr-1"></i> Generate Report
                                        </button>
                                        <button type="button" class="btn btn-success export-btn" onclick="exportReport()">
                                            <i class="fas fa-download mr-1"></i> Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

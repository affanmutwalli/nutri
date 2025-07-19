<?php
session_start();
require_once '../database/dbconnection.php';
require_once '../includes/RewardsSystem.php';
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards & Coupons CMS Dashboard - My Nutrify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" rel="stylesheet">
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
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .quick-action-btn {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            border: none;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin: 5px;
            transition: all 0.3s;
        }
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
            color: white;
        }
        .activity-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #27ae60;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .top-customer {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
                        <a href="#" class="nav-link active">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a href="coupon_management.php" class="nav-link">
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
                        <hr class="my-3">
                        <a href="../customer_rewards_dashboard.php" class="nav-link">
                            <i class="fas fa-eye me-2"></i> Customer View
                        </a>
                        <a href="../index.php" class="nav-link">
                            <i class="fas fa-home me-2"></i> Back to Site
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
                            <h1><i class="fas fa-tachometer-alt"></i> Rewards & Coupons Dashboard</h1>
                            <p class="text-muted">Comprehensive management system for customer rewards and coupons</p>
                        </div>
                        <div>
                            <button class="quick-action-btn" onclick="location.href='coupon_management.php?action=create'">
                                <i class="fas fa-plus"></i> New Coupon
                            </button>
                            <button class="quick-action-btn" onclick="location.href='rewards_management.php?action=create'">
                                <i class="fas fa-gift"></i> New Reward
                            </button>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-primary"><?php echo number_format($stats['active_customers']); ?></div>
                                <div class="stat-label">Active Customers</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-success"><?php echo number_format($stats['total_points_awarded']); ?></div>
                                <div class="stat-label">Points Awarded</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-warning"><?php echo number_format($stats['active_coupons']); ?></div>
                                <div class="stat-label">Active Coupons</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-info"><?php echo number_format($stats['total_coupons_used']); ?></div>
                                <div class="stat-label">Coupons Used</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Secondary Stats -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-danger"><?php echo number_format($stats['total_points_redeemed']); ?></div>
                                <div class="stat-label">Points Redeemed</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-secondary"><?php echo number_format($stats['total_rewards']); ?></div>
                                <div class="stat-label">Active Rewards</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-dark"><?php echo number_format($stats['monthly_redemptions']); ?></div>
                                <div class="stat-label">Monthly Redemptions</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number text-success">
                                    <?php echo $stats['total_points_awarded'] > 0 ? number_format(($stats['total_points_redeemed'] / $stats['total_points_awarded']) * 100, 1) : 0; ?>%
                                </div>
                                <div class="stat-label">Redemption Rate</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts and Data -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="chart-container">
                                <h4><i class="fas fa-chart-line"></i> Points & Redemptions Trend</h4>
                                <canvas id="trendsChart" height="100"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="chart-container">
                                <h4><i class="fas fa-crown"></i> Top Customers by Points</h4>
                                <?php if (!empty($stats['top_customers'])): ?>
                                    <?php foreach ($stats['top_customers'] as $customer): ?>
                                        <div class="top-customer">
                                            <div>
                                                <strong><?php echo htmlspecialchars($customer['Name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($customer['Email']); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success"><?php echo number_format($customer['total_points']); ?> pts</span><br>
                                                <small class="text-muted">Lifetime: <?php echo number_format($customer['lifetime_points']); ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No customer data available yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="chart-container">
                                <h4><i class="fas fa-bolt"></i> Quick Actions</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="coupon_management.php" class="btn quick-action-btn w-100">
                                            <i class="fas fa-ticket-alt"></i><br>Manage Coupons
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="rewards_management.php" class="btn quick-action-btn w-100">
                                            <i class="fas fa-gift"></i><br>Manage Rewards
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="customer_points.php" class="btn quick-action-btn w-100">
                                            <i class="fas fa-users"></i><br>Customer Points
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="analytics_reports.php" class="btn quick-action-btn w-100">
                                            <i class="fas fa-chart-bar"></i><br>View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sample chart for trends
        const ctx = document.getElementById('trendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Points Awarded',
                    data: [1200, 1900, 3000, 5000, 2000, 3000],
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Points Redeemed',
                    data: [800, 1200, 1800, 2500, 1500, 2200],
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
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

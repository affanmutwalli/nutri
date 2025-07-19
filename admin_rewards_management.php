<?php
session_start();
require_once 'database/dbconnection.php';
require_once 'includes/RewardsSystem.php';
require_once 'includes/CouponSystem.php';

// Initialize database connection
$obj = new main();
$mysqli = $obj->connection();

// Simple admin check (you can enhance this with proper admin authentication)
if (!isset($_SESSION['admin_logged_in'])) {
    // For demo purposes, allow access if user is logged in
    // In production, implement proper admin authentication
    if (!isset($_SESSION['CustomerId'])) {
        header('Location: login.php');
        exit;
    }
}

$rewardsSystem = new RewardsSystem($mysqli);
$couponSystem = new CouponSystem($mysqli);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_coupon':
                createCoupon($_POST);
                break;
            case 'create_reward':
                createReward($_POST);
                break;
        }
    }
}

function createCoupon($data) {
    global $mysqli;
    
    $query = "INSERT INTO enhanced_coupons 
              (coupon_code, coupon_name, description, discount_type, discount_value, 
               minimum_order_amount, usage_limit_per_customer, valid_from, valid_until, 
               is_active, created_by) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'admin')";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssssddiiss", 
        $data['coupon_code'], $data['coupon_name'], $data['description'],
        $data['discount_type'], $data['discount_value'], $data['minimum_order_amount'],
        $data['usage_limit_per_customer'], $data['valid_from'], $data['valid_until']
    );
    
    if ($stmt->execute()) {
        $success_message = "Coupon created successfully!";
    } else {
        $error_message = "Error creating coupon: " . $mysqli->error;
    }
}

function createReward($data) {
    global $mysqli;
    
    $query = "INSERT INTO rewards_catalog 
              (reward_name, reward_description, points_required, reward_type, reward_value, 
               minimum_order_amount, max_redemptions_per_customer, is_active, valid_from, valid_until) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssissdiss", 
        $data['reward_name'], $data['reward_description'], $data['points_required'],
        $data['reward_type'], $data['reward_value'], $data['minimum_order_amount'],
        $data['max_redemptions_per_customer'], $data['valid_from'], $data['valid_until']
    );
    
    if ($stmt->execute()) {
        $success_message = "Reward created successfully!";
    } else {
        $error_message = "Error creating reward: " . $mysqli->error;
    }
}

// Get existing data (with table existence checks)
$coupons = [];
$rewards = [];

try {
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT * FROM enhanced_coupons ORDER BY created_at DESC LIMIT 20");
        if ($result) {
            $coupons = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
} catch (Exception $e) {
    error_log("Error fetching coupons: " . $e->getMessage());
}

try {
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'rewards_catalog'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT * FROM rewards_catalog ORDER BY created_at DESC LIMIT 20");
        if ($result) {
            $rewards = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
} catch (Exception $e) {
    error_log("Error fetching rewards: " . $e->getMessage());
}

// Get statistics (with table existence checks)
$stats = [
    'total_customers' => 0,
    'total_points_awarded' => 0,
    'total_coupons_used' => 0,
    'active_coupons' => 0
];

try {
    $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_master WHERE IsActive = 'Y'");
    if ($result) {
        $stats['total_customers'] = $result->fetch_assoc()['count'];
    }
} catch (Exception $e) {
    error_log("Error getting customer count: " . $e->getMessage());
}

try {
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'points_transactions'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COALESCE(SUM(points_amount), 0) as total FROM points_transactions WHERE transaction_type = 'earned'");
        if ($result) {
            $stats['total_points_awarded'] = $result->fetch_assoc()['total'];
        }
    }
} catch (Exception $e) {
    error_log("Error getting points awarded: " . $e->getMessage());
}

try {
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'coupon_usage'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM coupon_usage");
        if ($result) {
            $stats['total_coupons_used'] = $result->fetch_assoc()['count'];
        }
    }
} catch (Exception $e) {
    error_log("Error getting coupon usage: " . $e->getMessage());
}

try {
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM enhanced_coupons WHERE is_active = 1 AND valid_until > NOW()");
        if ($result) {
            $stats['active_coupons'] = $result->fetch_assoc()['count'];
        }
    }
} catch (Exception $e) {
    error_log("Error getting active coupons: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards & Coupons Management - My Nutrify Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #27ae60;
        }
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">
    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-cogs"></i> Rewards & Coupons Management</h1>
                    <p class="mb-0">Manage customer rewards, coupons, and view analytics</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="index.php" class="btn btn-light">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <div class="stat-number"><?php echo number_format($stats['total_customers']); ?></div>
                    <div class="text-muted">Active Customers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                    <div class="stat-number"><?php echo number_format($stats['total_points_awarded']); ?></div>
                    <div class="text-muted">Points Awarded</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-ticket-alt fa-2x text-success mb-2"></i>
                    <div class="stat-number"><?php echo number_format($stats['total_coupons_used']); ?></div>
                    <div class="text-muted">Coupons Used</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-tags fa-2x text-info mb-2"></i>
                    <div class="stat-number"><?php echo number_format($stats['active_coupons']); ?></div>
                    <div class="text-muted">Active Coupons</div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="coupons-tab" data-bs-toggle="tab" data-bs-target="#coupons" type="button" role="tab">
                    <i class="fas fa-ticket-alt"></i> Manage Coupons
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rewards-tab" data-bs-toggle="tab" data-bs-target="#rewards" type="button" role="tab">
                    <i class="fas fa-gift"></i> Manage Rewards
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                    <i class="fas fa-chart-bar"></i> Analytics
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabContent">
            <!-- Coupons Tab -->
            <div class="tab-pane fade show active" id="coupons" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-section">
                            <h4><i class="fas fa-plus"></i> Create New Coupon</h4>
                            <form method="POST">
                                <input type="hidden" name="action" value="create_coupon">
                                
                                <div class="mb-3">
                                    <label for="coupon_code" class="form-label">Coupon Code</label>
                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="coupon_name" class="form-label">Coupon Name</label>
                                    <input type="text" class="form-control" id="coupon_name" name="coupon_name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="discount_type" class="form-label">Discount Type</label>
                                            <select class="form-control" id="discount_type" name="discount_type" required>
                                                <option value="fixed">Fixed Amount</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="discount_value" class="form-label">Discount Value</label>
                                            <input type="number" class="form-control" id="discount_value" name="discount_value" step="0.01" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="minimum_order_amount" class="form-label">Minimum Order Amount</label>
                                            <input type="number" class="form-control" id="minimum_order_amount" name="minimum_order_amount" step="0.01" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="usage_limit_per_customer" class="form-label">Usage Limit per Customer</label>
                                            <input type="number" class="form-control" id="usage_limit_per_customer" name="usage_limit_per_customer" value="1">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="valid_from" class="form-label">Valid From</label>
                                            <input type="datetime-local" class="form-control" id="valid_from" name="valid_from" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="valid_until" class="form-label">Valid Until</label>
                                            <input type="datetime-local" class="form-control" id="valid_until" name="valid_until" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Coupon
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="table-section">
                            <h4><i class="fas fa-list"></i> Recent Coupons</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Discount</th>
                                            <th>Status</th>
                                            <th>Usage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($coupons as $coupon): ?>
                                            <tr>
                                                <td><code><?php echo htmlspecialchars($coupon['coupon_code']); ?></code></td>
                                                <td><?php echo htmlspecialchars($coupon['coupon_name']); ?></td>
                                                <td>
                                                    <?php echo $coupon['discount_type'] === 'fixed' ? '₹' . $coupon['discount_value'] : $coupon['discount_value'] . '%'; ?>
                                                </td>
                                                <td>
                                                    <?php if ($coupon['is_active'] && strtotime($coupon['valid_until']) > time()): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $coupon['current_usage_count']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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
    </script>
</body>
</html>

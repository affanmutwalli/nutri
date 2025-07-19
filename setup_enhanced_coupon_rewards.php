<?php
/**
 * Setup Enhanced Coupon and Rewards System
 * Run this script to initialize the enhanced coupon and rewards system
 */

require_once 'database/dbconnection.php';

// Initialize database connection
$obj = new main();
$mysqli = $obj->connection();

$setupSteps = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    try {
        // Step 1: Create enhanced coupon system tables
        $sql = file_get_contents('database/enhanced_coupon_system.sql');
        if ($sql) {
            $queries = explode(';', $sql);
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query) && !preg_match('/^(--|SELECT)/i', $query)) {
                    if ($mysqli->query($query)) {
                        $setupSteps[] = "✅ Executed: " . substr($query, 0, 50) . "...";
                    } else {
                        $errors[] = "❌ Error executing query: " . $mysqli->error;
                    }
                }
            }
        }
        
        // Step 2: Create rewards system tables (if not exists)
        $rewardsSql = file_get_contents('database/rewards_system_schema.sql');
        if ($rewardsSql) {
            $queries = explode(';', $rewardsSql);
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query) && !preg_match('/^(--|SELECT)/i', $query)) {
                    if ($mysqli->query($query)) {
                        $setupSteps[] = "✅ Executed rewards query: " . substr($query, 0, 50) . "...";
                    } else {
                        // Don't treat as error if table already exists
                        if (strpos($mysqli->error, 'already exists') === false) {
                            $errors[] = "❌ Error executing rewards query: " . $mysqli->error;
                        }
                    }
                }
            }
        }
        
        // Step 3: Check existing columns and add missing ones to order_master
        $setupSteps[] = "🔍 Checking order_master table structure...";

        // Check which columns already exist
        $existingColumns = [];
        $result = $mysqli->query("SHOW COLUMNS FROM order_master");
        while ($row = $result->fetch_assoc()) {
            $existingColumns[] = $row['Field'];
        }

        $columnsToAdd = [
            'CouponCode' => "ALTER TABLE order_master ADD COLUMN CouponCode VARCHAR(50) NULL AFTER PaymentType",
            'CouponDiscount' => "ALTER TABLE order_master ADD COLUMN CouponDiscount DECIMAL(10,2) DEFAULT 0 AFTER PaymentType",
            'PointsUsed' => "ALTER TABLE order_master ADD COLUMN PointsUsed INT DEFAULT 0 AFTER PaymentType",
            'PointsDiscount' => "ALTER TABLE order_master ADD COLUMN PointsDiscount DECIMAL(10,2) DEFAULT 0 AFTER PaymentType"
        ];

        $alterQueries = [];
        foreach ($columnsToAdd as $columnName => $query) {
            if (!in_array($columnName, $existingColumns)) {
                $alterQueries[] = $query;
            } else {
                $setupSteps[] = "ℹ️ Column '$columnName' already exists";
            }
        }
        
        foreach ($alterQueries as $query) {
            if ($mysqli->query($query)) {
                $setupSteps[] = "✅ Added column to order_master table";
            } else {
                if (strpos($mysqli->error, 'Duplicate column') !== false) {
                    $setupSteps[] = "ℹ️ Column already exists - skipping";
                } else {
                    $errors[] = "❌ Error adding column: " . $mysqli->error;
                }
            }
        }
        
        // Step 4: Check existing indexes and create missing ones
        $setupSteps[] = "🔍 Checking database indexes...";

        // Check existing indexes on order_master
        $existingIndexes = [];
        $result = $mysqli->query("SHOW INDEX FROM order_master");
        while ($row = $result->fetch_assoc()) {
            $existingIndexes[] = $row['Key_name'];
        }

        // Check existing indexes on customer_master
        $result = $mysqli->query("SHOW INDEX FROM customer_master");
        while ($row = $result->fetch_assoc()) {
            $existingIndexes[] = $row['Key_name'];
        }

        $indexesToCreate = [
            'idx_order_coupon' => "CREATE INDEX idx_order_coupon ON order_master(CouponCode)",
            'idx_order_points' => "CREATE INDEX idx_order_points ON order_master(PointsUsed)",
            'idx_customer_active' => "CREATE INDEX idx_customer_active ON customer_master(IsActive(10))"
        ];

        $indexQueries = [];
        foreach ($indexesToCreate as $indexName => $query) {
            if (!in_array($indexName, $existingIndexes)) {
                $indexQueries[] = $query;
            } else {
                $setupSteps[] = "ℹ️ Index '$indexName' already exists";
            }
        }
        
        foreach ($indexQueries as $query) {
            if ($mysqli->query($query)) {
                $setupSteps[] = "✅ Created performance index";
            } else {
                if (strpos($mysqli->error, 'Duplicate key') !== false ||
                    strpos($mysqli->error, 'already exists') !== false) {
                    $setupSteps[] = "ℹ️ Index already exists - skipping";
                } else {
                    $errors[] = "❌ Error creating index: " . $mysqli->error;
                }
            }
        }
        
        $setupComplete = true;
        
    } catch (Exception $e) {
        $errors[] = "❌ Setup failed: " . $e->getMessage();
    }
}

// Check if system is already set up
$tablesExist = [
    'enhanced_coupons' => $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'")->num_rows > 0,
    'customer_points' => $mysqli->query("SHOW TABLES LIKE 'customer_points'")->num_rows > 0,
    'rewards_catalog' => $mysqli->query("SHOW TABLES LIKE 'rewards_catalog'")->num_rows > 0
];

$isSetup = $tablesExist['enhanced_coupons'] && $tablesExist['customer_points'] && $tablesExist['rewards_catalog'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Coupon & Rewards System Setup - My Nutrify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .setup-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .setup-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #27ae60;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 5px 5px 0;
        }
        .btn-setup {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .btn-setup:hover {
            transform: translateY(-2px);
            color: white;
        }
        .status-good { color: #27ae60; }
        .status-bad { color: #e74c3c; }
        .log-item {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            margin: 0.25rem 0;
            font-family: monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body style="background-color: #ecf0f1;">
    <!-- Header -->
    <div class="setup-header">
        <div class="container text-center">
            <h1><i class="fas fa-rocket"></i> Enhanced Coupon & Rewards System Setup</h1>
            <p class="lead">Initialize the advanced coupon and rewards system for My Nutrify</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <?php if (isset($setupComplete) && $setupComplete): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h4 class="alert-heading"><i class="fas fa-check-circle"></i> Setup Complete!</h4>
                        <p>The enhanced coupon and rewards system has been successfully set up.</p>
                        <hr>
                        <div class="d-flex gap-3">
                            <a href="customer_rewards_dashboard.php" class="btn btn-success">
                                <i class="fas fa-gift"></i> View Customer Dashboard
                            </a>
                            <a href="admin_rewards_management.php" class="btn btn-primary">
                                <i class="fas fa-cogs"></i> Admin Management
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Back to Home
                            </a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="setup-card">
                    <h2><i class="fas fa-info-circle"></i> System Status</h2>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Enhanced Coupons</h5>
                            <p class="<?php echo $tablesExist['enhanced_coupons'] ? 'status-good' : 'status-bad'; ?>">
                                <i class="fas fa-<?php echo $tablesExist['enhanced_coupons'] ? 'check' : 'times'; ?>-circle"></i>
                                <?php echo $tablesExist['enhanced_coupons'] ? 'Installed' : 'Not Installed'; ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h5>Customer Points</h5>
                            <p class="<?php echo $tablesExist['customer_points'] ? 'status-good' : 'status-bad'; ?>">
                                <i class="fas fa-<?php echo $tablesExist['customer_points'] ? 'check' : 'times'; ?>-circle"></i>
                                <?php echo $tablesExist['customer_points'] ? 'Installed' : 'Not Installed'; ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h5>Rewards Catalog</h5>
                            <p class="<?php echo $tablesExist['rewards_catalog'] ? 'status-good' : 'status-bad'; ?>">
                                <i class="fas fa-<?php echo $tablesExist['rewards_catalog'] ? 'check' : 'times'; ?>-circle"></i>
                                <?php echo $tablesExist['rewards_catalog'] ? 'Installed' : 'Not Installed'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if (!$isSetup): ?>
                    <div class="setup-card">
                        <h2><i class="fas fa-cogs"></i> Setup Process</h2>
                        
                        <div class="step">
                            <h5>Step 1: Database Tables</h5>
                            <p>Create enhanced coupon tables, customer points system, and rewards catalog with proper relationships and indexes.</p>
                        </div>
                        
                        <div class="step">
                            <h5>Step 2: Order Integration</h5>
                            <p>Add coupon and points tracking columns to the order system for seamless integration.</p>
                        </div>
                        
                        <div class="step">
                            <h5>Step 3: Default Data</h5>
                            <p>Insert default configuration, sample rewards, and performance optimizations.</p>
                        </div>
                        
                        <div class="step">
                            <h5>Step 4: System Integration</h5>
                            <p>Configure the system for automatic points awarding and coupon validation.</p>
                        </div>
                        
                        <div class="text-center mt-4">
                            <form method="POST">
                                <button type="submit" name="setup" class="btn btn-setup">
                                    <i class="fas fa-rocket"></i> Start Setup Process
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="setup-card">
                        <h2><i class="fas fa-check-circle text-success"></i> System Ready</h2>
                        <p>The enhanced coupon and rewards system is already set up and ready to use!</p>
                        
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="customer_rewards_dashboard.php" class="btn btn-success">
                                <i class="fas fa-gift"></i> Customer Dashboard
                            </a>
                            <a href="admin_rewards_management.php" class="btn btn-primary">
                                <i class="fas fa-cogs"></i> Admin Management
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($setupSteps) || !empty($errors)): ?>
                    <div class="setup-card">
                        <h3><i class="fas fa-list"></i> Setup Log</h3>
                        
                        <?php foreach ($setupSteps as $step): ?>
                            <div class="log-item text-success"><?php echo htmlspecialchars($step); ?></div>
                        <?php endforeach; ?>
                        
                        <?php foreach ($errors as $error): ?>
                            <div class="log-item text-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

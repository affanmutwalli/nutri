<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🎁 Rewards System Debug</h2>";

// Get customer ID from session
$customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

if ($customerId) {
    echo "<p><strong>Customer ID:</strong> $customerId</p>";
} else {
    echo "<p style='color: red;'>❌ Not logged in! Please log in to check rewards.</p>";
    echo "<p><a href='login.php'>Login</a></p>";
    exit;
}

// Check if rewards tables exist
echo "<h3>1. Rewards Tables Status:</h3>";
$rewardsTables = [
    'customer_points',
    'points_transactions', 
    'rewards_catalog',
    'reward_redemptions',
    'customer_referrals',
    'points_config'
];

$tablesExist = [];
foreach ($rewardsTables as $table) {
    $checkQuery = "SHOW TABLES LIKE '$table'";
    $result = $mysqli->query($checkQuery);
    $exists = $result && $result->num_rows > 0;
    $tablesExist[$table] = $exists;
    
    $status = $exists ? '✅' : '❌';
    echo "<p>$status <strong>$table</strong></p>";
}

$allTablesExist = !in_array(false, $tablesExist);

if (!$allTablesExist) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h4>⚠️ Rewards System Not Set Up!</h4>";
    echo "<p>Some rewards tables are missing. You need to set up the rewards system first.</p>";
    echo "<p><a href='setup_rewards_system.php' style='background: #ff8c00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Setup Rewards System</a></p>";
    echo "</div>";
} else {
    echo "<p style='color: green;'>✅ All rewards tables exist!</p>";
}

// Check customer points
echo "<h3>2. Customer Points Status:</h3>";
if ($allTablesExist) {
    $pointsQuery = "SELECT * FROM customer_points WHERE customer_id = ?";
    $stmt = $mysqli->prepare($pointsQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $pointsResult = $stmt->get_result();
    
    if ($pointsResult->num_rows > 0) {
        $points = $pointsResult->fetch_assoc();
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        foreach ($points as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No points record found for customer $customerId</p>";
        echo "<p>This means you haven't been added to the rewards system yet.</p>";
    }
}

// Check points transactions
echo "<h3>3. Points Transactions:</h3>";
if ($allTablesExist) {
    $transQuery = "SELECT * FROM points_transactions WHERE customer_id = ? ORDER BY created_at DESC LIMIT 10";
    $stmt = $mysqli->prepare($transQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $transResult = $stmt->get_result();
    
    if ($transResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Date</th><th>Type</th><th>Points</th><th>Description</th><th>Order ID</th></tr>";
        while ($trans = $transResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $trans['created_at'] . "</td>";
            echo "<td>" . $trans['transaction_type'] . "</td>";
            echo "<td>" . $trans['points'] . "</td>";
            echo "<td>" . $trans['description'] . "</td>";
            echo "<td>" . ($trans['order_id'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No points transactions found</p>";
        echo "<p>This means you haven't earned any points yet.</p>";
    }
}

// Check recent orders
echo "<h3>4. Recent Orders Analysis:</h3>";
$ordersQuery = "SELECT OrderId, OrderDate, Amount, OrderStatus, CreatedAt FROM order_master WHERE CustomerId = ? ORDER BY CreatedAt DESC LIMIT 5";
$stmt = $mysqli->prepare($ordersQuery);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$ordersResult = $stmt->get_result();

if ($ordersResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Order ID</th><th>Date</th><th>Amount</th><th>Status</th><th>Points Earned?</th></tr>";
    
    while ($order = $ordersResult->fetch_assoc()) {
        // Check if points were awarded for this order
        $pointsCheck = '';
        if ($allTablesExist) {
            $checkPointsQuery = "SELECT points FROM points_transactions WHERE customer_id = ? AND order_id = ? AND transaction_type = 'earned'";
            $checkStmt = $mysqli->prepare($checkPointsQuery);
            $checkStmt->bind_param("is", $customerId, $order['OrderId']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $earnedPoints = $checkResult->fetch_assoc()['points'];
                $pointsCheck = "✅ $earnedPoints points";
            } else {
                $pointsCheck = "❌ No points";
            }
        }
        
        echo "<tr>";
        echo "<td>" . $order['OrderId'] . "</td>";
        echo "<td>" . $order['OrderDate'] . "</td>";
        echo "<td>₹" . $order['Amount'] . "</td>";
        echo "<td>" . $order['OrderStatus'] . "</td>";
        echo "<td>$pointsCheck</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders found for customer $customerId</p>";
}

// Check points configuration
echo "<h3>5. Points Configuration:</h3>";
if ($allTablesExist) {
    $configQuery = "SELECT * FROM points_config";
    $configResult = $mysqli->query($configQuery);
    
    if ($configResult && $configResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        while ($config = $configResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $config['config_key'] . "</td>";
            echo "<td>" . $config['config_value'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No points configuration found</p>";
    }
}

// Check if RewardsSystem class exists
echo "<h3>6. RewardsSystem Class:</h3>";
if (file_exists('includes/RewardsSystem.php')) {
    echo "<p>✅ RewardsSystem.php file exists</p>";
    
    try {
        include_once 'includes/RewardsSystem.php';
        $rewards = new RewardsSystem();
        echo "<p>✅ RewardsSystem class loaded successfully</p>";
        
        // Test getting customer points
        if ($allTablesExist) {
            $customerPoints = $rewards->getCustomerPoints($customerId);
            echo "<p><strong>Customer Points Data:</strong></p>";
            echo "<pre>" . print_r($customerPoints, true) . "</pre>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error loading RewardsSystem: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ RewardsSystem.php file not found</p>";
}

// Check order processing integration
echo "<h3>7. Order Processing Integration:</h3>";
$orderFiles = [
    'exe_files/dcus_place_order_cod.php',
    'exe_files/dcus_place_order_online.php'
];

foreach ($orderFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $hasRewardsIntegration = strpos($content, 'RewardsSystem') !== false || strpos($content, 'points') !== false;
        
        $status = $hasRewardsIntegration ? '✅' : '❌';
        echo "<p>$status <strong>$file</strong> - " . ($hasRewardsIntegration ? 'Has rewards integration' : 'No rewards integration') . "</p>";
    } else {
        echo "<p>❌ <strong>$file</strong> - File not found</p>";
    }
}

// Recommendations
echo "<h3>🛠️ Recommendations:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";

if (!$allTablesExist) {
    echo "<h4>1. Set Up Rewards System</h4>";
    echo "<p>First, you need to set up the rewards system database tables.</p>";
    echo "<p><a href='setup_rewards_system.php' style='background: #ff8c00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Setup Rewards System</a></p>";
} else {
    echo "<h4>Possible Issues:</h4>";
    echo "<ul>";
    echo "<li><strong>No Points Integration:</strong> Order placement files may not be awarding points</li>";
    echo "<li><strong>Missing Customer Record:</strong> You may not be registered in the rewards system</li>";
    echo "<li><strong>Order Status:</strong> Points may only be awarded for completed orders</li>";
    echo "<li><strong>Manual Trigger:</strong> Points may need to be manually triggered</li>";
    echo "</ul>";
    
    echo "<h4>Quick Fixes:</h4>";
    echo "<ul>";
    echo "<li>Add rewards integration to order placement files</li>";
    echo "<li>Create customer points record</li>";
    echo "<li>Manually award points for existing orders</li>";
    echo "</ul>";
}

echo "</div>";

echo "<br><p><a href='index.php'>Homepage</a> | <a href='rewards.php'>Rewards Page</a> | <a href='cms/'>Admin Panel</a></p>";
?>

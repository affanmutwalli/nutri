<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Debug Rewards System</h2>";

try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    echo "<p>✅ Database connected</p>";
    
    // Test RewardsSystem class
    include_once 'includes/RewardsSystem.php';
    $rewards = new RewardsSystem();
    
    echo "<p>✅ RewardsSystem loaded</p>";
    
    // Test the exact same call that's failing
    echo "<h3>Testing awardOrderPoints():</h3>";
    
    $customerId = 1;
    $orderId = 'TEST_ORDER_' . time();
    $orderAmount = 299;
    
    echo "<p><strong>Parameters:</strong></p>";
    echo "<ul>";
    echo "<li>Customer ID: $customerId</li>";
    echo "<li>Order ID: $orderId</li>";
    echo "<li>Order Amount: ₹$orderAmount</li>";
    echo "</ul>";
    
    // Test step by step
    echo "<h4>Step 1: Check customer points record</h4>";
    $customerCheck = $mysqli->query("SELECT * FROM customer_points WHERE customer_id = $customerId");
    if ($customerCheck && $customerCheck->num_rows > 0) {
        $customer = $customerCheck->fetch_assoc();
        echo "<p>✅ Customer points record exists</p>";
        echo "<pre>" . print_r($customer, true) . "</pre>";
    } else {
        echo "<p>⚠️ No customer points record - will be created</p>";
    }
    
    echo "<h4>Step 2: Check points configuration</h4>";
    $configCheck = $mysqli->query("SELECT * FROM points_config");
    if ($configCheck && $configCheck->num_rows > 0) {
        echo "<p>✅ Points configuration exists</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Key</th><th>Value</th></tr>";
        while ($config = $configCheck->fetch_assoc()) {
            echo "<tr><td>{$config['config_key']}</td><td>{$config['config_value']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No points configuration found</p>";
    }
    
    echo "<h4>Step 3: Test points calculation</h4>";
    $pointsPerRupee = 3; // Default
    $expectedPoints = floor(($orderAmount / 100) * $pointsPerRupee);
    echo "<p><strong>Expected Points:</strong> $expectedPoints (₹$orderAmount ÷ 100 × $pointsPerRupee = $expectedPoints)</p>";
    
    echo "<h4>Step 4: Test actual awardOrderPoints() call</h4>";
    
    try {
        $result = $rewards->awardOrderPoints($customerId, $orderId, $orderAmount);
        echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<p>✅ <strong>Success!</strong> Points awarded: $result</p>";
        echo "</div>";
        
        // Check if transaction was recorded
        $transactionCheck = $mysqli->query("SELECT * FROM points_transactions WHERE customer_id = $customerId AND order_id = '$orderId'");
        if ($transactionCheck && $transactionCheck->num_rows > 0) {
            $transaction = $transactionCheck->fetch_assoc();
            echo "<p>✅ Transaction recorded in database</p>";
            echo "<pre>" . print_r($transaction, true) . "</pre>";
        } else {
            echo "<p>❌ No transaction found in database</p>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<p>❌ <strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>Stack trace:</strong></p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        echo "</div>";
    }
    
    echo "<h4>Step 5: Check recent error logs</h4>";
    $recentErrors = [];
    $errorLog = error_get_last();
    if ($errorLog) {
        echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
        echo "<p><strong>Last PHP Error:</strong></p>";
        echo "<pre>" . print_r($errorLog, true) . "</pre>";
        echo "</div>";
    }
    
    echo "<h4>Step 6: Manual points insertion test</h4>";
    
    if (isset($_GET['manual_test'])) {
        try {
            $manualOrderId = 'MANUAL_TEST_' . time();
            $manualPoints = 8;
            
            // Manual insertion
            $insertQuery = "INSERT INTO points_transactions (customer_id, transaction_type, points, description, order_id) VALUES (?, 'earned', ?, ?, ?)";
            $stmt = $mysqli->prepare($insertQuery);
            $description = "Manual test points for order $manualOrderId (₹299)";
            $stmt->bind_param("iiss", $customerId, $manualPoints, $description, $manualOrderId);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Manual points insertion successful!</p>";
                
                // Update customer total
                $updateQuery = "UPDATE customer_points SET total_points = total_points + ?, lifetime_points = lifetime_points + ? WHERE customer_id = ?";
                $updateStmt = $mysqli->prepare($updateQuery);
                $updateStmt->bind_param("iii", $manualPoints, $manualPoints, $customerId);
                $updateStmt->execute();
                
                echo "<p style='color: green;'>✅ Customer points updated!</p>";
            } else {
                echo "<p style='color: red;'>❌ Manual insertion failed: " . $stmt->error . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Manual test error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p><a href='?manual_test=1' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Manual Test</a></p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<p>❌ <strong>Fatal Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<h3>🛠️ Quick Fixes:</h3>";
echo "<div style='background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
echo "<ul>";
echo "<li><a href='setup_rewards_system.php'>Re-run Rewards Setup</a></li>";
echo "<li><a href='quick_setup_rewards.php'>Quick Setup</a></li>";
echo "<li><a href='award_missing_points.php'>Award Missing Points</a></li>";
echo "</ul>";
echo "</div>";

echo "<br><p><a href='test_checkout_quick.php'>Test Checkout</a> | <a href='debug_rewards.php'>Full Debug</a></p>";
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Detailed Rewards Debug</h2>";

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h3>Step 1: Database Connection Test</h3>";
if ($mysqli) {
    echo "<p>✅ Database connected</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

echo "<h3>Step 2: Table Structure Check</h3>";

// Check customer_points table structure
$customerTableCheck = $mysqli->query("DESCRIBE customer_points");
if ($customerTableCheck) {
    echo "<h4>customer_points table structure:</h4>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $customerTableCheck->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ customer_points table not found</p>";
}

// Check points_transactions table structure
$transTableCheck = $mysqli->query("DESCRIBE points_transactions");
if ($transTableCheck) {
    echo "<h4>points_transactions table structure:</h4>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $transTableCheck->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ points_transactions table not found</p>";
}

echo "<h3>Step 3: Manual SQL Test</h3>";

// Test manual insertion
$customerId = 1;
$testOrderId = 'MANUAL_SQL_TEST_' . time();
$points = 10;
$description = "Manual SQL test";

echo "<h4>Testing manual SQL insertion:</h4>";

try {
    $mysqli->begin_transaction();
    
    // Test customer record exists
    $customerCheck = $mysqli->query("SELECT customer_id FROM customer_points WHERE customer_id = $customerId");
    if ($customerCheck->num_rows == 0) {
        echo "<p>Creating customer record...</p>";
        $createCustomer = "INSERT INTO customer_points (customer_id, total_points, lifetime_points, tier_level) VALUES ($customerId, 0, 0, 'Bronze')";
        if ($mysqli->query($createCustomer)) {
            echo "<p>✅ Customer record created</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create customer: " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p>✅ Customer record exists</p>";
    }
    
    // Test transaction insertion
    $insertTrans = "INSERT INTO points_transactions (customer_id, transaction_type, points, description, order_id) VALUES ($customerId, 'earned', $points, '$description', '$testOrderId')";
    if ($mysqli->query($insertTrans)) {
        echo "<p>✅ Transaction inserted</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to insert transaction: " . $mysqli->error . "</p>";
        $mysqli->rollback();
        goto skip_update;
    }
    
    // Test customer update
    $updateCustomer = "UPDATE customer_points SET total_points = total_points + $points, lifetime_points = lifetime_points + $points WHERE customer_id = $customerId";
    if ($mysqli->query($updateCustomer)) {
        echo "<p>✅ Customer points updated</p>";
        $mysqli->commit();
        echo "<p style='color: green;'>🎉 Manual SQL test successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update customer: " . $mysqli->error . "</p>";
        $mysqli->rollback();
    }
    
    skip_update:
    
} catch (Exception $e) {
    $mysqli->rollback();
    echo "<p style='color: red;'>❌ Manual SQL test failed: " . $e->getMessage() . "</p>";
}

echo "<h3>Step 4: RewardsSystem Class Test</h3>";

try {
    include_once 'includes/RewardsSystem.php';
    $rewards = new RewardsSystem();
    echo "<p>✅ RewardsSystem class loaded</p>";
    
    // Test configuration
    $reflection = new ReflectionClass($rewards);
    $configProperty = $reflection->getProperty('config');
    $configProperty->setAccessible(true);
    $config = $configProperty->getValue($rewards);
    
    echo "<h4>Configuration:</h4>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    foreach ($config as $key => $value) {
        echo "<tr><td>$key</td><td>$value</td></tr>";
    }
    echo "</table>";
    
    // Test step by step
    echo "<h4>Testing awardOrderPoints step by step:</h4>";
    
    $customerId = 1;
    $orderId = 'STEP_TEST_' . time();
    $orderAmount = 300;
    
    echo "<p><strong>Input:</strong> Customer $customerId, Order $orderId, Amount ₹$orderAmount</p>";
    
    // Manual calculation
    $pointsPerRupee = floatval($config['points_per_rupee']);
    $expectedPoints = floor(($orderAmount / 100) * $pointsPerRupee);
    echo "<p><strong>Expected Points:</strong> $expectedPoints (₹$orderAmount ÷ 100 × $pointsPerRupee)</p>";
    
    // Test the method
    echo "<p><strong>Calling awardOrderPoints...</strong></p>";
    $result = $rewards->awardOrderPoints($customerId, $orderId, $orderAmount);
    
    echo "<p><strong>Result:</strong> " . var_export($result, true) . " (type: " . gettype($result) . ")</p>";
    
    if ($result === false) {
        echo "<p style='color: red;'>❌ Method returned FALSE - check error logs</p>";
        
        // Check recent error logs
        $errorLog = error_get_last();
        if ($errorLog) {
            echo "<h4>Last PHP Error:</h4>";
            echo "<pre>" . print_r($errorLog, true) . "</pre>";
        }
        
    } elseif (is_numeric($result) && $result > 0) {
        echo "<p style='color: green;'>✅ Method successful! Returned $result points</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Unexpected result: $result</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ RewardsSystem error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h3>Step 5: Check Current Points Status</h3>";

$currentPoints = $mysqli->query("SELECT * FROM customer_points WHERE customer_id = 1");
if ($currentPoints && $currentPoints->num_rows > 0) {
    $customer = $currentPoints->fetch_assoc();
    echo "<p><strong>Current Points:</strong> {$customer['total_points']}</p>";
    echo "<p><strong>Lifetime Points:</strong> {$customer['lifetime_points']}</p>";
} else {
    echo "<p>No customer points record found</p>";
}

$recentTrans = $mysqli->query("SELECT * FROM points_transactions WHERE customer_id = 1 ORDER BY created_at DESC LIMIT 3");
if ($recentTrans && $recentTrans->num_rows > 0) {
    echo "<h4>Recent Transactions:</h4>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Order ID</th><th>Points</th><th>Description</th><th>Created</th></tr>";
    while ($trans = $recentTrans->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$trans['order_id']}</td>";
        echo "<td>{$trans['points']}</td>";
        echo "<td>{$trans['description']}</td>";
        echo "<td>{$trans['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No recent transactions found</p>";
}

echo "<br><p><a href='verify_points_working.php'>Back to Verify</a></p>";
?>

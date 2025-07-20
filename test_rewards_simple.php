<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 Simple Rewards Test</h2>";

try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    echo "<p>✅ Database connected</p>";
    
    include_once 'includes/RewardsSystem.php';
    $rewards = new RewardsSystem();
    
    echo "<p>✅ RewardsSystem loaded</p>";
    
    // Test with exact same parameters as the failing order
    $customerId = 1;
    $orderId = 'SIMPLE_TEST_' . time();
    $orderAmount = 299;
    
    echo "<h3>Test Parameters:</h3>";
    echo "<ul>";
    echo "<li><strong>Customer ID:</strong> $customerId</li>";
    echo "<li><strong>Order ID:</strong> $orderId</li>";
    echo "<li><strong>Order Amount:</strong> ₹$orderAmount</li>";
    echo "</ul>";
    
    // Check configuration
    echo "<h3>Configuration Check:</h3>";
    $reflection = new ReflectionClass($rewards);
    $configProperty = $reflection->getProperty('config');
    $configProperty->setAccessible(true);
    $config = $configProperty->getValue($rewards);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Config Key</th><th>Value</th></tr>";
    foreach ($config as $key => $value) {
        echo "<tr><td>$key</td><td>$value</td></tr>";
    }
    echo "</table>";
    
    // Manual calculation
    $pointsPerRupee = floatval($config['points_per_rupee']);
    $expectedPoints = floor(($orderAmount / 100) * $pointsPerRupee);
    
    echo "<h3>Manual Calculation:</h3>";
    echo "<p><strong>Points per rupee:</strong> $pointsPerRupee</p>";
    echo "<p><strong>Calculation:</strong> floor(($orderAmount / 100) * $pointsPerRupee) = floor(" . ($orderAmount / 100) . " * $pointsPerRupee) = $expectedPoints</p>";
    
    // Test the method
    echo "<h3>Method Test:</h3>";
    
    $result = $rewards->awardOrderPoints($customerId, $orderId, $orderAmount);
    
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p><strong>Result:</strong> " . var_export($result, true) . "</p>";
    echo "<p><strong>Type:</strong> " . gettype($result) . "</p>";
    
    if ($result === false) {
        echo "<p style='color: red;'>❌ Method returned FALSE</p>";
    } elseif ($result === 0) {
        echo "<p style='color: orange;'>⚠️ Method returned 0 (no points)</p>";
    } elseif (is_numeric($result) && $result > 0) {
        echo "<p style='color: green;'>✅ Method returned $result points</p>";
    } else {
        echo "<p style='color: red;'>❌ Unexpected result: $result</p>";
    }
    echo "</div>";
    
    // Check if transaction was created
    echo "<h3>Database Check:</h3>";
    $transactionQuery = "SELECT * FROM points_transactions WHERE customer_id = ? AND order_id = ?";
    $stmt = $mysqli->prepare($transactionQuery);
    $stmt->bind_param("is", $customerId, $orderId);
    $stmt->execute();
    $transactionResult = $stmt->get_result();
    
    if ($transactionResult->num_rows > 0) {
        $transaction = $transactionResult->fetch_assoc();
        echo "<p style='color: green;'>✅ Transaction found in database</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        foreach ($transaction as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No transaction found in database</p>";
    }
    
    // Check customer points
    $customerQuery = "SELECT * FROM customer_points WHERE customer_id = ?";
    $stmt = $mysqli->prepare($customerQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $customerResult = $stmt->get_result();
    
    if ($customerResult->num_rows > 0) {
        $customer = $customerResult->fetch_assoc();
        echo "<h3>Customer Points Record:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        foreach ($customer as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No customer points record found</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<p>❌ <strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<br><p><a href='test_checkout_quick.php'>Back to Checkout Test</a></p>";
?>

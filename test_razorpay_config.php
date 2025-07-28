<?php
/**
 * Test Razorpay Configuration and API
 */

header("Content-Type: text/html");
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Razorpay Configuration Test</h2>";

// Test 1: Check Razorpay config file
echo "<h3>1. Razorpay Configuration</h3>";
try {
    include 'exe_files/razorpay_config.php';
    
    if (defined('RAZORPAY_KEY_ID') && defined('RAZORPAY_KEY_SECRET')) {
        echo "<p style='color: green;'>✅ Razorpay config loaded successfully</p>";
        echo "<p>Key ID: " . substr(RAZORPAY_KEY_ID, 0, 15) . "...</p>";
        echo "<p>Key Secret: " . substr(RAZORPAY_KEY_SECRET, 0, 10) . "...</p>";
    } else {
        echo "<p style='color: red;'>❌ Razorpay config not loaded properly</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading Razorpay config: " . $e->getMessage() . "</p>";
}

// Test 2: Check Razorpay library
echo "<h3>2. Razorpay Library</h3>";
try {
    if (file_exists('razorpay/Razorpay.php')) {
        require_once('razorpay/Razorpay.php');
        echo "<p style='color: green;'>✅ Razorpay library found</p>";
        
        use Razorpay\Api\Api;
        
        // Test API initialization
        $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
        echo "<p style='color: green;'>✅ Razorpay API initialized</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Razorpay library not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error with Razorpay library: " . $e->getMessage() . "</p>";
}

// Test 3: Test API connectivity (simple test)
echo "<h3>3. API Connectivity Test</h3>";
try {
    if (isset($api)) {
        // Try to create a test order with minimal data
        $testOrderData = [
            'receipt' => 'test_' . time(),
            'amount' => 100, // 1 rupee in paise
            'currency' => 'INR',
            'payment_capture' => 1
        ];
        
        echo "<p>Attempting to create test order...</p>";
        $testOrder = $api->order->create($testOrderData);
        
        if (isset($testOrder['id'])) {
            echo "<p style='color: green;'>✅ Test order created successfully</p>";
            echo "<p>Order ID: " . $testOrder['id'] . "</p>";
            echo "<p>Status: " . $testOrder['status'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Test order creation failed - no ID returned</p>";
        }
        
    } else {
        echo "<p style='color: orange;'>⚠️ API not initialized, skipping connectivity test</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ API connectivity test failed: " . $e->getMessage() . "</p>";
    echo "<p>Error details: " . $e->getTraceAsString() . "</p>";
}

// Test 4: Check recent order creation
echo "<h3>4. Recent Order Check</h3>";
try {
    include_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    if ($mysqli) {
        $recentOrders = $mysqli->query("SELECT OrderId, Amount, PaymentStatus, PaymentType, CreatedAt FROM order_master ORDER BY CreatedAt DESC LIMIT 5");
        
        if ($recentOrders && $recentOrders->num_rows > 0) {
            echo "<p style='color: green;'>✅ Recent orders found:</p>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Order ID</th><th>Amount</th><th>Payment Status</th><th>Payment Type</th><th>Created At</th></tr>";
            
            while ($row = $recentOrders->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['OrderId']) . "</td>";
                echo "<td>₹" . htmlspecialchars($row['Amount']) . "</td>";
                echo "<td>" . htmlspecialchars($row['PaymentStatus']) . "</td>";
                echo "<td>" . htmlspecialchars($row['PaymentType']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CreatedAt']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ No recent orders found</p>";
        }
        
        $mysqli->close();
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database check failed: " . $e->getMessage() . "</p>";
}

// Test 5: Environment check
echo "<h3>5. Environment Check</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Timezone: " . date_default_timezone_get() . "</p>";

// Test 6: Check for any PHP errors
echo "<h3>6. Error Log Check</h3>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $errors = file($errorLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recentErrors = array_slice($errors, -10);
    
    echo "<p>Recent errors from " . $errorLog . ":</p>";
    echo "<pre style='background-color: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;'>";
    foreach ($recentErrors as $error) {
        if (strpos($error, 'razorpay') !== false || strpos($error, 'payment') !== false) {
            echo "<span style='color: red;'>" . htmlspecialchars($error) . "</span>\n";
        } else {
            echo htmlspecialchars($error) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>No error log found or accessible</p>";
}

echo "<h3>Test Complete</h3>";
?>

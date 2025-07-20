<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🚨 Checkout Debug</h2>";

// Test the order files directly
echo "<h3>1. Testing COD Order File:</h3>";

$testData = [
    'name' => 'Test Customer',
    'email' => 'test@example.com',
    'phone' => '9876543210',
    'address' => '123 Test Street',
    'landmark' => 'Near Mall',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'pincode' => '400001',
    'final_total' => 100,
    'CustomerId' => 1,
    'products' => [
        [
            'id' => '1',
            'name' => 'Test Product',
            'code' => 'TP001',
            'size' => 'Medium',
            'quantity' => '1',
            'offer_price' => '100'
        ]
    ]
];

echo "<h4>Testing rcus_place_order_cod.php:</h4>";

// Test COD file
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/exe_files/rcus_place_order_cod.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$codResponse = curl_exec($ch);
$codHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$codError = curl_error($ch);
curl_close($ch);

echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>HTTP Code:</strong> $codHttpCode</p>";
if ($codError) {
    echo "<p><strong>cURL Error:</strong> $codError</p>";
}
echo "<p><strong>Response:</strong></p>";
echo "<pre>" . htmlspecialchars($codResponse) . "</pre>";
echo "</div>";

echo "<h4>Testing rcus_place_order_online_simple.php:</h4>";

// Test Online file
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, 'http://localhost/nutrify/exe_files/rcus_place_order_online_simple.php');
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 10);

$onlineResponse = curl_exec($ch2);
$onlineHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$onlineError = curl_error($ch2);
curl_close($ch2);

echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>HTTP Code:</strong> $onlineHttpCode</p>";
if ($onlineError) {
    echo "<p><strong>cURL Error:</strong> $onlineError</p>";
}
echo "<p><strong>Response:</strong></p>";
echo "<pre>" . htmlspecialchars($onlineResponse) . "</pre>";
echo "</div>";

// Check if RewardsSystem file exists
echo "<h3>2. Checking RewardsSystem:</h3>";
if (file_exists('includes/RewardsSystem.php')) {
    echo "<p>✅ RewardsSystem.php exists</p>";
    
    try {
        include_once 'includes/RewardsSystem.php';
        $rewards = new RewardsSystem();
        echo "<p>✅ RewardsSystem class loads successfully</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ RewardsSystem error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ RewardsSystem.php not found</p>";
}

// Check database connection
echo "<h3>3. Checking Database:</h3>";
try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    if ($mysqli) {
        echo "<p>✅ Database connection successful</p>";
        
        // Check rewards tables
        $tables = ['customer_points', 'points_transactions', 'points_config'];
        foreach ($tables as $table) {
            $result = $mysqli->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<p>✅ Table $table exists</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Table $table missing</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Check PHP error log
echo "<h3>4. Recent PHP Errors:</h3>";
$errorLogPath = ini_get('error_log');
if ($errorLogPath && file_exists($errorLogPath)) {
    $errors = file_get_contents($errorLogPath);
    $recentErrors = array_slice(explode("\n", $errors), -20);
    echo "<pre style='background-color: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars(implode("\n", $recentErrors));
    echo "</pre>";
} else {
    echo "<p>No error log found or accessible</p>";
}

echo "<h3>🛠️ Quick Fixes:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h4>If checkout is broken:</h4>";
echo "<ol>";
echo "<li><a href='?action=remove_rewards' style='color: #dc3545;'>Remove Rewards Integration (Emergency Fix)</a></li>";
echo "<li><a href='?action=fix_syntax' style='color: #28a745;'>Fix Syntax Errors</a></li>";
echo "<li><a href='checkout.php' style='color: #007bff;'>Test Checkout Page</a></li>";
echo "</ol>";
echo "</div>";

// Emergency fixes
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'remove_rewards':
            echo "<h3>🚨 Emergency: Removing Rewards Integration</h3>";
            
            // Backup and restore original files
            $files = [
                'exe_files/rcus_place_order_cod.php',
                'exe_files/rcus_place_order_online_simple.php'
            ];
            
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $content = file_get_contents($file);
                    
                    // Remove rewards integration
                    $content = preg_replace('/\/\/ Award rewards points.*?catch \(Exception \$e\) \{.*?\}/s', '', $content);
                    $content = str_replace("'points_awarded' => \$pointsAwarded", '', $content);
                    $content = str_replace(", 'points_awarded' => \$pointsAwarded", '', $content);
                    
                    file_put_contents($file, $content);
                    echo "<p>✅ Cleaned $file</p>";
                }
            }
            
            echo "<p style='color: green;'>✅ Emergency fix applied. Try checkout now.</p>";
            break;
    }
}

echo "<br><p><a href='checkout.php'>Test Checkout</a> | <a href='index.php'>Homepage</a></p>";
?>

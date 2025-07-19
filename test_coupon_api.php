<?php
/**
 * Simple test for coupon API
 */

echo "<h2>Testing Coupon API</h2>";

// Test data - using all available coupon codes from database
$testCoupons = [
    ['code' => 'WELCOME10', 'amount' => 1000, 'expected' => 'success'],
    ['code' => 'SAVE50', 'amount' => 1500, 'expected' => 'success'],
    ['code' => 'FLAT100', 'amount' => 2500, 'expected' => 'success'],
    ['code' => 'WELCOME50', 'amount' => 1000, 'expected' => 'success'],
    ['code' => 'SAVE100', 'amount' => 1500, 'expected' => 'success'],
    ['code' => 'PERCENT10', 'amount' => 1000, 'expected' => 'success'],
    ['code' => 'FREESHIP', 'amount' => 500, 'expected' => 'success'],
    ['code' => 'WELCOME10', 'amount' => 300, 'expected' => 'error'], // Below minimum
    ['code' => 'INVALID123', 'amount' => 1000, 'expected' => 'error']
];

foreach ($testCoupons as $test) {
    $expectedResult = isset($test['expected']) ? $test['expected'] : 'unknown';
    echo "<h3>Testing: {$test['code']} with order amount ₹{$test['amount']} (Expected: $expectedResult)</h3>";

    $postData = json_encode([
        'code' => $test['code'],
        'order_amount' => $test['amount']
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/exe_files/fetch_coupon.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if ($data) {
            $isSuccess = ($data['response'] === 'S');
            $expectedSuccess = ($expectedResult === 'success');

            if ($isSuccess === $expectedSuccess) {
                if ($isSuccess) {
                    echo "<p style='color: green;'>✅ SUCCESS: {$data['msg']}</p>";
                    if (isset($data['discount'])) {
                        echo "<p style='color: green;'>💰 Discount Applied: ₹{$data['discount']}</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>✅ EXPECTED ERROR: {$data['msg']}</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ UNEXPECTED RESULT: {$data['msg']}</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Invalid JSON response</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ No response received (HTTP: $httpCode)</p>";
    }

    echo "<hr>";
}

echo "<h3>Direct Database Check</h3>";

try {
    include_once 'cms/includes/db_connect.php';
    include_once 'cms/includes/functions.php';
    include('cms/database/dbconnection.php');

    $obj = new main();
    $mysqli = $obj->connection();
    
    $result = $mysqli->query("SELECT coupon_code, coupon_name, discount_type, discount_value, minimum_order_amount, is_active FROM enhanced_coupons WHERE is_active = 1");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Code</th><th>Name</th><th>Type</th><th>Value</th><th>Min Order</th><th>Status</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $discount = $row['discount_type'] === 'fixed' ? '₹' . $row['discount_value'] : $row['discount_value'] . '%';
            echo "<tr>";
            echo "<td>{$row['coupon_code']}</td>";
            echo "<td>{$row['coupon_name']}</td>";
            echo "<td>{$row['discount_type']}</td>";
            echo "<td>$discount</td>";
            echo "<td>₹{$row['minimum_order_amount']}</td>";
            echo "<td>" . ($row['is_active'] ? 'Active' : 'Inactive') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No active coupons found in database.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
?>

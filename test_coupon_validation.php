<?php
session_start();

// Set test session
if (!isset($_SESSION['CustomerId'])) {
    $_SESSION['CustomerId'] = 1;
}

echo "<h2>🎫 Test Coupon Validation</h2>";

if (isset($_GET['test_coupon'])) {
    $couponCode = $_GET['test_coupon'];
    $orderAmount = floatval($_GET['amount'] ?? 500);
    
    echo "<h3>Testing Coupon: $couponCode</h3>";
    echo "<p><strong>Order Amount:</strong> ₹$orderAmount</p>";
    
    // Test the coupon validation (send JSON data as expected by fetch_coupon.php)
    $testData = [
        'code' => $couponCode,
        'order_amount' => $orderAmount
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/exe_files/fetch_coupon.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Cookie: ' . session_name() . '=' . session_id()
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
    
    echo "<h4>Raw Response:</h4>";
    echo "<pre style='background: #e9ecef; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars($response);
    echo "</pre>";
    
    // Try to decode JSON
    $jsonData = json_decode($response, true);
    if ($jsonData) {
        echo "<h4>Parsed JSON:</h4>";
        echo "<pre style='background: #d4edda; padding: 10px; border-radius: 5px;'>";
        print_r($jsonData);
        echo "</pre>";
        
        if (isset($jsonData['response']) && $jsonData['response'] === 'S') {
            echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>✅ Coupon Valid!</h4>";
            echo "<p><strong>Message:</strong> {$jsonData['msg']}</p>";
            if (isset($jsonData['discount'])) {
                echo "<p><strong>Discount:</strong> ₹{$jsonData['discount']}</p>";
            }
            echo "</div>";
        } else {
            echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>❌ Coupon Invalid</h4>";
            echo "<p><strong>Message:</strong> {$jsonData['msg']}</p>";
            echo "</div>";
        }
    } else {
        echo "<h4>❌ JSON Parse Error:</h4>";
        echo "<p style='color: red;'>Response is not valid JSON</p>";
    }
    echo "</div>";
    
} else {
    // Show test form
    echo "<h3>🧪 Test Your Coupon Code</h3>";
    
    // Get recent coupons from database
    try {
        require_once 'database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();
        
        $recentCoupons = $mysqli->query("SELECT coupon_code, discount_value, min_order_amount, expires_at, is_used FROM coupons WHERE customer_id = 1 ORDER BY created_at DESC LIMIT 5");
        
        if ($recentCoupons && $recentCoupons->num_rows > 0) {
            echo "<h4>Your Recent Reward Coupons:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 15px 0;'>";
            echo "<tr><th>Coupon Code</th><th>Discount</th><th>Min Order</th><th>Expires</th><th>Status</th><th>Test</th></tr>";
            
            while ($coupon = $recentCoupons->fetch_assoc()) {
                $status = $coupon['is_used'] ? 'Used' : 'Active';
                $statusColor = $coupon['is_used'] ? 'red' : 'green';
                
                echo "<tr>";
                echo "<td style='font-family: monospace;'>{$coupon['coupon_code']}</td>";
                echo "<td>₹{$coupon['discount_value']}</td>";
                echo "<td>₹{$coupon['min_order_amount']}</td>";
                echo "<td>{$coupon['expires_at']}</td>";
                echo "<td style='color: $statusColor;'>$status</td>";
                echo "<td><a href='?test_coupon={$coupon['coupon_code']}&amount=500' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;'>Test</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>No reward coupons found. <a href='test_rewards_modal.php'>Redeem some rewards first!</a></p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
    
    echo "<form method='GET' style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>Manual Test:</h4>";
    echo "<p>";
    echo "<label>Coupon Code:</label><br>";
    echo "<input type='text' name='test_coupon' placeholder='Enter coupon code' style='padding: 8px; width: 200px; margin: 5px 0;' required>";
    echo "</p>";
    echo "<p>";
    echo "<label>Order Amount:</label><br>";
    echo "<input type='number' name='amount' value='500' min='1' step='0.01' style='padding: 8px; width: 200px; margin: 5px 0;' required>";
    echo "</p>";
    echo "<p>";
    echo "<button type='submit' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test Coupon</button>";
    echo "</p>";
    echo "</form>";
}

echo "<h3>🎯 Expected Behavior:</h3>";
echo "<div style='background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
echo "<ul>";
echo "<li>✅ <strong>Valid Coupon:</strong> Shows discount amount and success message</li>";
echo "<li>✅ <strong>Used Coupon:</strong> Shows 'Coupon already used' error</li>";
echo "<li>✅ <strong>Expired Coupon:</strong> Shows 'Coupon expired' error</li>";
echo "<li>✅ <strong>Min Order Not Met:</strong> Shows minimum order requirement</li>";
echo "<li>✅ <strong>Invalid Code:</strong> Shows 'Invalid coupon code' error</li>";
echo "</ul>";
echo "</div>";

echo "<br><p><a href='test_rewards_modal.php'>← Back to Rewards Modal</a> | <a href='checkout.php'>Test in Checkout</a></p>";
?>

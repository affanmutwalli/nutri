<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>✅ Verify Points System Working</h2>";

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

// Check customer record
echo "<h3>1. Customer Record Status:</h3>";
$customerCheck = $mysqli->query("SELECT * FROM customer_points WHERE customer_id = 1");
if ($customerCheck && $customerCheck->num_rows > 0) {
    $customer = $customerCheck->fetch_assoc();
    echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<p>✅ <strong>Customer Record Found</strong></p>";
    echo "<p><strong>Total Points:</strong> {$customer['total_points']}</p>";
    echo "<p><strong>Lifetime Points:</strong> {$customer['lifetime_points']}</p>";
    echo "<p><strong>Tier Level:</strong> {$customer['tier_level']}</p>";
    echo "</div>";
} else {
    echo "<p style='color: red;'>❌ No customer record found</p>";
}

// Test RewardsSystem
echo "<h3>2. RewardsSystem Test:</h3>";
try {
    include_once 'includes/RewardsSystem.php';
    $rewards = new RewardsSystem();
    
    $testOrderId = 'VERIFY_TEST_' . time();
    $testAmount = 300; // Should give 9 points
    
    $result = $rewards->awardOrderPoints(1, $testOrderId, $testAmount);
    
    if ($result && $result > 0) {
        echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<p>✅ <strong>RewardsSystem Working!</strong></p>";
        echo "<p><strong>Test Order:</strong> $testOrderId</p>";
        echo "<p><strong>Amount:</strong> ₹$testAmount</p>";
        echo "<p><strong>Points Awarded:</strong> $result</p>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>❌ RewardsSystem failed: " . var_export($result, true) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ RewardsSystem error: " . $e->getMessage() . "</p>";
}

// Check recent transactions
echo "<h3>3. Recent Points Transactions:</h3>";
$transactionsQuery = "SELECT * FROM points_transactions WHERE customer_id = 1 ORDER BY created_at DESC LIMIT 5";
$transactions = $mysqli->query($transactionsQuery);

if ($transactions && $transactions->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Date</th><th>Type</th><th>Points</th><th>Description</th><th>Order ID</th></tr>";
    
    while ($trans = $transactions->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $trans['created_at'] . "</td>";
        echo "<td>" . $trans['transaction_type'] . "</td>";
        echo "<td style='color: " . ($trans['points'] > 0 ? 'green' : 'red') . "; font-weight: bold;'>" . $trans['points'] . "</td>";
        echo "<td>" . $trans['description'] . "</td>";
        echo "<td>" . ($trans['order_id'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No transactions found</p>";
}

// Test order file integration
echo "<h3>4. Order File Integration Test:</h3>";

$testData = [
    'name' => 'Integration Test',
    'email' => 'test@example.com',
    'phone' => '9876543210',
    'address' => '123 Test Street',
    'landmark' => 'Near Mall',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'pincode' => '400001',
    'final_total' => 450, // Should give 13 points
    'CustomerId' => 1,
    'paymentMethod' => 'COD',
    'products' => [[
        'id' => '1',
        'name' => 'Integration Test Product',
        'code' => 'ITP001',
        'size' => 'Medium',
        'quantity' => '1',
        'offer_price' => '450'
    ]]
];

// Test COD order
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/exe_files/rcus_place_order_cod.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseData = json_decode($response, true);

echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>COD Order Test Result:</h4>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";

if ($responseData) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    foreach ($responseData as $key => $value) {
        $color = '';
        if ($key == 'points_awarded' && $value > 0) {
            $color = 'background-color: #d4edda; color: #155724; font-weight: bold;';
        } elseif ($key == 'response' && $value == 'S') {
            $color = 'background-color: #d4edda; color: #155724;';
        }
        echo "<tr style='$color'><td><strong>$key</strong></td><td>$value</td></tr>";
    }
    echo "</table>";
    
    if (isset($responseData['points_awarded']) && $responseData['points_awarded'] > 0) {
        echo "<p style='color: green;'>🎉 <strong>SUCCESS!</strong> Order integration working with {$responseData['points_awarded']} points awarded!</p>";
    } else {
        echo "<p style='color: red;'>⚠️ Order successful but no points awarded</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Invalid response</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
echo "</div>";

// Final status
echo "<h3>🎯 Final Status:</h3>";

$finalCustomerCheck = $mysqli->query("SELECT total_points FROM customer_points WHERE customer_id = 1");
if ($finalCustomerCheck && $finalCustomerCheck->num_rows > 0) {
    $finalPoints = $finalCustomerCheck->fetch_assoc()['total_points'];
    
    echo "<div style='background-color: #d1ecf1; padding: 20px; border: 1px solid #bee5eb; border-radius: 5px;'>";
    echo "<h4>🎁 Your Current Points: $finalPoints</h4>";
    
    if ($finalPoints > 0) {
        echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ POINTS SYSTEM IS WORKING!</p>";
        echo "<p>🎉 You can now place orders and earn points with the beautiful popup!</p>";
        
        // Calculate what they can redeem
        if ($finalPoints >= 200) {
            echo "<p>🎁 You can redeem: <strong>Free Shipping</strong> (200 points)</p>";
        }
        if ($finalPoints >= 500) {
            echo "<p>🎁 You can redeem: <strong>₹50 Discount Coupon</strong> (500 points)</p>";
        }
        if ($finalPoints >= 1000) {
            echo "<p>🎁 You can redeem: <strong>₹100 Discount Coupon</strong> (1000 points)</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No points yet, but system is ready</p>";
    }
    echo "</div>";
}

echo "<br><p><a href='final_checkout_test.html' style='background: #ff8c00; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>🛒 Test Checkout with Popup</a></p>";
echo "<p><a href='award_missing_points.php'>Award Missing Points</a> | <a href='debug_rewards.php'>Debug Rewards</a></p>";
?>

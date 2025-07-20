<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🚀 Quick Checkout Test</h2>";

$testData = [
    'name' => 'Test Customer',
    'email' => 'test@example.com',
    'phone' => '9876543210',
    'address' => '123 Test Street',
    'landmark' => 'Near Mall',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'pincode' => '400001',
    'final_total' => 299,
    'CustomerId' => 1,
    'paymentMethod' => 'COD',
    'products' => [
        [
            'id' => '1',
            'name' => 'Test Product',
            'code' => 'TP001',
            'size' => 'Medium',
            'quantity' => '1',
            'offer_price' => '299'
        ]
    ]
];

echo "<h3>Testing COD Order (₹299 = 8 points expected):</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/exe_files/rcus_place_order_cod.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
if ($error) {
    echo "<p><strong>cURL Error:</strong> $error</p>";
}

$responseData = json_decode($response, true);
if ($responseData) {
    echo "<h4>✅ Response (JSON):</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
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
        echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>🎉 Points System Working!</h4>";
        echo "<p><strong>Points Awarded:</strong> {$responseData['points_awarded']}</p>";
        echo "<p><strong>Expected:</strong> 8 points (299 ÷ 100 × 3 = 8.97 → 8)</p>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>⚠️ Points Issue</h4>";
        echo "<p>Points not awarded or returned as false/0</p>";
        echo "</div>";
    }
} else {
    echo "<h4>❌ Raw Response:</h4>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
echo "</div>";

// Test online order
echo "<h3>Testing Online Order:</h3>";
$testData['paymentMethod'] = 'Online';

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, 'http://localhost/nutrify/exe_files/rcus_place_order_online_simple.php');
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 15);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$error2 = curl_error($ch2);
curl_close($ch2);

echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>HTTP Code:</strong> $httpCode2</p>";
if ($error2) {
    echo "<p><strong>cURL Error:</strong> $error2</p>";
}

$responseData2 = json_decode($response2, true);
if ($responseData2) {
    echo "<h4>✅ Response (JSON):</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    foreach ($responseData2 as $key => $value) {
        $color = '';
        if ($key == 'points_awarded' && $value > 0) {
            $color = 'background-color: #d4edda; color: #155724; font-weight: bold;';
        } elseif ($key == 'response' && $value == 'S') {
            $color = 'background-color: #d4edda; color: #155724;';
        } elseif ($key == 'response' && $value == 'E') {
            $color = 'background-color: #f8d7da; color: #721c24;';
        }
        echo "<tr style='$color'><td><strong>$key</strong></td><td>$value</td></tr>";
    }
    echo "</table>";
} else {
    echo "<h4>❌ Raw Response:</h4>";
    echo "<pre>" . htmlspecialchars($response2) . "</pre>";
}
echo "</div>";

echo "<h3>🎯 Summary:</h3>";
echo "<div style='background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
if ($httpCode == 200 && isset($responseData['response']) && $responseData['response'] == 'S') {
    echo "<p>✅ <strong>COD Orders:</strong> Working with points!</p>";
} else {
    echo "<p>❌ <strong>COD Orders:</strong> Issues detected</p>";
}

if ($httpCode2 == 200 && isset($responseData2['response']) && $responseData2['response'] == 'S') {
    echo "<p>✅ <strong>Online Orders:</strong> Working with points!</p>";
} else {
    echo "<p>❌ <strong>Online Orders:</strong> Issues detected</p>";
}
echo "</div>";

echo "<br><p><a href='checkout.php'>Test Real Checkout</a> | <a href='debug_rewards.php'>Check Rewards</a> | <a href='test_points_popup.html'>Test Popup</a></p>";
?>

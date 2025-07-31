<?php
// Test payment callback with sample data
header('Content-Type: application/json');

echo "<h2>Testing Payment Callback</h2>";

// Test data that would come from Razorpay
$testData = [
    'razorpay_payment_id' => 'pay_test123456789',
    'razorpay_order_id' => 'order_test123456789',
    'razorpay_signature' => 'test_signature_123',
    'order_db_id' => 'ON000001'
];

echo "<h3>Test Data:</h3>";
echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";

// Make a POST request to the callback
$url = 'http://localhost/nutrify/exe_files/razorpay_callback_bulletproof.php';
$postData = json_encode($testData);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>Response:</h3>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";

if ($error) {
    echo "<p style='color: red;'><strong>cURL Error:</strong> $error</p>";
} else {
    echo "<p><strong>Response Body:</strong></p>";
    echo "<pre>$response</pre>";
    
    // Try to decode JSON
    $jsonResponse = json_decode($response, true);
    if ($jsonResponse) {
        echo "<p><strong>Parsed JSON:</strong></p>";
        echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
    }
}
?>

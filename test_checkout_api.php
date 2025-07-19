<?php
/**
 * Test the coupon API from the same context as checkout page
 */

echo "<h2>Testing Coupon API from Checkout Context</h2>";

// Test if the file exists and is accessible
$apiPath = 'exe_files/fetch_coupon.php';
$fullPath = __DIR__ . '/' . $apiPath;

echo "<p><strong>API Path:</strong> $apiPath</p>";
echo "<p><strong>Full Path:</strong> $fullPath</p>";
echo "<p><strong>File Exists:</strong> " . (file_exists($fullPath) ? 'Yes' : 'No') . "</p>";

if (file_exists($fullPath)) {
    echo "<p><strong>File Readable:</strong> " . (is_readable($fullPath) ? 'Yes' : 'No') . "</p>";
}

// Test direct access
echo "<h3>Testing Direct Access</h3>";
$directUrl = "http://localhost/nutrify/exe_files/fetch_coupon.php";
$headers = @get_headers($directUrl);
if ($headers) {
    echo "<p><strong>Direct Access:</strong> " . $headers[0] . "</p>";
} else {
    echo "<p><strong>Direct Access:</strong> Failed</p>";
}

// Test POST request
echo "<h3>Testing POST Request</h3>";
$testData = json_encode(['code' => 'WELCOME10', 'order_amount' => 1000]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $directUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $testData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
if ($error) {
    echo "<p><strong>cURL Error:</strong> $error</p>";
}
echo "<p><strong>Response:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Test with JavaScript-like fetch simulation
echo "<h3>JavaScript Fetch Simulation</h3>";
?>

<script>
console.log('Testing fetch from browser...');

fetch('exe_files/fetch_coupon.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        code: 'WELCOME10',
        order_amount: 1000
    }),
})
.then(response => {
    console.log('Fetch response status:', response.status);
    console.log('Fetch response ok:', response.ok);
    return response.text();
})
.then(text => {
    console.log('Fetch response text:', text);
    document.getElementById('fetch-result').innerHTML = '<pre>' + text + '</pre>';
})
.catch(error => {
    console.error('Fetch error:', error);
    document.getElementById('fetch-result').innerHTML = '<p style="color: red;">Fetch Error: ' + error.message + '</p>';
});
</script>

<div id="fetch-result">
    <p>Loading JavaScript fetch test...</p>
</div>

<h3>Manual Test</h3>
<p>Try the coupon manually:</p>
<input type="text" id="test-coupon" value="WELCOME10" placeholder="Enter coupon code">
<button onclick="testCoupon()">Test Coupon</button>
<div id="manual-result"></div>

<script>
function testCoupon() {
    const code = document.getElementById('test-coupon').value;
    const resultDiv = document.getElementById('manual-result');
    
    resultDiv.innerHTML = '<p>Testing...</p>';
    
    fetch('exe_files/fetch_coupon.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            code: code,
            order_amount: 1000
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.response === 'S') {
            resultDiv.innerHTML = '<p style="color: green;">✅ Success: ' + data.msg + '</p>';
        } else {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + data.msg + '</p>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<p style="color: red;">❌ Network Error: ' + error.message + '</p>';
    });
}
</script>

<?php
echo "<hr>";
echo "<p><strong>Available Coupons to Test:</strong></p>";
echo "<ul>";
echo "<li>WELCOME10 - 10% off (min ₹500)</li>";
echo "<li>SAVE50 - ₹50 off (min ₹1000)</li>";
echo "<li>FLAT100 - ₹100 off (min ₹2000)</li>";
echo "<li>FREESHIP - ₹50 off (no minimum)</li>";
echo "</ul>";
?>

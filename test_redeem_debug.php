<?php
session_start();

// Set test session
if (!isset($_SESSION['CustomerId'])) {
    $_SESSION['CustomerId'] = 1;
}

echo "<h2>🔍 Debug Redemption Issue</h2>";

echo "<h3>1. Session Check:</h3>";
echo "<p><strong>Customer ID:</strong> " . ($_SESSION['CustomerId'] ?? 'Not set') . "</p>";

echo "<h3>2. Test Redemption API:</h3>";

if (isset($_GET['test_api'])) {
    echo "<h4>Testing redeem_reward.php directly...</h4>";
    
    $testData = [
        'reward_id' => 1,
        'points_required' => 200
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/redeem_reward.php');
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
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
    
    if ($error) {
        echo "<p><strong>cURL Error:</strong> $error</p>";
    }
    
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
    } else {
        echo "<h4>❌ JSON Parse Error:</h4>";
        echo "<p style='color: red;'>Response is not valid JSON. This is the issue!</p>";
        
        // Check if it's HTML error
        if (strpos($response, '<br') !== false || strpos($response, '<b>') !== false) {
            echo "<p style='color: red;'>Response contains HTML error messages. PHP errors are being displayed.</p>";
        }
    }
    echo "</div>";
    
} else {
    echo "<a href='?test_api=1' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Test API Call</a>";
}

echo "<h3>3. Check Database:</h3>";

try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    // Check customer points
    $pointsQuery = "SELECT * FROM customer_points WHERE customer_id = 1";
    $pointsResult = $mysqli->query($pointsQuery);
    
    if ($pointsResult && $pointsResult->num_rows > 0) {
        $customer = $pointsResult->fetch_assoc();
        echo "<p>✅ <strong>Customer Points:</strong> {$customer['total_points']}</p>";
        echo "<p>✅ <strong>Lifetime Points:</strong> {$customer['lifetime_points']}</p>";
        echo "<p>✅ <strong>Tier:</strong> {$customer['tier_level']}</p>";
    } else {
        echo "<p style='color: red;'>❌ No customer points record found</p>";
    }
    
    // Check rewards catalog
    $catalogQuery = "SELECT * FROM rewards_catalog WHERE id = 1";
    $catalogResult = $mysqli->query($catalogQuery);
    
    if ($catalogResult && $catalogResult->num_rows > 0) {
        $reward = $catalogResult->fetch_assoc();
        echo "<p>✅ <strong>Reward Available:</strong> {$reward['reward_name']} ({$reward['points_required']} points)</p>";
    } else {
        echo "<p style='color: red;'>❌ No reward found with ID 1</p>";
    }
    
    // Check coupons table
    $couponsCheck = $mysqli->query("SHOW TABLES LIKE 'coupons'");
    if ($couponsCheck && $couponsCheck->num_rows > 0) {
        echo "<p>✅ <strong>Coupons table exists</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ Coupons table missing</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<h3>4. Test JavaScript Redemption:</h3>";
?>

<div style="background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;">
    <h4>Test Redemption Button:</h4>
    <button onclick="testRedemption()" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
        🎁 Test Redeem ₹50 Coupon (200 points)
    </button>
    <div id="redemptionResult" style="margin-top: 15px;"></div>
</div>

<script>
function testRedemption() {
    const resultDiv = document.getElementById('redemptionResult');
    resultDiv.innerHTML = '<p>🔄 Testing redemption...</p>';
    
    const testData = {
        reward_id: 1,
        points_required: 200
    };
    
    console.log('Sending redemption request:', testData);
    
    fetch('redeem_reward.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(testData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        return response.text(); // Get as text first to see raw response
    })
    .then(text => {
        console.log('Raw response:', text);
        resultDiv.innerHTML = '<h4>Raw Response:</h4><pre style="background: #f8f9fa; padding: 10px; border-radius: 5px;">' + text + '</pre>';
        
        // Try to parse as JSON
        try {
            const data = JSON.parse(text);
            console.log('Parsed JSON:', data);
            
            resultDiv.innerHTML += '<h4>Parsed JSON:</h4><pre style="background: #d4edda; padding: 10px; border-radius: 5px;">' + JSON.stringify(data, null, 2) + '</pre>';
            
            if (data.success) {
                resultDiv.innerHTML += '<p style="color: green;">✅ <strong>Success!</strong> ' + data.message + '</p>';
                if (data.coupon_code) {
                    resultDiv.innerHTML += '<p><strong>Coupon Code:</strong> ' + data.coupon_code + '</p>';
                }
            } else {
                resultDiv.innerHTML += '<p style="color: red;">❌ <strong>Failed:</strong> ' + data.message + '</p>';
            }
            
        } catch (e) {
            console.error('JSON parse error:', e);
            resultDiv.innerHTML += '<p style="color: red;">❌ <strong>JSON Parse Error:</strong> Response is not valid JSON</p>';
            resultDiv.innerHTML += '<p>This means PHP is outputting HTML errors instead of clean JSON.</p>';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        resultDiv.innerHTML = '<p style="color: red;">❌ <strong>Network Error:</strong> ' + error.message + '</p>';
    });
}
</script>

<h3>5. Quick Fixes:</h3>
<div style="background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;">
    <ul>
        <li><a href="fix_coupons_table.php">🔧 Fix Coupons Table</a></li>
        <li><a href="setup_rewards_catalog.php">🎁 Setup Rewards Catalog</a></li>
        <li><a href="verify_points_working.php">✅ Verify Points System</a></li>
        <li><a href="test_rewards_modal.php">🎯 Test Rewards Modal</a></li>
    </ul>
</div>

<p><a href="rewards.php">← Back to Rewards Page</a></p>

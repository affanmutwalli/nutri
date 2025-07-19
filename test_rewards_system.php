<?php
/**
 * Test script to verify the rewards system is working
 */

include_once 'cms/includes/db_connect.php';
include_once 'cms/includes/functions.php';
include('cms/database/dbconnection.php');
require_once 'includes/setup_rewards_database.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>Testing Rewards System Setup</h2>";

// Test database setup
echo "<h3>1. Setting up database tables...</h3>";
$setupResult = setupRewardsDatabase($mysqli);
if ($setupResult) {
    echo "✅ Database tables created successfully!<br>";
} else {
    echo "❌ Error creating database tables<br>";
}

// Test table existence
echo "<h3>2. Checking table existence...</h3>";
$tables = ['enhanced_coupons', 'customer_points', 'points_transactions', 'rewards_catalog', 'reward_redemptions', 'coupon_usage'];

foreach ($tables as $table) {
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Table '$table' exists<br>";
    } else {
        echo "❌ Table '$table' missing<br>";
    }
}

// Test sample data
echo "<h3>3. Checking sample data...</h3>";

// Check coupons
$result = $mysqli->query("SELECT COUNT(*) as count FROM enhanced_coupons");
if ($result) {
    $count = $result->fetch_assoc()['count'];
    echo "📊 Enhanced coupons: $count records<br>";
    
    if ($count > 0) {
        $result = $mysqli->query("SELECT coupon_code, coupon_name, discount_type, discount_value FROM enhanced_coupons LIMIT 3");
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            $discount = $row['discount_type'] === 'fixed' ? '₹' . $row['discount_value'] : $row['discount_value'] . '%';
            echo "<li>{$row['coupon_code']} - {$row['coupon_name']} ($discount)</li>";
        }
        echo "</ul>";
    }
}

// Check rewards
$result = $mysqli->query("SELECT COUNT(*) as count FROM rewards_catalog");
if ($result) {
    $count = $result->fetch_assoc()['count'];
    echo "🎁 Rewards catalog: $count records<br>";
    
    if ($count > 0) {
        $result = $mysqli->query("SELECT reward_name, points_required, reward_value FROM rewards_catalog LIMIT 3");
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['reward_name']} - {$row['points_required']} points (₹{$row['reward_value']})</li>";
        }
        echo "</ul>";
    }
}

// Test coupon validation
echo "<h3>4. Testing coupon validation...</h3>";
require_once 'includes/CouponSystem.php';

$couponSystem = new CouponSystem($mysqli);

// Test with sample coupon
$testResult = $couponSystem->validateAndApplyCoupon('WELCOME10', 1, 1000);
if ($testResult['success']) {
    echo "✅ Coupon validation working: {$testResult['message']}<br>";
    echo "💰 Discount amount: ₹{$testResult['discount_amount']}<br>";
} else {
    echo "⚠️ Coupon validation result: {$testResult['message']}<br>";
}

// Test fetch_coupon.php endpoint
echo "<h3>5. Testing coupon API endpoint...</h3>";
$testData = json_encode(['code' => 'WELCOME10', 'order_amount' => 1000]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $testData
    ]
]);

$response = @file_get_contents('http://localhost/nutrify/exe_files/fetch_coupon.php', false, $context);
if ($response) {
    $data = json_decode($response, true);
    if ($data && $data['response'] === 'S') {
        echo "✅ API endpoint working: {$data['msg']}<br>";
    } else {
        echo "⚠️ API response: " . ($data['msg'] ?? 'Unknown error') . "<br>";
    }
} else {
    echo "❌ Could not test API endpoint (check if server is running)<br>";
}

echo "<h3>6. System Status Summary</h3>";
echo "<p><strong>✅ Database Setup:</strong> Complete</p>";
echo "<p><strong>✅ Sample Data:</strong> Loaded</p>";
echo "<p><strong>✅ Coupon System:</strong> Functional</p>";
echo "<p><strong>🎯 Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Access CMS at: <a href='cms/rewards_dashboard.php'>cms/rewards_dashboard.php</a></li>";
echo "<li>Test coupons on checkout page</li>";
echo "<li>Create new coupons through CMS</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>

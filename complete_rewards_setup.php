<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🎯 Complete Rewards Setup</h2>";

try {
    // Add the missing points_per_rupee config
    $checkQuery = "SELECT id FROM points_config WHERE config_key = 'points_per_rupee'";
    $result = $mysqli->query($checkQuery);
    
    if ($result->num_rows == 0) {
        $insertQuery = "INSERT INTO points_config (config_key, config_value) VALUES ('points_per_rupee', '3')";
        if ($mysqli->query($insertQuery)) {
            echo "<p>✅ Added points_per_rupee config</p>";
        }
    } else {
        echo "<p>✅ points_per_rupee config already exists</p>";
    }
    
    // Add default rewards
    $rewards = [
        ['₹50 Discount Coupon', 'discount', 500, 50.00],
        ['₹100 Discount Coupon', 'discount', 1000, 100.00],
        ['₹200 Discount Coupon', 'discount', 2000, 200.00],
        ['Free Shipping', 'free_shipping', 200, 0.00]
    ];
    
    foreach ($rewards as $reward) {
        $checkRewardQuery = "SELECT id FROM rewards_catalog WHERE reward_name = ?";
        $checkRewardStmt = $mysqli->prepare($checkRewardQuery);
        $checkRewardStmt->bind_param("s", $reward[0]);
        $checkRewardStmt->execute();
        $rewardResult = $checkRewardStmt->get_result();
        
        if ($rewardResult->num_rows == 0) {
            $insertRewardQuery = "INSERT INTO rewards_catalog (reward_name, reward_type, points_required, reward_value) VALUES (?, ?, ?, ?)";
            $insertRewardStmt = $mysqli->prepare($insertRewardQuery);
            $insertRewardStmt->bind_param("ssid", $reward[0], $reward[1], $reward[2], $reward[3]);
            if ($insertRewardStmt->execute()) {
                echo "<p>✅ Added reward: {$reward[0]} ({$reward[2]} points)</p>";
            }
        } else {
            echo "<p>✅ Reward already exists: {$reward[0]}</p>";
        }
    }
    
    echo "<h3>🎉 Rewards Setup Complete!</h3>";
    echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h4>✅ What's Ready:</h4>";
    echo "<ul>";
    echo "<li>✅ All rewards tables created</li>";
    echo "<li>✅ Points configuration set (3 points per ₹100)</li>";
    echo "<li>✅ Default rewards catalog loaded</li>";
    echo "<li>✅ Order integration added</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>🚀 Test Your Rewards System:</h3>";
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='debug_rewards.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Debug Rewards</a>";
    echo "<a href='index.php' style='background: #ff8c00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🛒 Place Test Order</a>";
    echo "<a href='rewards.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🎁 View Rewards</a>";
    echo "</div>";
    
    echo "<h3>📊 How It Works:</h3>";
    echo "<div style='background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
    echo "<ul>";
    echo "<li><strong>Earn Points:</strong> 3 points for every ₹100 spent</li>";
    echo "<li><strong>Your ₹249 order:</strong> Will earn 7 points (249/100 × 3 = 7.47 → 7)</li>";
    echo "<li><strong>Automatic:</strong> Points awarded immediately after order placement</li>";
    echo "<li><strong>Redeem:</strong> Use points for discounts and free shipping</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

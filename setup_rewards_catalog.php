<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🎁 Setup Rewards Catalog</h2>";

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

try {
    // Create rewards_catalog table
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `rewards_catalog` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `reward_name` varchar(255) NOT NULL,
        `reward_type` enum('discount','free_shipping','bonus_points','special') NOT NULL,
        `reward_value` decimal(10,2) NOT NULL,
        `points_required` int(11) NOT NULL,
        `description` text,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )";
    
    if ($mysqli->query($createTableQuery)) {
        echo "<p style='color: green;'>✅ Rewards catalog table created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating table: " . $mysqli->error . "</p>";
    }
    
    // Check if rewards already exist
    $checkQuery = "SELECT COUNT(*) as count FROM rewards_catalog";
    $result = $mysqli->query($checkQuery);
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        echo "<p style='color: orange;'>⚠️ Rewards catalog already has $count items</p>";
        
        // Show existing rewards
        $existingQuery = "SELECT * FROM rewards_catalog ORDER BY points_required ASC";
        $existingResult = $mysqli->query($existingQuery);
        
        echo "<h3>Existing Rewards:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Name</th><th>Type</th><th>Value</th><th>Points Required</th><th>Status</th></tr>";
        
        while ($reward = $existingResult->fetch_assoc()) {
            $status = $reward['is_active'] ? 'Active' : 'Inactive';
            echo "<tr>";
            echo "<td>{$reward['reward_name']}</td>";
            echo "<td>{$reward['reward_type']}</td>";
            echo "<td>₹{$reward['reward_value']}</td>";
            echo "<td>{$reward['points_required']}</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        // Insert default rewards
        $defaultRewards = [
            [
                'name' => '₹50 Discount Coupon',
                'type' => 'discount',
                'value' => 50.00,
                'points' => 200,
                'description' => 'Get ₹50 off on your next order (minimum order ₹500)'
            ],
            [
                'name' => '₹100 Discount Coupon',
                'type' => 'discount',
                'value' => 100.00,
                'points' => 400,
                'description' => 'Get ₹100 off on your next order (minimum order ₹1000)'
            ],
            [
                'name' => '₹200 Discount Coupon',
                'type' => 'discount',
                'value' => 200.00,
                'points' => 800,
                'description' => 'Get ₹200 off on your next order (minimum order ₹2000)'
            ],
            [
                'name' => 'Free Shipping',
                'type' => 'free_shipping',
                'value' => 0.00,
                'points' => 150,
                'description' => 'Free shipping on any order'
            ],
            [
                'name' => '₹500 Discount Coupon',
                'type' => 'discount',
                'value' => 500.00,
                'points' => 2000,
                'description' => 'Get ₹500 off on your next order (minimum order ₹5000)'
            ],
            [
                'name' => 'Bonus 100 Points',
                'type' => 'bonus_points',
                'value' => 100.00,
                'points' => 300,
                'description' => 'Get 100 bonus points added to your account'
            ]
        ];
        
        $insertQuery = "INSERT INTO rewards_catalog (reward_name, reward_type, reward_value, points_required, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);
        
        foreach ($defaultRewards as $reward) {
            $stmt->bind_param("ssdis", 
                $reward['name'], 
                $reward['type'], 
                $reward['value'], 
                $reward['points'], 
                $reward['description']
            );
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Added: {$reward['name']} ({$reward['points']} points)</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to add: {$reward['name']} - " . $stmt->error . "</p>";
            }
        }
        
        echo "<p style='color: green;'>🎉 Default rewards catalog created successfully!</p>";
    }
    
    // Create coupons table for redemptions
    $createCouponsQuery = "CREATE TABLE IF NOT EXISTS `coupons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `coupon_code` varchar(50) NOT NULL,
        `customer_id` int(11) NOT NULL,
        `discount_type` enum('fixed','percentage','free_shipping') NOT NULL,
        `discount_value` decimal(10,2) NOT NULL,
        `min_order_amount` decimal(10,2) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        `is_used` tinyint(1) DEFAULT 0,
        `used_at` timestamp NULL DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `expires_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `coupon_code` (`coupon_code`),
        KEY `customer_id` (`customer_id`)
    )";
    
    if ($mysqli->query($createCouponsQuery)) {
        echo "<p style='color: green;'>✅ Coupons table created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating coupons table: " . $mysqli->error . "</p>";
    }
    
    echo "<h3>🎯 Rewards System Summary:</h3>";
    echo "<div style='background-color: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<ul>";
    echo "<li>✅ <strong>Points System:</strong> 3 points per ₹100 spent</li>";
    echo "<li>✅ <strong>Rewards Catalog:</strong> 6 different rewards available</li>";
    echo "<li>✅ <strong>Discount Coupons:</strong> ₹50, ₹100, ₹200, ₹500</li>";
    echo "<li>✅ <strong>Free Shipping:</strong> 150 points</li>";
    echo "<li>✅ <strong>Bonus Points:</strong> 100 points for 300 points</li>";
    echo "<li>✅ <strong>Tier System:</strong> Bronze, Silver, Gold, Platinum</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>💰 Point Values:</h3>";
    echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
    echo "<p><strong>Your ₹549 order = 16 points</strong></p>";
    echo "<p><strong>₹200 order = 6 points</strong></p>";
    echo "<p><strong>₹300 order = 9 points</strong></p>";
    echo "<p><strong>Total possible = 31 points</strong></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><p><a href='test_rewards_modal.php' style='background: #ff8c00; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>🎁 Test Rewards Modal</a></p>";
echo "<p><a href='rewards.php'>Go to Rewards Page</a> | <a href='get_user_rewards.php'>Test API</a></p>";
?>

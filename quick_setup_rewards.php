<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🚀 Quick Rewards Setup</h2>";

if (isset($_GET['action']) && $_GET['action'] == 'setup') {
    echo "<h3>Setting up rewards system...</h3>";
    
    try {
        // Check if tables exist
        $tables = [
            'customer_points' => "CREATE TABLE IF NOT EXISTS `customer_points` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `customer_id` int(11) NOT NULL,
                `total_points` int(11) DEFAULT 0,
                `lifetime_points` int(11) DEFAULT 0,
                `tier_level` enum('Bronze','Silver','Gold','Platinum') DEFAULT 'Bronze',
                `referral_code` varchar(20) DEFAULT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `customer_id` (`customer_id`),
                UNIQUE KEY `referral_code` (`referral_code`)
            )",
            
            'points_transactions' => "CREATE TABLE IF NOT EXISTS `points_transactions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `customer_id` int(11) NOT NULL,
                `transaction_type` enum('earned','redeemed','expired','bonus') NOT NULL,
                `points` int(11) NOT NULL,
                `description` text,
                `order_id` varchar(50) DEFAULT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `customer_id` (`customer_id`),
                KEY `order_id` (`order_id`)
            )",
            
            'points_config' => "CREATE TABLE IF NOT EXISTS `points_config` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `config_key` varchar(100) NOT NULL,
                `config_value` varchar(255) NOT NULL,
                `description` text,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `config_key` (`config_key`)
            )",
            
            'rewards_catalog' => "CREATE TABLE IF NOT EXISTS `rewards_catalog` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `reward_name` varchar(255) NOT NULL,
                `reward_type` enum('discount','cashback','free_shipping','product') NOT NULL,
                `points_required` int(11) NOT NULL,
                `reward_value` decimal(10,2) NOT NULL,
                `description` text,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            )"
        ];
        
        foreach ($tables as $tableName => $sql) {
            if ($mysqli->query($sql)) {
                echo "<p>✅ Created/verified table: $tableName</p>";
            } else {
                echo "<p>❌ Error creating $tableName: " . $mysqli->error . "</p>";
            }
        }
        
        // Insert default configuration
        $configs = [
            ['points_per_rupee', '3'],
            ['signup_bonus', '25'],
            ['review_bonus', '25'],
            ['referral_bonus_referrer', '100'],
            ['referral_bonus_referred', '50'],
            ['bronze_threshold', '0'],
            ['silver_threshold', '500'],
            ['gold_threshold', '1500'],
            ['platinum_threshold', '3000']
        ];

        foreach ($configs as $config) {
            $checkQuery = "SELECT id FROM points_config WHERE config_key = ?";
            $checkStmt = $mysqli->prepare($checkQuery);
            $checkStmt->bind_param("s", $config[0]);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows == 0) {
                $insertQuery = "INSERT INTO points_config (config_key, config_value) VALUES (?, ?)";
                $insertStmt = $mysqli->prepare($insertQuery);
                $insertStmt->bind_param("ss", $config[0], $config[1]);
                if ($insertStmt->execute()) {
                    echo "<p>✅ Added config: {$config[0]} = {$config[1]}</p>";
                }
            }
        }
        
        // Insert default rewards
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
            }
        }
        
        echo "<h3>🎉 Rewards System Setup Complete!</h3>";
        echo "<p><a href='debug_rewards.php' style='background: #ff8c00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Rewards System</a></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Setup failed: " . $e->getMessage() . "</p>";
    }
    
} else {
    // Show setup option
    echo "<h3>Setup Rewards System</h3>";
    echo "<p>This will create the necessary database tables and default configuration for the rewards system.</p>";
    
    echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>What will be created:</h4>";
    echo "<ul>";
    echo "<li>✅ customer_points table</li>";
    echo "<li>✅ points_transactions table</li>";
    echo "<li>✅ points_config table</li>";
    echo "<li>✅ rewards_catalog table</li>";
    echo "<li>✅ Default configuration (3 points per ₹100)</li>";
    echo "<li>✅ Default rewards (₹50, ₹100, ₹200 coupons)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<a href='?action=setup' style='background: #ff8c00; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;' onclick='return confirm(\"Set up rewards system now?\")'>🚀 Setup Now</a>";
}

echo "<br><br><p><a href='index.php'>Homepage</a> | <a href='debug_rewards.php'>Debug Rewards</a></p>";
?>

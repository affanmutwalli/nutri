<?php
/**
 * Basic Rewards System Setup
 * Creates only the essential tables needed for basic rewards functionality
 */

include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>Setting up Basic Rewards System...</h2>";

$tablesCreated = 0;
$totalTables = 7;

try {
    // Create customer_points table
    $createCustomerPoints = "
    CREATE TABLE IF NOT EXISTS `customer_points` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `total_points` int(11) DEFAULT 0,
        `lifetime_points` int(11) DEFAULT 0,
        `points_redeemed` int(11) DEFAULT 0,
        `tier_level` enum('Bronze','Silver','Gold','Platinum') DEFAULT 'Bronze',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `customer_id` (`customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($mysqli->query($createCustomerPoints)) {
        echo "<p style='color: green;'>✅ Created customer_points table</p>";
        $tablesCreated++;
    } else {
        echo "<p style='color: orange;'>⚠️ Customer_points table may already exist</p>";
    }

    // Create points_transactions table
    $createPointsTransactions = "
    CREATE TABLE IF NOT EXISTS `points_transactions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `transaction_type` enum('earned','redeemed','expired','bonus','referral') NOT NULL,
        `points_amount` int(11) NOT NULL,
        `description` varchar(255) NOT NULL,
        `reference_type` enum('order','review','referral','signup','birthday','manual') NULL,
        `reference_id` varchar(50) NULL,
        `order_id` varchar(50) NULL,
        `expiry_date` date NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_customer_id` (`customer_id`),
        KEY `idx_transaction_type` (`transaction_type`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($mysqli->query($createPointsTransactions)) {
        echo "<p style='color: green;'>✅ Created points_transactions table</p>";
        $tablesCreated++;
    } else {
        echo "<p style='color: orange;'>⚠️ Points_transactions table may already exist</p>";
    }

    // Create rewards_catalog table
    $createRewardsCatalog = "
    CREATE TABLE IF NOT EXISTS `rewards_catalog` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `reward_name` varchar(100) NOT NULL,
        `reward_description` text NULL,
        `points_required` int(11) NOT NULL,
        `reward_type` enum('discount','coupon','product','cashback') DEFAULT 'discount',
        `reward_value` decimal(10,2) NOT NULL,
        `minimum_order_amount` decimal(10,2) DEFAULT 0,
        `max_redemptions_per_customer` int(11) DEFAULT 1,
        `total_redemptions_limit` int(11) NULL,
        `current_redemptions` int(11) DEFAULT 0,
        `is_active` boolean DEFAULT TRUE,
        `valid_from` date NULL,
        `valid_until` date NULL,
        `terms_conditions` text NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_points_required` (`points_required`),
        KEY `idx_is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($mysqli->query($createRewardsCatalog)) {
        echo "<p style='color: green;'>✅ Created rewards_catalog table</p>";
        $tablesCreated++;
    } else {
        echo "<p style='color: orange;'>⚠️ Rewards_catalog table may already exist</p>";
    }

    // Create reward_redemptions table
    $createRewardRedemptions = "
    CREATE TABLE IF NOT EXISTS `reward_redemptions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `reward_id` int(11) NOT NULL,
        `points_used` int(11) NOT NULL,
        `coupon_code` varchar(50) NULL,
        `order_id` varchar(50) NULL,
        `redemption_status` enum('pending','active','used','expired','cancelled') DEFAULT 'active',
        `redeemed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `used_at` timestamp NULL,
        `expires_at` timestamp NULL,
        PRIMARY KEY (`id`),
        KEY `idx_customer_id` (`customer_id`),
        KEY `idx_reward_id` (`reward_id`),
        KEY `idx_coupon_code` (`coupon_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($mysqli->query($createRewardRedemptions)) {
        echo "<p style='color: green;'>✅ Created reward_redemptions table</p>";
        $tablesCreated++;
    } else {
        echo "<p style='color: orange;'>⚠️ Reward_redemptions table may already exist</p>";
    }

    // Create points_config table
    $createPointsConfig = "
    CREATE TABLE IF NOT EXISTS `points_config` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `config_key` varchar(50) NOT NULL,
        `config_value` varchar(255) NOT NULL,
        `description` text NULL,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($mysqli->query($createPointsConfig)) {
        echo "<p style='color: green;'>✅ Created points_config table</p>";
        $tablesCreated++;
    } else {
        echo "<p style='color: orange;'>⚠️ Points_config table may already exist</p>";
    }

    // Add unique constraint if it doesn't exist
    $addUniqueConstraint = "ALTER TABLE `points_config` ADD UNIQUE KEY `config_key` (`config_key`)";
    $mysqli->query($addUniqueConstraint); // Ignore errors if constraint already exists

    // Insert default configuration
    $defaultConfigs = [
        ['points_per_rupee', '3', 'Points earned per ₹100 spent'],
        ['signup_bonus_points', '25', 'Points awarded on customer signup'],
        ['review_points', '25', 'Points awarded for writing a product review'],
        ['referral_points_referrer', '100', 'Points awarded to referrer when referred customer makes first purchase'],
        ['referral_points_referred', '50', 'Points awarded to referred customer on signup'],
        ['points_expiry_months', '12', 'Number of months after which points expire'],
        ['silver_tier_threshold', '500', 'Points required for Silver tier'],
        ['gold_tier_threshold', '1500', 'Points required for Gold tier'],
        ['platinum_tier_threshold', '5000', 'Points required for Platinum tier']
    ];

    foreach ($defaultConfigs as $config) {
        $insertConfig = "INSERT INTO points_config (config_key, config_value, description) 
                         VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE 
                         config_value = VALUES(config_value), description = VALUES(description)";
        $stmt = $mysqli->prepare($insertConfig);
        $stmt->bind_param("sss", $config[0], $config[1], $config[2]);
        $stmt->execute();
    }
    echo "<p style='color: green;'>✅ Inserted default configuration</p>";

    // Insert default rewards
    $defaultRewards = [
        ['₹50 Off Coupon', 'Get ₹50 discount on your next order', 500, 'discount', 50.00, 500.00, 'Valid for 30 days. Minimum order value ₹500.'],
        ['₹100 Off Coupon', 'Get ₹100 discount on your next order', 1000, 'discount', 100.00, 1000.00, 'Valid for 30 days. Minimum order value ₹1000.'],
        ['₹200 Off Coupon', 'Get ₹200 discount on your next order', 2000, 'discount', 200.00, 1500.00, 'Valid for 30 days. Minimum order value ₹1500.'],
        ['Free Shipping', 'Free shipping on your next order', 300, 'discount', 50.00, 0.00, 'Valid for 30 days. Applicable on all orders.']
    ];

    foreach ($defaultRewards as $reward) {
        $insertReward = "INSERT INTO rewards_catalog (reward_name, reward_description, points_required, reward_type, reward_value, minimum_order_amount, terms_conditions) 
                         VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
                         reward_description = VALUES(reward_description), points_required = VALUES(points_required)";
        $stmt = $mysqli->prepare($insertReward);
        $stmt->bind_param("ssissds", $reward[0], $reward[1], $reward[2], $reward[3], $reward[4], $reward[5], $reward[6]);
        $stmt->execute();
    }
    echo "<p style='color: green;'>✅ Inserted default rewards</p>";

    // Create enhanced_coupons table for coupon system
    $createEnhancedCoupons = "
    CREATE TABLE IF NOT EXISTS `enhanced_coupons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `coupon_code` varchar(50) NOT NULL UNIQUE,
        `coupon_name` varchar(255) NOT NULL,
        `description` text,
        `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
        `discount_value` decimal(10,2) NOT NULL,
        `minimum_order_amount` decimal(10,2) DEFAULT 0,
        `usage_limit_total` int(11) DEFAULT NULL,
        `usage_limit_per_customer` int(11) DEFAULT 1,
        `current_usage_count` int(11) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        `is_reward_coupon` tinyint(1) DEFAULT 0,
        `valid_from` datetime NOT NULL,
        `valid_until` datetime NOT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_coupon_code` (`coupon_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($mysqli->query($createEnhancedCoupons)) {
        echo "<p style='color: green;'>✅ Created enhanced_coupons table</p>";
        $tablesCreated++;
    } else {
        echo "<p style='color: orange;'>⚠️ Enhanced_coupons table may already exist</p>";
    }

    // Create customer_coupons table
    $createCustomerCoupons = "
    CREATE TABLE IF NOT EXISTS `customer_coupons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `coupon_id` int(11) NOT NULL,
        `redeemed_from_points` tinyint(1) DEFAULT 0,
        `points_used` int(11) DEFAULT 0,
        `is_used` tinyint(1) DEFAULT 0,
        `used_at` timestamp NULL,
        `expires_at` timestamp NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_customer_id` (`customer_id`),
        KEY `idx_coupon_id` (`coupon_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($mysqli->query($createCustomerCoupons)) {
        echo "<p style='color: green;'>✅ Created customer_coupons table</p>";
        $tablesCreated++;
    } else {
        echo "<p style='color: orange;'>⚠️ Customer_coupons table may already exist</p>";
    }

    echo "<h3 style='color: green;'>🎉 Basic Rewards System Setup Complete!</h3>";
    echo "<p>The essential tables have been created and configured. You can now:</p>";
    echo "<ul>";
    echo "<li>✅ Test the rewards system on the homepage</li>";
    echo "<li>✅ Login and see your points balance</li>";
    echo "<li>✅ Redeem points for rewards</li>";
    echo "<li>✅ View transaction history</li>";
    echo "</ul>";
    echo "<p><a href='index.php' style='color: #ff8c00;'>Go to Homepage</a> | <a href='test_rewards.php' style='color: #ff8c00;'>Test Rewards System</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error setting up rewards system: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>

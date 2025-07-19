<?php
/**
 * Database setup for Rewards and Coupons System
 * This file creates all necessary tables if they don't exist
 */

function setupRewardsDatabase($mysqli) {
    try {
        // Create enhanced_coupons table
        $createEnhancedCoupons = "
        CREATE TABLE IF NOT EXISTS `enhanced_coupons` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `coupon_code` varchar(50) NOT NULL UNIQUE,
            `coupon_name` varchar(255) NOT NULL,
            `description` text,
            `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
            `discount_value` decimal(10,2) NOT NULL,
            `max_discount_amount` decimal(10,2) DEFAULT NULL,
            `minimum_order_amount` decimal(10,2) DEFAULT 0,
            `usage_limit_total` int(11) DEFAULT NULL,
            `usage_limit_per_customer` int(11) DEFAULT 1,
            `current_usage_count` int(11) DEFAULT 0,
            `customer_type` enum('all','new','existing') DEFAULT 'all',
            `is_active` tinyint(1) DEFAULT 1,
            `is_reward_coupon` tinyint(1) DEFAULT 0,
            `valid_from` datetime NOT NULL,
            `valid_until` datetime NOT NULL,
            `created_by` varchar(100) DEFAULT 'admin',
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_coupon_code` (`coupon_code`),
            KEY `idx_active_valid` (`is_active`, `valid_from`, `valid_until`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $mysqli->query($createEnhancedCoupons);
        
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
            UNIQUE KEY `customer_id` (`customer_id`),
            KEY `idx_tier` (`tier_level`),
            KEY `idx_points` (`total_points`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $mysqli->query($createCustomerPoints);
        
        // Create points_transactions table
        $createPointsTransactions = "
        CREATE TABLE IF NOT EXISTS `points_transactions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `customer_id` int(11) NOT NULL,
            `transaction_type` enum('earned','redeemed','expired','admin_adjustment','bulk_admin_adjustment') NOT NULL,
            `points_amount` int(11) NOT NULL,
            `description` text,
            `reference_type` varchar(50) DEFAULT NULL,
            `reference_id` varchar(100) DEFAULT NULL,
            `order_id` varchar(50) DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_customer` (`customer_id`),
            KEY `idx_type` (`transaction_type`),
            KEY `idx_reference` (`reference_type`, `reference_id`),
            KEY `idx_order` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $mysqli->query($createPointsTransactions);
        
        // Create rewards_catalog table
        $createRewardsCatalog = "
        CREATE TABLE IF NOT EXISTS `rewards_catalog` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `reward_name` varchar(255) NOT NULL,
            `reward_description` text,
            `points_required` int(11) NOT NULL,
            `reward_type` enum('coupon','discount','freebie') NOT NULL DEFAULT 'coupon',
            `reward_value` decimal(10,2) NOT NULL,
            `minimum_order_amount` decimal(10,2) DEFAULT 0,
            `max_redemptions_per_customer` int(11) DEFAULT 1,
            `total_redemptions_limit` int(11) DEFAULT NULL,
            `current_redemptions` int(11) DEFAULT 0,
            `is_active` tinyint(1) DEFAULT 1,
            `valid_from` date DEFAULT NULL,
            `valid_until` date DEFAULT NULL,
            `terms_conditions` text,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_points` (`points_required`),
            KEY `idx_active` (`is_active`),
            KEY `idx_type` (`reward_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $mysqli->query($createRewardsCatalog);
        
        // Create reward_redemptions table
        $createRewardRedemptions = "
        CREATE TABLE IF NOT EXISTS `reward_redemptions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `customer_id` int(11) NOT NULL,
            `reward_id` int(11) NOT NULL,
            `points_used` int(11) NOT NULL,
            `coupon_code` varchar(50) DEFAULT NULL,
            `status` enum('pending','completed','cancelled') DEFAULT 'completed',
            `redeemed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `used_at` timestamp NULL DEFAULT NULL,
            `order_id` varchar(50) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_customer` (`customer_id`),
            KEY `idx_reward` (`reward_id`),
            KEY `idx_coupon` (`coupon_code`),
            KEY `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $mysqli->query($createRewardRedemptions);
        
        // Create coupon_usage table
        $createCouponUsage = "
        CREATE TABLE IF NOT EXISTS `coupon_usage` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `coupon_id` int(11) DEFAULT NULL,
            `coupon_code` varchar(50) NOT NULL,
            `customer_id` int(11) DEFAULT NULL,
            `order_id` varchar(50) NOT NULL,
            `discount_amount` decimal(10,2) NOT NULL,
            `order_amount` decimal(10,2) NOT NULL,
            `used_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_coupon_code` (`coupon_code`),
            KEY `idx_customer` (`customer_id`),
            KEY `idx_order` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $mysqli->query($createCouponUsage);
        
        // Insert some sample data if tables are empty
        insertSampleData($mysqli);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error setting up rewards database: " . $e->getMessage());
        return false;
    }
}

function insertSampleData($mysqli) {
    try {
        // Check if enhanced_coupons has any data
        $result = $mysqli->query("SELECT COUNT(*) as count FROM enhanced_coupons");
        $count = $result->fetch_assoc()['count'];
        
        if ($count == 0) {
            // Insert sample coupons
            $sampleCoupons = [
                [
                    'WELCOME10', 'Welcome 10% Off', 'Get 10% off on your first order', 'percentage', 10.00, 100.00, 500.00, NULL, 1, 'new'
                ],
                [
                    'SAVE50', 'Save ₹50', 'Flat ₹50 off on orders above ₹1000', 'fixed', 50.00, NULL, 1000.00, 100, 1, 'all'
                ],
                [
                    'FLAT100', 'Flat ₹100 Off', 'Get ₹100 off on orders above ₹2000', 'fixed', 100.00, NULL, 2000.00, 50, 1, 'all'
                ]
            ];
            
            $stmt = $mysqli->prepare("
                INSERT INTO enhanced_coupons 
                (coupon_code, coupon_name, description, discount_type, discount_value, max_discount_amount, minimum_order_amount, usage_limit_total, usage_limit_per_customer, customer_type, valid_from, valid_until) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))
            ");
            
            foreach ($sampleCoupons as $coupon) {
                $stmt->bind_param("ssssdddiis", ...$coupon);
                $stmt->execute();
            }
        }
        
        // Check if rewards_catalog has any data
        $result = $mysqli->query("SELECT COUNT(*) as count FROM rewards_catalog");
        $count = $result->fetch_assoc()['count'];
        
        if ($count == 0) {
            // Insert sample rewards
            $sampleRewards = [
                ['₹25 Discount Coupon', 'Get ₹25 off on your next order', 250, 'coupon', 25.00, 500.00, 1, NULL],
                ['₹50 Discount Coupon', 'Get ₹50 off on your next order', 500, 'coupon', 50.00, 1000.00, 1, NULL],
                ['₹100 Discount Coupon', 'Get ₹100 off on your next order', 1000, 'coupon', 100.00, 2000.00, 1, NULL],
                ['Free Shipping', 'Free shipping on your next order', 150, 'discount', 50.00, 0.00, 2, NULL]
            ];
            
            $stmt = $mysqli->prepare("
                INSERT INTO rewards_catalog 
                (reward_name, reward_description, points_required, reward_type, reward_value, minimum_order_amount, max_redemptions_per_customer, total_redemptions_limit) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($sampleRewards as $reward) {
                $stmt->bind_param("ssissdii", ...$reward);
                $stmt->execute();
            }
        }
        
    } catch (Exception $e) {
        error_log("Error inserting sample data: " . $e->getMessage());
    }
}

// Auto-setup function that can be called from any CMS page
function autoSetupRewardsSystem($mysqli) {
    static $setupDone = false;
    
    if (!$setupDone) {
        setupRewardsDatabase($mysqli);
        $setupDone = true;
    }
}
?>

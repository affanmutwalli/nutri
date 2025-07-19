-- My Nutrify Rewards System Database Schema
-- Creates tables for dynamic points and rewards functionality

-- =====================================================
-- 1. Customer Points Table
-- =====================================================
CREATE TABLE IF NOT EXISTS customer_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_points INT DEFAULT 0,
    lifetime_points INT DEFAULT 0 COMMENT 'Total points earned ever',
    points_redeemed INT DEFAULT 0 COMMENT 'Total points redeemed',
    tier_level ENUM('Bronze', 'Silver', 'Gold', 'Platinum') DEFAULT 'Bronze',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_customer (customer_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_total_points (total_points),
    INDEX idx_tier_level (tier_level),
    
    FOREIGN KEY (customer_id) REFERENCES customer_master(CustomerId) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Store customer reward points and tier information';

-- =====================================================
-- 2. Points Transactions Table
-- =====================================================
CREATE TABLE IF NOT EXISTS points_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    transaction_type ENUM('earned', 'redeemed', 'expired', 'bonus', 'referral') NOT NULL,
    points_amount INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    reference_type ENUM('order', 'review', 'referral', 'signup', 'birthday', 'manual') NULL,
    reference_id VARCHAR(50) NULL COMMENT 'OrderId, ReviewId, etc.',
    order_id VARCHAR(50) NULL,
    expiry_date DATE NULL COMMENT 'When points expire (if applicable)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_order_id (order_id),
    INDEX idx_created_at (created_at),
    INDEX idx_expiry_date (expiry_date),
    
    FOREIGN KEY (customer_id) REFERENCES customer_master(CustomerId) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track all points earning and redemption transactions';

-- =====================================================
-- 3. Rewards Catalog Table
-- =====================================================
CREATE TABLE IF NOT EXISTS rewards_catalog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reward_name VARCHAR(100) NOT NULL,
    reward_description TEXT NULL,
    points_required INT NOT NULL,
    reward_type ENUM('discount', 'coupon', 'product', 'cashback') DEFAULT 'discount',
    reward_value DECIMAL(10,2) NOT NULL COMMENT 'Discount amount or product value',
    minimum_order_amount DECIMAL(10,2) DEFAULT 0,
    max_redemptions_per_customer INT DEFAULT 1,
    total_redemptions_limit INT NULL COMMENT 'Total available across all customers',
    current_redemptions INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    valid_from DATE NULL,
    valid_until DATE NULL,
    terms_conditions TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_points_required (points_required),
    INDEX idx_reward_type (reward_type),
    INDEX idx_is_active (is_active),
    INDEX idx_valid_dates (valid_from, valid_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Available rewards that customers can redeem with points';

-- =====================================================
-- 4. Reward Redemptions Table
-- =====================================================
CREATE TABLE IF NOT EXISTS reward_redemptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    reward_id INT NOT NULL,
    points_used INT NOT NULL,
    coupon_code VARCHAR(50) NULL,
    order_id VARCHAR(50) NULL COMMENT 'Order where reward was applied',
    redemption_status ENUM('pending', 'active', 'used', 'expired', 'cancelled') DEFAULT 'active',
    redeemed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_reward_id (reward_id),
    INDEX idx_coupon_code (coupon_code),
    INDEX idx_order_id (order_id),
    INDEX idx_status (redemption_status),
    INDEX idx_redeemed_at (redeemed_at),
    
    FOREIGN KEY (customer_id) REFERENCES customer_master(CustomerId) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES rewards_catalog(id) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track customer reward redemptions and usage';

-- =====================================================
-- 5. Referral System Table
-- =====================================================
CREATE TABLE IF NOT EXISTS customer_referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_customer_id INT NOT NULL COMMENT 'Customer who made the referral',
    referred_customer_id INT NULL COMMENT 'Customer who was referred (after signup)',
    referral_code VARCHAR(20) NOT NULL,
    referred_mobile VARCHAR(15) NULL COMMENT 'Mobile number of referred person',
    referred_email VARCHAR(100) NULL,
    referral_status ENUM('pending', 'completed', 'rewarded') DEFAULT 'pending',
    referrer_points_awarded INT DEFAULT 0,
    referred_points_awarded INT DEFAULT 0,
    first_order_id VARCHAR(50) NULL COMMENT 'First order by referred customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_referral_code (referral_code),
    INDEX idx_referrer_id (referrer_customer_id),
    INDEX idx_referred_id (referred_customer_id),
    INDEX idx_referral_code (referral_code),
    INDEX idx_referred_mobile (referred_mobile),
    INDEX idx_status (referral_status),
    
    FOREIGN KEY (referrer_customer_id) REFERENCES customer_master(CustomerId) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (referred_customer_id) REFERENCES customer_master(CustomerId) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track customer referrals and rewards';

-- =====================================================
-- 6. Points Configuration Table
-- =====================================================
CREATE TABLE IF NOT EXISTS points_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(50) NOT NULL,
    config_value VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_config_key (config_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Configuration settings for points system';

-- =====================================================
-- 7. Insert Default Configuration
-- =====================================================
INSERT INTO points_config (config_key, config_value, description) VALUES
('points_per_rupee', '3', 'Points earned per ₹100 spent'),
('signup_bonus_points', '25', 'Points awarded on customer signup'),
('review_points', '25', 'Points awarded for writing a product review'),
('referral_points_referrer', '100', 'Points awarded to referrer when referred customer makes first purchase'),
('referral_points_referred', '50', 'Points awarded to referred customer on signup'),
('points_expiry_months', '12', 'Number of months after which points expire'),
('min_points_redemption', '100', 'Minimum points required for any redemption'),
('bronze_tier_threshold', '0', 'Points required for Bronze tier'),
('silver_tier_threshold', '500', 'Points required for Silver tier'),
('gold_tier_threshold', '1500', 'Points required for Gold tier'),
('platinum_tier_threshold', '5000', 'Points required for Platinum tier')
ON DUPLICATE KEY UPDATE 
config_value = VALUES(config_value),
description = VALUES(description);

-- =====================================================
-- 8. Insert Default Rewards
-- =====================================================
INSERT INTO rewards_catalog (reward_name, reward_description, points_required, reward_type, reward_value, minimum_order_amount, terms_conditions) VALUES
('₹50 Off Coupon', 'Get ₹50 discount on your next order', 500, 'discount', 50.00, 500.00, 'Valid for 30 days. Minimum order value ₹500. Cannot be combined with other offers.'),
('₹100 Off Coupon', 'Get ₹100 discount on your next order', 1000, 'discount', 100.00, 1000.00, 'Valid for 30 days. Minimum order value ₹1000. Cannot be combined with other offers.'),
('₹200 Off Coupon', 'Get ₹200 discount on your next order', 2000, 'discount', 200.00, 1500.00, 'Valid for 30 days. Minimum order value ₹1500. Cannot be combined with other offers.'),
('Free Shipping', 'Free shipping on your next order', 300, 'discount', 50.00, 0.00, 'Valid for 30 days. Applicable on all orders.'),
('5% Cashback', 'Get 5% cashback on your order', 750, 'cashback', 5.00, 1000.00, 'Valid for 30 days. Maximum cashback ₹100. Minimum order value ₹1000.')
ON DUPLICATE KEY UPDATE 
reward_description = VALUES(reward_description),
points_required = VALUES(points_required),
reward_value = VALUES(reward_value);

SELECT 'Rewards system database schema created successfully!' as result;

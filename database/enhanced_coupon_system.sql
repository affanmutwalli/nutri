-- =====================================================
-- Enhanced Coupon System Database Schema
-- Integrates with existing rewards system
-- =====================================================

-- =====================================================
-- 1. Enhanced Coupons Table
-- =====================================================
CREATE TABLE IF NOT EXISTS enhanced_coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_code VARCHAR(50) NOT NULL UNIQUE,
    coupon_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    
    -- Discount Configuration
    discount_type ENUM('fixed', 'percentage') NOT NULL DEFAULT 'fixed',
    discount_value DECIMAL(10,2) NOT NULL,
    max_discount_amount DECIMAL(10,2) NULL COMMENT 'Max discount for percentage type',
    
    -- Order Requirements
    minimum_order_amount DECIMAL(10,2) DEFAULT 0,
    applicable_categories TEXT NULL COMMENT 'JSON array of category IDs, NULL for all',
    excluded_products TEXT NULL COMMENT 'JSON array of product IDs to exclude',
    
    -- Usage Limits
    usage_limit_total INT NULL COMMENT 'Total usage limit across all customers',
    usage_limit_per_customer INT DEFAULT 1,
    current_usage_count INT DEFAULT 0,
    
    -- Customer Restrictions
    customer_type ENUM('all', 'new', 'existing', 'specific') DEFAULT 'all',
    specific_customers TEXT NULL COMMENT 'JSON array of customer IDs if customer_type is specific',
    tier_restrictions TEXT NULL COMMENT 'JSON array of tier levels (Bronze,Silver,Gold,Platinum)',
    
    -- Validity
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    
    -- Integration with Rewards
    points_required INT NULL COMMENT 'Points needed to redeem this coupon, NULL if not from rewards',
    is_reward_coupon BOOLEAN DEFAULT FALSE,
    reward_catalog_id INT NULL COMMENT 'Link to rewards_catalog if generated from rewards',
    
    -- Status and Metadata
    is_active BOOLEAN DEFAULT TRUE,
    is_stackable BOOLEAN DEFAULT FALSE COMMENT 'Can be combined with other coupons',
    created_by VARCHAR(50) DEFAULT 'system',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_coupon_code (coupon_code),
    INDEX idx_valid_dates (valid_from, valid_until),
    INDEX idx_active (is_active),
    INDEX idx_reward_coupon (is_reward_coupon),
    INDEX idx_points_required (points_required),
    
    -- Foreign Key
    FOREIGN KEY (reward_catalog_id) REFERENCES rewards_catalog(id) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Enhanced coupon system with rewards integration';

-- =====================================================
-- 2. Coupon Usage Tracking Table
-- =====================================================
CREATE TABLE IF NOT EXISTS coupon_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT NOT NULL,
    customer_id INT NOT NULL,
    order_id VARCHAR(50) NOT NULL,
    
    -- Usage Details
    discount_applied DECIMAL(10,2) NOT NULL,
    order_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Metadata
    customer_ip VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    -- Indexes
    INDEX idx_coupon_id (coupon_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_id (order_id),
    INDEX idx_used_at (used_at),
    
    -- Foreign Keys
    FOREIGN KEY (coupon_id) REFERENCES enhanced_coupons(id) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customer_master(CustomerId) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Unique constraint to prevent duplicate usage
    UNIQUE KEY unique_coupon_customer_order (coupon_id, customer_id, order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track coupon usage by customers';

-- =====================================================
-- 3. Customer Coupon Wallet Table
-- =====================================================
CREATE TABLE IF NOT EXISTS customer_coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    coupon_id INT NOT NULL,
    
    -- Redemption Details
    redeemed_from_points BOOLEAN DEFAULT FALSE,
    points_used INT NULL,
    redemption_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Status
    status ENUM('active', 'used', 'expired', 'cancelled') DEFAULT 'active',
    used_at TIMESTAMP NULL,
    order_id VARCHAR(50) NULL COMMENT 'Order where coupon was used',
    
    -- Expiry (can override coupon expiry for individual redemptions)
    expires_at TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_customer_id (customer_id),
    INDEX idx_coupon_id (coupon_id),
    INDEX idx_status (status),
    INDEX idx_expires_at (expires_at),
    
    -- Foreign Keys
    FOREIGN KEY (customer_id) REFERENCES customer_master(CustomerId) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES enhanced_coupons(id) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Unique constraint for non-stackable coupons
    UNIQUE KEY unique_customer_coupon (customer_id, coupon_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Customer coupon wallet for redeemed coupons';

-- =====================================================
-- 4. Insert Default Enhanced Coupons
-- =====================================================
INSERT INTO enhanced_coupons (
    coupon_code, coupon_name, description, discount_type, discount_value, 
    minimum_order_amount, usage_limit_per_customer, valid_from, valid_until,
    is_active, created_by
) VALUES
('WELCOME50', 'Welcome Discount', 'Get ₹50 off on your first order', 'fixed', 50.00, 500.00, 1, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), TRUE, 'system'),
('SAVE100', 'Save ₹100', 'Get ₹100 off on orders above ₹1000', 'fixed', 100.00, 1000.00, 3, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH), TRUE, 'system'),
('PERCENT10', '10% Off', 'Get 10% discount up to ₹200', 'percentage', 10.00, 800.00, 2, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), TRUE, 'system'),
('FREESHIP', 'Free Shipping', 'Free shipping on all orders', 'fixed', 50.00, 0.00, 5, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), TRUE, 'system')
ON DUPLICATE KEY UPDATE 
    description = VALUES(description),
    discount_value = VALUES(discount_value),
    minimum_order_amount = VALUES(minimum_order_amount);

-- =====================================================
-- 5. Create Views for Easy Access
-- =====================================================
CREATE OR REPLACE VIEW active_coupons AS
SELECT 
    id, coupon_code, coupon_name, description,
    discount_type, discount_value, max_discount_amount,
    minimum_order_amount, usage_limit_total, usage_limit_per_customer,
    current_usage_count, valid_from, valid_until,
    points_required, is_reward_coupon
FROM enhanced_coupons 
WHERE is_active = TRUE 
  AND valid_from <= NOW() 
  AND valid_until >= NOW();

CREATE OR REPLACE VIEW customer_available_coupons AS
SELECT 
    c.id, c.coupon_code, c.coupon_name, c.description,
    c.discount_type, c.discount_value, c.max_discount_amount,
    c.minimum_order_amount, c.points_required, c.is_reward_coupon,
    cc.customer_id, cc.status as wallet_status, cc.expires_at
FROM enhanced_coupons c
LEFT JOIN customer_coupons cc ON c.id = cc.coupon_id
WHERE c.is_active = TRUE 
  AND c.valid_from <= NOW() 
  AND c.valid_until >= NOW();

SELECT 'Enhanced coupon system database schema created successfully!' as result;

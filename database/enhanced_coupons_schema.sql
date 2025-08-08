-- =====================================================
-- Enhanced Coupons Table with Shining Feature
-- =====================================================

-- Create enhanced_coupons table if it doesn't exist
CREATE TABLE IF NOT EXISTS enhanced_coupons (
    CouponId INT AUTO_INCREMENT PRIMARY KEY,
    CouponCode VARCHAR(50) UNIQUE NOT NULL,
    CouponName VARCHAR(100) NOT NULL,
    Description TEXT,
    DiscountType ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
    DiscountValue DECIMAL(10,2) NOT NULL,
    MinimumOrderValue DECIMAL(10,2) DEFAULT 0,
    MaximumDiscountAmount DECIMAL(10,2) DEFAULT 0,
    UsageLimit INT DEFAULT NULL,
    UsedCount INT DEFAULT 0,
    ExpiryDate DATE DEFAULT NULL,
    IsActive TINYINT(1) DEFAULT 1,
    IsShining TINYINT(1) DEFAULT 0 COMMENT 'Highlight this coupon in the dropdown',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample enhanced coupons
INSERT INTO enhanced_coupons (CouponCode, CouponName, Description, DiscountType, DiscountValue, MinimumOrderValue, MaximumDiscountAmount, IsActive, IsShining) VALUES
('WELCOME10', 'Welcome Offer', 'Get 10% off on your first order', 'percentage', 10.00, 500.00, 100.00, 1, 1),
('SAVE20', 'Mega Savings', 'Flat 20% off on orders above ₹1000', 'percentage', 20.00, 1000.00, 200.00, 1, 1),
('FLAT50', 'Flat Discount', 'Flat ₹50 off on orders above ₹300', 'fixed', 50.00, 300.00, 0.00, 1, 0),
('BIGDEAL', 'Big Deal Special', 'Get 25% off on orders above ₹2000', 'percentage', 25.00, 2000.00, 500.00, 1, 1),
('QUICK15', 'Quick Discount', 'Instant 15% off on orders above ₹750', 'percentage', 15.00, 750.00, 150.00, 1, 0);

-- Create indexes for better performance
CREATE INDEX idx_coupon_code ON enhanced_coupons(CouponCode);
CREATE INDEX idx_active_coupons ON enhanced_coupons(IsActive);
CREATE INDEX idx_shining_coupons ON enhanced_coupons(IsShining);
CREATE INDEX idx_minimum_order ON enhanced_coupons(MinimumOrderValue);

-- =====================================================
-- Migration from existing coupons table (if exists)
-- =====================================================

-- Check if old coupons table exists and migrate data
INSERT IGNORE INTO enhanced_coupons (CouponCode, CouponName, Description, DiscountType, DiscountValue, MinimumOrderValue, MaximumDiscountAmount, IsActive)
SELECT 
    CouponCode,
    COALESCE(CouponName, CouponCode) as CouponName,
    COALESCE(Description, CONCAT('Get discount with code ', CouponCode)) as Description,
    CASE 
        WHEN DiscountType = 'percentage' THEN 'percentage'
        ELSE 'fixed'
    END as DiscountType,
    DiscountValue,
    COALESCE(MinimumOrderValue, 0) as MinimumOrderValue,
    COALESCE(MaximumDiscountAmount, 0) as MaximumDiscountAmount,
    COALESCE(IsActive, 1) as IsActive
FROM coupons 
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'coupons' AND table_schema = DATABASE());

-- =====================================================
-- Create view for CMS management
-- =====================================================

CREATE OR REPLACE VIEW coupon_management_view AS
SELECT 
    CouponId,
    CouponCode,
    CouponName,
    Description,
    CASE 
        WHEN DiscountType = 'percentage' THEN CONCAT(DiscountValue, '% OFF')
        ELSE CONCAT('₹', DiscountValue, ' OFF')
    END as DiscountDisplay,
    MinimumOrderValue,
    MaximumDiscountAmount,
    UsageLimit,
    UsedCount,
    ExpiryDate,
    IsActive,
    IsShining,
    CASE 
        WHEN IsShining = 1 THEN '✨ Featured'
        ELSE 'Regular'
    END as ShiningStatus,
    CreatedAt,
    UpdatedAt
FROM enhanced_coupons
ORDER BY IsShining DESC, CreatedAt DESC;

-- =====================================================
-- Stored procedures for coupon management
-- =====================================================

DELIMITER //

-- Procedure to toggle shining status
CREATE PROCEDURE ToggleCouponShining(IN coupon_id INT)
BEGIN
    UPDATE enhanced_coupons 
    SET IsShining = NOT IsShining 
    WHERE CouponId = coupon_id;
END //

-- Procedure to activate/deactivate coupon
CREATE PROCEDURE ToggleCouponStatus(IN coupon_id INT)
BEGIN
    UPDATE enhanced_coupons 
    SET IsActive = NOT IsActive 
    WHERE CouponId = coupon_id;
END //

-- Procedure to get applicable coupons for order amount
CREATE PROCEDURE GetApplicableCoupons(IN order_amount DECIMAL(10,2))
BEGIN
    SELECT 
        CouponCode,
        CouponName,
        Description,
        DiscountType,
        DiscountValue,
        MinimumOrderValue,
        MaximumDiscountAmount,
        IsShining,
        CASE 
            WHEN DiscountType = 'percentage' THEN 
                LEAST(
                    (order_amount * DiscountValue / 100),
                    CASE WHEN MaximumDiscountAmount > 0 THEN MaximumDiscountAmount ELSE 999999 END
                )
            ELSE DiscountValue
        END as PotentialDiscount
    FROM enhanced_coupons 
    WHERE IsActive = 1 
    AND MinimumOrderValue <= order_amount
    AND (ExpiryDate IS NULL OR ExpiryDate >= CURDATE())
    ORDER BY IsShining DESC, PotentialDiscount DESC;
END //

DELIMITER ;

-- =====================================================
-- Sample data update to make some coupons shine
-- =====================================================

-- Make WELCOME10 and SAVE20 shine by default
UPDATE enhanced_coupons 
SET IsShining = 1 
WHERE CouponCode IN ('WELCOME10', 'SAVE20', 'BIGDEAL');

-- Show final table structure
DESCRIBE enhanced_coupons;

SELECT 'Enhanced coupons table created successfully!' as result;

-- =====================================================
-- Dynamic Combo System Database Schema
-- Allows users to create custom combos by selecting products
-- =====================================================

-- =====================================================
-- 1. Dynamic Combos Table
-- =====================================================
CREATE TABLE IF NOT EXISTS dynamic_combos (
    combo_id VARCHAR(50) PRIMARY KEY COMMENT 'Unique identifier for the combo (e.g., COMBO_22_15)',
    product1_id INT NOT NULL COMMENT 'First product in the combo',
    product2_id INT NOT NULL COMMENT 'Second product in the combo',
    combo_name VARCHAR(500) NULL COMMENT 'Generated combo name',
    combo_description TEXT NULL COMMENT 'Generated combo description',
    total_price DECIMAL(10,2) NULL COMMENT 'Combined price of both products',
    discount_percentage DECIMAL(5,2) DEFAULT 10.00 COMMENT 'Discount percentage for combo',
    combo_price DECIMAL(10,2) NULL COMMENT 'Final combo price after discount',
    savings DECIMAL(10,2) NULL COMMENT 'Amount saved with combo',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (product1_id) REFERENCES product_master(ProductId) ON DELETE CASCADE,
    FOREIGN KEY (product2_id) REFERENCES product_master(ProductId) ON DELETE CASCADE,
    
    -- Ensure unique combinations (both directions)
    UNIQUE KEY unique_combo_products (product1_id, product2_id),
    
    -- Indexes for performance
    INDEX idx_product1 (product1_id),
    INDEX idx_product2 (product2_id),
    INDEX idx_active (is_active),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. Combo Views/Clicks Tracking Table
-- =====================================================
CREATE TABLE IF NOT EXISTS combo_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    combo_id VARCHAR(50) NOT NULL,
    action_type ENUM('view', 'click', 'cart_add') NOT NULL,
    user_session VARCHAR(100) NULL COMMENT 'Session ID for tracking',
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (combo_id) REFERENCES dynamic_combos(combo_id) ON DELETE CASCADE,
    INDEX idx_combo_id (combo_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. Insert some sample dynamic combos
-- =====================================================
-- Note: These will be created dynamically when users make selections
-- This is just for reference of the data structure

-- Combo details will be generated in PHP code instead of using triggers
-- to avoid MySQL privilege issues on shared hosting

-- =====================================================
-- 4. View for easy combo querying
-- =====================================================
CREATE OR REPLACE VIEW combo_details_view AS
SELECT 
    dc.combo_id,
    dc.product1_id,
    dc.product2_id,
    dc.combo_name,
    dc.combo_description,
    dc.total_price,
    dc.discount_percentage,
    dc.combo_price,
    dc.savings,
    dc.is_active,
    dc.created_at,
    
    -- Product 1 details
    p1.ProductName as product1_name,
    p1.PhotoPath as product1_image,
    p1.ShortDescription as product1_description,
    
    -- Product 2 details
    p2.ProductName as product2_name,
    p2.PhotoPath as product2_image,
    p2.ShortDescription as product2_description,
    
    -- Analytics
    (SELECT COUNT(*) FROM combo_analytics WHERE combo_id = dc.combo_id AND action_type = 'view') as total_views,
    (SELECT COUNT(*) FROM combo_analytics WHERE combo_id = dc.combo_id AND action_type = 'click') as total_clicks
    
FROM dynamic_combos dc
LEFT JOIN product_master p1 ON dc.product1_id = p1.ProductId
LEFT JOIN product_master p2 ON dc.product2_id = p2.ProductId
WHERE dc.is_active = TRUE;

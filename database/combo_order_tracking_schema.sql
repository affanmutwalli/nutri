-- Combo Order Tracking Table
-- This table tracks combo orders for better management and reporting

CREATE TABLE IF NOT EXISTS combo_order_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    combo_id VARCHAR(50) NOT NULL,
    combo_name VARCHAR(500) NULL,
    combo_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better performance
    INDEX idx_order_id (order_id),
    INDEX idx_combo_id (combo_id),
    INDEX idx_created_at (created_at),
    
    -- Foreign key constraints (if needed)
    FOREIGN KEY (combo_id) REFERENCES dynamic_combos(combo_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample data for testing (optional)
-- INSERT INTO combo_order_tracking (order_id, combo_id, combo_name, combo_price, quantity, total_amount) 
-- VALUES ('CB000001', 'COMBO_14_6', 'Sample Combo', 299.00, 1, 339.00);

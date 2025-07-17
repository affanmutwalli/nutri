-- Delhivery-only delivery integration setup
-- This file sets up the database for Delhivery integration only

-- Create shipping_config table if it doesn't exist
CREATE TABLE IF NOT EXISTS shipping_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(100) NOT NULL UNIQUE,
    config_value TEXT NOT NULL,
    provider VARCHAR(50) DEFAULT 'general',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Delhivery configuration
INSERT INTO shipping_config (config_key, config_value, provider) VALUES
-- Delhivery Configuration
('delhivery_api_key', '', 'delhivery'),
('delhivery_client_name', 'Pure Nutrition Co', 'delhivery'),
('delhivery_return_address', '', 'delhivery'),
('delhivery_return_city', '', 'delhivery'),
('delhivery_return_state', '', 'delhivery'),
('delhivery_return_pincode', '', 'delhivery'),
('delhivery_return_phone', '', 'delhivery'),
('delhivery_seller_name', 'Pure Nutrition Co', 'delhivery'),
('delhivery_seller_address', '', 'delhivery'),
('delhivery_seller_gst', '', 'delhivery'),

-- General Configuration
('auto_create_shipments', '0', 'general'),
('default_package_weight', '0.5', 'general'),
('default_dimensions', '{"length":10,"width":10,"height":10}', 'general') 

ON DUPLICATE KEY UPDATE 
config_value = VALUES(config_value),
provider = VALUES(provider);

-- Create delivery_logs table for logging
CREATE TABLE IF NOT EXISTS delivery_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) NOT NULL,
    provider VARCHAR(50) NOT NULL DEFAULT 'delhivery',
    action VARCHAR(100) NOT NULL,
    status ENUM('success', 'failed', 'pending') NOT NULL,
    response TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_order_id (order_id),
    INDEX idx_provider (provider),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Add delivery columns to order_master table (with error handling for existing columns)
-- Check and add delivery_provider column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE table_name = 'order_master'
     AND column_name = 'delivery_provider'
     AND table_schema = DATABASE()) > 0,
    'SELECT "delivery_provider column already exists"',
    'ALTER TABLE order_master ADD COLUMN delivery_provider VARCHAR(50) DEFAULT "delhivery" AFTER shipping_cost'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add delivery_status column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE table_name = 'order_master'
     AND column_name = 'delivery_status'
     AND table_schema = DATABASE()) > 0,
    'SELECT "delivery_status column already exists"',
    'ALTER TABLE order_master ADD COLUMN delivery_status VARCHAR(50) DEFAULT "pending" AFTER delivery_provider'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add tracking_url column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE table_name = 'order_master'
     AND column_name = 'tracking_url'
     AND table_schema = DATABASE()) > 0,
    'SELECT "tracking_url column already exists"',
    'ALTER TABLE order_master ADD COLUMN tracking_url TEXT NULL AFTER delivery_status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add delivery_notes column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE table_name = 'order_master'
     AND column_name = 'delivery_notes'
     AND table_schema = DATABASE()) > 0,
    'SELECT "delivery_notes column already exists"',
    'ALTER TABLE order_master ADD COLUMN delivery_notes TEXT NULL AFTER tracking_url'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add estimated_delivery_date column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE table_name = 'order_master'
     AND column_name = 'estimated_delivery_date'
     AND table_schema = DATABASE()) > 0,
    'SELECT "estimated_delivery_date column already exists"',
    'ALTER TABLE order_master ADD COLUMN estimated_delivery_date DATE NULL AFTER delivery_notes'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add actual_delivery_date column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE table_name = 'order_master'
     AND column_name = 'actual_delivery_date'
     AND table_schema = DATABASE()) > 0,
    'SELECT "actual_delivery_date column already exists"',
    'ALTER TABLE order_master ADD COLUMN actual_delivery_date TIMESTAMP NULL AFTER estimated_delivery_date'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create delivery_tracking table for tracking information
CREATE TABLE IF NOT EXISTS delivery_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) NOT NULL,
    provider VARCHAR(50) NOT NULL DEFAULT 'delhivery',
    tracking_id VARCHAR(100) NOT NULL,
    waybill_number VARCHAR(100) NULL,
    courier_company VARCHAR(100) NULL,
    current_status VARCHAR(100) NOT NULL,
    current_location VARCHAR(255) NULL,
    status_date TIMESTAMP NOT NULL,
    status_description TEXT NULL,
    is_delivered BOOLEAN DEFAULT FALSE,
    delivery_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_tracking (order_id, provider, tracking_id),
    INDEX idx_order_id (order_id),
    INDEX idx_provider (provider),
    INDEX idx_tracking_id (tracking_id),
    INDEX idx_waybill (waybill_number),
    INDEX idx_status (current_status),
    INDEX idx_delivered (is_delivered)
);

-- Create indexes for better performance (with error handling)
-- Check and create delivery_status index
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE table_name = 'order_master'
     AND index_name = 'idx_order_delivery_status'
     AND table_schema = DATABASE()) > 0,
    'SELECT "idx_order_delivery_status index already exists"',
    'CREATE INDEX idx_order_delivery_status ON order_master(delivery_status)'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and create delivery_provider index
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE table_name = 'order_master'
     AND index_name = 'idx_order_delivery_provider'
     AND table_schema = DATABASE()) > 0,
    'SELECT "idx_order_delivery_provider index already exists"',
    'CREATE INDEX idx_order_delivery_provider ON order_master(delivery_provider)'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and create delivery_date index
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE table_name = 'order_master'
     AND index_name = 'idx_order_delivery_date'
     AND table_schema = DATABASE()) > 0,
    'SELECT "idx_order_delivery_date index already exists"',
    'CREATE INDEX idx_order_delivery_date ON order_master(estimated_delivery_date)'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create a view for delivery status reporting
CREATE OR REPLACE VIEW delivery_status_summary AS
SELECT 
    om.OrderId,
    om.CustomerId,
    om.OrderDate,
    om.delivery_provider,
    om.delivery_status,
    om.shipment_id,
    om.awb_code,
    om.tracking_url,
    om.estimated_delivery_date,
    om.actual_delivery_date,
    dt.current_status as tracking_status,
    dt.current_location,
    dt.courier_company,
    DATEDIFF(COALESCE(om.actual_delivery_date, NOW()), om.OrderDate) as days_since_order
FROM order_master om
LEFT JOIN delivery_tracking dt ON om.OrderId = dt.order_id 
    AND dt.id = (
        SELECT MAX(id) FROM delivery_tracking dt2 
        WHERE dt2.order_id = om.OrderId
    )
WHERE om.delivery_provider = 'delhivery' OR om.delivery_provider IS NULL;

-- Update existing orders to use Delhivery as default provider
UPDATE order_master 
SET delivery_provider = 'delhivery' 
WHERE delivery_provider IS NULL OR delivery_provider = '';

-- Show configuration status
SELECT 'Delhivery Setup Complete' as status;
SELECT config_key, 
       CASE 
           WHEN config_value = '' THEN 'NOT CONFIGURED' 
           ELSE 'CONFIGURED' 
       END as status,
       provider
FROM shipping_config 
WHERE provider = 'delhivery' OR provider = 'general'
ORDER BY provider, config_key;

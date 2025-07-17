-- Simple Delhivery setup for older MySQL versions
-- Run this script to set up Delhivery integration

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

-- Insert Delhivery configuration (ignore if already exists)
INSERT IGNORE INTO shipping_config (config_key, config_value, provider) VALUES
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
('default_dimensions', '{"length":10,"width":10,"height":10}', 'general');

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

-- Show current table structure to check what columns exist
SELECT 'Current order_master columns:' as info;
SHOW COLUMNS FROM order_master;

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

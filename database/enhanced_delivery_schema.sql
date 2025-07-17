-- Enhanced delivery integration schema
-- This file contains all the database changes needed for proper delivery integration

-- Update shipping_config table to support both providers
ALTER TABLE shipping_config 
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS provider VARCHAR(50) DEFAULT 'general',
ADD UNIQUE KEY unique_config_key (config_key);

-- Insert enhanced configuration for both providers
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

-- ShipRocket Configuration (update existing)
('shiprocket_password', '', 'shiprocket'),

-- General Configuration
('default_delivery_provider', 'delhivery', 'general'),
('enable_fallback', 'yes', 'general'),
('auto_assign_delivery', 'yes', 'general'),
('delivery_notification_email', '', 'general')

ON DUPLICATE KEY UPDATE 
config_value = VALUES(config_value),
provider = VALUES(provider);

-- Enhanced order_master table for delivery tracking
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS delivery_provider VARCHAR(50) NULL AFTER shipping_cost,
ADD COLUMN IF NOT EXISTS delivery_status VARCHAR(50) DEFAULT 'pending' AFTER delivery_provider,
ADD COLUMN IF NOT EXISTS tracking_url TEXT NULL AFTER delivery_status,
ADD COLUMN IF NOT EXISTS delivery_attempts INT DEFAULT 0 AFTER tracking_url,
ADD COLUMN IF NOT EXISTS last_delivery_attempt TIMESTAMP NULL AFTER delivery_attempts,
ADD COLUMN IF NOT EXISTS delivery_notes TEXT NULL AFTER last_delivery_attempt,
ADD COLUMN IF NOT EXISTS estimated_delivery_date DATE NULL AFTER delivery_notes,
ADD COLUMN IF NOT EXISTS actual_delivery_date TIMESTAMP NULL AFTER estimated_delivery_date;

-- Create delivery_logs table for comprehensive logging
CREATE TABLE IF NOT EXISTS delivery_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) NOT NULL,
    provider VARCHAR(50) NOT NULL,
    action VARCHAR(100) NOT NULL,
    status ENUM('success', 'failed', 'pending') NOT NULL,
    request_data TEXT NULL,
    response TEXT NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_order_id (order_id),
    INDEX idx_provider (provider),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Create delivery_tracking table for detailed tracking information
CREATE TABLE IF NOT EXISTS delivery_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) NOT NULL,
    provider VARCHAR(50) NOT NULL,
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

-- Create delivery_rates table for rate comparison
CREATE TABLE IF NOT EXISTS delivery_rates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provider VARCHAR(50) NOT NULL,
    from_pincode VARCHAR(10) NOT NULL,
    to_pincode VARCHAR(10) NOT NULL,
    weight_from DECIMAL(5,2) NOT NULL,
    weight_to DECIMAL(5,2) NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    service_type VARCHAR(50) NOT NULL DEFAULT 'standard',
    estimated_days INT NOT NULL DEFAULT 7,
    is_cod_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_provider (provider),
    INDEX idx_pincodes (from_pincode, to_pincode),
    INDEX idx_weight (weight_from, weight_to),
    INDEX idx_service_type (service_type)
);

-- Create delivery_zones table for zone-based provider selection
CREATE TABLE IF NOT EXISTS delivery_zones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    zone_name VARCHAR(100) NOT NULL,
    pincode_start VARCHAR(10) NOT NULL,
    pincode_end VARCHAR(10) NOT NULL,
    preferred_provider VARCHAR(50) NOT NULL,
    backup_provider VARCHAR(50) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    priority INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_zone_name (zone_name),
    INDEX idx_pincodes (pincode_start, pincode_end),
    INDEX idx_provider (preferred_provider),
    INDEX idx_active (is_active),
    INDEX idx_priority (priority)
);

-- Create delivery_performance table for provider performance tracking
CREATE TABLE IF NOT EXISTS delivery_performance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provider VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    total_orders INT DEFAULT 0,
    successful_deliveries INT DEFAULT 0,
    failed_deliveries INT DEFAULT 0,
    average_delivery_time DECIMAL(5,2) DEFAULT 0,
    on_time_deliveries INT DEFAULT 0,
    customer_rating DECIMAL(3,2) DEFAULT 0,
    cost_per_delivery DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_provider_date (provider, date),
    INDEX idx_provider (provider),
    INDEX idx_date (date)
);

-- Add foreign key constraints
ALTER TABLE delivery_logs 
ADD CONSTRAINT fk_delivery_logs_order 
FOREIGN KEY (order_id) REFERENCES order_master(OrderId) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE delivery_tracking 
ADD CONSTRAINT fk_delivery_tracking_order 
FOREIGN KEY (order_id) REFERENCES order_master(OrderId) 
ON DELETE CASCADE ON UPDATE CASCADE;

-- Create indexes for better performance
CREATE INDEX idx_order_delivery_status ON order_master(delivery_status);
CREATE INDEX idx_order_delivery_provider ON order_master(delivery_provider);
CREATE INDEX idx_order_delivery_date ON order_master(estimated_delivery_date);

-- Insert some default delivery zones (example for Indian pincodes) - Delhivery only
INSERT INTO delivery_zones (zone_name, pincode_start, pincode_end, preferred_provider, backup_provider) VALUES
('Mumbai Metro', '400001', '400099', 'delhivery', NULL),
('Delhi NCR', '110001', '110099', 'delhivery', NULL),
('Bangalore', '560001', '560099', 'delhivery', NULL),
('Chennai', '600001', '600099', 'delhivery', NULL),
('Pune', '411001', '411099', 'delhivery', NULL),
('Hyderabad', '500001', '500099', 'delhivery', NULL),
('Kolkata', '700001', '700099', 'delhivery', NULL),
('Ahmedabad', '380001', '380099', 'delhivery', NULL)
ON DUPLICATE KEY UPDATE 
preferred_provider = VALUES(preferred_provider),
backup_provider = VALUES(backup_provider);

-- Create a view for easy delivery status reporting
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
WHERE om.delivery_provider IS NOT NULL;

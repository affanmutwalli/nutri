-- Add ShipRocket related columns to order_master table
ALTER TABLE order_master
ADD COLUMN shipment_id VARCHAR(50) NULL AFTER OrderStatus,
ADD COLUMN awb_code VARCHAR(50) NULL AFTER shipment_id,
ADD COLUMN courier_company_id INT NULL AFTER awb_code,
ADD COLUMN shipping_cost DECIMAL(10,2) NULL AFTER courier_company_id;

-- Add indices for better query performance
CREATE INDEX idx_shipment_id ON order_master(shipment_id);
CREATE INDEX idx_awb_code ON order_master(awb_code);

-- Create table for storing shipping configuration
CREATE TABLE IF NOT EXISTS shipping_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(50) NOT NULL,
    config_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default ShipRocket configuration
INSERT INTO shipping_config (config_key, config_value) VALUES
('shiprocket_api_key', 'YOUR_API_KEY'),
('shiprocket_email', 'YOUR_REGISTERED_EMAIL'),
('pickup_location', 'Primary'),
('default_weight', '0.5'),
('default_dimensions', '{"length":10,"breadth":10,"height":10}');
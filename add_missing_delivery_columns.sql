-- =====================================================
-- Add Missing Delivery Columns to order_master Table
-- This script safely adds all delivery-related columns
-- =====================================================

-- Check current table structure first
SELECT 'Current order_master table structure:' as info;
DESCRIBE order_master;

-- =====================================================
-- Add Waybill and delivery tracking columns
-- =====================================================

-- Add Waybill column (primary waybill field)
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS Waybill VARCHAR(50) NULL 
COMMENT 'Primary Waybill/AWB number from delivery provider';

-- Add WaybillNumber column (alternative waybill field)
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS WaybillNumber VARCHAR(50) NULL 
COMMENT 'Alternative Waybill field for compatibility';

-- Add delivery status tracking
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS delivery_status VARCHAR(50) DEFAULT 'pending' 
COMMENT 'Current delivery status (pending, shipped, delivered, etc.)';

-- Add delivery provider
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS delivery_provider VARCHAR(50) DEFAULT 'delhivery' 
COMMENT 'Delivery service provider (delhivery, etc.)';

-- Add tracking URL
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS tracking_url TEXT NULL 
COMMENT 'Tracking URL from delivery provider';

-- Add delivery dates
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS estimated_delivery_date DATE NULL 
COMMENT 'Estimated delivery date';

ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS actual_delivery_date TIMESTAMP NULL 
COMMENT 'Actual delivery date and time';

-- Add delivery attempts counter
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS delivery_attempts INT DEFAULT 0 
COMMENT 'Number of delivery attempts made';

-- Add delivery notes
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS delivery_notes TEXT NULL 
COMMENT 'Delivery notes and remarks';

-- Add last tracking update timestamp
ALTER TABLE order_master 
ADD COLUMN IF NOT EXISTS last_tracking_update TIMESTAMP NULL 
COMMENT 'Last time tracking information was updated';

-- =====================================================
-- Add indexes for better performance
-- =====================================================

-- Add indexes (ignore errors if they already exist)
CREATE INDEX IF NOT EXISTS idx_waybill ON order_master(Waybill);
CREATE INDEX IF NOT EXISTS idx_waybill_number ON order_master(WaybillNumber);
CREATE INDEX IF NOT EXISTS idx_delivery_status ON order_master(delivery_status);
CREATE INDEX IF NOT EXISTS idx_delivery_provider ON order_master(delivery_provider);
CREATE INDEX IF NOT EXISTS idx_estimated_delivery ON order_master(estimated_delivery_date);
CREATE INDEX IF NOT EXISTS idx_actual_delivery ON order_master(actual_delivery_date);

-- =====================================================
-- Update existing orders with default values
-- =====================================================

-- Set default delivery provider for existing orders
UPDATE order_master 
SET delivery_provider = 'delhivery' 
WHERE delivery_provider IS NULL OR delivery_provider = '';

-- Set default delivery status for existing orders
UPDATE order_master 
SET delivery_status = CASE 
    WHEN OrderStatus = 'Delivered' THEN 'delivered'
    WHEN OrderStatus = 'Shipped' THEN 'shipped'
    WHEN OrderStatus = 'Confirmed' OR OrderStatus = 'Placed' THEN 'pending'
    ELSE 'pending'
END
WHERE delivery_status IS NULL OR delivery_status = '';

-- =====================================================
-- Verify the changes
-- =====================================================

SELECT 'Updated order_master table structure:' as info;
DESCRIBE order_master;

-- Show delivery-related columns
SELECT 'Delivery columns added:' as info;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'order_master'
AND TABLE_SCHEMA = DATABASE()
AND COLUMN_NAME IN (
    'Waybill', 'WaybillNumber', 'delivery_status', 'delivery_provider', 
    'tracking_url', 'estimated_delivery_date', 'actual_delivery_date', 
    'delivery_attempts', 'delivery_notes', 'last_tracking_update'
)
ORDER BY ORDINAL_POSITION;

-- =====================================================
-- Create delivery_logs table if it doesn't exist
-- =====================================================

CREATE TABLE IF NOT EXISTS delivery_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci,
    action VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    message TEXT NULL,
    provider VARCHAR(50) DEFAULT 'delhivery',
    waybill_number VARCHAR(50) NULL,
    tracking_data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_order_id (order_id),
    INDEX idx_action (action),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_waybill (waybill_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Delivery tracking and activity logs';

-- =====================================================
-- Create direct_customers table if it doesn't exist
-- =====================================================

CREATE TABLE IF NOT EXISTS direct_customers (
    CustomerId VARCHAR(50) PRIMARY KEY COLLATE utf8mb4_general_ci,
    CustomerName VARCHAR(100) NOT NULL,
    CustomerEmail VARCHAR(100) NULL,
    CustomerMobile VARCHAR(15) NULL,
    CustomerAddress TEXT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_mobile (CustomerMobile),
    INDEX idx_email (CustomerEmail),
    INDEX idx_name (CustomerName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Direct customers (non-registered)';

SELECT 'Delivery columns and tables setup completed successfully!' as result;

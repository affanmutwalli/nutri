-- Add delivery columns to order_master table (safe version)
-- This script checks for existing columns before adding them

-- First, let's check the current structure
SELECT 'Current order_master table structure:' as info;
DESCRIBE order_master;

-- Check which delivery columns already exist
SELECT 'Checking existing delivery columns...' as info;
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'order_master'
AND TABLE_SCHEMA = DATABASE()
AND COLUMN_NAME IN ('delivery_provider', 'delivery_status', 'tracking_url', 'delivery_notes', 'estimated_delivery_date', 'actual_delivery_date')
ORDER BY ORDINAL_POSITION;

-- Only add columns that don't exist
-- Check and add delivery_status column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME = 'order_master'
                   AND COLUMN_NAME = 'delivery_status'
                   AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@col_exists = 0,
              'ALTER TABLE order_master ADD COLUMN delivery_status VARCHAR(50) DEFAULT "pending"',
              'SELECT "delivery_status column already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add tracking_url column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME = 'order_master'
                   AND COLUMN_NAME = 'tracking_url'
                   AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@col_exists = 0,
              'ALTER TABLE order_master ADD COLUMN tracking_url TEXT NULL',
              'SELECT "tracking_url column already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add delivery_notes column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME = 'order_master'
                   AND COLUMN_NAME = 'delivery_notes'
                   AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@col_exists = 0,
              'ALTER TABLE order_master ADD COLUMN delivery_notes TEXT NULL',
              'SELECT "delivery_notes column already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add estimated_delivery_date column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME = 'order_master'
                   AND COLUMN_NAME = 'estimated_delivery_date'
                   AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@col_exists = 0,
              'ALTER TABLE order_master ADD COLUMN estimated_delivery_date DATE NULL',
              'SELECT "estimated_delivery_date column already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add actual_delivery_date column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME = 'order_master'
                   AND COLUMN_NAME = 'actual_delivery_date'
                   AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@col_exists = 0,
              'ALTER TABLE order_master ADD COLUMN actual_delivery_date TIMESTAMP NULL',
              'SELECT "actual_delivery_date column already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes (ignore errors if they already exist)
-- Note: These will show errors if indexes already exist, but that's OK

-- Try to create delivery_status index
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_NAME = 'order_master'
                     AND INDEX_NAME = 'idx_order_delivery_status'
                     AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@index_exists = 0,
              'CREATE INDEX idx_order_delivery_status ON order_master(delivery_status)',
              'SELECT "idx_order_delivery_status index already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Try to create delivery_provider index
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_NAME = 'order_master'
                     AND INDEX_NAME = 'idx_order_delivery_provider'
                     AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@index_exists = 0,
              'CREATE INDEX idx_order_delivery_provider ON order_master(delivery_provider)',
              'SELECT "idx_order_delivery_provider index already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Try to create delivery_date index
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_NAME = 'order_master'
                     AND INDEX_NAME = 'idx_order_delivery_date'
                     AND TABLE_SCHEMA = DATABASE());

SET @sql = IF(@index_exists = 0,
              'CREATE INDEX idx_order_delivery_date ON order_master(estimated_delivery_date)',
              'SELECT "idx_order_delivery_date index already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing orders to use Delhivery as default provider
UPDATE order_master
SET delivery_provider = 'delhivery'
WHERE delivery_provider IS NULL OR delivery_provider = '';

-- Show the final structure
SELECT 'Final order_master delivery columns:' as info;
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'order_master'
AND TABLE_SCHEMA = DATABASE()
AND COLUMN_NAME IN ('delivery_provider', 'delivery_status', 'tracking_url', 'delivery_notes', 'estimated_delivery_date', 'actual_delivery_date')
ORDER BY ORDINAL_POSITION;

SELECT 'Delivery columns setup completed successfully!' as result;

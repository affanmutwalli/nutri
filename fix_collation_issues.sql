-- =====================================================
-- Fix Collation Issues in Database
-- This script standardizes all tables to use utf8mb4_general_ci
-- =====================================================

-- Check current collations
SELECT 'Current table collations:' as info;
SELECT 
    TABLE_NAME,
    TABLE_COLLATION
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('order_master', 'customer_master', 'direct_customers', 'delivery_logs', 'order_details')
ORDER BY TABLE_NAME;

-- Check current column collations for key fields
SELECT 'Current column collations for key fields:' as info;
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    COLLATION_NAME,
    DATA_TYPE
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('order_master', 'customer_master', 'direct_customers', 'delivery_logs', 'order_details')
AND COLUMN_NAME IN ('OrderId', 'CustomerId', 'order_id', 'CustomerName', 'Name')
ORDER BY TABLE_NAME, COLUMN_NAME;

-- =====================================================
-- Fix table collations to utf8mb4_general_ci
-- =====================================================

-- Fix order_master table
ALTER TABLE order_master CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Fix customer_master table  
ALTER TABLE customer_master CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Fix direct_customers table (if exists)
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'direct_customers');

SET @sql = IF(@table_exists > 0,
              'ALTER TABLE direct_customers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci',
              'SELECT "direct_customers table does not exist" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Fix delivery_logs table (if exists)
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'delivery_logs');

SET @sql = IF(@table_exists > 0,
              'ALTER TABLE delivery_logs CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci',
              'SELECT "delivery_logs table does not exist" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Fix order_details table
ALTER TABLE order_details CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- =====================================================
-- Fix specific column collations if needed
-- =====================================================

-- Fix OrderId columns specifically
ALTER TABLE order_master MODIFY COLUMN OrderId VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE order_details MODIFY COLUMN OrderId VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Fix CustomerId columns specifically  
ALTER TABLE order_master MODIFY COLUMN CustomerId VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE customer_master MODIFY COLUMN CustomerId VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Fix delivery_logs order_id column (if table exists)
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'delivery_logs');

SET @sql = IF(@table_exists > 0,
              'ALTER TABLE delivery_logs MODIFY COLUMN order_id VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci',
              'SELECT "delivery_logs table does not exist" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Fix direct_customers CustomerId column (if table exists)
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'direct_customers');

SET @sql = IF(@table_exists > 0,
              'ALTER TABLE direct_customers MODIFY COLUMN CustomerId VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci',
              'SELECT "direct_customers table does not exist" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- Set database default collation
-- =====================================================

-- Set database default collation
SET @db_name = DATABASE();
SET @sql = CONCAT('ALTER DATABASE ', @db_name, ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- Verify the fixes
-- =====================================================

SELECT 'Updated table collations:' as info;
SELECT 
    TABLE_NAME,
    TABLE_COLLATION
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('order_master', 'customer_master', 'direct_customers', 'delivery_logs', 'order_details')
ORDER BY TABLE_NAME;

SELECT 'Updated column collations for key fields:' as info;
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    COLLATION_NAME,
    DATA_TYPE
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('order_master', 'customer_master', 'direct_customers', 'delivery_logs', 'order_details')
AND COLUMN_NAME IN ('OrderId', 'CustomerId', 'order_id', 'CustomerName', 'Name')
ORDER BY TABLE_NAME, COLUMN_NAME;

SELECT 'Collation fixes completed successfully!' as result;

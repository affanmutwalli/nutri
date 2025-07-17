-- Fix order_details table structure
-- This script ensures the Id column is properly set as auto-increment primary key

-- First, check the current structure
DESCRIBE order_details;

-- Check if Id column exists and its properties
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    EXTRA
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'order_details' 
AND TABLE_SCHEMA = DATABASE()
ORDER BY ORDINAL_POSITION;

-- If the Id column exists but is not auto-increment, fix it
-- Note: This assumes the table structure should have an auto-increment Id column

-- Option 1: If Id column exists but is not auto-increment
-- ALTER TABLE order_details MODIFY COLUMN Id INT AUTO_INCREMENT PRIMARY KEY;

-- Option 2: If Id column doesn't exist, add it
-- ALTER TABLE order_details ADD COLUMN Id INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- Option 3: If the table structure is completely wrong, recreate it
-- But first backup the data if any exists

-- Check if there's any data in the table
SELECT COUNT(*) as record_count FROM order_details;

-- Show current table structure for manual review
SHOW CREATE TABLE order_details;

-- Recommended table structure for order_details:
/*
CREATE TABLE IF NOT EXISTS order_details_new (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    OrderId VARCHAR(50) NOT NULL,
    ProductId INT NOT NULL,
    ProductCode VARCHAR(100),
    Size VARCHAR(50),
    Quantity INT NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    SubTotal DECIMAL(10,2) NOT NULL,
    INDEX idx_order_id (OrderId),
    INDEX idx_product_id (ProductId)
);
*/

-- If you need to migrate data from old table to new table:
-- INSERT INTO order_details_new (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal)
-- SELECT OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal FROM order_details;

-- Then rename tables:
-- DROP TABLE order_details;
-- RENAME TABLE order_details_new TO order_details;

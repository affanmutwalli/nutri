-- =====================================================
-- Guest Checkout Schema Updates
-- Add support for guest checkout functionality
-- =====================================================

-- Check current table structure first
SELECT 'Current order_master table structure:' as info;
DESCRIBE order_master;

-- =====================================================
-- Add Guest Information Columns to order_master Table
-- =====================================================

-- Check if columns exist before adding them
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND COLUMN_NAME = 'GuestName') = 0,
    'ALTER TABLE order_master ADD COLUMN GuestName VARCHAR(255) NULL COMMENT ''Guest customer name for non-registered orders''',
    'SELECT ''GuestName column already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND COLUMN_NAME = 'GuestEmail') = 0,
    'ALTER TABLE order_master ADD COLUMN GuestEmail VARCHAR(255) NULL COMMENT ''Guest customer email for order confirmation''',
    'SELECT ''GuestEmail column already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND COLUMN_NAME = 'GuestPhone') = 0,
    'ALTER TABLE order_master ADD COLUMN GuestPhone VARCHAR(20) NULL COMMENT ''Guest customer phone number for delivery updates''',
    'SELECT ''GuestPhone column already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- Create indexes for better performance
-- =====================================================

-- Index for guest email lookups
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND INDEX_NAME = 'idx_guest_email') = 0,
    'CREATE INDEX idx_guest_email ON order_master(GuestEmail)',
    'SELECT ''idx_guest_email index already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for guest phone lookups
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND INDEX_NAME = 'idx_guest_phone') = 0,
    'CREATE INDEX idx_guest_phone ON order_master(GuestPhone)',
    'SELECT ''idx_guest_phone index already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for customer type filtering
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND INDEX_NAME = 'idx_customer_type') = 0,
    'CREATE INDEX idx_customer_type ON order_master(CustomerType)',
    'SELECT ''idx_customer_type index already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- Update existing data (if needed)
-- =====================================================

-- Update any existing orders with CustomerId = 0 to be marked as Guest
UPDATE order_master 
SET CustomerType = 'Guest' 
WHERE CustomerId = 0 AND CustomerType != 'Guest';

-- =====================================================
-- Create views after ensuring columns exist
-- =====================================================

-- Wait a moment to ensure all columns are created
SELECT SLEEP(1);

-- Create a view for guest orders (only if guest columns exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND COLUMN_NAME IN ('GuestName', 'GuestEmail', 'GuestPhone')) = 3,
    'CREATE OR REPLACE VIEW guest_orders AS
     SELECT
         OrderId,
         GuestName as CustomerName,
         GuestEmail as CustomerEmail,
         GuestPhone as CustomerPhone,
         OrderDate,
         Amount,
         PaymentStatus,
         OrderStatus,
         ShipAddress,
         PaymentType,
         CreatedAt,
         ''Guest'' as CustomerType
     FROM order_master
     WHERE CustomerType = ''Guest'' AND CustomerId = 0',
    'SELECT ''Cannot create guest_orders view - guest columns missing'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create a view for all orders (registered + guest)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'order_master'
     AND COLUMN_NAME IN ('GuestName', 'GuestEmail', 'GuestPhone')) = 3,
    'CREATE OR REPLACE VIEW all_orders_unified AS
     SELECT
         om.OrderId,
         CASE
             WHEN om.CustomerType = ''Guest'' THEN om.GuestName
             ELSE cm.Name
         END as CustomerName,
         CASE
             WHEN om.CustomerType = ''Guest'' THEN om.GuestEmail
             ELSE cm.Email
         END as CustomerEmail,
         CASE
             WHEN om.CustomerType = ''Guest'' THEN om.GuestPhone
             ELSE cm.MobileNo
         END as CustomerPhone,
         om.CustomerId,
         om.CustomerType,
         om.OrderDate,
         om.Amount,
         om.PaymentStatus,
         om.OrderStatus,
         om.ShipAddress,
         om.PaymentType,
         om.TransactionId,
         om.CreatedAt
     FROM order_master om
     LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType != ''Guest''
     ORDER BY om.CreatedAt DESC',
    'SELECT ''Cannot create all_orders_unified view - guest columns missing'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- Show final table structure
-- =====================================================

SELECT 'Updated order_master table structure:' as info;
DESCRIBE order_master;

SELECT 'Guest checkout schema update completed successfully!' as result;

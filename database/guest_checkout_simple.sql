-- =====================================================
-- Simple Guest Checkout Schema (Compatible with older MySQL)
-- =====================================================

-- Add guest information columns to order_master table
ALTER TABLE order_master 
ADD COLUMN GuestName VARCHAR(255) NULL COMMENT 'Guest customer name for non-registered orders';

ALTER TABLE order_master 
ADD COLUMN GuestEmail VARCHAR(255) NULL COMMENT 'Guest customer email for order confirmation';

ALTER TABLE order_master 
ADD COLUMN GuestPhone VARCHAR(20) NULL COMMENT 'Guest customer phone number for delivery updates';

-- Create indexes for better performance
CREATE INDEX idx_guest_email ON order_master(GuestEmail);
CREATE INDEX idx_guest_phone ON order_master(GuestPhone);
CREATE INDEX idx_customer_type ON order_master(CustomerType);

-- Update existing data (if needed)
UPDATE order_master 
SET CustomerType = 'Guest' 
WHERE CustomerId = 0 AND CustomerType != 'Guest';

-- Create a view for guest orders
CREATE OR REPLACE VIEW guest_orders AS
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
    'Guest' as CustomerType
FROM order_master 
WHERE CustomerType = 'Guest' AND CustomerId = 0;

-- Create a view for all orders (registered + guest)
CREATE OR REPLACE VIEW all_orders_unified AS
SELECT 
    om.OrderId,
    CASE 
        WHEN om.CustomerType = 'Guest' THEN om.GuestName
        ELSE cm.Name
    END as CustomerName,
    CASE 
        WHEN om.CustomerType = 'Guest' THEN om.GuestEmail
        ELSE cm.Email
    END as CustomerEmail,
    CASE 
        WHEN om.CustomerType = 'Guest' THEN om.GuestPhone
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
LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType != 'Guest'
ORDER BY om.CreatedAt DESC;

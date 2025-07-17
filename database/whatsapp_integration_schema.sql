-- WhatsApp Integration Database Schema
-- Add WhatsApp support columns and tables to existing Nutrify database
-- Simple version without INFORMATION_SCHEMA checks

-- =====================================================
-- 1. Add WhatsApp columns to customer_master table
-- =====================================================

-- Add WhatsApp opt-in column (ignore error if exists)
ALTER TABLE customer_master
ADD COLUMN whatsapp_opt_in TINYINT(1) DEFAULT 1 COMMENT '1=opted in, 0=opted out';

-- Add WhatsApp opt-out column (ignore error if exists)
ALTER TABLE customer_master
ADD COLUMN whatsapp_opt_out TINYINT(1) DEFAULT 0 COMMENT '1=opted out, 0=opted in';

-- Add last WhatsApp sent timestamp (ignore error if exists)
ALTER TABLE customer_master
ADD COLUMN last_whatsapp_sent TIMESTAMP NULL COMMENT 'Last WhatsApp message sent timestamp';

-- Add DateOfBirth column for birthday wishes (ignore error if exists)
ALTER TABLE customer_master
ADD COLUMN DateOfBirth DATE NULL COMMENT 'Customer birth date for birthday wishes';

-- =====================================================
-- 2. Create WhatsApp message log table
-- =====================================================

CREATE TABLE IF NOT EXISTS whatsapp_message_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id VARCHAR(255) NULL COMMENT 'Interakt API message ID',
    customer_id INT NULL COMMENT 'Reference to customer_master.CustomerId',
    order_id VARCHAR(50) NULL COMMENT 'Reference to order_master.OrderId',
    phone_number VARCHAR(20) NOT NULL COMMENT 'Customer phone number',
    template_name VARCHAR(100) NOT NULL COMMENT 'WhatsApp template name used',
    message_type VARCHAR(50) NOT NULL COMMENT 'Type: order_update, payment_reminder, birthday, etc.',
    status ENUM('pending', 'sent', 'delivered', 'read', 'failed') DEFAULT 'pending',
    api_response TEXT NULL COMMENT 'Full API response from Interakt',
    error_message TEXT NULL COMMENT 'Error message if failed',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    retry_count INT DEFAULT 0 COMMENT 'Number of retry attempts',
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_id (order_id),
    INDEX idx_phone (phone_number),
    INDEX idx_template (template_name),
    INDEX idx_message_type (message_type),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at),
    INDEX idx_retry_count (retry_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. Create WhatsApp activity log table
-- =====================================================

CREATE TABLE IF NOT EXISTS whatsapp_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL COMMENT 'Reference to customer_master.CustomerId',
    order_id VARCHAR(50) NULL COMMENT 'Reference to order_master.OrderId',
    message_type VARCHAR(50) NOT NULL COMMENT 'Activity type',
    success TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=success, 0=failed',
    response TEXT NULL COMMENT 'API response or error details',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_id (order_id),
    INDEX idx_message_type (message_type),
    INDEX idx_success (success),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. Create WhatsApp templates table
-- =====================================================

CREATE TABLE IF NOT EXISTS whatsapp_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100) NOT NULL UNIQUE,
    template_category ENUM('MARKETING', 'UTILITY', 'AUTHENTICATION') NOT NULL,
    language_code VARCHAR(10) DEFAULT 'en',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    header_type ENUM('TEXT', 'IMAGE', 'VIDEO', 'DOCUMENT') NULL,
    header_text TEXT NULL,
    body_text TEXT NOT NULL,
    footer_text VARCHAR(60) NULL,
    button_type ENUM('URL', 'PHONE', 'QUICK_REPLY') NULL,
    button_text VARCHAR(25) NULL,
    variable_count INT DEFAULT 0 COMMENT 'Number of variables in template',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_template_name (template_name),
    INDEX idx_status (status),
    INDEX idx_category (template_category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. Create cart table if it doesn't exist
-- =====================================================

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    CustomerId INT NOT NULL COMMENT 'Reference to customer_master.CustomerId',
    ProductId INT NOT NULL COMMENT 'Reference to product_master.ProductId',
    Quantity INT NOT NULL DEFAULT 1,
    Price DECIMAL(10,2) NOT NULL,
    CreationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_customer_product (CustomerId, ProductId),
    INDEX idx_customer_id (CustomerId),
    INDEX idx_product_id (ProductId),
    INDEX idx_creation_date (CreationDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. Insert default WhatsApp templates
-- =====================================================

INSERT INTO whatsapp_templates (template_name, template_category, language_code, status, body_text, footer_text, variable_count) VALUES
('order_shipped', 'MARKETING', 'en', 'approved', 'Hi {{1}}, great news! Your order #{{2}} has been shipped and is on its way to you. Track your order: {{3}}', 'My Nutrify - Your Health Partner', 3),
('out_for_delivery', 'MARKETING', 'en', 'approved', 'Hi {{1}}, your order #{{2}} is out for delivery and will reach you today ({{3}}). Please keep your phone handy!', 'My Nutrify - Your Health Partner', 3),
('order_delivered', 'MARKETING', 'en', 'approved', 'Hi {{1}}, your order #{{2}} has been delivered successfully! We hope you love your products from {{3}}.', 'My Nutrify - Your Health Partner', 3),
('payment_reminder', 'MARKETING', 'en', 'approved', 'Hi {{1}}, your order #{{2}} for {{3}} is awaiting payment. Complete your payment within {{4}} to confirm your order.', 'My Nutrify - Your Health Partner', 4),
('birthday_wishes', 'MARKETING', 'en', 'approved', '🎉 Happy Birthday {{1}}! Use code {{2}} and get {{3}} OFF on your next order! Valid for 7 days only.', 'My Nutrify - Your Health Partner', 3),
('feedback_request', 'MARKETING', 'en', 'approved', 'Hi {{1}}, we hope you are loving your {{2}} from {{3}}! Could you spare 2 minutes to share your experience?', 'My Nutrify - Your Health Partner', 3)
ON DUPLICATE KEY UPDATE
    body_text = VALUES(body_text),
    variable_count = VALUES(variable_count),
    updated_at = CURRENT_TIMESTAMP;

-- =====================================================
-- 7. Create indexes for better performance
-- =====================================================

-- Add index to customer_master for WhatsApp queries (ignore errors if exist)
CREATE INDEX idx_customer_whatsapp_opt_in ON customer_master(whatsapp_opt_in);
CREATE INDEX idx_customer_birthday ON customer_master(DateOfBirth);
CREATE INDEX idx_customer_mobile ON customer_master(MobileNo);

-- Add index to order_master for WhatsApp queries (ignore errors if exist)
CREATE INDEX idx_order_payment_status ON order_master(PaymentStatus);
CREATE INDEX idx_order_status ON order_master(OrderStatus);
CREATE INDEX idx_order_creation_date ON order_master(CreationDate);

-- =====================================================
-- 8. Create views for easy querying
-- =====================================================

CREATE OR REPLACE VIEW whatsapp_customer_view AS
SELECT
    cm.CustomerId,
    cm.Name,
    cm.Email,
    cm.MobileNo,
    cm.DateOfBirth,
    cm.whatsapp_opt_in,
    cm.whatsapp_opt_out,
    cm.last_whatsapp_sent,
    cm.IsActive,
    COUNT(wml.id) as total_messages_sent,
    MAX(wml.sent_at) as last_message_sent
FROM customer_master cm
LEFT JOIN whatsapp_message_log wml ON cm.CustomerId = wml.customer_id
WHERE cm.IsActive = 1
GROUP BY cm.CustomerId, cm.Name, cm.Email, cm.MobileNo, cm.DateOfBirth, cm.whatsapp_opt_in, cm.whatsapp_opt_out, cm.last_whatsapp_sent, cm.IsActive;

CREATE OR REPLACE VIEW whatsapp_message_stats AS
SELECT
    DATE(sent_at) as message_date,
    template_name,
    message_type,
    status,
    COUNT(*) as message_count,
    COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent_count,
    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count,
    COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_count,
    ROUND(COUNT(CASE WHEN status = 'delivered' THEN 1 END) * 100.0 / COUNT(*), 2) as delivery_rate
FROM whatsapp_message_log
WHERE sent_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(sent_at), template_name, message_type, status
ORDER BY message_date DESC, template_name;

-- =====================================================
-- 9. Show final status
-- =====================================================

SELECT 'WhatsApp Integration Schema Setup Complete!' as status;

-- Show basic table info
SELECT 'Setup completed successfully. Tables created: whatsapp_message_log, whatsapp_activity_log, whatsapp_templates, cart' as info;
SELECT 'Columns added to customer_master: whatsapp_opt_in, whatsapp_opt_out, last_whatsapp_sent, DateOfBirth' as info;

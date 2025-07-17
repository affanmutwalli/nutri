<?php
/**
 * WhatsApp Database Setup Script
 * Run this once to set up WhatsApp integration tables and columns
 */

header('Content-Type: text/html; charset=UTF-8');

// Include database connection
require_once 'database/dbconnection.php';

echo "<h1>🚀 WhatsApp Integration Database Setup</h1>";
echo "<p>Setting up database tables and columns for WhatsApp automation...</p>";

try {
    $obj = new main();
    $obj->connection();
    
    echo "<h2>📋 Setup Progress</h2>";
    
    // Step 1: Add columns to customer_master
    echo "<h3>Step 1: Adding columns to customer_master table</h3>";
    
    $columns = [
        'whatsapp_opt_in' => "ALTER TABLE customer_master ADD COLUMN whatsapp_opt_in TINYINT(1) DEFAULT 1 COMMENT '1=opted in, 0=opted out'",
        'whatsapp_opt_out' => "ALTER TABLE customer_master ADD COLUMN whatsapp_opt_out TINYINT(1) DEFAULT 0 COMMENT '1=opted out, 0=opted in'",
        'last_whatsapp_sent' => "ALTER TABLE customer_master ADD COLUMN last_whatsapp_sent TIMESTAMP NULL COMMENT 'Last WhatsApp message sent timestamp'",
        'DateOfBirth' => "ALTER TABLE customer_master ADD COLUMN DateOfBirth DATE NULL COMMENT 'Customer birth date for birthday wishes'"
    ];
    
    foreach ($columns as $columnName => $sql) {
        try {
            $obj->MysqliQuery($sql);
            echo "<p style='color: green;'>✅ Added column: $columnName</p>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>⚠️ Column $columnName already exists</p>";
            } else {
                echo "<p style='color: red;'>❌ Error adding $columnName: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    // Step 2: Create WhatsApp message log table
    echo "<h3>Step 2: Creating whatsapp_message_log table</h3>";
    
    $messageLogSql = "CREATE TABLE IF NOT EXISTS whatsapp_message_log (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $obj->MysqliQuery($messageLogSql);
        echo "<p style='color: green;'>✅ Created whatsapp_message_log table</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating whatsapp_message_log: " . $e->getMessage() . "</p>";
    }
    
    // Step 3: Create WhatsApp activity log table
    echo "<h3>Step 3: Creating whatsapp_activity_log table</h3>";
    
    $activityLogSql = "CREATE TABLE IF NOT EXISTS whatsapp_activity_log (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $obj->MysqliQuery($activityLogSql);
        echo "<p style='color: green;'>✅ Created whatsapp_activity_log table</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating whatsapp_activity_log: " . $e->getMessage() . "</p>";
    }
    
    // Step 4: Create WhatsApp templates table
    echo "<h3>Step 4: Creating whatsapp_templates table</h3>";
    
    $templatesSql = "CREATE TABLE IF NOT EXISTS whatsapp_templates (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $obj->MysqliQuery($templatesSql);
        echo "<p style='color: green;'>✅ Created whatsapp_templates table</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating whatsapp_templates: " . $e->getMessage() . "</p>";
    }
    
    // Step 5: Create cart table if it doesn't exist
    echo "<h3>Step 5: Creating cart table (if needed)</h3>";
    
    $cartSql = "CREATE TABLE IF NOT EXISTS cart (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $obj->MysqliQuery($cartSql);
        echo "<p style='color: green;'>✅ Created cart table</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating cart table: " . $e->getMessage() . "</p>";
    }
    
    // Step 6: Insert default templates
    echo "<h3>Step 6: Inserting default WhatsApp templates</h3>";
    
    $templates = [
        ['order_shipped', 'MARKETING', 'en', 'approved', 'Hi {{1}}, great news! Your order #{{2}} has been shipped and is on its way to you. Track your order: {{3}}', 'My Nutrify - Your Health Partner', 3],
        ['out_for_delivery', 'MARKETING', 'en', 'approved', 'Hi {{1}}, your order #{{2}} is out for delivery and will reach you today ({{3}}). Please keep your phone handy!', 'My Nutrify - Your Health Partner', 3],
        ['order_delivered', 'MARKETING', 'en', 'approved', 'Hi {{1}}, your order #{{2}} has been delivered successfully! We hope you love your products from {{3}}.', 'My Nutrify - Your Health Partner', 3],
        ['payment_reminder', 'MARKETING', 'en', 'approved', 'Hi {{1}}, your order #{{2}} for {{3}} is awaiting payment. Complete your payment within {{4}} to confirm your order.', 'My Nutrify - Your Health Partner', 4],
        ['birthday_wishes', 'MARKETING', 'en', 'approved', '🎉 Happy Birthday {{1}}! Use code {{2}} and get {{3}} OFF on your next order! Valid for 7 days only.', 'My Nutrify - Your Health Partner', 3],
        ['feedback_request', 'MARKETING', 'en', 'approved', 'Hi {{1}}, we hope you are loving your {{2}} from {{3}}! Could you spare 2 minutes to share your experience?', 'My Nutrify - Your Health Partner', 3]
    ];
    
    foreach ($templates as $template) {
        try {
            $insertSql = "INSERT INTO whatsapp_templates (template_name, template_category, language_code, status, body_text, footer_text, variable_count) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE 
                         body_text = VALUES(body_text),
                         variable_count = VALUES(variable_count),
                         updated_at = CURRENT_TIMESTAMP";
            
            $obj->fInsertNew($insertSql, "ssssssi", $template);
            echo "<p style='color: green;'>✅ Added template: {$template[0]}</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error adding template {$template[0]}: " . $e->getMessage() . "</p>";
        }
    }
    
    // Step 7: Create indexes
    echo "<h3>Step 7: Creating database indexes</h3>";
    
    $indexes = [
        "CREATE INDEX idx_customer_whatsapp_opt_in ON customer_master(whatsapp_opt_in)",
        "CREATE INDEX idx_customer_birthday ON customer_master(DateOfBirth)",
        "CREATE INDEX idx_customer_mobile ON customer_master(MobileNo)",
        "CREATE INDEX idx_order_payment_status ON order_master(PaymentStatus)",
        "CREATE INDEX idx_order_status ON order_master(OrderStatus)",
        "CREATE INDEX idx_order_creation_date ON order_master(CreationDate)"
    ];
    
    foreach ($indexes as $indexSql) {
        try {
            $obj->MysqliQuery($indexSql);
            echo "<p style='color: green;'>✅ Created index</p>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "<p style='color: orange;'>⚠️ Index already exists</p>";
            } else {
                echo "<p style='color: red;'>❌ Error creating index: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ WhatsApp Integration Database Setup Successful!</h3>";
    echo "<p><strong>What was set up:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Added WhatsApp columns to customer_master table</li>";
    echo "<li>✅ Created whatsapp_message_log table for tracking messages</li>";
    echo "<li>✅ Created whatsapp_activity_log table for activity tracking</li>";
    echo "<li>✅ Created whatsapp_templates table for template management</li>";
    echo "<li>✅ Created cart table for cart abandonment tracking</li>";
    echo "<li>✅ Inserted default WhatsApp templates</li>";
    echo "<li>✅ Created database indexes for better performance</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Test the integration with: <a href='test_whatsapp_integration.php'>test_whatsapp_integration.php</a></li>";
    echo "<li>Integrate with your order system</li>";
    echo "<li>Set up automated scheduling</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Setup Failed</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
}

h1, h2, h3 {
    color: #333;
}

p {
    margin: 5px 0;
}

.success {
    color: #28a745;
}

.warning {
    color: #ffc107;
}

.error {
    color: #dc3545;
}
</style>

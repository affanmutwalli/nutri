<?php
// Setup script for product documents table
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed");
}

try {
    echo "<h2>Setting up Product Documents Table</h2>";
    
    // Check if table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'product_documents'");
    
    if ($result->num_rows == 0) {
        echo "<p>Creating product_documents table...</p>";
        
        // Create the table
        $createTableSQL = "
        CREATE TABLE product_documents (
            document_id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            document_title VARCHAR(255) NOT NULL COMMENT 'Display title for the document',
            document_type ENUM('lab_report', 'certificate', 'test_report', 'specification', 'other') DEFAULT 'lab_report',
            file_name VARCHAR(255) NOT NULL COMMENT 'Original filename',
            file_path VARCHAR(500) NOT NULL COMMENT 'Path to the uploaded file',
            file_size INT NULL COMMENT 'File size in bytes',
            mime_type VARCHAR(100) NULL COMMENT 'MIME type of the file',
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = inactive',
            display_order INT DEFAULT 0 COMMENT 'Order for displaying documents',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_product_id (product_id),
            INDEX idx_document_type (document_type),
            INDEX idx_is_active (is_active),
            INDEX idx_display_order (display_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        COMMENT='Table to store PDF documents and files associated with products'";
        
        if ($mysqli->query($createTableSQL)) {
            echo "<p style='color: green;'>✅ Table created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Error creating table: " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Table already exists.</p>";
    }
    
    // Create directories
    echo "<p>Creating upload directories...</p>";
    $directories = [
        'cms/docs',
        'cms/docs/products',
        'cms/docs/products/lab_reports',
        'cms/docs/products/certificates',
        'cms/docs/products/test_reports',
        'cms/docs/products/specifications',
        'cms/docs/products/other'
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "<p style='color: green;'>✅ Created directory: $dir</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create directory: $dir</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ Directory already exists: $dir</p>";
        }
    }
    
    echo "<h3 style='color: green;'>✅ Setup Complete!</h3>";
    echo "<p>The PDF document management system is ready to use.</p>";
    echo "<p><a href='cms/products.php'>Go to Products CMS</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>

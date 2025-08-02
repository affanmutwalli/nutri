<?php
include('database/dbconnection.php');

$obj = new main();
$connection = $obj->connection();

echo "<h2>Setting up Dynamic Combo System</h2>";

try {
    $successCount = 0;
    $errorCount = 0;

    // Create dynamic_combos table
    $createDynamicCombos = "
    CREATE TABLE IF NOT EXISTS dynamic_combos (
        combo_id VARCHAR(50) PRIMARY KEY COMMENT 'Unique identifier for the combo (e.g., COMBO_22_15)',
        product1_id INT NOT NULL COMMENT 'First product in the combo',
        product2_id INT NOT NULL COMMENT 'Second product in the combo',
        combo_name VARCHAR(255) NULL COMMENT 'Generated combo name',
        combo_description TEXT NULL COMMENT 'Generated combo description',
        total_price DECIMAL(10,2) NULL COMMENT 'Combined price of both products',
        discount_percentage DECIMAL(5,2) DEFAULT 10.00 COMMENT 'Discount percentage for combo',
        combo_price DECIMAL(10,2) NULL COMMENT 'Final combo price after discount',
        savings DECIMAL(10,2) NULL COMMENT 'Amount saved with combo',
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        FOREIGN KEY (product1_id) REFERENCES product_master(ProductId) ON DELETE CASCADE,
        FOREIGN KEY (product2_id) REFERENCES product_master(ProductId) ON DELETE CASCADE,

        UNIQUE KEY unique_combo_products (product1_id, product2_id),

        INDEX idx_product1 (product1_id),
        INDEX idx_product2 (product2_id),
        INDEX idx_active (is_active),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($connection->query($createDynamicCombos)) {
        $successCount++;
        echo "<p style='color: green;'>✓ Created table: dynamic_combos</p>";
    } else {
        $errorCount++;
        echo "<p style='color: red;'>✗ Error creating dynamic_combos table: " . $connection->error . "</p>";
    }

    // Create combo_analytics table
    $createComboAnalytics = "
    CREATE TABLE IF NOT EXISTS combo_analytics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        combo_id VARCHAR(50) NOT NULL,
        action_type ENUM('view', 'click', 'cart_add') NOT NULL,
        user_session VARCHAR(100) NULL COMMENT 'Session ID for tracking',
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

        FOREIGN KEY (combo_id) REFERENCES dynamic_combos(combo_id) ON DELETE CASCADE,
        INDEX idx_combo_id (combo_id),
        INDEX idx_action_type (action_type),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($connection->query($createComboAnalytics)) {
        $successCount++;
        echo "<p style='color: green;'>✓ Created table: combo_analytics</p>";
    } else {
        $errorCount++;
        echo "<p style='color: red;'>✗ Error creating combo_analytics table: " . $connection->error . "</p>";
    }

    // Create combo_details_view
    $createComboView = "
    CREATE OR REPLACE VIEW combo_details_view AS
    SELECT
        dc.combo_id,
        dc.product1_id,
        dc.product2_id,
        dc.combo_name,
        dc.combo_description,
        dc.total_price,
        dc.discount_percentage,
        dc.combo_price,
        dc.savings,
        dc.is_active,
        dc.created_at,

        p1.ProductName as product1_name,
        p1.PhotoPath as product1_image,
        p1.ShortDescription as product1_description,

        p2.ProductName as product2_name,
        p2.PhotoPath as product2_image,
        p2.ShortDescription as product2_description,

        (SELECT COUNT(*) FROM combo_analytics WHERE combo_id = dc.combo_id AND action_type = 'view') as total_views,
        (SELECT COUNT(*) FROM combo_analytics WHERE combo_id = dc.combo_id AND action_type = 'click') as total_clicks

    FROM dynamic_combos dc
    LEFT JOIN product_master p1 ON dc.product1_id = p1.ProductId
    LEFT JOIN product_master p2 ON dc.product2_id = p2.ProductId
    WHERE dc.is_active = TRUE";

    if ($connection->query($createComboView)) {
        $successCount++;
        echo "<p style='color: green;'>✓ Created view: combo_details_view</p>";
    } else {
        $errorCount++;
        echo "<p style='color: red;'>✗ Error creating combo_details_view: " . $connection->error . "</p>";
    }
    
    echo "<h3>Setup Summary</h3>";
    echo "<p>Successful statements: <strong>$successCount</strong></p>";
    echo "<p>Failed statements: <strong>$errorCount</strong></p>";
    
    if ($errorCount === 0) {
        echo "<p style='color: green; font-weight: bold;'>✓ Dynamic Combo System setup completed successfully!</p>";
        echo "<h4>Next Steps:</h4>";
        echo "<ul>";
        echo "<li>Visit your homepage and try selecting products from both combo carousels</li>";
        echo "<li>The system will automatically create a combo and redirect you to the combo product page</li>";
        echo "<li>You can view all created combos in the database table 'dynamic_combos'</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange; font-weight: bold;'>⚠ Setup completed with some errors. Please check the errors above.</p>";
    }

    // Test database connection and show table status
    echo "<h4>Database Table Status:</h4>";

    $tables = ['dynamic_combos', 'combo_analytics'];
    foreach ($tables as $table) {
        $result = $connection->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Table '$table' exists</p>";

            // Show table structure
            $structure = $connection->query("DESCRIBE $table");
            if ($structure) {
                echo "<details><summary>View $table structure</summary>";
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                while ($row = $structure->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Field']}</td>";
                    echo "<td>{$row['Type']}</td>";
                    echo "<td>{$row['Null']}</td>";
                    echo "<td>{$row['Key']}</td>";
                    echo "<td>{$row['Default']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</details>";
            }
        } else {
            echo "<p style='color: red;'>✗ Table '$table' does not exist</p>";
        }
    }

    // Check if view exists
    $viewResult = $connection->query("SHOW TABLES LIKE 'combo_details_view'");
    if ($viewResult && $viewResult->num_rows > 0) {
        echo "<p style='color: green;'>✓ View 'combo_details_view' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ View 'combo_details_view' does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Homepage</a> | <a href='combos.php'>View Combos Page</a></p>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dynamic Combo System Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h2, h3, h4 { color: #333; }
        p { margin: 5px 0; }
        details { margin: 10px 0; }
        summary { cursor: pointer; font-weight: bold; }
        table { width: 100%; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <!-- Content is echoed above -->
</body>
</html>

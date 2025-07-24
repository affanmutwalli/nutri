<?php
/**
 * Create the product_offers table and add sample data
 */

include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>Creating Product Offers Table...</h2>";

try {
    // Create the product_offers table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS product_offers (
        offer_id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        offer_title VARCHAR(255) NULL COMMENT 'Optional custom title for the offer',
        offer_description TEXT NULL COMMENT 'Optional description for the offer',
        is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active offer, 0 = inactive',
        created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        -- Foreign key constraint to ensure product exists
        FOREIGN KEY (product_id) REFERENCES product_master(ProductId) ON DELETE CASCADE ON UPDATE CASCADE,
        
        -- Unique constraint to ensure each product can only have one offer entry
        UNIQUE KEY unique_product_offer (product_id),
        
        -- Index for faster queries
        INDEX idx_is_active (is_active),
        INDEX idx_created_date (created_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Table to manage which products are featured as special offers'
    ";

    if ($mysqli->query($createTableSQL)) {
        echo "<p style='color: green;'>✓ Product offers table created successfully!</p>";
    } else {
        throw new Exception("Error creating table: " . $mysqli->error);
    }

    // Add some sample offers - only add the product with price 499 that you mentioned
    $sampleOffers = [
        19, // The Shilajit product with price 499 that you mentioned
        6,  // Wild Amla Juice
        14  // Wheatgrass Juice
    ];

    echo "<h3>Adding sample offers...</h3>";
    
    foreach ($sampleOffers as $productId) {
        $insertSQL = "INSERT INTO product_offers (product_id, offer_title, offer_description, is_active) 
                     VALUES (?, 'Special Offer', 'Limited time special offer', 1) 
                     ON DUPLICATE KEY UPDATE 
                         offer_title = VALUES(offer_title),
                         offer_description = VALUES(offer_description),
                         is_active = VALUES(is_active),
                         updated_date = CURRENT_TIMESTAMP";
        
        $stmt = $mysqli->prepare($insertSQL);
        $stmt->bind_param("i", $productId);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✓ Added offer for Product ID: $productId</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Could not add offer for Product ID: $productId (may not exist)</p>";
        }
    }

    echo "<h3>Checking results...</h3>";
    
    // Check what offers were created
    $checkSQL = "SELECT po.*, pm.ProductName 
                FROM product_offers po 
                INNER JOIN product_master pm ON po.product_id = pm.ProductId 
                WHERE po.is_active = 1";
    
    $result = $mysqli->query($checkSQL);
    
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Found " . $result->num_rows . " active offers:</p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>Product ID: {$row['product_id']} - {$row['ProductName']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ No active offers found</p>";
    }

    echo "<p style='color: blue;'><strong>Setup complete! You can now visit the offers page.</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>

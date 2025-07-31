<?php
// Simple debug script to test combo creation
session_start();

// Capture all output
ob_start();

include('database/dbconnection.php');

$obj = new main();
$connection = $obj->connection();

echo "Testing combo creation...\n";

try {
    // Test with products 14 and 6 (from your error)
    $product1_id = 14;
    $product2_id = 6;
    $combo_id = "COMBO_{$product1_id}_{$product2_id}";
    
    echo "Product IDs: $product1_id, $product2_id\n";
    echo "Combo ID: $combo_id\n";
    
    // Check if products exist
    $stmt = $connection->prepare("SELECT ProductId, ProductName FROM product_master WHERE ProductId = ?");
    $stmt->bind_param("i", $product1_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product1 = $result->fetch_assoc();
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT ProductId, ProductName FROM product_master WHERE ProductId = ?");
    $stmt->bind_param("i", $product2_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product2 = $result->fetch_assoc();
    $stmt->close();
    
    if (!$product1) {
        echo "ERROR: Product 1 (ID: $product1_id) not found\n";
        exit;
    }
    
    if (!$product2) {
        echo "ERROR: Product 2 (ID: $product2_id) not found\n";
        exit;
    }
    
    echo "Product 1: " . $product1['ProductName'] . "\n";
    echo "Product 2: " . $product2['ProductName'] . "\n";
    
    // Get prices
    $stmt = $connection->prepare("SELECT MIN(OfferPrice) as min_price FROM product_price WHERE ProductId = ? AND OfferPrice > 0");
    $stmt->bind_param("i", $product1_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $price1_data = $result->fetch_assoc();
    $product1_price = $price1_data['min_price'] ?? 0;
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT MIN(OfferPrice) as min_price FROM product_price WHERE ProductId = ? AND OfferPrice > 0");
    $stmt->bind_param("i", $product2_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $price2_data = $result->fetch_assoc();
    $product2_price = $price2_data['min_price'] ?? 0;
    $stmt->close();
    
    echo "Product 1 price: $product1_price\n";
    echo "Product 2 price: $product2_price\n";
    
    // Calculate combo details
    $total_price = $product1_price + $product2_price;
    $discount_percentage = 10.00;
    $combo_price = $total_price * (1 - $discount_percentage / 100);
    $savings = $total_price - $combo_price;
    
    // Create shorter combo name
    $product1_short = strlen($product1['ProductName']) > 30 ? substr($product1['ProductName'], 0, 30) . '...' : $product1['ProductName'];
    $product2_short = strlen($product2['ProductName']) > 30 ? substr($product2['ProductName'], 0, 30) . '...' : $product2['ProductName'];
    $combo_name = $product1_short . ' + ' . $product2_short . ' Combo';
    $combo_description = 'Special combo offer: Get ' . $product1['ProductName'] . ' and ' . $product2['ProductName'] . ' together at a discounted price!';
    
    echo "Combo name: $combo_name\n";
    echo "Combo name length: " . strlen($combo_name) . "\n";
    echo "Total price: $total_price\n";
    echo "Combo price: $combo_price\n";
    echo "Savings: $savings\n";
    
    // Check if combo already exists
    $stmt = $connection->prepare("SELECT combo_id FROM dynamic_combos WHERE combo_id = ?");
    $stmt->bind_param("s", $combo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingCombo = $result->fetch_assoc();
    $stmt->close();
    
    if ($existingCombo) {
        echo "Combo already exists, deleting for test...\n";
        $stmt = $connection->prepare("DELETE FROM dynamic_combos WHERE combo_id = ?");
        $stmt->bind_param("s", $combo_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Try to create combo
    echo "Attempting to create combo...\n";
    $stmt = $connection->prepare("INSERT INTO dynamic_combos (combo_id, product1_id, product2_id, combo_name, combo_description, total_price, discount_percentage, combo_price, savings) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siissdddd", $combo_id, $product1_id, $product2_id, $combo_name, $combo_description, $total_price, $discount_percentage, $combo_price, $savings);
    $insertResult = $stmt->execute();
    
    if ($insertResult) {
        echo "SUCCESS: Combo created successfully!\n";
        
        // Try to fetch from view
        $stmt2 = $connection->prepare("SELECT * FROM combo_details_view WHERE combo_id = ?");
        $stmt2->bind_param("s", $combo_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $comboDetails = $result2->fetch_assoc();
        $stmt2->close();
        
        if ($comboDetails) {
            echo "SUCCESS: Combo fetched from view successfully!\n";
            echo "Fetched combo name: " . $comboDetails['combo_name'] . "\n";
        } else {
            echo "ERROR: Could not fetch combo from view\n";
        }
        
    } else {
        echo "ERROR: Failed to create combo\n";
        echo "MySQL Error: " . $stmt->error . "\n";
        echo "MySQL Error Number: " . $stmt->errno . "\n";
    }
    $stmt->close();
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

$output = ob_get_clean();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Combo Creation</title>
    <style>
        body { font-family: monospace; white-space: pre-wrap; padding: 20px; }
    </style>
</head>
<body>
<?php echo htmlspecialchars($output); ?>
</body>
</html>

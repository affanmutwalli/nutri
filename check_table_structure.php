<?php
/**
 * Check table structures for combo order system
 */

echo "<h2>🔍 Table Structure Analysis</h2>";

try {
    include('database/dbconnection.php');
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    // Check order_details table structure
    echo "<h3>📋 order_details Table Structure:</h3>";
    $structure_query = "DESCRIBE order_details";
    $structure_result = $mysqli->query($structure_query);
    
    if ($structure_result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $structure_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check combo_details_view structure
    echo "<h3>🔗 combo_details_view Structure:</h3>";
    $view_query = "DESCRIBE combo_details_view";
    $view_result = $mysqli->query($view_query);
    
    if ($view_result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $view_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check product_price table structure
    echo "<h3>💰 product_price Table Structure:</h3>";
    $price_query = "DESCRIBE product_price";
    $price_result = $mysqli->query($price_query);
    
    if ($price_result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $price_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test query to get product prices for combo
    echo "<h3>🧪 Test Product Price Query:</h3>";
    $combo_id = 'COMBO_14_6';
    
    $test_query = "
    SELECT 
        dc.combo_id,
        dc.product1_id,
        dc.product2_id,
        dc.combo_price,
        p1.ProductName as product1_name,
        p2.ProductName as product2_name,
        pp1.OfferPrice as product1_price,
        pp2.OfferPrice as product2_price
    FROM dynamic_combos dc
    LEFT JOIN product_master p1 ON dc.product1_id = p1.ProductId
    LEFT JOIN product_master p2 ON dc.product2_id = p2.ProductId
    LEFT JOIN product_price pp1 ON dc.product1_id = pp1.ProductId
    LEFT JOIN product_price pp2 ON dc.product2_id = pp2.ProductId
    WHERE dc.combo_id = ?
    ";
    
    $test_stmt = $mysqli->prepare($test_query);
    $test_stmt->bind_param("s", $combo_id);
    $test_stmt->execute();
    $test_result = $test_stmt->get_result();
    
    if ($test_result && $test_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'>";
        echo "<th>Combo ID</th><th>Product 1 ID</th><th>Product 1 Name</th><th>Product 1 Price</th>";
        echo "<th>Product 2 ID</th><th>Product 2 Name</th><th>Product 2 Price</th><th>Combo Price</th>";
        echo "</tr>";
        
        while ($row = $test_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['combo_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['product1_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['product1_name']) . "</td>";
            echo "<td>₹" . htmlspecialchars($row['product1_price'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['product2_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['product2_name']) . "</td>";
            echo "<td>₹" . htmlspecialchars($row['product2_price'] ?? 'N/A') . "</td>";
            echo "<td>₹" . htmlspecialchars($row['combo_price']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No data found for combo: $combo_id</p>";
    }
    
    $test_stmt->close();
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

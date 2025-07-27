<?php
// Investigate Phantom Products Issue
// This script will analyze the database and order processing to find the root cause

// Include database configuration and connection
include_once 'cms/includes/psl-config.php';
include_once 'cms/database/dbconnection.php';

// Initialize database object
$obj = new main();

echo "<h2>🔍 Phantom Products Investigation</h2>\n";
echo "<p>Analyzing the system to find why unwanted products are being added to orders...</p>\n";

try {
    // Step 1: Check for database triggers
    echo "<h3>Step 1: Database Triggers Analysis</h3>\n";
    
    $triggerQuery = "SHOW TRIGGERS FROM my_nutrify_db";
    $mysqli = $obj->connection();
    $triggerResult = mysqli_query($mysqli, $triggerQuery);
    
    if ($triggerResult && mysqli_num_rows($triggerResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Trigger Name</th><th>Event</th><th>Table</th><th>Statement</th></tr>\n";
        
        while ($trigger = mysqli_fetch_assoc($triggerResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($trigger['Trigger']) . "</td>";
            echo "<td>" . htmlspecialchars($trigger['Event']) . "</td>";
            echo "<td>" . htmlspecialchars($trigger['Table']) . "</td>";
            echo "<td style='max-width: 300px; word-wrap: break-word;'>" . htmlspecialchars($trigger['Statement']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No database triggers found</p>\n";
    }
    
    // Step 2: Check for stored procedures
    echo "<h3>Step 2: Stored Procedures Analysis</h3>\n";
    
    $procedureQuery = "SHOW PROCEDURE STATUS WHERE Db = 'my_nutrify_db'";
    $procedureResult = mysqli_query($mysqli, $procedureQuery);
    
    if ($procedureResult && mysqli_num_rows($procedureResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Procedure Name</th><th>Type</th><th>Created</th></tr>\n";
        
        while ($procedure = mysqli_fetch_assoc($procedureResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($procedure['Name']) . "</td>";
            echo "<td>" . htmlspecialchars($procedure['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($procedure['Created']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No stored procedures found</p>\n";
    }
    
    // Step 3: Analyze recent orders with phantom products
    echo "<h3>Step 3: Recent Orders with Phantom Products</h3>\n";
    
    $phantomQuery = "
        SELECT
            od.OrderId,
            od.ProductId,
            od.ProductCode,
            od.Quantity,
            od.Price,
            od.SubTotal,
            pm.ProductName,
            om.OrderDate,
            om.CustomerId
        FROM order_details od
        LEFT JOIN product_master pm ON od.ProductId = pm.ProductId
        LEFT JOIN order_master om ON od.OrderId = om.OrderId
        WHERE (od.ProductCode LIKE BINARY '%AC%' OR od.ProductCode LIKE BINARY '%SC%' OR od.ProductCode = '' OR od.ProductCode IS NULL)
        ORDER BY om.OrderDate DESC
        LIMIT 20
    ";
    
    $phantomResult = mysqli_query($mysqli, $phantomQuery);
    
    if ($phantomResult && mysqli_num_rows($phantomResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Order ID</th><th>Product ID</th><th>Product Code</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Order Date</th></tr>\n";
        
        while ($phantom = mysqli_fetch_assoc($phantomResult)) {
            echo "<tr style='background-color: #ffeeee;'>";
            echo "<td>" . htmlspecialchars($phantom['OrderId']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['ProductCode']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['ProductName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($phantom['Quantity']) . "</td>";
            echo "<td>₹" . number_format($phantom['Price'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['OrderDate']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No recent phantom products found in orders</p>\n";
    }
    
    // Step 4: Check product master for suspicious products
    echo "<h3>Step 4: Suspicious Products in Database</h3>\n";
    
    $suspiciousQuery = "
        SELECT
            ProductId,
            ProductName,
            ProductCode,
            CategoryId,
            SubCategoryId,
            IsActive,
            IsCombo
        FROM product_master
        WHERE ProductId IN (12, 15) OR ProductCode LIKE BINARY '%AC%' OR ProductCode LIKE BINARY '%SC%'
        ORDER BY ProductId
    ";
    
    $suspiciousResult = mysqli_query($mysqli, $suspiciousQuery);
    
    if ($suspiciousResult && mysqli_num_rows($suspiciousResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Product Code</th><th>Category ID</th><th>SubCategory ID</th><th>Is Active</th><th>Is Combo</th></tr>\n";
        
        while ($product = mysqli_fetch_assoc($suspiciousResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($product['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($product['ProductName']) . "</td>";
            echo "<td>" . htmlspecialchars($product['ProductCode']) . "</td>";
            echo "<td>" . htmlspecialchars($product['CategoryId']) . "</td>";
            echo "<td>" . htmlspecialchars($product['SubCategoryId']) . "</td>";
            echo "<td>" . htmlspecialchars($product['IsActive']) . "</td>";
            echo "<td>" . htmlspecialchars($product['IsCombo']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: orange;'>⚠️ Suspicious products not found in product_master</p>\n";
    }
    
    // Step 5: Check for combo product relationships
    echo "<h3>Step 5: Combo Product Relationships</h3>\n";
    
    $comboQuery = "
        SELECT 
            pm.ProductId,
            pm.ProductName,
            pm.IsCombo,
            GROUP_CONCAT(DISTINCT pm2.ProductName) as RelatedProducts
        FROM product_master pm
        LEFT JOIN product_master pm2 ON pm.CategoryId = pm2.CategoryId AND pm.ProductId != pm2.ProductId
        WHERE pm.IsCombo = 'Y' OR pm.ProductId IN (6, 11, 12, 15)
        GROUP BY pm.ProductId, pm.ProductName, pm.IsCombo
        ORDER BY pm.ProductId
    ";
    
    $comboResult = mysqli_query($mysqli, $comboQuery);
    
    if ($comboResult && mysqli_num_rows($comboResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Is Combo</th><th>Related Products</th></tr>\n";
        
        while ($combo = mysqli_fetch_assoc($comboResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($combo['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($combo['ProductName']) . "</td>";
            echo "<td>" . htmlspecialchars($combo['IsCombo']) . "</td>";
            echo "<td style='max-width: 300px; word-wrap: break-word;'>" . htmlspecialchars($combo['RelatedProducts']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No combo product relationships found</p>\n";
    }
    
    // Step 6: Check order processing logs
    echo "<h3>Step 6: Order Processing Pattern Analysis</h3>\n";
    
    $patternQuery = "
        SELECT 
            om.OrderId,
            om.CustomerId,
            om.OrderDate,
            COUNT(od.ProductId) as ProductCount,
            GROUP_CONCAT(od.ProductId) as ProductIds,
            GROUP_CONCAT(od.ProductCode) as ProductCodes,
            SUM(od.SubTotal) as CalculatedTotal,
            om.Amount as OrderTotal
        FROM order_master om
        LEFT JOIN order_details od ON om.OrderId = od.OrderId
        WHERE om.OrderDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY om.OrderId, om.CustomerId, om.OrderDate, om.Amount
        HAVING ProductCount > 1
        ORDER BY om.OrderDate DESC
        LIMIT 10
    ";
    
    $patternResult = mysqli_query($mysqli, $patternQuery);
    
    if ($patternResult && mysqli_num_rows($patternResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Order ID</th><th>Customer ID</th><th>Order Date</th><th>Product Count</th><th>Product IDs</th><th>Product Codes</th><th>Calculated Total</th><th>Order Total</th></tr>\n";
        
        while ($pattern = mysqli_fetch_assoc($patternResult)) {
            $isPhantom = (strpos($pattern['ProductCodes'], 'AC') !== false || strpos($pattern['ProductCodes'], 'SC') !== false);
            $bgColor = $isPhantom ? 'background-color: #ffeeee;' : '';
            
            echo "<tr style='$bgColor'>";
            echo "<td>" . htmlspecialchars($pattern['OrderId']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['CustomerId']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['OrderDate']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['ProductCount']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['ProductIds']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['ProductCodes']) . "</td>";
            echo "<td>₹" . number_format($pattern['CalculatedTotal'], 2) . "</td>";
            echo "<td>₹" . number_format($pattern['OrderTotal'], 2) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No multi-product orders found in recent history</p>\n";
    }
    
    mysqli_close($mysqli);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error during investigation: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h3>🎯 Investigation Summary</h3>\n";
echo "<div style='background-color: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<p><strong>Key Findings:</strong></p>\n";
echo "<ul>\n";
echo "<li>Database triggers and stored procedures analysis</li>\n";
echo "<li>Recent phantom product occurrences</li>\n";
echo "<li>Product relationships and combo configurations</li>\n";
echo "<li>Order processing patterns</li>\n";
echo "</ul>\n";
echo "<p><strong>Next Steps:</strong> Review the order processing scripts for automatic product additions.</p>\n";
echo "</div>\n";
?>

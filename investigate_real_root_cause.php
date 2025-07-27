<?php
// Investigate Real Root Cause - These are legitimate products being auto-added
include_once 'cms/includes/psl-config.php';

echo "<h2>🔍 Real Root Cause Investigation</h2>\n";
echo "<p>The 'phantom' products are actually legitimate products being automatically added. Let's find out why...</p>\n";

try {
    $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    // Step 1: Get details of both products
    echo "<h3>Step 1: Product Details</h3>\n";
    
    $productQuery = "SELECT ProductId, ProductName, ProductCode, CategoryId, SubCategoryId FROM product_master WHERE ProductId IN (12, 15)";
    $result = $mysqli->query($productQuery);
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Product Code</th><th>Category ID</th><th>SubCategory ID</th></tr>\n";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ProductCode']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CategoryId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['SubCategoryId']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    // Step 2: Check if these products are in any combo or bundle relationships
    echo "<h3>Step 2: Combo/Bundle Relationships</h3>\n";
    
    // Check if products 6 and 11 (the ones user wanted) are related to 12 and 15
    $relationQuery = "
        SELECT 
            pm1.ProductId as Product1_ID,
            pm1.ProductName as Product1_Name,
            pm1.CategoryId as Product1_Category,
            pm1.SubCategoryId as Product1_SubCategory,
            pm2.ProductId as Product2_ID,
            pm2.ProductName as Product2_Name,
            pm2.CategoryId as Product2_Category,
            pm2.SubCategoryId as Product2_SubCategory
        FROM product_master pm1
        CROSS JOIN product_master pm2
        WHERE pm1.ProductId IN (6, 11) 
        AND pm2.ProductId IN (12, 15)
        AND (pm1.CategoryId = pm2.CategoryId OR pm1.SubCategoryId = pm2.SubCategoryId)
    ";
    
    $relationResult = $mysqli->query($relationQuery);
    
    if ($relationResult && $relationResult->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Found category/subcategory relationships:</p>\n";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Ordered Product</th><th>Auto-Added Product</th><th>Relationship</th></tr>\n";
        
        while ($relation = $relationResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($relation['Product1_Name']) . " (ID: " . $relation['Product1_ID'] . ")</td>";
            echo "<td>" . htmlspecialchars($relation['Product2_Name']) . " (ID: " . $relation['Product2_ID'] . ")</td>";
            
            $relationship = "";
            if ($relation['Product1_Category'] == $relation['Product2_Category']) {
                $relationship .= "Same Category (" . $relation['Product1_Category'] . ")";
            }
            if ($relation['Product1_SubCategory'] == $relation['Product2_SubCategory']) {
                $relationship .= " Same SubCategory (" . $relation['Product1_SubCategory'] . ")";
            }
            
            echo "<td>" . htmlspecialchars($relationship) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No direct category relationships found</p>\n";
    }
    
    // Step 3: Check for any automatic bundling logic in the database
    echo "<h3>Step 3: Database Bundling Logic</h3>\n";
    
    // Check for any tables that might define product bundles
    $tablesQuery = "SHOW TABLES LIKE '%bundle%' OR SHOW TABLES LIKE '%combo%' OR SHOW TABLES LIKE '%related%'";
    $tablesResult = $mysqli->query("SHOW TABLES");
    
    $bundleTables = [];
    if ($tablesResult) {
        while ($table = $tablesResult->fetch_array()) {
            $tableName = $table[0];
            if (stripos($tableName, 'bundle') !== false || 
                stripos($tableName, 'combo') !== false || 
                stripos($tableName, 'related') !== false ||
                stripos($tableName, 'recommendation') !== false) {
                $bundleTables[] = $tableName;
            }
        }
    }
    
    if (!empty($bundleTables)) {
        echo "<p style='color: orange;'>⚠️ Found potential bundling tables:</p>\n";
        echo "<ul>\n";
        foreach ($bundleTables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: green;'>✓ No bundling tables found</p>\n";
    }
    
    // Step 4: Check recent order patterns to see if this is systematic
    echo "<h3>Step 4: Order Pattern Analysis</h3>\n";
    
    $patternQuery = "
        SELECT 
            om.OrderId,
            om.OrderDate,
            GROUP_CONCAT(od.ProductId ORDER BY od.ProductId) as ProductIds,
            GROUP_CONCAT(od.ProductCode ORDER BY od.ProductId) as ProductCodes,
            COUNT(od.ProductId) as ProductCount
        FROM order_master om
        JOIN order_details od ON om.OrderId = od.OrderId
        WHERE om.OrderDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY om.OrderId, om.OrderDate
        HAVING ProductCount > 1
        ORDER BY om.OrderDate DESC
        LIMIT 10
    ";
    
    $patternResult = $mysqli->query($patternQuery);
    
    if ($patternResult && $patternResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Order ID</th><th>Order Date</th><th>Product Count</th><th>Product IDs</th><th>Product Codes</th></tr>\n";
        
        while ($pattern = $patternResult->fetch_assoc()) {
            $hasPhantom = (strpos($pattern['ProductIds'], '12') !== false || strpos($pattern['ProductIds'], '15') !== false);
            $bgColor = $hasPhantom ? 'background-color: #ffeeee;' : '';
            
            echo "<tr style='$bgColor'>";
            echo "<td>" . htmlspecialchars($pattern['OrderId']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['OrderDate']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['ProductCount']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['ProductIds']) . "</td>";
            echo "<td>" . htmlspecialchars($pattern['ProductCodes']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No recent multi-product orders found</p>\n";
    }
    
    // Step 5: Check if there's any session contamination
    echo "<h3>Step 5: Session Analysis</h3>\n";
    
    session_start();
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo "<p style='color: orange;'>⚠️ Current session cart contains:</p>\n";
        echo "<ul>\n";
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            echo "<li>Product ID: $productId, Quantity: $quantity</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: green;'>✓ Session cart is empty</p>\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h3>🎯 Real Root Cause Analysis</h3>\n";
echo "<div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<p><strong>Key Findings:</strong></p>\n";
echo "<ul>\n";
echo "<li>🔍 <strong>Product ID 15:</strong> Apple Cider Vinegar (MN-AC100) - Legitimate product</li>\n";
echo "<li>🔍 <strong>Product ID 12:</strong> Another legitimate product (likely MN-SC100)</li>\n";
echo "<li>🔍 <strong>These are NOT phantom products</strong> - they exist in the database</li>\n";
echo "<li>🔍 <strong>The issue:</strong> They are being automatically added to orders somehow</li>\n";
echo "</ul>\n";
echo "<p><strong>Possible Causes:</strong></p>\n";
echo "<ul>\n";
echo "<li>🔍 <strong>Cart Session Contamination:</strong> Old products stuck in session</li>\n";
echo "<li>🔍 <strong>Database Cart Sync:</strong> Old cart records being loaded</li>\n";
echo "<li>🔍 <strong>Checkout Page Logic:</strong> Hidden products being processed</li>\n";
echo "<li>🔍 <strong>Automatic Bundling:</strong> Products being auto-added as recommendations</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<h3>🛠️ Recommended Solution</h3>\n";
echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<p style='color: #155724;'><strong>Since these are legitimate products being auto-added:</strong></p>\n";
echo "<ol style='color: #155724;'>\n";
echo "<li><strong>Clear all cart sessions and database cart records</strong></li>\n";
echo "<li><strong>Add validation to prevent unwanted auto-additions</strong></li>\n";
echo "<li><strong>Implement strict cart management</strong></li>\n";
echo "<li><strong>Add logging to track when products are added to cart</strong></li>\n";
echo "</ol>\n";
echo "</div>\n";
?>

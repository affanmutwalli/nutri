<?php
// Verify Multiple Categories Fix
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>✅ Verify Multiple Categories Fix</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .info-box { background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .test-box { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .category-link { display: inline-block; margin: 5px; padding: 8px 12px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
    .category-link:hover { background: #0056b3; color: white; text-decoration: none; }
</style>";

try {
    echo "<div class='success-box'>";
    echo "<h3>🔧 What Was Fixed</h3>";
    echo "<ul>";
    echo "<li>✅ <strong>Updated Query:</strong> Now uses LEFT JOIN instead of INNER JOIN</li>";
    echo "<li>✅ <strong>Dual Source:</strong> Finds products from both junction table AND legacy field</li>";
    echo "<li>✅ <strong>Backward Compatible:</strong> Works with existing products</li>";
    echo "<li>✅ <strong>Multiple Categories:</strong> Products appear in all assigned categories</li>";
    echo "</ul>";
    echo "</div>";

    // Test 1: Show products with multiple category assignments
    echo "<div class='info-box'>";
    echo "<h3>📊 Products with Multiple Category Assignments</h3>";
    
    $multiQuery = "SELECT ps.ProductId, pm.ProductName, 
                          GROUP_CONCAT(sc.SubCategoryName SEPARATOR ', ') as Categories,
                          COUNT(ps.SubCategoryId) as CategoryCount
                   FROM product_subcategories ps
                   JOIN product_master pm ON ps.ProductId = pm.ProductId
                   JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                   GROUP BY ps.ProductId, pm.ProductName
                   HAVING CategoryCount > 1
                   ORDER BY CategoryCount DESC";
    
    $multiResult = mysqli_query($mysqli, $multiQuery);
    
    if (mysqli_num_rows($multiResult) > 0) {
        echo "<table>";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Categories</th><th>Count</th></tr>";
        while ($row = mysqli_fetch_assoc($multiResult)) {
            echo "<tr>";
            echo "<td>{$row['ProductId']}</td>";
            echo "<td>{$row['ProductName']}</td>";
            echo "<td>{$row['Categories']}</td>";
            echo "<td><strong>{$row['CategoryCount']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No products found with multiple category assignments. Try assigning a product to multiple categories in the CMS first.</p>";
    }
    echo "</div>";

    // Test 2: Quick category browser
    echo "<div class='test-box'>";
    echo "<h3>🔍 Quick Category Browser - Test Multiple Categories</h3>";
    echo "<p>Click on any category below to see products in that category:</p>";
    
    $catQuery = "SELECT SubCategoryId, SubCategoryName FROM sub_category ORDER BY SubCategoryName";
    $catResult = mysqli_query($mysqli, $catQuery);
    
    while ($row = mysqli_fetch_assoc($catResult)) {
        echo "<a href='products.php?SubCategoryId={$row['SubCategoryId']}' class='category-link' target='_blank'>";
        echo htmlspecialchars($row['SubCategoryName']);
        echo "</a>";
    }
    echo "</div>";

    // Test 3: Simulate the exact products.php query
    echo "<div class='info-box'>";
    echo "<h3>🧪 Test the Fixed Query</h3>";
    
    // Get a few categories to test
    $testCats = array();
    mysqli_data_seek($catResult, 0);
    $count = 0;
    while ($row = mysqli_fetch_assoc($catResult) && $count < 3) {
        $testCats[] = $row;
        $count++;
    }
    
    foreach ($testCats as $cat) {
        echo "<h4>Testing: {$cat['SubCategoryName']} (ID: {$cat['SubCategoryId']})</h4>";
        
        // This is the exact query from products.php
        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
        $prefixedFields = array();
        foreach ($FieldNames as $field) {
            $prefixedFields[] = "pm." . $field;
        }
        $Fields = implode(",", $prefixedFields);
        
        $testQuery = "SELECT DISTINCT " . $Fields . "
                     FROM product_master pm
                     LEFT JOIN product_subcategories ps ON pm.ProductId = ps.ProductId
                     WHERE ps.SubCategoryId = ? OR pm.SubCategoryId = ?";
        
        $all_products = $obj->MysqliSelect1(
            $testQuery,
            $FieldNames,
            "ii",
            array($cat['SubCategoryId'], $cat['SubCategoryId'])
        );
        
        if (!empty($all_products)) {
            echo "<p><strong>✅ Found " . count($all_products) . " products</strong></p>";
            echo "<table>";
            echo "<tr><th>Product ID</th><th>Product Name</th><th>Source</th></tr>";
            foreach ($all_products as $product) {
                // Check if this product is in junction table for this category
                $sourceQuery = "SELECT COUNT(*) as in_junction FROM product_subcategories WHERE ProductId = ? AND SubCategoryId = ?";
                $sourceStmt = mysqli_prepare($mysqli, $sourceQuery);
                mysqli_stmt_bind_param($sourceStmt, "ii", $product['ProductId'], $cat['SubCategoryId']);
                mysqli_stmt_execute($sourceStmt);
                $sourceResult = mysqli_stmt_get_result($sourceStmt);
                $sourceData = mysqli_fetch_assoc($sourceResult);
                $source = $sourceData['in_junction'] > 0 ? 'Junction Table' : 'Legacy Field';
                mysqli_stmt_close($sourceStmt);
                
                echo "<tr>";
                echo "<td>{$product['ProductId']}</td>";
                echo "<td>{$product['ProductName']}</td>";
                echo "<td>$source</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No products found</p>";
        }
    }
    echo "</div>";

    // Instructions
    echo "<div class='success-box'>";
    echo "<h3>🚀 How to Test the Fix</h3>";
    echo "<ol>";
    echo "<li><strong>Assign a product to multiple categories:</strong>";
    echo "<ul>";
    echo "<li>Go to <a href='cms/products.php' target='_blank'>CMS Products</a></li>";
    echo "<li>Edit any product</li>";
    echo "<li>Select 3 different sub-categories (e.g., Immunity Wellness, Diabetic Care, Digestive Wellness)</li>";
    echo "<li>Save the product</li>";
    echo "</ul></li>";
    echo "<li><strong>Test on frontend:</strong>";
    echo "<ul>";
    echo "<li>Browse to each of the 3 categories you selected</li>";
    echo "<li>The product should appear in ALL 3 categories</li>";
    echo "</ul></li>";
    echo "<li><strong>Use the category links above</strong> to quickly test different categories</li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

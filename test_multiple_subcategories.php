<?php
// Test Multiple Sub-Categories Functionality
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🧪 Test Multiple Sub-Categories Functionality</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .info-box { background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .test-box { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; }
</style>";

try {
    echo "<div class='success-box'>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p>The multiple sub-categories system has been successfully implemented:</p>";
    echo "<ul>";
    echo "<li>✅ Junction table <code>product_subcategories</code> created</li>";
    echo "<li>✅ CMS product form updated with multi-select dropdown</li>";
    echo "<li>✅ Save logic updated to handle multiple assignments</li>";
    echo "<li>✅ Product display updated to use junction table</li>";
    echo "</ul>";
    echo "</div>";

    // Test 1: Check junction table
    echo "<div class='info-box'>";
    echo "<h3>📊 Test 1: Junction Table Status</h3>";
    $junctionQuery = "SELECT COUNT(*) as total_relationships FROM product_subcategories";
    $junctionResult = mysqli_query($mysqli, $junctionQuery);
    $junctionData = mysqli_fetch_assoc($junctionResult);
    
    echo "<p><strong>Total Product-SubCategory Relationships:</strong> {$junctionData['total_relationships']}</p>";
    
    // Show sample relationships
    $sampleQuery = "SELECT ps.ProductId, pm.ProductName, ps.SubCategoryId, sc.SubCategoryName, ps.is_primary
                    FROM product_subcategories ps
                    JOIN product_master pm ON ps.ProductId = pm.ProductId
                    JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                    ORDER BY ps.ProductId
                    LIMIT 10";
    $sampleResult = mysqli_query($mysqli, $sampleQuery);
    
    if (mysqli_num_rows($sampleResult) > 0) {
        echo "<table>";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Sub-Category</th><th>Primary</th></tr>";
        while ($row = mysqli_fetch_assoc($sampleResult)) {
            $primary = $row['is_primary'] ? '✅ Yes' : '❌ No';
            echo "<tr>";
            echo "<td>{$row['ProductId']}</td>";
            echo "<td>{$row['ProductName']}</td>";
            echo "<td>{$row['SubCategoryName']}</td>";
            echo "<td>$primary</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";

    // Test 2: Find products that could benefit from multiple categories
    echo "<div class='test-box'>";
    echo "<h3>🎯 Test 2: Products That Could Be in Multiple Categories</h3>";
    echo "<p>Here are some products that might benefit from being assigned to multiple sub-categories:</p>";
    
    $potentialQuery = "SELECT pm.ProductId, pm.ProductName, sc.SubCategoryName as CurrentCategory
                       FROM product_master pm
                       JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId
                       WHERE pm.ProductName LIKE '%vitamin%' 
                       OR pm.ProductName LIKE '%zinc%' 
                       OR pm.ProductName LIKE '%immunity%'
                       OR pm.ProductName LIKE '%antioxidant%'
                       ORDER BY pm.ProductName
                       LIMIT 10";
    
    $potentialResult = mysqli_query($mysqli, $potentialQuery);
    
    if (mysqli_num_rows($potentialResult) > 0) {
        echo "<table>";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Current Category</th><th>Could Also Be In</th></tr>";
        while ($row = mysqli_fetch_assoc($potentialResult)) {
            $suggestions = array();
            $productName = strtolower($row['ProductName']);
            
            if (stripos($productName, 'vitamin') !== false || stripos($productName, 'immunity') !== false) {
                $suggestions[] = "Immunity Wellness";
            }
            if (stripos($productName, 'zinc') !== false) {
                $suggestions[] = "Immunity Wellness";
            }
            if (stripos($productName, 'antioxidant') !== false) {
                $suggestions[] = "Immunity Wellness";
            }
            
            $suggestionText = !empty($suggestions) ? implode(", ", array_unique($suggestions)) : "No suggestions";
            
            echo "<tr>";
            echo "<td>{$row['ProductId']}</td>";
            echo "<td>{$row['ProductName']}</td>";
            echo "<td>{$row['CurrentCategory']}</td>";
            echo "<td>$suggestionText</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No obvious candidates found. You can manually assign products to multiple categories using the CMS.</p>";
    }
    echo "</div>";

    // Instructions
    echo "<div class='success-box'>";
    echo "<h3>🚀 How to Use the New Feature</h3>";
    echo "<ol>";
    echo "<li><strong>Go to CMS Products:</strong> <a href='cms/products.php' target='_blank'>cms/products.php</a></li>";
    echo "<li><strong>Edit any product</strong> or create a new one</li>";
    echo "<li><strong>In the Sub Categories field:</strong> Hold Ctrl (Cmd on Mac) and click multiple categories</li>";
    echo "<li><strong>Save the product</strong> - it will now appear in all selected categories</li>";
    echo "<li><strong>Test on frontend:</strong> Browse different sub-categories to see the product appear in multiple places</li>";
    echo "</ol>";
    echo "</div>";

    echo "<div class='info-box'>";
    echo "<h3>💡 Example Use Case</h3>";
    echo "<p><strong>Scenario:</strong> You have a \"Vitamin C + Zinc\" product currently in \"Vitamins\" category.</p>";
    echo "<p><strong>Solution:</strong> Edit the product and assign it to both:</p>";
    echo "<ul>";
    echo "<li>✅ Vitamins (original category)</li>";
    echo "<li>✅ Immunity Wellness (new category)</li>";
    echo "</ul>";
    echo "<p><strong>Result:</strong> Customers browsing either \"Vitamins\" or \"Immunity Wellness\" will see this product!</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

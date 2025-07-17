<?php
// Test Products Query - Direct Test
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🧪 Test Products Query - Direct Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .test-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .error-box { background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; }
</style>";

// Get SubCategoryId from URL or use a default for testing
$testSubCategoryId = isset($_GET['SubCategoryId']) ? $_GET['SubCategoryId'] : null;

if (!$testSubCategoryId) {
    // Show available sub-categories for testing
    echo "<div class='test-box'>";
    echo "<h3>🔍 Select a Sub-Category to Test</h3>";
    
    $subCatQuery = "SELECT SubCategoryId, SubCategoryName FROM sub_category ORDER BY SubCategoryName";
    $subCatResult = mysqli_query($mysqli, $subCatQuery);
    
    echo "<table>";
    echo "<tr><th>Sub-Category ID</th><th>Sub-Category Name</th><th>Test Link</th></tr>";
    while ($row = mysqli_fetch_assoc($subCatResult)) {
        echo "<tr>";
        echo "<td>{$row['SubCategoryId']}</td>";
        echo "<td>{$row['SubCategoryName']}</td>";
        echo "<td><a href='?SubCategoryId={$row['SubCategoryId']}'>Test This Category</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    try {
        // Get sub-category name
        $subCatNameQuery = "SELECT SubCategoryName FROM sub_category WHERE SubCategoryId = ?";
        $stmt = mysqli_prepare($mysqli, $subCatNameQuery);
        mysqli_stmt_bind_param($stmt, "i", $testSubCategoryId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $subCatData = mysqli_fetch_assoc($result);
        $subCatName = $subCatData['SubCategoryName'];
        mysqli_stmt_close($stmt);
        
        echo "<div class='success-box'>";
        echo "<h3>🎯 Testing Sub-Category: $subCatName (ID: $testSubCategoryId)</h3>";
        echo "</div>";
        
        // Test the exact same query as products.php
        echo "<div class='test-box'>";
        echo "<h3>📋 Method 1: Using MysqliSelect1 (like products.php)</h3>";
        
        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
        $ParamArray = array($testSubCategoryId);
        
        // Build explicit field list with table prefix
        $prefixedFields = array();
        foreach ($FieldNames as $field) {
            $prefixedFields[] = "pm." . $field;
        }
        $Fields = implode(",", $prefixedFields);
        
        $query1 = "SELECT DISTINCT " . $Fields . "
                   FROM product_master pm
                   INNER JOIN product_subcategories ps ON pm.ProductId = ps.ProductId
                   WHERE ps.SubCategoryId = ?";
        
        echo "<p><strong>Query:</strong> <code>" . htmlspecialchars($query1) . "</code></p>";
        echo "<p><strong>Parameter:</strong> $testSubCategoryId</p>";
        
        $all_products = $obj->MysqliSelect1($query1, $FieldNames, "i", $ParamArray);
        
        if (!empty($all_products)) {
            echo "<p><strong>✅ Found " . count($all_products) . " products</strong></p>";
            echo "<table>";
            echo "<tr><th>Product ID</th><th>Product Name</th><th>Photo Path</th></tr>";
            foreach ($all_products as $product) {
                echo "<tr>";
                echo "<td>{$product['ProductId']}</td>";
                echo "<td>{$product['ProductName']}</td>";
                echo "<td>" . ($product['PhotoPath'] ?: 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No products found using MysqliSelect1 method</p>";
        }
        echo "</div>";
        
        // Test with direct mysqli query
        echo "<div class='test-box'>";
        echo "<h3>📋 Method 2: Direct MySQLi Query</h3>";
        
        $query2 = "SELECT DISTINCT pm.ProductId, pm.ProductName, pm.PhotoPath
                   FROM product_master pm
                   INNER JOIN product_subcategories ps ON pm.ProductId = ps.ProductId
                   WHERE ps.SubCategoryId = ?";
        
        echo "<p><strong>Query:</strong> <code>" . htmlspecialchars($query2) . "</code></p>";
        
        $stmt = mysqli_prepare($mysqli, $query2);
        mysqli_stmt_bind_param($stmt, "i", $testSubCategoryId);
        mysqli_stmt_execute($stmt);
        $directResult = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($directResult) > 0) {
            echo "<p><strong>✅ Found " . mysqli_num_rows($directResult) . " products</strong></p>";
            echo "<table>";
            echo "<tr><th>Product ID</th><th>Product Name</th><th>Photo Path</th></tr>";
            while ($row = mysqli_fetch_assoc($directResult)) {
                echo "<tr>";
                echo "<td>{$row['ProductId']}</td>";
                echo "<td>{$row['ProductName']}</td>";
                echo "<td>" . ($row['PhotoPath'] ?: 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No products found using direct query</p>";
        }
        mysqli_stmt_close($stmt);
        echo "</div>";
        
        // Show what's actually in the junction table for this sub-category
        echo "<div class='test-box'>";
        echo "<h3>📊 What's in Junction Table for This Sub-Category</h3>";
        
        $junctionQuery = "SELECT ps.ProductId, pm.ProductName, ps.is_primary
                          FROM product_subcategories ps
                          JOIN product_master pm ON ps.ProductId = pm.ProductId
                          WHERE ps.SubCategoryId = ?";
        
        $stmt = mysqli_prepare($mysqli, $junctionQuery);
        mysqli_stmt_bind_param($stmt, "i", $testSubCategoryId);
        mysqli_stmt_execute($stmt);
        $junctionResult = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($junctionResult) > 0) {
            echo "<table>";
            echo "<tr><th>Product ID</th><th>Product Name</th><th>Primary</th></tr>";
            while ($row = mysqli_fetch_assoc($junctionResult)) {
                $primary = $row['is_primary'] ? '✅ Yes' : '❌ No';
                echo "<tr>";
                echo "<td>{$row['ProductId']}</td>";
                echo "<td>{$row['ProductName']}</td>";
                echo "<td>$primary</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No products assigned to this sub-category in junction table!</p>";
        }
        mysqli_stmt_close($stmt);
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error-box'>";
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

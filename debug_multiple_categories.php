<?php
// Debug Multiple Categories Issue
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🔍 Debug Multiple Categories Issue</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .error-box { background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .info-box { background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; }
</style>";

try {
    // Step 1: Check what's in the junction table
    echo "<div class='info-box'>";
    echo "<h3>📊 Step 1: Check Junction Table Data</h3>";
    
    $junctionQuery = "SELECT ps.ProductId, pm.ProductName, ps.SubCategoryId, sc.SubCategoryName, ps.is_primary
                      FROM product_subcategories ps
                      JOIN product_master pm ON ps.ProductId = pm.ProductId
                      JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                      ORDER BY ps.ProductId, ps.is_primary DESC";
    
    $junctionResult = mysqli_query($mysqli, $junctionQuery);
    
    if (mysqli_num_rows($junctionResult) > 0) {
        echo "<table>";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Sub-Category</th><th>Primary</th></tr>";
        
        $productGroups = array();
        while ($row = mysqli_fetch_assoc($junctionResult)) {
            $productId = $row['ProductId'];
            if (!isset($productGroups[$productId])) {
                $productGroups[$productId] = array(
                    'name' => $row['ProductName'],
                    'categories' => array()
                );
            }
            $productGroups[$productId]['categories'][] = array(
                'name' => $row['SubCategoryName'],
                'id' => $row['SubCategoryId'],
                'primary' => $row['is_primary']
            );
            
            $primary = $row['is_primary'] ? '✅ Yes' : '❌ No';
            echo "<tr>";
            echo "<td>{$row['ProductId']}</td>";
            echo "<td>{$row['ProductName']}</td>";
            echo "<td>{$row['SubCategoryName']}</td>";
            echo "<td>$primary</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show products with multiple categories
        echo "<h4>Products with Multiple Categories:</h4>";
        foreach ($productGroups as $productId => $data) {
            if (count($data['categories']) > 1) {
                echo "<p><strong>{$data['name']} (ID: $productId)</strong> is in " . count($data['categories']) . " categories:</p>";
                echo "<ul>";
                foreach ($data['categories'] as $cat) {
                    $primary = $cat['primary'] ? ' (Primary)' : '';
                    echo "<li>{$cat['name']} (ID: {$cat['id']})$primary</li>";
                }
                echo "</ul>";
            }
        }
    } else {
        echo "<p>❌ No data found in junction table!</p>";
    }
    echo "</div>";

    // Step 2: Test the frontend query
    echo "<div class='info-box'>";
    echo "<h3>🔍 Step 2: Test Frontend Query</h3>";
    
    // Get a few sub-category IDs to test
    $testSubCats = array();
    $subCatQuery = "SELECT SubCategoryId, SubCategoryName FROM sub_category LIMIT 5";
    $subCatResult = mysqli_query($mysqli, $subCatQuery);
    
    while ($row = mysqli_fetch_assoc($subCatResult)) {
        $testSubCats[] = $row;
    }
    
    foreach ($testSubCats as $subCat) {
        echo "<h4>Testing Sub-Category: {$subCat['SubCategoryName']} (ID: {$subCat['SubCategoryId']})</h4>";
        
        // This is the query from products.php
        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
        $Fields = implode(",", $FieldNames);
        $testQuery = "SELECT DISTINCT pm." . $Fields . " 
                     FROM product_master pm 
                     INNER JOIN product_subcategories ps ON pm.ProductId = ps.ProductId 
                     WHERE ps.SubCategoryId = ?";
        
        echo "<p><strong>Query:</strong> <code>" . htmlspecialchars($testQuery) . "</code></p>";
        echo "<p><strong>Parameter:</strong> {$subCat['SubCategoryId']}</p>";
        
        $stmt = mysqli_prepare($mysqli, $testQuery);
        mysqli_stmt_bind_param($stmt, "i", $subCat['SubCategoryId']);
        mysqli_stmt_execute($stmt);
        $testResult = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($testResult) > 0) {
            echo "<table>";
            echo "<tr><th>Product ID</th><th>Product Name</th></tr>";
            while ($row = mysqli_fetch_assoc($testResult)) {
                echo "<tr>";
                echo "<td>{$row['ProductId']}</td>";
                echo "<td>{$row['ProductName']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No products found for this sub-category!</p>";
        }
        
        mysqli_stmt_close($stmt);
    }
    echo "</div>";

    // Step 3: Check if there's an issue with the products.php query
    echo "<div class='error-box'>";
    echo "<h3>🚨 Step 3: Potential Issues</h3>";
    echo "<p>If products are in the junction table but not showing on frontend, possible issues:</p>";
    echo "<ul>";
    echo "<li>❌ <strong>Cache issue:</strong> Browser or server caching old results</li>";
    echo "<li>❌ <strong>Query issue:</strong> The INNER JOIN might be too restrictive</li>";
    echo "<li>❌ <strong>Field mismatch:</strong> PhotoPath or other fields might be NULL</li>";
    echo "<li>❌ <strong>JavaScript filtering:</strong> Frontend JS might be filtering results</li>";
    echo "</ul>";
    echo "</div>";

    // Step 4: Test with a more permissive query
    echo "<div class='success-box'>";
    echo "<h3>🔧 Step 4: Testing Alternative Query</h3>";
    
    if (!empty($testSubCats)) {
        $subCat = $testSubCats[0]; // Test with first sub-category
        
        echo "<h4>Testing with more permissive query for: {$subCat['SubCategoryName']}</h4>";
        
        $altQuery = "SELECT pm.ProductId, pm.ProductName, pm.PhotoPath, ps.SubCategoryId
                     FROM product_master pm 
                     LEFT JOIN product_subcategories ps ON pm.ProductId = ps.ProductId 
                     WHERE ps.SubCategoryId = ? OR pm.SubCategoryId = ?";
        
        echo "<p><strong>Alternative Query:</strong> <code>" . htmlspecialchars($altQuery) . "</code></p>";
        
        $stmt = mysqli_prepare($mysqli, $altQuery);
        mysqli_stmt_bind_param($stmt, "ii", $subCat['SubCategoryId'], $subCat['SubCategoryId']);
        mysqli_stmt_execute($stmt);
        $altResult = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($altResult) > 0) {
            echo "<table>";
            echo "<tr><th>Product ID</th><th>Product Name</th><th>PhotoPath</th><th>Source</th></tr>";
            while ($row = mysqli_fetch_assoc($altResult)) {
                $source = $row['SubCategoryId'] ? 'Junction Table' : 'Legacy (product_master)';
                echo "<tr>";
                echo "<td>{$row['ProductId']}</td>";
                echo "<td>{$row['ProductName']}</td>";
                echo "<td>" . ($row['PhotoPath'] ?: 'NULL') . "</td>";
                echo "<td>$source</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No products found with alternative query either!</p>";
        }
        
        mysqli_stmt_close($stmt);
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

<?php
// Full Structure Check - Comprehensive Diagnosis
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🔍 Full Structure Check - Comprehensive Diagnosis</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .error-box { background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .info-box { background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .warning-box { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; }
    code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
</style>";

try {
    // Step 1: Check if junction table exists and has data
    echo "<div class='info-box'>";
    echo "<h3>📊 Step 1: Junction Table Check</h3>";
    
    $tableCheck = "SHOW TABLES LIKE 'product_subcategories'";
    $tableResult = mysqli_query($mysqli, $tableCheck);
    
    if (mysqli_num_rows($tableResult) > 0) {
        echo "<p>✅ <code>product_subcategories</code> table exists</p>";
        
        // Check table structure
        $structureQuery = "DESCRIBE product_subcategories";
        $structureResult = mysqli_query($mysqli, $structureQuery);
        
        echo "<h4>Table Structure:</h4>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = mysqli_fetch_assoc($structureResult)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>" . ($row['Default'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check data count
        $countQuery = "SELECT COUNT(*) as total FROM product_subcategories";
        $countResult = mysqli_query($mysqli, $countQuery);
        $countData = mysqli_fetch_assoc($countResult);
        
        echo "<p><strong>Total Records:</strong> {$countData['total']}</p>";
        
        if ($countData['total'] > 0) {
            // Show sample data
            $sampleQuery = "SELECT ps.*, pm.ProductName, sc.SubCategoryName 
                           FROM product_subcategories ps
                           LEFT JOIN product_master pm ON ps.ProductId = pm.ProductId
                           LEFT JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                           LIMIT 10";
            $sampleResult = mysqli_query($mysqli, $sampleQuery);
            
            echo "<h4>Sample Data:</h4>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Product ID</th><th>Product Name</th><th>SubCategory ID</th><th>SubCategory Name</th><th>Primary</th></tr>";
            while ($row = mysqli_fetch_assoc($sampleResult)) {
                $primary = $row['is_primary'] ? '✅' : '❌';
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['ProductId']}</td>";
                echo "<td>" . ($row['ProductName'] ?: 'NULL') . "</td>";
                echo "<td>{$row['SubCategoryId']}</td>";
                echo "<td>" . ($row['SubCategoryName'] ?: 'NULL') . "</td>";
                echo "<td>$primary</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='error-box'>";
            echo "<p>❌ <strong>Junction table is empty!</strong> This is the problem.</p>";
            echo "</div>";
        }
    } else {
        echo "<div class='error-box'>";
        echo "<p>❌ <code>product_subcategories</code> table does not exist!</p>";
        echo "</div>";
    }
    echo "</div>";

    // Step 2: Check if products are being saved to junction table
    echo "<div class='info-box'>";
    echo "<h3>💾 Step 2: Recent Save Activity Check</h3>";
    
    if ($countData['total'] > 0) {
        $recentQuery = "SELECT ps.*, pm.ProductName, sc.SubCategoryName, ps.created_at
                       FROM product_subcategories ps
                       LEFT JOIN product_master pm ON ps.ProductId = pm.ProductId
                       LEFT JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                       ORDER BY ps.created_at DESC
                       LIMIT 5";
        $recentResult = mysqli_query($mysqli, $recentQuery);
        
        echo "<h4>Most Recent Assignments:</h4>";
        echo "<table>";
        echo "<tr><th>Product Name</th><th>SubCategory</th><th>Primary</th><th>Created At</th></tr>";
        while ($row = mysqli_fetch_assoc($recentResult)) {
            $primary = $row['is_primary'] ? '✅' : '❌';
            echo "<tr>";
            echo "<td>" . ($row['ProductName'] ?: 'NULL') . "</td>";
            echo "<td>" . ($row['SubCategoryName'] ?: 'NULL') . "</td>";
            echo "<td>$primary</td>";
            echo "<td>" . ($row['created_at'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";

    // Step 3: Check the save logic
    echo "<div class='warning-box'>";
    echo "<h3>🔧 Step 3: Save Logic Check</h3>";
    echo "<p>Let's check if the save logic in <code>exe_save_products.php</code> is working:</p>";
    
    // Check if the save file has our modifications
    $saveFile = 'cms/exe_save_products.php';
    if (file_exists($saveFile)) {
        $saveContent = file_get_contents($saveFile);
        
        if (strpos($saveContent, 'product_subcategories') !== false) {
            echo "<p>✅ Save file contains junction table logic</p>";
        } else {
            echo "<p>❌ Save file does NOT contain junction table logic</p>";
        }
        
        if (strpos($saveContent, 'SubCategoryId[]') !== false || strpos($saveContent, 'subCategoryIds') !== false) {
            echo "<p>✅ Save file handles multiple sub-categories</p>";
        } else {
            echo "<p>❌ Save file does NOT handle multiple sub-categories</p>";
        }
    } else {
        echo "<p>❌ Save file not found</p>";
    }
    echo "</div>";

    // Step 4: Test a manual insert
    echo "<div class='warning-box'>";
    echo "<h3>🧪 Step 4: Manual Test Insert</h3>";
    echo "<p>Let's try manually inserting a test record:</p>";
    
    // Get a product and subcategory for testing
    $testProductQuery = "SELECT ProductId, ProductName FROM product_master LIMIT 1";
    $testProductResult = mysqli_query($mysqli, $testProductQuery);
    $testProduct = mysqli_fetch_assoc($testProductResult);
    
    $testSubCatQuery = "SELECT SubCategoryId, SubCategoryName FROM sub_category LIMIT 1";
    $testSubCatResult = mysqli_query($mysqli, $testSubCatQuery);
    $testSubCat = mysqli_fetch_assoc($testSubCatResult);
    
    if ($testProduct && $testSubCat) {
        echo "<p>Testing with Product: <strong>{$testProduct['ProductName']}</strong> (ID: {$testProduct['ProductId']})</p>";
        echo "<p>Testing with SubCategory: <strong>{$testSubCat['SubCategoryName']}</strong> (ID: {$testSubCat['SubCategoryId']})</p>";
        
        // Check if this combination already exists
        $existsQuery = "SELECT COUNT(*) as exists_count FROM product_subcategories WHERE ProductId = ? AND SubCategoryId = ?";
        $existsStmt = mysqli_prepare($mysqli, $existsQuery);
        mysqli_stmt_bind_param($existsStmt, "ii", $testProduct['ProductId'], $testSubCat['SubCategoryId']);
        mysqli_stmt_execute($existsStmt);
        $existsResult = mysqli_stmt_get_result($existsStmt);
        $existsData = mysqli_fetch_assoc($existsResult);
        mysqli_stmt_close($existsStmt);
        
        if ($existsData['exists_count'] == 0) {
            // Try to insert
            $insertQuery = "INSERT INTO product_subcategories (ProductId, SubCategoryId, is_primary) VALUES (?, ?, 1)";
            $insertStmt = mysqli_prepare($mysqli, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, "ii", $testProduct['ProductId'], $testSubCat['SubCategoryId']);
            
            if (mysqli_stmt_execute($insertStmt)) {
                echo "<p>✅ Manual insert successful!</p>";
            } else {
                echo "<p>❌ Manual insert failed: " . mysqli_error($mysqli) . "</p>";
            }
            mysqli_stmt_close($insertStmt);
        } else {
            echo "<p>ℹ️ Test combination already exists</p>";
        }
    }
    echo "</div>";

    // Step 5: Check the frontend query
    echo "<div class='info-box'>";
    echo "<h3>🔍 Step 5: Frontend Query Test</h3>";
    
    if ($testSubCat) {
        echo "<p>Testing frontend query for SubCategory: <strong>{$testSubCat['SubCategoryName']}</strong></p>";
        
        // Test the exact query from products.php
        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
        $prefixedFields = array();
        foreach ($FieldNames as $field) {
            $prefixedFields[] = "pm." . $field;
        }
        $Fields = implode(",", $prefixedFields);
        
        $frontendQuery = "SELECT DISTINCT " . $Fields . "
                         FROM product_master pm
                         LEFT JOIN product_subcategories ps ON pm.ProductId = ps.ProductId
                         WHERE ps.SubCategoryId = ? OR pm.SubCategoryId = ?";
        
        echo "<p><strong>Query:</strong> <code>" . htmlspecialchars($frontendQuery) . "</code></p>";
        
        $stmt = mysqli_prepare($mysqli, $frontendQuery);
        mysqli_stmt_bind_param($stmt, "ii", $testSubCat['SubCategoryId'], $testSubCat['SubCategoryId']);
        mysqli_stmt_execute($stmt);
        $frontendResult = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($frontendResult) > 0) {
            echo "<p>✅ Frontend query found " . mysqli_num_rows($frontendResult) . " products</p>";
            echo "<table>";
            echo "<tr><th>Product ID</th><th>Product Name</th></tr>";
            while ($row = mysqli_fetch_assoc($frontendResult)) {
                echo "<tr>";
                echo "<td>{$row['ProductId']}</td>";
                echo "<td>{$row['ProductName']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ Frontend query found NO products</p>";
        }
        mysqli_stmt_close($stmt);
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='error-box'>";
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

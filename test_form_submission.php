<?php
// Test Form Submission
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🧪 Test Form Submission</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .form-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
    .result-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .error-box { background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; }
    select { width: 100%; height: 150px; padding: 10px; }
    button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #0056b3; }
</style>";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<div class='result-box'>";
    echo "<h3>📋 Form Data Received</h3>";
    echo "<pre>" . htmlspecialchars(print_r($_POST, true)) . "</pre>";
    
    if (isset($_POST['SubCategoryId'])) {
        echo "<p><strong>SubCategoryId Type:</strong> " . gettype($_POST['SubCategoryId']) . "</p>";
        if (is_array($_POST['SubCategoryId'])) {
            echo "<p><strong>✅ Received as array with " . count($_POST['SubCategoryId']) . " items</strong></p>";
            
            // Test inserting into junction table
            if (!empty($_POST['ProductId']) && !empty($_POST['SubCategoryId'])) {
                echo "<h4>🧪 Testing Junction Table Insert</h4>";
                
                $productId = $_POST['ProductId'];
                $subCategoryIds = $_POST['SubCategoryId'];
                
                // First delete existing
                $deleteQuery = "DELETE FROM product_subcategories WHERE ProductId = ?";
                $deleteStmt = mysqli_prepare($mysqli, $deleteQuery);
                mysqli_stmt_bind_param($deleteStmt, "i", $productId);
                mysqli_stmt_execute($deleteStmt);
                mysqli_stmt_close($deleteStmt);
                
                // Then insert new
                $insertCount = 0;
                foreach ($subCategoryIds as $index => $subCatId) {
                    if (!empty($subCatId)) {
                        $isPrimary = ($index === 0) ? 1 : 0;
                        $insertQuery = "INSERT INTO product_subcategories (ProductId, SubCategoryId, is_primary) VALUES (?, ?, ?)";
                        $insertStmt = mysqli_prepare($mysqli, $insertQuery);
                        mysqli_stmt_bind_param($insertStmt, "iii", $productId, $subCatId, $isPrimary);
                        
                        if (mysqli_stmt_execute($insertStmt)) {
                            $insertCount++;
                            echo "<p>✅ Inserted: Product $productId → SubCategory $subCatId (Primary: " . ($isPrimary ? 'Yes' : 'No') . ")</p>";
                        } else {
                            echo "<p>❌ Failed to insert: " . mysqli_error($mysqli) . "</p>";
                        }
                        mysqli_stmt_close($insertStmt);
                    }
                }
                
                echo "<p><strong>✅ Successfully inserted $insertCount relationships</strong></p>";
                
                // Verify the inserts
                $verifyQuery = "SELECT ps.SubCategoryId, sc.SubCategoryName, ps.is_primary
                               FROM product_subcategories ps
                               JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                               WHERE ps.ProductId = ?";
                $verifyStmt = mysqli_prepare($mysqli, $verifyQuery);
                mysqli_stmt_bind_param($verifyStmt, "i", $productId);
                mysqli_stmt_execute($verifyStmt);
                $verifyResult = mysqli_stmt_get_result($verifyStmt);
                
                echo "<h4>📊 Verification - Current Assignments:</h4>";
                echo "<ul>";
                while ($row = mysqli_fetch_assoc($verifyResult)) {
                    $primary = $row['is_primary'] ? ' (Primary)' : '';
                    echo "<li>{$row['SubCategoryName']} (ID: {$row['SubCategoryId']})$primary</li>";
                }
                echo "</ul>";
                mysqli_stmt_close($verifyStmt);
            }
        } else {
            echo "<p><strong>❌ Received as single value, not array</strong></p>";
        }
    } else {
        echo "<p><strong>❌ No SubCategoryId received</strong></p>";
    }
    echo "</div>";
}

// Get data for form
$productQuery = "SELECT ProductId, ProductName FROM product_master LIMIT 5";
$productResult = mysqli_query($mysqli, $productQuery);

$subCatQuery = "SELECT SubCategoryId, SubCategoryName FROM sub_category ORDER BY SubCategoryName";
$subCatResult = mysqli_query($mysqli, $subCatQuery);

echo "<div class='form-box'>";
echo "<h3>🔧 Test Multiple Sub-Category Selection</h3>";
echo "<form method='POST'>";

echo "<label for='ProductId'>Select a Product to Test:</label><br>";
echo "<select name='ProductId' required>";
echo "<option value=''>Select Product</option>";
while ($row = mysqli_fetch_assoc($productResult)) {
    echo "<option value='{$row['ProductId']}'>{$row['ProductName']} (ID: {$row['ProductId']})</option>";
}
echo "</select><br><br>";

echo "<label for='SubCategoryId'>Select Multiple Sub-Categories:</label><br>";
echo "<select name='SubCategoryId[]' multiple size='6' required>";
while ($row = mysqli_fetch_assoc($subCatResult)) {
    echo "<option value='{$row['SubCategoryId']}'>{$row['SubCategoryName']}</option>";
}
echo "</select><br><br>";

echo "<small>Hold Ctrl (Cmd on Mac) to select multiple categories</small><br><br>";
echo "<button type='submit'>Test Submission</button>";
echo "</form>";
echo "</div>";

echo "<div class='form-box'>";
echo "<h3>📋 Instructions</h3>";
echo "<ol>";
echo "<li>Select a product from the dropdown</li>";
echo "<li>Hold Ctrl (Cmd on Mac) and click multiple sub-categories</li>";
echo "<li>Click 'Test Submission' to see if the data is received correctly</li>";
echo "<li>If this works, the issue is in the CMS form or save logic</li>";
echo "</ol>";
echo "</div>";

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

<?php
// Verify Immunity Wellness Product Count
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🔍 Verify Immunity Wellness Product Count</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .combo-row { background-color: #fff3cd; }
    .single-row { background-color: #d4edda; }
</style>";

try {
    // Step 1: Find Immunity Wellness subcategory
    echo "<h3>Step 1: Finding Immunity Wellness Sub-Category</h3>";
    $subCatQuery = "SELECT SubCategoryId, SubCategoryName FROM sub_category WHERE SubCategoryName LIKE '%immunity%' OR SubCategoryName LIKE '%immune%'";
    $subCatResult = mysqli_query($mysqli, $subCatQuery);
    
    if ($subCatResult && mysqli_num_rows($subCatResult) > 0) {
        echo "<table>";
        echo "<tr><th>Sub-Category ID</th><th>Sub-Category Name</th></tr>";
        $immunitySubCatId = null;
        while ($row = mysqli_fetch_assoc($subCatResult)) {
            echo "<tr>";
            echo "<td>{$row['SubCategoryId']}</td>";
            echo "<td>{$row['SubCategoryName']}</td>";
            echo "</tr>";
            $immunitySubCatId = $row['SubCategoryId']; // Take the last one found
        }
        echo "</table>";
        
        if ($immunitySubCatId) {
            // Step 2: Count products in Immunity Wellness
            echo "<h3>Step 2: Products in Immunity Wellness (SubCategoryId: $immunitySubCatId)</h3>";
            
            $productQuery = "SELECT ProductId, ProductName, IsCombo FROM product_master WHERE SubCategoryId = ? ORDER BY IsCombo DESC, ProductName";
            $stmt = mysqli_prepare($mysqli, $productQuery);
            mysqli_stmt_bind_param($stmt, "i", $immunitySubCatId);
            mysqli_stmt_execute($stmt);
            $productResult = mysqli_stmt_get_result($stmt);
            
            $comboCount = 0;
            $singleCount = 0;
            $totalCount = 0;
            
            echo "<table>";
            echo "<tr><th>#</th><th>Product ID</th><th>Product Name</th><th>Type</th></tr>";
            
            while ($row = mysqli_fetch_assoc($productResult)) {
                $totalCount++;
                $isCombo = ($row['IsCombo'] == 'Y');
                $type = $isCombo ? 'COMBO' : 'SINGLE';
                $rowClass = $isCombo ? 'combo-row' : 'single-row';
                
                if ($isCombo) {
                    $comboCount++;
                } else {
                    $singleCount++;
                }
                
                echo "<tr class='$rowClass'>";
                echo "<td>$totalCount</td>";
                echo "<td>{$row['ProductId']}</td>";
                echo "<td>{$row['ProductName']}</td>";
                echo "<td><strong>$type</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Summary
            echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
            echo "<h3>📊 Summary for Immunity Wellness</h3>";
            echo "<ul>";
            echo "<li><strong>Total Products:</strong> $totalCount</li>";
            echo "<li><strong>Combo Products:</strong> $comboCount</li>";
            echo "<li><strong>Single Products:</strong> $singleCount</li>";
            echo "</ul>";
            echo "</div>";
            
            // Verify the user's claim
            if ($comboCount == 15) {
                echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
                echo "<h3>✅ Verification Result</h3>";
                echo "<p><strong>CONFIRMED:</strong> There are indeed <strong>15 combo products</strong> in Immunity Wellness subcategory!</p>";
                echo "<p>You were absolutely correct. I apologize for the earlier mistake of saying 11.</p>";
                echo "</div>";
            } else {
                echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
                echo "<h3>📋 Verification Result</h3>";
                echo "<p>Found <strong>$comboCount combo products</strong> in Immunity Wellness subcategory.</p>";
                echo "</div>";
            }
        }
    } else {
        echo "<p>❌ No Immunity Wellness subcategory found!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

<?php
// Count Products by Sub-Category
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>📊 Product Count by Sub-Category</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .total-row { background-color: #e8f5e8; font-weight: bold; }
    .no-products { color: #ff6b6b; }
    .has-products { color: #51cf66; }
</style>";

try {
    // Get all sub-categories with product counts (single vs combo)
    $query = "SELECT
                sc.SubCategoryId,
                sc.SubCategoryName,
                COUNT(pm.ProductId) as TotalProducts,
                SUM(CASE WHEN pm.IsCombo = 'Y' THEN 1 ELSE 0 END) as ComboProducts,
                SUM(CASE WHEN pm.IsCombo != 'Y' OR pm.IsCombo IS NULL THEN 1 ELSE 0 END) as SingleProducts
              FROM sub_category sc
              LEFT JOIN product_master pm ON sc.SubCategoryId = pm.SubCategoryId
              GROUP BY sc.SubCategoryId, sc.SubCategoryName
              ORDER BY TotalProducts DESC, sc.SubCategoryName ASC";
    
    $result = mysqli_query($mysqli, $query);
    
    if ($result) {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Sub-Category ID</th>";
        echo "<th>Sub-Category Name</th>";
        echo "<th>Single Products</th>";
        echo "<th>Combo Products</th>";
        echo "<th>Total Products</th>";
        echo "<th>Status</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $totalProducts = 0;
        $totalSingleProducts = 0;
        $totalComboProducts = 0;
        $totalSubCategories = 0;
        $emptySubCategories = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $totalSubCategories++;
            $totalProductCount = (int)$row['TotalProducts'];
            $singleProducts = (int)$row['SingleProducts'];
            $comboProducts = (int)$row['ComboProducts'];

            $totalProducts += $totalProductCount;
            $totalSingleProducts += $singleProducts;
            $totalComboProducts += $comboProducts;

            if ($totalProductCount == 0) {
                $emptySubCategories++;
                $statusClass = "no-products";
                $status = "❌ No Products";
            } else {
                $statusClass = "has-products";
                $status = "✅ Has Products";
                if ($singleProducts > 0 && $comboProducts > 0) {
                    $status .= " (Mixed)";
                } elseif ($comboProducts > 0) {
                    $status .= " (Combo Only)";
                } else {
                    $status .= " (Single Only)";
                }
            }

            echo "<tr>";
            echo "<td>{$row['SubCategoryId']}</td>";
            echo "<td>{$row['SubCategoryName']}</td>";
            echo "<td class='$statusClass'><strong>$singleProducts</strong></td>";
            echo "<td class='$statusClass'><strong>$comboProducts</strong></td>";
            echo "<td class='$statusClass'><strong>$totalProductCount</strong></td>";
            echo "<td class='$statusClass'>$status</td>";
            echo "</tr>";
        }
        
        // Summary row
        echo "<tr class='total-row'>";
        echo "<td colspan='2'><strong>TOTAL</strong></td>";
        echo "<td><strong>$totalSingleProducts</strong></td>";
        echo "<td><strong>$totalComboProducts</strong></td>";
        echo "<td><strong>$totalProducts</strong></td>";
        echo "<td><strong>$totalSubCategories Sub-Categories</strong></td>";
        echo "</tr>";
        
        echo "</tbody>";
        echo "</table>";
        
        // Summary statistics
        echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h3>📈 Summary Statistics</h3>";
        echo "<ul>";
        echo "<li><strong>Total Sub-Categories:</strong> $totalSubCategories</li>";
        echo "<li><strong>Total Products:</strong> $totalProducts</li>";
        echo "<li><strong>Single Products:</strong> $totalSingleProducts (" . ($totalProducts > 0 ? round(($totalSingleProducts / $totalProducts) * 100, 1) : 0) . "%)</li>";
        echo "<li><strong>Combo Products:</strong> $totalComboProducts (" . ($totalProducts > 0 ? round(($totalComboProducts / $totalProducts) * 100, 1) : 0) . "%)</li>";
        echo "<li><strong>Sub-Categories with Products:</strong> " . ($totalSubCategories - $emptySubCategories) . "</li>";
        echo "<li><strong>Empty Sub-Categories:</strong> $emptySubCategories</li>";
        echo "<li><strong>Average Products per Sub-Category:</strong> " . ($totalSubCategories > 0 ? round($totalProducts / $totalSubCategories, 2) : 0) . "</li>";
        echo "</ul>";
        echo "</div>";
        
        // Show empty sub-categories if any
        if ($emptySubCategories > 0) {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
            echo "<h3>⚠️ Empty Sub-Categories ($emptySubCategories found)</h3>";
            echo "<p>These sub-categories don't have any products assigned:</p>";
            
            // Reset result pointer and show empty ones
            mysqli_data_seek($result, 0);
            echo "<ul>";
            while ($row = mysqli_fetch_assoc($result)) {
                if ((int)$row['TotalProducts'] == 0) {
                    echo "<li><strong>{$row['SubCategoryName']}</strong> (ID: {$row['SubCategoryId']})</li>";
                }
            }
            echo "</ul>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Error executing query: " . mysqli_error($mysqli) . "</p>";
    }
    
    // Additional query: Show top 5 sub-categories by product count
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🏆 Top 5 Sub-Categories by Product Count</h3>";

    $topQuery = "SELECT
                    sc.SubCategoryName,
                    COUNT(pm.ProductId) as TotalProducts,
                    SUM(CASE WHEN pm.IsCombo = 'Y' THEN 1 ELSE 0 END) as ComboProducts,
                    SUM(CASE WHEN pm.IsCombo != 'Y' OR pm.IsCombo IS NULL THEN 1 ELSE 0 END) as SingleProducts
                 FROM sub_category sc
                 LEFT JOIN product_master pm ON sc.SubCategoryId = pm.SubCategoryId
                 GROUP BY sc.SubCategoryId, sc.SubCategoryName
                 HAVING TotalProducts > 0
                 ORDER BY TotalProducts DESC
                 LIMIT 5";

    $topResult = mysqli_query($mysqli, $topQuery);

    if ($topResult && mysqli_num_rows($topResult) > 0) {
        echo "<ol>";
        while ($row = mysqli_fetch_assoc($topResult)) {
            $total = $row['TotalProducts'];
            $single = $row['SingleProducts'];
            $combo = $row['ComboProducts'];
            echo "<li><strong>{$row['SubCategoryName']}</strong> - $total products ";
            echo "<span style='color: #666;'>($single single, $combo combo)</span></li>";
        }
        echo "</ol>";
    } else {
        echo "<p>No sub-categories with products found.</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

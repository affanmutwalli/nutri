<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Debug Subcategories for Combo Products</h2>";

// Test 1: Get all combo products with their subcategory info
echo "<h3>1. All Combo Products with Subcategory Info:</h3>";
$query = "SELECT pm.ProductId, pm.ProductName, pm.SubCategoryId, sc.SubCategoryName
          FROM product_master pm 
          LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId
          WHERE pm.IsCombo = 'Y' 
          ORDER BY pm.SubCategoryId, pm.ProductId";

$products = $obj->MysqliSelect1($query, 
    array("ProductId", "ProductName", "SubCategoryId", "SubCategoryName"), "", array());

if (!empty($products)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>SubCategoryId</th><th>SubCategoryName</th></tr>";
    
    $currentSubCat = null;
    $subCatCount = 0;
    
    foreach ($products as $product) {
        if ($currentSubCat !== $product['SubCategoryId']) {
            if ($currentSubCat !== null) {
                echo "<tr style='background: #f0f0f0;'><td colspan='4'><strong>SubCategory {$currentSubCat} has {$subCatCount} products</strong></td></tr>";
            }
            $currentSubCat = $product['SubCategoryId'];
            $subCatCount = 0;
        }
        $subCatCount++;
        
        echo "<tr>";
        echo "<td>{$product['ProductId']}</td>";
        echo "<td>{$product['ProductName']}</td>";
        echo "<td>{$product['SubCategoryId']}</td>";
        echo "<td>{$product['SubCategoryName']}</td>";
        echo "</tr>";
    }
    
    if ($currentSubCat !== null) {
        echo "<tr style='background: #f0f0f0;'><td colspan='4'><strong>SubCategory {$currentSubCat} has {$subCatCount} products</strong></td></tr>";
    }
    
    echo "</table>";
    echo "<p><strong>Total combo products:</strong> " . count($products) . "</p>";
} else {
    echo "<p>No combo products found!</p>";
}

// Test 2: Get subcategory counts
echo "<h3>2. Subcategory Counts:</h3>";
$countQuery = "SELECT sc.SubCategoryId, sc.SubCategoryName, COUNT(pm.ProductId) as count 
               FROM sub_category sc
               INNER JOIN product_master pm ON sc.SubCategoryId = pm.SubCategoryId 
               WHERE pm.IsCombo = 'Y' 
               GROUP BY sc.SubCategoryId, sc.SubCategoryName 
               ORDER BY count DESC";

$subcategoryCounts = $obj->MysqliSelect1($countQuery, 
    array("SubCategoryId", "SubCategoryName", "count"), "", array());

if (!empty($subcategoryCounts)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>SubCategoryId</th><th>SubCategoryName</th><th>Count</th></tr>";
    foreach ($subcategoryCounts as $subcat) {
        echo "<tr><td>{$subcat['SubCategoryId']}</td><td>{$subcat['SubCategoryName']}</td><td>{$subcat['count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No subcategory counts found!</p>";
}

// Test 3: Check what the filter API returns
echo "<h3>3. Filter API Response:</h3>";
echo "<div id='filter-response' style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo "<p>Loading filter data...</p>";
echo "</div>";

?>

<script>
// Test the filter API
fetch('get_filter_counts.php')
.then(response => response.json())
.then(data => {
    console.log('Filter API response:', data);
    
    const responseDiv = document.getElementById('filter-response');
    
    if (data.success) {
        let html = '<h4>Filter API Response:</h4>';
        html += '<p><strong>Total Products:</strong> ' + data.filter_counts.total_products + '</p>';
        
        if (data.filter_counts.subcategories) {
            html += '<h5>Subcategories:</h5>';
            html += '<ul>';
            data.filter_counts.subcategories.forEach(subcat => {
                html += '<li>ID: ' + subcat.id + ', Name: ' + subcat.name + ', Count: ' + subcat.count + '</li>';
            });
            html += '</ul>';
        }
        
        html += '<h5>Raw Response:</h5>';
        html += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        
        responseDiv.innerHTML = html;
    } else {
        responseDiv.innerHTML = '<p style="color: red;">Error: ' + (data.error || 'Unknown error') + '</p>';
    }
})
.catch(error => {
    console.error('Error:', error);
    document.getElementById('filter-response').innerHTML = '<p style="color: red;">Network error: ' + error.message + '</p>';
});
</script>

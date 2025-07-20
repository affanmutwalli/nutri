<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Debug HTML Data Attributes</h2>";

// Use the exact same query as combos.php
$FieldNames = array("ProductId", "ProductName", "PhotoPath", "ShortDescription", "CategoryId", "SubCategoryId", "CategoryName", "SubCategoryName");
$ParamArray = array();
$query = "SELECT pm.ProductId, pm.ProductName, pm.PhotoPath, pm.ShortDescription, pm.CategoryId, pm.SubCategoryId, 
          cm.CategoryName, sc.SubCategoryName
          FROM product_master pm 
          LEFT JOIN category_master cm ON pm.CategoryId = cm.CategoryId
          LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId
          WHERE pm.IsCombo = 'Y' 
          ORDER BY pm.ProductId DESC";
$all_products = $obj->MysqliSelect1($query, $FieldNames, "", $ParamArray);

echo "<h3>Query Results:</h3>";
echo "<p><strong>Total products found:</strong> " . (count($all_products) ?? 0) . "</p>";

if (!empty($all_products)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>CategoryId</th><th>CategoryName</th><th>SubCategoryId</th><th>SubCategoryName</th></tr>";
    
    foreach ($all_products as $product) {
        echo "<tr>";
        echo "<td>{$product['ProductId']}</td>";
        echo "<td>{$product['ProductName']}</td>";
        echo "<td>" . ($product['CategoryId'] ?? 'NULL') . "</td>";
        echo "<td>" . ($product['CategoryName'] ?? 'NULL') . "</td>";
        echo "<td>" . ($product['SubCategoryId'] ?? 'NULL') . "</td>";
        echo "<td>" . ($product['SubCategoryName'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show what the HTML data attributes would look like
    echo "<h3>HTML Data Attributes Preview:</h3>";
    echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace;'>";
    
    $count = 0;
    foreach ($all_products as $product) {
        $count++;
        if ($count > 5) break; // Only show first 5
        
        echo "<div style='margin: 10px 0; padding: 10px; background: white; border-radius: 3px;'>";
        echo "<strong>Product {$count}:</strong><br>";
        echo "data-name=\"" . htmlspecialchars($product["ProductName"]) . "\"<br>";
        echo "data-category-id=\"" . ($product["CategoryId"] ?? '') . "\"<br>";
        echo "data-subcategory-id=\"" . ($product["SubCategoryId"] ?? '') . "\"<br>";
        echo "data-category-name=\"" . htmlspecialchars($product["CategoryName"] ?? '') . "\"<br>";
        echo "data-subcategory-name=\"" . htmlspecialchars($product["SubCategoryName"] ?? '') . "\"<br>";
        echo "</div>";
    }
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>No products found with the query!</p>";
}

// Test the filter counts API
echo "<h3>Filter Counts API Test:</h3>";
echo "<div id='api-test'>";
echo "<p>Loading...</p>";
echo "</div>";

?>

<script>
fetch('get_filter_counts.php')
.then(response => response.json())
.then(data => {
    console.log('Filter counts API response:', data);
    
    const apiDiv = document.getElementById('api-test');
    
    if (data.success) {
        let html = '<h4>API Response:</h4>';
        html += '<p><strong>Total Products:</strong> ' + data.filter_counts.total_products + '</p>';
        
        if (data.filter_counts.subcategories && data.filter_counts.subcategories.length > 0) {
            html += '<h5>Subcategories from API:</h5>';
            html += '<table border="1" style="border-collapse: collapse;">';
            html += '<tr><th>ID</th><th>Name</th><th>Count</th></tr>';
            data.filter_counts.subcategories.forEach(sub => {
                html += '<tr><td>' + sub.id + '</td><td>' + sub.name + '</td><td>' + sub.count + '</td></tr>';
            });
            html += '</table>';
        }
        
        if (data.filter_counts.categories && data.filter_counts.categories.length > 0) {
            html += '<h5>Categories from API:</h5>';
            html += '<table border="1" style="border-collapse: collapse;">';
            html += '<tr><th>ID</th><th>Name</th><th>Count</th></tr>';
            data.filter_counts.categories.forEach(cat => {
                html += '<tr><td>' + cat.id + '</td><td>' + cat.name + '</td><td>' + cat.count + '</td></tr>';
            });
            html += '</table>';
        }
        
    } else {
        html = '<p style="color: red;">API Error: ' + (data.error || 'Unknown error') + '</p>';
    }
    
    apiDiv.innerHTML = html;
})
.catch(error => {
    console.error('API Error:', error);
    document.getElementById('api-test').innerHTML = '<p style="color: red;">Network Error: ' + error.message + '</p>';
});
</script>

<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Categories and Subcategories for Combo Products</h2>";

// Get categories for combo products
echo "<h3>1. Categories with Combo Products:</h3>";
$categoriesQuery = "
    SELECT cm.CategoryId, cm.CategoryName, COUNT(pm.ProductId) as combo_count 
    FROM category_master cm
    INNER JOIN product_master pm ON cm.CategoryId = pm.CategoryId 
    WHERE pm.IsCombo = 'Y' 
    GROUP BY cm.CategoryId, cm.CategoryName 
    ORDER BY combo_count DESC";

$categories = $obj->MysqliSelect1($categoriesQuery, 
    array("CategoryId", "CategoryName", "combo_count"), "", array());

if (!empty($categories)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>CategoryId</th><th>CategoryName</th><th>Combo Count</th></tr>";
    foreach ($categories as $cat) {
        echo "<tr><td>{$cat['CategoryId']}</td><td>{$cat['CategoryName']}</td><td>{$cat['combo_count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No categories found with combo products.</p>";
}

// Get subcategories for combo products
echo "<h3>2. Subcategories with Combo Products:</h3>";
$subcategoriesQuery = "
    SELECT sc.SubCategoryId, sc.SubCategoryName, COUNT(pm.ProductId) as combo_count
    FROM sub_category sc
    INNER JOIN product_master pm ON sc.SubCategoryId = pm.SubCategoryId
    WHERE pm.IsCombo = 'Y'
    GROUP BY sc.SubCategoryId, sc.SubCategoryName
    ORDER BY combo_count DESC";

$subcategories = $obj->MysqliSelect1($subcategoriesQuery,
    array("SubCategoryId", "SubCategoryName", "combo_count"), "", array());

if (!empty($subcategories)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>SubCategoryId</th><th>SubCategoryName</th><th>Combo Count</th></tr>";
    foreach ($subcategories as $subcat) {
        echo "<tr><td>{$subcat['SubCategoryId']}</td><td>{$subcat['SubCategoryName']}</td><td>{$subcat['combo_count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No subcategories found with combo products.</p>";
}

// Get sample combo products with their categories
echo "<h3>3. Sample Combo Products with Categories:</h3>";
$sampleQuery = "
    SELECT pm.ProductId, pm.ProductName, cm.CategoryName, sc.SubCategoryName
    FROM product_master pm
    LEFT JOIN category_master cm ON pm.CategoryId = cm.CategoryId
    LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId
    WHERE pm.IsCombo = 'Y'
    ORDER BY pm.ProductId DESC
    LIMIT 15";

$samples = $obj->MysqliSelect1($sampleQuery,
    array("ProductId", "ProductName", "CategoryName", "SubCategoryName"), "", array());

if (!empty($samples)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>Category</th><th>SubCategory</th></tr>";
    foreach ($samples as $sample) {
        echo "<tr>";
        echo "<td>{$sample['ProductId']}</td>";
        echo "<td>{$sample['ProductName']}</td>";
        echo "<td>{$sample['CategoryName']}</td>";
        echo "<td>{$sample['SubCategoryName']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No sample products found.</p>";
}

// Check if multiple subcategories system is being used
echo "<h3>4. Multiple Subcategories Check:</h3>";
$multiSubQuery = "
    SELECT COUNT(*) as count 
    FROM product_subcategories ps
    INNER JOIN product_master pm ON ps.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y'";

$multiSubResult = $obj->MysqliSelect1($multiSubQuery, array("count"), "", array());
$multiSubCount = $multiSubResult[0]['count'] ?? 0;

if ($multiSubCount > 0) {
    echo "<p><strong>Multiple subcategories system is active:</strong> {$multiSubCount} combo product-subcategory relationships found.</p>";
    
    // Get subcategories from the junction table
    echo "<h4>Subcategories from Multiple System:</h4>";
    $junctionQuery = "
        SELECT sc.SubCategoryId, sc.SubCategoryName, COUNT(DISTINCT pm.ProductId) as combo_count
        FROM product_subcategories ps
        INNER JOIN product_master pm ON ps.ProductId = pm.ProductId
        INNER JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
        WHERE pm.IsCombo = 'Y'
        GROUP BY sc.SubCategoryId, sc.SubCategoryName
        ORDER BY combo_count DESC";
    
    $junctionSubs = $obj->MysqliSelect1($junctionQuery, 
        array("SubCategoryId", "SubCategoryName", "combo_count"), "", array());
    
    if (!empty($junctionSubs)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>SubCategoryId</th><th>SubCategoryName</th><th>Combo Count</th></tr>";
        foreach ($junctionSubs as $jsub) {
            echo "<tr><td>{$jsub['SubCategoryId']}</td><td>{$jsub['SubCategoryName']}</td><td>{$jsub['combo_count']}</td></tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p>Multiple subcategories system is not being used for combo products.</p>";
}

echo "<h3>5. Recommended Filter Structure:</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>Based on the data above, here's the recommended filter structure:</h4>";
echo "<ul>";
echo "<li><strong>Product Type:</strong> Combos (default checked)</li>";
echo "<li><strong>Category:</strong> List all categories that have combo products</li>";
echo "<li><strong>Subcategory:</strong> List all subcategories that have combo products</li>";
echo "<li><strong>Price Range:</strong> Keep the existing price slider</li>";
echo "<li><strong>Availability:</strong> Keep the existing availability filter</li>";
echo "</ul>";
echo "</div>";

?>

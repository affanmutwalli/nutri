<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Category Analysis for Filter System</h2>";

// Check categories
echo "<h3>1. Categories in category_master:</h3>";
$categories = $obj->MysqliSelect1("SELECT CategoryId, CategoryName FROM category_master ORDER BY CategoryId", 
    array("CategoryId", "CategoryName"), "", array());

if (!empty($categories)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>CategoryId</th><th>CategoryName</th></tr>";
    foreach ($categories as $cat) {
        echo "<tr><td>{$cat['CategoryId']}</td><td>{$cat['CategoryName']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No categories found.</p>";
}

// Check subcategories
echo "<h3>2. Subcategories in sub_category:</h3>";
$subcategories = $obj->MysqliSelect1("SELECT SubCategoryId, SubCategoryName FROM sub_category ORDER BY SubCategoryId",
    array("SubCategoryId", "SubCategoryName"), "", array());

if (!empty($subcategories)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>SubCategoryId</th><th>SubCategoryName</th></tr>";
    foreach ($subcategories as $subcat) {
        echo "<tr><td>{$subcat['SubCategoryId']}</td><td>{$subcat['SubCategoryName']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No subcategories found.</p>";
}

// Check combo products by category
echo "<h3>3. Combo Products by Category:</h3>";
$combosByCategory = $obj->MysqliSelect1("
    SELECT pm.CategoryId, cm.CategoryName, COUNT(*) as combo_count 
    FROM product_master pm 
    LEFT JOIN category_master cm ON pm.CategoryId = cm.CategoryId 
    WHERE pm.IsCombo = 'Y' 
    GROUP BY pm.CategoryId, cm.CategoryName 
    ORDER BY combo_count DESC", 
    array("CategoryId", "CategoryName", "combo_count"), "", array());

if (!empty($combosByCategory)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>CategoryId</th><th>CategoryName</th><th>Combo Count</th></tr>";
    foreach ($combosByCategory as $combo) {
        echo "<tr><td>{$combo['CategoryId']}</td><td>{$combo['CategoryName']}</td><td>{$combo['combo_count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No combo products found.</p>";
}

// Check product sizes for packaging filters
echo "<h3>4. Product Sizes (for Packaging Filters):</h3>";
$sizes = $obj->MysqliSelect1("
    SELECT DISTINCT pp.Size, COUNT(*) as product_count 
    FROM product_price pp 
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId 
    WHERE pm.IsCombo = 'Y' AND pp.Size IS NOT NULL AND pp.Size != ''
    GROUP BY pp.Size 
    ORDER BY product_count DESC 
    LIMIT 20", 
    array("Size", "product_count"), "", array());

if (!empty($sizes)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Size</th><th>Product Count</th></tr>";
    foreach ($sizes as $size) {
        echo "<tr><td>{$size['Size']}</td><td>{$size['product_count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No product sizes found.</p>";
}

// Check price ranges
echo "<h3>5. Price Ranges for Combo Products:</h3>";
$priceRanges = $obj->MysqliSelect1("
    SELECT 
        MIN(pp.OfferPrice) as min_price,
        MAX(pp.OfferPrice) as max_price,
        AVG(pp.OfferPrice) as avg_price,
        COUNT(DISTINCT pm.ProductId) as product_count
    FROM product_price pp 
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId 
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0", 
    array("min_price", "max_price", "avg_price", "product_count"), "", array());

if (!empty($priceRanges)) {
    $range = $priceRanges[0];
    echo "<p><strong>Min Price:</strong> ₹{$range['min_price']}</p>";
    echo "<p><strong>Max Price:</strong> ₹{$range['max_price']}</p>";
    echo "<p><strong>Average Price:</strong> ₹" . number_format($range['avg_price'], 2) . "</p>";
    echo "<p><strong>Total Combo Products with Prices:</strong> {$range['product_count']}</p>";
}

echo "<h3>6. Sample Combo Products:</h3>";
$sampleProducts = $obj->MysqliSelect1("
    SELECT pm.ProductId, pm.ProductName, pm.CategoryId, cm.CategoryName, pm.IsCombo
    FROM product_master pm 
    LEFT JOIN category_master cm ON pm.CategoryId = cm.CategoryId 
    WHERE pm.IsCombo = 'Y' 
    ORDER BY pm.ProductId DESC 
    LIMIT 10", 
    array("ProductId", "ProductName", "CategoryId", "CategoryName", "IsCombo"), "", array());

if (!empty($sampleProducts)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>CategoryId</th><th>CategoryName</th></tr>";
    foreach ($sampleProducts as $product) {
        echo "<tr><td>{$product['ProductId']}</td><td>{$product['ProductName']}</td><td>{$product['CategoryId']}</td><td>{$product['CategoryName']}</td></tr>";
    }
    echo "</table>";
}

?>

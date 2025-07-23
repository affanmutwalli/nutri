<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Combo Product Price Analysis</h2>";

// Get all combo product prices
$priceQuery = "
    SELECT 
        pm.ProductId,
        pm.ProductName,
        pm.SubCategoryId,
        sc.SubCategoryName,
        MIN(pp.OfferPrice) as min_price,
        MAX(pp.OfferPrice) as max_price,
        AVG(pp.OfferPrice) as avg_price
    FROM product_master pm 
    LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId
    LEFT JOIN product_price pp ON pm.ProductId = pp.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0
    GROUP BY pm.ProductId, pm.ProductName, pm.SubCategoryId, sc.SubCategoryName
    ORDER BY min_price ASC";

$prices = $obj->MysqliSelect1($priceQuery, 
    array("ProductId", "ProductName", "SubCategoryId", "SubCategoryName", "min_price", "max_price", "avg_price"), "", array());

if (!empty($prices)) {
    echo "<h3>Individual Product Prices:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>SubCategory</th><th>Min Price</th><th>Max Price</th><th>Avg Price</th></tr>";
    
    $overallMin = PHP_INT_MAX;
    $overallMax = 0;
    
    foreach ($prices as $price) {
        $overallMin = min($overallMin, $price['min_price']);
        $overallMax = max($overallMax, $price['max_price']);
        
        echo "<tr>";
        echo "<td>{$price['ProductId']}</td>";
        echo "<td>" . substr($price['ProductName'], 0, 50) . "...</td>";
        echo "<td>{$price['SubCategoryName']}</td>";
        echo "<td>₹{$price['min_price']}</td>";
        echo "<td>₹{$price['max_price']}</td>";
        echo "<td>₹" . number_format($price['avg_price'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Overall Price Range:</h3>";
    echo "<p><strong>Minimum Price:</strong> ₹{$overallMin}</p>";
    echo "<p><strong>Maximum Price:</strong> ₹{$overallMax}</p>";
    echo "<p><strong>Current Filter Range:</strong> ₹900 - ₹1,400</p>";
    echo "<p><strong>Recommended Filter Range:</strong> ₹{$overallMin} - ₹{$overallMax}</p>";
    
    // Check which products are outside current range
    echo "<h3>Products Outside Current Range (₹900 - ₹1,400):</h3>";
    $outsideRange = array();
    foreach ($prices as $price) {
        if ($price['min_price'] < 900 || $price['max_price'] > 1400) {
            $outsideRange[] = $price;
        }
    }
    
    if (!empty($outsideRange)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ProductId</th><th>ProductName</th><th>SubCategory</th><th>Price Range</th><th>Issue</th></tr>";
        foreach ($outsideRange as $price) {
            $issue = array();
            if ($price['min_price'] < 900) $issue[] = "Below minimum (₹{$price['min_price']})";
            if ($price['max_price'] > 1400) $issue[] = "Above maximum (₹{$price['max_price']})";
            
            echo "<tr>";
            echo "<td>{$price['ProductId']}</td>";
            echo "<td>" . substr($price['ProductName'], 0, 40) . "...</td>";
            echo "<td>{$price['SubCategoryName']}</td>";
            echo "<td>₹{$price['min_price']} - ₹{$price['max_price']}</td>";
            echo "<td>" . implode(", ", $issue) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>All products are within the current range.</p>";
    }
    
} else {
    echo "<p>No combo products with prices found.</p>";
}

// Check subcategory distribution by price
echo "<h3>Price Distribution by Subcategory:</h3>";
$subcatPrices = $obj->MysqliSelect1("
    SELECT 
        sc.SubCategoryName,
        COUNT(DISTINCT pm.ProductId) as product_count,
        MIN(pp.OfferPrice) as min_price,
        MAX(pp.OfferPrice) as max_price,
        AVG(pp.OfferPrice) as avg_price
    FROM product_master pm 
    LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId
    LEFT JOIN product_price pp ON pm.ProductId = pp.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0
    GROUP BY sc.SubCategoryId, sc.SubCategoryName
    ORDER BY avg_price DESC", 
    array("SubCategoryName", "product_count", "min_price", "max_price", "avg_price"), "", array());

if (!empty($subcatPrices)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Subcategory</th><th>Product Count</th><th>Min Price</th><th>Max Price</th><th>Avg Price</th></tr>";
    foreach ($subcatPrices as $subcat) {
        echo "<tr>";
        echo "<td>{$subcat['SubCategoryName']}</td>";
        echo "<td>{$subcat['product_count']}</td>";
        echo "<td>₹{$subcat['min_price']}</td>";
        echo "<td>₹{$subcat['max_price']}</td>";
        echo "<td>₹" . number_format($subcat['avg_price'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>

<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Test Price Range Query</h2>";

// Test the exact query from get_filter_counts.php
$priceRange = $obj->MysqliSelect1("
    SELECT
        MIN(pp.OfferPrice) as min_price,
        MAX(pp.OfferPrice) as max_price
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0",
    array("min_price", "max_price"), "", array());

echo "<h3>Raw Query Result:</h3>";
echo "<pre>";
print_r($priceRange);
echo "</pre>";

if (!empty($priceRange)) {
    $minPrice = $priceRange[0]['min_price'] ?? 0;
    $maxPrice = $priceRange[0]['max_price'] ?? 2000;
    
    echo "<h3>Extracted Values:</h3>";
    echo "<p><strong>Min Price:</strong> {$minPrice}</p>";
    echo "<p><strong>Max Price:</strong> {$maxPrice}</p>";
    
    // Apply the same logic as get_filter_counts.php
    echo "<h3>After Processing Logic:</h3>";
    
    // Ensure min is actually less than max
    if ($minPrice > $maxPrice) {
        $temp = $minPrice;
        $minPrice = $maxPrice;
        $maxPrice = $temp;
        echo "<p>⚠️ Swapped min/max values</p>";
    }

    // Set reasonable defaults if no prices found
    if ($minPrice == 0 && $maxPrice == 0) {
        $minPrice = 0;
        $maxPrice = 2000;
        echo "<p>⚠️ Applied default values (0-2000)</p>";
    }

    // Make the price range more inclusive by adding some buffer
    // Round down the minimum to nearest 50 and round up the maximum to nearest 100
    $originalMin = $minPrice;
    $originalMax = $maxPrice;
    $minPrice = floor($minPrice / 50) * 50;
    $maxPrice = ceil($maxPrice / 100) * 100;
    
    echo "<p><strong>Before rounding:</strong> {$originalMin} - {$originalMax}</p>";
    echo "<p><strong>After rounding:</strong> {$minPrice} - {$maxPrice}</p>";

    // Ensure minimum range of at least 500
    if (($maxPrice - $minPrice) < 500) {
        $oldMax = $maxPrice;
        $maxPrice = $minPrice + 500;
        echo "<p>⚠️ Adjusted max from {$oldMax} to {$maxPrice} to ensure minimum range of 500</p>";
    }
    
    echo "<h3>Final Result:</h3>";
    echo "<p><strong>Min:</strong> {$minPrice}</p>";
    echo "<p><strong>Max:</strong> {$maxPrice}</p>";
    
    // Test if this matches what get_filter_counts.php should return
    $expectedResult = array(
        'min' => intval($minPrice),
        'max' => intval($maxPrice)
    );
    
    echo "<h3>Expected API Response:</h3>";
    echo "<pre>";
    print_r($expectedResult);
    echo "</pre>";
    
} else {
    echo "<p>❌ No price data found!</p>";
}

// Also test a simpler query to see if there are any combo products with prices
echo "<h3>Combo Products with Prices:</h3>";
$comboCount = $obj->MysqliSelect1("
    SELECT COUNT(*) as count
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0",
    array("count"), "", array());

echo "<p><strong>Combo products with prices:</strong> " . ($comboCount[0]['count'] ?? 0) . "</p>";

// Test individual prices
echo "<h3>Sample Combo Product Prices:</h3>";
$samplePrices = $obj->MysqliSelect1("
    SELECT pm.ProductId, pm.ProductName, pp.OfferPrice
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0
    ORDER BY pp.OfferPrice ASC
    LIMIT 5",
    array("ProductId", "ProductName", "OfferPrice"), "", array());

if (!empty($samplePrices)) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>OfferPrice</th></tr>";
    foreach ($samplePrices as $price) {
        echo "<tr>";
        echo "<td>{$price['ProductId']}</td>";
        echo "<td>" . substr($price['ProductName'], 0, 40) . "...</td>";
        echo "<td>₹{$price['OfferPrice']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>

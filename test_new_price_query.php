<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Test New Price Query</h2>";

// Test the new query
$priceRange = $obj->MysqliSelect1("
    SELECT
        MIN(CASE WHEN pp.OfferPrice > 0 THEN pp.OfferPrice END) as min_price,
        MAX(CASE WHEN pp.OfferPrice > 0 THEN pp.OfferPrice END) as max_price
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y'",
    array("min_price", "max_price"), "", array());

echo "<h3>New Query Result:</h3>";
echo "<pre>";
print_r($priceRange);
echo "</pre>";

if (!empty($priceRange)) {
    $minPrice = $priceRange[0]['min_price'] ?? 0;
    $maxPrice = $priceRange[0]['max_price'] ?? 2000;
    
    echo "<h3>Extracted Values:</h3>";
    echo "<p><strong>Min Price:</strong> {$minPrice}</p>";
    echo "<p><strong>Max Price:</strong> {$maxPrice}</p>";
    
    // Apply the same processing logic
    if ($minPrice > $maxPrice) {
        $temp = $minPrice;
        $minPrice = $maxPrice;
        $maxPrice = $temp;
        echo "<p>⚠️ Swapped min/max values</p>";
    }

    if ($minPrice == 0 && $maxPrice == 0) {
        $minPrice = 0;
        $maxPrice = 2000;
        echo "<p>⚠️ Applied default values</p>";
    }

    $originalMin = $minPrice;
    $originalMax = $maxPrice;
    $minPrice = floor($minPrice / 50) * 50;
    $maxPrice = ceil($maxPrice / 100) * 100;
    
    echo "<p><strong>Before rounding:</strong> {$originalMin} - {$originalMax}</p>";
    echo "<p><strong>After rounding:</strong> {$minPrice} - {$maxPrice}</p>";

    if (($maxPrice - $minPrice) < 500) {
        $oldMax = $maxPrice;
        $maxPrice = $minPrice + 500;
        echo "<p>⚠️ Adjusted max from {$oldMax} to {$maxPrice}</p>";
    }
    
    echo "<h3>Final Result:</h3>";
    echo "<p><strong>Min:</strong> {$minPrice}</p>";
    echo "<p><strong>Max:</strong> {$maxPrice}</p>";
    
    echo "<h3>Expected API Response:</h3>";
    $expectedResult = array(
        'min' => intval($minPrice),
        'max' => intval($maxPrice)
    );
    echo "<pre>";
    print_r($expectedResult);
    echo "</pre>";
}

// Also test a simpler approach
echo "<h3>Alternative Simple Query:</h3>";
$simpleQuery = $obj->MysqliSelect1("
    SELECT pp.OfferPrice
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0
    ORDER BY pp.OfferPrice ASC",
    array("OfferPrice"), "", array());

if (!empty($simpleQuery)) {
    $prices = array_column($simpleQuery, 'OfferPrice');
    $actualMin = min($prices);
    $actualMax = max($prices);
    
    echo "<p><strong>Simple approach - Min:</strong> ₹{$actualMin}</p>";
    echo "<p><strong>Simple approach - Max:</strong> ₹{$actualMax}</p>";
    echo "<p><strong>Total valid prices:</strong> " . count($prices) . "</p>";
}

?>

<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Debug Price Query Issues</h2>";

// Test 1: Get all combo products
echo "<h3>1. All Combo Products:</h3>";
$allCombos = $obj->MysqliSelect1("
    SELECT ProductId, ProductName
    FROM product_master 
    WHERE IsCombo = 'Y'
    ORDER BY ProductId",
    array("ProductId", "ProductName"), "", array());

echo "<p><strong>Total combo products:</strong> " . count($allCombos) . "</p>";

// Test 2: Get all prices for combo products
echo "<h3>2. All Prices for Combo Products:</h3>";
$allPrices = $obj->MysqliSelect1("
    SELECT pm.ProductId, pm.ProductName, pp.OfferPrice
    FROM product_master pm
    INNER JOIN product_price pp ON pm.ProductId = pp.ProductId
    WHERE pm.IsCombo = 'Y'
    ORDER BY pp.OfferPrice ASC",
    array("ProductId", "ProductName", "OfferPrice"), "", array());

echo "<p><strong>Combo products with prices:</strong> " . count($allPrices) . "</p>";

if (!empty($allPrices)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>OfferPrice</th></tr>";
    foreach ($allPrices as $price) {
        echo "<tr>";
        echo "<td>{$price['ProductId']}</td>";
        echo "<td>" . substr($price['ProductName'], 0, 50) . "...</td>";
        echo "<td>₹{$price['OfferPrice']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Calculate actual min/max
    $prices = array_column($allPrices, 'OfferPrice');
    $actualMin = min($prices);
    $actualMax = max($prices);
    
    echo "<h3>3. Actual Min/Max from All Prices:</h3>";
    echo "<p><strong>Actual Min:</strong> ₹{$actualMin}</p>";
    echo "<p><strong>Actual Max:</strong> ₹{$actualMax}</p>";
}

// Test 3: Test the original query with different conditions
echo "<h3>4. Test Original Query Variations:</h3>";

// Original query
echo "<h4>4a. Original Query (with OfferPrice > 0):</h4>";
$query1 = $obj->MysqliSelect1("
    SELECT MIN(pp.OfferPrice) as min_price, MAX(pp.OfferPrice) as max_price
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0",
    array("min_price", "max_price"), "", array());
echo "<pre>"; print_r($query1); echo "</pre>";

// Without the OfferPrice > 0 condition
echo "<h4>4b. Without OfferPrice > 0 condition:</h4>";
$query2 = $obj->MysqliSelect1("
    SELECT MIN(pp.OfferPrice) as min_price, MAX(pp.OfferPrice) as max_price
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y'",
    array("min_price", "max_price"), "", array());
echo "<pre>"; print_r($query2); echo "</pre>";

// Test 4: Check if there are products with OfferPrice = 0
echo "<h3>5. Products with OfferPrice = 0:</h3>";
$zeroPrice = $obj->MysqliSelect1("
    SELECT COUNT(*) as count
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice = 0",
    array("count"), "", array());
echo "<p><strong>Combo products with OfferPrice = 0:</strong> " . ($zeroPrice[0]['count'] ?? 0) . "</p>";

// Test 5: Check if there are products with NULL OfferPrice
echo "<h3>6. Products with NULL OfferPrice:</h3>";
$nullPrice = $obj->MysqliSelect1("
    SELECT COUNT(*) as count
    FROM product_price pp
    INNER JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.IsCombo = 'Y' AND pp.OfferPrice IS NULL",
    array("count"), "", array());
echo "<p><strong>Combo products with NULL OfferPrice:</strong> " . ($nullPrice[0]['count'] ?? 0) . "</p>";

// Test 6: Check for multiple prices per product
echo "<h3>7. Products with Multiple Prices:</h3>";
$multiPrice = $obj->MysqliSelect1("
    SELECT pm.ProductId, pm.ProductName, COUNT(pp.OfferPrice) as price_count
    FROM product_master pm
    INNER JOIN product_price pp ON pm.ProductId = pp.ProductId
    WHERE pm.IsCombo = 'Y'
    GROUP BY pm.ProductId, pm.ProductName
    HAVING COUNT(pp.OfferPrice) > 1
    ORDER BY price_count DESC",
    array("ProductId", "ProductName", "price_count"), "", array());

if (!empty($multiPrice)) {
    echo "<p><strong>Products with multiple prices:</strong> " . count($multiPrice) . "</p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>Price Count</th></tr>";
    foreach ($multiPrice as $mp) {
        echo "<tr>";
        echo "<td>{$mp['ProductId']}</td>";
        echo "<td>" . substr($mp['ProductName'], 0, 40) . "...</td>";
        echo "<td>{$mp['price_count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No products with multiple prices found.</p>";
}

?>

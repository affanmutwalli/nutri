<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Filter Debug Test</h2>";

// Test 1: Basic combo query
echo "<h3>1. Basic Combo Products Query:</h3>";
$basicQuery = "SELECT ProductId, ProductName, IsCombo FROM product_master WHERE IsCombo = 'Y' ORDER BY ProductId DESC LIMIT 10";
$basicResults = $obj->MysqliSelect1($basicQuery, array("ProductId", "ProductName", "IsCombo"), "", array());

if (!empty($basicResults)) {
    echo "<p><strong>Found " . count($basicResults) . " combo products (showing first 10):</strong></p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>IsCombo</th></tr>";
    foreach ($basicResults as $product) {
        echo "<tr><td>{$product['ProductId']}</td><td>{$product['ProductName']}</td><td>{$product['IsCombo']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No combo products found!</p>";
}

// Test 2: Total combo count
echo "<h3>2. Total Combo Count:</h3>";
$countQuery = "SELECT COUNT(*) as total FROM product_master WHERE IsCombo = 'Y'";
$countResult = $obj->MysqliSelect1($countQuery, array("total"), "", array());
echo "<p><strong>Total combo products:</strong> " . ($countResult[0]['total'] ?? 0) . "</p>";

// Test 3: Test the filter with default parameters
echo "<h3>3. Test Filter with Default Parameters:</h3>";

$testFilters = array(
    'product_type' => array('combos'),
    'packaging' => array(),
    'size' => array(),
    'availability' => array('in-stock'),
    'price_min' => 0,
    'price_max' => 2000,
    'sort' => 'featured'
);

echo "<p><strong>Test filters:</strong> " . json_encode($testFilters) . "</p>";

// Simulate the filter logic
$baseQuery = "SELECT DISTINCT pm.ProductId, pm.ProductName, pm.PhotoPath, pm.ShortDescription, pm.CategoryId, pm.SubCategoryId, pm.IsCombo";
$fromClause = " FROM product_master pm";
$whereClause = " WHERE pm.IsCombo = 'Y'";
$params = array();
$paramTypes = "";

// Apply product type filters
if (!empty($testFilters['product_type'])) {
    $productTypes = $testFilters['product_type'];
    
    if (count($productTypes) == 1 && in_array('combos', $productTypes)) {
        // Keep the base query as is
        echo "<p>✓ Product type filter: Showing all combos (default)</p>";
    }
}

// Apply price range filter
if (isset($testFilters['price_min']) && isset($testFilters['price_max'])) {
    $priceMin = floatval($testFilters['price_min']);
    $priceMax = floatval($testFilters['price_max']);
    
    if ($priceMin > 0 || $priceMax < 5000) {
        $whereClause .= " AND EXISTS (SELECT 1 FROM product_price pp WHERE pp.ProductId = pm.ProductId AND pp.OfferPrice BETWEEN ? AND ?)";
        $params[] = $priceMin;
        $params[] = $priceMax;
        $paramTypes .= "dd";
        echo "<p>✓ Price filter applied: ₹{$priceMin} - ₹{$priceMax}</p>";
    } else {
        echo "<p>✓ Price filter: No restriction (full range)</p>";
    }
}

$orderClause = " ORDER BY pm.ProductId DESC";
$groupClause = "";

$finalQuery = $baseQuery . $fromClause . $whereClause . $groupClause . $orderClause . " LIMIT 10";

echo "<p><strong>Final Query:</strong></p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>" . htmlspecialchars($finalQuery) . "</pre>";

if (!empty($params)) {
    echo "<p><strong>Parameters:</strong> " . json_encode($params) . "</p>";
    echo "<p><strong>Parameter Types:</strong> " . $paramTypes . "</p>";
}

// Execute the test query
$FieldNames = array("ProductId", "ProductName", "PhotoPath", "ShortDescription", "CategoryId", "SubCategoryId", "IsCombo");
$testResults = $obj->MysqliSelect1($finalQuery, $FieldNames, $paramTypes, $params);

if (!empty($testResults)) {
    echo "<p><strong>Filter Results (" . count($testResults) . " products):</strong></p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>CategoryId</th><th>IsCombo</th></tr>";
    foreach ($testResults as $product) {
        echo "<tr><td>{$product['ProductId']}</td><td>{$product['ProductName']}</td><td>{$product['CategoryId']}</td><td>{$product['IsCombo']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No results from filter query!</p>";
}

// Test 4: Check if there are any products with prices
echo "<h3>4. Products with Prices:</h3>";
$priceQuery = "SELECT COUNT(DISTINCT pm.ProductId) as count FROM product_master pm INNER JOIN product_price pp ON pm.ProductId = pp.ProductId WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0";
$priceResult = $obj->MysqliSelect1($priceQuery, array("count"), "", array());
echo "<p><strong>Combo products with prices:</strong> " . ($priceResult[0]['count'] ?? 0) . "</p>";

// Test 5: Sample price data
echo "<h3>5. Sample Price Data:</h3>";
$samplePriceQuery = "SELECT pm.ProductId, pm.ProductName, pp.OfferPrice, pp.MRP, pp.Size FROM product_master pm INNER JOIN product_price pp ON pm.ProductId = pp.ProductId WHERE pm.IsCombo = 'Y' AND pp.OfferPrice > 0 ORDER BY pm.ProductId DESC LIMIT 5";
$samplePrices = $obj->MysqliSelect1($samplePriceQuery, array("ProductId", "ProductName", "OfferPrice", "MRP", "Size"), "", array());

if (!empty($samplePrices)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>OfferPrice</th><th>MRP</th><th>Size</th></tr>";
    foreach ($samplePrices as $price) {
        echo "<tr><td>{$price['ProductId']}</td><td>{$price['ProductName']}</td><td>₹{$price['OfferPrice']}</td><td>₹{$price['MRP']}</td><td>{$price['Size']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No price data found for combo products.</p>";
}

?>

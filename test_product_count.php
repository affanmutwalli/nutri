<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Test Product Count</h2>";

// Test the exact same query as combos.php
$FieldNames = array("ProductId", "ProductName", "PhotoPath", "ShortDescription");
$ParamArray = array();
$Fields = implode(",", $FieldNames);
$query = "SELECT " . $Fields . " FROM product_master where IsCombo = 'Y' ORDER BY ProductId DESC";
$all_products = $obj->MysqliSelect1($query, $FieldNames, "", $ParamArray);

echo "<h3>Query: " . htmlspecialchars($query) . "</h3>";
echo "<h3>Results:</h3>";

if (!empty($all_products)) {
    echo "<p><strong>Total products found:</strong> " . count($all_products) . "</p>";
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th></tr>";
    
    foreach ($all_products as $product) {
        echo "<tr>";
        echo "<td>{$product['ProductId']}</td>";
        echo "<td>{$product['ProductName']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No products found!</p>";
}

// Also test a simple count query
$countQuery = "SELECT COUNT(*) as total FROM product_master WHERE IsCombo = 'Y'";
$countResult = $obj->MysqliSelect1($countQuery, array("total"), "", array());
echo "<h3>Direct Count Query:</h3>";
echo "<p><strong>Total combo products in database:</strong> " . ($countResult[0]['total'] ?? 0) . "</p>";

?>

<?php
/**
 * Check pricing data for Shilajit product (ID 19)
 */

include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>Checking Shilajit Product (ID 19) Pricing...</h2>";

// Check product details
echo "<h3>Product Details:</h3>";
$product_query = "SELECT * FROM product_master WHERE ProductId = 19";
$result = $mysqli->query($product_query);
if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo "<p><strong>Product Name:</strong> " . htmlspecialchars($product['ProductName']) . "</p>";
    echo "<p><strong>Product Code:</strong> " . htmlspecialchars($product['ProductCode']) . "</p>";
} else {
    echo "<p style='color: red;'>Product ID 19 not found in product_master</p>";
}

// Check pricing data
echo "<h3>Pricing Data:</h3>";
$price_query = "SELECT * FROM product_price WHERE ProductId = 19";
$result = $mysqli->query($price_query);
if ($result && $result->num_rows > 0) {
    echo "<p>Found " . $result->num_rows . " pricing records:</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Size</th><th>OfferPrice</th><th>MRP</th><th>Coins</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Size']) . "</td>";
        echo "<td>" . htmlspecialchars($row['OfferPrice']) . "</td>";
        echo "<td>" . htmlspecialchars($row['MRP']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Coins']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No pricing data found for Product ID 19</p>";
}

// Check what the current query would return
echo "<h3>Current Query Test:</h3>";
$test_query = "SELECT MIN(OfferPrice) as min_offer_price, MIN(MRP) as min_mrp 
              FROM product_price 
              WHERE ProductId = 19 AND OfferPrice > 0 AND MRP > 0";
$result = $mysqli->query($test_query);
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p><strong>Min OfferPrice:</strong> " . ($row['min_offer_price'] ?? 'NULL') . "</p>";
    echo "<p><strong>Min MRP:</strong> " . ($row['min_mrp'] ?? 'NULL') . "</p>";
    
    if ($row['min_offer_price'] > 0 && $row['min_mrp'] > 0) {
        $savings = $row['min_mrp'] - $row['min_offer_price'];
        $discount = round(($savings / $row['min_mrp']) * 100);
        echo "<p><strong>Savings:</strong> ₹$savings</p>";
        echo "<p><strong>Discount:</strong> $discount%</p>";
        echo "<p style='color: green;'>✓ This product should appear in offers</p>";
    } else {
        echo "<p style='color: red;'>✗ This product won't appear in offers (invalid pricing)</p>";
    }
}

// Check if there's any pricing data at all
echo "<h3>All Pricing Data (any values):</h3>";
$all_price_query = "SELECT * FROM product_price WHERE ProductId = 19";
$result = $mysqli->query($all_price_query);
if ($result && $result->num_rows > 0) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "<p>No pricing records found at all</p>";
}

$mysqli->close();
?>

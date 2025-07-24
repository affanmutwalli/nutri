<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>Offers Debug Test</h2>";

// Test 1: Check if active_product_offers view exists
echo "<h3>1. Testing active_product_offers view:</h3>";
$query1 = "SELECT * FROM active_product_offers LIMIT 5";
$result1 = $mysqli->query($query1);
if ($result1) {
    echo "<p style='color: green;'>✓ View exists and query successful</p>";
    echo "<p>Found " . $result1->num_rows . " offers in view</p>";
    if ($result1->num_rows > 0) {
        echo "<pre>";
        while ($row = $result1->fetch_assoc()) {
            print_r($row);
            break; // Just show first one
        }
        echo "</pre>";
    }
} else {
    echo "<p style='color: red;'>✗ View query failed: " . $mysqli->error . "</p>";
}

// Test 2: Check product_price table
echo "<h3>2. Testing product_price table:</h3>";
$query2 = "SELECT COUNT(*) as total FROM product_price WHERE OfferPrice > 0 AND MRP > OfferPrice";
$result2 = $mysqli->query($query2);
if ($result2) {
    $row = $result2->fetch_assoc();
    echo "<p>Products with valid pricing: " . $row['total'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ product_price query failed: " . $mysqli->error . "</p>";
}

// Test 3: Check product_offers table
echo "<h3>3. Testing product_offers table:</h3>";
$query3 = "SELECT COUNT(*) as total FROM product_offers WHERE is_active = 1";
$result3 = $mysqli->query($query3);
if ($result3) {
    $row = $result3->fetch_assoc();
    echo "<p>Active offers: " . $row['total'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ product_offers query failed: " . $mysqli->error . "</p>";
}

// Test 4: Direct fallback query
echo "<h3>4. Testing fallback query:</h3>";
$fallback_query = "SELECT
    p.ProductId as product_id,
    p.ProductName,
    p.PhotoPath,
    MIN(pp.OfferPrice) as min_offer_price,
    MIN(pp.MRP) as min_mrp,
    ROUND(((MIN(pp.MRP) - MIN(pp.OfferPrice)) / MIN(pp.MRP)) * 100) as discount_percentage,
    (MIN(pp.MRP) - MIN(pp.OfferPrice)) as savings_amount,
    NULL as offer_title,
    NULL as offer_description,
    NOW() as created_date
FROM product_master p
INNER JOIN product_price pp ON p.ProductId = pp.ProductId
WHERE pp.OfferPrice > 0 AND pp.OfferPrice < pp.MRP
GROUP BY p.ProductId, p.ProductName, p.PhotoPath
HAVING discount_percentage > 0
ORDER BY discount_percentage DESC
LIMIT 5";

$result4 = $mysqli->query($fallback_query);
if ($result4) {
    echo "<p style='color: green;'>✓ Fallback query successful</p>";
    echo "<p>Found " . $result4->num_rows . " products with discounts</p>";
    if ($result4->num_rows > 0) {
        echo "<pre>";
        while ($row = $result4->fetch_assoc()) {
            print_r($row);
            break; // Just show first one
        }
        echo "</pre>";
    }
} else {
    echo "<p style='color: red;'>✗ Fallback query failed: " . $mysqli->error . "</p>";
}

// Test 5: Check specific product (ProductId 19)
echo "<h3>5. Check ProductId 19 pricing:</h3>";
$query5a = "SELECT * FROM product_price WHERE ProductId = 19";
$result5a = $mysqli->query($query5a);
if ($result5a) {
    echo "<p>Pricing records for ProductId 19: " . $result5a->num_rows . "</p>";
    if ($result5a->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Size</th><th>Offer Price</th><th>MRP</th><th>Coins</th></tr>";
        while ($row = $result5a->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Size']) . "</td>";
            echo "<td>₹" . $row['OfferPrice'] . "</td>";
            echo "<td>₹" . $row['MRP'] . "</td>";
            echo "<td>" . $row['Coins'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>✗ ProductId 19 query failed: " . $mysqli->error . "</p>";
}

// Test 6: Sample product prices from any products
echo "<h3>6. Sample product prices (any products):</h3>";
$query6 = "SELECT p.ProductId, p.ProductName, pp.Size, pp.OfferPrice, pp.MRP
           FROM product_master p
           INNER JOIN product_price pp ON p.ProductId = pp.ProductId
           WHERE pp.OfferPrice > 0
           LIMIT 10";
$result6 = $mysqli->query($query6);
if ($result6) {
    echo "<p>Found " . $result6->num_rows . " products with pricing</p>";
    if ($result6->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Product ID</th><th>Product</th><th>Size</th><th>Offer Price</th><th>MRP</th></tr>";
        while ($row = $result6->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['ProductId'] . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['ProductName'], 0, 50)) . "...</td>";
            echo "<td>" . htmlspecialchars($row['Size']) . "</td>";
            echo "<td>₹" . $row['OfferPrice'] . "</td>";
            echo "<td>₹" . $row['MRP'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>✗ Sample prices query failed: " . $mysqli->error . "</p>";
}

// Test 7: Check if product_price table exists and structure
echo "<h3>7. Check product_price table structure:</h3>";
$query7 = "DESCRIBE product_price";
$result7 = $mysqli->query($query7);
if ($result7) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result7->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ Table structure query failed: " . $mysqli->error . "</p>";
}

?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🔍 Checking ProductId 20</h2>";

// Check what ProductId 20 actually is
$query = "SELECT * FROM product_master WHERE ProductId = 20";
$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo "<h3>ProductId 20 in product_master:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    foreach ($product as $key => $value) {
        echo "<tr>";
        echo "<td><strong>$key</strong></td>";
        echo "<td>" . htmlspecialchars($value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ ProductId 20 does NOT exist in product_master!</p>";
}

// Check product_price for ProductId 20
echo "<h3>ProductId 20 in product_price:</h3>";
$priceQuery = "SELECT * FROM product_price WHERE ProductId = 20";
$priceResult = $mysqli->query($priceQuery);

if ($priceResult && $priceResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>PriceId</th><th>ProductId</th><th>Size</th><th>OfferPrice</th><th>MRP</th><th>Coins</th></tr>";
    while ($row = $priceResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['PriceId'] . "</td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['Size'] . "</td>";
        echo "<td>₹" . $row['OfferPrice'] . "</td>";
        echo "<td>₹" . $row['MRP'] . "</td>";
        echo "<td>" . $row['Coins'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No price data found for ProductId 20</p>";
}

// Check recent orders with ProductId 20
echo "<h3>Recent orders with ProductId 20:</h3>";
$orderQuery = "SELECT od.OrderId, od.ProductId, od.ProductCode, od.Quantity, od.Price, od.SubTotal FROM order_details od WHERE od.ProductId = 20 ORDER BY od.OrderId DESC LIMIT 5";
$orderResult = $mysqli->query($orderQuery);

if ($orderResult && $orderResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Price</th><th>SubTotal</th></tr>";
    while ($row = $orderResult->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['OrderId'] . "</td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "<td>₹" . $row['Price'] . "</td>";
        echo "<td>₹" . $row['SubTotal'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No orders found with ProductId 20</p>";
}

// Check if ProductId 20 is in any cart
echo "<h3>ProductId 20 in cart:</h3>";
$cartQuery = "SELECT * FROM cart WHERE ProductId = 20";
$cartResult = $mysqli->query($cartQuery);

if ($cartResult && $cartResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>CustomerId</th><th>ProductId</th><th>Quantity</th><th>Price</th></tr>";
    while ($row = $cartResult->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['CustomerId'] . "</td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "<td>₹" . $row['Price'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ ProductId 20 not found in any cart</p>";
}

echo "<h3>🔧 Actions</h3>";
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'remove_phantom_orders':
            echo "<h4>Removing ProductId 20 from order_details...</h4>";
            $deleteQuery = "DELETE FROM order_details WHERE ProductId = 20 AND ProductCode = 'MN-XX-000'";
            if ($mysqli->query($deleteQuery)) {
                $affected = $mysqli->affected_rows;
                echo "<p style='color: green;'>✅ Removed $affected phantom product entries from order_details</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to remove phantom products: " . $mysqli->error . "</p>";
            }
            break;
            
        case 'remove_phantom_cart':
            echo "<h4>Removing ProductId 20 from cart...</h4>";
            $deleteCartQuery = "DELETE FROM cart WHERE ProductId = 20";
            if ($mysqli->query($deleteCartQuery)) {
                $affected = $mysqli->affected_rows;
                echo "<p style='color: green;'>✅ Removed $affected phantom product entries from cart</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to remove phantom products from cart: " . $mysqli->error . "</p>";
            }
            break;
            
        case 'remove_phantom_price':
            echo "<h4>Removing ProductId 20 from product_price...</h4>";
            $deletePriceQuery = "DELETE FROM product_price WHERE ProductId = 20";
            if ($mysqli->query($deletePriceQuery)) {
                $affected = $mysqli->affected_rows;
                echo "<p style='color: green;'>✅ Removed $affected phantom product entries from product_price</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to remove phantom products from product_price: " . $mysqli->error . "</p>";
            }
            break;
    }
} else {
    echo "<p><a href='?action=remove_phantom_orders' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Remove from Orders</a></p>";
    echo "<p><a href='?action=remove_phantom_cart' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Remove from Cart</a></p>";
    echo "<p><a href='?action=remove_phantom_price' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Remove from Product Price</a></p>";
}
?>

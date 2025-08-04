<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🔍 Debug Phantom Product Issue</h2>";

// Step 1: Check for phantom products in product_master
echo "<h3>Step 1: Checking product_master for phantom products</h3>";
$phantomQuery = "
    SELECT pm.ProductId, pm.ProductName, pm.ProductCode
    FROM product_master pm
    WHERE pm.ProductName = 'N/A' 
    OR pm.ProductName LIKE '%N/A%'
    OR pm.ProductCode = 'MN-XX-000'
    OR pm.ProductCode LIKE '%XX-000%'
    OR pm.ProductName = ''
    OR pm.ProductName IS NULL
    OR pm.ProductCode LIKE 'MN-XX-%'
";

$result = $mysqli->query($phantomQuery);
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No phantom products found in product_master</p>";
}

// Step 2: Check for phantom products in cart table
echo "<h3>Step 2: Checking cart table for phantom products</h3>";
$cartQuery = "
    SELECT c.CustomerId, c.ProductId, c.Quantity, pm.ProductName, pm.ProductCode
    FROM cart c
    LEFT JOIN product_master pm ON c.ProductId = pm.ProductId
    WHERE pm.ProductName = 'N/A' 
    OR pm.ProductName LIKE '%N/A%'
    OR pm.ProductCode = 'MN-XX-000'
    OR pm.ProductCode LIKE '%XX-000%'
    OR pm.ProductName = ''
    OR pm.ProductName IS NULL
    OR pm.ProductCode LIKE 'MN-XX-%'
    OR pm.ProductId IS NULL
";

$cartResult = $mysqli->query($cartQuery);
if ($cartResult && $cartResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>CustomerId</th><th>ProductId</th><th>Quantity</th><th>ProductName</th><th>ProductCode</th></tr>";
    while ($row = $cartResult->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['CustomerId'] . "</td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No phantom products found in cart table</p>";
}

// Step 3: Check recent orders for phantom products
echo "<h3>Step 3: Checking recent orders for phantom products</h3>";
$recentOrdersQuery = "
    SELECT od.OrderId, od.ProductId, od.ProductCode, od.Quantity, od.Price,
           om.CreatedAt
    FROM order_details od
    JOIN order_master om ON od.OrderId = om.OrderId
    WHERE (od.ProductCode = 'MN-XX-000'
           OR od.ProductCode LIKE '%XX-000%'
           OR od.ProductCode LIKE 'MN-XX-%')
    AND om.CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY om.CreatedAt DESC
    LIMIT 20
";

$recentResult = $mysqli->query($recentOrdersQuery);
if ($recentResult && $recentResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Price</th><th>CreatedAt</th></tr>";
    while ($row = $recentResult->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['OrderId'] . "</td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "<td>₹" . $row['Price'] . "</td>";
        echo "<td>" . $row['CreatedAt'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No phantom products found in recent orders</p>";
}

// Step 4: Check for products with ProductId that might be causing issues
echo "<h3>Step 4: Checking for problematic ProductIds</h3>";
$problemQuery = "
    SELECT DISTINCT od.ProductId, COUNT(*) as order_count, 
           pm.ProductName, pm.ProductCode
    FROM order_details od
    LEFT JOIN product_master pm ON od.ProductId = pm.ProductId
    WHERE od.ProductCode = 'MN-XX-000' OR od.Quantity = 30
    GROUP BY od.ProductId
    ORDER BY order_count DESC
";

$problemResult = $mysqli->query($problemQuery);
if ($problemResult && $problemResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>Order Count</th><th>ProductName</th><th>ProductCode</th></tr>";
    while ($row = $problemResult->fetch_assoc()) {
        echo "<tr style='background-color: #ffffcc;'>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['order_count'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No problematic ProductIds found</p>";
}

// Step 5: Check product_price table for phantom products
echo "<h3>Step 5: Checking product_price table</h3>";
$priceQuery = "
    SELECT pp.ProductId, pp.Size, pp.OfferPrice, pm.ProductName, pm.ProductCode
    FROM product_price pp
    LEFT JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.ProductName = 'N/A' 
    OR pm.ProductName LIKE '%N/A%'
    OR pm.ProductCode = 'MN-XX-000'
    OR pm.ProductCode LIKE '%XX-000%'
    OR pm.ProductName = ''
    OR pm.ProductName IS NULL
    OR pm.ProductCode LIKE 'MN-XX-%'
    OR pm.ProductId IS NULL
";

$priceResult = $mysqli->query($priceQuery);
if ($priceResult && $priceResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>Size</th><th>OfferPrice</th><th>ProductName</th><th>ProductCode</th></tr>";
    while ($row = $priceResult->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['Size'] . "</td>";
        echo "<td>₹" . $row['OfferPrice'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No phantom products found in product_price table</p>";
}

echo "<h3>🔧 Actions Available</h3>";
echo "<p><a href='?action=clean_phantom_orders' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Clean Phantom Products from Recent Orders</a></p>";
echo "<p><a href='?action=clean_phantom_cart' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Clean Phantom Products from Cart</a></p>";

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'clean_phantom_orders':
            echo "<h3>🧹 Cleaning Phantom Products from Orders</h3>";
            $deleteOrdersQuery = "DELETE FROM order_details WHERE ProductCode = 'MN-XX-000' OR ProductCode LIKE '%XX-000%' OR ProductCode LIKE 'MN-XX-%'";
            if ($mysqli->query($deleteOrdersQuery)) {
                echo "<p style='color: green;'>✅ Phantom products removed from order_details</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to remove phantom products: " . $mysqli->error . "</p>";
            }
            break;
            
        case 'clean_phantom_cart':
            echo "<h3>🧹 Cleaning Phantom Products from Cart</h3>";
            $deleteCartQuery = "DELETE c FROM cart c LEFT JOIN product_master pm ON c.ProductId = pm.ProductId WHERE pm.ProductId IS NULL OR pm.ProductName = 'N/A' OR pm.ProductCode LIKE 'MN-XX-%'";
            if ($mysqli->query($deleteCartQuery)) {
                echo "<p style='color: green;'>✅ Phantom products removed from cart</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to remove phantom products from cart: " . $mysqli->error . "</p>";
            }
            break;
    }
}
?>

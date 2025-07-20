<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>👻 Phantom Product Investigation</h2>";

// Check order MN000070 details
$orderId = 'MN000070';
echo "<h3>Order $orderId Analysis:</h3>";

$orderDetails = $mysqli->query("SELECT * FROM order_details WHERE OrderId = '$orderId' ORDER BY id");
if ($orderDetails && $orderDetails->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>ProductId</th><th>ProductCode</th><th>Size</th><th>Quantity</th><th>Price</th><th>SubTotal</th></tr>";
    
    $phantomProducts = [];
    
    while ($detail = $orderDetails->fetch_assoc()) {
        $bgColor = '';
        if ($detail['ProductCode'] == 'MN-XX-000' || $detail['Price'] == 1) {
            $bgColor = 'background-color: #ffcccc;'; // Red for phantom products
            $phantomProducts[] = $detail;
        }
        
        echo "<tr style='$bgColor'>";
        echo "<td>" . $detail['id'] . "</td>";
        echo "<td>" . $detail['ProductId'] . "</td>";
        echo "<td>" . $detail['ProductCode'] . "</td>";
        echo "<td>" . $detail['Size'] . "</td>";
        echo "<td>" . $detail['Quantity'] . "</td>";
        echo "<td>₹" . $detail['Price'] . "</td>";
        echo "<td>₹" . $detail['SubTotal'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (!empty($phantomProducts)) {
        echo "<h4>🚨 Phantom Products Detected:</h4>";
        foreach ($phantomProducts as $phantom) {
            echo "<div style='background-color: #f8d7da; padding: 10px; margin: 5px 0; border: 1px solid #f5c6cb; border-radius: 5px;'>";
            echo "<strong>ProductId:</strong> " . $phantom['ProductId'] . "<br>";
            echo "<strong>ProductCode:</strong> " . $phantom['ProductCode'] . "<br>";
            echo "<strong>Price:</strong> ₹" . $phantom['Price'] . "<br>";
            echo "<strong>Size:</strong> " . $phantom['Size'] . "<br>";
            echo "</div>";
        }
    }
}

// Check if these ProductIds exist in product_master
echo "<h3>Product Master Verification:</h3>";
$productIds = $mysqli->query("SELECT DISTINCT ProductId FROM order_details WHERE OrderId = '$orderId'");
while ($row = $productIds->fetch_assoc()) {
    $productId = $row['ProductId'];
    
    $productCheck = $mysqli->query("SELECT * FROM product_master WHERE ProductId = $productId");
    
    echo "<h4>ProductId: $productId</h4>";
    if ($productCheck->num_rows > 0) {
        $product = $productCheck->fetch_assoc();
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($product as $key => $value) {
            echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<strong>❌ CRITICAL: ProductId $productId NOT FOUND in product_master table!</strong>";
        echo "</div>";
    }
    echo "<br>";
}

// Search for products with suspicious codes
echo "<h3>Suspicious Product Codes Search:</h3>";
$suspiciousCodes = ['MN-XX-000', 'N/A', 'DEFAULT', 'PLACEHOLDER'];

foreach ($suspiciousCodes as $code) {
    $suspiciousQuery = $mysqli->query("SELECT * FROM product_master WHERE ProductCode LIKE '%$code%'");
    if ($suspiciousQuery && $suspiciousQuery->num_rows > 0) {
        echo "<h4>Products with code '$code':</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th><th>IsActive</th></tr>";
        while ($suspicious = $suspiciousQuery->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $suspicious['ProductId'] . "</td>";
            echo "<td>" . htmlspecialchars($suspicious['ProductName']) . "</td>";
            echo "<td>" . $suspicious['ProductCode'] . "</td>";
            echo "<td>" . $suspicious['IsActive'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Check for products with ₹1 price
echo "<h3>₹1 Price Products:</h3>";
$oneRupeeProducts = $mysqli->query("SELECT * FROM product_price WHERE OfferPrice = 1 OR MRP = 1");
if ($oneRupeeProducts && $oneRupeeProducts->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>MRP</th><th>OfferPrice</th><th>Size</th></tr>";
    while ($oneRupee = $oneRupeeProducts->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $oneRupee['ProductId'] . "</td>";
        echo "<td>₹" . $oneRupee['MRP'] . "</td>";
        echo "<td>₹" . $oneRupee['OfferPrice'] . "</td>";
        echo "<td>" . $oneRupee['Size'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check recent orders for pattern
echo "<h3>Recent Orders Pattern Analysis:</h3>";
$recentOrders = $mysqli->query("
    SELECT om.OrderId, COUNT(od.ProductId) as ProductCount, SUM(od.SubTotal) as Total
    FROM order_master om
    LEFT JOIN order_details od ON om.OrderId = od.OrderId
    WHERE om.CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY om.OrderId
    HAVING ProductCount > 1
    ORDER BY om.CreatedAt DESC
    LIMIT 10
");

if ($recentOrders && $recentOrders->num_rows > 0) {
    echo "<h4>Recent Multi-Product Orders:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Order ID</th><th>Product Count</th><th>Total Amount</th><th>Action</th></tr>";
    
    while ($order = $recentOrders->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $order['OrderId'] . "</td>";
        echo "<td>" . $order['ProductCount'] . "</td>";
        echo "<td>₹" . $order['Total'] . "</td>";
        echo "<td><a href='order-placed.php?order_id=" . $order['OrderId'] . "'>View</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check for automatic product addition logic
echo "<h3>🔍 Potential Causes:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h4>Possible Sources of Phantom Products:</h4>";
echo "<ul>";
echo "<li><strong>Default/Placeholder Products:</strong> Products with ID that don't exist in product_master</li>";
echo "<li><strong>Automatic Bundling:</strong> System automatically adding complementary products</li>";
echo "<li><strong>Cart Session Issues:</strong> Old cart data persisting across sessions</li>";
echo "<li><strong>Database Corruption:</strong> Invalid ProductIds in order_details</li>";
echo "<li><strong>Order Processing Bug:</strong> Logic error in order placement code</li>";
echo "<li><strong>Sample/Free Products:</strong> Automatic addition of free samples</li>";
echo "</ul>";
echo "</div>";

// Quick fix options
echo "<h3>🛠️ Quick Fix Options:</h3>";
echo "<div style='margin: 10px 0;'>";

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'remove_phantom':
            $deleteQuery = "DELETE FROM order_details WHERE OrderId = '$orderId' AND (ProductCode = 'MN-XX-000' OR Price = 1)";
            if ($mysqli->query($deleteQuery)) {
                echo "<p style='color: green;'>✅ Phantom products removed from order $orderId!</p>";
                
                // Update order total
                $newTotal = $mysqli->query("SELECT SUM(SubTotal) as total FROM order_details WHERE OrderId = '$orderId'")->fetch_assoc()['total'];
                $updateQuery = "UPDATE order_master SET Amount = $newTotal WHERE OrderId = '$orderId'";
                $mysqli->query($updateQuery);
                echo "<p style='color: green;'>✅ Order total updated to ₹$newTotal</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to remove phantom products</p>";
            }
            break;
    }
    echo "<script>setTimeout(function(){ window.location.href = 'debug_phantom_product.php'; }, 3000);</script>";
}

echo "<a href='?action=remove_phantom' class='btn' style='background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; margin: 5px; border-radius: 4px;' onclick='return confirm(\"Remove phantom products from order $orderId?\")'>Remove Phantom Products</a>";

echo "</div>";

echo "<br><p><a href='order-placed.php?order_id=$orderId'>View Order</a> | <a href='debug_cart.php'>Debug Cart</a> | <a href='cms/'>Admin Panel</a></p>";
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

$orderId = 'MN000070';

echo "<h2>🔍 Debugging Order: $orderId</h2>";

// Check order master
echo "<h3>Order Master Details:</h3>";
$orderMaster = $mysqli->query("SELECT * FROM order_master WHERE OrderId = '$orderId'");
if ($orderMaster && $orderMaster->num_rows > 0) {
    $order = $orderMaster->fetch_assoc();
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    foreach ($order as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No order found with ID: $orderId</p>";
}

// Check order details
echo "<h3>Order Details (Products):</h3>";
$orderDetails = $mysqli->query("SELECT * FROM order_details WHERE OrderId = '$orderId' ORDER BY ProductId");
if ($orderDetails && $orderDetails->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>ProductId</th><th>ProductCode</th><th>Size</th><th>Quantity</th><th>Price</th><th>SubTotal</th></tr>";
    
    $totalProducts = 0;
    $totalAmount = 0;
    
    while ($detail = $orderDetails->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $detail['id'] . "</td>";
        echo "<td>" . $detail['ProductId'] . "</td>";
        echo "<td>" . $detail['ProductCode'] . "</td>";
        echo "<td>" . $detail['Size'] . "</td>";
        echo "<td>" . $detail['Quantity'] . "</td>";
        echo "<td>₹" . $detail['Price'] . "</td>";
        echo "<td>₹" . $detail['SubTotal'] . "</td>";
        echo "</tr>";
        
        $totalProducts++;
        $totalAmount += $detail['SubTotal'];
    }
    
    echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
    echo "<td colspan='4'>TOTAL</td>";
    echo "<td>$totalProducts items</td>";
    echo "<td>-</td>";
    echo "<td>₹$totalAmount</td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "<p>❌ No order details found for order: $orderId</p>";
}

// Get product names for the products in this order
echo "<h3>Product Information Analysis:</h3>";
$productQuery = "
    SELECT 
        od.ProductId, 
        od.ProductCode, 
        od.Price, 
        od.SubTotal, 
        pm.ProductName,
        pm.IsActive,
        pm.CreatedDate as ProductCreated
    FROM order_details od 
    LEFT JOIN product_master pm ON od.ProductId = pm.ProductId 
    WHERE od.OrderId = '$orderId'
    ORDER BY od.ProductId
";

$productResult = $mysqli->query($productQuery);
if ($productResult && $productResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>Product Name</th><th>Product Code</th><th>Price</th><th>SubTotal</th><th>Active</th><th>Created</th></tr>";
    
    while ($product = $productResult->fetch_assoc()) {
        $bgColor = '';
        if (empty($product['ProductName']) || $product['ProductName'] == null) {
            $bgColor = 'background-color: #ffcccc;'; // Red for missing products
        } elseif ($product['IsActive'] != 'Y') {
            $bgColor = 'background-color: #fff3cd;'; // Yellow for inactive products
        }
        
        echo "<tr style='$bgColor'>";
        echo "<td>" . $product['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars($product['ProductName'] ?? 'PRODUCT NOT FOUND') . "</td>";
        echo "<td>" . $product['ProductCode'] . "</td>";
        echo "<td>₹" . $product['Price'] . "</td>";
        echo "<td>₹" . $product['SubTotal'] . "</td>";
        echo "<td>" . ($product['IsActive'] ?? 'N/A') . "</td>";
        echo "<td>" . ($product['ProductCreated'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check for data inconsistencies
echo "<h3>🔍 Data Inconsistency Analysis:</h3>";

// Check if ProductId exists in product_master
$inconsistencies = [];
$orderDetails = $mysqli->query("SELECT DISTINCT ProductId FROM order_details WHERE OrderId = '$orderId'");
while ($detail = $orderDetails->fetch_assoc()) {
    $productId = $detail['ProductId'];
    
    $productCheck = $mysqli->query("SELECT ProductId, ProductName, IsActive FROM product_master WHERE ProductId = $productId");
    if ($productCheck->num_rows == 0) {
        $inconsistencies[] = "Product ID $productId in order but NOT found in product_master table";
    } else {
        $product = $productCheck->fetch_assoc();
        if ($product['IsActive'] != 'Y') {
            $inconsistencies[] = "Product ID $productId is INACTIVE but was ordered";
        }
    }
}

if (!empty($inconsistencies)) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h4>⚠️ Issues Found:</h4>";
    echo "<ul>";
    foreach ($inconsistencies as $issue) {
        echo "<li>$issue</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<p>✅ No data inconsistencies found</p>";
    echo "</div>";
}

// Check recent similar orders
echo "<h3>📊 Recent Orders Analysis:</h3>";
if (isset($order['CustomerId'])) {
    $customerId = $order['CustomerId'];
    
    $recentOrders = $mysqli->query("
        SELECT OrderId, OrderDate, Amount, OrderStatus, COUNT(*) as ProductCount
        FROM order_master om
        LEFT JOIN order_details od ON om.OrderId = od.OrderId
        WHERE om.CustomerId = $customerId 
        AND om.OrderId != '$orderId'
        GROUP BY om.OrderId
        ORDER BY om.CreatedAt DESC 
        LIMIT 5
    ");
    
    if ($recentOrders && $recentOrders->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Date</th><th>Amount</th><th>Status</th><th>Products</th></tr>";
        
        while ($recentOrder = $recentOrders->fetch_assoc()) {
            echo "<tr>";
            echo "<td><a href='debug_order_" . $recentOrder['OrderId'] . ".php'>" . $recentOrder['OrderId'] . "</a></td>";
            echo "<td>" . $recentOrder['OrderDate'] . "</td>";
            echo "<td>₹" . $recentOrder['Amount'] . "</td>";
            echo "<td>" . $recentOrder['OrderStatus'] . "</td>";
            echo "<td>" . $recentOrder['ProductCount'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No other orders found for this customer</p>";
    }
}

// Check product_master table for unusual entries
echo "<h3>🔍 Product Master Table Analysis:</h3>";
$suspiciousProducts = $mysqli->query("
    SELECT ProductId, ProductName, IsActive, CreatedDate 
    FROM product_master 
    WHERE ProductName LIKE '%Wild Amla%' 
    OR ProductName LIKE '%Fresh cold pressed%'
    OR LENGTH(ProductName) > 100
    ORDER BY CreatedDate DESC
    LIMIT 10
");

if ($suspiciousProducts && $suspiciousProducts->num_rows > 0) {
    echo "<h4>Suspicious/Long Product Names:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>Product Name</th><th>Active</th><th>Created</th></tr>";
    
    while ($suspicious = $suspiciousProducts->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $suspicious['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars(substr($suspicious['ProductName'], 0, 100)) . "...</td>";
        echo "<td>" . $suspicious['IsActive'] . "</td>";
        echo "<td>" . $suspicious['CreatedDate'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Recommendations
echo "<h3>🛠️ Recommendations:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";

echo "<p><strong>Possible Causes:</strong></p>";
echo "<ul>";
echo "<li>Product data corruption in database</li>";
echo "<li>Incorrect product ID mapping</li>";
echo "<li>Database import/export issues</li>";
echo "<li>Manual data entry errors</li>";
echo "<li>Product name field overflow</li>";
echo "</ul>";

echo "<p><strong>🔧 Suggested Actions:</strong></p>";
echo "<ul>";
echo "<li>Check product_master table for data integrity</li>";
echo "<li>Verify product ID mappings</li>";
echo "<li>Review recent database changes</li>";
echo "<li>Implement product validation before orders</li>";
echo "<li>Add product name length limits</li>";
echo "</ul>";

echo "</div>";

echo "<br><p><a href='order-placed.php?order_id=$orderId'>View Order Page</a> | <a href='debug_cart.php'>Debug Cart</a> | <a href='cms/'>Admin Panel</a></p>";
?>

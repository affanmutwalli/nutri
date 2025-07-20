<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

$orderId = 'MN000069';

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
echo "<h3>Product Information:</h3>";
$productQuery = "
    SELECT od.ProductId, od.ProductCode, od.Price, od.SubTotal, pm.ProductName 
    FROM order_details od 
    LEFT JOIN product_master pm ON od.ProductId = pm.ProductId 
    WHERE od.OrderId = '$orderId'
    ORDER BY od.ProductId
";

$productResult = $mysqli->query($productQuery);
if ($productResult && $productResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>Product Name</th><th>Product Code</th><th>Price</th><th>SubTotal</th></tr>";
    
    while ($product = $productResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $product['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars($product['ProductName']) . "</td>";
        echo "<td>" . $product['ProductCode'] . "</td>";
        echo "<td>₹" . $product['Price'] . "</td>";
        echo "<td>₹" . $product['SubTotal'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check if there are any cart remnants for this customer
echo "<h3>Cart Analysis:</h3>";
if (isset($order['CustomerId'])) {
    $customerId = $order['CustomerId'];
    echo "<p><strong>Customer ID:</strong> $customerId</p>";
    
    // Check current cart
    $cartQuery = "SELECT * FROM cart WHERE CustomerId = $customerId";
    $cartResult = $mysqli->query($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > 0) {
        echo "<h4>⚠️ Current Cart Items (should be empty after order):</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ProductId</th><th>Quantity</th><th>Price</th><th>Created</th><th>Updated</th></tr>";
        
        while ($cartItem = $cartResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $cartItem['ProductId'] . "</td>";
            echo "<td>" . $cartItem['Quantity'] . "</td>";
            echo "<td>₹" . $cartItem['Price'] . "</td>";
            echo "<td>" . $cartItem['CreatedDate'] . "</td>";
            echo "<td>" . $cartItem['UpdatedDate'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>✅ Cart is empty (as expected after order placement)</p>";
    }
}

// Check for any combo or bundle logic
echo "<h3>Potential Issues Analysis:</h3>";

// Check if products are part of any combo
$comboQuery = "
    SELECT DISTINCT pm.ProductId, pm.ProductName, 'Individual Product' as Type
    FROM product_master pm 
    WHERE pm.ProductId IN (SELECT ProductId FROM order_details WHERE OrderId = '$orderId')
    
    UNION
    
    SELECT DISTINCT pm.ProductId, pm.ProductName, 'Combo Product' as Type
    FROM product_master pm 
    WHERE pm.ProductName LIKE '%combo%' OR pm.ProductName LIKE '%bundle%'
    AND pm.ProductId IN (SELECT ProductId FROM order_details WHERE OrderId = '$orderId')
";

$comboResult = $mysqli->query($comboQuery);
if ($comboResult && $comboResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>Product Name</th><th>Type</th></tr>";
    
    while ($combo = $comboResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $combo['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars($combo['ProductName']) . "</td>";
        echo "<td>" . $combo['Type'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Recommendations
echo "<h3>🔧 Recommendations:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";

if ($orderDetails && $orderDetails->num_rows > 1) {
    echo "<p><strong>⚠️ Issue Detected:</strong> This order has multiple products when customer expected only one.</p>";
    echo "<p><strong>Possible Causes:</strong></p>";
    echo "<ul>";
    echo "<li>Cart not properly cleared before new order</li>";
    echo "<li>Session persistence issues</li>";
    echo "<li>Automatic combo/bundle addition</li>";
    echo "<li>Database cart sync issues</li>";
    echo "</ul>";
    
    echo "<p><strong>🛠️ Suggested Fixes:</strong></p>";
    echo "<ul>";
    echo "<li>Clear cart completely before each new order</li>";
    echo "<li>Add cart validation before order placement</li>";
    echo "<li>Implement better session management</li>";
    echo "<li>Add order confirmation step showing all items</li>";
    echo "</ul>";
} else {
    echo "<p>✅ Order looks normal with expected number of products.</p>";
}

echo "</div>";

echo "<br><p><a href='order-placed.php?order_id=$orderId'>View Order Page</a> | <a href='cms/'>Go to Admin Panel</a></p>";
?>

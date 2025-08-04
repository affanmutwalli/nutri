<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🔍 Simple Phantom Product Check</h2>";

// Check for orders with ProductCode MN-XX-000
echo "<h3>Checking for orders with ProductCode MN-XX-000</h3>";
$query1 = "SELECT OrderId, ProductId, ProductCode, Quantity, Price FROM order_details WHERE ProductCode = 'MN-XX-000' ORDER BY OrderId DESC LIMIT 10";
$result1 = $mysqli->query($query1);

if ($result1 && $result1->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Price</th></tr>";
    while ($row = $result1->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['OrderId'] . "</td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "<td>₹" . $row['Price'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Found " . $result1->num_rows . " orders with phantom products!</strong></p>";
} else {
    echo "<p>✅ No orders found with ProductCode MN-XX-000</p>";
}

// Check for orders with quantity 30
echo "<h3>Checking for orders with quantity 30</h3>";
$query2 = "SELECT OrderId, ProductId, ProductCode, Quantity, Price FROM order_details WHERE Quantity = 30 ORDER BY OrderId DESC LIMIT 10";
$result2 = $mysqli->query($query2);

if ($result2 && $result2->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Price</th></tr>";
    while ($row = $result2->fetch_assoc()) {
        echo "<tr style='background-color: #ffffcc;'>";
        echo "<td>" . $row['OrderId'] . "</td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "<td>₹" . $row['Price'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Found " . $result2->num_rows . " orders with quantity 30!</strong></p>";
} else {
    echo "<p>✅ No orders found with quantity 30</p>";
}

// Check for products with specific ProductId that might be causing issues
echo "<h3>Checking for ProductId that might be causing issues</h3>";
$query3 = "SELECT DISTINCT ProductId, ProductCode, COUNT(*) as count FROM order_details WHERE ProductCode LIKE 'MN-XX-%' GROUP BY ProductId, ProductCode ORDER BY count DESC LIMIT 10";
$result3 = $mysqli->query($query3);

if ($result3 && $result3->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>ProductCode</th><th>Order Count</th></tr>";
    while ($row = $result3->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No problematic ProductIds found</p>";
}

// Check if there's a product with ProductId that exists but has wrong data
echo "<h3>Checking product_master for any suspicious products</h3>";
$query4 = "SELECT ProductId, ProductName, ProductCode FROM product_master WHERE ProductCode LIKE 'MN-XX-%' OR ProductName = 'N/A' LIMIT 10";
$result4 = $mysqli->query($query4);

if ($result4 && $result4->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th></tr>";
    while ($row = $result4->fetch_assoc()) {
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No suspicious products found in product_master</p>";
}

// Actions
echo "<h3>🔧 Actions</h3>";
if (isset($_GET['action']) && $_GET['action'] == 'remove_phantom') {
    echo "<h4>Removing phantom products from order_details...</h4>";
    $deleteQuery = "DELETE FROM order_details WHERE ProductCode = 'MN-XX-000' OR ProductCode LIKE 'MN-XX-%'";
    if ($mysqli->query($deleteQuery)) {
        $affected = $mysqli->affected_rows;
        echo "<p style='color: green;'>✅ Removed $affected phantom product entries from order_details</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to remove phantom products: " . $mysqli->error . "</p>";
    }
} else {
    echo "<p><a href='?action=remove_phantom' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Remove Phantom Products from Orders</a></p>";
}
?>

<?php
/**
 * Debug order details to see why items aren't showing
 */

header("Content-Type: text/html");
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Order Details Debug</h2>";

session_start();
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

$orderId = $_GET['id'] ?? 'ON1753680466568';

echo "<h3>Debugging Order: " . htmlspecialchars($orderId) . "</h3>";

try {
    // Check if order exists in order_master
    echo "<h4>1. Order Master Data</h4>";
    $orderFields = ["OrderId", "CustomerId", "OrderDate", "Amount", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType", "TransactionId", "CreatedAt"];
    $orderQuery = "SELECT " . implode(",", $orderFields) . " FROM order_master WHERE OrderId = ?";
    $orderData = $obj->MysqliSelect1($orderQuery, $orderFields, "s", [$orderId]);
    
    if ($orderData && count($orderData) > 0) {
        echo "<p style='color: green;'>✅ Order found in order_master</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'>";
        foreach ($orderData[0] as $key => $value) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr><tr>";
        foreach ($orderData[0] as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr></table>";
    } else {
        echo "<p style='color: red;'>❌ Order NOT found in order_master</p>";
        
        // Check recent orders
        echo "<h5>Recent Orders:</h5>";
        $recentOrders = $obj->MysqliSelect1("SELECT OrderId, CreatedAt FROM order_master ORDER BY CreatedAt DESC LIMIT 10", 
            ["OrderId", "CreatedAt"], "", []);
        if ($recentOrders) {
            echo "<ul>";
            foreach ($recentOrders as $order) {
                echo "<li>" . htmlspecialchars($order['OrderId']) . " - " . htmlspecialchars($order['CreatedAt']) . "</li>";
            }
            echo "</ul>";
        }
        return;
    }
    
    // Check order_details table
    echo "<h4>2. Order Details Data</h4>";
    $detailsFields = ["OrderId", "ProductId", "ProductCode", "Size", "Quantity", "Price", "SubTotal"];
    $detailsQuery = "SELECT " . implode(",", $detailsFields) . " FROM order_details WHERE OrderId = ?";
    $detailsData = $obj->MysqliSelect1($detailsQuery, $detailsFields, "s", [$orderId]);
    
    if ($detailsData && count($detailsData) > 0) {
        echo "<p style='color: green;'>✅ Order details found: " . count($detailsData) . " items</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'>";
        echo "<th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Size</th><th>Quantity</th><th>Price</th><th>SubTotal</th>";
        echo "</tr>";
        
        foreach ($detailsData as $detail) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($detail['OrderId']) . "</td>";
            echo "<td>" . htmlspecialchars($detail['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($detail['ProductCode']) . "</td>";
            echo "<td>" . htmlspecialchars($detail['Size']) . "</td>";
            echo "<td>" . htmlspecialchars($detail['Quantity']) . "</td>";
            echo "<td>" . htmlspecialchars($detail['Price']) . "</td>";
            echo "<td>" . htmlspecialchars($detail['SubTotal']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No order details found in order_details table</p>";
        
        // Check if there are any order details at all
        echo "<h5>Recent Order Details:</h5>";
        $recentDetails = $obj->MysqliSelect1("SELECT OrderId, ProductId, Quantity FROM order_details ORDER BY OrderId DESC LIMIT 10", 
            ["OrderId", "ProductId", "Quantity"], "", []);
        if ($recentDetails) {
            echo "<ul>";
            foreach ($recentDetails as $detail) {
                echo "<li>Order: " . htmlspecialchars($detail['OrderId']) . " - Product: " . htmlspecialchars($detail['ProductId']) . " - Qty: " . htmlspecialchars($detail['Quantity']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>❌ No order details found in the entire table</p>";
        }
    }
    
    // Check product details for the items
    if ($detailsData && count($detailsData) > 0) {
        echo "<h4>3. Product Details</h4>";
        foreach ($detailsData as $item) {
            $productQuery = "SELECT ProductName, ProductCode, PhotoPath FROM product_master WHERE ProductId = ?";
            $productData = $obj->MysqliSelect1($productQuery, 
                ["ProductName", "ProductCode", "PhotoPath"], 
                "i", [$item['ProductId']]);
            
            if ($productData && count($productData) > 0) {
                echo "<p style='color: green;'>✅ Product ID " . $item['ProductId'] . ": " . htmlspecialchars($productData[0]['ProductName']) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Product ID " . $item['ProductId'] . " not found in product_master</p>";
            }
        }
    }
    
    // Check table structure
    echo "<h4>4. Table Structure Check</h4>";
    
    // Check order_details table structure
    $tableStructure = $obj->MysqliSelect1("DESCRIBE order_details", 
        ["Field", "Type", "Null", "Key", "Default", "Extra"], "", []);
    
    if ($tableStructure) {
        echo "<h5>order_details table structure:</h5>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #f2f2f2;'>";
        echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
        echo "</tr>";
        foreach ($tableStructure as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Debug Complete</h3>";
echo "<p>Timestamp: " . date('Y-m-d H:i:s') . "</p>";
?>

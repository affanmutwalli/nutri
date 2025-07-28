<?php
/**
 * Check all recent orders for missing order details
 */

header("Content-Type: text/html");
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Check All Recent Orders</h2>";

session_start();
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

try {
    // Get all recent orders
    $recentOrders = $obj->MysqliSelect1(
        "SELECT OrderId, CustomerId, Amount, PaymentType, CreatedAt FROM order_master ORDER BY CreatedAt DESC LIMIT 20", 
        ["OrderId", "CustomerId", "Amount", "PaymentType", "CreatedAt"], 
        "", []
    );
    
    if ($recentOrders && count($recentOrders) > 0) {
        echo "<h3>Recent Orders Status</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'>";
        echo "<th>Order ID</th><th>Customer ID</th><th>Amount</th><th>Payment Type</th><th>Created At</th><th>Order Details</th><th>Action</th>";
        echo "</tr>";
        
        foreach ($recentOrders as $order) {
            $orderId = $order['OrderId'];
            
            // Check if order details exist
            $detailsCount = $obj->MysqliSelect1(
                "SELECT COUNT(*) as count FROM order_details WHERE OrderId = ?", 
                ["count"], "s", [$orderId]
            );
            
            $hasDetails = ($detailsCount && $detailsCount[0]['count'] > 0);
            $detailsCountNum = $hasDetails ? $detailsCount[0]['count'] : 0;
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($orderId) . "</td>";
            echo "<td>" . htmlspecialchars($order['CustomerId']) . "</td>";
            echo "<td>₹" . htmlspecialchars($order['Amount']) . "</td>";
            echo "<td>" . htmlspecialchars($order['PaymentType']) . "</td>";
            echo "<td>" . htmlspecialchars($order['CreatedAt']) . "</td>";
            
            if ($hasDetails) {
                echo "<td style='color: green;'>✅ " . $detailsCountNum . " items</td>";
                echo "<td><a href='order-details.php?id=" . urlencode($orderId) . "'>View</a></td>";
            } else {
                echo "<td style='color: red;'>❌ Missing</td>";
                echo "<td><a href='fix_missing_order_details.php?id=" . urlencode($orderId) . "'>Fix</a></td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Summary
        $totalOrders = count($recentOrders);
        $ordersWithDetails = 0;
        $ordersWithoutDetails = 0;
        
        foreach ($recentOrders as $order) {
            $detailsCount = $obj->MysqliSelect1(
                "SELECT COUNT(*) as count FROM order_details WHERE OrderId = ?", 
                ["count"], "s", [$order['OrderId']]
            );
            
            if ($detailsCount && $detailsCount[0]['count'] > 0) {
                $ordersWithDetails++;
            } else {
                $ordersWithoutDetails++;
            }
        }
        
        echo "<h3>Summary</h3>";
        echo "<p>Total Orders: " . $totalOrders . "</p>";
        echo "<p style='color: green;'>Orders with Details: " . $ordersWithDetails . "</p>";
        echo "<p style='color: red;'>Orders without Details: " . $ordersWithoutDetails . "</p>";
        
        if ($ordersWithoutDetails > 0) {
            echo "<div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>⚠️ Action Required</h4>";
            echo "<p>There are " . $ordersWithoutDetails . " orders missing order details. This will cause the order details pages to show empty.</p>";
            echo "<p>This is likely due to the order details insertion issue in the payment callback that we just fixed.</p>";
            echo "<p>Future orders should work correctly, but existing orders may need manual fixing.</p>";
            echo "</div>";
        }
        
    } else {
        echo "<p>No orders found.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Check Complete</h3>";
echo "<p>Timestamp: " . date('Y-m-d H:i:s') . "</p>";
?>

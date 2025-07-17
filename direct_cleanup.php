<?php
/**
 * Direct Cleanup Script: Immediately Remove Pending Online Orders
 * 
 * This script directly removes all pending online orders without confirmation.
 * Use this for a quick cleanup of the database.
 */

include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Direct Cleanup of Pending Online Orders</h2>";

// First, let's see what we have
$pendingOnlineOrders = $obj->MysqliSelect1(
    "SELECT OrderId, Amount, PaymentStatus, TransactionId, OrderDate, CustomerType 
     FROM order_master 
     WHERE PaymentType = 'Online' AND PaymentStatus = 'Pending'
     ORDER BY OrderDate DESC",
    ["OrderId", "Amount", "PaymentStatus", "TransactionId", "OrderDate", "CustomerType"],
    "",
    []
);

echo "<h3>Pending Online Orders Found:</h3>";
if (!empty($pendingOnlineOrders)) {
    echo "<p>Found <strong>" . count($pendingOnlineOrders) . " pending online orders</strong> to clean up.</p>";
    
    // Display the orders that will be deleted
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Order ID</th><th>Amount</th><th>Date</th><th>Customer Type</th><th>Transaction ID</th></tr>";
    
    foreach ($pendingOnlineOrders as $order) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($order['OrderId']) . "</td>";
        echo "<td>₹" . number_format($order['Amount'], 2) . "</td>";
        echo "<td>" . date("d-m-Y H:i", strtotime($order['OrderDate'])) . "</td>";
        echo "<td>" . htmlspecialchars($order['CustomerType']) . "</td>";
        echo "<td>" . htmlspecialchars($order['TransactionId']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Performing Cleanup...</h3>";

    // Get the specific OrderIds to avoid collation issues
    $orderIds = [];
    foreach ($pendingOnlineOrders as $order) {
        $orderIds[] = $order['OrderId'];
    }

    $deletedCount = 0;

    // Delete each order individually to avoid collation issues
    foreach ($orderIds as $orderId) {
        // First delete order details
        $deleteDetailsResult = $obj->fInsertNew(
            "DELETE FROM order_details WHERE OrderId = ?",
            "s",
            [$orderId]
        );

        // Then delete the order
        $deleteOrderResult = $obj->fInsertNew(
            "DELETE FROM order_master WHERE OrderId = ? AND PaymentType = 'Online' AND PaymentStatus = 'Pending'",
            "s",
            [$orderId]
        );

        if ($deleteOrderResult) {
            $deletedCount++;
        }
    }

    $deleteOrdersResult = ($deletedCount > 0);
    
    if ($deleteOrdersResult) {
        echo "<p style='color: green; font-size: 18px;'>✅ <strong>SUCCESS!</strong> All " . count($pendingOnlineOrders) . " pending online orders have been deleted.</p>";
        echo "<p>Your database is now clean and the new payment flow will prevent any new pending online orders.</p>";
    } else {
        echo "<p style='color: red;'>❌ Error deleting orders. Please try again or use the manual cleanup page.</p>";
    }
    
} else {
    echo "<p style='color: green;'><strong>✅ No pending online orders found!</strong></p>";
    echo "<p>Your database is already clean. The new payment flow is working correctly.</p>";
}

echo "<hr>";
echo "<h3>Summary of New Payment Flow:</h3>";
echo "<ul>";
echo "<li><strong>COD Orders:</strong> Created immediately in database with 'Pending' payment status (to be paid on delivery)</li>";
echo "<li><strong>Online Orders:</strong> Created in database only after successful Razorpay payment with 'Paid' status</li>";
echo "<li><strong>Failed Online Payments:</strong> No database entry created (keeps database clean)</li>";
echo "</ul>";

echo "<p><a href='oms/razorpay_dashboard.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Go to Razorpay Dashboard</a></p>";

?>

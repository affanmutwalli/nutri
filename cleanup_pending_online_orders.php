<?php
/**
 * Cleanup Script: Remove Pending Online Orders
 * 
 * This script removes pending online orders that were created under the old system
 * where orders were created before payment completion.
 * 
 * Under the new system, online orders are only created after successful payment,
 * so there should be no pending online orders in the database.
 */

session_start();
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Cleanup Pending Online Orders</h2>";

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

echo "<h3>Current Pending Online Orders:</h3>";
if (!empty($pendingOnlineOrders)) {
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
    
    echo "<br><p><strong>Found " . count($pendingOnlineOrders) . " pending online orders.</strong></p>";
    echo "<p>These orders were likely created under the old system where orders were created before payment completion.</p>";
    
    // Provide cleanup options
    echo "<h3>Cleanup Options:</h3>";
    echo "<form method='POST' action=''>";
    echo "<input type='hidden' name='action' value='cleanup'>";
    echo "<p><input type='radio' name='cleanup_type' value='delete' id='delete'> <label for='delete'>Delete these pending online orders (Recommended)</label></p>";
    echo "<p><input type='radio' name='cleanup_type' value='mark_failed' id='mark_failed'> <label for='mark_failed'>Mark as 'Failed' instead of deleting</label></p>";
    echo "<br><button type='submit' onclick='return confirm(\"Are you sure you want to proceed with cleanup?\")'>Proceed with Cleanup</button>";
    echo "</form>";
    
} else {
    echo "<p style='color: green;'><strong>✅ No pending online orders found!</strong></p>";
    echo "<p>Your database is clean. The new payment flow is working correctly.</p>";
}

// Handle cleanup action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cleanup') {
    $cleanupType = $_POST['cleanup_type'];
    
    if ($cleanupType === 'delete') {
        // Delete pending online orders and their details
        echo "<h3>Deleting Pending Online Orders...</h3>";
        
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
            echo "<p style='color: green;'>✅ Successfully deleted pending online orders and their details.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error deleting orders.</p>";
        }
        
    } elseif ($cleanupType === 'mark_failed') {
        // Mark as failed
        echo "<h3>Marking Pending Online Orders as Failed...</h3>";
        
        $updateResult = $obj->fInsertNew(
            "UPDATE order_master SET PaymentStatus = 'Failed' WHERE PaymentType = 'Online' AND PaymentStatus = 'Pending'",
            "",
            []
        );
        
        if ($updateResult) {
            echo "<p style='color: green;'>✅ Successfully marked pending online orders as failed.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error updating orders.</p>";
        }
    }
    
    echo "<br><a href='cleanup_pending_online_orders.php'>Refresh to see updated status</a>";
}

echo "<hr>";
echo "<h3>Summary of New Payment Flow:</h3>";
echo "<ul>";
echo "<li><strong>COD Orders:</strong> Created immediately in database with 'Pending' payment status (to be paid on delivery)</li>";
echo "<li><strong>Online Orders:</strong> Created in database only after successful Razorpay payment with 'Paid' status</li>";
echo "<li><strong>Failed Online Payments:</strong> No database entry created (keeps database clean)</li>";
echo "</ul>";

echo "<p><strong>Benefits:</strong></p>";
echo "<ul>";
echo "<li>✅ No more pending online orders cluttering the database</li>";
echo "<li>✅ OMS only shows actual completed transactions</li>";
echo "<li>✅ Cleaner analytics and reporting</li>";
echo "<li>✅ Better inventory management (no ghost orders)</li>";
echo "</ul>";

echo "<br><a href='oms/razorpay_dashboard.php'>Go to Razorpay Dashboard</a> | <a href='oms/'>Go to OMS</a>";

?>

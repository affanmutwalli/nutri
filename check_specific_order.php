<?php
// Check specific order status
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

$testOrderId = 'ON1752061926930';

echo "<h2>🔍 Checking Order: $testOrderId</h2>";

try {
    // Check order status
    $query = "SELECT OrderId, OrderStatus, Amount, Waybill, PaymentType, CreatedAt, 
                     delivery_status, delivery_provider, tracking_url
              FROM order_master 
              WHERE OrderId = ?";
    
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $testOrderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($order = mysqli_fetch_assoc($result)) {
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>📋 Order Details:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Value</th></tr>";
        
        foreach ($order as $key => $value) {
            echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$value</td></tr>";
        }
        
        echo "</table>";
        echo "</div>";
        
        // Check if it was actually shipped
        if (!empty($order['Waybill']) && $order['Waybill'] !== 'NULL') {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>⚠️ This Order WAS Shipped!</h3>";
            echo "<p><strong>Waybill:</strong> {$order['Waybill']}</p>";
            echo "<p><strong>Amount Charged:</strong> ₹{$order['Amount']}</p>";
            echo "<p><strong>Status:</strong> {$order['OrderStatus']}</p>";
            
            if (!empty($order['tracking_url'])) {
                echo "<p><strong>Tracking:</strong> <a href='{$order['tracking_url']}' target='_blank'>{$order['tracking_url']}</a></p>";
            }
            
            echo "<p><strong>⚠️ This shipment is now active with Delhivery and will be delivered.</strong></p>";
            echo "</div>";
        } else {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>✅ This Order Was NOT Shipped</h3>";
            echo "<p>No waybill found - the order failed to ship.</p>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Order $testOrderId not found!</p>";
    }
    
    // Check delivery logs for this order
    echo "<h3>📝 Delivery Logs:</h3>";
    $logQuery = "SELECT * FROM delivery_logs WHERE order_id = ? ORDER BY created_at DESC";
    $logStmt = mysqli_prepare($mysqli, $logQuery);
    mysqli_stmt_bind_param($logStmt, "s", $testOrderId);
    mysqli_stmt_execute($logStmt);
    $logResult = mysqli_stmt_get_result($logStmt);
    
    if (mysqli_num_rows($logResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th style='padding: 8px;'>Time</th><th style='padding: 8px;'>Action</th><th style='padding: 8px;'>Status</th><th style='padding: 8px;'>Response</th></tr>";
        
        while ($log = mysqli_fetch_assoc($logResult)) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>{$log['created_at']}</td>";
            echo "<td style='padding: 8px;'>{$log['action']}</td>";
            echo "<td style='padding: 8px;'>{$log['status']}</td>";
            echo "<td style='padding: 8px; max-width: 300px; word-wrap: break-word;'>" . substr($log['response'], 0, 200) . "...</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No delivery logs found for this order.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔄 Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Check your Delhivery account</strong> to see actual charges</li>";
echo "<li><strong>Contact Delhivery support</strong> if you need to cancel any unwanted shipments</li>";
echo "<li><strong>For future testing:</strong> Use a single order processor instead of bulk</li>";
echo "</ol>";
?>

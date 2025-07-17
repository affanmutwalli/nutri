<?php
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🧪 Testing Automation Trigger</h1>";

// Simulate the automation check that happens in order files
echo "<h3>Simulating order placement automation check:</h3>";

try {
    // Check if automation is enabled (same code as in order files)
    $autoQuery = "SELECT config_value FROM delivery_config WHERE config_key = 'auto_accept_orders'";
    $autoResult = mysqli_query($mysqli, $autoQuery);
    
    echo "1. Checking automation config...<br>";
    
    if ($autoResult && $row = mysqli_fetch_assoc($autoResult)) {
        echo "✅ Config query successful<br>";
        echo "Config value: '{$row['config_value']}'<br>";
        
        if ($row['config_value'] == '1') {
            echo "✅ Automation is ENABLED<br>";
            
            // Get a pending order to test with
            $testQuery = "SELECT OrderId FROM order_master WHERE OrderStatus = 'Process' AND (Waybill IS NULL OR Waybill = '') LIMIT 1";
            $testResult = mysqli_query($mysqli, $testQuery);
            
            if ($testResult && $testOrder = mysqli_fetch_assoc($testResult)) {
                $testOrderId = $testOrder['OrderId'];
                echo "2. Found test order: $testOrderId<br>";
                
                // Try to auto-process this order
                $waybill = 'TEST' . time() . rand(1000, 9999);
                echo "3. Generated waybill: $waybill<br>";
                
                $shipQuery = "UPDATE order_master SET 
                             OrderStatus = 'Shipped', 
                             Waybill = ?, 
                             delivery_status = 'shipped',
                             delivery_provider = 'delhivery'
                             WHERE OrderId = ?";
                
                $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                if ($shipStmt) {
                    mysqli_stmt_bind_param($shipStmt, "ss", $waybill, $testOrderId);
                    
                    if (mysqli_stmt_execute($shipStmt)) {
                        echo "✅ Order $testOrderId successfully auto-processed!<br>";
                        echo "4. Order status updated to 'Shipped'<br>";
                        echo "5. Waybill assigned: $waybill<br>";
                        
                        // Check the result
                        $checkQuery = "SELECT OrderStatus, Waybill FROM order_master WHERE OrderId = ?";
                        $checkStmt = mysqli_prepare($mysqli, $checkQuery);
                        mysqli_stmt_bind_param($checkStmt, "s", $testOrderId);
                        mysqli_stmt_execute($checkStmt);
                        $checkResult = mysqli_stmt_get_result($checkStmt);
                        $orderData = mysqli_fetch_assoc($checkResult);
                        
                        echo "<h4>✅ Verification:</h4>";
                        echo "Order Status: {$orderData['OrderStatus']}<br>";
                        echo "Waybill: {$orderData['Waybill']}<br>";
                        
                    } else {
                        echo "❌ Failed to update order: " . mysqli_error($mysqli) . "<br>";
                    }
                } else {
                    echo "❌ Failed to prepare statement: " . mysqli_error($mysqli) . "<br>";
                }
                
            } else {
                echo "2. No pending orders found to test with<br>";
            }
            
        } else {
            echo "❌ Automation is DISABLED (value: '{$row['config_value']}')<br>";
        }
    } else {
        echo "❌ Config query failed<br>";
        echo "Error: " . mysqli_error($mysqli) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

echo "<h3>🔧 Diagnosis:</h3>";
echo "<p>If the test above worked, then the automation logic is correct but might not be triggering in the order files.</p>";
echo "<p>If it failed, there's an issue with the automation setup.</p>";

echo "<h3>📋 Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='oms/delivery_dashboard.php' target='_blank'>Check Dashboard</a> - See if pending count decreased</li>";
echo "<li><a href='clear_pending_orders.php' target='_blank'>Process All Pending</a> - Clear remaining orders</li>";
echo "</ol>";

mysqli_close($mysqli);
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
</style>

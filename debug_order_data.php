<?php
// Debug Order Data Retrieval
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

$testOrderId = 'ON1752061926930';

echo "<h2>🔍 Debug Order Data for: $testOrderId</h2>";

try {
    // Use the EXACT same query as process_all_unshipped_orders.php
    $query = "SELECT om.OrderId, om.CustomerId, om.CustomerType, om.Amount, om.ShipAddress, om.OrderStatus, om.PaymentType,
                    COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName,
                    COALESCE(cm.MobileNo, dc.MobileNo, '') as CustomerPhone,
                    COALESCE(cm.Email, dc.Email, '') as CustomerEmail,
                    ca.Address as CustomerAddress,
                    ca.Landmark as CustomerLandmark,
                    ca.City as CustomerCity,
                    ca.State as CustomerState,
                    ca.PinCode as CustomerPincode
             FROM order_master om
             LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
             LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
             LEFT JOIN customer_address ca ON om.CustomerId = ca.CustomerId AND om.CustomerType = 'Registered'
             WHERE om.OrderId = ?";
    
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $testOrderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($order = mysqli_fetch_assoc($result)) {
        echo "<h3>📋 Raw Database Data:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th style='padding: 8px; background: #f0f0f0;'>Field</th><th style='padding: 8px; background: #f0f0f0;'>Value</th><th style='padding: 8px; background: #f0f0f0;'>Type</th></tr>";
        
        foreach ($order as $key => $value) {
            $displayValue = $value === null ? 'NULL' : ($value === '' ? 'EMPTY STRING' : $value);
            $type = gettype($value);
            echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$displayValue</td><td style='padding: 8px;'>$type</td></tr>";
        }
        
        echo "</table>";
        
        // Now simulate the data processing logic
        echo "<h3>🔄 Data Processing Logic:</h3>";
        
        $customerPhone = trim($order['CustomerPhone'] ?? '');
        $shippingAddress = trim($order['ShipAddress'] ?? '');
        $totalAmount = $order['Amount'] ?? 0;
        
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Step 1: Initial Processing</h4>";
        echo "CustomerPhone (from DB): '" . ($order['CustomerPhone'] ?? 'NULL') . "'<br>";
        echo "After trim(): '" . $customerPhone . "'<br>";
        echo "Is empty?: " . (empty($customerPhone) ? 'YES' : 'NO') . "<br><br>";
        
        echo "ShipAddress (from DB): '" . ($order['ShipAddress'] ?? 'NULL') . "'<br>";
        echo "After trim(): '" . $shippingAddress . "'<br>";
        echo "Is empty?: " . (empty($shippingAddress) ? 'YES' : 'NO') . "<br><br>";
        
        echo "Amount (from DB): '" . ($order['Amount'] ?? 'NULL') . "'<br>";
        echo "After processing: '" . $totalAmount . "'<br>";
        echo "Is <= 0?: " . ($totalAmount <= 0 ? 'YES' : 'NO') . "<br>";
        echo "</div>";
        
        // Check address fallback logic
        if (empty($shippingAddress) && !empty($order['CustomerAddress'])) {
            $addressParts = array_filter([
                $order['CustomerAddress'],
                $order['CustomerLandmark'],
                $order['CustomerCity'],
                $order['CustomerPincode'],
                $order['CustomerState']
            ]);
            $shippingAddress = implode(', ', $addressParts);
            
            echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>Step 2: Address Fallback</h4>";
            echo "Built address from customer_address: '$shippingAddress'<br>";
            echo "Address parts found: " . count($addressParts) . "<br>";
            echo "</div>";
        }
        
        // Final order data that would be sent
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Final Order Data (what gets sent to Delhivery):</h4>";
        echo "order_id: '" . $order['OrderId'] . "'<br>";
        echo "customer_name: '" . ($order['CustomerName'] ?? 'Customer') . "'<br>";
        echo "customer_phone: '" . $customerPhone . "'<br>";
        echo "shipping_address: '" . $shippingAddress . "'<br>";
        echo "total_amount: '" . $totalAmount . "'<br>";
        echo "payment_mode: '" . (($order['PaymentType'] == 'COD') ? 'COD' : 'Prepaid') . "'<br>";
        echo "</div>";
        
        // Check what would fail validation
        $missingFields = [];
        if (empty($order['OrderId'])) $missingFields[] = 'order_id';
        if (empty($order['CustomerName'])) $missingFields[] = 'customer_name';
        if (empty($customerPhone)) $missingFields[] = 'customer_phone';
        if (empty($shippingAddress)) $missingFields[] = 'shipping_address';
        if ($totalAmount <= 0) $missingFields[] = 'total_amount';
        
        if (!empty($missingFields)) {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>❌ Validation Would Fail:</h4>";
            echo "Missing fields: " . implode(', ', $missingFields) . "<br>";
            echo "</div>";
        } else {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>✅ Validation Would Pass!</h4>";
            echo "All required fields are present.<br>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Order $testOrderId not found in database!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

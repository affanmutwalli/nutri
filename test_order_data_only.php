<?php
// SAFE TEST - Only shows data preparation, NO API calls
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

$testOrderId = 'ON1752061926930';

echo "<h2>🛡️ SAFE TEST - Data Preparation Only</h2>";
echo "<p style='color: green;'><strong>✅ This will NOT send any API calls or charge money!</strong></p>";

try {
    // Use the EXACT same query as the bulk processor
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
             LEFT JOIN customer_address ca ON om.CustomerId = ca.CustomerId
             WHERE om.OrderId = ?";
    
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "s", $testOrderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($order = mysqli_fetch_assoc($result)) {
        echo "<h3>📋 Step 1: Raw Database Query Result</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th style='padding: 8px; background: #f0f0f0;'>Field</th><th style='padding: 8px; background: #f0f0f0;'>Value</th></tr>";
        
        foreach ($order as $key => $value) {
            $displayValue = $value === null ? 'NULL' : ($value === '' ? 'EMPTY STRING' : $value);
            $color = ($value === null || $value === '') ? 'color: red;' : 'color: green;';
            echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px; $color'>$displayValue</td></tr>";
        }
        echo "</table>";
        
        echo "<h3>🔄 Step 2: Data Processing (Same as Bulk Processor)</h3>";
        
        // EXACT same logic as bulk processor
        $customerPhone = trim($order['CustomerPhone'] ?? '');
        $shippingAddress = trim($order['ShipAddress'] ?? '');
        $totalAmount = $order['Amount'] ?? 0;
        
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Initial Processing:</h4>";
        echo "CustomerPhone: '" . $customerPhone . "' (empty: " . (empty($customerPhone) ? 'YES' : 'NO') . ")<br>";
        echo "ShipAddress: '" . $shippingAddress . "' (empty: " . (empty($shippingAddress) ? 'YES' : 'NO') . ")<br>";
        echo "Amount: '" . $totalAmount . "' (≤ 0: " . ($totalAmount <= 0 ? 'YES' : 'NO') . ")<br>";
        echo "</div>";
        
        // Address fallback logic
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
            echo "<h4>Address Fallback Applied:</h4>";
            echo "Built from customer_address: '$shippingAddress'<br>";
            echo "</div>";
        }
        
        // Validation checks
        $validationErrors = [];
        if (empty($customerPhone)) $validationErrors[] = 'customer_phone';
        if (empty($shippingAddress)) $validationErrors[] = 'shipping_address';
        if ($totalAmount <= 0) $validationErrors[] = 'total_amount';
        
        echo "<h3>🔍 Step 3: Final Order Data (What Would Be Sent to Delhivery)</h3>";
        
        $finalOrderData = [
            'order_id' => $order['OrderId'],
            'customer_name' => $order['CustomerName'] ?? 'Customer',
            'customer_phone' => $customerPhone,
            'shipping_address' => $shippingAddress,
            'total_amount' => $totalAmount,
            'payment_mode' => ($order['PaymentType'] == 'COD') ? 'COD' : 'Prepaid',
            'weight' => 0.5,
            'products' => [['name' => 'Product', 'quantity' => 1]],
            'order_date' => date('Y-m-d H:i:s')
        ];
        
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Final Data Package:</h4>";
        foreach ($finalOrderData as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            echo "$key: '$value'<br>";
        }
        echo "</div>";
        
        // Validation result
        if (empty($validationErrors)) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>✅ VALIDATION WOULD PASS!</h3>";
            echo "<p>All required fields are present. This order would be sent to Delhivery successfully.</p>";
            echo "<p><strong>Next step:</strong> If you want to actually ship this order, add balance to Delhivery and use the single order processor.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>❌ VALIDATION WOULD FAIL!</h3>";
            echo "<p>Missing fields: " . implode(', ', $validationErrors) . "</p>";
            echo "<p><strong>These issues need to be fixed before shipping.</strong></p>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Order $testOrderId not found!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🛡️ Safety Reminder</h3>";
echo "<p><strong>This test is 100% safe:</strong></p>";
echo "<ul>";
echo "<li>✅ No API calls made</li>";
echo "<li>✅ No money charged</li>";
echo "<li>✅ No shipments created</li>";
echo "<li>✅ Only shows data preparation</li>";
echo "</ul>";
echo "</div>";
?>

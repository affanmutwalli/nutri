<?php
// Check Customer Data for Order
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

$testOrderId = 'ON1752061926930';

echo "<h2>🔍 Check Customer Data for Order: $testOrderId</h2>";

try {
    // First get the order details
    $orderQuery = "SELECT OrderId, CustomerId, CustomerType, Amount, ShipAddress, PaymentType, CreatedAt FROM order_master WHERE OrderId = ?";
    $stmt = mysqli_prepare($mysqli, $orderQuery);
    mysqli_stmt_bind_param($stmt, "s", $testOrderId);
    mysqli_stmt_execute($stmt);
    $orderResult = mysqli_stmt_get_result($stmt);
    
    if ($order = mysqli_fetch_assoc($orderResult)) {
        echo "<h3>📋 Order Master Data:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Value</th></tr>";
        
        foreach ($order as $key => $value) {
            $displayValue = $value === null ? 'NULL' : ($value === '' ? 'EMPTY' : $value);
            echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$displayValue</td></tr>";
        }
        echo "</table>";
        
        $customerId = $order['CustomerId'];
        $customerType = $order['CustomerType'];
        
        echo "<h3>👤 Customer Type: $customerType</h3>";
        
        if ($customerType === 'Registered') {
            // Check customer_master table
            echo "<h4>Checking customer_master table:</h4>";
            $customerQuery = "SELECT * FROM customer_master WHERE CustomerId = ?";
            $stmt2 = mysqli_prepare($mysqli, $customerQuery);
            mysqli_stmt_bind_param($stmt2, "i", $customerId);
            mysqli_stmt_execute($stmt2);
            $customerResult = mysqli_stmt_get_result($stmt2);
            
            if ($customer = mysqli_fetch_assoc($customerResult)) {
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Value</th></tr>";
                
                foreach ($customer as $key => $value) {
                    $displayValue = $value === null ? 'NULL' : ($value === '' ? 'EMPTY' : $value);
                    echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$displayValue</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: red;'>❌ No customer found in customer_master with ID: $customerId</p>";
            }
            
            // Check customer_address table
            echo "<h4>Checking customer_address table:</h4>";
            $addressQuery = "SELECT * FROM customer_address WHERE CustomerId = ?";
            $stmt3 = mysqli_prepare($mysqli, $addressQuery);
            mysqli_stmt_bind_param($stmt3, "i", $customerId);
            mysqli_stmt_execute($stmt3);
            $addressResult = mysqli_stmt_get_result($stmt3);
            
            if ($address = mysqli_fetch_assoc($addressResult)) {
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Value</th></tr>";
                
                foreach ($address as $key => $value) {
                    $displayValue = $value === null ? 'NULL' : ($value === '' ? 'EMPTY' : $value);
                    echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$displayValue</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: red;'>❌ No address found in customer_address with ID: $customerId</p>";
            }
            
        } elseif ($customerType === 'Direct') {
            // Check direct_customers table
            echo "<h4>Checking direct_customers table:</h4>";
            $directQuery = "SELECT * FROM direct_customers WHERE CustomerId = ?";
            $stmt4 = mysqli_prepare($mysqli, $directQuery);
            mysqli_stmt_bind_param($stmt4, "i", $customerId);
            mysqli_stmt_execute($stmt4);
            $directResult = mysqli_stmt_get_result($stmt4);
            
            if ($direct = mysqli_fetch_assoc($directResult)) {
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Value</th></tr>";
                
                foreach ($direct as $key => $value) {
                    $displayValue = $value === null ? 'NULL' : ($value === '' ? 'EMPTY' : $value);
                    echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$displayValue</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: red;'>❌ No customer found in direct_customers with ID: $customerId</p>";
            }
        }
        
        // Check if ShipAddress contains customer data
        if (!empty($order['ShipAddress'])) {
            echo "<h4>📍 ShipAddress Analysis:</h4>";
            echo "<p>ShipAddress content: '" . $order['ShipAddress'] . "'</p>";
            
            // Try to parse it
            $shipAddressParts = explode(", ", $order['ShipAddress']);
            echo "<p>Parts count: " . count($shipAddressParts) . "</p>";
            
            if (count($shipAddressParts) >= 8) {
                echo "<p>✅ Looks like it contains customer details (8+ parts)</p>";
                echo "<ol>";
                foreach ($shipAddressParts as $index => $part) {
                    echo "<li>$part</li>";
                }
                echo "</ol>";
            } else {
                echo "<p>⚠️ Doesn't look like full customer details</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ Order $testOrderId not found!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

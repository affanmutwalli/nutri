<?php
/**
 * Fix Existing Order Addresses for Delhivery Compatibility
 */

include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🔧 Fixing Existing Order Addresses</h1>";

try {
    // Get all orders with problematic addresses (containing name, email, phone)
    $query = "SELECT OrderId, CustomerId, CustomerType, ShipAddress FROM order_master 
             WHERE ShipAddress LIKE '%@%' OR ShipAddress LIKE '%+91%' OR LENGTH(ShipAddress) > 200
             ORDER BY CreatedAt DESC";
    
    $result = mysqli_query($mysqli, $query);
    $totalOrders = mysqli_num_rows($result);
    $fixedCount = 0;
    
    echo "<p>📦 Found $totalOrders orders with problematic addresses</p>";
    
    if ($totalOrders == 0) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ No problematic addresses found!</h3>";
        echo "<p>All order addresses are already properly formatted.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>⚡ Fixing Addresses...</h3>";
        
        while ($order = mysqli_fetch_assoc($result)) {
            try {
                echo "<p>🔄 Fixing order: {$order['OrderId']} (Type: {$order['CustomerType']})</p>";
                echo "<p><strong>Old Address:</strong> " . htmlspecialchars($order['ShipAddress']) . "</p>";
                
                $newAddress = "";
                
                if ($order['CustomerType'] == 'Direct') {
                    // Get address from direct_customers table
                    $custQuery = "SELECT Address, City, Pincode, State FROM direct_customers WHERE CustomerId = ?";
                    $stmt = mysqli_prepare($mysqli, $custQuery);
                    mysqli_stmt_bind_param($stmt, "s", $order['CustomerId']);
                    mysqli_stmt_execute($stmt);
                    $custResult = mysqli_stmt_get_result($stmt);
                    $custData = mysqli_fetch_assoc($custResult);
                    
                    if ($custData) {
                        $newAddress = implode(", ", array_filter([
                            $custData['Address'],
                            $custData['City'],
                            $custData['State'] . " - " . $custData['Pincode']
                        ]));
                    }
                } else {
                    // Get address from customer_address table
                    $addrQuery = "SELECT Address, Landmark, City, PinCode, State FROM customer_address WHERE CustomerId = ?";
                    $stmt = mysqli_prepare($mysqli, $addrQuery);
                    mysqli_stmt_bind_param($stmt, "i", $order['CustomerId']);
                    mysqli_stmt_execute($stmt);
                    $addrResult = mysqli_stmt_get_result($stmt);
                    $addrData = mysqli_fetch_assoc($addrResult);
                    
                    if ($addrData) {
                        $newAddress = implode(", ", array_filter([
                            $addrData['Address'],
                            $addrData['Landmark'],
                            $addrData['City'],
                            $addrData['State'] . " - " . $addrData['PinCode']
                        ]));
                    }
                }
                
                if (!empty($newAddress)) {
                    // Update the order with the proper address
                    $updateQuery = "UPDATE order_master SET ShipAddress = ? WHERE OrderId = ?";
                    $updateStmt = mysqli_prepare($mysqli, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "ss", $newAddress, $order['OrderId']);
                    
                    if (mysqli_stmt_execute($updateStmt)) {
                        echo "<p><strong>New Address:</strong> " . htmlspecialchars($newAddress) . "</p>";
                        echo "<p>✅ Address fixed successfully!</p>";
                        $fixedCount++;
                    } else {
                        echo "<p>❌ Failed to update address: " . mysqli_error($mysqli) . "</p>";
                    }
                } else {
                    echo "<p>⚠️ Could not find customer address data</p>";
                }
                
                echo "<hr>";
                
            } catch (Exception $e) {
                echo "<p>❌ Error fixing order {$order['OrderId']}: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "</div>";
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 Address Fix Complete!</h3>";
        echo "<p><strong>Successfully fixed:</strong> $fixedCount out of $totalOrders orders</p>";
        echo "<p><strong>All addresses are now Delhivery-compatible!</strong></p>";
        echo "</div>";
    }
    
    echo "<h3>✅ Address Format for Delhivery:</h3>";
    echo "<p><strong>Correct Format:</strong> Street Address, Landmark, City, State - Pincode</p>";
    echo "<p><strong>Example:</strong> 123 Main Street, Near Park, Mumbai, Maharashtra - 400001</p>";
    
    echo "<h3>🔄 Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='debug_customer_address.php' target='_blank'>Verify Addresses</a> - Check all addresses are properly formatted</li>";
    echo "<li><a href='oms/order_details.php?OrderId=ORD000001' target='_blank'>Test Order Details</a> - Check if addresses display correctly</li>";
    echo "<li>New orders will automatically use the correct address format</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Close database connection
if (isset($mysqli)) {
    mysqli_close($mysqli);
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1 { color: #28a745; }
h3 { color: #007bff; }
p { margin: 10px 0; }
hr { margin: 20px 0; border: 1px solid #ddd; }
ul, ol { margin: 10px 0 10px 20px; }
</style>

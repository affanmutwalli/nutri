<?php
/**
 * Migrate Registered Customer Addresses from ShipAddress to customer_address table
 */

include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🔄 Migrating Registered Customer Addresses</h1>";

try {
    // Get all registered customers who have orders but no saved addresses
    $query = "SELECT DISTINCT om.CustomerId, om.ShipAddress, cm.Name, cm.Email
             FROM order_master om 
             JOIN customer_master cm ON om.CustomerId = cm.CustomerId 
             WHERE om.CustomerType = 'Registered' 
             AND om.CustomerId NOT IN (SELECT CustomerId FROM customer_address WHERE CustomerId IS NOT NULL)
             AND om.ShipAddress IS NOT NULL 
             AND om.ShipAddress != ''
             ORDER BY om.CustomerId";
    
    $result = mysqli_query($mysqli, $query);
    $totalCustomers = mysqli_num_rows($result);
    $migratedCount = 0;
    
    echo "<p>📦 Found $totalCustomers registered customers with orders but no saved addresses</p>";
    
    if ($totalCustomers == 0) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ No migration needed!</h3>";
        echo "<p>All registered customers already have addresses saved in customer_address table.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>⚡ Migrating Addresses...</h3>";
        
        while ($customer = mysqli_fetch_assoc($result)) {
            try {
                echo "<p>🔄 Processing customer: {$customer['Name']} (ID: {$customer['CustomerId']})</p>";
                echo "<p><strong>Original ShipAddress:</strong> " . htmlspecialchars($customer['ShipAddress']) . "</p>";
                
                // Parse the ShipAddress to extract components
                $addressParts = explode(", ", $customer['ShipAddress']);
                
                // Try to intelligently parse the address
                $address = "";
                $landmark = "";
                $city = "";
                $state = "";
                $pincode = "";
                
                if (count($addressParts) >= 3) {
                    $address = trim($addressParts[0]);
                    $landmark = isset($addressParts[1]) ? trim($addressParts[1]) : "";
                    $city = isset($addressParts[2]) ? trim($addressParts[2]) : "";
                    
                    // Look for state and pincode in the last parts
                    for ($i = 3; $i < count($addressParts); $i++) {
                        $part = trim($addressParts[$i]);
                        
                        // Check if it's a pincode (6 digits)
                        if (preg_match('/^\d{6}$/', $part)) {
                            $pincode = $part;
                        } 
                        // Check if it contains a pincode pattern (State - Pincode)
                        else if (preg_match('/^(.+?)\s*-\s*(\d{6})$/', $part, $matches)) {
                            $state = trim($matches[1]);
                            $pincode = trim($matches[2]);
                        }
                        // Otherwise, assume it's part of state
                        else if (empty($state)) {
                            $state = $part;
                        }
                    }
                }
                
                // Fallback: if parsing failed, use basic extraction
                if (empty($address) && !empty($customer['ShipAddress'])) {
                    $address = $customer['ShipAddress'];
                }
                
                // Ensure we have at least an address
                if (!empty($address)) {
                    // Insert into customer_address table
                    $insertQuery = "INSERT INTO customer_address (CustomerId, Address, Landmark, City, PinCode, State) VALUES (?, ?, ?, ?, ?, ?)";
                    $insertStmt = mysqli_prepare($mysqli, $insertQuery);
                    mysqli_stmt_bind_param($insertStmt, "isssss", 
                        $customer['CustomerId'],
                        $address,
                        $landmark,
                        $city,
                        $pincode,
                        $state
                    );
                    
                    if (mysqli_stmt_execute($insertStmt)) {
                        echo "<p><strong>Migrated Address:</strong></p>";
                        echo "<ul>";
                        echo "<li><strong>Address:</strong> $address</li>";
                        echo "<li><strong>Landmark:</strong> $landmark</li>";
                        echo "<li><strong>City:</strong> $city</li>";
                        echo "<li><strong>State:</strong> $state</li>";
                        echo "<li><strong>Pincode:</strong> $pincode</li>";
                        echo "</ul>";
                        echo "<p>✅ Address migrated successfully!</p>";
                        $migratedCount++;
                    } else {
                        echo "<p>❌ Failed to migrate address: " . mysqli_error($mysqli) . "</p>";
                    }
                } else {
                    echo "<p>⚠️ Could not parse address from ShipAddress field</p>";
                }
                
                echo "<hr>";
                
            } catch (Exception $e) {
                echo "<p>❌ Error migrating customer {$customer['CustomerId']}: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "</div>";
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 Migration Complete!</h3>";
        echo "<p><strong>Successfully migrated:</strong> $migratedCount out of $totalCustomers customers</p>";
        echo "<p><strong>All registered customers now have addresses in customer_address table!</strong></p>";
        echo "</div>";
    }
    
    echo "<h3>✅ Benefits of This Migration:</h3>";
    echo "<ul>";
    echo "<li>✅ Order details pages will now show proper customer addresses</li>";
    echo "<li>✅ Registered customers can edit their saved addresses</li>";
    echo "<li>✅ Future orders will automatically use saved addresses</li>";
    echo "<li>✅ Delhivery integration will have clean, structured address data</li>";
    echo "</ul>";
    
    echo "<h3>🔄 Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='debug_registered_customer_flow.php' target='_blank'>Verify Migration</a> - Check that customer_address table now has data</li>";
    echo "<li><a href='oms/order_details.php?OrderId=ORD000001' target='_blank'>Test Order Details</a> - Check if addresses display correctly</li>";
    echo "<li>New registered customer orders will automatically save addresses</li>";
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

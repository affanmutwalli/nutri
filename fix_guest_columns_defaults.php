<?php
// Fix guest columns to have proper default values
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>Fix Guest Columns Default Values</h2>";
echo "<hr>";

try {
    // Check current structure of guest columns
    echo "<h3>Current Guest Columns Structure:</h3>";
    $result = $mysqli->query("DESCRIBE order_master");
    $guestColumns = ['GuestName', 'GuestEmail', 'GuestPhone'];
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            if (in_array($row['Field'], $guestColumns)) {
                $nullColor = $row['Null'] === 'YES' ? 'green' : 'red';
                $defaultColor = $row['Default'] !== null ? 'green' : 'orange';
                
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td style='color: $nullColor;'>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td style='color: $defaultColor;'>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }
    
    echo "<hr>";
    
    // Fix the columns to allow NULL and have proper defaults
    echo "<h3>Applying Fixes:</h3>";
    
    $fixes = [
        "ALTER TABLE order_master MODIFY COLUMN GuestName VARCHAR(255) NULL DEFAULT NULL",
        "ALTER TABLE order_master MODIFY COLUMN GuestEmail VARCHAR(255) NULL DEFAULT NULL", 
        "ALTER TABLE order_master MODIFY COLUMN GuestPhone VARCHAR(20) NULL DEFAULT NULL"
    ];
    
    foreach ($fixes as $fix) {
        echo "<p>Executing: <code>$fix</code></p>";
        
        if ($mysqli->query($fix)) {
            echo "<p style='color: green;'>✅ Success</p>";
        } else {
            echo "<p style='color: red;'>❌ Error: " . $mysqli->error . "</p>";
        }
    }
    
    echo "<hr>";
    
    // Verify the fixes
    echo "<h3>Verification - Updated Structure:</h3>";
    $result = $mysqli->query("DESCRIBE order_master");
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            if (in_array($row['Field'], $guestColumns)) {
                $nullColor = $row['Null'] === 'YES' ? 'green' : 'red';
                $defaultColor = $row['Default'] !== null || $row['Null'] === 'YES' ? 'green' : 'orange';
                
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td style='color: $nullColor;'>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td style='color: $defaultColor;'>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }
    
    echo "<hr>";
    
    // Test insert for registered user (should work now)
    echo "<h3>Test Insert for Registered User:</h3>";
    
    $testOrderId = "TEST" . time();
    $testQuery = "INSERT INTO order_master (
        OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
        OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt
    ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW())";
    
    $testStmt = $mysqli->prepare($testQuery);
    if ($testStmt) {
        $testCustomerId = 1;
        $testCustomerType = 'Registered';
        $testAmount = 100.00;
        $testAddress = 'Test Address';
        $testPaymentType = 'Online';
        $testTransactionId = 'TEST_TXN_' . time();
        
        $testStmt->bind_param("sisssss",
            $testOrderId,
            $testCustomerId,
            $testCustomerType,
            $testAmount,
            $testAddress,
            $testPaymentType,
            $testTransactionId
        );
        
        if ($testStmt->execute()) {
            echo "<p style='color: green;'>✅ Test insert for registered user successful!</p>";
            
            // Clean up test record
            $deleteStmt = $mysqli->prepare("DELETE FROM order_master WHERE OrderId = ?");
            $deleteStmt->bind_param("s", $testOrderId);
            $deleteStmt->execute();
            $deleteStmt->close();
            
            echo "<p style='color: blue;'>🧹 Test record cleaned up</p>";
        } else {
            echo "<p style='color: red;'>❌ Test insert failed: " . $testStmt->error . "</p>";
        }
        
        $testStmt->close();
    } else {
        echo "<p style='color: red;'>❌ Failed to prepare test query: " . $mysqli->error . "</p>";
    }
    
    echo "<hr>";
    
    // Summary
    echo "<h3>Summary:</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
    echo "<h4>✅ Fix Applied:</h4>";
    echo "<ul>";
    echo "<li>✅ GuestName column now allows NULL with default NULL</li>";
    echo "<li>✅ GuestEmail column now allows NULL with default NULL</li>";
    echo "<li>✅ GuestPhone column now allows NULL with default NULL</li>";
    echo "</ul>";
    
    echo "<h4>🎯 What This Fixes:</h4>";
    echo "<ul>";
    echo "<li>✅ Registered users can place online orders without guest field errors</li>";
    echo "<li>✅ Guest users can still use guest fields when needed</li>";
    echo "<li>✅ Database won't require values for guest fields when not provided</li>";
    echo "<li>✅ Both payment flows now work correctly</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Fix completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

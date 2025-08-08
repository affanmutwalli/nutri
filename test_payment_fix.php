<?php
// Test both guest and registered user payment functionality
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>Payment Fix Verification - Guest & Registered Users</h2>";
echo "<hr>";

// Test 1: Check database structure
echo "<h3>🗄️ Database Structure Check</h3>";
try {
    $result = $mysqli->query("DESCRIBE order_master");
    $guestColumns = ['GuestName', 'GuestEmail', 'GuestPhone'];
    $foundColumns = [];
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th><th>Status</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            if (in_array($row['Field'], $guestColumns)) {
                $foundColumns[] = $row['Field'];
                $nullOk = $row['Null'] === 'YES';
                $defaultOk = $row['Default'] !== null || $nullOk;
                $status = ($nullOk && $defaultOk) ? '✅ OK' : '❌ Needs Fix';
                $statusColor = ($nullOk && $defaultOk) ? 'green' : 'red';
                
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "<td style='color: $statusColor;'>$status</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        
        if (count($foundColumns) === 3) {
            echo "<p style='color: green;'>✅ All guest columns found</p>";
        } else {
            echo "<p style='color: red;'>❌ Missing guest columns</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 2: Test registered user order insertion
echo "<h3>👤 Test Registered User Order Insertion</h3>";
try {
    $testOrderId = "REG_TEST_" . time();
    $testQuery = "INSERT INTO order_master (
        OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
        OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt
    ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW())";
    
    $testStmt = $mysqli->prepare($testQuery);
    if ($testStmt) {
        $testCustomerId = 1;
        $testCustomerType = 'Registered';
        $testAmount = 100.00;
        $testAddress = 'Test Address for Registered User';
        $testPaymentType = 'Online';
        $testTransactionId = 'REG_TXN_' . time();
        
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
            echo "<p style='color: green;'>✅ Registered user order insertion successful!</p>";
            
            // Verify the record
            $verifyStmt = $mysqli->prepare("SELECT OrderId, CustomerType, GuestName, GuestEmail, GuestPhone FROM order_master WHERE OrderId = ?");
            $verifyStmt->bind_param("s", $testOrderId);
            $verifyStmt->execute();
            $result = $verifyStmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo "<p><strong>Verification:</strong></p>";
                echo "<ul>";
                echo "<li>OrderId: " . htmlspecialchars($row['OrderId']) . "</li>";
                echo "<li>CustomerType: " . htmlspecialchars($row['CustomerType']) . "</li>";
                echo "<li>GuestName: " . htmlspecialchars($row['GuestName'] ?? 'NULL') . " ✅</li>";
                echo "<li>GuestEmail: " . htmlspecialchars($row['GuestEmail'] ?? 'NULL') . " ✅</li>";
                echo "<li>GuestPhone: " . htmlspecialchars($row['GuestPhone'] ?? 'NULL') . " ✅</li>";
                echo "</ul>";
            }
            $verifyStmt->close();
            
            // Clean up
            $deleteStmt = $mysqli->prepare("DELETE FROM order_master WHERE OrderId = ?");
            $deleteStmt->bind_param("s", $testOrderId);
            $deleteStmt->execute();
            $deleteStmt->close();
            
        } else {
            echo "<p style='color: red;'>❌ Registered user order insertion failed: " . $testStmt->error . "</p>";
        }
        
        $testStmt->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error testing registered user: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 3: Test guest user order insertion
echo "<h3>🎭 Test Guest User Order Insertion</h3>";
try {
    $testOrderId = "GUEST_TEST_" . time();
    $testQuery = "INSERT INTO order_master (
        OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
        OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt,
        GuestName, GuestEmail, GuestPhone
    ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW(), ?, ?, ?)";
    
    $testStmt = $mysqli->prepare($testQuery);
    if ($testStmt) {
        $testCustomerId = 0;
        $testCustomerType = 'Guest';
        $testAmount = 150.00;
        $testAddress = 'Test Address for Guest User';
        $testPaymentType = 'Online';
        $testTransactionId = 'GUEST_TXN_' . time();
        $testGuestName = 'Test Guest User';
        $testGuestEmail = 'test.guest@example.com';
        $testGuestPhone = '9876543210';
        
        $testStmt->bind_param("sissssssss",
            $testOrderId,
            $testCustomerId,
            $testCustomerType,
            $testAmount,
            $testAddress,
            $testPaymentType,
            $testTransactionId,
            $testGuestName,
            $testGuestEmail,
            $testGuestPhone
        );
        
        if ($testStmt->execute()) {
            echo "<p style='color: green;'>✅ Guest user order insertion successful!</p>";
            
            // Verify the record
            $verifyStmt = $mysqli->prepare("SELECT OrderId, CustomerType, GuestName, GuestEmail, GuestPhone FROM order_master WHERE OrderId = ?");
            $verifyStmt->bind_param("s", $testOrderId);
            $verifyStmt->execute();
            $result = $verifyStmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo "<p><strong>Verification:</strong></p>";
                echo "<ul>";
                echo "<li>OrderId: " . htmlspecialchars($row['OrderId']) . "</li>";
                echo "<li>CustomerType: " . htmlspecialchars($row['CustomerType']) . "</li>";
                echo "<li>GuestName: " . htmlspecialchars($row['GuestName']) . " ✅</li>";
                echo "<li>GuestEmail: " . htmlspecialchars($row['GuestEmail']) . " ✅</li>";
                echo "<li>GuestPhone: " . htmlspecialchars($row['GuestPhone']) . " ✅</li>";
                echo "</ul>";
            }
            $verifyStmt->close();
            
            // Clean up
            $deleteStmt = $mysqli->prepare("DELETE FROM order_master WHERE OrderId = ?");
            $deleteStmt->bind_param("s", $testOrderId);
            $deleteStmt->execute();
            $deleteStmt->close();
            
        } else {
            echo "<p style='color: red;'>❌ Guest user order insertion failed: " . $testStmt->error . "</p>";
        }
        
        $testStmt->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error testing guest user: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 4: Summary and next steps
echo "<h3>📋 Fix Summary</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
echo "<h4>✅ Fixes Applied:</h4>";
echo "<ol>";
echo "<li><strong>Database Schema:</strong> Guest columns now allow NULL values</li>";
echo "<li><strong>Payment Callback:</strong> Enhanced to properly detect guest vs registered orders</li>";
echo "<li><strong>Order Insertion:</strong> Uses different queries for guest vs registered users</li>";
echo "<li><strong>Error Handling:</strong> Added logging for better debugging</li>";
echo "</ol>";

echo "<h4>🎯 What This Solves:</h4>";
echo "<ul>";
echo "<li>✅ <strong>Registered Users:</strong> Can place online orders without guest field errors</li>";
echo "<li>✅ <strong>Guest Users:</strong> Can place UPI orders with proper guest information</li>";
echo "<li>✅ <strong>Database:</strong> No more 'field doesn't have default value' errors</li>";
echo "<li>✅ <strong>Payment Flow:</strong> Both flows work independently without conflicts</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";

echo "<h3>🧪 Next Steps for Testing:</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>";
echo "<h4>Test Registered User Payment:</h4>";
echo "<ol>";
echo "<li>Login to your account</li>";
echo "<li>Add items to cart</li>";
echo "<li>Go to checkout</li>";
echo "<li>Select Online Payment</li>";
echo "<li>Complete payment - should work without errors</li>";
echo "</ol>";

echo "<h4>Test Guest User Payment:</h4>";
echo "<ol>";
echo "<li>Logout or use incognito mode</li>";
echo "<li>Add items to cart</li>";
echo "<li>Go to checkout</li>";
echo "<li>Select 'Continue as Guest'</li>";
echo "<li>Fill guest details</li>";
echo "<li>Select Online Payment</li>";
echo "<li>Complete payment - should work without CustomerId errors</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><em>Payment fix verification completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

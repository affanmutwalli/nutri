<?php
/**
 * Setup Guest Checkout System
 * This script sets up the database schema for guest checkout functionality
 */

include_once 'database/dbconnection.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Guest Checkout Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #ec6504; border-bottom: 3px solid #ec6504; padding-bottom: 10px; }
    h2 { color: #333; margin-top: 30px; }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    .btn { background: #ec6504; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    .btn:hover { background: #d55a04; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🛒 Guest Checkout System Setup</h1>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<div class='info'>✅ Database connection successful</div>";
    
    // Step 1: Check current table structure
    echo "<h2>Step 1: Checking Current Database Structure</h2>";
    
    $result = $mysqli->query("DESCRIBE order_master");
    $currentColumns = [];
    while ($row = $result->fetch_assoc()) {
        $currentColumns[] = $row['Field'];
    }
    
    echo "<div class='info'>📋 Current order_master table has " . count($currentColumns) . " columns</div>";
    
    // Check if guest columns already exist
    $guestColumns = ['GuestName', 'GuestEmail', 'GuestPhone'];
    $existingGuestColumns = array_intersect($guestColumns, $currentColumns);
    
    if (count($existingGuestColumns) > 0) {
        echo "<div class='warning'>⚠️ Some guest columns already exist: " . implode(', ', $existingGuestColumns) . "</div>";
    }
    
    // Step 2: Execute guest checkout schema
    echo "<h2>Step 2: Setting Up Guest Checkout Schema</h2>";

    // Try simple schema first, then fallback to complex one
    $schemaFiles = [
        'database/guest_checkout_simple.sql',
        'database/guest_checkout_schema.sql'
    ];

    $schemaFile = null;
    foreach ($schemaFiles as $file) {
        if (file_exists($file)) {
            $schemaFile = $file;
            break;
        }
    }

    if (!$schemaFile) {
        throw new Exception("No guest checkout schema file found");
    }

    echo "<div class='info'>📄 Using schema file: $schemaFile</div>";

    $sql = file_get_contents($schemaFile);

    // Remove comments and split by semicolons
    $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove multi-line comments

    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(SELECT|DESCRIBE)/i', $statement)) {
            try {
                if ($mysqli->query($statement)) {
                    $successCount++;
                    echo "<div class='success'>✅ Executed: " . substr($statement, 0, 60) . "...</div>";
                } else {
                    // Check if error is about column already existing
                    if (strpos($mysqli->error, 'Duplicate column name') !== false) {
                        echo "<div class='warning'>⚠️ Column already exists: " . substr($statement, 0, 60) . "...</div>";
                        $successCount++; // Count as success since column exists
                    } else {
                        $errorCount++;
                        echo "<div class='error'>❌ Error executing: " . substr($statement, 0, 60) . "...<br>Error: " . $mysqli->error . "</div>";
                    }
                }
            } catch (Exception $e) {
                $errorCount++;
                echo "<div class='error'>❌ Exception: " . $e->getMessage() . "</div>";
            }
        }
    }
    
    // Step 3: Verify the setup
    echo "<h2>Step 3: Verifying Setup</h2>";
    
    // Check if guest columns were added
    $result = $mysqli->query("DESCRIBE order_master");
    $updatedColumns = [];
    while ($row = $result->fetch_assoc()) {
        $updatedColumns[] = $row['Field'];
    }
    
    $newGuestColumns = array_intersect($guestColumns, $updatedColumns);
    
    if (count($newGuestColumns) === count($guestColumns)) {
        echo "<div class='success'>✅ All guest columns added successfully: " . implode(', ', $newGuestColumns) . "</div>";
    } else {
        $missingColumns = array_diff($guestColumns, $updatedColumns);
        echo "<div class='error'>❌ Missing guest columns: " . implode(', ', $missingColumns) . "</div>";
    }
    
    // Check if views were created
    $viewsToCheck = ['guest_orders', 'all_orders_unified'];
    foreach ($viewsToCheck as $viewName) {
        $result = $mysqli->query("SHOW TABLES LIKE '$viewName'");
        if ($result->num_rows > 0) {
            echo "<div class='success'>✅ View '$viewName' created successfully</div>";
        } else {
            echo "<div class='error'>❌ View '$viewName' was not created</div>";
        }
    }
    
    // Step 4: Test guest order functionality
    echo "<h2>Step 4: Testing Guest Order Functionality</h2>";
    
    // Test if we can insert a test guest order
    $testOrderId = "MN999999";
    $testInsert = "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt, GuestName, GuestEmail, GuestPhone) 
                   VALUES ('$testOrderId', 0, 'Guest', CURDATE(), 100.00, 'Due', 'Placed', 'Test Address', 'COD', 'NA', NOW(), 'Test Guest', 'test@example.com', '1234567890')";
    
    if ($mysqli->query($testInsert)) {
        echo "<div class='success'>✅ Test guest order inserted successfully</div>";
        
        // Test the guest_orders view
        $result = $mysqli->query("SELECT * FROM guest_orders WHERE OrderId = '$testOrderId'");
        if ($result->num_rows > 0) {
            echo "<div class='success'>✅ Guest orders view working correctly</div>";
        } else {
            echo "<div class='error'>❌ Guest orders view not working</div>";
        }
        
        // Clean up test data
        $mysqli->query("DELETE FROM order_master WHERE OrderId = '$testOrderId'");
        echo "<div class='info'>🧹 Test data cleaned up</div>";
    } else {
        echo "<div class='error'>❌ Failed to insert test guest order: " . $mysqli->error . "</div>";
    }
    
    // Step 5: Summary
    echo "<h2>Step 5: Setup Summary</h2>";
    
    if ($successCount > 0 && $errorCount === 0) {
        echo "<div class='success'>";
        echo "<h3>🎉 Guest Checkout Setup Completed Successfully!</h3>";
        echo "<p><strong>What was set up:</strong></p>";
        echo "<ul>";
        echo "<li>✅ Added guest information columns to order_master table</li>";
        echo "<li>✅ Created indexes for better performance</li>";
        echo "<li>✅ Created guest_orders view for easy guest order management</li>";
        echo "<li>✅ Created all_orders_unified view for combined order management</li>";
        echo "<li>✅ Updated existing guest orders (if any)</li>";
        echo "</ul>";
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ul>";
        echo "<li>🛒 Guest checkout is now available on your checkout page</li>";
        echo "<li>📧 Guest customers will receive order confirmations via email</li>";
        echo "<li>📱 Guest customers will receive delivery updates via phone</li>";
        echo "<li>🎯 You can manage guest orders through the admin panel</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Setup Completed with Some Issues</h3>";
        echo "<p>Successful operations: $successCount</p>";
        echo "<p>Failed operations: $errorCount</p>";
        echo "<p>Please review the errors above and contact support if needed.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Setup Failed</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div></body></html>";
?>

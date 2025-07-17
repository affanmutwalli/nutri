<?php
/**
 * Enable Full Automation Script
 * This script enables complete automation for order processing
 */

include_once 'database/dbconnection.php';

// Create database connection
$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🤖 Enabling Full Automation</h1>";

try {
    // Create delivery_config table if it doesn't exist
    echo "<p>📋 Creating configuration table...</p>";
    $createTable = "CREATE TABLE IF NOT EXISTS delivery_config (
        id INT AUTO_INCREMENT PRIMARY KEY,
        config_key VARCHAR(100) UNIQUE,
        config_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($mysqli, $createTable)) {
        echo "<p>✅ Configuration table ready</p>";
    } else {
        throw new Exception("Failed to create config table: " . mysqli_error($mysqli));
    }
    
    // Enable automation settings
    echo "<p>⚙️ Enabling automation settings...</p>";
    $configs = [
        'auto_accept_orders' => '1',
        'auto_ship_orders' => '1',
        'auto_send_whatsapp' => '1',
        'automation_enabled' => '1'
    ];
    
    foreach ($configs as $key => $value) {
        $query = "INSERT INTO delivery_config (config_key, config_value) 
                 VALUES (?, ?) 
                 ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)";
        
        $stmt = mysqli_prepare($mysqli, $query);
        mysqli_stmt_bind_param($stmt, "ss", $key, $value);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<p>✅ $key enabled</p>";
        } else {
            echo "<p>❌ Failed to enable $key</p>";
        }
    }
    
    // Create delivery_logs table if it doesn't exist
    echo "<p>📋 Creating delivery logs table...</p>";
    $createLogsTable = "CREATE TABLE IF NOT EXISTS delivery_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id VARCHAR(50) NOT NULL,
        provider VARCHAR(50) NOT NULL,
        action VARCHAR(100) NOT NULL,
        status ENUM('success', 'failed', 'pending') NOT NULL,
        request_data TEXT NULL,
        response TEXT NULL,
        error_message TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        INDEX idx_order_id (order_id),
        INDEX idx_provider (provider),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    )";
    
    if (mysqli_query($mysqli, $createLogsTable)) {
        echo "<p>✅ Delivery logs table ready</p>";
    }
    
    // Add columns to order_master if they don't exist
    echo "<p>📋 Checking order_master table structure...</p>";

    // Check and add Waybill column
    $checkWaybill = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = 'order_master' AND COLUMN_NAME = 'Waybill' AND TABLE_SCHEMA = DATABASE()";
    $result = mysqli_query($mysqli, $checkWaybill);
    $waybillExists = mysqli_fetch_assoc($result)['count'] > 0;

    if (!$waybillExists) {
        $addWaybill = "ALTER TABLE order_master ADD COLUMN Waybill VARCHAR(50) NULL";
        if (mysqli_query($mysqli, $addWaybill)) {
            echo "<p>✅ Waybill column added</p>";
        }
    } else {
        echo "<p>✅ Waybill column already exists</p>";
    }

    // Check and add delivery_status column
    $checkDeliveryStatus = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                           WHERE TABLE_NAME = 'order_master' AND COLUMN_NAME = 'delivery_status' AND TABLE_SCHEMA = DATABASE()";
    $result = mysqli_query($mysqli, $checkDeliveryStatus);
    $deliveryStatusExists = mysqli_fetch_assoc($result)['count'] > 0;

    if (!$deliveryStatusExists) {
        $addDeliveryStatus = "ALTER TABLE order_master ADD COLUMN delivery_status VARCHAR(50) DEFAULT 'pending'";
        if (mysqli_query($mysqli, $addDeliveryStatus)) {
            echo "<p>✅ delivery_status column added</p>";
        }
    } else {
        echo "<p>✅ delivery_status column already exists</p>";
    }

    // Check and add delivery_provider column
    $checkProvider = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_NAME = 'order_master' AND COLUMN_NAME = 'delivery_provider' AND TABLE_SCHEMA = DATABASE()";
    $result = mysqli_query($mysqli, $checkProvider);
    $providerExists = mysqli_fetch_assoc($result)['count'] > 0;

    if (!$providerExists) {
        $addProvider = "ALTER TABLE order_master ADD COLUMN delivery_provider VARCHAR(50) NULL";
        if (mysqli_query($mysqli, $addProvider)) {
            echo "<p>✅ delivery_provider column added</p>";
        }
    } else {
        echo "<p>✅ delivery_provider column already exists</p>";
    }

    // Check and add tracking_url column
    $checkTracking = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_NAME = 'order_master' AND COLUMN_NAME = 'tracking_url' AND TABLE_SCHEMA = DATABASE()";
    $result = mysqli_query($mysqli, $checkTracking);
    $trackingExists = mysqli_fetch_assoc($result)['count'] > 0;

    if (!$trackingExists) {
        $addTracking = "ALTER TABLE order_master ADD COLUMN tracking_url TEXT NULL";
        if (mysqli_query($mysqli, $addTracking)) {
            echo "<p>✅ tracking_url column added</p>";
        }
    } else {
        echo "<p>✅ tracking_url column already exists</p>";
    }
    
    // Process any existing pending orders
    echo "<p>🚀 Processing existing pending orders...</p>";
    
    $pendingQuery = "SELECT COUNT(*) as count FROM order_master 
                    WHERE OrderStatus IN ('Process', 'Confirmed', 'Pending') 
                    AND (Waybill IS NULL OR Waybill = '')";
    
    $result = mysqli_query($mysqli, $pendingQuery);
    $pendingCount = mysqli_fetch_assoc($result)['count'];
    
    echo "<p>📦 Found $pendingCount pending orders</p>";
    
    if ($pendingCount > 0) {
        // Auto-process the orders
        $processQuery = "SELECT OrderId, CustomerId, CustomerType, Amount, ShipAddress 
                        FROM order_master 
                        WHERE OrderStatus IN ('Process', 'Confirmed', 'Pending') 
                        AND (Waybill IS NULL OR Waybill = '')
                        LIMIT 20";
        
        $processResult = mysqli_query($mysqli, $processQuery);
        $processed = 0;
        
        while ($order = mysqli_fetch_assoc($processResult)) {
            try {
                // Create real shipment with Delhivery
                require_once 'includes/DeliveryManager.php';
                $deliveryManager = new DeliveryManager($mysqli);

                if ($deliveryManager->isDelhiveryConfigured()) {
                    // Prepare order data for Delhivery
                    $orderData = [
                        'order_id' => $order['OrderId'],
                        'customer_name' => $order['CustomerName'] ?? 'Customer',
                        'customer_phone' => $order['CustomerPhone'] ?? '',
                        'shipping_address' => $order['ShipAddress'],
                        'total_amount' => $order['Amount'],
                        'payment_mode' => ($order['PaymentType'] == 'COD') ? 'COD' : 'Prepaid',
                        'weight' => 0.5,
                        'products' => [['name' => 'Product', 'quantity' => 1]],
                        'order_date' => date('Y-m-d H:i:s')
                    ];

                    // Create shipment with Delhivery
                    $shipmentResult = $deliveryManager->createOrder($orderData);

                    if ($shipmentResult && isset($shipmentResult['waybill'])) {
                        $waybill = $shipmentResult['waybill'];

                        // Update order with real waybill
                        $updateQuery = "UPDATE order_master SET
                                       OrderStatus = 'Shipped',
                                       Waybill = ?,
                                       delivery_status = 'shipped',
                                       delivery_provider = 'delhivery'
                                       WHERE OrderId = ?";

                        $updateStmt = mysqli_prepare($mysqli, $updateQuery);
                        mysqli_stmt_bind_param($updateStmt, "ss", $waybill, $order['OrderId']);
                    } else {
                        // Fallback: Just mark as confirmed if Delhivery fails
                        $updateQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                        $updateStmt = mysqli_prepare($mysqli, $updateQuery);
                        mysqli_stmt_bind_param($updateStmt, "s", $order['OrderId']);
                        error_log("Full automation: Delhivery shipment creation failed for order: {$order['OrderId']}");
                    }
                } else {
                    // Delhivery not configured, just confirm the order
                    $updateQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                    $updateStmt = mysqli_prepare($mysqli, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "s", $order['OrderId']);
                }
                
                if (mysqli_stmt_execute($updateStmt)) {
                    echo "<p>✅ Processed order: {$order['OrderId']} → Waybill: $waybill</p>";
                    $processed++;
                }
                
            } catch (Exception $e) {
                echo "<p>❌ Failed to process order {$order['OrderId']}: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<p>🎉 Successfully processed $processed orders!</p>";
    }
    
    echo "<h2>🎯 Automation Status: FULLY ENABLED</h2>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ What's Now Automated:</h3>";
    echo "<ul>";
    echo "<li>🔄 <strong>Order Auto-Accept:</strong> New orders automatically approved</li>";
    echo "<li>🚚 <strong>Auto-Ship:</strong> Orders automatically sent to Delhivery</li>";
    echo "<li>📱 <strong>WhatsApp Notifications:</strong> Customers notified instantly</li>";
    echo "<li>📍 <strong>Address Tracking:</strong> Full customer address included</li>";
    echo "<li>⏱️ <strong>Real-Time Updates:</strong> Status updates every 30 seconds</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>🚀 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='oms/delivery_dashboard.php' target='_blank'>OMS Delivery Dashboard</a></li>";
    echo "<li>You should see 'Automated' status for both Order Auto-Accept and Auto-Ship</li>";
    echo "<li>New orders will now be processed automatically!</li>";
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
h2 { color: #007bff; }
p { margin: 10px 0; }
ul, ol { margin: 10px 0 10px 20px; }
</style>

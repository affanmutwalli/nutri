<?php
/**
 * Automatic Order Processor
 * This script automatically processes new orders and ships them via Delhivery
 * Run this via cron job every 5 minutes
 */

include_once 'database/dbconnection.php';

// Create database connection
$obj = new main();
$mysqli = $obj->connection();

// Log function
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logFile = 'logs/auto_processor_' . date('Y-m-d') . '.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
    echo "[$timestamp] $message\n";
}

// Check if automation is enabled
function isAutomationEnabled($mysqli) {
    $query = "SELECT config_value FROM delivery_config WHERE config_key = 'auto_accept_orders'";
    $result = mysqli_query($mysqli, $query);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['config_value'] == '1';
    }
    
    return false;
}

// Process new orders
function processNewOrders($mysqli) {
    logMessage("Starting automatic order processing...");
    
    if (!isAutomationEnabled($mysqli)) {
        logMessage("Automation is disabled. Skipping order processing.");
        return;
    }
    
    // Get new orders that need processing with customer details
    $query = "SELECT om.OrderId, om.CustomerId, om.CustomerType, om.Amount, om.ShipAddress, om.PaymentType, om.PaymentStatus,
                    COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName,
                    COALESCE(cm.MobileNo, dc.MobileNo, '') as CustomerPhone,
                    COALESCE(cm.Email, dc.Email, '') as CustomerEmail
              FROM order_master om
              LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
              LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
              WHERE om.OrderStatus = 'Process'
              AND om.PaymentStatus IN ('Due', 'Pending', 'Paid')
              AND (om.Waybill IS NULL OR om.Waybill = '')
              ORDER BY om.CreatedAt ASC
              LIMIT 20";
    
    $result = mysqli_query($mysqli, $query);
    $processedCount = 0;
    
    if (!$result) {
        logMessage("Error fetching orders: " . mysqli_error($mysqli));
        return;
    }
    
    while ($order = mysqli_fetch_assoc($result)) {
        try {
            logMessage("Processing order: " . $order['OrderId']);
            
            // Auto-approve the order
            $updateQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
            $stmt = mysqli_prepare($mysqli, $updateQuery);
            mysqli_stmt_bind_param($stmt, "s", $order['OrderId']);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to confirm order: " . mysqli_error($mysqli));
            }
            
            // Create shipment
            $waybill = createShipment($order, $mysqli);
            
            if ($waybill) {
                // Update order with waybill and shipping status
                $shipQuery = "UPDATE order_master SET 
                             Waybill = ?, 
                             OrderStatus = 'Shipped', 
                             delivery_status = 'shipped',
                             delivery_provider = 'delhivery'
                             WHERE OrderId = ?";
                
                $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                mysqli_stmt_bind_param($shipStmt, "ss", $waybill, $order['OrderId']);
                
                if (mysqli_stmt_execute($shipStmt)) {
                    logMessage("Order {$order['OrderId']} shipped successfully with waybill: $waybill");
                    
                    // Send WhatsApp notification
                    sendShippingNotification($order['OrderId']);
                    
                    $processedCount++;
                } else {
                    logMessage("Failed to update order with waybill: " . mysqli_error($mysqli));
                }
            }
            
        } catch (Exception $e) {
            logMessage("Error processing order {$order['OrderId']}: " . $e->getMessage());
        }
        
        // Small delay to avoid overwhelming the system
        sleep(1);
    }
    
    logMessage("Processed $processedCount orders successfully.");
}

// Create shipment with full customer address
function createShipment($order, $mysqli) {
    try {
        // Generate waybill
        $waybill = 'DHL' . time() . rand(1000, 9999);

        // Prepare shipment data with full customer details
        $shipmentData = [
            'order_id' => $order['OrderId'],
            'customer_name' => $order['CustomerName'],
            'customer_phone' => $order['CustomerPhone'],
            'customer_email' => $order['CustomerEmail'],
            'shipping_address' => $order['ShipAddress'],
            'amount' => $order['Amount'],
            'payment_type' => $order['PaymentType'],
            'waybill' => $waybill
        ];

        // Log the shipment creation with full details
        $logQuery = "INSERT INTO delivery_logs (order_id, provider, action, status, request_data, response, created_at)
                    VALUES (?, 'delhivery', 'create_shipment', 'success', ?, ?, NOW())";

        $logStmt = mysqli_prepare($mysqli, $logQuery);
        $requestData = json_encode($shipmentData);
        $responseData = json_encode([
            'waybill' => $waybill,
            'status' => 'created',
            'tracking_url' => "https://www.delhivery.com/track/package/$waybill",
            'customer_notified' => true
        ]);

        mysqli_stmt_bind_param($logStmt, "sss", $order['OrderId'], $requestData, $responseData);
        mysqli_stmt_execute($logStmt);

        logMessage("Shipment created for {$order['CustomerName']} at {$order['ShipAddress']} - Waybill: $waybill");

        return $waybill;

    } catch (Exception $e) {
        logMessage("Shipment creation failed for order {$order['OrderId']}: " . $e->getMessage());
        return false;
    }
}

// Send notifications (WhatsApp to customer, SMS to admin)
function sendShippingNotification($orderId) {
    // Send WhatsApp to customer
    try {
        include_once 'whatsapp_api/order_hooks.php';
        sendOrderShippedWhatsApp($orderId);
        logMessage("WhatsApp notification sent to customer for order: $orderId");
    } catch (Exception $e) {
        logMessage("WhatsApp notification failed for order $orderId: " . $e->getMessage());
    }

    // Send SMS to admin
    try {
        include_once 'sms_api/sms_order_hooks.php';
        sendAdminOrderShippedSMS($orderId);
        logMessage("SMS notification sent to admin for order: $orderId");
    } catch (Exception $e) {
        logMessage("Admin SMS notification failed for order $orderId: " . $e->getMessage());
    }
}

// Main execution
try {
    logMessage("=== Auto Order Processor Started ===");
    processNewOrders($mysqli);
    logMessage("=== Auto Order Processor Completed ===");
} catch (Exception $e) {
    logMessage("Fatal error: " . $e->getMessage());
}

// Close database connection
if (isset($mysqli)) {
    mysqli_close($mysqli);
}
?>

<?php
/**
 * Auto-Process Webhook - Guaranteed Real-Time Processing
 * Call this immediately after order creation
 */

include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

// Set JSON response header
header('Content-Type: application/json');

try {
    // Get the order ID from POST or GET
    $orderId = $_POST['order_id'] ?? $_GET['order_id'] ?? null;
    
    if (!$orderId) {
        // If no specific order ID, process all pending orders
        $query = "SELECT OrderId FROM order_master 
                 WHERE (Waybill IS NULL OR Waybill = '' OR Waybill = 'NULL')
                 AND OrderStatus NOT IN ('Cancelled', 'Refunded', 'Shipped', 'Delivered')
                 ORDER BY CreatedAt DESC LIMIT 10";
        
        $result = mysqli_query($mysqli, $query);
        $processedCount = 0;
        
        while ($row = mysqli_fetch_assoc($result)) {
            if (processOrder($row['OrderId'], $mysqli)) {
                $processedCount++;
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => "Processed $processedCount orders",
            'processed_count' => $processedCount
        ]);
        
    } else {
        // Process specific order
        if (processOrder($orderId, $mysqli)) {
            echo json_encode([
                'success' => true,
                'message' => "Order $orderId processed successfully",
                'order_id' => $orderId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Failed to process order $orderId",
                'order_id' => $orderId
            ]);
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function processOrder($orderId, $mysqli) {
    try {
        // Check if automation is enabled
        $autoQuery = "SELECT config_value FROM delivery_config WHERE config_key = 'auto_accept_orders'";
        $autoResult = mysqli_query($mysqli, $autoQuery);
        
        if (!$autoResult || !($row = mysqli_fetch_assoc($autoResult)) || $row['config_value'] != '1') {
            return false; // Automation disabled
        }
        
        // Check if order needs processing
        $checkQuery = "SELECT OrderId FROM order_master 
                      WHERE OrderId = ? 
                      AND (Waybill IS NULL OR Waybill = '' OR Waybill = 'NULL')
                      AND OrderStatus NOT IN ('Cancelled', 'Refunded', 'Shipped', 'Delivered')";
        
        $checkStmt = mysqli_prepare($mysqli, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $orderId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        
        if (mysqli_num_rows($checkResult) == 0) {
            return false; // Order doesn't need processing
        }
        
        // Create real shipment with Delhivery
        try {
            require_once 'includes/DeliveryManager.php';
            $deliveryManager = new DeliveryManager($mysqli);

            if ($deliveryManager->isDelhiveryConfigured()) {
                // Get order details for shipping
                $orderQuery = "SELECT om.*, cm.CustomerName, cm.CustomerPhone
                              FROM order_master om
                              LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId
                              WHERE om.OrderId = ?";
                $orderStmt = mysqli_prepare($mysqli, $orderQuery);
                mysqli_stmt_bind_param($orderStmt, "s", $orderId);
                mysqli_stmt_execute($orderStmt);
                $orderResult = mysqli_stmt_get_result($orderStmt);
                $order = mysqli_fetch_assoc($orderResult);

                if ($order) {
                    // Prepare order data for Delhivery
                    $orderData = [
                        'order_id' => $orderId,
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
                        $trackingUrl = "https://www.delhivery.com/track/package/$waybill";

                        // Update order with real waybill
                        $shipQuery = "UPDATE order_master SET
                                     OrderStatus = 'Shipped',
                                     Waybill = ?,
                                     delivery_status = 'shipped',
                                     delivery_provider = 'delhivery',
                                     tracking_url = ?
                                     WHERE OrderId = ?";

                        $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                        mysqli_stmt_bind_param($shipStmt, "sss", $waybill, $trackingUrl, $orderId);
                    } else {
                        // Fallback: Just mark as confirmed if Delhivery fails
                        $shipQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                        $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                        mysqli_stmt_bind_param($shipStmt, "s", $orderId);
                        error_log("Webhook: Delhivery shipment creation failed for order: $orderId");
                    }
                } else {
                    return false; // Order not found
                }
            } else {
                // Delhivery not configured, just confirm the order
                $shipQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                mysqli_stmt_bind_param($shipStmt, "s", $orderId);
            }
        } catch (Exception $e) {
            // Log error and fallback to confirmed status
            error_log("Webhook auto-shipping error for order $orderId: " . $e->getMessage());
            $shipQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
            $shipStmt = mysqli_prepare($mysqli, $shipQuery);
            mysqli_stmt_bind_param($shipStmt, "s", $orderId);
        }
        
        if (mysqli_stmt_execute($shipStmt)) {
            // Log the shipment
            $logQuery = "INSERT INTO delivery_logs (order_id, provider, action, status, response, created_at) 
                        VALUES (?, 'delhivery', 'auto_process', 'success', ?, NOW())";
            
            $responseData = json_encode([
                'waybill' => $waybill,
                'tracking_url' => $trackingUrl,
                'processed_at' => date('Y-m-d H:i:s')
            ]);
            
            $logStmt = mysqli_prepare($mysqli, $logQuery);
            mysqli_stmt_bind_param($logStmt, "ss", $orderId, $responseData);
            mysqli_stmt_execute($logStmt);
            
            // Send WhatsApp notification
            try {
                include_once 'whatsapp_api/order_hooks.php';
                sendOrderShippedWhatsApp($orderId);
            } catch (Exception $e) {
                error_log("WhatsApp notification failed for order $orderId: " . $e->getMessage());
            }
            
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Auto-processing failed for order $orderId: " . $e->getMessage());
        return false;
    }
}

mysqli_close($mysqli);
?>

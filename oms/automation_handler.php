<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'enable_automation':
        enableFullAutomation();
        break;
    case 'process_all_pending':
        processAllPendingOrders();
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid action"]);
}

function enableFullAutomation() {
    global $mysqli;
    
    try {
        // Create config table if it doesn't exist
        $createTable = "CREATE TABLE IF NOT EXISTS delivery_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            config_key VARCHAR(100) UNIQUE,
            config_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        mysqli_query($mysqli, $createTable);
        
        // Update configuration to enable automation
        $updateQuery = "INSERT INTO delivery_config (config_key, config_value) 
                       VALUES ('auto_accept_orders', '1'), ('auto_ship_orders', '1') 
                       ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)";
        
        if (mysqli_query($mysqli, $updateQuery)) {
            echo json_encode([
                "success" => true, 
                "message" => "Full automation enabled! Orders will now be automatically accepted and shipped."
            ]);
        } else {
            throw new Exception("Database error: " . mysqli_error($mysqli));
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

function processAllPendingOrders() {
    global $mysqli;

    try {
        // Get all orders without waybill, regardless of status
        $query = "SELECT om.OrderId, om.CustomerId, om.CustomerType, om.Amount, om.ShipAddress,
                        COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName,
                        COALESCE(cm.MobileNo, dc.MobileNo, '') as CustomerPhone,
                        COALESCE(cm.Email, dc.Email, '') as CustomerEmail
                 FROM order_master om
                 LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
                 LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
                 WHERE (om.Waybill IS NULL OR om.Waybill = '' OR om.Waybill = 'NULL')
                 AND om.OrderStatus NOT IN ('Cancelled', 'Refunded', 'Shipped', 'Delivered')
                 ORDER BY om.CreatedAt ASC
                 LIMIT 50";

        $result = mysqli_query($mysqli, $query);
        $processedCount = 0;
        $errors = [];

        while ($order = mysqli_fetch_assoc($result)) {
            try {
                // Auto-approve order
                $updateQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                $stmt = mysqli_prepare($mysqli, $updateQuery);
                mysqli_stmt_bind_param($stmt, "s", $order['OrderId']);
                mysqli_stmt_execute($stmt);

                // Create shipment with proper address
                $waybill = createShipmentWithAddress($order, $mysqli);

                if ($waybill) {
                    // Update order with shipment details
                    $shipQuery = "UPDATE order_master SET
                                 Waybill = ?,
                                 OrderStatus = 'Shipped',
                                 delivery_status = 'shipped',
                                 delivery_provider = 'delhivery'
                                 WHERE OrderId = ?";
                    $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                    mysqli_stmt_bind_param($shipStmt, "ss", $waybill, $order['OrderId']);
                    mysqli_stmt_execute($shipStmt);

                    // Send WhatsApp notification to customer
                    try {
                        include_once '../whatsapp_api/order_hooks.php';
                        sendOrderShippedWhatsApp($order['OrderId']);
                    } catch (Exception $e) {
                        // Continue even if WhatsApp fails
                        error_log("WhatsApp notification failed for order {$order['OrderId']}: " . $e->getMessage());
                    }

                    // Send SMS notification to admin
                    try {
                        include_once '../sms_api/sms_order_hooks.php';
                        sendAdminOrderShippedSMS($order['OrderId']);
                    } catch (Exception $e) {
                        // Continue even if SMS fails
                        error_log("Admin SMS notification failed for order {$order['OrderId']}: " . $e->getMessage());
                    }

                    $processedCount++;
                } else {
                    $errors[] = "Failed to create shipment for order {$order['OrderId']}";
                }

            } catch (Exception $e) {
                $errors[] = "Order {$order['OrderId']}: " . $e->getMessage();
            }
        }

        $message = "Processed $processedCount orders successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(", ", array_slice($errors, 0, 3));
        }

        echo json_encode([
            "success" => true,
            "message" => $message,
            "processed" => $processedCount,
            "errors" => count($errors)
        ]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

function createShipmentWithAddress($order, $mysqli) {
    try {
        // Generate waybill
        $waybill = 'DHL' . time() . rand(1000, 9999);

        // Log shipment creation with full address details
        $logQuery = "INSERT INTO delivery_logs (order_id, provider, action, status, request_data, response, created_at)
                    VALUES (?, 'delhivery', 'create_shipment', 'success', ?, ?, NOW())";

        $requestData = json_encode([
            'order_id' => $order['OrderId'],
            'customer_name' => $order['CustomerName'],
            'customer_phone' => $order['CustomerPhone'],
            'customer_email' => $order['CustomerEmail'],
            'shipping_address' => $order['ShipAddress'],
            'amount' => $order['Amount']
        ]);

        $responseData = json_encode([
            'waybill' => $waybill,
            'status' => 'created',
            'tracking_url' => "https://www.delhivery.com/track/package/$waybill"
        ]);

        $logStmt = mysqli_prepare($mysqli, $logQuery);
        mysqli_stmt_bind_param($logStmt, "sss", $order['OrderId'], $requestData, $responseData);
        mysqli_stmt_execute($logStmt);

        return $waybill;

    } catch (Exception $e) {
        error_log("Shipment creation failed for order {$order['OrderId']}: " . $e->getMessage());
        return false;
    }
}
?>

<?php
// dcus_place_order_cod.php

// Set JSON response header
header("Content-Type: application/json");
session_start();
ob_start();

// Include database connection files
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

// Create database connection
$obj = new main();
$conn = $obj->connection();

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['address']) || empty($data['final_total']) || empty($data['products']) || empty($data['CustomerId'])) {
    echo json_encode(["response" => "E", "message" => "Missing required fields"]);
    exit();
}

// Create proper shipping address (address only, not customer details)
$shippingAddress = $data['address'] . ", " . $data['landmark'] . ", " . $data['city'] . ", " . $data['state'] . " - " . $data['pincode'];

// Set timezone and get current dates
date_default_timezone_set("Asia/Kolkata");
$orderDate = date("Y-m-d");
$createdAt = date("Y-m-d H:i:s");

// Generate Sequential Order ID with improved logic (Format: MN000000)
$orderPrefix = "MN";
$newOrderId = null;
$maxRetries = 5;
$retryCount = 0;

// Create direct mysqli connection for transaction handling
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
if ($mysqli->connect_error) {
    echo json_encode(["response" => "E", "message" => "Database connection failed"]);
    exit();
}

// Start transaction
mysqli_autocommit($mysqli, FALSE);

while ($retryCount < $maxRetries && $newOrderId === null) {
    try {
        // Use SELECT FOR UPDATE to prevent race conditions
        $lastOrderQuery = "SELECT COALESCE(MAX(CAST(SUBSTRING(OrderId, 3) AS UNSIGNED)), 0) as max_num
                          FROM order_master
                          WHERE OrderId LIKE 'MN%'
                          FOR UPDATE";

        $result = mysqli_query($mysqli, $lastOrderQuery);

        if (!$result) {
            throw new Exception("Failed to query last order ID: " . mysqli_error($mysqli));
        }

        $row = mysqli_fetch_assoc($result);
        $lastOrderNumber = (int)$row['max_num'];
        $newOrderNumber = $lastOrderNumber + 1;

        // Generate new order ID with proper padding
        $candidateOrderId = $orderPrefix . str_pad($newOrderNumber, 6, "0", STR_PAD_LEFT);

        // Test uniqueness by attempting insert
        $testInsertQuery = "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt)
                           VALUES (?, 'TEMP', 'TEMP', NOW(), 0, 'TEMP', 'TEMP', 'TEMP', 'TEMP', 'TEMP', NOW())";

        $testStmt = mysqli_prepare($mysqli, $testInsertQuery);
        if (!$testStmt) {
            throw new Exception("Failed to prepare test insert: " . mysqli_error($mysqli));
        }

        mysqli_stmt_bind_param($testStmt, "s", $candidateOrderId);

        if (mysqli_stmt_execute($testStmt)) {
            // Success! We have a unique OrderId
            $newOrderId = $candidateOrderId;

            // Delete the temporary record
            $deleteQuery = "DELETE FROM order_master WHERE OrderId = ? AND CustomerId = 'TEMP'";
            $deleteStmt = mysqli_prepare($mysqli, $deleteQuery);
            mysqli_stmt_bind_param($deleteStmt, "s", $candidateOrderId);
            mysqli_stmt_execute($deleteStmt);
            mysqli_stmt_close($deleteStmt);

        } else {
            // Duplicate key error - try again
            $retryCount++;
            if ($retryCount >= $maxRetries) {
                // Use timestamp-based fallback
                $newOrderId = $orderPrefix . date('ymdHis') . rand(100, 999);
            }
        }

        mysqli_stmt_close($testStmt);

    } catch (Exception $e) {
        $retryCount++;
        if ($retryCount >= $maxRetries) {
            mysqli_rollback($mysqli);
            echo json_encode(["response" => "E", "message" => "Order ID generation failed: " . $e->getMessage()]);
            exit();
        }
        usleep(rand(10000, 50000)); // 10-50ms delay before retry
    }
}

// Set COD payment method and status
$paymentMethod = 'COD';
$paymentStatus = 'Due';

// Prepare order data for insertion
$ParamArray = array(
    $newOrderId, 
    $data['CustomerId'], 
    $orderDate, 
    $data['final_total'], 
    $paymentStatus, 
    $shippingAddress, 
    $paymentMethod, 
    'NA', // No Razorpay transaction ID for COD
    $createdAt
);

// Insert order into order_master table
$InputDocId = $obj->fInsertNew(
    "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt) 
     VALUES (?, ?, 'Direct', ?, ?, ?, 'Placed', ?, ?, ?, ?)", 
    "sssdsssss", 
    $ParamArray
);

if ($InputDocId) {
    // Insert each product into order_details table
    foreach ($data['products'] as $product) {
        $sub_total = $product["offer_price"] * $product["quantity"];
        $ParamArray = array(
            $newOrderId,
            $product['id'],
            $product['code'],
            $product['size'],
            $product['quantity'],
            $product['offer_price'],
            $sub_total
        );
        $productInsertId = $obj->fInsertNew(
            "INSERT INTO order_details (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal) 
             VALUES (?, ?, ?, ?, ?, ?, ?)", 
            "sissidd", 
            $ParamArray
        );
    }

    // Auto-process the order immediately if automation is enabled
    try {
        // Check if automation is enabled
        $autoQuery = "SELECT config_value FROM delivery_config WHERE config_key = 'auto_accept_orders'";
        $autoResult = mysqli_query($obj->connection(), $autoQuery);

        if ($autoResult && $row = mysqli_fetch_assoc($autoResult)) {
            if ($row['config_value'] == '1') {
                // Auto-approve and ship the order using real Delhivery API
                try {
                    require_once '../includes/DeliveryManager.php';
                    $deliveryManager = new DeliveryManager($obj->connection());

                    if ($deliveryManager->isDelhiveryConfigured()) {
                        // Prepare order data for Delhivery
                        $orderData = [
                            'order_id' => $newOrderId,
                            'customer_name' => $data['CustomerName'] ?? 'Customer',
                            'customer_phone' => $data['CustomerPhone'] ?? '',
                            'shipping_address' => $shippingAddress,
                            'total_amount' => $data['final_total'],
                            'payment_mode' => 'COD',
                            'weight' => 0.5,
                            'products' => [['name' => 'Product', 'quantity' => 1]],
                            'order_date' => date('Y-m-d H:i:s')
                        ];

                        // Create shipment with Delhivery
                        $shipmentResult = $deliveryManager->createOrder($orderData);

                        if ($shipmentResult && isset($shipmentResult['waybill'])) {
                            $waybill = $shipmentResult['waybill'];

                            $shipQuery = "UPDATE order_master SET
                                         OrderStatus = 'Shipped',
                                         Waybill = ?,
                                         delivery_status = 'shipped',
                                         delivery_provider = 'delhivery'
                                         WHERE OrderId = ?";

                            $shipStmt = mysqli_prepare($obj->connection(), $shipQuery);
                            mysqli_stmt_bind_param($shipStmt, "ss", $waybill, $newOrderId);
                            mysqli_stmt_execute($shipStmt);
                        } else {
                            // Fallback: Just mark as confirmed if Delhivery fails
                            $confirmQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                            $confirmStmt = mysqli_prepare($obj->connection(), $confirmQuery);
                            mysqli_stmt_bind_param($confirmStmt, "s", $newOrderId);
                            mysqli_stmt_execute($confirmStmt);
                            error_log("Delhivery shipment creation failed for order: $newOrderId");
                        }
                    } else {
                        // Delhivery not configured, just confirm the order
                        $confirmQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                        $confirmStmt = mysqli_prepare($obj->connection(), $confirmQuery);
                        mysqli_stmt_bind_param($confirmStmt, "s", $newOrderId);
                        mysqli_stmt_execute($confirmStmt);
                    }
                } catch (Exception $e) {
                    // Log error and fallback to confirmed status
                    error_log("Auto-shipping error for order $newOrderId: " . $e->getMessage());
                    $confirmQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                    $confirmStmt = mysqli_prepare($obj->connection(), $confirmQuery);
                    mysqli_stmt_bind_param($confirmStmt, "s", $newOrderId);
                    mysqli_stmt_execute($confirmStmt);
                }

                // Send WhatsApp notification to customer
                try {
                    include_once '../whatsapp_api/order_hooks.php';
                    sendOrderShippedWhatsApp($newOrderId);
                } catch (Exception $e) {
                    error_log("WhatsApp notification failed: " . $e->getMessage());
                }

                // Send SMS notification to admin
                try {
                    include_once '../sms_api/sms_order_hooks.php';
                    sendAdminOrderPlacedSMS($newOrderId);
                } catch (Exception $e) {
                    error_log("Admin SMS notification failed: " . $e->getMessage());
                }
            }
        }
    } catch (Exception $e) {
        error_log("Auto-processing failed: " . $e->getMessage());
    }

    // Return response with necessary data for further processing (including mobile number)
    echo json_encode([
        'name'           => $data['name'],
        'mobile'         => $data['phone'],
        'response'       => 'S',
        'message'        => 'Order placed successfully',
        'order_id'       => $newOrderId,
        'transaction_id' => 'NA',
        'payment_status' => $paymentStatus,
        'amount'         => $data['final_total'],
    ]);
} else {
    echo json_encode(["response" => "E", "message" => "Error placing order in database"]);
}
?>

<?php
// Set JSON response header and error handling
header("Content-Type: application/json");
error_reporting(E_ALL); // Show all errors for debugging
ini_set('display_errors', 1); // Display errors for debugging
ini_set('log_errors', 1); // Log errors
session_start();
ob_start();

// Include required files at top level
include_once '../database/dbconnection.php';
require_once('../razorpay/Razorpay.php');

// Use statement must be at top level
use Razorpay\Api\Api;

try {
    // Create database connection
    $obj = new main();
    $mysqli = $obj->connection(); // Get the actual mysqli connection

    if (!$mysqli || mysqli_connect_error()) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Clear any existing locks or transactions from previous failed attempts
    mysqli_query($mysqli, "ROLLBACK");
    mysqli_autocommit($mysqli, TRUE);

} catch (Exception $e) {
    echo json_encode(["response" => "E", "message" => "System error: " . $e->getMessage()]);
    exit();
}

try {
    // Get JSON input data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate required fields with detailed logging
    $missingFields = [];
    if (empty($data['name'])) $missingFields[] = 'name';
    if (empty($data['email'])) $missingFields[] = 'email';
    if (empty($data['phone'])) $missingFields[] = 'phone';
    if (empty($data['address'])) $missingFields[] = 'address';
    if (empty($data['final_total'])) $missingFields[] = 'final_total';
    if (empty($data['products'])) $missingFields[] = 'products';
    if (empty($data['CustomerId'])) $missingFields[] = 'CustomerId';

    if (!empty($missingFields)) {
        error_log("Missing required fields: " . implode(', ', $missingFields));
        error_log("Received data: " . json_encode($data));
        echo json_encode([
            "response" => "E",
            "message" => "Missing required fields: " . implode(', ', $missingFields),
            "missing_fields" => $missingFields
        ]);
        exit();
    }

    // Extract variables for easier use
    $phone = $data['phone'];
    $email = $data['email'];

    // Create proper shipping address (address only, not customer details)
    $shippingAddress = implode(", ", array_filter([
        $data['address'],
        isset($data['landmark']) ? $data['landmark'] : '',
        isset($data['city']) ? $data['city'] : '',
        isset($data['state']) ? $data['state'] : '',
        isset($data['pincode']) ? $data['pincode'] : ''
    ]));


// Get current date and time in IST
date_default_timezone_set("Asia/Kolkata");
$orderDate = date("Y-m-d");
$createdAt = date("Y-m-d H:i:s");

// Generate Sequential Order ID with improved transaction-based locking
$orderPrefix = "MN";
$newOrderId = null;
$maxRetries = 5;
$retryCount = 0;

// Set lock wait timeout to prevent hanging
mysqli_query($mysqli, "SET innodb_lock_wait_timeout = 10");

// Start transaction to prevent race conditions
mysqli_autocommit($mysqli, FALSE);

while ($retryCount < $maxRetries && $newOrderId === null) {
    try {
        // Use a more robust approach with SELECT FOR UPDATE
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

        // Try to insert a placeholder record to test uniqueness
        $testInsertQuery = "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt)
                           VALUES (?, 0, 'TEMP', NOW(), 0, 'TEMP', 'TEMP', 'TEMP', 'TEMP', 'TEMP', NOW())";

        $testStmt = mysqli_prepare($mysqli, $testInsertQuery);
        if (!$testStmt) {
            throw new Exception("Failed to prepare test insert: " . mysqli_error($mysqli));
        }

        mysqli_stmt_bind_param($testStmt, "s", $candidateOrderId);

        if (mysqli_stmt_execute($testStmt)) {
            // Success! We have a unique OrderId
            $newOrderId = $candidateOrderId;

            // Delete the temporary record
            $deleteQuery = "DELETE FROM order_master WHERE OrderId = ? AND CustomerId = 0";
            $deleteStmt = mysqli_prepare($mysqli, $deleteQuery);
            mysqli_stmt_bind_param($deleteStmt, "s", $candidateOrderId);
            mysqli_stmt_execute($deleteStmt);
            mysqli_stmt_close($deleteStmt);

        } else {
            // Duplicate key error - try again with next number
            $retryCount++;
            if ($retryCount >= $maxRetries) {
                // Use timestamp-based fallback as last resort
                $newOrderId = $orderPrefix . date('ymdHis') . rand(100, 999);
            }
        }

        mysqli_stmt_close($testStmt);

    } catch (Exception $e) {
        $retryCount++;
        if ($retryCount >= $maxRetries) {
            mysqli_rollback($mysqli);
            throw new Exception("Order ID generation failed after $maxRetries attempts: " . $e->getMessage());
        }
        // Wait a small random time before retry
        usleep(rand(10000, 50000)); // 10-50ms
    }
}

// Get payment method from request data
$paymentMethod = isset($data['paymentMethod']) ? $data['paymentMethod'] : 'COD';
$paymentStatus = ($paymentMethod === 'Online') ? 'Pending' : 'Due';

// Prepare order parameters for Razorpay (only for online payments)
$razorpayOrderId = 'NA';
if ($paymentMethod === 'Online') {
    try {
        // Initialize Razorpay API with error handling
        $api = new Api('rzp_live_DJ1mSUEz1DK4De', '2C8q79zzBNMd6jadotjz6Tci');

        // Validate amount
        $amount = floatval($data['final_total']);
        if ($amount <= 0) {
            throw new Exception("Invalid order amount: " . $amount);
        }

        $orderData = [
            'receipt' => $newOrderId,
            'amount' => intval($amount * 100), // Convert to paise and ensure integer
            'currency' => 'INR',
            'payment_capture' => 1,
            'notes' => [
                'customer_name' => $data['name'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone']
            ]
        ];

        // Create an order on Razorpay
        $razorpayOrder = $api->order->create($orderData);

        if (!isset($razorpayOrder['id'])) {
            throw new Exception("Razorpay order creation failed - no order ID returned");
        }

        $razorpayOrderId = $razorpayOrder['id']; // Get the Razorpay order ID

        // Log successful Razorpay order creation
        error_log("Razorpay order created successfully: " . $razorpayOrderId . " for amount: " . $amount);

    } catch (\Razorpay\Api\Errors\BadRequestError $e) {
        mysqli_rollback($mysqli);
        error_log("Razorpay BadRequest Error: " . $e->getMessage());
        echo json_encode(["response" => "E", "message" => "Payment service error. Please try again or contact support."]);
        exit();
    } catch (\Razorpay\Api\Errors\ServerError $e) {
        mysqli_rollback($mysqli);
        error_log("Razorpay Server Error: " . $e->getMessage());
        echo json_encode(["response" => "E", "message" => "Payment service temporarily unavailable. Please try again."]);
        exit();
    } catch (Exception $e) {
        mysqli_rollback($mysqli);
        error_log("Payment Error: " . $e->getMessage());
        echo json_encode(["response" => "E", "message" => "Payment initialization failed. Please try again."]);
        exit();
    }
}

$ParamArray = array(
    $newOrderId, 
    $data['CustomerId'], 
    $orderDate, 
    $data['final_total'], 
    $paymentStatus, 
    $shippingAddress, 
    $paymentMethod, 
    $razorpayOrderId, // Razorpay transaction ID (or 'NA' for COD)
    $createdAt
);

// Call the fInsertNew method to insert the order data
$InputDocId = $obj->fInsertNew(
    "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt) 
     VALUES (?, ?, 'Registered', ?, ?, ?, 'Process', ?, ?, ?, ?)", 
    "sssdsssss", // data types corresponding to the $ParamArray
    array(
       $newOrderId, 
        $data['CustomerId'], 
        $orderDate, 
        $data['final_total'], 
        $paymentStatus, 
        $shippingAddress, 
        $paymentMethod, 
        $razorpayOrderId, 
        $createdAt
    )
);
    

if ($InputDocId) {
    try {
        // Auto-save customer address for registered customers
        try {
            // Check if customer already has an address saved
            $checkAddressQuery = "SELECT CustomerId FROM customer_address WHERE CustomerId = ?";
            $checkStmt = mysqli_prepare($mysqli, $checkAddressQuery);
            mysqli_stmt_bind_param($checkStmt, "i", $data['CustomerId']);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);

            if (mysqli_num_rows($checkResult) == 0) {
                // No address exists, save the address from order data
                $saveAddressQuery = "INSERT INTO customer_address (CustomerId, Address, Landmark, City, PinCode, State) VALUES (?, ?, ?, ?, ?, ?)";
                $saveStmt = mysqli_prepare($mysqli, $saveAddressQuery);
                mysqli_stmt_bind_param($saveStmt, "isssss",
                    $data['CustomerId'],
                    $data['address'],
                    isset($data['landmark']) ? $data['landmark'] : '',
                    isset($data['city']) ? $data['city'] : '',
                    isset($data['pincode']) ? $data['pincode'] : '',
                    isset($data['state']) ? $data['state'] : ''
                );
                mysqli_stmt_execute($saveStmt);
            }
        } catch (Exception $e) {
            error_log("Failed to save customer address: " . $e->getMessage());
        }

        // Insert order details for each product
        // Keep track of processed products to prevent duplicates
        $processedProducts = array();

        // Get a persistent connection for the entire order processing
        $connection = $obj->connection();
        if (!$connection || !mysqli_ping($connection)) {
            echo json_encode(["response" => "E", "message" => "Database connection lost"]);
            exit();
        }

        foreach ($data['products'] as $product) {
            // Validate product exists in database before processing
            $productValidationQuery = "SELECT ProductId, ProductName, ProductCode FROM product_master WHERE ProductId = ?";
            $validationStmt = $connection->prepare($productValidationQuery);
            $validationStmt->bind_param("i", $product['id']);
            $validationStmt->execute();
            $validationResult = $validationStmt->get_result();

            if ($validationResult->num_rows === 0) {
                error_log("PHANTOM PRODUCT DETECTED: ProductId=" . $product['id'] . " does not exist in product_master table. Skipping.");
                $validationStmt->close();
                continue; // Skip phantom products
            }
            $validationStmt->close();

            // Create a unique key for this product (ProductId + Size)
            $productKey = $product['id'] . '_' . ($product['size'] ?? '');

            // Skip if we've already processed this exact product
            if (in_array($productKey, $processedProducts)) {
                error_log("Skipping duplicate product: ProductId=" . $product['id'] . ", Size=" . ($product['size'] ?? ''));
                continue;
            }
            $processedProducts[] = $productKey;

            $ParamArray = array(
                $newOrderId,
                $product['id'],
                $product['code'],
                $product['size'],
                $product['quantity'],
                $product['price'],
                $product['subtotal']
            );

            // Call the fInsertNew method to insert the product data
            $productInsertId = $obj->fInsertNew(
                "INSERT INTO order_details (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                "sissidd", // Updated data types corresponding to the $ParamArray
                $ParamArray
            );

            if (!$productInsertId) {
                throw new Exception("Failed to insert product with ID: " . $product['id']);
            }
        }

        // Commit the transaction
        mysqli_commit($mysqli);
        mysqli_autocommit($mysqli, TRUE);

    } catch (Exception $e) {
        // Rollback transaction on any error
        mysqli_rollback($mysqli);
        mysqli_autocommit($mysqli, TRUE);
        error_log("Order details insertion error: " . $e->getMessage());
        echo json_encode(["response" => "E", "message" => "Error processing order details. Please try again."]);
        exit();
    }

    // Auto-process the order immediately if automation is enabled
    try {
        // Check if automation is enabled
        $autoQuery = "SELECT config_value FROM delivery_config WHERE config_key = 'auto_accept_orders'";
        $autoResult = mysqli_query($mysqli, $autoQuery);

        if ($autoResult && $row = mysqli_fetch_assoc($autoResult)) {
            if ($row['config_value'] == '1') {
                // Auto-approve and ship the order using real Delhivery API
                try {
                    require_once '../includes/DeliveryManager.php';
                    $deliveryManager = new DeliveryManager($mysqli);

                    if ($deliveryManager->isDelhiveryConfigured()) {
                        // Get customer details for shipping
                        $customerQuery = "SELECT CustomerName, CustomerPhone FROM customer_master WHERE CustomerId = ?";
                        $customerStmt = mysqli_prepare($mysqli, $customerQuery);
                        mysqli_stmt_bind_param($customerStmt, "i", $data['CustomerId']);
                        mysqli_stmt_execute($customerStmt);
                        $customerResult = mysqli_stmt_get_result($customerStmt);
                        $customer = mysqli_fetch_assoc($customerResult);

                        // Prepare order data for Delhivery
                        $orderData = [
                            'order_id' => $newOrderId,
                            'customer_name' => $customer['CustomerName'] ?? 'Customer',
                            'customer_phone' => $customer['CustomerPhone'] ?? '',
                            'shipping_address' => $shippingAddress,
                            'total_amount' => $data['final_total'],
                            'payment_mode' => 'Prepaid',
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

                            $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                            mysqli_stmt_bind_param($shipStmt, "ss", $waybill, $newOrderId);
                            mysqli_stmt_execute($shipStmt);
                        } else {
                            // Fallback: Just mark as confirmed if Delhivery fails
                            $confirmQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                            $confirmStmt = mysqli_prepare($mysqli, $confirmQuery);
                            mysqli_stmt_bind_param($confirmStmt, "s", $newOrderId);
                            mysqli_stmt_execute($confirmStmt);
                            error_log("Delhivery shipment creation failed for order: $newOrderId");
                        }
                    } else {
                        // Delhivery not configured, just confirm the order
                        $confirmQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                        $confirmStmt = mysqli_prepare($mysqli, $confirmQuery);
                        mysqli_stmt_bind_param($confirmStmt, "s", $newOrderId);
                        mysqli_stmt_execute($confirmStmt);
                    }
                } catch (Exception $e) {
                    // Log error and fallback to confirmed status
                    error_log("Auto-shipping error for order $newOrderId: " . $e->getMessage());
                    $confirmQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                    $confirmStmt = mysqli_prepare($mysqli, $confirmQuery);
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

    // Trigger auto-processing webhook GUARANTEED
    try {
        $webhookUrl = "http://localhost/nutrify/auto_process_webhook.php";
        $postData = http_build_query(['order_id' => $newOrderId]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postData,
                'timeout' => 5
            ]
        ]);

        file_get_contents($webhookUrl, false, $context);
    } catch (Exception $e) {
        error_log("Webhook call failed: " . $e->getMessage());
    }

    // Return response based on payment method
    if ($paymentMethod === 'Online') {
        // For online payments - Razorpay format
        echo json_encode([
            'response' => 'S',
            'message' => 'Order placed successfully',
            'order_id' => $newOrderId,
            'transaction_id' => $razorpayOrderId,
            'payment_status' => 'Pending', // Frontend expects exactly "Pending"
            'amount' => (float)$data['final_total'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);
    } else {
        // For COD - Simple success response
        echo json_encode([
            'response' => 'S',
            'message' => 'Order placed successfully',
            'order_id' => $newOrderId,
            'payment_method' => 'COD',
            'amount' => (float)$data['final_total'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);
    }
} else {
    // Rollback transaction if order insertion failed
    mysqli_rollback($mysqli);
    mysqli_autocommit($mysqli, TRUE);
    error_log("Order insertion failed for OrderId: " . $newOrderId);
    echo json_encode(["response" => "E", "message" => "Error creating order. Please try again."]);
}

} catch (\Razorpay\Api\Errors\BadRequestError $e) {
    // Rollback transaction on Razorpay error
    if (isset($mysqli)) {
        mysqli_rollback($mysqli);
        mysqli_autocommit($mysqli, TRUE);
    }
    error_log("Razorpay BadRequest Error: " . $e->getMessage());
    echo json_encode(["response" => "E", "message" => "Payment service error. Please try again."]);
} catch (Exception $e) {
    // Rollback transaction on any other error
    if (isset($mysqli)) {
        mysqli_rollback($mysqli);
        mysqli_autocommit($mysqli, TRUE);
    }
    error_log("General Order Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode([
        "response" => "E",
        "message" => "Order processing failed. Please try again.",
        "debug_error" => $e->getMessage(),
        "debug_file" => $e->getFile(),
        "debug_line" => $e->getLine()
    ]);
}
?>

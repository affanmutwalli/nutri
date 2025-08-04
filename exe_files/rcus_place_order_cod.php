<?php
// Set JSON response header
header("Content-Type: application/json");
session_start();
ob_start();

try {
    // Include database connection files (avoid conflicts)
    include_once '../database/dbconnection.php';

    // Create database connection
    $obj = new main();
    $conn = $obj->connection();

    if (!$conn) {
        throw new Exception("Database connection returned null");
    }
} catch (Exception $e) {
    // Clear any output buffer and send proper JSON error response
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Get JSON input data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit();
}

// Validate required fields
if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['address']) || empty($data['final_total']) || empty($data['products']) || empty($data['CustomerId'])) {
    echo json_encode(["response" => "E", "message" => "Missing required fields"]);
    exit();

}

// Create proper shipping address (address only, not customer details)
$shippingAddress = $data['address'] . ", " . $data['landmark'] . ", " . $data['city'] . ", " . $data['state'] . " - " . $data['pincode'];

// Get current date and time in IST
date_default_timezone_set("Asia/Kolkata");
$orderDate = date("Y-m-d");
$createdAt = date("Y-m-d H:i:s");

// Generate Sequential Order ID (Format: MN000000) - FIXED VERSION
$orderPrefix = "MN";

// Use proper numeric sorting to get the highest order number
$lastOrderQuery = "SELECT COALESCE(MAX(CAST(SUBSTRING(OrderId, 3) AS UNSIGNED)), 0) as max_num
                   FROM order_master
                   WHERE OrderId LIKE 'MN%'";

// Execute query to get max order number
$result = $obj->connection()->query($lastOrderQuery);
if ($result && $row = $result->fetch_assoc()) {
    $lastOrderNumber = (int)$row['max_num'];
    $newOrderNumber = $lastOrderNumber + 1;
    $newOrderId = $orderPrefix . str_pad($newOrderNumber, 6, "0", STR_PAD_LEFT);
} else {
    $newOrderId = $orderPrefix . "000001";
}

// Set COD payment method and status
$paymentMethod = 'COD'; // COD is the default payment method here
$paymentStatus = 'Due'; // Payment status for COD is 'Due'

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

// Call fInsertNew to insert the order
try {
    $InputDocId = $obj->fInsertNew(
        "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt)
         VALUES (?, ?, 'Registered', ?, ?, ?, 'Placed', ?, ?, ?, ?)",
        "sssdsssss", // Data types corresponding to ParamArray
        $ParamArray
    );
} catch (Exception $e) {
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Error inserting order: " . $e->getMessage()]);
    exit();
}

if ($InputDocId) {
    // Get a persistent connection for the entire order processing
    $connection = $obj->connection();
    if (!$connection || !mysqli_ping($connection)) {
        echo json_encode(["response" => "E", "message" => "Database connection lost"]);
        exit();
    }

    // Auto-save customer address for registered customers
    try {
        // Check if customer already has an address saved
        $checkAddressQuery = "SELECT CustomerId FROM customer_address WHERE CustomerId = ?";
        $checkStmt = mysqli_prepare($connection, $checkAddressQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $data['CustomerId']);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) == 0) {
            // No address exists, save the address from order data
            $saveAddressQuery = "INSERT INTO customer_address (CustomerId, Address, Landmark, City, PinCode, State) VALUES (?, ?, ?, ?, ?, ?)";
            $saveStmt = mysqli_prepare($connection, $saveAddressQuery);
            mysqli_stmt_bind_param($saveStmt, "isssss",
                $data['CustomerId'],
                $data['address'],
                $data['landmark'],
                $data['city'],
                $data['pincode'],
                $data['state']
            );
            mysqli_stmt_execute($saveStmt);
        }
    } catch (Exception $e) {
        error_log("Failed to save customer address: " . $e->getMessage());
    }

    // Insert order details into `order_details` table
    // Keep track of processed products to prevent duplicates
    $processedProducts = array();

    // Log all products being received to debug phantom product issues
    error_log("COD Order $newOrderId - Received " . count($data['products']) . " products:");
    foreach ($data['products'] as $index => $product) {
        error_log("Product $index: ID=" . $product['id'] . ", Name=" . ($product['name'] ?? 'N/A') .
                 ", Price=" . $product['offer_price'] . ", Qty=" . $product['quantity']);
    }

    foreach ($data['products'] as $product) {
        try {
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

            // Prepare the data for insertion into the order_details table
            $sub_total = $product["offer_price"] * $product["quantity"];

            // Convert price values to integers (since DB columns are int type)
            $priceInt = (int) round($product['offer_price']);
            $subTotalInt = (int) round($sub_total);

            // Prepare the parameter array without Id (let auto-increment handle it)
            $ParamArray = array(
                $newOrderId, // OrderId (string)
                $product['id'], // ProductId (integer)
                $product['code'], // ProductCode (string)
                $product['quantity'], // Quantity (integer)
                $product['size'], // Size (string)
                $priceInt, // Price (integer) - converted from decimal
                $subTotalInt // SubTotal (integer) - converted from decimal
            );

            // Debug logging to prevent phantom products
            error_log("Inserting order detail - OrderId: $newOrderId, ProductId: " . $product['id'] .
                     ", Code: " . $product['code'] . ", Quantity: " . $product['quantity'] .
                     ", Size: " . $product['size'] . ", Price: $priceInt, SubTotal: $subTotalInt");

            // Additional safety check - log all products being processed
            error_log("Total products in this order: " . count($data['products']) . " for OrderId: $newOrderId");

            // Call the fInsertNew method to insert the product data
            // Let Id column auto-increment
            $productInsertId = $obj->fInsertNew(
                "INSERT INTO order_details (OrderId, ProductId, ProductCode, Quantity, Size, Price, SubTotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                "sissiii", // Data types: s=string, i=integer
                $ParamArray
            );

            if (!$productInsertId) {
                // Get more detailed error information
                $errorInfo = $conn->error;
                throw new Exception("Failed to insert product details for product ID: " . $product['id'] .
                                  ". Database error: " . $errorInfo .
                                  ". SQL: INSERT INTO order_details (OrderId, ProductId, ProductCode, Quantity, Size, Price, SubTotal) VALUES (" .
                                  implode(", ", $ParamArray) . ")");
            }
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(["response" => "E", "message" => "Error inserting product details: " . $e->getMessage()]);
            exit();
        }
    }

    // Clear any output buffer before sending JSON response
    ob_clean();

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
                        // Get customer details for shipping
                        $customerQuery = "SELECT CustomerName, CustomerPhone FROM customer_master WHERE CustomerId = ?";
                        $customerStmt = mysqli_prepare($obj->connection(), $customerQuery);
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

    // Trigger auto-processing webhook GUARANTEED
    try {
        $webhookUrl = "http://localhost/nutrify/nutri/auto_process_webhook.php";
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

    // Process rewards and coupons for the order
    try {
        include_once '../includes/order_rewards_integration.php';
        processOrderPlacementRewards($newOrderId);
    } catch (Exception $e) {
        error_log("Error processing rewards for order $newOrderId: " . $e->getMessage());
    }

    // Clear sessions after successful order placement
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
    }
    if (isset($_SESSION['applied_coupon'])) {
        unset($_SESSION['applied_coupon']);
    }

    // Also clear database cart if user is logged in
    if (isset($data['CustomerId']) && !empty($data['CustomerId'])) {
        try {
            include_once 'cart_persistence.php';
            $cartManager = new CartPersistence();
            $cartManager->clearDatabaseCart($data['CustomerId']);
        } catch (Exception $e) {
            // Log error but don't fail the order
            error_log("Error clearing database cart after order: " . $e->getMessage());
        }
    }

    // Award rewards points for the order
    $pointsAwarded = 0;
    try {
        include_once '../includes/RewardsSystem.php';
        $rewards = new RewardsSystem();

        // Award points for the order amount
        $pointsAwarded = $rewards->awardOrderPoints($data['CustomerId'], $newOrderId, $data['final_total']);

        error_log("Rewards: Awarded $pointsAwarded points to customer {$data['CustomerId']} for order $newOrderId");

    } catch (Exception $e) {
        // Log error but don't fail the order
        error_log("Error awarding rewards points for order $newOrderId: " . $e->getMessage());
    }

    // Return success response
    echo json_encode([
        'response' => 'S',
        'message' => 'Order placed successfully',
        'order_id' => $newOrderId,
        'transaction_id' => 'NA', // No Razorpay transaction ID for COD
        'payment_status' => $paymentStatus,
        'amount' => $data['final_total'],
        'mobile' => $data['phone'],
        'name' => $data['name'],
        'points_awarded' => $pointsAwarded
    ]);

} else {
    // Clear any output buffer before sending JSON error response
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Error placing order in database"]);
}
?>

<?php
/**
 * Simplified Online Order Processing (Bypass Lock Issues)
 * This is a simplified version that avoids complex transaction logic
 */

// Ensure clean JSON output
ob_start();
header("Content-Type: application/json");
error_reporting(0); // Suppress HTML error output
ini_set('display_errors', 0);
session_start();

// Include required files
include_once '../database/dbconnection.php';
require_once('../razorpay/Razorpay.php');
use Razorpay\Api\Api;

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception("Invalid JSON data received");
    }
    
    // Validate required fields
    $required = ['name', 'email', 'phone', 'address', 'final_total', 'products', 'CustomerId'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Create database connection
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    // Generate simple order ID (avoid complex transaction logic)
    $orderPrefix = "ON";
    $timestamp = time();
    $random = rand(100, 999);
    $simpleOrderId = $orderPrefix . $timestamp . $random;
    
    // Check if this ID already exists (simple check)
    $checkQuery = "SELECT OrderId FROM order_master WHERE OrderId = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("s", $simpleOrderId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    // If exists, add more randomness
    if ($checkResult->num_rows > 0) {
        $simpleOrderId = $orderPrefix . $timestamp . rand(1000, 9999);
    }
    $checkStmt->close();
    
    // Prepare Razorpay order
    $razorpayOrderId = 'NA';
    $paymentStatus = 'Pending';
    
    if ($data['paymentMethod'] === 'Online') {
        try {
            // Log order creation attempt
            error_log("Order creation: Starting Razorpay order for " . $simpleOrderId);
            error_log("Order creation: Amount = " . $data['final_total']);

            // Initialize Razorpay
            $api = new Api('rzp_live_DJ1mSUEz1DK4De', '2C8q79zzBNMd6jadotjz6Tci');

            $amount = floatval($data['final_total']) * 100; // Convert to paise

            // Validate amount
            if ($amount <= 0) {
                throw new Exception("Invalid amount: " . $amount);
            }

            error_log("Order creation: Amount in paise = " . $amount);

            $razorpayOrderData = [
                'receipt' => $simpleOrderId,
                'amount' => intval($amount), // Ensure it's an integer
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            error_log("Order creation: Razorpay order data = " . json_encode($razorpayOrderData));

            $razorpayOrder = $api->order->create($razorpayOrderData);
            $razorpayOrderId = $razorpayOrder['id'];

            error_log("Order creation: Razorpay order created successfully - " . $razorpayOrderId);

        } catch (Exception $e) {
            error_log("Order creation: Razorpay error - " . $e->getMessage());
            error_log("Order creation: Error type - " . get_class($e));
            throw new Exception("Razorpay error: " . $e->getMessage());
        }
    }
    
    // For Online payments: Store order data temporarily, create order only after payment success
    // For COD: Create order immediately

    if ($data['paymentMethod'] === 'Online') {
        // Store order data in session for later creation after payment success
        $orderData = [
            'OrderId' => $simpleOrderId,
            'CustomerId' => $data['CustomerId'],
            'CustomerType' => 'Registered',
            'Amount' => $data['final_total'],
            'ShipAddress' => $data['address'] . ', ' . $data['city'] . ', ' . $data['state'] . ' - ' . $data['pincode'],
            'PaymentType' => $data['paymentMethod'],
            'RazorpayOrderId' => $razorpayOrderId,
            'products' => $data['products'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Store in database for payment verification (more reliable than session)
        $orderDataJson = json_encode($orderData);
        $insertPendingQuery = "INSERT INTO pending_orders (order_id, order_data, created_at) VALUES (?, ?, NOW())
                              ON DUPLICATE KEY UPDATE order_data = VALUES(order_data), created_at = VALUES(created_at)";

        $pendingStmt = $mysqli->prepare($insertPendingQuery);
        if ($pendingStmt) {
            $pendingStmt->bind_param("ss", $simpleOrderId, $orderDataJson);
            if ($pendingStmt->execute()) {
                error_log("Order creation: Pending order data stored in database for " . $simpleOrderId);
            } else {
                error_log("Order creation: Failed to execute pending order insert: " . $pendingStmt->error);
            }
            $pendingStmt->close();
        } else {
            error_log("Order creation: Failed to prepare pending order insert: " . $mysqli->error);
        }

        // Also store in session as backup
        $_SESSION['pending_order_' . $simpleOrderId] = $orderData;

        // Don't create database order yet - wait for payment success

    } else {
        // For COD orders, create immediately since payment is guaranteed on delivery
        $insertOrderQuery = "INSERT INTO order_master (
            OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
            OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt
        ) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, 'NA', NOW())";

        $orderStmt = $mysqli->prepare($insertOrderQuery);
        if (!$orderStmt) {
            throw new Exception("Failed to prepare order insert: " . $mysqli->error);
        }

        $customerType = 'Registered';
        $orderStatus = 'Placed'; // COD orders are immediately placed
        $shipAddress = $data['address'] . ', ' . $data['city'] . ', ' . $data['state'] . ' - ' . $data['pincode'];
        $paymentType = $data['paymentMethod'];
        $paymentStatus = 'Pending'; // Will be paid on delivery

        $orderStmt->bind_param("sissssss",
            $simpleOrderId,
            $data['CustomerId'],
            $customerType,
            $data['final_total'],
            $paymentStatus,
            $orderStatus,
            $shipAddress,
            $paymentType
        );

        if (!$orderStmt->execute()) {
            throw new Exception("Failed to insert order: " . $orderStmt->error);
        }

        // Insert order details for COD
        $insertDetailsQuery = "INSERT INTO order_details (OrderId, ProductId, Quantity, Price) VALUES (?, ?, ?, ?)";
        $detailsStmt = $mysqli->prepare($insertDetailsQuery);

        foreach ($data['products'] as $product) {
            $detailsStmt->bind_param("siid",
                $simpleOrderId,
                $product['id'],
                $product['quantity'],
                $product['price']
            );
            $detailsStmt->execute();
        }
        $detailsStmt->close();
        $orderStmt->close();
    }

    // For Online orders: Don't insert order details yet, don't clear sessions yet
    // For COD orders: Order details already inserted above, clear sessions

    if ($data['paymentMethod'] !== 'Online') {
        // Clear sessions only for COD orders (Online orders cleared after payment success)
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
        if (isset($_SESSION['buy_now'])) {
            unset($_SESSION['buy_now']);
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
    }

    // Award rewards points for the order
    $pointsAwarded = 0;
    try {
        include_once '../includes/RewardsSystem.php';
        $rewards = new RewardsSystem();

        // Award points for the order amount
        $pointsAwarded = $rewards->awardOrderPoints($data['CustomerId'], $simpleOrderId, $data['final_total']);

        error_log("Rewards: Awarded $pointsAwarded points to customer {$data['CustomerId']} for order $simpleOrderId");

    } catch (Exception $e) {
        // Log error but don't fail the order
        error_log("Error awarding rewards points for order $simpleOrderId: " . $e->getMessage());
    }

    // Success response
    $response = [
        "response" => "S",
        "message" => "Order placed successfully",
        "order_id" => $simpleOrderId,
        "transaction_id" => $razorpayOrderId,
        "payment_status" => $paymentStatus,
        "amount" => $data['final_total'],
        "name" => $data['name'],
        "email" => $data['email'],
        "phone" => $data['phone'],
        "points_awarded" => $pointsAwarded
    ];

    echo json_encode($response);
    
} catch (Exception $e) {
    // Clear any output buffer to ensure clean JSON
    ob_clean();
    error_log("Simple order processing error: " . $e->getMessage());
    echo json_encode([
        "response" => "E",
        "message" => "Order processing failed: " . $e->getMessage(),
        "debug_error" => $e->getMessage()
    ]);
} catch (Error $e) {
    // Catch PHP fatal errors
    ob_clean();
    error_log("PHP Error in order processing: " . $e->getMessage());
    echo json_encode([
        "response" => "E",
        "message" => "System error occurred",
        "debug_error" => $e->getMessage()
    ]);
}

// Ensure we end cleanly
ob_end_flush();
?>

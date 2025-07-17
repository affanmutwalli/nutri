<?php

header("Content-Type: application/json");
session_start();
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$obj->connection();

include('razorpay_config.php'); // Include Razorpay API keys

$data = json_decode(file_get_contents("php://input"), true);

$order_db_id = $data['order_db_id'];
$razorpay_payment_id = $data['razorpay_payment_id'];
$razorpay_order_id = $data['razorpay_order_id'];
$razorpay_signature = $data['razorpay_signature'];

// Verify payment signature
$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, RAZORPAY_KEY_SECRET);

if ($generated_signature === $razorpay_signature) {
    // Payment is verified, now create the actual order in database

    // Get the stored order data from session
    $sessionKey = 'pending_order_' . $order_db_id;
    if (!isset($_SESSION[$sessionKey])) {
        echo json_encode(["status" => "failure", "message" => "Order data not found in session"]);
        exit;
    }

    $orderData = $_SESSION[$sessionKey];

    // Create the order in database with Paid status
    $insertOrderQuery = "INSERT INTO order_master (
        OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
        OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt
    ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW())";

    $orderStmt = $mysqli->prepare($insertOrderQuery);
    if (!$orderStmt) {
        echo json_encode(["status" => "failure", "message" => "Failed to prepare order insert"]);
        exit;
    }

    $orderStmt->bind_param("sisssss",
        $orderData['OrderId'],
        $orderData['CustomerId'],
        $orderData['CustomerType'],
        $orderData['Amount'],
        $orderData['ShipAddress'],
        $orderData['PaymentType'],
        $razorpay_payment_id // Use actual payment ID as transaction ID
    );

    if (!$orderStmt->execute()) {
        echo json_encode(["status" => "failure", "message" => "Failed to create order"]);
        exit;
    }
    $orderStmt->close();

    // Insert order details
    $detailsQuery = "INSERT INTO order_details (
        OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $detailsStmt = $mysqli->prepare($detailsQuery);
    if ($detailsStmt && isset($orderData['products'])) {
        foreach ($orderData['products'] as $product) {
            $subTotal = floatval($product['quantity']) * floatval($product['offer_price']);

            $detailsStmt->bind_param("ssssidd",
                $orderData['OrderId'],
                $product['id'],
                $product['code'] ?? '',
                $product['size'] ?? '',
                $product['quantity'],
                $product['offer_price'],
                $subTotal
            );

            $detailsStmt->execute();
        }
        $detailsStmt->close();
    }

    // Clear the temporary order data from session
    unset($_SESSION[$sessionKey]);

    // Clear cart sessions
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
    }

    // Clear database cart if user is logged in
    if (isset($orderData['CustomerId']) && !empty($orderData['CustomerId'])) {
        try {
            include_once 'cart_persistence.php';
            $cartManager = new CartPersistence();
            $cartManager->clearDatabaseCart($orderData['CustomerId']);
        } catch (Exception $e) {
            // Log error but don't fail the order
            error_log("Error clearing database cart after payment: " . $e->getMessage());
        }
    }

    echo json_encode(["status" => "success", "message" => "Payment verified and order created successfully"]);

} else {
    // Signature mismatch, payment failed
    // Clean up the temporary order data
    $sessionKey = 'pending_order_' . $order_db_id;
    if (isset($_SESSION[$sessionKey])) {
        unset($_SESSION[$sessionKey]);
    }

    echo json_encode(["status" => "failure", "message" => "Payment verification failed"]);
}
?>

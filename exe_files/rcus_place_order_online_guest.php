<?php
// Guest Online Payment Processing
session_start();
include("../database/dbconnection.php");
require_once '../razorpay/Razorpay.php';

use Razorpay\Api\Api;

$obj = new main();
$mysqli = $obj->connection();

// Set content type to JSON
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception("Invalid JSON data received");
    }
    
    error_log("Guest Online Payment: Received data - " . print_r($data, true));
    
    // Validate required fields for guest checkout
    $requiredFields = ['name', 'email', 'phone', 'address', 'city', 'state', 'pincode', 'final_total', 'products'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate products array
    if (!is_array($data['products']) || empty($data['products'])) {
        throw new Exception("No products in order");
    }
    
    // Generate simple order ID for guest
    $orderPrefix = "GN"; // Guest Online
    $timestamp = time();
    $random = rand(100, 999);
    $simpleOrderId = $orderPrefix . $timestamp . $random;
    
    // Check if this ID already exists
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
    
    try {
        // Log order creation attempt
        error_log("Guest Online Payment: Starting Razorpay order for " . $simpleOrderId);
        error_log("Guest Online Payment: Amount = " . $data['final_total']);

        // Initialize Razorpay
        $api = new Api('rzp_live_DJ1mSUEz1DK4De', '2C8q79zzBNMd6jadotjz6Tci');

        $amount = floatval($data['final_total']) * 100; // Convert to paise

        // Validate amount
        if ($amount <= 0) {
            throw new Exception("Invalid amount: " . $amount);
        }

        error_log("Guest Online Payment: Amount in paise = " . $amount);

        $razorpayOrderData = [
            'receipt' => $simpleOrderId,
            'amount' => intval($amount), // Ensure it's an integer
            'currency' => 'INR',
            'payment_capture' => 1,
            'notes' => [
                'customer_type' => 'Guest',
                'customer_name' => $data['name'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone']
            ]
        ];

        error_log("Guest Online Payment: Creating Razorpay order with data - " . print_r($razorpayOrderData, true));

        $razorpayOrder = $api->order->create($razorpayOrderData);
        $razorpayOrderId = $razorpayOrder['id'];

        error_log("Guest Online Payment: Razorpay order created successfully - " . $razorpayOrderId);

    } catch (Exception $e) {
        error_log("Guest Online Payment: Razorpay error - " . $e->getMessage());
        throw new Exception("Payment gateway error: " . $e->getMessage());
    }
    
    // Store order data for later creation after payment success
    $orderData = [
        'OrderId' => $simpleOrderId,
        'CustomerId' => 0, // Guest CustomerId
        'CustomerType' => 'Guest',
        'Amount' => $data['final_total'],
        'ShipAddress' => $data['address'] . ', ' . ($data['landmark'] ?? '') . ', ' . $data['city'] . ', ' . $data['state'] . ' - ' . $data['pincode'],
        'PaymentType' => 'Online',
        'RazorpayOrderId' => $razorpayOrderId,
        'products' => $data['products'],
        'created_at' => date('Y-m-d H:i:s'),
        'GuestName' => $data['name'],
        'GuestEmail' => $data['email'],
        'GuestPhone' => $data['phone']
    ];

    // Store in database for payment verification
    $orderDataJson = json_encode($orderData);
    $insertPendingQuery = "INSERT INTO pending_orders (order_id, order_data, created_at) VALUES (?, ?, NOW())
                          ON DUPLICATE KEY UPDATE order_data = VALUES(order_data), created_at = VALUES(created_at)";

    $pendingStmt = $mysqli->prepare($insertPendingQuery);
    if ($pendingStmt) {
        $pendingStmt->bind_param("ss", $simpleOrderId, $orderDataJson);
        if ($pendingStmt->execute()) {
            error_log("Guest Online Payment: Pending order data stored in database for " . $simpleOrderId);
        } else {
            error_log("Guest Online Payment: Failed to execute pending order insert: " . $pendingStmt->error);
        }
        $pendingStmt->close();
    } else {
        error_log("Guest Online Payment: Failed to prepare pending order insert: " . $mysqli->error);
    }

    // Return success response with Razorpay order details
    echo json_encode([
        'response' => 'S',
        'message' => 'Payment initiated successfully',
        'order_id' => $simpleOrderId,
        'transaction_id' => $razorpayOrderId,
        'payment_status' => $paymentStatus,
        'amount' => $data['final_total'],
        'mobile' => $data['phone'],
        'name' => $data['name'],
        'email' => $data['email'],
        'customer_type' => 'Guest'
    ]);

} catch (Exception $e) {
    error_log("Guest Online Payment Error: " . $e->getMessage());
    echo json_encode([
        'response' => 'E',
        'message' => 'Order processing failed: ' . $e->getMessage()
    ]);
}
?>

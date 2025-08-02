<?php
/**
 * Combo Order Processing for Online Payment (Razorpay)
 * Handles combo product orders with online payment method
 */

// Ensure clean JSON output
ob_start();
header("Content-Type: application/json");
error_reporting(0);
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
    $required = ['name', 'email', 'phone', 'address', 'final_total', 'combo', 'CustomerId'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate combo data
    if (!isset($data['combo']['combo_id']) || !isset($data['combo']['product1_id']) || !isset($data['combo']['product2_id'])) {
        throw new Exception("Invalid combo data");
    }
    
    // Create database connection
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    // Verify combo exists and is active
    $comboCheckQuery = "SELECT * FROM dynamic_combos WHERE combo_id = ? AND is_active = TRUE";
    $comboCheckStmt = $mysqli->prepare($comboCheckQuery);
    $comboCheckStmt->bind_param("s", $data['combo']['combo_id']);
    $comboCheckStmt->execute();
    $comboCheckResult = $comboCheckStmt->get_result();
    
    if ($comboCheckResult->num_rows === 0) {
        throw new Exception("Combo not found or inactive");
    }
    
    $comboData = $comboCheckResult->fetch_assoc();
    $comboCheckStmt->close();
    
    // Generate simple order ID for online payments
    $orderPrefix = "CB";
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
    
    // Initialize Razorpay
    $api = new Api('rzp_live_DJ1mSUEz1DK4De', '2C8q79zzBNMd6jadotjz6Tci');
    
    $amount = floatval($data['final_total']) * 100; // Convert to paise
    
    // Validate amount
    if ($amount <= 0) {
        throw new Exception("Invalid amount: " . $amount);
    }
    
    $razorpayOrderData = [
        'receipt' => $simpleOrderId,
        'amount' => intval($amount),
        'currency' => 'INR',
        'payment_capture' => 1
    ];
    
    error_log("Creating Razorpay order for combo: " . json_encode($razorpayOrderData));
    
    // Create Razorpay order
    $razorpayOrder = $api->order->create($razorpayOrderData);
    $razorpayOrderId = $razorpayOrder['id'];
    
    error_log("Razorpay order created: " . $razorpayOrderId);
    
    // Create proper shipping address
    $shippingAddress = $data['address'] . ", " . $data['landmark'] . ", " . $data['city'] . ", " . $data['state'] . " - " . $data['pincode'];
    
    // Get current date and time in IST
    date_default_timezone_set("Asia/Kolkata");
    $orderDate = date("Y-m-d");
    $createdAt = date("Y-m-d H:i:s");
    
    // Set online payment method and status
    $paymentMethod = 'Online';
    $paymentStatus = 'Pending';
    
    // Insert order into order_master
    $insertOrderQuery = "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt) 
                         VALUES (?, ?, 'Registered', ?, ?, ?, 'Process', ?, ?, ?, ?)";
    
    $orderStmt = $mysqli->prepare($insertOrderQuery);
    if (!$orderStmt) {
        throw new Exception("Failed to prepare order insert: " . $mysqli->error);
    }
    
    $orderStmt->bind_param("sssdsssss", 
        $simpleOrderId, 
        $data['CustomerId'], 
        $orderDate, 
        $data['final_total'], 
        $paymentStatus, 
        $shippingAddress, 
        $paymentMethod, 
        $razorpayOrderId,
        $createdAt
    );
    
    if (!$orderStmt->execute()) {
        throw new Exception("Failed to insert order: " . $orderStmt->error);
    }
    $orderStmt->close();
    
    // Insert combo details into order_details as individual products
    $quantity = intval($data['combo']['quantity']);
    
    // Insert Product 1
    $product1InsertQuery = "INSERT INTO order_details (OrderId, ProductId, ProductCode, Quantity, Size, Price, SubTotal) 
                           VALUES (?, ?, ?, ?, 'Combo Item', ?, ?)";
    
    $product1Stmt = $mysqli->prepare($product1InsertQuery);
    if (!$product1Stmt) {
        throw new Exception("Failed to prepare product1 insert: " . $mysqli->error);
    }
    
    // Calculate individual product prices (split combo price)
    $individualPrice = $comboData['combo_price'] / 2;
    $individualSubTotal = $individualPrice * $quantity;
    
    // Get product codes
    $product1Code = "COMBO-P1-" . $data['combo']['product1_id'];
    $product2Code = "COMBO-P2-" . $data['combo']['product2_id'];
    
    $product1Stmt->bind_param("sissdd", 
        $simpleOrderId, 
        $data['combo']['product1_id'], 
        $product1Code,
        $quantity, 
        $individualPrice, 
        $individualSubTotal
    );
    
    if (!$product1Stmt->execute()) {
        throw new Exception("Failed to insert product1: " . $product1Stmt->error);
    }
    $product1Stmt->close();
    
    // Insert Product 2
    $product2Stmt = $mysqli->prepare($product1InsertQuery); // Same query structure
    if (!$product2Stmt) {
        throw new Exception("Failed to prepare product2 insert: " . $mysqli->error);
    }
    
    $product2Stmt->bind_param("sissdd", 
        $simpleOrderId, 
        $data['combo']['product2_id'], 
        $product2Code,
        $quantity, 
        $individualPrice, 
        $individualSubTotal
    );
    
    if (!$product2Stmt->execute()) {
        throw new Exception("Failed to insert product2: " . $product2Stmt->error);
    }
    $product2Stmt->close();
    
    // Insert combo tracking record
    $comboTrackingQuery = "INSERT INTO combo_order_tracking (order_id, combo_id, combo_name, combo_price, quantity, total_amount) 
                          VALUES (?, ?, ?, ?, ?, ?)";
    
    $comboTrackingStmt = $mysqli->prepare($comboTrackingQuery);
    if ($comboTrackingStmt) {
        $comboTrackingStmt->bind_param("sssdid", 
            $simpleOrderId, 
            $data['combo']['combo_id'], 
            $data['combo']['combo_name'], 
            $comboData['combo_price'], 
            $quantity, 
            $data['final_total']
        );
        $comboTrackingStmt->execute();
        $comboTrackingStmt->close();
    }
    
    // Clear any output buffer and send success response
    ob_clean();
    echo json_encode([
        'response' => 'S',
        'message' => 'Combo order created successfully',
        'order_id' => $simpleOrderId,
        'transaction_id' => $razorpayOrderId,
        'payment_status' => $paymentStatus,
        'amount' => $data['final_total'],
        'combo_id' => $data['combo']['combo_id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone']
    ]);
    
} catch (Exception $e) {
    // Clear any output buffer and send error response
    ob_clean();
    error_log("Combo Online Order Error: " . $e->getMessage());
    echo json_encode([
        'response' => 'E',
        'message' => $e->getMessage()
    ]);
}
?>

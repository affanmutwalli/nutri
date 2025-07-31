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
    
    $orderStmt->bind_param("sisdsssss",
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
    
    // Get actual product prices from product_price table
    $price1_query = "SELECT OfferPrice FROM product_price WHERE ProductId = ? AND OfferPrice IS NOT NULL AND OfferPrice != '' LIMIT 1";
    $price1_stmt = $mysqli->prepare($price1_query);
    $price1_stmt->bind_param("i", $data['combo']['product1_id']);
    $price1_stmt->execute();
    $price1_result = $price1_stmt->get_result();
    $product1_price = 0;
    if ($price1_result->num_rows > 0) {
        $price1_row = $price1_result->fetch_assoc();
        $product1_price = intval($price1_row['OfferPrice']);
    }
    $price1_stmt->close();

    $price2_query = "SELECT OfferPrice FROM product_price WHERE ProductId = ? AND OfferPrice IS NOT NULL AND OfferPrice != '' LIMIT 1";
    $price2_stmt = $mysqli->prepare($price2_query);
    $price2_stmt->bind_param("i", $data['combo']['product2_id']);
    $price2_stmt->execute();
    $price2_result = $price2_stmt->get_result();
    $product2_price = 0;
    if ($price2_result->num_rows > 0) {
        $price2_row = $price2_result->fetch_assoc();
        $product2_price = intval($price2_row['OfferPrice']);
    }
    $price2_stmt->close();

    // Calculate subtotals
    $product1_subtotal = $product1_price * $quantity;

    // Get product codes
    $product1Code = "COMBO-P1-" . $data['combo']['product1_id'];
    $product2Code = "COMBO-P2-" . $data['combo']['product2_id'];

    $product1Stmt->bind_param("sisiii",
        $simpleOrderId,
        $data['combo']['product1_id'],
        $product1Code,
        $quantity,
        $product1_price,
        $product1_subtotal
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

    $product2_subtotal = $product2_price * $quantity;

    $product2Stmt->bind_param("sisiii",
        $simpleOrderId,
        $data['combo']['product2_id'],
        $product2Code,
        $quantity,
        $product2_price,
        $product2_subtotal
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

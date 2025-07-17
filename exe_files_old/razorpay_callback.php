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
    // Payment is verified, now update the database with payment details and status
    $stmt = $mysqli->prepare("UPDATE order_master SET PaymentStatus = 'Paid', TransactionId = ?, OrderStatus = 'Placed' WHERE OrderId = ?");
    $stmt->bind_param("ss", $razorpay_payment_id, $order_db_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Payment verified and successful"]);
    } else {
        echo json_encode(["status" => "failure", "message" => "Error updating payment status"]);
    }
    $stmt->close();
} else {
    // Signature mismatch, payment failed
    echo json_encode(["status" => "failure", "message" => "Payment verification failed"]);
}
?>

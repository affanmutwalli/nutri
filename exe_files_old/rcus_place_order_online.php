<?php
// Set JSON response header
header("Content-Type: application/json");
session_start();
ob_start();

// Include database connection files
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

// Ensure the Razorpay library is properly included
require_once('../razorpay/Razorpay.php');

use Razorpay\Api\Api;

// Create database connection
$obj = new main();
$obj->connection(); // Ensure $conn is accessible for prepared statements

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['address']) || empty($data['final_total']) || empty($data['products']) || empty($data['CustomerId'])) {
    echo json_encode(["response" => "E", "message" => "Missing required fields"]);
    exit();
}


// Create shipping address as a single string
$shippingAddress = implode(", ", [
    $data['name'],
    $data['email'],
    $data['phone'],
    $data['address'],
    isset($data['landmark']) ? $data['landmark'] : '',
    isset($data['pincode']) ? $data['pincode'] : '',
    isset($data['city']) ? $data['city'] : '',
    isset($data['state']) ? $data['state'] : ''
]);


// Get current date and time in IST
date_default_timezone_set("Asia/Kolkata");
$orderDate = date("Y-m-d");
$createdAt = date("Y-m-d H:i:s");

// Generate Sequential Order ID (Format: MN000000)
$orderPrefix = "MN";

// Fetch last inserted order ID
$FieldNames = array("OrderId");
$ParamArray = array(); // No specific condition needed for this query
$Fields = implode(",", $FieldNames);
$lastOrderQuery = "SELECT " . $Fields . " FROM order_master ORDER BY OrderId DESC LIMIT 1";

// Execute the query using the MysqliSelect1 method
$lastOrderRow = $obj->MysqliSelect1($lastOrderQuery, $FieldNames, "", $ParamArray);

if ($lastOrderRow) {
    $lastOrderId = (int) substr($lastOrderRow[0]['OrderId'], 2); // Extract number part
    $newOrderId = $orderPrefix . str_pad($lastOrderId + 1, 6, "0", STR_PAD_LEFT); // Increment & format
} else {
    $newOrderId = $orderPrefix . "000001"; // First order case
}

// Set default payment method and status
$paymentMethod = "Online";
$paymentStatus = ($paymentMethod === 'Online') ? 'Pending' : 'Due';

// Initialize Razorpay API (Use live/test credentials accordingly)
$api = new Api('rzp_live_DJ1mSUEz1DK4De', '2C8q79zzBNMd6jadotjz6Tci');

// Prepare order parameters for Razorpay (only for online payments)
$razorpayOrderId = 'NA';
if ($paymentMethod === 'Online') {
    $orderData = [
        'receipt' => $newOrderId,
        'amount' => $data['final_total'] * 100, // Convert to paise
        'currency' => 'INR',
        'payment_capture' => 1
    ];

    try {
        // Create an order on Razorpay
        $razorpayOrder = $api->order->create($orderData);
        $razorpayOrderId = $razorpayOrder['id']; // Get the Razorpay order ID
    } catch (\Razorpay\Api\Errors\BadRequestError $e) {
        echo json_encode(["response" => "E", "message" => "Razorpay Error: " . $e->getMessage()]);
        exit();
    } catch (Exception $e) {
        echo json_encode(["response" => "E", "message" => "General Error: " . $e->getMessage()]);
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

    // Insert order details into `order_details` table
    foreach ($data['products'] as $product) {
        // Prepare the data for insertion into the order_details table
        $sub_total = $product["offer_price"] * $product["quantity"];
        
        // Prepare the parameter array
        $ParamArray = array(
            $newOrderId, // OrderId (string)
            $product['id'], // ProductId (integer)
            $product['code'], // ProductCode (string)
            $product['size'], // Size (string)
            $product['quantity'], // Quantity (integer)
            $product['offer_price'], // Price (double)
            $sub_total // SubTotal (double)
        );
        
        // Call the fInsertNew method to insert the product data
        $productInsertId = $obj->fInsertNew(
            "INSERT INTO order_details (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal) 
             VALUES (?, ?, ?, ?, ?, ?, ?)", 
            "sissidd", // Updated data types corresponding to the $ParamArray
            $ParamArray
        );
    }
        if (!$productInsertId) {
            throw new Exception("Failed to insert product with ID: " . $product['id']);
        }

    // Return response
    echo json_encode([
        'response' => 'S',
        'message' => 'Order placed successfully',
        'order_id' => $newOrderId,
        'transaction_id' => $razorpayOrderId,
        'payment_status' => $paymentStatus,
        'amount' => $data['final_total'],
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone']
    ]);

} else {
    echo json_encode(["response" => "E", "message" => "Error placing order in database"]);
}
?>

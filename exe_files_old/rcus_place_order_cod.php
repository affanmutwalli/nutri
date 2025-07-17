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

// Create shipping address as a single string
$shippingAddress = $data['name'] . ", " . $data['email'] . ", " . $data['phone'] . ", " . $data['address'] . ", " . $data['landmark'] . ", " . $data['pincode'] . ", " . $data['city'] . ", " . $data['state'];

// Get current date and time in IST
date_default_timezone_set("Asia/Kolkata");
$orderDate = date("Y-m-d");
$createdAt = date("Y-m-d H:i:s");

// Generate Sequential Order ID (Format: MN000000)
$orderPrefix = "MN";
$FieldNames = array("OrderId");
$ParamArray = array();
$Fields = implode(",", $FieldNames);
$lastOrderQuery = "SELECT " . $Fields . " FROM order_master ORDER BY OrderId DESC LIMIT 1";

// Fetch last order ID
$lastOrderRow = $obj->MysqliSelect1($lastOrderQuery, $FieldNames, "", $ParamArray);
if ($lastOrderRow) {
    $lastOrderId = (int) substr($lastOrderRow[0]['OrderId'], 2);
    $newOrderId = $orderPrefix . str_pad($lastOrderId + 1, 6, "0", STR_PAD_LEFT);
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
$InputDocId = $obj->fInsertNew(
    "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt) 
     VALUES (?, ?, 'Registered', ?, ?, ?, 'Placed', ?, ?, ?, ?)", 
    "sssdsssss", // Data types corresponding to ParamArray
    $ParamArray
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

    // Return success response
    echo json_encode([
        'response' => 'S',
        'message' => 'Order placed successfully',
        'order_id' => $newOrderId,
        'transaction_id' => 'NA', // No Razorpay transaction ID for COD
        'payment_status' => $paymentStatus,
        'amount' => $data['final_total']
    ]);

} else {
    echo json_encode(["response" => "E", "message" => "Error placing order in database"]);
}
?>

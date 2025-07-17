<?php
session_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$obj->connection();


$data = json_decode(file_get_contents('php://input'), true);
// Get data from the GET request
$otp = $data['otp'];
$customerId = $data['CustomerId'];
$IsVerify = "N";

// Validate input
if (empty($otp) || empty($customerId)) {
    echo json_encode(["msg" => "Customer ID and OTP are required.", "response" => "E"]);
    exit;
}

// Retrieve OTP and status from the direct_customers table
$FieldNames = ["CustomerId", "CustomerName", "MobileNo", "OTP", "IsVerify"];
$ParamArray = [$customerId, $IsVerify];
$Fields = implode(",", $FieldNames);

// Fetch customer details
$customerData = $obj->MysqliSelect1("SELECT $Fields FROM direct_customers WHERE CustomerId = ? AND IsVerify = ?", $FieldNames, "is", $ParamArray);

if (!empty($customerData)) {
    $storedOtp = $customerData[0]['OTP']; // Convert stored OTP to string
    $enteredOtp = $otp; // Convert entered OTP to string

    if ($enteredOtp == $storedOtp) {
        // OTP matches, activate the user
        $newStatus = 'Y';
   					$stmt = $mysqli->prepare("update direct_customers set  IsVerify = ? where CustomerId = ? ");
   					$stmt->bind_param("si", $newStatus,$customerId);
        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(["msg" => "OTP verified successfully", "response" => "S","CustomerId" => $customerId]);
        } else {
            echo json_encode(["msg" => "Database update failed.", "response" => "E"]);
        }
    } else {
        echo json_encode(["msg" => "Invalid OTP.", "response" => "E"]);
    }
} else {
    echo json_encode(["msg" => "No record found or account is already active.", "response" => "E"]);
}
?>

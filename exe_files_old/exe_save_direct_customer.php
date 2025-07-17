<?php
session_start();
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$obj->connection();

// Get data from the request
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$address = $data['address'];
$landmark = $data['landmark'];
$pincode = $data['pincode'];
$state = $data['state'];
$city = $data['city'];

// Fix: Closing parenthesis for the 'if' condition
if (isset($name) && isset($phone) && !empty($phone) && !empty($name)) {
    $OTP = rand(100000, 999999); // Generate OTP

    // Concatenate the address and landmark before insertion
    $fullAddress = $address . ", " . $landmark;  // Combine address and landmark
    $IsVerify = "N";
    // Proceed with insertion if customer data doesn't exist
    $ParamArray = array($name, $email, $phone, $fullAddress, $pincode, $state, $OTP, $city,$IsVerify);

    // Modify the SQL query to include city if needed or adjust as required
    $InputDocId = $obj->fInsertNew(
        "INSERT INTO direct_customers (CustomerName, Email, MobileNo, Address, Pincode, State, OTP, City,IsVerify) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", 
        "sssssssss", $ParamArray
    );

    if ($InputDocId) {
        // Fetch the newly registered customer data
        $FieldNames = array("CustomerId");
        $ParamArray = array($phone, $email); // Param for Email and Mobile Number

        // SQL query to fetch customer data
        $Fields = implode(",", $FieldNames);
        $register_customer = $obj->MysqliSelect1("SELECT " . $Fields . " FROM direct_customers WHERE MobileNo = ? AND Email = ?", $FieldNames, "ss", $ParamArray);

        // Set session variables
        $Date = date('j M Y');
        
        // Email details
        $subject = "My Nutrify - Your OTP Code for Verification";
        $message = "My Nutrify - Your OTP Code for Verification : $OTP ";

        // Set the email headers to send HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: verification@mynutrify.com" . "\r\n";

        // Send the email
        if (mail($email, $subject, $message, $headers)) {
            $CustomerId = $register_customer[0]["CustomerId"];
            echo json_encode(["msg" => "OTP Sent to Email Successfully.", "response" => "S", "CustomerId" => "$CustomerId", "CustomerType" => "Direct"]);
        } else {
            echo json_encode(["msg" => "Error: Could not send verification email.", "response" => "E"]);
        }
    }
} else {
    // Respond if required fields are missing
    echo json_encode(["msg" => "Please provide all required fields.", "response" => "E"]);
}
?>

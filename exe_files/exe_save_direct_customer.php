<?php
session_start();
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$obj->connection();

// Get JSON data from the request body
$requestData = json_decode(file_get_contents('php://input'), true);

// Validate that required fields exist
if (isset($requestData['name'], $requestData['email'], $requestData['phone'], $requestData['address'], $requestData['landmark'], $requestData['pincode'], $requestData['state'], $requestData['city'])
    && !empty($requestData['name']) 
    && !empty($requestData['phone'])) {

    // Sanitize input data
    $name     = htmlspecialchars(trim($requestData['name']));
    $email    = htmlspecialchars(trim($requestData['email']));
    $phone    = htmlspecialchars(trim($requestData['phone']));
    $address  = htmlspecialchars(trim($requestData['address']));
    $landmark = htmlspecialchars(trim($requestData['landmark']));
    $pincode  = htmlspecialchars(trim($requestData['pincode']));
    $state    = htmlspecialchars(trim($requestData['state']));
    $city     = htmlspecialchars(trim($requestData['city']));
    
    // Generate OTP
    $OTP = rand(100000, 999999);

    // Concatenate address and landmark
    $fullAddress = $address . ", " . $landmark;
    $IsVerify = "N";

    // Prepare parameters for insertion
    $ParamArray = array($name, $email, $phone, $fullAddress, $pincode, $state, $OTP, $city, $IsVerify);

    // Insert new customer data into direct_customers table
    $InputDocId = $obj->fInsertNew(
        "INSERT INTO direct_customers (CustomerName, Email, MobileNo, Address, Pincode, State, OTP, City, IsVerify) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", 
        "sssssssss", 
        $ParamArray
    );

    if ($InputDocId) {
        // Fetch the newly registered customer data (fetch both CustomerId and MobileNo)
        $FieldNames = array("CustomerId", "MobileNo");
        // Note: Since the WHERE clause expects MobileNo then Email, we pass them in that order.
        $ParamArray = array($phone, $email);

        $Fields = implode(",", $FieldNames);
        $register_customer = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM direct_customers WHERE MobileNo = ? AND Email = ?", 
            $FieldNames, 
            "ss", 
            $ParamArray
        );
        
        // Set session variable with MobileNo from the fetched data
        if (!empty($register_customer)) {
            $_SESSION["MobileNo"] = $register_customer[0]["MobileNo"];
        }
        
        // Prepare variables for OTP API call
        $mobile_number = $register_customer[0]["MobileNo"];
        $CustomerId    = $register_customer[0]["CustomerId"];
        $Date          = date('j M Y');
        
        // Prepare the API payload for sending OTP via Interakt
        $apiPayload = [
            "countryCode"  => "+91",
            "phoneNumber"  => $mobile_number,
            "callbackData" => "otp_callback_data",
            "type"         => "Template",
            "template"     => [
                "name"         => "register_user",
                "languageCode" => "en_US",
                "bodyValues"   => [
                    "$OTP" // OTP as a string
                ],
                // Ensure buttonValues is encoded as a JSON object
                "buttonValues" => (object)[
                    "0" => [
                        "$OTP"
                    ]
                ]
            ]
        ];
        
        // Initialize cURL to call the Interakt API
        $url = "https://api.interakt.ai/v1/public/message/";
        $apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo="; // Replace with your actual API key
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo json_encode(["msg" => "Error: " . curl_error($ch), "response" => "E"]);
        } else {
            echo json_encode([
                "msg"          => "OTP Sent Successfully.",
                "response"     => "S",
                "CustomerId"   => "$CustomerId",
                "CustomerType" => "Direct"
            ]);
        }
        curl_close($ch);
    }
} else {
    // Respond if required fields are missing
    echo json_encode(["msg" => "Please provide all required fields.", "response" => "E"]);
}
?>

<?php
session_start();
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if required fields are posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['name'], $_POST['email'], $_POST['mobile_no'], $_POST['password'])
    && !empty($_POST['name']) 
    && !empty($_POST['email']) 
    && !empty($_POST['mobile_no']) 
    && !empty($_POST['password'])) {

    // Sanitize input data
    $name      = htmlspecialchars(trim($_POST['name']));
    $email     = htmlspecialchars(trim($_POST['email']));
    $mobile_no = htmlspecialchars(trim($_POST['mobile_no']));
    $password  = $_POST['password'];
    $pass      = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Validate mobile number (simple check: must be at least 10 digits)
    if (strlen($mobile_no) < 10) {
        echo json_encode(["msg" => "Invalid mobile number.", "response" => "E"]);
        exit;
    }
    
    // Set creation date and activation flag
    $CreatedAt = date('j M Y H:i'); // e.g. "5 Mar 2025 14:30"
    $IsActive  = "N";
    
    // Use consistent order: Email first, then MobileNo
    $FieldNames = array("Email", "MobileNo");
    $ParamArray = array($email, $mobile_no); // Email then Mobile Number

    // Check if a customer with the same email and mobile number already exists
    $Fields = implode(",", $FieldNames);
    try {
        $customer_data = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM customer_master WHERE Email = ? AND MobileNo = ?",
            $FieldNames,
            "ss",
            $ParamArray
        );
    } catch (Exception $e) {
        error_log("Database error in registration check: " . $e->getMessage());
        echo json_encode(["msg" => "Database error occurred. Please try again.", "response" => "E"]);
        exit;
    }
    
    if (empty($customer_data)) {
        $OTP = rand(100000, 999999); // Generate OTP

        // Insert new customer data
        $InsertParams = array($name, $email, $mobile_no, $pass, $OTP, $CreatedAt, $IsActive);
        try {
            $InputDocId = $obj->fInsertNew(
                "INSERT INTO customer_master (Name, Email, MobileNo, Pass, OTP, CreationDate, IsActive) VALUES (?, ?, ?, ?, ?, ?, ?)",
                "sssssss",
                $InsertParams
            );
        } catch (Exception $e) {
            error_log("Database error in customer insertion: " . $e->getMessage());
            echo json_encode(["msg" => "Registration failed. Please try again.", "response" => "E"]);
            exit;
        }
        
        if ($InputDocId) {
            // Re-fetch the newly registered customer data using consistent ordering
            $ParamArray = array($email, $mobile_no); // Email then Mobile Number
            $register_customer = $obj->MysqliSelect1(
                "SELECT " . $Fields . " FROM customer_master WHERE Email = ? AND MobileNo = ?",
                $FieldNames,
                "ss",
                $ParamArray
            );

            // Set session variables
            $_SESSION["MobileNo"] = $register_customer[0]["MobileNo"];
            
            $Date = date('j M Y');
            $mobile_number = $register_customer[0]["MobileNo"];
            
            // Prepare the API call to send OTP via Interakt
            $url = "https://api.interakt.ai/v1/public/message/";
            $apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo="; // Replace with your actual API key

            // Build the payload data
            $data = [
                "countryCode"  => "+91",
                "phoneNumber"  => $mobile_number,
                "callbackData" => "otp_callback_data",
                "type"         => "Template",
                "template"     => [
                    "name"         => "register_user",
                    "languageCode" => "en_US",
                    "bodyValues"   => [
                        $OTP // OTP value as a string
                    ],
                    // Cast buttonValues to object so it’s encoded as a JSON object
                    "buttonValues" => (object)[
                        "0" => [
                            $OTP
                        ]
                    ]
                ]
            ];
            
            // Debug: Log the OTP and payload
            error_log("Registration OTP Debug - Generated OTP: $OTP");
            error_log("Registration OTP Debug - Payload: " . json_encode($data));

            // Initialize and set up cURL to send the request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Debug: Log the WhatsApp API response
            error_log("WhatsApp API Response Code: $httpCode");
            error_log("WhatsApp API Response: $response");

            if (curl_errno($ch)) {
                echo json_encode(["msg" => "Error: " . curl_error($ch), "response" => "E"]);
            } else {
                $_SESSION["MobileNo"] = $register_customer[0]["MobileNo"];
                 $_SESSION["Email"] = $register_customer[0]["Email"];
                            echo json_encode(["msg" => "OTP Sent to Email Successfully.", "response" => "S"]);
            }
            curl_close($ch);
        } else {
            echo json_encode(["msg" => "Error: Could not register customer.", "response" => "E"]);
        }
    } else {
        echo json_encode(["msg" => "Mobile Number or Email already exists.", "response" => "E"]);
    }
} else {
    echo json_encode(["msg" => "Please provide all required fields.", "response" => "E"]);
}
?>

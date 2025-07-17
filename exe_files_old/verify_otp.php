<?php
session_start();

include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$obj->connection();

if (!empty($_POST["email"]) && !empty($_POST["OTP"])) {
    $email = $_POST["email"];
    $otp = $_POST["OTP"];
    $IsActive = "N"; // Only check for inactive users during OTP verification

    // Retrieve OTP and status from the customer_master table
    $FieldNames = array("CustomerId", "Name", "MobileNo", "OTP", "IsActive");
    $ParamArray = [$email, $IsActive];
    $Fields = implode(",", $FieldNames);

    // Assuming MysqliSelect1 function handles the query correctly
    $customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE Email = ? AND IsActive = ?", $FieldNames, "ss", $ParamArray);

    if (!empty($customerData)) {
        if ($customerData[0]['OTP'] === $otp) {
            // OTP matches, activate the user
            $UpdatedAt = date('j M Y H:i');
            $newStatus = 'Y';
            $stmt = $mysqli->prepare("update customer_master set  IsActive = ?, UpdateDate = ? where Email= ? ");
			$stmt->bind_param("sss", $newStatus,$UpdatedAt,$email);
			$stmt->execute();
			$stmt->close();
            
            // Generate a secure session identifier (token)
            $sessionId = bin2hex(random_bytes(32)); // Secure random session ID

            // Store the session ID in the session and associate it with the CustomerId
            $_SESSION["session_id"] = $sessionId;
            $_SESSION["CustomerId"] = $single_data[0]["CustomerId"];
            
            // Store the session ID in a secure, HttpOnly cookie (optional)
            setcookie("session_id", $sessionId, time() + (30 * 24 * 60 * 60), "/", "", true, true); // 30 days

            // Optionally, store additional information (e.g., user-agent or IP) to prevent session hijacking
            $_SESSION["UserAgent"] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION["IP"] = $_SERVER['REMOTE_ADDR'];

            
            $response["msg"] = "OTP verified successfully. Account activated.";
            $response["response"] = "S"; // Success response
        } else {
            $response["msg"] = "Invalid OTP.";
            $response["response"] = "E"; // Error response
        }
    } else {
        $response["msg"] = "No record found or account is already active.";
        $response["response"] = "E"; // Error response
    }
} else {
    $response["msg"] = "Email and OTP are required.";
    $response["response"] = "E"; // Error response
}

echo json_encode($response);
?>

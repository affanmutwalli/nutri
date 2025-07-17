<?php
session_start();

// Set JSON header
header('Content-Type: application/json');

// Use only one set of database includes to avoid conflicts
include_once '../database/dbconnection.php';

// Initialize response array first
$response = array();

try {
    $obj = new main();
    $mysqli = $obj->connection();

    // Test database connection
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    error_log("Database connection error in verify_otp.php: " . $e->getMessage());
    $response["msg"] = "Error connecting to database";
    $response["response"] = "E";
    echo json_encode($response);
    exit;
}

if (!empty($_POST["email"]) && !empty($_POST["OTP"])) {
    $email = $_POST["email"];
    $otp = $_POST["OTP"];
    $IsActive = "N"; // Only check for inactive users during OTP verification

    // Retrieve OTP and status from the customer_master table
    $FieldNames = array("CustomerId", "Name", "MobileNo", "OTP", "IsActive");
    $ParamArray = [$email, $IsActive];
    $Fields = implode(",", $FieldNames);

    // Execute database query with error handling
    try {
        $customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE Email = ? AND IsActive = ?", $FieldNames, "ss", $ParamArray);
    } catch (Exception $e) {
        error_log("Database query error in verify_otp.php: " . $e->getMessage());
        $response["msg"] = "Database query failed";
        $response["response"] = "E";
        echo json_encode($response);
        exit;
    }

    if (!empty($customerData)) {
        // Debug logging
        error_log("OTP Verification Debug - Email: $email, Submitted OTP: '$otp', DB OTP: '{$customerData[0]['OTP']}'");

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
            $_SESSION["CustomerId"] = $customerData[0]["CustomerId"];
            
            // Store the session ID in a secure, HttpOnly cookie (optional)
            setcookie("session_id", $sessionId, time() + (30 * 24 * 60 * 60), "/", "", true, true); // 30 days

            // Optionally, store additional information (e.g., user-agent or IP) to prevent session hijacking
            $_SESSION["UserAgent"] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $_SESSION["IP"] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

            
            $response["msg"] = "OTP verified successfully. Account activated.";
            $response["response"] = "S"; // Success response
        } else {
            $response["msg"] = "Invalid OTP.";
            $response["response"] = "E"; // Error response
            error_log("OTP Mismatch - Expected: '{$customerData[0]['OTP']}', Got: '$otp'");
        }
    } else {
        $response["msg"] = "No record found or account is already active.";
        $response["response"] = "E"; // Error response
        error_log("No customer data found for email: $email with IsActive = N");
    }
} else {
    $response["msg"] = "Email and OTP are required.";
    $response["response"] = "E"; // Error response
}

echo json_encode($response);
?>

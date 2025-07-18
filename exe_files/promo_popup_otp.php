<?php
session_start();
header('Content-Type: application/json');

// Include database connection
include_once '../database/dbconnection.php';

// Initialize response
$response = array();

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    error_log("Database connection error in promo_popup_otp.php: " . $e->getMessage());
    $response["msg"] = "Database connection failed";
    $response["response"] = "E";
    echo json_encode($response);
    exit;
}

// Get POST data
$action = $_POST['action'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$otp = $_POST['otp'] ?? '';

if ($action === 'send_otp') {
    // Validate mobile number
    if (empty($mobile) || !preg_match('/^[6-9]\d{9}$/', $mobile)) {
        $response["msg"] = "Please enter a valid 10-digit mobile number";
        $response["response"] = "E";
        echo json_encode($response);
        exit;
    }
    
    // Generate 6-digit OTP
    $generatedOTP = rand(100000, 999999);
    
    try {
        // Check if mobile number already exists in promo_leads table
        $checkQuery = "SELECT id FROM promo_leads WHERE mobile_number = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing record
            $updateQuery = "UPDATE promo_leads SET otp = ?, otp_generated_at = NOW(), is_verified = 0 WHERE mobile_number = ?";
            $stmt = $mysqli->prepare($updateQuery);
            $stmt->bind_param("ss", $generatedOTP, $mobile);
            $stmt->execute();
        } else {
            // Insert new record
            $insertQuery = "INSERT INTO promo_leads (mobile_number, otp, otp_generated_at, is_verified, created_at) VALUES (?, ?, NOW(), 0, NOW())";
            $stmt = $mysqli->prepare($insertQuery);
            $stmt->bind_param("ss", $mobile, $generatedOTP);
            $stmt->execute();
        }
        
        // Send OTP via Interakt API
        $otpSent = sendOTPViaInterakt($mobile, $generatedOTP);
        
        if ($otpSent['success']) {
            $response["msg"] = "OTP sent successfully to your mobile number";
            $response["response"] = "S";
            $response["otp_for_testing"] = $generatedOTP; // Remove this in production
        } else {
            $response["msg"] = "Failed to send OTP. Please try again.";
            $response["response"] = "E";
            $response["error"] = $otpSent['error'];
        }
        
    } catch (Exception $e) {
        error_log("Error in promo_popup_otp.php (send_otp): " . $e->getMessage());
        $response["msg"] = "Error processing request";
        $response["response"] = "E";
    }
    
} elseif ($action === 'verify_otp') {
    // Validate inputs
    if (empty($mobile) || empty($otp)) {
        $response["msg"] = "Mobile number and OTP are required";
        $response["response"] = "E";
        echo json_encode($response);
        exit;
    }
    
    try {
        // Check OTP validity (within 10 minutes)
        $verifyQuery = "SELECT id, otp FROM promo_leads WHERE mobile_number = ? AND otp_generated_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) ORDER BY created_at DESC LIMIT 1";
        $stmt = $mysqli->prepare($verifyQuery);
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedOTP = $row['otp'];
            $leadId = $row['id'];
            
            if ($otp == $storedOTP) {
                // OTP is correct, mark as verified
                $updateQuery = "UPDATE promo_leads SET is_verified = 1, verified_at = NOW() WHERE id = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("i", $leadId);
                $stmt->execute();
                
                $response["msg"] = "OTP verified successfully";
                $response["response"] = "S";
                $response["promo_code"] = "WELCOME25";
            } else {
                $response["msg"] = "Invalid OTP. Please try again.";
                $response["response"] = "E";
            }
        } else {
            $response["msg"] = "OTP expired or invalid. Please request a new OTP.";
            $response["response"] = "E";
        }
        
    } catch (Exception $e) {
        error_log("Error in promo_popup_otp.php (verify_otp): " . $e->getMessage());
        $response["msg"] = "Error verifying OTP";
        $response["response"] = "E";
    }
    
} else {
    $response["msg"] = "Invalid action";
    $response["response"] = "E";
}

echo json_encode($response);

// Function to send OTP via Interakt API
function sendOTPViaInterakt($mobile, $otp) {
    $apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    // Prepare API payload for OTP template
    $apiPayload = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "promo_popup_otp",
        "type" => "Template",
        "template" => [
            "name" => "verify_acc", // Using existing OTP template
            "languageCode" => "en",
            "bodyValues" => [$otp],
            "buttonValues" => (object)[
                "0" => [$otp]
            ]
        ]
    ];
    
    // Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['success' => false, 'error' => 'cURL error: ' . $error];
    }
    
    curl_close($ch);
    
    // Check response (200 or 201 are success codes for Interakt API)
    if ($httpCode == 200 || $httpCode == 201) {
        $responseData = json_decode($response, true);
        if ($responseData && isset($responseData['result']) && $responseData['result'] === true) {
            return ['success' => true, 'response' => $responseData];
        } else {
            return ['success' => false, 'error' => 'API returned error: ' . $response];
        }
    } else {
        return ['success' => false, 'error' => 'HTTP error: ' . $httpCode . ' - ' . $response];
    }
}
?>

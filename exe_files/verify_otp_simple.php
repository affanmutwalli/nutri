<?php
session_start();

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = array();

// Simple database connection
try {
    include_once '../database/dbdetails.php';
    
    $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
} catch (Exception $e) {
    error_log("Database connection error in verify_otp_simple.php: " . $e->getMessage());
    $response["msg"] = "Database connection failed: " . $e->getMessage();
    $response["response"] = "E";
    echo json_encode($response);
    exit;
}

if (!empty($_POST["email"]) && !empty($_POST["OTP"])) {
    $email = $_POST["email"];
    $otp = $_POST["OTP"];
    
    try {
        // Prepare and execute query
        $stmt = $mysqli->prepare("SELECT CustomerId, Name, MobileNo, OTP, IsActive FROM customer_master WHERE Email = ? AND IsActive = ?");
        $isActive = "N";
        $stmt->bind_param("ss", $email, $isActive);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $customerData = $result->fetch_assoc();
            
            // Debug logging
            error_log("OTP Verification Debug - Email: $email, Submitted OTP: '$otp', DB OTP: '{$customerData['OTP']}'");
            
            if ($customerData['OTP'] === $otp) {
                // OTP matches, activate the user
                $updateStmt = $mysqli->prepare("UPDATE customer_master SET IsActive = ?, UpdateDate = ? WHERE Email = ?");
                $newStatus = 'Y';
                $updatedAt = date('j M Y H:i');
                $updateStmt->bind_param("sss", $newStatus, $updatedAt, $email);
                $updateStmt->execute();
                $updateStmt->close();
                
                // Generate a secure session identifier (token)
                $sessionId = bin2hex(random_bytes(32));
                
                // Store the session ID in the session and associate it with the CustomerId
                $_SESSION["session_id"] = $sessionId;
                $_SESSION["CustomerId"] = $customerData["CustomerId"];
                
                // Store the session ID in a secure, HttpOnly cookie
                setcookie("session_id", $sessionId, time() + (30 * 24 * 60 * 60), "/", "", true, true);
                
                // Store additional information for security
                $_SESSION["UserAgent"] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                $_SESSION["IP"] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                
                $response["msg"] = "OTP verified successfully. Account activated.";
                $response["response"] = "S";
            } else {
                $response["msg"] = "Invalid OTP.";
                $response["response"] = "E";
                error_log("OTP Mismatch - Expected: '{$customerData['OTP']}', Got: '$otp'");
            }
        } else {
            $response["msg"] = "No record found or account is already active.";
            $response["response"] = "E";
            error_log("No customer data found for email: $email with IsActive = N");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Database query error in verify_otp_simple.php: " . $e->getMessage());
        $response["msg"] = "Database query failed: " . $e->getMessage();
        $response["response"] = "E";
    }
    
} else {
    $response["msg"] = "Email and OTP are required.";
    $response["response"] = "E";
}

$mysqli->close();
echo json_encode($response);
?>

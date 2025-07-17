<?php
/**
 * Simple Backend Test
 * Tests if the backend can return proper JSON
 */

// Ensure clean JSON output
ob_start();
header("Content-Type: application/json");
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Test 1: Basic JSON response
    $response = [
        "status" => "success",
        "message" => "Backend is working",
        "timestamp" => date('Y-m-d H:i:s'),
        "php_version" => PHP_VERSION
    ];
    
    // Test 2: Database connection
    if (file_exists('../database/dbconnection.php')) {
        include_once '../database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();
        
        if ($mysqli) {
            $response["database"] = "connected";
            $response["mysql_version"] = $mysqli->server_info;
        } else {
            $response["database"] = "failed";
        }
    } else {
        $response["database"] = "dbconnection.php not found";
    }
    
    // Test 3: Razorpay SDK
    if (file_exists('../razorpay/Razorpay.php')) {
        require_once('../razorpay/Razorpay.php');
        $response["razorpay_sdk"] = "loaded";
        
        if (class_exists('Razorpay\Api\Api')) {
            $response["razorpay_api"] = "available";
        } else {
            $response["razorpay_api"] = "class not found";
        }
    } else {
        $response["razorpay_sdk"] = "not found";
    }
    
    // Test 4: Session
    session_start();
    $response["session_id"] = session_id();
    
    // Test 5: Input data
    $input = file_get_contents('php://input');
    if ($input) {
        $data = json_decode($input, true);
        $response["input_received"] = $data ? "valid_json" : "invalid_json";
        $response["input_data"] = $data;
    } else {
        $response["input_received"] = "no_input";
    }
    
    // Clean output and send JSON
    ob_clean();
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
} catch (Error $e) {
    ob_clean();
    echo json_encode([
        "status" => "php_error",
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}

ob_end_flush();
?>

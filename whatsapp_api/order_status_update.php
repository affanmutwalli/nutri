<?php
/**
 * Order Status Update WhatsApp Notification
 * Sends WhatsApp messages when order status changes
 */

function sendOrderStatusUpdate($orderId, $customerName, $mobile, $status, $trackingNumber = null) {
    // Interakt API credentials
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    // Determine template and message based on status
    $templateData = getTemplateByStatus($status, $customerName, $orderId, $trackingNumber);
    
    if (!$templateData) {
        return ["success" => false, "message" => "Invalid status or template not found"];
    }
    
    // Prepare payload
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "order_status_" . $orderId,
        "type" => "Template",
        "template" => $templateData
    ];
    
    // Send via cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        "success" => ($httpCode == 201),
        "response" => $response,
        "http_code" => $httpCode
    ];
}

function getTemplateByStatus($status, $customerName, $orderId, $trackingNumber) {
    switch (strtolower($status)) {
        case 'shipped':
        case 'dispatched':
            return [
                "name" => "order_shipped",
                "languageCode" => "en", // Try "en_US" if "en" doesn't work
                "bodyValues" => [
                    $customerName,
                    $orderId,
                    $trackingNumber ?: "Will be updated soon"
                ]
            ];
            
        case 'out_for_delivery':
        case 'out for delivery':
            return [
                "name" => "out_for_delivery",
                "languageCode" => "en",
                "bodyValues" => [
                    $customerName,
                    $orderId,
                    date('d M Y')
                ]
            ];
            
        case 'delivered':
            return [
                "name" => "order_delivered",
                "languageCode" => "en",
                "bodyValues" => [
                    $customerName,
                    $orderId,
                    "My Nutrify"
                ]
            ];
            
        case 'cancelled':
            return [
                "name" => "order_cancelled",
                "languageCode" => "en",
                "bodyValues" => [
                    $customerName,
                    $orderId,
                    "Refund will be processed within 5-7 business days"
                ]
            ];
            
        default:
            return null;
    }
}

// Example usage:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderId = $input['order_id'] ?? '';
    $customerName = $input['customer_name'] ?? '';
    $mobile = $input['mobile'] ?? '';
    $status = $input['status'] ?? '';
    $trackingNumber = $input['tracking_number'] ?? null;
    
    if (empty($orderId) || empty($customerName) || empty($mobile) || empty($status)) {
        echo json_encode(["error" => "Missing required parameters"]);
        exit;
    }
    
    $result = sendOrderStatusUpdate($orderId, $customerName, $mobile, $status, $trackingNumber);
    echo json_encode($result);
}
?>

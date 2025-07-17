<?php
// send_order_placed_whatsapp.php

// Set response headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Get JSON input from the request
$input = json_decode(file_get_contents("php://input"), true);

// Validate required parameters: 'mobile' and 'customer_name'
if (
    !isset($input['mobile']) || empty($input['mobile']) ||
    !isset($input['customer_name']) || empty($input['customer_name'])
) {
    echo json_encode([
        "response" => "E",
        "msg" => "Required parameters missing: mobile and customer_name are needed."
    ]);
    exit;
}

// Retrieve and sanitize input data
$mobile       = trim($input['mobile']);
$customerName = trim($input['customer_name']);
$deliveryDays = isset($input['delivery_days']) ? trim($input['delivery_days']) : "5"; // Default to 5 days
$orderId      = isset($input['order_id']) ? trim($input['order_id']) : ""; // Optional order ID

// Build the payload for the Interakt API using the "order_placed" template
$apiPayload = [
    "countryCode"  => "+91",
    "phoneNumber"  => $mobile,
    "callbackData" => $orderId,
    "type"         => "Template",
    "template"     => [
        "name"         => "order_placed_prepaid",  // Exact code name of your template in Interakt
        "languageCode" => "en",
        "bodyValues"   => [
            $customerName,  // Replaces {{1}}
            "My Nutrify",   // Replaces {{2}} (static store name)
            $deliveryDays   // Replaces {{3}}
        ]
        // Optionally, add "buttonValues" if required.
    ]
];

// Interakt API endpoint and your API key
$url    = "https://api.interakt.ai/v1/public/message/";
$apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo="; // Replace with your actual API key

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));

// Execute the cURL request
$response = curl_exec($ch);
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    echo json_encode(["response" => "E", "msg" => "cURL error: " . $error_msg]);
    exit;
}
curl_close($ch);

// Output the API response
echo $response;
?>

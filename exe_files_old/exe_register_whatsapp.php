<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// API URL
$url = "https://api.interakt.ai/v1/public/message/";

// Your API Key
$apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo="; // Replace with your actual API key

// Payload data
$data = [
    "countryCode" => "+91",
    "phoneNumber" => "8329566751", // Replace with the actual phone number
    "callbackData" => "otp_callback_data",
    "type" => "Template",
    "template" => [
        "name" => "register_user",
        "languageCode" => "en_US",
        "bodyValues" => [
            "123456" // Replace with the actual OTP value
        ],
        "buttonValues" => [
            "0" => [
                "123456" // Replace with the actual OTP value
            ]
        ]
    ]
];

// Initialize cURL
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // Print response
    echo 'Response: ' . $response;
}

// Close cURL session
curl_close($ch);
?>
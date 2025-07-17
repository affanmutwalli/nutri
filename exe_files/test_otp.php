<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// API URL
$url = "https://api.interakt.ai/v1/public/message/";

// Your API Key
$apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo="; // Replace with your actual API key

// Prepare the payload data
$data = [
    "countryCode"   => "+91",
    "phoneNumber"   => "8329566751",  // Replace with the recipient's phone number
    "callbackData"  => "Optional callback data",
    "type"          => "Template",
    "template"      => [
        "name"         => "verify_acc",      // Template name (code name in your Interakt account)
        "languageCode" => "en",              // Language code as set during template creation
        // Both bodyValues and buttonValues should have the same OTP or code value
        "bodyValues"   => ["123456"],        // Replace with your OTP or verification code
        "buttonValues" => [
            "0" => ["123456"]              // Must be the same as in bodyValues
        ]
    ]
];

// Encode the payload as JSON
$jsonData = json_encode($data);

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic ' . $apiKey", // Use your API key here (if required, you may need to base64 encode it)
    "Content-Type: application/json"
]);

// Execute the API request
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'Request Error: ' . curl_error($ch);
} else {
    echo "Response from Interakt: " . $response;
}

// Close the cURL session
curl_close($ch);
?>

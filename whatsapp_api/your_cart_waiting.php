<?php
// Customer details
$countryCode = "+91";             // Valid country code
$phone = "8329566751";            // Local phone number without country code
$customerName = "Muddassar Kazi";
$productName = "Cholestrol Care";
$checkoutLink = "https://mynutrify.com/checkout";
$productImageUrl = "https://mynutrify.com/cms/images/products/2974.png";

// Interakt API credentials (ensure this key is correctly base64 encoded)
$accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
// API endpoint with trailing slash
$apiUrl = "https://api.interakt.ai/v1/public/message/";

// Prepare payload
$data = [
    "countryCode" => $countryCode,   // Separate field for country code
    "phoneNumber" => $phone,           // Local phone number only
    "callbackData" => "cart_reminder",
    "type" => "Template",
    "template" => [
        "name" => "your_cart_is_waiting",  // Template code name from your Interakt dashboard
        "languageCode" => "en_US",
        // For media header templates, headerValues should be an array of URL strings:
        "headerValues" => [
            $productImageUrl
        ],
        "bodyValues" => [
            $customerName,   // {{1}} - Customer name
            $productName,    // {{2}} - Product name
        ],
        // buttonValues is required – use an empty object if no buttons are needed
        "buttonValues" => new stdClass()
    ]
];

// cURL to send the WhatsApp message
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic $accessToken",  // Using HTTP Basic Auth
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle response
if ($httpCode == 200) {
    echo "✅ WhatsApp cart reminder sent successfully!";
} else {
    echo "❌ Failed to send message. Response: " . $response;
}
?>

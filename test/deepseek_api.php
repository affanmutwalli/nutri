<?php
header('Content-Type: application/json');

// Read the JSON input sent from the front end
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$userMessage = isset($data['message']) ? trim($data['message']) : '';

if (empty($userMessage)) {
    echo json_encode(['response' => 'Please type a message.']);
    exit;
}

// DeepSeek API endpoint and API key
$apiUrl = 'https://api.deepseek.com/chat/completions';
$apiKey = 'sk-19533c5b859e406482937de8a5a4c5b2';

// Build the conversation payload for DeepSeek with a custom system prompt
$payload = [
    'model' => 'deepseek-chat',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are an Ayurvedic AI assistant for My Nutrify Herbal & Ayurveda (mynutrify.com). You engage with users about their health concerns and recommend our herbal and Ayurvedic products to help improve their wellness. Use friendly, knowledgeable language and always guide the conversation towards product recommendations.'
        ],
        [
            'role' => 'user',
            'content' => $userMessage
        ]
    ],
    'stream' => false
];

// Initialize cURL session
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors
if ($response === false) {
    echo json_encode(['response' => 'cURL Error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

// Decode the API response
$responseData = json_decode($response, true);

// Extract the reply text from DeepSeek's response (modify as needed based on API structure)
$reply = isset($responseData['choices'][0]['message']['content']) ? $responseData['choices'][0]['message']['content'] : 'Sorry, I could not process your request.';

// Close cURL session
curl_close($ch);

// Return the reply as JSON
echo json_encode(['response' => $reply]);
?>

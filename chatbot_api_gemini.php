<?php
header('Content-Type: application/json');

// Simple response cache
session_start();
if (!isset($_SESSION['chatbot_cache'])) {
    $_SESSION['chatbot_cache'] = [];
}

// Read JSON from front end
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$userMessage = isset($data['message']) ? trim($data['message']) : '';

if (empty($userMessage)) {
    echo json_encode([
        'response' => 'Please type a message.',
        'products' => []
    ]);
    exit;
}

// Check cache first
$cacheKey = md5(strtolower($userMessage));
if (isset($_SESSION['chatbot_cache'][$cacheKey])) {
    echo json_encode($_SESSION['chatbot_cache'][$cacheKey]);
    exit;
}

// Google Gemini API endpoint (Fast & Free)
$apiKey = 'YOUR_GEMINI_API_KEY_HERE'; // Get free API key from https://aistudio.google.com/app/apikey
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;

// Ultra-compressed system prompt
$systemPrompt = <<<EOT
Ayurvedic AI for My Nutrify (mynutrify.com). Products:
IMMUNITY: Shilajit Resin ₹699 (ID:18,img:2974.png), Amla Juice ₹249 (ID:6,img:4453.jpg), Wheatgrass ₹449 (ID:14,img:2370.jpg), Shilajit Pro ₹499 (ID:19,img:3310.jpg), Shilajit Gold ₹899 (ID:21,img:5992.jpg)
DIGESTIVE: Apple Cider Vinegar ₹749 (ID:15,img:1346.png), Neem Karela Jamun ₹349 (ID:11,img:9240.jpg)
SKIN: Amla Juice ₹249 (ID:6,img:4453.jpg), She Care ₹499 (ID:23,img:3851.jpg)
BLOOD: BP Care ₹999 (ID:22,img:7674.jpg)
DIABETIC: Cholesterol Care ₹599 (ID:9,img:9616.jpg), Diabetic Care ₹549 (ID:10,img:7132.jpg), Neem Karela ₹349 (ID:11,img:9240.jpg)
WOMEN: She Care ₹499 (ID:23,img:3851.jpg)
THYROID: Thyro Balance ₹499 (ID:23)

Rules: Short answers. Same language as user. Ayurveda only. For other topics: "I don't have knowledge about this. If you have any enquiry related to My Nutrify Herbal & Ayurveda, I can help you with that."
Format: [JSON]{"recommended_products":[{"name":"ProductName","url":"https://mynutrify.com/product_details.php?ProductId=X","price":"₹xxx","image_url":"https://mynutrify.com/cms/images/products/XXXX.jpg"}]}[/JSON]
EOT;

// Gemini API payload format
$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $systemPrompt . "\n\nUser: " . $userMessage]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.3,
        'topK' => 1,
        'topP' => 1,
        'maxOutputTokens' => 300,
        'stopSequences' => []
    ],
    'safetySettings' => [
        [
            'category' => 'HARM_CATEGORY_HARASSMENT',
            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
        ],
        [
            'category' => 'HARM_CATEGORY_HATE_SPEECH',
            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
        ],
        [
            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
        ],
        [
            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
        ]
    ]
];

// Fast cURL settings for Gemini
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Aggressive timeouts for speed
curl_setopt($ch, CURLOPT_TIMEOUT, 12);           // Total timeout: 12 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);     // Connection timeout: 4 seconds
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'MyNutrify-Gemini-Chatbot/1.0');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    curl_close($ch);
    echo json_encode([
        'response' => 'Sorry, I\'m having trouble connecting right now. Please try again.',
        'products' => []
    ]);
    exit;
}

if ($httpCode !== 200) {
    curl_close($ch);
    echo json_encode([
        'response' => 'Sorry, I\'m experiencing technical difficulties. Please try again.',
        'products' => []
    ]);
    exit;
}

$responseData = json_decode($response, true);
curl_close($ch);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'response' => 'Sorry, I received an invalid response. Please try again.',
        'products' => []
    ]);
    exit;
}

// Extract text from Gemini response format
$replyText = '';
if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $replyText = $responseData['candidates'][0]['content']['parts'][0]['text'];
} else {
    $replyText = 'Sorry, I could not process your request.';
}

// Parse JSON block [JSON] ... [/JSON]
$products = [];
if (preg_match('/\[JSON\](.*?)\[\/JSON\]/s', $replyText, $matches)) {
    $jsonBlock = trim($matches[1]);
    $decodedBlock = json_decode($jsonBlock, true);
    if (isset($decodedBlock['recommended_products'])) {
        $products = $decodedBlock['recommended_products'];
    }
}

// Clean the reply text
$cleanReply = preg_replace('/\[JSON\].*\[\/JSON\]/s', '', $replyText);
$cleanReply = trim($cleanReply);

// Format text
$cleanReply = preg_replace('/https?:\/\/\S+/', '<a href="$0" target="_blank">Click here</a>', $cleanReply);
$cleanReply = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $cleanReply);
$cleanReply = preg_replace('/\*(.*?)\*/', '<i>$1</i>', $cleanReply);
$cleanReply = preg_replace('/`(.*?)`/', '<code>$1</code>', $cleanReply);

// Prepare response
$result = [
    'response' => $cleanReply,
    'products' => $products
];

// Cache successful responses
if (!empty($cleanReply) && $cleanReply !== 'Sorry, I could not process your request.') {
    $_SESSION['chatbot_cache'][$cacheKey] = $result;
    
    // Limit cache size
    if (count($_SESSION['chatbot_cache']) > 50) {
        $_SESSION['chatbot_cache'] = array_slice($_SESSION['chatbot_cache'], -25, 25, true);
    }
}

echo json_encode($result);
?>

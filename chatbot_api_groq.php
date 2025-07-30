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

// Groq API endpoint (MUCH FASTER than DeepSeek)
$apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
$apiKey = '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';

// Ultra-compressed system prompt for maximum speed
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

When recommending products, provide helpful text response followed by product recommendations in this EXACT format:

[JSON]{"recommended_products":[{"name":"ProductName","url":"product_details.php?ProductId=X","price":"₹xxx","image_url":"cms/images/products/XXXX"}]}[/JSON]

CRITICAL:
- Always close JSON block with [/JSON]
- Use RELATIVE URLs only (product_details.php?ProductId=X) - NO domain names
- Use exact image filename with extension from product data (e.g., 2974.png, 4453.jpg, etc.)
EOT;

// Optimized payload for Groq (lightning fast)
$payload = [
    'model' => 'meta-llama/llama-4-scout-17b-16e-instruct', // Latest fastest model
    'messages' => [
        [ 'role' => 'system', 'content' => $systemPrompt ],
        [ 'role' => 'user',   'content' => $userMessage ]
    ],
    'max_tokens' => 300,
    'temperature' => 0.3,
    'stream' => false
];

// Ultra-fast cURL settings for Groq
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Super aggressive timeouts for Groq's speed
curl_setopt($ch, CURLOPT_TIMEOUT, 10);           // Total timeout: 10 seconds (Groq is FAST)
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);     // Connection timeout: 3 seconds
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'MyNutrify-Groq-Chatbot/1.0');
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

// Extract the text portion from the AI
$replyText = isset($responseData['choices'][0]['message']['content'])
    ? $responseData['choices'][0]['message']['content']
    : 'Sorry, I could not process your request.';

// Parse JSON block [JSON] ... [/JSON]
$products = [];
if (preg_match('/\[JSON\](.*?)\[\/JSON\]/s', $replyText, $matches)) {
    $jsonBlock = trim($matches[1]);
    $decodedBlock = json_decode($jsonBlock, true);
    if (isset($decodedBlock['recommended_products'])) {
        $products = $decodedBlock['recommended_products'];

        // Ensure URLs are relative (remove domain if present)
        foreach ($products as &$product) {
            if (isset($product['url'])) {
                // More aggressive URL cleaning
                $url = $product['url'];

                // Remove any full domain URLs
                $url = preg_replace('/^https?:\/\/[^\/]+\//', '', $url);
                $url = preg_replace('/^https?:\/\/[^\/]+/', '', $url);

                // Remove specific domain references
                $url = str_replace(['mynutrify.com/', 'mynutrify.com', 'www.mynutrify.com/', 'www.mynutrify.com'], '', $url);

                // Remove any leading slashes
                $url = ltrim($url, '/');

                // If URL doesn't start with product_details.php, extract ProductId and rebuild
                if (!str_starts_with($url, 'product_details.php')) {
                    // Try to extract ProductId from the URL
                    if (preg_match('/ProductId=(\d+)/', $url, $matches)) {
                        $url = 'product_details.php?ProductId=' . $matches[1];
                    } else {
                        // Fallback to a default
                        $url = 'product_details.php?ProductId=1';
                    }
                }

                $product['url'] = $url;
            }
        }

        // Debug: Log the processed products to see what URLs we're getting (disabled for production)
        // error_log("Raw AI response: " . $replyText);
        // error_log("Extracted JSON block: " . $jsonBlock);
        // error_log("Processed products: " . json_encode($products));
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

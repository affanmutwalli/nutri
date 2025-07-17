<?php
header('Content-Type: application/json');

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

// DeepSeek API endpoint
$apiUrl = 'https://api.deepseek.com/chat/completions';
$apiKey = 'sk-19533c5b859e406482937de8a5a4c5b2';

/**
 * We embed an instruction for the AI to return:
 * 1) A short text response
 * 2) A JSON block [JSON] ... [/JSON] with recommended_products
 */
$systemPrompt = <<<EOT
You are an Ayurvedic AI assistant for My Nutrify Herbal & Ayurveda (mynutrify.com).
You have the following static data:

--PRODUCT CATEGORIES & ITEMS--
1) Immunity:
   - My Nutrify Herbal & Ayurveda’s Pure Shilajit Resin (₹699): Himalayan Shilajit Resin with fulvic acid, minerals & antioxidants for energy, stamina, and enhanced immunity.
     Image: https://mynutrify.com/cms/images/products/2974.png
     URL: https://mynutrify.com/product_details.php?ProductId=18
   - My Nutrify Herbal & Ayurveda’s Special Amla High Fiber Juice - 1000 ml (₹249): Fresh cold pressed Amla Juice that helps boost skin and hair health, aids detox, is rich in Vitamin C, and acts as a natural immunity booster. (Offer: Off ₹50; Original: ₹299)
     Image: https://mynutrify.com/cms/images/products/4453.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=6
    - My Nutrify Herbal & Ayurveda Wheatgrass Juice - 1000ml (₹449): A natural detox and immunity booster rich in chlorophyll, vitamins, and antioxidants. Also supports digestion & metabolism (Available in 500ml). (Offer: Off ₹50; Original: ₹499)
     Image: https://mynutrify.com/cms/images/products/2370.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=14

2) Digestive:
   - My Nutrify Herbal & Ayurveda Apple Cider Vinegar- 1000ml (₹749): Raw, unfiltered apple cider vinegar with the mother. Aids digestion, detox, supports weight management, boosts immunity, and improves skin & hair health (Available in 500ml / 1000ml). (Offer: Off ₹150; Original: ₹899)
     Image: https://mynutrify.com/cms/images/products/1346.png
     URL: https://mynutrify.com/product_details.php?ProductId=15
    - My Nutrify Herbal & Ayurveda's Neem Karela Jamun Juice - 1000ml (₹349): A pure herbal health tonic that supports blood sugar, digestion, and detox. (Offer: Off ₹50; Original: ₹399)
     Image: https://mynutrify.com/cms/images/products/9240.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=11
3) Skin Wellness:
   - My Nutrify Herbal & Ayurveda’s Special Amla High Fiber Juice - 1000 ml (₹249): Fresh cold pressed Amla Juice that helps boost skin and hair health, aids detox, is rich in Vitamin C, and acts as a natural immunity booster. (Offer: Off ₹50; Original: ₹299)
     Image: https://mynutrify.com/cms/images/products/4453.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=6
   - My Nutrify Herbal & Ayurveda’s She Care Juice (₹499): A natural women’s health drink aimed at hormonal balance, period relief, and supporting skin & hair. It also aids detox, digestion, and immunity, and is suitable for PCOD/PCOS. (Offer: Off ₹50; Original: ₹549)
     Image: https://mynutrify.com/cms/images/products/3851.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=23

4) Blood Purifier:
   - My Nutrify Herbal & Ayurveda's BP Care Juice 1000ml (₹999): Natural Blood Pressure Management juice that helps reduce stress and supports heart function. Enriched with Bach, Sarpagandha, Shankhpushpi, and other herbs. (Offer: Off ₹50; Original: ₹1049)
     Image: https://mynutrify.com/cms/images/products/7674.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=22

5) Diabetic Wellness:
   - My Nutrify Herbal & Ayurveda's Cholesterol Care Juice (₹599): Juice that boosts heart health and supports cholesterol balance with Amla, Giloy, Turmeric, Neem, & Ashwagandha. Contains antioxidant and detox benefits. (Offer: Off ₹50; Original: ₹649)
     Image: https://mynutrify.com/cms/images/products/9616.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=9
   - My Nutrify Herbal & Ayurveda's Diabetic Care Juice - 1000ml (₹549): Formulated for natural blood glucose control; enhances insulin sensitivity, metabolic health, boosts energy, aids digestion and immunity, and is 100% herbal with no added sugar. (Offer: Off ₹50; Original: ₹599)
     Image: https://mynutrify.com/cms/images/products/7132.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=10
   - My Nutrify Herbal & Ayurveda's Neem Karela Jamun Juice - 1000ml (₹349): A pure herbal health tonic that supports blood sugar, digestion, and detox. (Offer: Off ₹50; Original: ₹399)
     Image: https://mynutrify.com/cms/images/products/9240.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=11

6) Women Wellness:
   - My Nutrify Herbal & Ayurveda’s She Care Juice (₹499): A natural women’s health drink aimed at hormonal balance, period relief, and supporting skin & hair. It also aids detox, digestion, and immunity, and is suitable for PCOD/PCOS. (Offer: Off ₹50; Original: ₹549)
     Image: https://mynutrify.com/cms/images/products/3851.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=23

7) Thyroid & Metabolism:
   - My Nutrify Herbal & Ayurveda Thyro Balance Juice – 1000ml (₹499): Provides natural thyroid support, acts as an energy booster, and enhances metabolism. (Offer: Off ₹50; Original: ₹549)
     Image: Not explicitly provided
    URL: https://mynutrify.com/product_details.php?ProductId=23
    
8) Additional Immunity:
   - My Nutrify Herbal & Ayurveda Wheatgrass Juice - 1000ml (₹449): A natural detox and immunity booster rich in chlorophyll, vitamins, and antioxidants. Also supports digestion & metabolism (Available in 500ml). (Offer: Off ₹50; Original: ₹499)
     Image: https://mynutrify.com/cms/images/products/2370.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=14
     
   - My Nutrify Shilajit Resin Pro (₹499): A variant of Shilajit Resin. (Offer: Off ₹700; Original: ₹1199)
     Image: https://mynutrify.com/cms/images/products/3310.jpg
     URL: https://mynutrify.com/product_details.php?ProductId=19
    - My Nutrify Herbal & Ayurveda Himalayan Shilajit Gold Pro (₹899): Premium Himalayan Shilajit & Ayurvedic Super Herbs for Strength, Testosterone & Immunity Support.
    Image: https://mynutrify.com/cms/images/products/5992.jpg
    URL: https://mynutrify.com/product_details.php?ProductId=21


IMPORTANT:
- Keep answers short and direct.
- Respond in the same language that the user has used, even if it is written using English characters.
- When recommending products, output a short user-facing reply.
- You should only discuss Ayurvedic health, herbal products, and related wellness topics. If the user asks about unrelated topics like web development, technology, politics, or general eCommerce, respond with: "I don’t have knowledge about this. If you have any enquiry related to My Nutrify Herbal & Ayurveda, I can help you with that."
- Then output a JSON block in the format:

[JSON]
{
  "recommended_products": [
    { "name": "ProductName","url":"https://mynutrify.com/...", "price": "₹xxx", "image_url": "..." },
    ...
  ]
}
[/JSON]

If no product is recommended, return an empty array in "recommended_products".
EOT;


// Build payload for DeepSeek
$payload = [
    'model' => 'deepseek-chat',
    'messages' => [
        [ 'role' => 'system', 'content' => $systemPrompt ],
        [ 'role' => 'user',   'content' => $userMessage ]
    ],
    'stream' => false
];

// cURL to DeepSeek
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);

if ($response === false) {
    echo json_encode([
        'response' => 'cURL Error: ' . curl_error($ch),
        'products' => []
    ]);
    curl_close($ch);
    exit;
}

// Decode the entire JSON from DeepSeek
$responseData = json_decode($response, true);
curl_close($ch);

// Extract the text portion from the AI
$replyText = isset($responseData['choices'][0]['message']['content'])
    ? $responseData['choices'][0]['message']['content']
    : 'Sorry, I could not process your request.';

// Attempt to parse the JSON block [JSON] ... [/JSON]
$products = [];
if (preg_match('/\[JSON\](.*?)\[\/JSON\]/s', $replyText, $matches)) {
    $jsonBlock = trim($matches[1]);
    $decodedBlock = json_decode($jsonBlock, true);
    if (isset($decodedBlock['recommended_products'])) {
        $products = $decodedBlock['recommended_products'];
    }
}

// Remove the JSON block from the user-facing text
$cleanReply = preg_replace('/\[JSON\].*\[\/JSON\]/s', '', $replyText);
$cleanReply = trim($cleanReply);

// Convert links to "Click here" anchors
$cleanReply = preg_replace('/https?:\/\/\S+/', '<a href="$0" target="_blank">Click here</a>', $cleanReply);

// Convert **bold** to <b>bold</b>
$cleanReply = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $cleanReply);

// Convert *italic* to <i>italic</i>
$cleanReply = preg_replace('/\*(.*?)\*/', '<i>$1</i>', $cleanReply);

// Convert `code` to <code>code</code>
$cleanReply = preg_replace('/`(.*?)`/', '<code>$1</code>', $cleanReply);


// Return the short text plus product array
echo json_encode([
    'response' => $cleanReply,
    'products' => $products
]);
?>

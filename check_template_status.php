<?php
/**
 * Template Status Checker
 * Checks which templates are available in your Interakt account
 */

header('Content-Type: text/html; charset=UTF-8');

// Interakt API credentials
$apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";

// Test different template names and language codes
$testTemplates = [
    ['name' => 'order_shipped', 'language' => 'en'],
    ['name' => 'order_shipped', 'language' => 'en_US'],
    ['name' => 'order_shipped', 'language' => 'English'],
    // Add variations
    ['name' => 'order_placed_prepaid', 'language' => 'en'], // Your existing working template
    ['name' => 'register_user', 'language' => 'en_US'], // Your existing working template
    ['name' => 'your_cart_is_waiting', 'language' => 'en_US'], // Your existing working template
];

echo "<h1>🔍 Template Status Checker</h1>";
echo "<p>Testing different template names and language codes...</p>";

foreach ($testTemplates as $template) {
    echo "<h3>Testing: {$template['name']} ({$template['language']})</h3>";
    
    // Test payload
    $testPayload = [
        "countryCode" => "+91",
        "phoneNumber" => "8329566751", // Your test number
        "callbackData" => "test_template_check",
        "type" => "Template",
        "template" => [
            "name" => $template['name'],
            "languageCode" => $template['language'],
            "bodyValues" => [
                "Test Customer",
                "TEST001",
                "Sample Value"
            ]
        ]
    ];
    
    // Make API call
    $ch = curl_init("https://api.interakt.ai/v1/public/message/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testPayload));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $responseData = json_decode($response, true);
    
    if ($httpCode == 201) {
        echo "<p style='color: green;'>✅ <strong>SUCCESS</strong> - Template found and working!</p>";
        echo "<pre style='background: #d4edda; padding: 10px;'>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ <strong>FAILED</strong> - HTTP Code: $httpCode</p>";
        echo "<pre style='background: #f8d7da; padding: 10px;'>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";
    }
    
    echo "<hr>";
    
    // Small delay between requests
    sleep(1);
}

echo "<h2>📋 Recommendations</h2>";
echo "<ul>";
echo "<li><strong>If all tests fail:</strong> Contact Interakt support to sync templates</li>";
echo "<li><strong>If some work:</strong> Use the working language code format</li>";
echo "<li><strong>Category Issue:</strong> Change template category from Marketing to Transactional</li>";
echo "<li><strong>Re-create Template:</strong> Delete and recreate with correct category</li>";
echo "</ul>";

echo "<h2>🔧 Quick Fix Options</h2>";
echo "<ol>";
echo "<li><strong>Re-sync in Dashboard:</strong> Go to Templates → Sync/Refresh</li>";
echo "<li><strong>Wait 24 hours:</strong> Sometimes takes time to propagate</li>";
echo "<li><strong>Contact Support:</strong> Interakt support can force sync</li>";
echo "<li><strong>Recreate Template:</strong> Delete and recreate as TRANSACTIONAL</li>";
echo "</ol>";
?>

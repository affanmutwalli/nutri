<?php
// Quick Template Status Checker for Interakt
$apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
$apiUrl = "https://api.interakt.ai/v1/public/message/";
$testPhone = "8329566751"; // Your test number

// Templates to check
$templates = [
    'order_placed_prepaid' => [
        'name' => 'Order Placed (Prepaid)',
        'bodyValues' => ['Test Customer', 'My Nutrify', '3-5 business days']
    ],
    'birthday_wishes' => [
        'name' => 'Birthday Wishes',
        'bodyValues' => ['Test Customer', 'BIRTHDAY20', '20%', 'My Nutrify'],
        'headerValues' => ['https://mynutrify.com/cms/images/birthday-banner.jpg']
    ],
    'anniversary_wishes' => [
        'name' => 'Anniversary Wishes',
        'bodyValues' => ['Test Customer', '2', 'ANNIVERSARY25', '25%']
    ],
    'your_cart_is_waiting' => [
        'name' => 'Cart Abandonment',
        'bodyValues' => ['Test Customer', 'Cholesterol Care Juice'],
        'headerValues' => ['https://mynutrify.com/cms/images/products/2974.png'],
        'buttonValues' => new stdClass()
    ],
    'payment_reminder' => [
        'name' => 'Payment Reminder',
        'bodyValues' => ['Test Customer', 'MN001234', '₹599']
    ],
    'feedback_request' => [
        'name' => 'Feedback Request',
        'bodyValues' => ['Test Customer', 'MN001234']
    ],
    'order_shipped' => [
        'name' => 'Order Shipped',
        'bodyValues' => ['Test Customer', 'MN001234', 'TRK123456789']
    ],
    'out_for_delivery' => [
        'name' => 'Out for Delivery',
        'bodyValues' => ['Test Customer', 'MN001234', date('d M Y')]
    ],
    'order_delivered' => [
        'name' => 'Order Delivered',
        'bodyValues' => ['Test Customer', 'MN001234']
    ],
    'payment_success' => [
        'name' => 'Payment Success',
        'bodyValues' => ['Test Customer', 'MN001234', '₹599']
    ],
    'payment_failed' => [
        'name' => 'Payment Failed',
        'bodyValues' => ['Test Customer', 'MN001234', '₹599']
    ]
];

$results = [];

// Test each template if requested
if ($_POST && isset($_POST['test_template'])) {
    $templateKey = $_POST['template_key'];
    $phoneNumber = $_POST['phone_number'] ?: $testPhone;
    
    if (isset($templates[$templateKey])) {
        $template = $templates[$templateKey];
        
        $payload = [
            "countryCode" => "+91",
            "phoneNumber" => $phoneNumber,
            "callbackData" => $templateKey . '_test_' . time(),
            "type" => "Template",
            "template" => [
                "name" => $templateKey,
                "languageCode" => "en",
                "bodyValues" => $template['bodyValues']
            ]
        ];
        
        if (isset($template['headerValues'])) {
            $payload["template"]["headerValues"] = $template['headerValues'];
        }
        
        if (isset($template['buttonValues'])) {
            $payload["template"]["buttonValues"] = $template['buttonValues'];
        }
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $results[$templateKey] = [
            'success' => ($httpCode == 201),
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'payload' => $payload
        ];
    }
}

// Test all templates if requested
if ($_POST && isset($_POST['test_all'])) {
    $phoneNumber = $_POST['phone_number'] ?: $testPhone;
    
    foreach ($templates as $templateKey => $template) {
        $payload = [
            "countryCode" => "+91",
            "phoneNumber" => $phoneNumber,
            "callbackData" => $templateKey . '_bulk_test_' . time(),
            "type" => "Template",
            "template" => [
                "name" => $templateKey,
                "languageCode" => "en",
                "bodyValues" => $template['bodyValues']
            ]
        ];
        
        if (isset($template['headerValues'])) {
            $payload["template"]["headerValues"] = $template['headerValues'];
        }
        
        if (isset($template['buttonValues'])) {
            $payload["template"]["buttonValues"] = $template['buttonValues'];
        }
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $results[$templateKey] = [
            'success' => ($httpCode == 201),
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'payload' => $payload
        ];
        
        // Add delay between requests to avoid rate limiting
        sleep(1);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Template Checker - Interakt WhatsApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .template-card { margin-bottom: 15px; }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .pending { border-left: 4px solid #6c757d; }
        .code-block { background: #f8f9fa; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">
            <i class="fab fa-whatsapp text-success"></i>
            Quick Template Checker
        </h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-cog"></i> Test Controls</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" 
                                       value="<?php echo $testPhone; ?>" placeholder="10-digit number">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Test Individual Template</label>
                                <select class="form-control" name="template_key">
                                    <option value="">Select template...</option>
                                    <?php foreach ($templates as $key => $template): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $template['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" name="test_template" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-paper-plane"></i> Test Selected Template
                            </button>
                            
                            <button type="submit" name="test_all" class="btn btn-warning w-100" 
                                    onclick="return confirm('This will send <?php echo count($templates); ?> test messages. Continue?')">
                                <i class="fas fa-broadcast-tower"></i> Test All Templates
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6><i class="fas fa-info-circle"></i> Template List</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($templates as $key => $template): ?>
                            <div class="mb-2">
                                <strong><?php echo $template['name']; ?></strong><br>
                                <small class="text-muted"><?php echo $key; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <?php if (!empty($results)): ?>
                    <h4><i class="fas fa-chart-bar"></i> Test Results</h4>
                    
                    <!-- Summary -->
                    <div class="alert alert-info">
                        <?php 
                        $successCount = count(array_filter($results, function($r) { return $r['success']; }));
                        $totalCount = count($results);
                        ?>
                        <strong>Summary:</strong> <?php echo $successCount; ?> successful out of <?php echo $totalCount; ?> templates tested
                    </div>
                    
                    <?php foreach ($results as $templateKey => $result): ?>
                        <div class="card template-card <?php echo $result['success'] ? 'success' : 'error'; ?>">
                            <div class="card-header">
                                <h6>
                                    <?php if ($result['success']): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                    <?php endif; ?>
                                    <?php echo $templates[$templateKey]['name']; ?>
                                    <span class="badge bg-<?php echo $result['success'] ? 'success' : 'danger'; ?> float-end">
                                        HTTP <?php echo $result['http_code']; ?>
                                    </span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (!$result['success']): ?>
                                    <div class="alert alert-danger">
                                        <strong>Error:</strong> 
                                        <?php if (!empty($result['error'])): ?>
                                            <?php echo htmlspecialchars($result['error']); ?>
                                        <?php else: ?>
                                            HTTP <?php echo $result['http_code']; ?> - Check template approval status
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h6>API Response:</h6>
                                <div class="code-block p-2 mb-3">
                                    <?php echo htmlspecialchars($result['response']); ?>
                                </div>
                                
                                <details>
                                    <summary>View Payload</summary>
                                    <div class="code-block p-2 mt-2">
                                        <?php echo htmlspecialchars(json_encode($result['payload'], JSON_PRETTY_PRINT)); ?>
                                    </div>
                                </details>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h5><i class="fas fa-rocket"></i> Ready to Test Templates</h5>
                        <p>Use the controls on the left to test individual templates or all templates at once.</p>
                        
                        <h6>Available Templates (<?php echo count($templates); ?>):</h6>
                        <div class="row">
                            <?php foreach ($templates as $key => $template): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="card template-card pending">
                                        <div class="card-body py-2">
                                            <strong><?php echo $template['name']; ?></strong><br>
                                            <small class="text-muted"><?php echo $key; ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Important:</strong>
                            <ul class="mb-0">
                                <li>Make sure all templates are approved in your Interakt dashboard</li>
                                <li>Test messages will be sent to the specified phone number</li>
                                <li>HTTP 201 = Success, other codes indicate errors</li>
                                <li>Check your WhatsApp for received messages</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

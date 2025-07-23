<?php
session_start();
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

// Test results
$testResults = [];

// Test existing integration files
if ($_POST && isset($_POST['test_existing'])) {
    $testType = $_POST['test_type'];
    $phoneNumber = $_POST['phone_number'] ?: "8329566751";
    
    switch ($testType) {
        case 'simple_whatsapp':
            // Test SimpleWhatsAppIntegration
            if (file_exists('whatsapp_api/SimpleWhatsAppIntegration.php')) {
                require_once 'whatsapp_api/SimpleWhatsAppIntegration.php';
                try {
                    $whatsapp = new SimpleWhatsAppIntegration();
                    $result = $whatsapp->sendMessage('order_placed_prepaid', $phoneNumber, [
                        'Test Customer',
                        'My Nutrify',
                        '3-5 business days'
                    ]);
                    $testResults['simple_whatsapp'] = $result;
                } catch (Exception $e) {
                    $testResults['simple_whatsapp'] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                $testResults['simple_whatsapp'] = [
                    'success' => false,
                    'error' => 'SimpleWhatsAppIntegration.php not found'
                ];
            }
            break;
            
        case 'production_whatsapp':
            // Test WhatsAppIntegration
            if (file_exists('whatsapp_api/WhatsAppIntegration.php')) {
                require_once 'whatsapp_api/WhatsAppIntegration.php';
                try {
                    $whatsapp = new WhatsAppIntegration();
                    $result = $whatsapp->sendMessage('birthday_wishes', $phoneNumber, [
                        'Test Customer',
                        'BIRTHDAY20',
                        '20%',
                        'My Nutrify'
                    ], ['ignore_business_hours' => true]);
                    $testResults['production_whatsapp'] = $result;
                } catch (Exception $e) {
                    $testResults['production_whatsapp'] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                $testResults['production_whatsapp'] = [
                    'success' => false,
                    'error' => 'WhatsAppIntegration.php not found'
                ];
            }
            break;
            
        case 'order_hooks':
            // Test order hooks
            if (file_exists('whatsapp_api/order_hooks.php')) {
                require_once 'whatsapp_api/order_hooks.php';
                try {
                    // Get a sample order
                    $orders = $obj->MysqliSelect1(
                        "SELECT OrderId FROM order_master ORDER BY OrderId DESC LIMIT 1",
                        array("OrderId"), "", array()
                    );
                    
                    if (!empty($orders)) {
                        $orderId = $orders[0]['OrderId'];
                        $result = testWhatsAppWithOrder($orderId, 'shipped');
                        $testResults['order_hooks'] = $result;
                    } else {
                        $testResults['order_hooks'] = [
                            'success' => false,
                            'error' => 'No orders found in database'
                        ];
                    }
                } catch (Exception $e) {
                    $testResults['order_hooks'] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                $testResults['order_hooks'] = [
                    'success' => false,
                    'error' => 'order_hooks.php not found'
                ];
            }
            break;
            
        case 'birthday_wishes':
            // Test birthday wishes
            if (file_exists('whatsapp_api/birthday_wishes.php')) {
                require_once 'whatsapp_api/birthday_wishes.php';
                try {
                    $result = sendBirthdayWish('Test Customer', $phoneNumber, 'BIRTHDAY20');
                    $testResults['birthday_wishes'] = $result;
                } catch (Exception $e) {
                    $testResults['birthday_wishes'] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                $testResults['birthday_wishes'] = [
                    'success' => false,
                    'error' => 'birthday_wishes.php not found'
                ];
            }
            break;
            
        case 'cart_abandonment':
            // Test cart abandonment
            if (file_exists('whatsapp_api/your_cart_waiting.php')) {
                // This file contains direct execution code, so we'll simulate it
                $apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
                $apiUrl = "https://api.interakt.ai/v1/public/message/";
                
                $payload = [
                    "countryCode" => "+91",
                    "phoneNumber" => $phoneNumber,
                    "callbackData" => "cart_abandonment_test",
                    "type" => "Template",
                    "template" => [
                        "name" => "your_cart_is_waiting",
                        "languageCode" => "en_US",
                        "headerValues" => ["https://mynutrify.com/cms/images/products/2974.png"],
                        "bodyValues" => [
                            "Test Customer",
                            "Cholesterol Care Juice"
                        ],
                        "buttonValues" => new stdClass()
                    ]
                ];
                
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
                curl_close($ch);
                
                $testResults['cart_abandonment'] = [
                    'success' => ($httpCode == 201),
                    'http_code' => $httpCode,
                    'response' => $response,
                    'payload' => $payload
                ];
            } else {
                $testResults['cart_abandonment'] = [
                    'success' => false,
                    'error' => 'your_cart_waiting.php not found'
                ];
            }
            break;
            
        case 'order_placed_exe':
            // Test the exe file for order placed
            if (file_exists('exe_files/send_order_placed_whatsapp.php')) {
                // Get a sample order and customer
                $orderData = $obj->MysqliSelect1(
                    "SELECT om.OrderId, cm.Name, cm.MobileNo 
                     FROM order_master om 
                     JOIN customer_master cm ON om.CustomerId = cm.CustomerId 
                     ORDER BY om.OrderId DESC LIMIT 1",
                    array("OrderId", "Name", "MobileNo"), "", array()
                );
                
                if (!empty($orderData)) {
                    // Simulate the exe file execution
                    $orderId = $orderData[0]['OrderId'];
                    $customerName = $orderData[0]['Name'];
                    $mobile = $phoneNumber; // Use test number instead
                    $deliveryDays = "3-5 business days";
                    
                    $apiPayload = [
                        "countryCode" => "+91",
                        "phoneNumber" => $mobile,
                        "callbackData" => $orderId,
                        "type" => "Template",
                        "template" => [
                            "name" => "order_placed_prepaid",
                            "languageCode" => "en",
                            "bodyValues" => [
                                $customerName,
                                "My Nutrify",
                                $deliveryDays
                            ]
                        ]
                    ];
                    
                    $apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
                    $url = "https://api.interakt.ai/v1/public/message/";
                    
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Basic ' . $apiKey,
                        'Content-Type: application/json'
                    ]);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    $testResults['order_placed_exe'] = [
                        'success' => ($httpCode == 201),
                        'http_code' => $httpCode,
                        'response' => $response,
                        'payload' => $apiPayload,
                        'order_id' => $orderId,
                        'customer_name' => $customerName
                    ];
                } else {
                    $testResults['order_placed_exe'] = [
                        'success' => false,
                        'error' => 'No order data found'
                    ];
                }
            } else {
                $testResults['order_placed_exe'] = [
                    'success' => false,
                    'error' => 'send_order_placed_whatsapp.php not found'
                ];
            }
            break;
    }
}

// Check which integration files exist
$integrationFiles = [
    'whatsapp_api/SimpleWhatsAppIntegration.php' => 'Simple WhatsApp Integration',
    'whatsapp_api/WhatsAppIntegration.php' => 'Production WhatsApp Integration',
    'whatsapp_api/order_hooks.php' => 'Order Hooks',
    'whatsapp_api/birthday_wishes.php' => 'Birthday Wishes',
    'whatsapp_api/your_cart_waiting.php' => 'Cart Abandonment',
    'whatsapp_api/automated_scheduler.php' => 'Automated Scheduler',
    'exe_files/send_order_placed_whatsapp.php' => 'Order Placed Exe',
    'exe_files/exe_register.php' => 'Registration with WhatsApp OTP',
    'check_template_status.php' => 'Template Status Checker'
];

$fileStatus = [];
foreach ($integrationFiles as $file => $description) {
    $fileStatus[$file] = [
        'exists' => file_exists($file),
        'description' => $description
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Existing Interakt Integrations - My Nutrify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .file-exists { color: #28a745; }
        .file-missing { color: #dc3545; }
        .test-result-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-result-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .code-display { background: #f8f9fa; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h1 class="mb-4">
            <i class="fab fa-whatsapp text-success"></i>
            Test Existing Interakt Integrations
        </h1>

        <div class="row">
            <!-- File Status -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-file-code"></i> Integration Files Status</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($fileStatus as $file => $info): ?>
                            <div class="mb-2">
                                <i class="fas <?php echo $info['exists'] ? 'fa-check-circle file-exists' : 'fa-times-circle file-missing'; ?>"></i>
                                <strong><?php echo $info['description']; ?></strong><br>
                                <small class="text-muted"><?php echo $file; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Test Controls -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-play"></i> Run Integration Tests</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Test Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" 
                                       value="8329566751" placeholder="10-digit number">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Select Integration Test</label>
                                <select class="form-control" name="test_type" required>
                                    <option value="">Choose test...</option>
                                    <option value="simple_whatsapp">Simple WhatsApp Integration</option>
                                    <option value="production_whatsapp">Production WhatsApp Integration</option>
                                    <option value="order_hooks">Order Hooks</option>
                                    <option value="birthday_wishes">Birthday Wishes</option>
                                    <option value="cart_abandonment">Cart Abandonment</option>
                                    <option value="order_placed_exe">Order Placed (Exe File)</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="test_existing" class="btn btn-primary w-100">
                                <i class="fas fa-rocket"></i> Run Test
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div class="col-md-8">
                <?php if (!empty($testResults)): ?>
                    <?php foreach ($testResults as $testType => $result): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>
                                    <?php if ($result['success']): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                    <?php endif; ?>
                                    <?php echo ucwords(str_replace('_', ' ', $testType)); ?> Test
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert <?php echo $result['success'] ? 'test-result-success' : 'test-result-error'; ?>">
                                    <strong>Status:</strong> <?php echo $result['success'] ? 'SUCCESS' : 'FAILED'; ?>
                                    <?php if (isset($result['http_code'])): ?>
                                        (HTTP <?php echo $result['http_code']; ?>)
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($result['error'])): ?>
                                        <br><strong>Error:</strong> <?php echo htmlspecialchars($result['error']); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($result['order_id'])): ?>
                                        <br><strong>Order ID:</strong> <?php echo $result['order_id']; ?>
                                        <br><strong>Customer:</strong> <?php echo htmlspecialchars($result['customer_name']); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (isset($result['response'])): ?>
                                    <h6>API Response:</h6>
                                    <div class="code-display p-2 mb-3">
                                        <?php echo htmlspecialchars($result['response']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($result['payload'])): ?>
                                    <h6>Sent Payload:</h6>
                                    <div class="code-display p-2">
                                        <?php echo htmlspecialchars(json_encode($result['payload'], JSON_PRETTY_PRINT)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Ready to Test</h5>
                        <p>Select an integration test from the left panel to begin testing your existing Interakt WhatsApp integrations.</p>
                        
                        <h6>Available Tests:</h6>
                        <ul>
                            <li><strong>Simple WhatsApp Integration:</strong> Tests basic message sending</li>
                            <li><strong>Production WhatsApp Integration:</strong> Tests advanced features with business hours</li>
                            <li><strong>Order Hooks:</strong> Tests order-related messaging</li>
                            <li><strong>Birthday Wishes:</strong> Tests birthday message functionality</li>
                            <li><strong>Cart Abandonment:</strong> Tests cart recovery messages</li>
                            <li><strong>Order Placed (Exe):</strong> Tests the order placement notification</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

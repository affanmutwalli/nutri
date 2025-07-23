<?php
session_start();
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

// Test results storage
$testResults = [];
$selectedTest = $_GET['test'] ?? '';

// Interakt API Configuration
$apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
$apiUrl = "https://api.interakt.ai/v1/public/message/";

// Test phone number (you can change this)
$testPhoneNumber = "8329566751"; // Your test number

// Function to send WhatsApp message
function sendWhatsAppMessage($templateName, $phoneNumber, $bodyValues = [], $headerValues = [], $buttonValues = null) {
    global $apiKey, $apiUrl;
    
    $payload = [
        "countryCode" => "+91",
        "phoneNumber" => $phoneNumber,
        "callbackData" => $templateName . '_test_' . time(),
        "type" => "Template",
        "template" => [
            "name" => $templateName,
            "languageCode" => "en",
            "bodyValues" => $bodyValues
        ]
    ];
    
    if (!empty($headerValues)) {
        $payload["template"]["headerValues"] = $headerValues;
    }
    
    if ($buttonValues !== null) {
        $payload["template"]["buttonValues"] = $buttonValues;
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
    
    return [
        'success' => ($httpCode == 201),
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'payload' => $payload
    ];
}

// Handle test execution
if ($_POST && isset($_POST['run_test'])) {
    $testType = $_POST['test_type'];
    $phoneNumber = $_POST['phone_number'] ?: $testPhoneNumber;
    
    switch ($testType) {
        case 'order_placed':
            $result = sendWhatsAppMessage('order_placed_prepaid', $phoneNumber, [
                "Test Customer",
                "My Nutrify",
                "3-5 business days"
            ]);
            break;
            
        case 'birthday_wishes':
            $result = sendWhatsAppMessage('birthday_wishes', $phoneNumber, [
                "Test Customer",
                "BIRTHDAY20",
                "20%",
                "My Nutrify"
            ], [
                "https://mynutrify.com/cms/images/birthday-banner.jpg"
            ]);
            break;
            
        case 'anniversary_wishes':
            $result = sendWhatsAppMessage('anniversary_wishes', $phoneNumber, [
                "Test Customer",
                "2",
                "ANNIVERSARY25",
                "25%"
            ]);
            break;
            
        case 'cart_abandonment':
            $result = sendWhatsAppMessage('your_cart_is_waiting', $phoneNumber, [
                "Test Customer",
                "Cholesterol Care Juice"
            ], [
                "https://mynutrify.com/cms/images/products/2974.png"
            ], new stdClass());
            break;
            
        case 'payment_reminder':
            $result = sendWhatsAppMessage('payment_reminder', $phoneNumber, [
                "Test Customer",
                "MN001234",
                "₹599"
            ]);
            break;
            
        case 'feedback_request':
            $result = sendWhatsAppMessage('feedback_request', $phoneNumber, [
                "Test Customer",
                "MN001234"
            ]);
            break;
            
        case 'order_shipped':
            $result = sendWhatsAppMessage('order_shipped', $phoneNumber, [
                "Test Customer",
                "MN001234",
                "TRK123456789"
            ]);
            break;
            
        case 'out_for_delivery':
            $result = sendWhatsAppMessage('out_for_delivery', $phoneNumber, [
                "Test Customer",
                "MN001234",
                date('d M Y')
            ]);
            break;
            
        case 'order_delivered':
            $result = sendWhatsAppMessage('order_delivered', $phoneNumber, [
                "Test Customer",
                "MN001234"
            ]);
            break;
            
        case 'payment_success':
            $result = sendWhatsAppMessage('payment_success', $phoneNumber, [
                "Test Customer",
                "MN001234",
                "₹599"
            ]);
            break;
            
        case 'payment_failed':
            $result = sendWhatsAppMessage('payment_failed', $phoneNumber, [
                "Test Customer",
                "MN001234",
                "₹599"
            ]);
            break;
            
        default:
            $result = ['success' => false, 'error' => 'Unknown test type'];
    }
    
    $testResults[$testType] = $result;
}

// Get sample order data for realistic testing
$sampleOrders = $obj->MysqliSelect1(
    "SELECT OrderId, CustomerId, TotalAmount FROM order_master ORDER BY OrderId DESC LIMIT 5",
    array("OrderId", "CustomerId", "TotalAmount"), "", array()
);

$sampleCustomers = $obj->MysqliSelect1(
    "SELECT CustomerId, Name, MobileNo FROM customer_master ORDER BY CustomerId DESC LIMIT 5",
    array("CustomerId", "Name", "MobileNo"), "", array()
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interakt WhatsApp Integration Tester - My Nutrify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-card {
            border-left: 4px solid #28a745;
            margin-bottom: 20px;
        }
        .test-card.warning {
            border-left-color: #ffc107;
        }
        .test-card.danger {
            border-left-color: #dc3545;
        }
        .result-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .result-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .payload-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
        .template-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fab fa-whatsapp text-success"></i>
                    Interakt WhatsApp Integration Tester
                </h1>
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Testing Information:</strong><br>
                    • API Endpoint: <code><?php echo $apiUrl; ?></code><br>
                    • Default Test Number: <code>+91 <?php echo $testPhoneNumber; ?></code><br>
                    • All tests use actual Interakt API calls<br>
                    • Make sure your templates are approved in Interakt dashboard
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Test Controls -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-play-circle"></i> Run Tests</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Phone Number (without +91)</label>
                                <input type="text" class="form-control" name="phone_number" 
                                       value="<?php echo $testPhoneNumber; ?>" placeholder="10-digit mobile number">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Select Test</label>
                                <select class="form-control" name="test_type" required>
                                    <option value="">Choose a test...</option>
                                    <optgroup label="Order Management">
                                        <option value="order_placed">Order Placed</option>
                                        <option value="order_shipped">Order Shipped</option>
                                        <option value="out_for_delivery">Out for Delivery</option>
                                        <option value="order_delivered">Order Delivered</option>
                                    </optgroup>
                                    <optgroup label="Payment">
                                        <option value="payment_success">Payment Success</option>
                                        <option value="payment_failed">Payment Failed</option>
                                        <option value="payment_reminder">Payment Reminder</option>
                                    </optgroup>
                                    <optgroup label="Customer Engagement">
                                        <option value="birthday_wishes">Birthday Wishes</option>
                                        <option value="anniversary_wishes">Anniversary Wishes</option>
                                        <option value="cart_abandonment">Cart Abandonment</option>
                                        <option value="feedback_request">Feedback Request</option>
                                    </optgroup>
                                </select>
                            </div>
                            
                            <button type="submit" name="run_test" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane"></i> Send Test Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sample Data -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6><i class="fas fa-database"></i> Sample Data</h6>
                    </div>
                    <div class="card-body">
                        <h6>Recent Orders:</h6>
                        <?php foreach (array_slice($sampleOrders, 0, 3) as $order): ?>
                            <small class="d-block">Order #<?php echo $order['OrderId']; ?> - ₹<?php echo $order['TotalAmount']; ?></small>
                        <?php endforeach; ?>
                        
                        <h6 class="mt-3">Recent Customers:</h6>
                        <?php foreach (array_slice($sampleCustomers, 0, 3) as $customer): ?>
                            <small class="d-block"><?php echo htmlspecialchars($customer['Name']); ?> - <?php echo $customer['MobileNo']; ?></small>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div class="col-md-8">
                <?php if (!empty($testResults)): ?>
                    <?php foreach ($testResults as $testType => $result): ?>
                        <div class="card test-card <?php echo $result['success'] ? '' : 'danger'; ?>">
                            <div class="card-header">
                                <h5>
                                    <?php if ($result['success']): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                    <?php endif; ?>
                                    Test: <?php echo ucwords(str_replace('_', ' ', $testType)); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert <?php echo $result['success'] ? 'result-success' : 'result-error'; ?>">
                                    <strong>Status:</strong> 
                                    <?php echo $result['success'] ? 'SUCCESS' : 'FAILED'; ?>
                                    (HTTP <?php echo $result['http_code']; ?>)
                                    
                                    <?php if (!empty($result['error'])): ?>
                                        <br><strong>Error:</strong> <?php echo htmlspecialchars($result['error']); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <h6>API Response:</h6>
                                <div class="payload-display p-2">
                                    <?php echo htmlspecialchars($result['response']); ?>
                                </div>
                                
                                <h6 class="mt-3">Sent Payload:</h6>
                                <div class="payload-display p-2">
                                    <?php echo htmlspecialchars(json_encode($result['payload'], JSON_PRETTY_PRINT)); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Template Information -->
                    <div class="row">
                        <div class="col-12">
                            <h4><i class="fas fa-list"></i> Available Templates & Tests</h4>

                            <div class="template-info">
                                <h5><i class="fas fa-shopping-cart"></i> Order Management Templates</h5>
                                <ul>
                                    <li><strong>order_placed_prepaid:</strong> Sent when customer places an order</li>
                                    <li><strong>order_shipped:</strong> Sent when order is shipped with tracking</li>
                                    <li><strong>out_for_delivery:</strong> Sent when order is out for delivery</li>
                                    <li><strong>order_delivered:</strong> Sent when order is delivered</li>
                                </ul>
                            </div>

                            <div class="template-info">
                                <h5><i class="fas fa-credit-card"></i> Payment Templates</h5>
                                <ul>
                                    <li><strong>payment_success:</strong> Sent when payment is successful</li>
                                    <li><strong>payment_failed:</strong> Sent when payment fails</li>
                                    <li><strong>payment_reminder:</strong> Sent to remind about pending payment</li>
                                </ul>
                            </div>

                            <div class="template-info">
                                <h5><i class="fas fa-heart"></i> Customer Engagement Templates</h5>
                                <ul>
                                    <li><strong>birthday_wishes:</strong> Birthday greetings with discount code</li>
                                    <li><strong>anniversary_wishes:</strong> Anniversary wishes with special offers</li>
                                    <li><strong>your_cart_is_waiting:</strong> Cart abandonment recovery message</li>
                                    <li><strong>feedback_request:</strong> Request for order feedback/review</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <strong><i class="fas fa-exclamation-triangle"></i> Important Notes:</strong>
                                <ul class="mb-0">
                                    <li>All templates must be approved in your Interakt dashboard</li>
                                    <li>Test messages will be sent to the specified phone number</li>
                                    <li>Check your WhatsApp for received messages</li>
                                    <li>HTTP 201 = Success, other codes indicate errors</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

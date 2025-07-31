<?php
// Test complete payment flow
header('Content-Type: text/html');

echo "<h2>Testing Complete Payment Flow</h2>";

try {
    $mysqli = new mysqli("localhost", "root", "", "my_nutrify_db");
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Database connection successful<br><br>";
    
    // Step 1: Create a test pending order
    $testOrderId = 'ON' . str_pad(rand(100000, 999999), 6, "0", STR_PAD_LEFT);
    $testOrderData = [
        'OrderId' => $testOrderId,
        'CustomerId' => 2, // Using existing customer
        'CustomerType' => 'Registered',
        'Amount' => 299.00,
        'ShipAddress' => 'Test Address, Test City, Test State - 123456',
        'PaymentType' => 'Online',
        'RazorpayOrderId' => 'order_test_' . time(),
        'products' => [
            [
                'ProductId' => 1,
                'ProductCode' => 'TEST001',
                'Size' => 'Medium',
                'Quantity' => 1,
                'Price' => 299.00,
                'SubTotal' => 299.00
            ]
        ],
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Insert into pending_orders table
    $orderDataJson = json_encode($testOrderData);
    $insertPendingQuery = "INSERT INTO pending_orders (order_id, order_data, created_at) VALUES (?, ?, NOW())";
    $pendingStmt = $mysqli->prepare($insertPendingQuery);
    
    if ($pendingStmt) {
        $pendingStmt->bind_param("ss", $testOrderId, $orderDataJson);
        if ($pendingStmt->execute()) {
            echo "✅ Test pending order created: $testOrderId<br>";
        } else {
            echo "❌ Failed to create pending order: " . $pendingStmt->error . "<br>";
            exit;
        }
        $pendingStmt->close();
    }
    
    // Step 2: Simulate payment callback data
    $razorpayPaymentId = 'pay_test_' . time();
    $razorpayOrderId = $testOrderData['RazorpayOrderId'];
    
    // Generate correct signature using the secret key
    $secretKey = '2C8q79zzBNMd6jadotjz6Tci';
    $razorpaySignature = hash_hmac('sha256', $razorpayOrderId . "|" . $razorpayPaymentId, $secretKey);
    
    $callbackData = [
        'razorpay_payment_id' => $razorpayPaymentId,
        'razorpay_order_id' => $razorpayOrderId,
        'razorpay_signature' => $razorpaySignature,
        'order_db_id' => $testOrderId
    ];
    
    echo "<h3>Step 2: Simulating Payment Callback</h3>";
    echo "<p><strong>Payment Data:</strong></p>";
    echo "<pre>" . json_encode($callbackData, JSON_PRETTY_PRINT) . "</pre>";
    
    // Step 3: Call the payment callback
    $url = 'http://localhost/nutrify/exe_files/razorpay_callback_bulletproof.php';
    $postData = json_encode($callbackData);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<h3>Step 3: Payment Callback Response</h3>";
    echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
    
    if ($error) {
        echo "<p style='color: red;'><strong>cURL Error:</strong> $error</p>";
    } else {
        echo "<p><strong>Response:</strong></p>";
        echo "<pre>$response</pre>";
        
        // Try to decode JSON
        $jsonResponse = json_decode($response, true);
        if ($jsonResponse) {
            echo "<p><strong>Parsed Response:</strong></p>";
            echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
            
            if ($jsonResponse['status'] === 'success') {
                echo "<h3 style='color: green;'>✅ PAYMENT PROCESSING SUCCESSFUL!</h3>";
                
                // Check if order was created in database
                $checkOrderQuery = "SELECT * FROM order_master WHERE OrderId = ?";
                $checkStmt = $mysqli->prepare($checkOrderQuery);
                if ($checkStmt) {
                    $checkStmt->bind_param("s", $testOrderId);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    
                    if ($result && $result->num_rows > 0) {
                        $orderRow = $result->fetch_assoc();
                        echo "<p>✅ Order created in database with status: " . $orderRow['PaymentStatus'] . "</p>";
                        echo "<p>✅ Transaction ID: " . $orderRow['TransactionId'] . "</p>";
                    } else {
                        echo "<p>❌ Order not found in database</p>";
                    }
                    $checkStmt->close();
                }
                
                // Check if pending order was cleaned up
                $checkPendingQuery = "SELECT * FROM pending_orders WHERE order_id = ?";
                $checkPendingStmt = $mysqli->prepare($checkPendingQuery);
                if ($checkPendingStmt) {
                    $checkPendingStmt->bind_param("s", $testOrderId);
                    $checkPendingStmt->execute();
                    $pendingResult = $checkPendingStmt->get_result();
                    
                    if ($pendingResult && $pendingResult->num_rows === 0) {
                        echo "<p>✅ Pending order data cleaned up successfully</p>";
                    } else {
                        echo "<p>⚠️ Pending order data still exists (may be intentional)</p>";
                    }
                    $checkPendingStmt->close();
                }
                
            } else {
                echo "<h3 style='color: red;'>❌ Payment processing failed</h3>";
                echo "<p>Error: " . ($jsonResponse['message'] ?? 'Unknown error') . "</p>";
            }
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

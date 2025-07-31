<?php
// Test with real order structure from actual orders
header('Content-Type: text/html');

echo "<h2>Testing Real Order Structure</h2>";

try {
    $mysqli = new mysqli("localhost", "root", "", "my_nutrify_db");
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Database connection successful<br><br>";
    
    // Step 1: Create a test pending order with REAL structure (like from logs)
    $testOrderId = 'ON' . str_pad(rand(100000, 999999), 6, "0", STR_PAD_LEFT);
    $testOrderData = [
        'OrderId' => $testOrderId,
        'CustomerId' => 2,
        'CustomerType' => 'Registered',
        'Amount' => 299.00,
        'ShipAddress' => 'Test Address, Test City, Test State - 123456',
        'PaymentType' => 'Online',
        'RazorpayOrderId' => 'order_test_' . time(),
        'products' => [
            [
                'id' => 15,  // Real structure uses 'id' not 'ProductId'
                'name' => 'Test Product Name',
                'code' => 'TEST001',  // Real structure uses 'code' not 'ProductCode'
                'size' => '500ml',  // Real structure uses 'size' not 'Size'
                'quantity' => 2,  // Real structure uses 'quantity' not 'Quantity'
                'offer_price' => 149.50,  // Real structure uses 'offer_price' not 'Price'
                'image' => 'test.jpg'
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
            echo "✅ Test pending order created with REAL structure: $testOrderId<br>";
        } else {
            echo "❌ Failed to create pending order: " . $pendingStmt->error . "<br>";
            exit;
        }
        $pendingStmt->close();
    }
    
    // Step 2: Simulate payment callback data
    $razorpayPaymentId = 'pay_real_test_' . time();
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
    
    echo "<h3>Step 2: Simulating Payment Callback with Real Data Structure</h3>";
    echo "<p><strong>Order Data Structure:</strong></p>";
    echo "<pre>" . json_encode($testOrderData, JSON_PRETTY_PRINT) . "</pre>";
    
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
                echo "<h3 style='color: green;'>✅ REAL ORDER STRUCTURE PROCESSING SUCCESSFUL!</h3>";
                
                // Check order details
                $checkDetailsQuery = "SELECT * FROM order_details WHERE OrderId = ?";
                $checkDetailsStmt = $mysqli->prepare($checkDetailsQuery);
                if ($checkDetailsStmt) {
                    $checkDetailsStmt->bind_param("s", $testOrderId);
                    $checkDetailsStmt->execute();
                    $detailsResult = $checkDetailsStmt->get_result();
                    
                    if ($detailsResult && $detailsResult->num_rows > 0) {
                        echo "<h4>Order Details Created:</h4>";
                        echo "<table border='1' style='border-collapse: collapse;'>";
                        echo "<tr><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Size</th><th>Quantity</th><th>Price</th><th>SubTotal</th></tr>";
                        
                        while ($detailRow = $detailsResult->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $detailRow['OrderId'] . "</td>";
                            echo "<td>" . $detailRow['ProductId'] . "</td>";
                            echo "<td>" . $detailRow['ProductCode'] . "</td>";
                            echo "<td>" . $detailRow['Size'] . "</td>";
                            echo "<td>" . $detailRow['Quantity'] . "</td>";
                            echo "<td>" . $detailRow['Price'] . "</td>";
                            echo "<td>" . $detailRow['SubTotal'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                    $checkDetailsStmt->close();
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

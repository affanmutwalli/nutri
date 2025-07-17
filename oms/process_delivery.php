<?php
require 'db_config.php'; // Include your database connection

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validate input
    if(empty($data['orderId']) || empty($data['customer'])) {
        throw new Exception('Invalid order data');
    }

    // Prepare Delhivery payload
    $payload = [
        'reference_number' => $data['orderId'],
        'payment_mode' => strtoupper($data['paymentMode']) === 'COD' ? 'COD' : 'PREPAID',
        'cod_amount' => strtoupper($data['paymentMode']) === 'COD' ? (float)$data['totalAmount'] : 0,
        'order_date' => date('Y-m-d H:i:s'),
        'shipments' => [
            [
                'waybill' => '',
                'name' => $data['customer']['name'],
                'phone' => $data['customer']['phone'],
                'address' => $data['customer']['address'],
                'city' => 'Mumbai', // Extract from your address data
                'state' => 'Maharashtra', // Extract from your address data
                'pin_code' => '400001', // Extract from your address data
                'country' => 'India',
                'products' => [],
                'total_amount' => (float)$data['totalAmount'],
                'weight' => 0.5 // Calculate based on products
            ]
        ]
    ];

    // Add products
    foreach($data['products'] as $product) {
        $payload['shipments'][0]['products'][] = [
            'name' => $product['ProductName'], // Fetch from your DB
            'sku' => $product['ProductCode'],
            'quantity' => (int)$product['Quantity'],
            'price' => (float)$product['Price']
        ];
    }

    // Get authentication token
    $clientId = 'YOUR_CLIENT_ID';
    $clientSecret = 'ca5b4c846fa361568ec30623a2952b35ab44684e';
    
    $authResponse = json_decode(file_get_contents('https://one.delhivery.com/api/token', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: Basic " . base64_encode("$clientId:$clientSecret"),
            'content' => json_encode(['grant_type' => 'client_credentials'])
        ]
    ])));

    if(!$authResponse || !isset($authResponse->access_token)) {
        throw new Exception('Authentication failed');
    }

    // Create order
    $ch = curl_init('https://one.delhivery.com/api/v2/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $authResponse->access_token
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode !== 200) {
        throw new Exception('API Error: ' . $response);
    }

    $responseData = json_decode($response, true);
    
    if(isset($responseData['shipments'][0]['waybill'])) {
        // Update database with waybill number
        $waybill = $responseData['shipments'][0]['waybill'];
        $stmt = $obj->MysqliQuery(
            "UPDATE order_master SET WaybillNumber = ? WHERE OrderId = ?",
            "si",
            [$waybill, $data['orderId']]
        );
        
        echo json_encode(['success' => true, 'waybill' => $waybill]);
    } else {
        throw new Exception('Failed to create order');
    }

} catch(Exception $e) {
    error_log('Delhivery Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
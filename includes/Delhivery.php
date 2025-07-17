<?php

class Delhivery {
    private $apiKey;
    private $baseUrl;
    private $config;
    private $isTestMode = true; // Set to false for production
    private $isMockMode = true; // Set to false to use real API

    public function __construct($apiKey = null) {
        $this->loadConfig();
        $this->apiKey = $apiKey ?? $this->config['delhivery_api_key'] ?? null;

        // Override default test/mock modes with database configuration
        if (isset($this->config['delhivery_test_mode'])) {
            $this->isTestMode = (bool)$this->config['delhivery_test_mode'];
        }
        if (isset($this->config['delhivery_mock_mode'])) {
            $this->isMockMode = (bool)$this->config['delhivery_mock_mode'];
        }

        // Use staging environment for testing to avoid charges
        $this->baseUrl = $this->isTestMode
            ? 'https://staging-express.delhivery.com/api'  // TEST - No charges
            : 'https://track.delhivery.com/api';           // PRODUCTION - Real charges

        if (empty($this->apiKey)) {
            throw new Exception('Delhivery API key not configured');
        }
    }

    /**
     * Set test mode - use staging environment to avoid charges
     */
    public function setTestMode($testMode = true) {
        $this->isTestMode = $testMode;
        $this->baseUrl = $this->isTestMode
            ? 'https://staging-express.delhivery.com/api'  // TEST - No charges
            : 'https://track.delhivery.com/api';           // PRODUCTION - Real charges
    }

    /**
     * Check if in test mode
     */
    public function isTestMode() {
        return $this->isTestMode;
    }

    /**
     * Set mock mode - use simulated responses for testing
     */
    public function setMockMode($mockMode = true) {
        $this->isMockMode = $mockMode;
    }

    /**
     * Check if in mock mode
     */
    public function isMockMode() {
        return $this->isMockMode;
    }

    private function loadConfig() {
        global $conn, $mysqli;
        $this->config = [];

        // Try to get database connection from global variables
        $connection = $conn ?? $mysqli ?? null;

        if (!$connection) {
            // Try to establish connection if none exists
            try {
                $connection = new mysqli("localhost", "root", "", "my_nutrify_db");
                if ($connection->connect_error) {
                    error_log('Delhivery: Database connection failed: ' . $connection->connect_error);
                    return; // Continue with default values
                }
            } catch (Exception $e) {
                error_log('Delhivery: Failed to create database connection: ' . $e->getMessage());
                return; // Continue with default values
            }
        }

        $sql = "SELECT config_key, config_value FROM shipping_config WHERE config_key LIKE 'delhivery_%'";
        $result = mysqli_query($connection, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $this->config[$row['config_key']] = $row['config_value'];
            }
        }
    }
    
    public function createOrder($orderData) {
        try {
            // Mock mode - return simulated success response
            if ($this->isMockMode) {
                return [
                    'success' => true,
                    'waybill' => 'MOCK' . time() . rand(100, 999),
                    'status' => 'Created',
                    'message' => 'Order created successfully (Mock Mode)',
                    'mock_mode' => true
                ];
            }

            $payload = $this->prepareOrderPayload($orderData);

            $url = "{$this->baseUrl}/cmu/create.json";

            // Prepare data in the format Delhivery expects
            $postData = http_build_query([
                "format" => "json",
                "data" => json_encode($payload)
            ]);
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Delhivery order creation failed: ' . $e->getMessage()
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                "Authorization: Token {$this->apiKey}"
            ],
            CURLOPT_POSTFIELDS => $postData
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Delhivery API error: {$error}");
        }
        
        if ($httpCode !== 200) {
            throw new Exception("Delhivery API error: HTTP {$httpCode} - {$response}");
        }
        
        $result = json_decode($response, true);

        if (!$result || !isset($result['packages'])) {
            throw new Exception('Invalid response from Delhivery API');
        }

        // Process the response to extract waybill
        $package = $result['packages'][0] ?? null;

        if (!$package) {
            throw new Exception('No package data in Delhivery response');
        }

        // Check if the order creation was successful
        if (isset($package['status']) && $package['status'] === 'Fail') {
            $errorMessage = 'Delhivery order creation failed';
            if (isset($package['remarks']) && is_array($package['remarks'])) {
                $errorMessage .= ': ' . implode(', ', $package['remarks']);
            } elseif (isset($package['remarks'])) {
                $errorMessage .= ': ' . $package['remarks'];
            }
            throw new Exception($errorMessage);
        }

        // Extract waybill
        $waybill = $package['waybill'] ?? null;

        if (empty($waybill)) {
            throw new Exception('No waybill returned from Delhivery API');
        }

        // Return standardized response
        return [
            'success' => true,
            'waybill' => $waybill,
            'tracking_url' => "https://www.delhivery.com/track/package/{$waybill}",
            'status' => 'Created',
            'message' => 'Order created successfully',
            'raw_response' => $result
        ];
    }
    
    public function trackShipment($waybill) {
        // Mock mode - return simulated tracking data
        if ($this->isMockMode) {
            return [
                'success' => true,
                'data' => [
                    [
                        'Status' => [
                            'Status' => 'In Transit',
                            'StatusDateTime' => date('Y-m-d H:i:s'),
                            'StatusLocation' => 'Mumbai Hub'
                        ],
                        'Scans' => [
                            [
                                'ScanDateTime' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                                'ScanType' => 'UD',
                                'Scan' => 'Shipment picked up',
                                'StatusCode' => 'UD',
                                'Location' => 'Origin Hub'
                            ],
                            [
                                'ScanDateTime' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                                'ScanType' => 'IT',
                                'Scan' => 'In transit',
                                'StatusCode' => 'IT',
                                'Location' => 'Mumbai Hub'
                            ]
                        ]
                    ]
                ],
                'waybill' => $waybill,
                'mock_mode' => true
            ];
        }

        $url = "{$this->baseUrl}/v1/packages/";
        $params = [
            'waybill' => $waybill,
            'verbose' => 2
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                "Authorization: Token {$this->apiKey}"
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Delhivery tracking error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new Exception("Delhivery tracking error: HTTP {$httpCode}");
        }

        return json_decode($response, true);
    }
    
    public function cancelOrder($waybill) {
        $url = "{$this->baseUrl}/p/edit";
        
        $payload = [
            'waybill' => $waybill,
            'cancellation' => true
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Token {$this->apiKey}"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload)
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Delhivery cancellation error: {$error}");
        }
        
        if ($httpCode !== 200) {
            throw new Exception("Delhivery cancellation error: HTTP {$httpCode}");
        }
        
        return json_decode($response, true);
    }
    
    public function testConnection() {
        // Test connection using the correct Delhivery API endpoint
        $url = "{$this->baseUrl}/v1/packages/";
        $params = ['waybill' => 'test123']; // Use a test waybill

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                "Authorization: Token {$this->apiKey}"
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Connection test failed: {$error}");
        }

        // For Delhivery API:
        // - 200: Success (API accessible, even if waybill not found)
        // - 401: Unauthorized (wrong API key)
        // - 404: Not found (could be wrong endpoint or waybill)
        if ($httpCode === 200 || $httpCode === 404) {
            // 404 is acceptable for test connection as the test waybill doesn't exist
            return true;
        }

        if ($httpCode === 401) {
            throw new Exception("Connection test failed: Invalid API key (HTTP 401)");
        }

        throw new Exception("Connection test failed: HTTP {$httpCode} - {$response}");
    }

    private function validateOrderData($orderData) {
        $missingFields = [];

        // Check each required field more specifically
        if (!isset($orderData['order_id']) || trim($orderData['order_id']) === '') {
            $missingFields[] = 'order_id';
        }

        if (!isset($orderData['customer_name']) || trim($orderData['customer_name']) === '') {
            $missingFields[] = 'customer_name';
        }

        if (!isset($orderData['customer_phone']) || trim($orderData['customer_phone']) === '') {
            $missingFields[] = 'customer_phone';
        }

        if (!isset($orderData['shipping_address']) || trim($orderData['shipping_address']) === '') {
            $missingFields[] = 'shipping_address';
        }

        if (!isset($orderData['total_amount']) || $orderData['total_amount'] <= 0) {
            $missingFields[] = 'total_amount';
        }

        if (!empty($missingFields)) {
            // Debug: Show what we actually received
            $debugInfo = [];
            foreach (['order_id', 'customer_name', 'customer_phone', 'shipping_address', 'total_amount'] as $field) {
                $value = isset($orderData[$field]) ? $orderData[$field] : 'NOT_SET';
                $debugInfo[] = "$field: '$value'";
            }

            throw new Exception("Missing required fields: " . implode(', ', $missingFields) . ". Received: " . implode(', ', $debugInfo));
        }

        // Validate products array
        if (!isset($orderData['products']) || !is_array($orderData['products'])) {
            // Set default product if missing
            $orderData['products'] = [['name' => 'Product', 'quantity' => 1]];
        }

        return $orderData;
    }

    private function prepareOrderPayload($orderData) {
        // Validate required fields and get cleaned data
        $orderData = $this->validateOrderData($orderData);

        // Extract address components
        $address = $this->parseAddress($orderData['shipping_address'] ?? '');

        // Use the exact structure from working create_order.php
        $payload = [
            "pickup_location" => [
                "name"    => "My Nutrify",
                "city"    => "Sangli",
                "pin"     => "416416",
                "phone"   => "9834243754",
                "address" => "55 North Shivaji Nagar, Near Apta Police Chowk, Sangli - 416416"
            ],
            "shipments" => [
                [
                    "name"           => $orderData['customer_name'] ?? 'Customer',
                    "order"          => $orderData['order_id'] ?? '',
                    "products_desc"  => $this->getProductsDescription($orderData['products'] ?? []),
                    "amount"         => $orderData['total_amount'] ?? 0,
                    "cod_amount"     => strtoupper($orderData['payment_mode'] ?? '') === 'COD' ? ($orderData['total_amount'] ?? 0) : 0,
                    "quantity"       => $this->getTotalQuantity($orderData['products'] ?? []),
                    "payment_mode"   => strtoupper($orderData['payment_mode'] ?? '') === 'COD' ? 'COD' : 'Prepaid',
                    "phone"          => $orderData['customer_phone'] ?? '',
                    "add"            => $address['address'],
                    "city"           => $address['city'],
                    "pin"            => $address['pincode'],
                    "state"          => $address['state'],
                    "shipment_length"         => 10,
                    "shipment_width"        => 10,
                    "shipment_height"         => 10,
                    "weight"         => $orderData['weight'] ?? 0.5,
                    "photo"          => "",
                    "volume"         => 1000, // 10 * 10 * 10
                    "package_type"   => "BOX",
                    "delivery_type"  => "Express"
                ]
            ]
        ];

        return $payload;
    }
    
    private function parseAddress($fullAddress) {
        // This is a simplified address parser
        // In production, you might want to use a more sophisticated address parsing service

        // Handle null or empty address
        if (empty($fullAddress)) {
            return [
                'address' => 'Address not provided',
                'pincode' => '000000',
                'city' => 'Unknown',
                'state' => 'Unknown',
                'country' => 'India'
            ];
        }

        $lines = explode(',', $fullAddress);
        $pincode = '';
        $city = '';
        $state = '';

        // Extract pincode (assuming it's a 6-digit number)
        foreach ($lines as $line) {
            if (preg_match('/\b\d{6}\b/', trim($line), $matches)) {
                $pincode = $matches[0];
                break;
            }
        }
        
        // For now, return basic structure
        // You should implement proper address parsing based on your address format
        return [
            'address' => $fullAddress,
            'pincode' => $pincode,
            'city' => $city ?: 'Unknown',
            'state' => $state ?: 'Unknown',
            'country' => 'India'
        ];
    }
    
    private function getProductsDescription($products) {
        if (!is_array($products) || empty($products)) {
            return 'Product';
        }

        $descriptions = [];
        foreach ($products as $product) {
            $name = $product['name'] ?? 'Product';
            $quantity = $product['quantity'] ?? 1;
            $descriptions[] = $name . ' (Qty: ' . $quantity . ')';
        }
        return implode(', ', $descriptions);
    }

    private function getTotalQuantity($products) {
        if (!is_array($products) || empty($products)) {
            return 1;
        }

        $total = 0;
        foreach ($products as $product) {
            $total += $product['quantity'] ?? 1;
        }
        return $total;
    }
}

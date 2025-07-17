<?php
/**
 * Simple WhatsApp Integration - NO DATABASE CHANGES REQUIRED
 * Uses your existing customer_master and order_master tables
 */

require_once __DIR__ . '/../database/dbconnection.php';

class SimpleWhatsAppIntegration {
    private $obj;
    private $apiKey;
    private $apiUrl;
    
    public function __construct() {
        $this->obj = new main();
        $this->obj->connection();
        
        // Your existing Interakt API credentials
        $this->apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
        $this->apiUrl = "https://api.interakt.ai/v1/public/message/";
    }
    
    /**
     * Send WhatsApp message using existing templates
     */
    public function sendMessage($templateName, $phoneNumber, $variables = []) {
        try {
            // Validate phone number
            $phoneNumber = $this->validatePhoneNumber($phoneNumber);
            
            // Prepare API payload (same as your working code)
            $payload = [
                "countryCode" => "+91",
                "phoneNumber" => $phoneNumber,
                "callbackData" => $templateName . '_' . time(),
                "type" => "Template",
                "template" => [
                    "name" => $templateName,
                    "languageCode" => "en",
                    "bodyValues" => array_values($variables)
                ]
            ];
            
            // Make API call
            $response = $this->makeApiCall($payload);
            
            if ($response['success']) {
                // Simple file logging (no database)
                $this->logToFile("SUCCESS: $templateName sent to $phoneNumber - ID: " . $response['message_id']);
                
                return [
                    'success' => true,
                    'message_id' => $response['message_id'],
                    'phone_number' => $phoneNumber,
                    'template_name' => $templateName
                ];
            } else {
                $this->logToFile("ERROR: $templateName failed for $phoneNumber - " . $response['error']);
                return [
                    'success' => false,
                    'error' => $response['error']
                ];
            }
            
        } catch (Exception $e) {
            $this->logToFile("EXCEPTION: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send order status update using existing database
     */
    public function sendOrderUpdate($orderId, $status, $trackingNumber = null) {
        try {
            // Get order details from your existing order_master table
            $orderData = $this->obj->MysqliSelect1(
                "SELECT OrderId, CustomerId, Amount FROM order_master WHERE OrderId = ?",
                ["OrderId", "CustomerId", "Amount"],
                "s",
                [$orderId]
            );
            
            if (empty($orderData)) {
                throw new Exception("Order not found: $orderId");
            }
            
            // Get customer details from your existing customer_master table
            $customerData = $this->obj->MysqliSelect1(
                "SELECT Name, MobileNo FROM customer_master WHERE CustomerId = ? AND IsActive = 1",
                ["Name", "MobileNo"],
                "i",
                [$orderData[0]['CustomerId']]
            );
            
            if (empty($customerData)) {
                throw new Exception("Customer not found for order: $orderId");
            }
            
            // Map status to your existing templates
            $templateMap = [
                'shipped' => 'order_shipped',
                'out_for_delivery' => 'out_for_delivery', 
                'delivered' => 'order_delivered'
            ];
            
            $templateName = $templateMap[$status] ?? null;
            if (!$templateName) {
                throw new Exception("Invalid status: $status");
            }
            
            // Prepare variables
            $variables = [
                $customerData[0]['Name'],
                $orderId
            ];
            
            // Add tracking number for shipped orders
            if ($status === 'shipped' && $trackingNumber) {
                $variables[] = $trackingNumber;
            } elseif ($status === 'out_for_delivery') {
                $variables[] = date('d M Y');
            } elseif ($status === 'delivered') {
                $variables[] = 'My Nutrify';
            }
            
            // Send WhatsApp message
            return $this->sendMessage($templateName, $customerData[0]['MobileNo'], $variables);
            
        } catch (Exception $e) {
            $this->logToFile("Order update failed for $orderId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send payment reminder using existing database
     */
    public function sendPaymentReminder($orderId) {
        try {
            // Get order and customer data
            $orderData = $this->obj->MysqliSelect1(
                "SELECT o.OrderId, o.CustomerId, o.Amount, c.Name, c.MobileNo 
                 FROM order_master o 
                 JOIN customer_master c ON o.CustomerId = c.CustomerId 
                 WHERE o.OrderId = ? AND c.IsActive = 1",
                ["OrderId", "CustomerId", "Amount", "Name", "MobileNo"],
                "s",
                [$orderId]
            );
            
            if (empty($orderData)) {
                throw new Exception("Order or customer not found: $orderId");
            }
            
            $variables = [
                $orderData[0]['Name'],
                $orderId,
                '₹' . number_format($orderData[0]['Amount'], 2),
                '24 hours'
            ];
            
            return $this->sendMessage('payment_reminder', $orderData[0]['MobileNo'], $variables);
            
        } catch (Exception $e) {
            $this->logToFile("Payment reminder failed for $orderId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send feedback request using existing database
     */
    public function sendFeedbackRequest($orderId) {
        try {
            // Get order, customer, and product data
            $orderData = $this->obj->MysqliSelect1(
                "SELECT o.OrderId, o.CustomerId, c.Name, c.MobileNo 
                 FROM order_master o 
                 JOIN customer_master c ON o.CustomerId = c.CustomerId 
                 WHERE o.OrderId = ? AND c.IsActive = 1",
                ["OrderId", "CustomerId", "Name", "MobileNo"],
                "s",
                [$orderId]
            );
            
            if (empty($orderData)) {
                throw new Exception("Order or customer not found: $orderId");
            }
            
            // Get product names for this order
            $productData = $this->obj->MysqliSelect(
                "SELECT p.ProductName 
                 FROM order_details od 
                 JOIN product_master p ON od.ProductId = p.ProductId 
                 WHERE od.OrderId = ?",
                ["ProductName"],
                "s",
                [$orderId]
            );
            
            $productNames = array_column($productData, 'ProductName');
            $productList = implode(', ', $productNames);
            
            $variables = [
                $orderData[0]['Name'],
                $productList,
                'My Nutrify'
            ];
            
            return $this->sendMessage('feedback_request', $orderData[0]['MobileNo'], $variables);
            
        } catch (Exception $e) {
            $this->logToFile("Feedback request failed for $orderId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send birthday wishes - DISABLED (requires DateOfBirth column)
     * This feature is disabled because DateOfBirth column doesn't exist
     */
    public function sendBirthdayWishes() {
        $this->logToFile("Birthday wishes feature disabled - DateOfBirth column not available");
        return [
            'success' => false,
            'error' => 'Birthday wishes feature requires DateOfBirth column in customer_master table'
        ];
    }
    
    /**
     * Make API call to Interakt (same as your working code)
     */
    private function makeApiCall($payload) {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return [
                'success' => false,
                'error' => 'cURL error: ' . $curlError
            ];
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode === 201 && isset($responseData['result']) && $responseData['result'] === true) {
            return [
                'success' => true,
                'message_id' => $responseData['id'] ?? null,
                'response' => $responseData
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseData['message'] ?? 'Unknown API error',
                'http_code' => $httpCode
            ];
        }
    }
    
    /**
     * Validate phone number
     */
    private function validatePhoneNumber($phone) {
        // Remove all non-digits
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid Indian mobile number
        if (preg_match('/^[6-9]\d{9}$/', $phone)) {
            return $phone;
        }
        
        throw new InvalidArgumentException('Invalid phone number format');
    }
    
    /**
     * Simple file logging (no database required)
     */
    private function logToFile($message) {
        $logDir = __DIR__ . '/logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . 'whatsapp_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message" . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
?>

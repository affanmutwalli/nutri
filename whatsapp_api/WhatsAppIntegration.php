<?php
/**
 * Production WhatsApp Integration for Nutrify
 * Integrates with existing order system and database
 */

require_once '../database/dbconnection.php';

class WhatsAppIntegration {
    private $obj;
    private $apiKey;
    private $apiUrl;
    private $logFile;
    
    public function __construct() {
        $this->obj = new main();
        $this->obj->connection();
        
        // Configuration - move to environment variables in production
        $this->apiKey = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
        $this->apiUrl = "https://api.interakt.ai/v1/public/message/";
        
        // Create logs directory
        $logDir = __DIR__ . '/logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $this->logFile = $logDir . 'whatsapp_' . date('Y-m-d') . '.log';
    }
    
    /**
     * Send WhatsApp message using template
     */
    public function sendMessage($templateName, $phoneNumber, $variables = [], $options = []) {
        try {
            // Validate inputs
            $phoneNumber = $this->validatePhoneNumber($phoneNumber);
            $templateName = trim($templateName);
            
            if (empty($templateName)) {
                throw new InvalidArgumentException('Template name is required');
            }
            
            // Check customer opt-out status
            if (isset($options['customer_id']) && $this->isCustomerOptedOut($options['customer_id'])) {
                throw new Exception('Customer has opted out of WhatsApp messages');
            }
            
            // Check for duplicate messages (prevent spam)
            if (isset($options['customer_id']) && $this->isDuplicateMessage($options['customer_id'], $templateName)) {
                throw new Exception('Duplicate message detected within last hour');
            }
            
            // Check business hours (9 AM to 9 PM)
            if (!$this->isBusinessHours() && !($options['ignore_business_hours'] ?? false)) {
                throw new Exception('Outside business hours (9 AM - 9 PM)');
            }
            
            // Prepare API payload
            $payload = [
                "countryCode" => "+91",
                "phoneNumber" => $phoneNumber,
                "callbackData" => $options['callback_data'] ?? $templateName . '_' . time(),
                "type" => "Template",
                "template" => [
                    "name" => $templateName,
                    "languageCode" => $options['language'] ?? "en",
                    "bodyValues" => array_values($variables)
                ]
            ];
            
            // Add header values if provided
            if (isset($options['header_values'])) {
                $payload['template']['headerValues'] = $options['header_values'];
            }
            
            // Add button values if provided
            if (isset($options['button_values'])) {
                $payload['template']['buttonValues'] = $options['button_values'];
            }
            
            // Log message attempt
            $logData = $this->logMessage([
                'customer_id' => $options['customer_id'] ?? null,
                'order_id' => $options['order_id'] ?? null,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'message_type' => $options['message_type'] ?? 'automated',
                'status' => 'sending',
                'payload' => json_encode($payload)
            ]);
            
            // Make API call
            $response = $this->makeApiCall($payload);
            
            if ($response['success']) {
                // Update log with success
                $this->updateMessageStatus($logData['id'], 'sent', $response['message_id']);
                
                $this->log("SUCCESS: Message sent to $phoneNumber using template $templateName. ID: " . $response['message_id']);
                
                return [
                    'success' => true,
                    'message_id' => $response['message_id'],
                    'phone_number' => $phoneNumber,
                    'template_name' => $templateName,
                    'log_id' => $logData['id']
                ];
            } else {
                // Update log with failure
                $this->updateMessageStatus($logData['id'], 'failed', null, $response['error']);
                
                throw new Exception('API call failed: ' . $response['error']);
            }
            
        } catch (Exception $e) {
            $this->log("ERROR: Failed to send message to $phoneNumber: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'phone_number' => $phoneNumber ?? null,
                'template_name' => $templateName ?? null
            ];
        }
    }
    
    /**
     * Send order status update
     */
    public function sendOrderUpdate($orderId, $status, $trackingNumber = null) {
        try {
            // Get order and customer details
            $orderData = $this->getOrderDetails($orderId);
            if (!$orderData) {
                throw new Exception("Order not found: $orderId");
            }
            
            $customerData = $this->getCustomerDetails($orderData['CustomerId']);
            if (!$customerData) {
                throw new Exception("Customer not found for order: $orderId");
            }
            
            // Map status to template
            $templateMap = [
                'shipped' => 'order_shipped',
                'out_for_delivery' => 'out_for_delivery',
                'delivered' => 'order_delivered',
                'cancelled' => 'order_cancelled'
            ];
            
            $templateName = $templateMap[$status] ?? null;
            if (!$templateName) {
                throw new Exception("Invalid order status: $status");
            }
            
            // Prepare variables based on status
            $variables = [$customerData['Name'], $orderId];
            
            if ($status === 'shipped' && $trackingNumber) {
                $variables[] = $trackingNumber;
            } elseif ($status === 'out_for_delivery') {
                $variables[] = date('d M Y');
            } elseif ($status === 'delivered') {
                $variables[] = 'My Nutrify';
            } elseif ($status === 'cancelled') {
                $variables[] = 'Refund will be processed within 5-7 business days';
            }
            
            // Send message
            return $this->sendMessage($templateName, $customerData['MobileNo'], $variables, [
                'customer_id' => $customerData['CustomerId'],
                'order_id' => $orderId,
                'message_type' => 'order_update',
                'callback_data' => 'order_' . $orderId . '_' . $status
            ]);
            
        } catch (Exception $e) {
            $this->log("ERROR: Order update failed for $orderId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send payment reminder
     */
    public function sendPaymentReminder($orderId, $paymentLink = null) {
        try {
            $orderData = $this->getOrderDetails($orderId);
            $customerData = $this->getCustomerDetails($orderData['CustomerId']);
            
            $variables = [
                $customerData['Name'],
                $orderId,
                '₹' . number_format($orderData['Amount'], 2),
                '24 hours'
            ];
            
            $options = [
                'customer_id' => $customerData['CustomerId'],
                'order_id' => $orderId,
                'message_type' => 'payment_reminder',
                'callback_data' => 'payment_reminder_' . $orderId
            ];
            
            if ($paymentLink) {
                $options['button_values'] = (object)["0" => [$paymentLink]];
            }
            
            return $this->sendMessage('payment_reminder', $customerData['MobileNo'], $variables, $options);
            
        } catch (Exception $e) {
            $this->log("ERROR: Payment reminder failed for $orderId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send birthday wish
     */
    public function sendBirthdayWish($customerId, $discountCode = 'BIRTHDAY20') {
        try {
            $customerData = $this->getCustomerDetails($customerId);
            
            $variables = [
                $customerData['Name'],
                $discountCode,
                '20%'
            ];
            
            return $this->sendMessage('birthday_wishes', $customerData['MobileNo'], $variables, [
                'customer_id' => $customerId,
                'message_type' => 'birthday_wish',
                'callback_data' => 'birthday_' . date('Y-m-d')
            ]);
            
        } catch (Exception $e) {
            $this->log("ERROR: Birthday wish failed for customer $customerId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send feedback request
     */
    public function sendFeedbackRequest($orderId, $reviewLink = null) {
        try {
            $orderData = $this->getOrderDetails($orderId);
            $customerData = $this->getCustomerDetails($orderData['CustomerId']);
            
            // Get product names for this order
            $productNames = $this->getOrderProductNames($orderId);
            
            $variables = [
                $customerData['Name'],
                $productNames,
                'My Nutrify'
            ];
            
            $options = [
                'customer_id' => $customerData['CustomerId'],
                'order_id' => $orderId,
                'message_type' => 'feedback_request',
                'callback_data' => 'feedback_' . $orderId
            ];
            
            if ($reviewLink) {
                $options['button_values'] = (object)["0" => [$reviewLink]];
            }
            
            return $this->sendMessage('feedback_request', $customerData['MobileNo'], $variables, $options);
            
        } catch (Exception $e) {
            $this->log("ERROR: Feedback request failed for $orderId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Make API call to Interakt
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
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }
    
    /**
     * Get order details from database
     */
    private function getOrderDetails($orderId) {
        $result = $this->obj->MysqliSelect1(
            "SELECT OrderId, CustomerId, Amount, PaymentStatus, OrderStatus, CreationDate FROM order_master WHERE OrderId = ?",
            ["OrderId", "CustomerId", "Amount", "PaymentStatus", "OrderStatus", "CreationDate"],
            "s",
            [$orderId]
        );
        
        return $result[0] ?? null;
    }
    
    /**
     * Get customer details from database
     */
    private function getCustomerDetails($customerId) {
        $result = $this->obj->MysqliSelect1(
            "SELECT CustomerId, Name, Email, MobileNo, DateOfBirth, whatsapp_opt_in, whatsapp_opt_out FROM customer_master WHERE CustomerId = ? AND IsActive = 1",
            ["CustomerId", "Name", "Email", "MobileNo", "DateOfBirth", "whatsapp_opt_in", "whatsapp_opt_out"],
            "i",
            [$customerId]
        );
        
        return $result[0] ?? null;
    }
    
    /**
     * Get product names for an order
     */
    private function getOrderProductNames($orderId) {
        $result = $this->obj->MysqliSelect(
            "SELECT p.ProductName FROM order_details od 
             JOIN product_master p ON od.ProductId = p.ProductId 
             WHERE od.OrderId = ?",
            ["ProductName"],
            "s",
            [$orderId]
        );
        
        $productNames = array_column($result, 'ProductName');
        return implode(', ', $productNames);
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
     * Check if customer has opted out
     */
    private function isCustomerOptedOut($customerId) {
        $customer = $this->getCustomerDetails($customerId);
        return ($customer['whatsapp_opt_out'] ?? 0) == 1;
    }
    
    /**
     * Check for duplicate messages
     */
    private function isDuplicateMessage($customerId, $templateName, $hours = 1) {
        $result = $this->obj->MysqliSelect1(
            "SELECT COUNT(*) as count FROM whatsapp_message_log 
             WHERE customer_id = ? AND template_name = ? 
             AND sent_at > DATE_SUB(NOW(), INTERVAL ? HOUR)
             AND status != 'failed'",
            ["count"],
            "isi",
            [$customerId, $templateName, $hours]
        );
        
        return ($result[0]["count"] > 0);
    }
    
    /**
     * Check business hours
     */
    private function isBusinessHours() {
        $hour = (int)date('H');
        return ($hour >= 9 && $hour <= 21); // 9 AM to 9 PM
    }
    
    /**
     * Log message to database
     */
    private function logMessage($data) {
        try {
            $sql = "INSERT INTO whatsapp_message_log 
                    (customer_id, order_id, phone_number, template_name, message_type, status, api_response) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $logId = $this->obj->fInsertNew(
                $sql,
                "issssss",
                [
                    $data['customer_id'],
                    $data['order_id'],
                    $data['phone_number'],
                    $data['template_name'],
                    $data['message_type'],
                    $data['status'],
                    $data['payload']
                ]
            );
            
            return ['id' => $logId] + $data;
        } catch (Exception $e) {
            $this->log("ERROR: Failed to log message: " . $e->getMessage());
            return $data;
        }
    }
    
    /**
     * Update message status in database
     */
    private function updateMessageStatus($logId, $status, $messageId = null, $errorMessage = null) {
        try {
            $sql = "UPDATE whatsapp_message_log SET status = ?, message_id = ?, error_message = ? WHERE id = ?";
            $this->obj->MysqliUpdate($sql, "sssi", [$status, $messageId, $errorMessage, $logId]);
        } catch (Exception $e) {
            $this->log("ERROR: Failed to update message status: " . $e->getMessage());
        }
    }
    
    /**
     * Log to file
     */
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

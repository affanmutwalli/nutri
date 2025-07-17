<?php
/**
 * SMS Notification System using Fast2SMS
 * Replaces WhatsApp notifications with SMS notifications
 */

require_once __DIR__ . '/../database/dbconnection.php';

class SMSNotification {
    private $obj;
    private $apiKey;
    private $apiUrl;
    private $adminPhone;
    
    public function __construct() {
        $this->obj = new main();
        $this->obj->connection();
        
        // SMS Local API credentials - Better than Fast2SMS (no minimum transaction required)
        $this->apiKey = "a60c97e59ead22cafccc320b8a622410"; // Your SMS Local API key
        $this->apiUrl = "https://secure.smslocal.com/api/service/enterprise-service/external/sms";
        
        // Admin phone number for notifications (from your memory: 8208593432)
        $this->adminPhone = "8208593432";
    }
    
    /**
     * Send SMS using Fast2SMS API
     */
    public function sendSMS($phoneNumber, $message, $templateId = null) {
        try {
            // Validate phone number
            $phoneNumber = $this->validatePhoneNumber($phoneNumber);
            
            // Prepare API payload for SMS Local
            $payload = [
                "from" => "MyNutrify", // Sender ID (alphanumeric, max 11 chars)
                "to" => $phoneNumber,
                "content" => $message,
                "datacoding" => "0" // 0 for GSM7, 1 for Unicode
            ];
            
            // Make API call
            $response = $this->makeApiCall($payload);
            
            if ($response['success']) {
                // Log success
                $this->logToFile("SUCCESS: SMS sent to $phoneNumber - Message: " . substr($message, 0, 50) . "...");
                
                return [
                    'success' => true,
                    'message_id' => $response['message_id'] ?? 'N/A',
                    'phone_number' => $phoneNumber,
                    'message' => $message
                ];
            } else {
                $this->logToFile("ERROR: SMS failed for $phoneNumber - " . $response['error']);
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
     * Send order placed notification to admin
     */
    public function sendOrderPlacedNotification($orderId) {
        try {
            // Get order details
            $orderData = $this->getOrderDetails($orderId);
            
            if (empty($orderData)) {
                throw new Exception("Order not found: $orderId");
            }
            
            $message = "🛒 NEW ORDER ALERT!\n";
            $message .= "Order ID: {$orderData['OrderId']}\n";
            $message .= "Customer: {$orderData['CustomerName']}\n";
            $message .= "Phone: {$orderData['CustomerPhone']}\n";
            $message .= "Amount: ₹{$orderData['Amount']}\n";
            $message .= "Time: " . date('d-M-Y H:i');
            
            return $this->sendSMS($this->adminPhone, $message);
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send order shipped notification to admin
     */
    public function sendAdminOrderShippedNotification($orderId, $trackingNumber = null) {
        try {
            // Get order and customer details
            $orderData = $this->getOrderDetails($orderId);

            if (empty($orderData)) {
                throw new Exception("Order not found: $orderId");
            }

            $message = "📦 ORDER SHIPPED ALERT!\n";
            $message .= "Order ID: {$orderData['OrderId']}\n";
            $message .= "Customer: {$orderData['CustomerName']}\n";
            $message .= "Phone: {$orderData['CustomerPhone']}\n";
            $message .= "Amount: ₹{$orderData['Amount']}\n";

            if ($trackingNumber) {
                $message .= "Tracking: $trackingNumber\n";
            }

            $message .= "Time: " . date('d-M-Y H:i');

            return $this->sendSMS($this->adminPhone, $message);

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send order delivered notification to customer
     */
    public function sendOrderDeliveredNotification($orderId) {
        try {
            // Get order and customer details
            $orderData = $this->getOrderDetails($orderId);
            
            if (empty($orderData)) {
                throw new Exception("Order not found: $orderId");
            }
            
            $message = "✅ Your order has been delivered!\n";
            $message .= "Order ID: {$orderData['OrderId']}\n";
            $message .= "Thank you for choosing MyNutrify!\n";
            $message .= "Please rate your experience and share feedback.";
            
            return $this->sendSMS($orderData['CustomerPhone'], $message);
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send payment reminder to customer
     */
    public function sendPaymentReminder($orderId) {
        try {
            // Get order details
            $orderData = $this->getOrderDetails($orderId);
            
            if (empty($orderData)) {
                throw new Exception("Order not found: $orderId");
            }
            
            $message = "💳 Payment Reminder\n";
            $message .= "Order ID: {$orderData['OrderId']}\n";
            $message .= "Amount: ₹{$orderData['Amount']}\n";
            $message .= "Please complete your payment to process your order.\n";
            $message .= "Pay now: https://mynutrify.com/payment.php?order_id={$orderData['OrderId']}";
            
            return $this->sendSMS($orderData['CustomerPhone'], $message);
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get order details from database
     */
    private function getOrderDetails($orderId) {
        $orderData = $this->obj->MysqliSelect1(
            "SELECT o.OrderId, o.Amount, c.Name as CustomerName, c.MobileNo as CustomerPhone
             FROM order_master o
             JOIN customer_master c ON o.CustomerId = c.CustomerId
             WHERE o.OrderId = ?",
            ["OrderId", "Amount", "CustomerName", "CustomerPhone"],
            "s",
            [$orderId]
        );

        return !empty($orderData) ? $orderData[0] : null;
    }
    
    /**
     * Validate phone number format
     */
    private function validatePhoneNumber($phoneNumber) {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Remove country code if present
        if (strlen($phoneNumber) == 12 && substr($phoneNumber, 0, 2) == '91') {
            $phoneNumber = substr($phoneNumber, 2);
        }
        
        // Validate 10-digit Indian mobile number
        if (strlen($phoneNumber) != 10 || !preg_match('/^[6-9][0-9]{9}$/', $phoneNumber)) {
            throw new Exception("Invalid phone number format: $phoneNumber");
        }
        
        return $phoneNumber;
    }
    
    /**
     * Make API call to Fast2SMS
     */
    private function makeApiCall($payload) {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Token: ' . $this->apiKey,  // SMS Local uses 'Token' header
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

        // Log the full response for debugging
        $this->logToFile("API Response - HTTP: $httpCode, Body: " . $response);

        if ($httpCode == 200 && isset($responseData['msgid'])) {
            // SMS Local returns msgid on success
            return [
                'success' => true,
                'message_id' => $responseData['msgid'],
                'response' => $responseData
            ];
        } else {
            $errorMsg = $responseData['error'] ?? $responseData['message'] ?? 'Unknown error';
            $errorCode = $responseData['errorcode'] ?? $httpCode;
            return [
                'success' => false,
                'error' => $errorMsg,
                'error_code' => $errorCode,
                'http_code' => $httpCode,
                'full_response' => $responseData
            ];
        }
    }
    
    /**
     * Log messages to file
     */
    private function logToFile($message) {
        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/sms_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
    }
}
?>

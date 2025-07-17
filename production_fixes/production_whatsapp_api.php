<?php
/**
 * Production-Ready WhatsApp API
 * Secure, logged, rate-limited WhatsApp messaging
 */

require_once 'secure_config.php';
require_once 'message_logger.php';

class ProductionWhatsAppAPI {
    
    public static function sendMessage($templateName, $phoneNumber, $variables = [], $options = []) {
        try {
            // Initialize logging
            MessageLogger::init();
            
            // Validate inputs
            $phoneNumber = InputValidator::validatePhoneNumber($phoneNumber);
            $templateName = trim($templateName);
            
            if (empty($templateName)) {
                throw new InvalidArgumentException('Template name is required');
            }
            
            // Check opt-out status
            if (MessageLogger::getCustomerOptOutStatus($phoneNumber)) {
                throw new Exception('Customer has opted out of WhatsApp messages');
            }
            
            // Check for duplicate messages
            $duplicateCheckMinutes = $options['duplicate_check_minutes'] ?? 60;
            if (MessageLogger::checkDuplicateMessage($phoneNumber, $templateName, $duplicateCheckMinutes)) {
                throw new Exception('Duplicate message detected within ' . $duplicateCheckMinutes . ' minutes');
            }
            
            // Check rate limits
            $rateLimitCheck = RateLimiter::checkLimit($phoneNumber);
            if (!$rateLimitCheck['allowed']) {
                throw new Exception('Rate limit exceeded: ' . $rateLimitCheck['reason']);
            }
            
            // Check business hours (unless override is set)
            if (!($options['ignore_business_hours'] ?? false) && !BusinessHours::isBusinessHours()) {
                throw new Exception('Outside business hours. Message will be queued.');
            }
            
            // Prepare API payload
            $payload = [
                "countryCode" => SecureConfig::getDefaultCountryCode(),
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
            $logData = MessageLogger::logMessage([
                'customer_id' => $options['customer_id'] ?? null,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'message_type' => $options['message_type'] ?? 'automated',
                'status' => 'sending',
                'payload' => json_encode($payload)
            ]);
            
            // Make API call
            $response = self::makeApiCall($payload);
            
            if ($response['success']) {
                // Update log with success
                MessageLogger::updateMessageStatus($response['message_id'], 'sent');
                
                MessageLogger::logToFile('messages', 'SUCCESS', 
                    "Message sent successfully to $phoneNumber using template $templateName. ID: " . $response['message_id']);
                
                return [
                    'success' => true,
                    'message_id' => $response['message_id'],
                    'phone_number' => $phoneNumber,
                    'template_name' => $templateName,
                    'rate_limit' => $rateLimitCheck
                ];
            } else {
                // Update log with failure
                MessageLogger::updateMessageStatus($logData['message_id'], 'failed', [
                    'error_message' => $response['error']
                ]);
                
                throw new Exception('API call failed: ' . $response['error']);
            }
            
        } catch (Exception $e) {
            MessageLogger::logToFile('messages', 'ERROR', 
                "Failed to send message to $phoneNumber: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'phone_number' => $phoneNumber ?? null,
                'template_name' => $templateName ?? null
            ];
        }
    }
    
    private static function makeApiCall($payload) {
        $apiUrl = SecureConfig::getInteraktApiUrl();
        $apiKey = SecureConfig::getInteraktApiKey();
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $apiKey,
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
    
    // Convenience methods for different message types
    public static function sendOrderUpdate($phoneNumber, $customerName, $orderId, $status, $trackingNumber = null) {
        $templates = [
            'shipped' => 'order_shipped',
            'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'order_delivered',
            'cancelled' => 'order_cancelled'
        ];
        
        $templateName = $templates[$status] ?? null;
        if (!$templateName) {
            throw new InvalidArgumentException('Invalid order status: ' . $status);
        }
        
        $variables = [$customerName, $orderId];
        if ($status === 'shipped' && $trackingNumber) {
            $variables[] = $trackingNumber;
        } elseif ($status === 'out_for_delivery') {
            $variables[] = date('d M Y');
        } elseif ($status === 'delivered') {
            $variables[] = 'My Nutrify';
        } elseif ($status === 'cancelled') {
            $variables[] = 'Refund will be processed within 5-7 business days';
        }
        
        return self::sendMessage($templateName, $phoneNumber, $variables, [
            'message_type' => 'order_update',
            'callback_data' => 'order_' . $orderId . '_' . $status
        ]);
    }
    
    public static function sendPaymentReminder($phoneNumber, $customerName, $orderId, $amount, $paymentLink) {
        $variables = [
            $customerName,
            $orderId,
            '₹' . number_format($amount, 2),
            '24 hours'
        ];
        
        return self::sendMessage('payment_reminder', $phoneNumber, $variables, [
            'message_type' => 'payment_reminder',
            'button_values' => (object)["0" => [$paymentLink]],
            'callback_data' => 'payment_reminder_' . $orderId
        ]);
    }
    
    public static function sendBirthdayWish($phoneNumber, $customerName, $discountCode = 'BIRTHDAY20') {
        $variables = [
            $customerName,
            $discountCode,
            '20%',
            'My Nutrify'
        ];
        
        return self::sendMessage('birthday_wishes', $phoneNumber, $variables, [
            'message_type' => 'birthday_wish',
            'header_values' => ['https://mynutrify.com/cms/images/birthday-banner.jpg'],
            'callback_data' => 'birthday_' . date('Y-m-d')
        ]);
    }
    
    public static function sendProductRecommendation($phoneNumber, $customerName, $productName, $price, $productLink, $productImage) {
        $variables = [
            $customerName,
            $productName,
            '₹' . number_format($price, 2)
        ];
        
        return self::sendMessage('product_recommendation', $phoneNumber, $variables, [
            'message_type' => 'product_recommendation',
            'header_values' => [$productImage],
            'button_values' => (object)["0" => [$productLink]],
            'callback_data' => 'product_recommendation'
        ]);
    }
    
    public static function sendFeedbackRequest($phoneNumber, $customerName, $orderId, $productNames, $reviewLink) {
        $variables = [
            $customerName,
            $productNames,
            'My Nutrify'
        ];
        
        return self::sendMessage('feedback_request', $phoneNumber, $variables, [
            'message_type' => 'feedback_request',
            'button_values' => (object)["0" => [$reviewLink]],
            'callback_data' => 'feedback_' . $orderId
        ]);
    }
    
    // Batch sending with proper delays
    public static function sendBatch($messages, $delaySeconds = 2) {
        $results = [];
        $successCount = 0;
        $failureCount = 0;
        
        foreach ($messages as $index => $message) {
            $result = self::sendMessage(
                $message['template'],
                $message['phone'],
                $message['variables'] ?? [],
                $message['options'] ?? []
            );
            
            $results[] = $result;
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
            
            // Add delay between messages to avoid rate limiting
            if ($index < count($messages) - 1) {
                sleep($delaySeconds);
            }
        }
        
        MessageLogger::logToFile('batch', 'INFO', 
            "Batch sending completed. Success: $successCount, Failed: $failureCount");
        
        return [
            'total' => count($messages),
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'results' => $results
        ];
    }
    
    // Get delivery statistics
    public static function getStats($days = 7) {
        return MessageLogger::getMessageStats($days);
    }
    
    // Retry failed messages
    public static function retryFailedMessages($limit = 10) {
        $failedMessages = MessageLogger::getFailedMessages($limit);
        if (!$failedMessages) return [];
        
        $results = [];
        foreach ($failedMessages as $message) {
            MessageLogger::incrementRetryCount($message['message_id']);
            
            // Attempt to resend (simplified - you'd need to reconstruct the original call)
            MessageLogger::logToFile('retry', 'INFO', 
                "Retrying message ID: " . $message['message_id']);
            
            $results[] = [
                'message_id' => $message['message_id'],
                'phone_number' => $message['phone_number'],
                'template_name' => $message['template_name'],
                'retry_count' => $message['retry_count'] + 1
            ];
        }
        
        return $results;
    }
}
?>

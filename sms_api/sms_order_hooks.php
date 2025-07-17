<?php
/**
 * SMS Order Hooks - Replace WhatsApp notifications with SMS
 * These functions can be called from your order processing files
 */

require_once __DIR__ . '/SMSNotification.php';

/**
 * Send SMS to admin when order is placed
 * Call this after order creation in order processing files
 */
function sendAdminOrderPlacedSMS($orderId) {
    try {
        $sms = new SMSNotification();
        $result = $sms->sendOrderPlacedNotification($orderId);

        if ($result['success']) {
            error_log("Admin SMS order notification sent for: $orderId");
            return true;
        } else {
            error_log("Admin SMS order notification failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("Admin SMS order notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send SMS to admin when order ships
 * Call this when admin updates order status to shipped
 */
function sendAdminOrderShippedSMS($orderId, $trackingNumber = null) {
    try {
        $sms = new SMSNotification();
        $result = $sms->sendAdminOrderShippedNotification($orderId, $trackingNumber);

        if ($result['success']) {
            error_log("Admin SMS shipping notification sent for: $orderId");
            return true;
        } else {
            error_log("Admin SMS shipping notification failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("Admin SMS shipping notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send SMS when order is delivered
 * Call this when order status is updated to delivered
 */
function sendOrderDeliveredSMS($orderId) {
    try {
        $sms = new SMSNotification();
        $result = $sms->sendOrderDeliveredNotification($orderId);
        
        if ($result['success']) {
            error_log("SMS delivery notification sent for: $orderId");
            return true;
        } else {
            error_log("SMS delivery notification failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("SMS delivery notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send SMS payment reminder
 * Call this for pending payment orders
 */
function sendPaymentReminderSMS($orderId) {
    try {
        $sms = new SMSNotification();
        $result = $sms->sendPaymentReminder($orderId);
        
        if ($result['success']) {
            error_log("SMS payment reminder sent for: $orderId");
            return true;
        } else {
            error_log("SMS payment reminder failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("SMS payment reminder error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send custom SMS notification
 * For any custom notifications needed
 */
function sendCustomSMS($phoneNumber, $message) {
    try {
        $sms = new SMSNotification();
        $result = $sms->sendSMS($phoneNumber, $message);
        
        if ($result['success']) {
            error_log("Custom SMS sent to: $phoneNumber");
            return true;
        } else {
            error_log("Custom SMS failed for: $phoneNumber - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("Custom SMS error: " . $e->getMessage());
        return false;
    }
}

/**
 * Test SMS integration with a specific order
 * Useful for testing
 */
function testSMSWithOrder($orderId, $notificationType = 'shipped') {
    try {
        $sms = new SMSNotification();
        
        switch ($notificationType) {
            case 'placed':
                $result = $sms->sendOrderPlacedNotification($orderId);
                break;
            case 'shipped':
                $result = $sms->sendOrderShippedNotification($orderId, 'TEST123456');
                break;
            case 'delivered':
                $result = $sms->sendOrderDeliveredNotification($orderId);
                break;
            case 'payment_reminder':
                $result = $sms->sendPaymentReminder($orderId);
                break;
            default:
                throw new Exception("Invalid notification type: $notificationType");
        }
        
        return $result;
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Send bulk SMS notifications
 * For sending notifications to multiple orders
 */
function sendBulkSMSNotifications($orderIds, $notificationType = 'shipped') {
    $results = [];
    
    foreach ($orderIds as $orderId) {
        $result = testSMSWithOrder($orderId, $notificationType);
        $results[$orderId] = $result['success'];
        
        // Small delay between SMS to avoid rate limiting
        sleep(1);
    }
    
    return $results;
}

/**
 * Check SMS API configuration
 * Test if SMS service is properly configured
 */
function checkSMSConfiguration() {
    try {
        $sms = new SMSNotification();
        
        // Try to send a test SMS to admin number
        $testMessage = "SMS API Test - " . date('Y-m-d H:i:s');
        $result = $sms->sendSMS("8208593432", $testMessage); // Admin number from memory
        
        return [
            'success' => $result['success'],
            'message' => $result['success'] ? 'SMS API is working' : 'SMS API failed: ' . $result['error']
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'SMS API error: ' . $e->getMessage()
        ];
    }
}

// API endpoint for testing SMS functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'test_order_sms':
            $orderId = $input['order_id'] ?? '';
            $type = $input['type'] ?? 'shipped';
            $result = testSMSWithOrder($orderId, $type);
            break;
            
        case 'send_custom_sms':
            $phone = $input['phone'] ?? '';
            $message = $input['message'] ?? '';
            $result = sendCustomSMS($phone, $message);
            break;
            
        case 'check_config':
            $result = checkSMSConfiguration();
            break;
            
        case 'bulk_sms':
            $orderIds = $input['order_ids'] ?? [];
            $type = $input['type'] ?? 'shipped';
            $result = sendBulkSMSNotifications($orderIds, $type);
            break;
            
        default:
            $result = ["error" => "Invalid action"];
    }
    
    echo json_encode($result);
}
?>

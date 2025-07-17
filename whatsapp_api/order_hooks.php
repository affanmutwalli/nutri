<?php
/**
 * WhatsApp Order Hooks - Simple Integration
 * Add these functions to your existing order processing files
 * NO DATABASE CHANGES REQUIRED
 */

require_once __DIR__ . '/SimpleWhatsAppIntegration.php';

/**
 * Send WhatsApp when order is placed
 * Call this after order creation in rcus_place_order_online.php
 */
function sendOrderPlacedWhatsApp($orderId) {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        // Use existing order_shipped template for order confirmation
        $result = $whatsapp->sendOrderUpdate($orderId, 'shipped');
        
        if ($result['success']) {
            error_log("WhatsApp order confirmation sent for: $orderId");
            return true;
        } else {
            error_log("WhatsApp order confirmation failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("WhatsApp order confirmation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send WhatsApp when payment succeeds
 * Call this in your payment success callback
 */
function sendPaymentSuccessWhatsApp($orderId) {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        // Use delivered template for payment success
        $result = $whatsapp->sendOrderUpdate($orderId, 'delivered');
        
        if ($result['success']) {
            error_log("WhatsApp payment success sent for: $orderId");
            return true;
        } else {
            error_log("WhatsApp payment success failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("WhatsApp payment success error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send WhatsApp when payment fails
 * Call this in your payment failure callback
 */
function sendPaymentFailedWhatsApp($orderId) {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        $result = $whatsapp->sendPaymentReminder($orderId);
        
        if ($result['success']) {
            error_log("WhatsApp payment reminder sent for: $orderId");
            return true;
        } else {
            error_log("WhatsApp payment reminder failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("WhatsApp payment reminder error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send WhatsApp when order ships
 * Call this when admin updates order status to shipped
 */
function sendOrderShippedWhatsApp($orderId, $trackingNumber = null) {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        $result = $whatsapp->sendOrderUpdate($orderId, 'shipped', $trackingNumber);
        
        if ($result['success']) {
            error_log("WhatsApp shipping notification sent for: $orderId");
            return true;
        } else {
            error_log("WhatsApp shipping notification failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("WhatsApp shipping notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send WhatsApp when order is out for delivery
 * Call this when admin updates order status
 */
function sendOutForDeliveryWhatsApp($orderId) {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        $result = $whatsapp->sendOrderUpdate($orderId, 'out_for_delivery');
        
        if ($result['success']) {
            error_log("WhatsApp out for delivery sent for: $orderId");
            return true;
        } else {
            error_log("WhatsApp out for delivery failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("WhatsApp out for delivery error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send WhatsApp when order is delivered
 * Call this when admin marks order as delivered
 */
function sendOrderDeliveredWhatsApp($orderId) {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        $result = $whatsapp->sendOrderUpdate($orderId, 'delivered');
        
        if ($result['success']) {
            error_log("WhatsApp delivery confirmation sent for: $orderId");
            return true;
        } else {
            error_log("WhatsApp delivery confirmation failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("WhatsApp delivery confirmation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send feedback request
 * Call this 2-3 days after delivery
 */
function sendFeedbackRequestWhatsApp($orderId) {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        $result = $whatsapp->sendFeedbackRequest($orderId);
        
        if ($result['success']) {
            error_log("WhatsApp feedback request sent for: $orderId");
            return true;
        } else {
            error_log("WhatsApp feedback request failed for: $orderId - " . $result['error']);
            return false;
        }
    } catch (Exception $e) {
        error_log("WhatsApp feedback request error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send birthday wishes - DISABLED
 * This feature requires DateOfBirth column in customer_master table
 */
function sendDailyBirthdayWishes() {
    error_log("Birthday wishes feature disabled - DateOfBirth column not available");
    return [
        'success' => false,
        'error' => 'Birthday wishes feature requires DateOfBirth column in customer_master table'
    ];
}

/**
 * Bulk send WhatsApp to multiple orders
 * Useful for admin bulk operations
 */
function sendBulkOrderUpdates($orderIds, $status, $trackingNumbers = []) {
    $results = [];
    
    foreach ($orderIds as $index => $orderId) {
        $trackingNumber = $trackingNumbers[$index] ?? null;
        
        switch ($status) {
            case 'shipped':
                $result = sendOrderShippedWhatsApp($orderId, $trackingNumber);
                break;
            case 'out_for_delivery':
                $result = sendOutForDeliveryWhatsApp($orderId);
                break;
            case 'delivered':
                $result = sendOrderDeliveredWhatsApp($orderId);
                break;
            default:
                $result = false;
        }
        
        $results[$orderId] = $result;
        
        // Add delay to avoid rate limiting
        sleep(2);
    }
    
    return $results;
}

/**
 * Test WhatsApp integration with a specific order
 * Useful for testing
 */
function testWhatsAppWithOrder($orderId, $templateType = 'shipped') {
    try {
        $whatsapp = new SimpleWhatsAppIntegration();
        
        switch ($templateType) {
            case 'shipped':
                $result = $whatsapp->sendOrderUpdate($orderId, 'shipped', 'TEST123456');
                break;
            case 'payment_reminder':
                $result = $whatsapp->sendPaymentReminder($orderId);
                break;
            case 'feedback':
                $result = $whatsapp->sendFeedbackRequest($orderId);
                break;
            default:
                throw new Exception("Invalid template type: $templateType");
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
 * Get WhatsApp logs for today
 * Simple file-based log reading
 */
function getTodayWhatsAppLogs() {
    $logFile = __DIR__ . '/logs/whatsapp_' . date('Y-m-d') . '.log';
    
    if (file_exists($logFile)) {
        return file_get_contents($logFile);
    } else {
        return "No logs found for today.";
    }
}

/**
 * Get WhatsApp statistics from logs
 * Parse log files to get basic stats
 */
function getWhatsAppStats($days = 7) {
    $stats = [
        'total_sent' => 0,
        'total_failed' => 0,
        'by_template' => [],
        'by_date' => []
    ];
    
    for ($i = 0; $i < $days; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $logFile = __DIR__ . '/logs/whatsapp_' . $date . '.log';
        
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);
            
            $dailyStats = [
                'sent' => 0,
                'failed' => 0
            ];
            
            foreach ($lines as $line) {
                if (strpos($line, 'SUCCESS:') !== false) {
                    $stats['total_sent']++;
                    $dailyStats['sent']++;
                    
                    // Extract template name
                    if (preg_match('/SUCCESS: (\w+) sent/', $line, $matches)) {
                        $template = $matches[1];
                        $stats['by_template'][$template] = ($stats['by_template'][$template] ?? 0) + 1;
                    }
                } elseif (strpos($line, 'ERROR:') !== false) {
                    $stats['total_failed']++;
                    $dailyStats['failed']++;
                }
            }
            
            $stats['by_date'][$date] = $dailyStats;
        }
    }
    
    return $stats;
}
?>

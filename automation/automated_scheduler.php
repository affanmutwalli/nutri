<?php
/**
 * Automated WhatsApp Scheduler
 * Handles time-based triggers like birthdays, cart abandonment, etc.
 */

require_once '../exe_files/connection.php';
require_once '../whatsapp_api/birthday_wishes.php';
require_once '../whatsapp_api/product_recommendations.php';
require_once '../whatsapp_api/payment_reminders.php';

class WhatsAppScheduler {
    private $obj;
    private $logFile;
    
    public function __construct() {
        $this->obj = new Connection();
        $this->logFile = __DIR__ . '/logs/scheduler_' . date('Y-m-d') . '.log';
        
        // Create logs directory
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0755, true);
        }
    }
    
    /**
     * Main scheduler function
     */
    public function run($taskType = null) {
        $this->log("Scheduler started for task: " . ($taskType ?? 'all'));
        
        try {
            switch ($taskType) {
                case 'birthdays':
                    $this->processBirthdays();
                    break;
                    
                case 'cart_abandonment':
                    $this->processCartAbandonment();
                    break;
                    
                case 'payment_reminders':
                    $this->processPaymentReminders();
                    break;
                    
                case 'reorder_reminders':
                    $this->processReorderReminders();
                    break;
                    
                case 'feedback_requests':
                    $this->processFeedbackRequests();
                    break;
                    
                default:
                    // Run all tasks
                    $this->processBirthdays();
                    $this->processCartAbandonment();
                    $this->processPaymentReminders();
                    $this->processReorderReminders();
                    $this->processFeedbackRequests();
                    break;
            }
            
            $this->log("Scheduler completed successfully");
            
        } catch (Exception $e) {
            $this->log("Scheduler error: " . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Process birthday wishes
     */
    private function processBirthdays() {
        $this->log("Processing birthday wishes...");
        
        try {
            // Get customers with birthdays today
            $today = date('m-d'); // MM-DD format
            
            $sql = "SELECT CustomerId, Name, MobileNo, Email, DateOfBirth 
                    FROM customer_master 
                    WHERE DATE_FORMAT(DateOfBirth, '%m-%d') = ? 
                    AND IsActive = 1 
                    AND (whatsapp_opt_in IS NULL OR whatsapp_opt_in = 1)";
            
            $customers = $this->obj->MysqliSelect(
                $sql,
                ["CustomerId", "Name", "MobileNo", "Email", "DateOfBirth"],
                "s",
                [$today]
            );
            
            if (empty($customers)) {
                $this->log("No birthdays today");
                return;
            }
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($customers as $customer) {
                // Check if birthday message already sent this year
                if ($this->isBirthdayMessageSent($customer['CustomerId'])) {
                    $this->log("Birthday message already sent for customer: " . $customer['Name']);
                    continue;
                }
                
                // Send birthday wish
                $result = sendBirthdayWish(
                    $customer['MobileNo'],
                    $customer['Name'],
                    'BIRTHDAY20' // 20% discount code
                );
                
                if ($result['success']) {
                    $successCount++;
                    $this->log("Birthday wish sent to: " . $customer['Name']);
                    
                    // Mark as sent
                    $this->markBirthdayMessageSent($customer['CustomerId']);
                } else {
                    $failureCount++;
                    $this->log("Failed to send birthday wish to: " . $customer['Name'] . " - " . $result['error'], 'ERROR');
                }
                
                // Delay between messages
                sleep(3);
            }
            
            $this->log("Birthday processing complete. Success: $successCount, Failed: $failureCount");
            
        } catch (Exception $e) {
            $this->log("Birthday processing error: " . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Process cart abandonment
     */
    private function processCartAbandonment() {
        $this->log("Processing cart abandonment...");
        
        try {
            // Get carts abandoned for more than 2 hours
            $sql = "SELECT DISTINCT c.CustomerId, cm.Name, cm.MobileNo, c.CreationDate,
                           GROUP_CONCAT(p.ProductName SEPARATOR ', ') as ProductNames
                    FROM cart c
                    JOIN customer_master cm ON c.CustomerId = cm.CustomerId
                    JOIN product_master p ON c.ProductId = p.ProductId
                    WHERE c.CreationDate < DATE_SUB(NOW(), INTERVAL 2 HOUR)
                    AND c.CreationDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    AND cm.IsActive = 1
                    AND (cm.whatsapp_opt_in IS NULL OR cm.whatsapp_opt_in = 1)
                    AND NOT EXISTS (
                        SELECT 1 FROM order_master om 
                        WHERE om.CustomerId = c.CustomerId 
                        AND om.CreationDate > c.CreationDate
                    )
                    GROUP BY c.CustomerId, cm.Name, cm.MobileNo, c.CreationDate";
            
            $abandonedCarts = $this->obj->MysqliSelect(
                $sql,
                ["CustomerId", "Name", "MobileNo", "CreationDate", "ProductNames"]
            );
            
            if (empty($abandonedCarts)) {
                $this->log("No abandoned carts found");
                return;
            }
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($abandonedCarts as $cart) {
                // Check if cart abandonment message already sent
                if ($this->isCartAbandonmentMessageSent($cart['CustomerId'], $cart['CreationDate'])) {
                    continue;
                }
                
                // Send cart abandonment reminder
                $result = sendCartAbandonmentReminder(
                    $cart['MobileNo'],
                    $cart['Name'],
                    $cart['ProductNames']
                );
                
                if ($result['success']) {
                    $successCount++;
                    $this->log("Cart abandonment reminder sent to: " . $cart['Name']);
                    
                    // Mark as sent
                    $this->markCartAbandonmentMessageSent($cart['CustomerId'], $cart['CreationDate']);
                } else {
                    $failureCount++;
                    $this->log("Failed to send cart reminder to: " . $cart['Name'] . " - " . $result['error'], 'ERROR');
                }
                
                sleep(3);
            }
            
            $this->log("Cart abandonment processing complete. Success: $successCount, Failed: $failureCount");
            
        } catch (Exception $e) {
            $this->log("Cart abandonment processing error: " . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Process payment reminders
     */
    private function processPaymentReminders() {
        $this->log("Processing payment reminders...");
        
        try {
            // Get orders with pending payments older than 2 hours
            $sql = "SELECT o.OrderId, o.CustomerId, o.TotalAmount, cm.Name, cm.MobileNo, o.CreationDate
                    FROM order_master o
                    JOIN customer_master cm ON o.CustomerId = cm.CustomerId
                    WHERE o.PaymentStatus IN ('pending', 'failed')
                    AND o.CreationDate < DATE_SUB(NOW(), INTERVAL 2 HOUR)
                    AND o.CreationDate > DATE_SUB(NOW(), INTERVAL 48 HOUR)
                    AND cm.IsActive = 1
                    AND (cm.whatsapp_opt_in IS NULL OR cm.whatsapp_opt_in = 1)";
            
            $pendingPayments = $this->obj->MysqliSelect(
                $sql,
                ["OrderId", "CustomerId", "TotalAmount", "Name", "MobileNo", "CreationDate"]
            );
            
            if (empty($pendingPayments)) {
                $this->log("No pending payments found");
                return;
            }
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($pendingPayments as $payment) {
                // Check if payment reminder already sent
                if ($this->isPaymentReminderSent($payment['OrderId'])) {
                    continue;
                }
                
                // Create payment link
                $paymentLink = "https://mynutrify.com/payment.php?order_id=" . $payment['OrderId'];
                
                // Send payment reminder
                $result = sendPaymentReminder(
                    $payment['Name'],
                    $payment['MobileNo'],
                    $payment['OrderId'],
                    $payment['TotalAmount'],
                    $paymentLink
                );
                
                if ($result['success']) {
                    $successCount++;
                    $this->log("Payment reminder sent for order: " . $payment['OrderId']);
                    
                    // Mark as sent
                    $this->markPaymentReminderSent($payment['OrderId']);
                } else {
                    $failureCount++;
                    $this->log("Failed to send payment reminder for order: " . $payment['OrderId'] . " - " . $result['error'], 'ERROR');
                }
                
                sleep(3);
            }
            
            $this->log("Payment reminder processing complete. Success: $successCount, Failed: $failureCount");
            
        } catch (Exception $e) {
            $this->log("Payment reminder processing error: " . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Process reorder reminders
     */
    private function processReorderReminders() {
        $this->log("Processing reorder reminders...");
        
        try {
            // Get customers who haven't ordered in 30 days
            $sql = "SELECT DISTINCT o.CustomerId, cm.Name, cm.MobileNo, 
                           MAX(o.CreationDate) as LastOrderDate,
                           GROUP_CONCAT(DISTINCT p.ProductName SEPARATOR ', ') as LastProducts
                    FROM order_master o
                    JOIN customer_master cm ON o.CustomerId = cm.CustomerId
                    JOIN order_details od ON o.OrderId = od.OrderId
                    JOIN product_master p ON od.ProductId = p.ProductId
                    WHERE o.PaymentStatus = 'completed'
                    AND cm.IsActive = 1
                    AND (cm.whatsapp_opt_in IS NULL OR cm.whatsapp_opt_in = 1)
                    GROUP BY o.CustomerId, cm.Name, cm.MobileNo
                    HAVING MAX(o.CreationDate) < DATE_SUB(NOW(), INTERVAL 30 DAY)
                    AND MAX(o.CreationDate) > DATE_SUB(NOW(), INTERVAL 60 DAY)";
            
            $reorderCandidates = $this->obj->MysqliSelect(
                $sql,
                ["CustomerId", "Name", "MobileNo", "LastOrderDate", "LastProducts"]
            );
            
            if (empty($reorderCandidates)) {
                $this->log("No reorder candidates found");
                return;
            }
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($reorderCandidates as $customer) {
                // Check if reorder reminder already sent
                if ($this->isReorderReminderSent($customer['CustomerId'])) {
                    continue;
                }
                
                // Calculate days since last order
                $daysSince = floor((time() - strtotime($customer['LastOrderDate'])) / 86400);
                
                // Send reorder reminder
                $result = sendReorderReminder(
                    $customer['MobileNo'],
                    $customer['Name'],
                    $customer['LastProducts'],
                    $daysSince . ' days ago'
                );
                
                if ($result['success']) {
                    $successCount++;
                    $this->log("Reorder reminder sent to: " . $customer['Name']);
                    
                    // Mark as sent
                    $this->markReorderReminderSent($customer['CustomerId']);
                } else {
                    $failureCount++;
                    $this->log("Failed to send reorder reminder to: " . $customer['Name'] . " - " . $result['error'], 'ERROR');
                }
                
                sleep(3);
            }
            
            $this->log("Reorder reminder processing complete. Success: $successCount, Failed: $failureCount");
            
        } catch (Exception $e) {
            $this->log("Reorder reminder processing error: " . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Process feedback requests
     */
    private function processFeedbackRequests() {
        $this->log("Processing feedback requests...");
        
        try {
            // Get delivered orders from 3 days ago (enough time to try products)
            $sql = "SELECT o.OrderId, o.CustomerId, cm.Name, cm.MobileNo,
                           GROUP_CONCAT(DISTINCT p.ProductName SEPARATOR ', ') as ProductNames
                    FROM order_master o
                    JOIN customer_master cm ON o.CustomerId = cm.CustomerId
                    JOIN order_details od ON o.OrderId = od.OrderId
                    JOIN product_master p ON od.ProductId = p.ProductId
                    WHERE o.OrderStatus = 'delivered'
                    AND o.DeliveryDate = DATE_SUB(CURDATE(), INTERVAL 3 DAY)
                    AND cm.IsActive = 1
                    AND (cm.whatsapp_opt_in IS NULL OR cm.whatsapp_opt_in = 1)
                    GROUP BY o.OrderId, o.CustomerId, cm.Name, cm.MobileNo";
            
            $feedbackCandidates = $this->obj->MysqliSelect(
                $sql,
                ["OrderId", "CustomerId", "Name", "MobileNo", "ProductNames"]
            );
            
            if (empty($feedbackCandidates)) {
                $this->log("No feedback candidates found");
                return;
            }
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($feedbackCandidates as $order) {
                // Check if feedback request already sent
                if ($this->isFeedbackRequestSent($order['OrderId'])) {
                    continue;
                }
                
                // Create review link
                $reviewLink = "https://mynutrify.com/review.php?order_id=" . $order['OrderId'];
                
                // Send feedback request
                $result = sendFeedbackRequest(
                    $order['MobileNo'],
                    $order['Name'],
                    $order['OrderId'],
                    $order['ProductNames'],
                    $reviewLink
                );
                
                if ($result['success']) {
                    $successCount++;
                    $this->log("Feedback request sent for order: " . $order['OrderId']);
                    
                    // Mark as sent
                    $this->markFeedbackRequestSent($order['OrderId']);
                } else {
                    $failureCount++;
                    $this->log("Failed to send feedback request for order: " . $order['OrderId'] . " - " . $result['error'], 'ERROR');
                }
                
                sleep(3);
            }
            
            $this->log("Feedback request processing complete. Success: $successCount, Failed: $failureCount");
            
        } catch (Exception $e) {
            $this->log("Feedback request processing error: " . $e->getMessage(), 'ERROR');
        }
    }
    
    // Helper methods for tracking sent messages
    private function isBirthdayMessageSent($customerId) {
        $year = date('Y');
        return $this->isMessageSent($customerId, 'birthday_wish', $year . '-01-01', $year . '-12-31');
    }
    
    private function isCartAbandonmentMessageSent($customerId, $cartDate) {
        $date = date('Y-m-d', strtotime($cartDate));
        return $this->isMessageSent($customerId, 'cart_abandonment', $date, $date);
    }
    
    private function isPaymentReminderSent($orderId) {
        return $this->isMessageSentForOrder($orderId, 'payment_reminder');
    }
    
    private function isReorderReminderSent($customerId) {
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        return $this->isMessageSent($customerId, 'reorder_reminder', $thirtyDaysAgo);
    }
    
    private function isFeedbackRequestSent($orderId) {
        return $this->isMessageSentForOrder($orderId, 'feedback_request');
    }
    
    private function isMessageSent($customerId, $messageType, $startDate, $endDate = null) {
        $endDate = $endDate ?? date('Y-m-d');
        
        $sql = "SELECT COUNT(*) as count FROM whatsapp_activity_log 
                WHERE customer_id = ? AND message_type = ? 
                AND DATE(created_at) BETWEEN ? AND ?";
        
        $result = $this->obj->MysqliSelect1(
            $sql,
            ["count"],
            "isss",
            [$customerId, $messageType, $startDate, $endDate]
        );
        
        return ($result[0]["count"] > 0);
    }
    
    private function isMessageSentForOrder($orderId, $messageType) {
        $sql = "SELECT COUNT(*) as count FROM whatsapp_activity_log 
                WHERE order_id = ? AND message_type = ?";
        
        $result = $this->obj->MysqliSelect1(
            $sql,
            ["count"],
            "ss",
            [$orderId, $messageType]
        );
        
        return ($result[0]["count"] > 0);
    }
    
    // Methods to mark messages as sent
    private function markBirthdayMessageSent($customerId) {
        $this->markMessageSent($customerId, null, 'birthday_wish');
    }
    
    private function markCartAbandonmentMessageSent($customerId, $cartDate) {
        $this->markMessageSent($customerId, null, 'cart_abandonment');
    }
    
    private function markPaymentReminderSent($orderId) {
        $this->markMessageSent(null, $orderId, 'payment_reminder');
    }
    
    private function markReorderReminderSent($customerId) {
        $this->markMessageSent($customerId, null, 'reorder_reminder');
    }
    
    private function markFeedbackRequestSent($orderId) {
        $this->markMessageSent(null, $orderId, 'feedback_request');
    }
    
    private function markMessageSent($customerId, $orderId, $messageType) {
        try {
            $this->obj->fInsertNew(
                "INSERT INTO whatsapp_activity_log (customer_id, order_id, message_type, success, response, created_at) VALUES (?, ?, ?, 1, 'Scheduled message sent', NOW())",
                "iss",
                [$customerId, $orderId, $messageType]
            );
        } catch (Exception $e) {
            $this->log("Failed to mark message as sent: " . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Log function
     */
    private function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Also output to console if running from command line
        if (php_sapi_name() === 'cli') {
            echo $logEntry;
        }
    }
}

// Command line execution
if (php_sapi_name() === 'cli') {
    $taskType = $argv[1] ?? null;
    $scheduler = new WhatsAppScheduler();
    $scheduler->run($taskType);
} elseif (isset($_GET['task'])) {
    // Web execution (for testing)
    $taskType = $_GET['task'];
    $scheduler = new WhatsAppScheduler();
    $scheduler->run($taskType);
    echo "Scheduler task '$taskType' completed. Check logs for details.";
}
?>

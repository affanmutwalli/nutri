<?php
/**
 * Order Integration Example
 * Shows how to integrate WhatsApp with your existing order system
 */

// Include your existing connection
require_once '../exe_files/connection.php';
require_once '../whatsapp_api/order_status_update.php';

class OrderWhatsAppIntegration {
    private $obj;
    
    public function __construct() {
        $this->obj = new Connection();
    }
    
    /**
     * Send WhatsApp when order is placed
     * Call this after order creation in rcus_place_order_online.php
     */
    public function sendOrderConfirmation($orderId, $customerId) {
        try {
            // Get customer details from database
            $customer = $this->getCustomerDetails($customerId);
            if (!$customer) {
                throw new Exception("Customer not found");
            }
            
            // Get order details
            $order = $this->getOrderDetails($orderId);
            if (!$order) {
                throw new Exception("Order not found");
            }
            
            // Check if customer wants WhatsApp notifications
            if (!$this->isWhatsAppEnabled($customerId)) {
                return ['success' => false, 'reason' => 'Customer opted out'];
            }
            
            // Send WhatsApp message
            $result = sendOrderStatusUpdate(
                $orderId,
                $customer['Name'],
                $customer['MobileNo'],
                'placed', // You can create this template
                null
            );
            
            // Log the activity
            $this->logWhatsAppActivity($customerId, $orderId, 'order_confirmation', $result);
            
            return $result;
            
        } catch (Exception $e) {
            error_log("WhatsApp order confirmation failed: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send WhatsApp when payment status changes
     */
    public function sendPaymentUpdate($orderId, $paymentStatus) {
        try {
            $order = $this->getOrderDetails($orderId);
            $customer = $this->getCustomerDetails($order['CustomerId']);
            
            if (!$this->isWhatsAppEnabled($order['CustomerId'])) {
                return ['success' => false, 'reason' => 'Customer opted out'];
            }
            
            switch ($paymentStatus) {
                case 'success':
                case 'completed':
                    // Payment successful - send confirmation
                    $result = sendOrderStatusUpdate(
                        $orderId,
                        $customer['Name'],
                        $customer['MobileNo'],
                        'confirmed',
                        null
                    );
                    break;
                    
                case 'failed':
                case 'pending':
                    // Payment failed - send reminder
                    require_once '../whatsapp_api/payment_reminders.php';
                    $paymentLink = "https://mynutrify.com/payment.php?order_id=" . $orderId;
                    
                    $result = sendPaymentReminder(
                        $customer['Name'],
                        $customer['MobileNo'],
                        $orderId,
                        $order['TotalAmount'],
                        $paymentLink
                    );
                    break;
                    
                default:
                    return ['success' => false, 'reason' => 'Unknown payment status'];
            }
            
            $this->logWhatsAppActivity($order['CustomerId'], $orderId, 'payment_update', $result);
            return $result;
            
        } catch (Exception $e) {
            error_log("WhatsApp payment update failed: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send WhatsApp when order status changes (shipping updates)
     */
    public function sendShippingUpdate($orderId, $newStatus, $trackingNumber = null) {
        try {
            $order = $this->getOrderDetails($orderId);
            $customer = $this->getCustomerDetails($order['CustomerId']);
            
            if (!$this->isWhatsAppEnabled($order['CustomerId'])) {
                return ['success' => false, 'reason' => 'Customer opted out'];
            }
            
            // Map your order statuses to WhatsApp templates
            $statusMap = [
                'shipped' => 'shipped',
                'dispatched' => 'shipped',
                'out_for_delivery' => 'out_for_delivery',
                'delivered' => 'delivered',
                'cancelled' => 'cancelled'
            ];
            
            $whatsappStatus = $statusMap[$newStatus] ?? null;
            if (!$whatsappStatus) {
                return ['success' => false, 'reason' => 'Status not mapped for WhatsApp'];
            }
            
            $result = sendOrderStatusUpdate(
                $orderId,
                $customer['Name'],
                $customer['MobileNo'],
                $whatsappStatus,
                $trackingNumber
            );
            
            $this->logWhatsAppActivity($order['CustomerId'], $orderId, 'shipping_update', $result);
            return $result;
            
        } catch (Exception $e) {
            error_log("WhatsApp shipping update failed: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get customer details from database
     */
    private function getCustomerDetails($customerId) {
        $result = $this->obj->MysqliSelect1(
            "SELECT CustomerId, Name, Email, MobileNo, whatsapp_opt_in FROM customer_master WHERE CustomerId = ? AND IsActive = 1",
            ["CustomerId", "Name", "Email", "MobileNo", "whatsapp_opt_in"],
            "i",
            [$customerId]
        );
        
        return $result[0] ?? null;
    }
    
    /**
     * Get order details from database
     */
    private function getOrderDetails($orderId) {
        $result = $this->obj->MysqliSelect1(
            "SELECT OrderId, CustomerId, TotalAmount, PaymentStatus, OrderStatus, CreationDate FROM order_master WHERE OrderId = ?",
            ["OrderId", "CustomerId", "TotalAmount", "PaymentStatus", "OrderStatus", "CreationDate"],
            "s",
            [$orderId]
        );
        
        return $result[0] ?? null;
    }
    
    /**
     * Check if customer has WhatsApp enabled
     */
    private function isWhatsAppEnabled($customerId) {
        $customer = $this->getCustomerDetails($customerId);
        
        // Default to enabled if column doesn't exist
        return ($customer['whatsapp_opt_in'] ?? 1) == 1;
    }
    
    /**
     * Log WhatsApp activity
     */
    private function logWhatsAppActivity($customerId, $orderId, $messageType, $result) {
        try {
            // Create activity log table if it doesn't exist
            $this->createActivityLogTable();
            
            $this->obj->fInsertNew(
                "INSERT INTO whatsapp_activity_log (customer_id, order_id, message_type, success, response, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
                "issss",
                [
                    $customerId,
                    $orderId,
                    $messageType,
                    $result['success'] ? 1 : 0,
                    json_encode($result)
                ]
            );
        } catch (Exception $e) {
            error_log("Failed to log WhatsApp activity: " . $e->getMessage());
        }
    }
    
    /**
     * Create activity log table
     */
    private function createActivityLogTable() {
        $sql = "CREATE TABLE IF NOT EXISTS whatsapp_activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT,
            order_id VARCHAR(50),
            message_type VARCHAR(50),
            success TINYINT(1),
            response TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_customer (customer_id),
            INDEX idx_order (order_id),
            INDEX idx_type (message_type)
        )";
        
        $this->obj->MysqliQuery($sql);
    }
}

// Usage Examples:

/**
 * Example 1: Add to your order creation code (rcus_place_order_online.php)
 */
function integrateWithOrderCreation() {
    // After successful order creation (around line 150 in your file)
    /*
    if ($orderId) {
        $whatsapp = new OrderWhatsAppIntegration();
        $whatsappResult = $whatsapp->sendOrderConfirmation($orderId, $data['CustomerId']);
        
        if ($whatsappResult['success']) {
            error_log("WhatsApp order confirmation sent for order: $orderId");
        }
    }
    */
}

/**
 * Example 2: Add to payment callback/webhook
 */
function integrateWithPaymentCallback() {
    // In your payment processing code
    /*
    $whatsapp = new OrderWhatsAppIntegration();
    
    if ($paymentStatus == 'success') {
        $whatsapp->sendPaymentUpdate($orderId, 'success');
    } elseif ($paymentStatus == 'failed') {
        $whatsapp->sendPaymentUpdate($orderId, 'failed');
    }
    */
}

/**
 * Example 3: Add to order status update (admin panel)
 */
function integrateWithStatusUpdate() {
    // When admin updates order status
    /*
    if ($newStatus == 'shipped') {
        $whatsapp = new OrderWhatsAppIntegration();
        $whatsapp->sendShippingUpdate($orderId, 'shipped', $trackingNumber);
    }
    */
}

/**
 * Example 4: Bulk status update for multiple orders
 */
function bulkStatusUpdate($orders) {
    $whatsapp = new OrderWhatsAppIntegration();
    $results = [];
    
    foreach ($orders as $order) {
        $result = $whatsapp->sendShippingUpdate(
            $order['OrderId'],
            $order['NewStatus'],
            $order['TrackingNumber'] ?? null
        );
        
        $results[] = [
            'order_id' => $order['OrderId'],
            'success' => $result['success'],
            'message' => $result['success'] ? 'Sent' : ($result['error'] ?? $result['reason'])
        ];
        
        // Add delay to avoid rate limiting
        sleep(2);
    }
    
    return $results;
}

// Test function
if (isset($_GET['test'])) {
    $whatsapp = new OrderWhatsAppIntegration();
    
    // Test with a real order ID from your database
    $testOrderId = "MN000001"; // Replace with actual order ID
    $testCustomerId = 1; // Replace with actual customer ID
    
    echo "<h1>Testing WhatsApp Integration</h1>";
    
    // Test order confirmation
    echo "<h3>Testing Order Confirmation:</h3>";
    $result = $whatsapp->sendOrderConfirmation($testOrderId, $testCustomerId);
    echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
    
    // Test shipping update
    echo "<h3>Testing Shipping Update:</h3>";
    $result = $whatsapp->sendShippingUpdate($testOrderId, 'shipped', 'DL123456789');
    echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
}
?>

<?php
/**
 * Email Notification System as backup for SMS
 * Free alternative that works immediately without API keys
 */

require_once __DIR__ . '/../database/dbconnection.php';

class EmailNotification {
    private $obj;
    private $adminEmail;
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        $this->obj = new main();
        $this->obj->connection();
        
        // Admin email for notifications (Mailpit will catch all emails)
        $this->adminEmail = "admin@mynutrify.com"; // Your admin email
        $this->fromEmail = "noreply@mynutrify.com"; // From email
        $this->fromName = "MyNutrify Admin";
    }
    
    /**
     * Send email notification
     */
    public function sendEmail($to, $subject, $message, $isHtml = true) {
        try {
            // Email headers
            $headers = [];
            $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
            $headers[] = "Reply-To: {$this->fromEmail}";
            $headers[] = "X-Mailer: PHP/" . phpversion();
            
            if ($isHtml) {
                $headers[] = "MIME-Version: 1.0";
                $headers[] = "Content-Type: text/html; charset=UTF-8";
            } else {
                $headers[] = "Content-Type: text/plain; charset=UTF-8";
            }
            
            // Send email
            $success = mail($to, $subject, $message, implode("\r\n", $headers));
            
            if ($success) {
                $this->logToFile("SUCCESS: Email sent to $to - Subject: $subject");
                return [
                    'success' => true,
                    'to' => $to,
                    'subject' => $subject
                ];
            } else {
                $this->logToFile("ERROR: Email failed for $to - Subject: $subject");
                return [
                    'success' => false,
                    'error' => 'Failed to send email'
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
            
            $subject = "🛒 NEW ORDER ALERT - Order #{$orderData['OrderId']}";
            
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <h2 style='color: #28a745;'>🛒 New Order Received!</h2>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                    <h3>Order Details:</h3>
                    <p><strong>Order ID:</strong> {$orderData['OrderId']}</p>
                    <p><strong>Customer:</strong> {$orderData['CustomerName']}</p>
                    <p><strong>Phone:</strong> {$orderData['CustomerPhone']}</p>
                    <p><strong>Amount:</strong> ₹{$orderData['Amount']}</p>
                    <p><strong>Time:</strong> " . date('d-M-Y H:i:s') . "</p>
                </div>
                <p>Please process this order in the admin panel.</p>
                <p><a href='https://mynutrify.com/oms/' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View in OMS</a></p>
            </body>
            </html>";
            
            return $this->sendEmail($this->adminEmail, $subject, $message, true);
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send order shipped notification to admin
     */
    public function sendAdminOrderShippedNotification($orderId, $trackingNumber = null) {
        try {
            // Get order details
            $orderData = $this->getOrderDetails($orderId);
            
            if (empty($orderData)) {
                throw new Exception("Order not found: $orderId");
            }
            
            $subject = "📦 ORDER SHIPPED - Order #{$orderData['OrderId']}";
            
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <h2 style='color: #17a2b8;'>📦 Order Shipped Successfully!</h2>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                    <h3>Shipping Details:</h3>
                    <p><strong>Order ID:</strong> {$orderData['OrderId']}</p>
                    <p><strong>Customer:</strong> {$orderData['CustomerName']}</p>
                    <p><strong>Phone:</strong> {$orderData['CustomerPhone']}</p>
                    <p><strong>Amount:</strong> ₹{$orderData['Amount']}</p>";
            
            if ($trackingNumber) {
                $message .= "<p><strong>Tracking Number:</strong> $trackingNumber</p>";
            }
            
            $message .= "
                    <p><strong>Shipped Time:</strong> " . date('d-M-Y H:i:s') . "</p>
                </div>
                <p>The customer will receive WhatsApp notification about shipping.</p>
            </body>
            </html>";
            
            return $this->sendEmail($this->adminEmail, $subject, $message, true);
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send custom email notification
     */
    public function sendCustomEmail($to, $subject, $message) {
        return $this->sendEmail($to, $subject, $message, true);
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
     * Log messages to file
     */
    private function logToFile($message) {
        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/email_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
    }
}

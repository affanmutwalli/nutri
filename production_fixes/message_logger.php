<?php
/**
 * Message Logger for Production
 * Comprehensive logging and tracking system
 */

require_once 'secure_config.php';

class MessageLogger {
    private static $logDir = __DIR__ . '/../logs/';
    private static $dbConnection = null;
    
    public static function init() {
        // Create logs directory if it doesn't exist
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        
        // Initialize database connection if available
        self::initDatabase();
    }
    
    private static function initDatabase() {
        try {
            // Try to connect to existing database
            require_once '../exe_files/connection.php';
            $obj = new Connection();
            self::$dbConnection = $obj;
            
            // Create message log table if it doesn't exist
            self::createMessageLogTable();
        } catch (Exception $e) {
            // Database not available, use file logging only
            self::logToFile('system', 'WARNING', 'Database not available, using file logging only: ' . $e->getMessage());
        }
    }
    
    private static function createMessageLogTable() {
        if (!self::$dbConnection) return;
        
        $sql = "CREATE TABLE IF NOT EXISTS whatsapp_message_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message_id VARCHAR(255),
            customer_id INT,
            phone_number VARCHAR(20),
            template_name VARCHAR(100),
            message_type VARCHAR(50),
            status VARCHAR(20),
            api_response TEXT,
            error_message TEXT,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            delivered_at TIMESTAMP NULL,
            read_at TIMESTAMP NULL,
            failed_at TIMESTAMP NULL,
            retry_count INT DEFAULT 0,
            INDEX idx_phone (phone_number),
            INDEX idx_template (template_name),
            INDEX idx_status (status),
            INDEX idx_sent_at (sent_at)
        )";
        
        try {
            self::$dbConnection->MysqliQuery($sql);
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Failed to create message log table: ' . $e->getMessage());
        }
    }
    
    public static function logMessage($data) {
        self::init();
        
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message_id' => $data['message_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'template_name' => $data['template_name'] ?? null,
            'message_type' => $data['message_type'] ?? 'unknown',
            'status' => $data['status'] ?? 'pending',
            'api_response' => $data['api_response'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'payload' => $data['payload'] ?? null
        ];
        
        // Log to database
        self::logToDatabase($logData);
        
        // Log to file
        self::logToFile('messages', $logData['status'], json_encode($logData));
        
        return $logData;
    }
    
    private static function logToDatabase($data) {
        if (!self::$dbConnection) return;
        
        try {
            $sql = "INSERT INTO whatsapp_message_log 
                    (message_id, customer_id, phone_number, template_name, message_type, status, api_response, error_message) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            self::$dbConnection->fInsertNew(
                $sql,
                "sissssss",
                [
                    $data['message_id'],
                    $data['customer_id'],
                    $data['phone_number'],
                    $data['template_name'],
                    $data['message_type'],
                    $data['status'],
                    $data['api_response'],
                    $data['error_message']
                ]
            );
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Database logging failed: ' . $e->getMessage());
        }
    }
    
    public static function logToFile($category, $level, $message) {
        $logFile = self::$logDir . $category . '_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function updateMessageStatus($messageId, $status, $additionalData = []) {
        if (!self::$dbConnection) return;
        
        try {
            $updateFields = ["status = ?"];
            $params = [$status];
            $types = "s";
            
            // Add timestamp based on status
            switch ($status) {
                case 'delivered':
                    $updateFields[] = "delivered_at = NOW()";
                    break;
                case 'read':
                    $updateFields[] = "read_at = NOW()";
                    break;
                case 'failed':
                    $updateFields[] = "failed_at = NOW()";
                    if (isset($additionalData['error_message'])) {
                        $updateFields[] = "error_message = ?";
                        $params[] = $additionalData['error_message'];
                        $types .= "s";
                    }
                    break;
            }
            
            $sql = "UPDATE whatsapp_message_log SET " . implode(", ", $updateFields) . " WHERE message_id = ?";
            $params[] = $messageId;
            $types .= "s";
            
            self::$dbConnection->MysqliUpdate($sql, $types, $params);
            
            self::logToFile('messages', 'INFO', "Message $messageId status updated to $status");
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Failed to update message status: ' . $e->getMessage());
        }
    }
    
    public static function getMessageStats($days = 7) {
        if (!self::$dbConnection) return null;
        
        try {
            $sql = "SELECT 
                        COUNT(*) as total_messages,
                        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_count,
                        SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
                        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                        template_name,
                        DATE(sent_at) as date
                    FROM whatsapp_message_log 
                    WHERE sent_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY template_name, DATE(sent_at)
                    ORDER BY sent_at DESC";
            
            return self::$dbConnection->MysqliSelect(
                $sql,
                ["total_messages", "sent_count", "delivered_count", "read_count", "failed_count", "template_name", "date"],
                "i",
                [$days]
            );
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Failed to get message stats: ' . $e->getMessage());
            return null;
        }
    }
    
    public static function getFailedMessages($limit = 50) {
        if (!self::$dbConnection) return null;
        
        try {
            $sql = "SELECT * FROM whatsapp_message_log 
                    WHERE status = 'failed' AND retry_count < 3
                    ORDER BY sent_at DESC 
                    LIMIT ?";
            
            return self::$dbConnection->MysqliSelect(
                $sql,
                ["id", "message_id", "phone_number", "template_name", "error_message", "retry_count", "sent_at"],
                "i",
                [$limit]
            );
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Failed to get failed messages: ' . $e->getMessage());
            return null;
        }
    }
    
    public static function incrementRetryCount($messageId) {
        if (!self::$dbConnection) return;
        
        try {
            $sql = "UPDATE whatsapp_message_log SET retry_count = retry_count + 1 WHERE message_id = ?";
            self::$dbConnection->MysqliUpdate($sql, "s", [$messageId]);
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Failed to increment retry count: ' . $e->getMessage());
        }
    }
    
    public static function checkDuplicateMessage($phoneNumber, $templateName, $minutes = 60) {
        if (!self::$dbConnection) return false;
        
        try {
            $sql = "SELECT COUNT(*) as count FROM whatsapp_message_log 
                    WHERE phone_number = ? AND template_name = ? 
                    AND sent_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)
                    AND status != 'failed'";
            
            $result = self::$dbConnection->MysqliSelect1(
                $sql,
                ["count"],
                "ssi",
                [$phoneNumber, $templateName, $minutes]
            );
            
            return ($result[0]["count"] > 0);
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Failed to check duplicate message: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function getCustomerOptOutStatus($phoneNumber) {
        if (!self::$dbConnection) return false;
        
        try {
            // Check if customer has opted out
            $sql = "SELECT opt_out FROM customer_master WHERE MobileNo = ?";
            $result = self::$dbConnection->MysqliSelect1(
                $sql,
                ["opt_out"],
                "s",
                [$phoneNumber]
            );
            
            return isset($result[0]["opt_out"]) ? (bool)$result[0]["opt_out"] : false;
        } catch (Exception $e) {
            self::logToFile('system', 'ERROR', 'Failed to check opt-out status: ' . $e->getMessage());
            return false;
        }
    }
}

// Error handler for production
class ProductionErrorHandler {
    public static function handleError($errno, $errstr, $errfile, $errline) {
        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_NOTICE => 'NOTICE',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE'
        ];
        
        $level = $errorTypes[$errno] ?? 'UNKNOWN';
        $message = "PHP $level: $errstr in $errfile on line $errline";
        
        MessageLogger::logToFile('errors', $level, $message);
        
        // Don't show errors to users in production
        if (SecureConfig::isProduction()) {
            return true;
        }
        
        return false;
    }
    
    public static function handleException($exception) {
        $message = "Uncaught exception: " . $exception->getMessage() . 
                  " in " . $exception->getFile() . 
                  " on line " . $exception->getLine();
        
        MessageLogger::logToFile('errors', 'EXCEPTION', $message);
        MessageLogger::logToFile('errors', 'TRACE', $exception->getTraceAsString());
        
        if (SecureConfig::isProduction()) {
            // Show generic error message
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        } else {
            // Show detailed error in development
            throw $exception;
        }
    }
}

// Set error handlers
set_error_handler(['ProductionErrorHandler', 'handleError']);
set_exception_handler(['ProductionErrorHandler', 'handleException']);
?>

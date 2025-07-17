<?php
/**
 * Secure Configuration for Production
 * Move sensitive data to environment variables
 */

class SecureConfig {
    private static $config = null;
    
    public static function init() {
        if (self::$config === null) {
            // Load from environment variables or config file
            self::$config = [
                'interakt_api_key' => self::getEnvVar('INTERAKT_API_KEY', 'SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo='),
                'interakt_api_url' => self::getEnvVar('INTERAKT_API_URL', 'https://api.interakt.ai/v1/public/message/'),
                'default_country_code' => self::getEnvVar('DEFAULT_COUNTRY_CODE', '+91'),
                'max_messages_per_hour' => (int)self::getEnvVar('MAX_MESSAGES_PER_HOUR', '100'),
                'max_messages_per_day' => (int)self::getEnvVar('MAX_MESSAGES_PER_DAY', '1000'),
                'business_hours_start' => self::getEnvVar('BUSINESS_HOURS_START', '09:00'),
                'business_hours_end' => self::getEnvVar('BUSINESS_HOURS_END', '21:00'),
                'log_level' => self::getEnvVar('LOG_LEVEL', 'INFO'),
                'environment' => self::getEnvVar('ENVIRONMENT', 'development')
            ];
        }
        return self::$config;
    }
    
    private static function getEnvVar($key, $default = null) {
        // Try $_ENV first, then getenv(), then default
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
    
    public static function get($key) {
        $config = self::init();
        return $config[$key] ?? null;
    }
    
    public static function getInteraktApiKey() {
        return self::get('interakt_api_key');
    }
    
    public static function getInteraktApiUrl() {
        return self::get('interakt_api_url');
    }
    
    public static function getDefaultCountryCode() {
        return self::get('default_country_code');
    }
    
    public static function isProduction() {
        return self::get('environment') === 'production';
    }
    
    public static function isDevelopment() {
        return self::get('environment') === 'development';
    }
}

// Rate limiting class
class RateLimiter {
    private static $logFile = __DIR__ . '/../logs/rate_limit.log';
    
    public static function checkLimit($identifier, $maxPerHour = null, $maxPerDay = null) {
        $maxPerHour = $maxPerHour ?? SecureConfig::get('max_messages_per_hour');
        $maxPerDay = $maxPerDay ?? SecureConfig::get('max_messages_per_day');
        
        $now = time();
        $hourAgo = $now - 3600;
        $dayAgo = $now - 86400;
        
        // Get recent requests
        $recentRequests = self::getRecentRequests($identifier, $dayAgo);
        
        // Count requests in last hour and day
        $hourlyCount = 0;
        $dailyCount = 0;
        
        foreach ($recentRequests as $timestamp) {
            if ($timestamp > $hourAgo) {
                $hourlyCount++;
            }
            if ($timestamp > $dayAgo) {
                $dailyCount++;
            }
        }
        
        // Check limits
        if ($hourlyCount >= $maxPerHour) {
            return [
                'allowed' => false,
                'reason' => 'Hourly limit exceeded',
                'limit' => $maxPerHour,
                'current' => $hourlyCount,
                'reset_time' => $hourAgo + 3600
            ];
        }
        
        if ($dailyCount >= $maxPerDay) {
            return [
                'allowed' => false,
                'reason' => 'Daily limit exceeded',
                'limit' => $maxPerDay,
                'current' => $dailyCount,
                'reset_time' => $dayAgo + 86400
            ];
        }
        
        // Log this request
        self::logRequest($identifier, $now);
        
        return [
            'allowed' => true,
            'hourly_remaining' => $maxPerHour - $hourlyCount - 1,
            'daily_remaining' => $maxPerDay - $dailyCount - 1
        ];
    }
    
    private static function getRecentRequests($identifier, $since) {
        if (!file_exists(self::$logFile)) {
            return [];
        }
        
        $requests = [];
        $lines = file(self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 2 && $parts[1] === $identifier) {
                $timestamp = (int)$parts[0];
                if ($timestamp > $since) {
                    $requests[] = $timestamp;
                }
            }
        }
        
        return $requests;
    }
    
    private static function logRequest($identifier, $timestamp) {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = $timestamp . '|' . $identifier . '|' . date('Y-m-d H:i:s', $timestamp) . PHP_EOL;
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Input validation class
class InputValidator {
    public static function validatePhoneNumber($phone) {
        // Remove all non-digits
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid Indian mobile number
        if (preg_match('/^[6-9]\d{9}$/', $phone)) {
            return $phone;
        }
        
        throw new InvalidArgumentException('Invalid phone number format');
    }
    
    public static function validateCustomerName($name) {
        $name = trim($name);
        
        if (empty($name)) {
            throw new InvalidArgumentException('Customer name cannot be empty');
        }
        
        if (strlen($name) > 100) {
            throw new InvalidArgumentException('Customer name too long');
        }
        
        // Remove potentially harmful characters
        $name = preg_replace('/[<>"\']/', '', $name);
        
        return $name;
    }
    
    public static function validateOrderId($orderId) {
        $orderId = trim($orderId);
        
        if (empty($orderId)) {
            throw new InvalidArgumentException('Order ID cannot be empty');
        }
        
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $orderId)) {
            throw new InvalidArgumentException('Invalid order ID format');
        }
        
        return $orderId;
    }
    
    public static function validateAmount($amount) {
        if (!is_numeric($amount) || $amount < 0) {
            throw new InvalidArgumentException('Invalid amount');
        }
        
        return (float)$amount;
    }
    
    public static function validateUrl($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }
        
        return $url;
    }
}

// Business hours checker
class BusinessHours {
    public static function isBusinessHours($timezone = 'Asia/Kolkata') {
        $start = SecureConfig::get('business_hours_start');
        $end = SecureConfig::get('business_hours_end');
        
        $currentTime = new DateTime('now', new DateTimeZone($timezone));
        $currentHour = $currentTime->format('H:i');
        
        return ($currentHour >= $start && $currentHour <= $end);
    }
    
    public static function getNextBusinessHour($timezone = 'Asia/Kolkata') {
        $start = SecureConfig::get('business_hours_start');
        $currentTime = new DateTime('now', new DateTimeZone($timezone));
        
        // If it's past business hours today, next business hour is tomorrow
        if ($currentTime->format('H:i') > SecureConfig::get('business_hours_end')) {
            $currentTime->modify('+1 day');
        }
        
        $currentTime->setTime(
            (int)substr($start, 0, 2),
            (int)substr($start, 3, 2)
        );
        
        return $currentTime;
    }
}
?>

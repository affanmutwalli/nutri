<?php
/**
 * Analytics Tracker Class
 * Cookie-based visitor tracking and analytics system
 */

class AnalyticsTracker {
    private $mysqli;
    private $visitorId;
    private $customerId;
    private $sessionId;
    private $ipAddress;
    private $userAgent;
    
    public function __construct($mysqli = null) {
        if ($mysqli) {
            $this->mysqli = $mysqli;
        } else {
            // Use existing database connection
            include_once __DIR__ . '/../database/dbconnection.php';
            if (class_exists('main')) {
                $obj = new main();
                $this->mysqli = $obj->connection();
            } else {
                throw new Exception("Database connection class not available");
            }
        }

        $this->initializeTracking();
    }
    
    /**
     * Initialize visitor tracking with cookie-based identification
     */
    private function initializeTracking() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get visitor ID from cookie or create new one
        $this->visitorId = $this->getOrCreateVisitorId();
        
        // Get customer ID if logged in
        $this->customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;
        
        // Get session ID
        $this->sessionId = session_id();
        
        // Get IP address
        $this->ipAddress = $this->getRealIpAddress();
        
        // Get user agent
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Update or create visitor record
        $this->updateVisitorRecord();
    }
    
    /**
     * Get or create unique visitor ID using cookies
     */
    private function getOrCreateVisitorId() {
        $cookieName = 'nutrify_visitor_id';
        $cookieExpiry = time() + (365 * 24 * 60 * 60); // 1 year
        
        // Check if visitor ID cookie exists
        if (isset($_COOKIE[$cookieName]) && !empty($_COOKIE[$cookieName])) {
            return $_COOKIE[$cookieName];
        }
        
        // Generate new unique visitor ID
        $visitorId = $this->generateUniqueVisitorId();
        
        // Set cookie with secure options
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        setcookie($cookieName, $visitorId, $cookieExpiry, '/', '', $secure, true);
        
        return $visitorId;
    }
    
    /**
     * Generate unique visitor ID
     */
    private function generateUniqueVisitorId() {
        // Create unique ID based on multiple factors
        $factors = [
            $this->getRealIpAddress(),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            microtime(true),
            random_bytes(16)
        ];
        
        $uniqueString = implode('|', $factors);
        return hash('sha256', $uniqueString);
    }
    
    /**
     * Get real IP address considering proxies
     */
    private function getRealIpAddress() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Update or create visitor record
     */
    private function updateVisitorRecord() {
        try {
            // Parse user agent for device info
            $deviceInfo = $this->parseUserAgent($this->userAgent);
            
            // Get referrer information
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;
            $utmParams = $this->extractUtmParameters();
            
            $stmt = $this->mysqli->prepare("
                INSERT INTO visitor_analytics (
                    visitor_id, customer_id, session_id, ip_address, user_agent,
                    device_type, browser, operating_system, referrer_url,
                    utm_source, utm_medium, utm_campaign, total_visits, first_visit, last_visit
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    customer_id = VALUES(customer_id),
                    session_id = VALUES(session_id),
                    last_visit = NOW(),
                    total_visits = total_visits + 1,
                    ip_address = VALUES(ip_address),
                    user_agent = VALUES(user_agent)
            ");
            
            $stmt->bind_param("sissssssssss",
                $this->visitorId, $this->customerId, $this->sessionId, $this->ipAddress,
                $this->userAgent, $deviceInfo['device_type'], $deviceInfo['browser'],
                $deviceInfo['os'], $referrer, $utmParams['source'], $utmParams['medium'], $utmParams['campaign']
            );
            
            $stmt->execute();
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Analytics tracking error: " . $e->getMessage());
        }
    }
    
    /**
     * Parse user agent for device information
     */
    private function parseUserAgent($userAgent) {
        $deviceType = 'unknown';
        $browser = 'unknown';
        $os = 'unknown';
        
        // Detect device type
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            if (preg_match('/iPad/', $userAgent)) {
                $deviceType = 'tablet';
            } else {
                $deviceType = 'mobile';
            }
        } else {
            $deviceType = 'desktop';
        }
        
        // Detect browser
        if (preg_match('/Chrome/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            $browser = 'Edge';
        }
        
        // Detect OS
        if (preg_match('/Windows/', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iOS/', $userAgent)) {
            $os = 'iOS';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os
        ];
    }
    
    /**
     * Extract UTM parameters from URL
     */
    private function extractUtmParameters() {
        return [
            'source' => $_GET['utm_source'] ?? null,
            'medium' => $_GET['utm_medium'] ?? null,
            'campaign' => $_GET['utm_campaign'] ?? null
        ];
    }
    
    /**
     * Track page view
     */
    public function trackPageView($pageUrl = null, $pageTitle = null, $pageType = 'other', $productId = null, $categoryId = null) {
        try {
            $pageUrl = $pageUrl ?: $_SERVER['REQUEST_URI'];
            $pageTitle = $pageTitle ?: (isset($_GET['title']) ? $_GET['title'] : '');
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;
            
            $stmt = $this->mysqli->prepare("
                INSERT INTO page_views (
                    visitor_id, customer_id, page_url, page_title, page_type,
                    product_id, category_id, session_id, ip_address, user_agent, referrer_url
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("sisssiissss",
                $this->visitorId, $this->customerId, $pageUrl, $pageTitle, $pageType,
                $productId, $categoryId, $this->sessionId, $this->ipAddress, $this->userAgent, $referrer
            );
            
            $stmt->execute();
            $stmt->close();
            
            // Update visitor page view count
            $this->updateVisitorPageViews();
            
            // Update product analytics if viewing a product
            if ($productId && $pageType === 'product') {
                $this->updateProductViews($productId);
            }
            
        } catch (Exception $e) {
            error_log("Page view tracking error: " . $e->getMessage());
        }
    }
    
    /**
     * Track user action
     */
    public function trackAction($actionType, $targetType = null, $targetId = null, $targetName = null, $actionValue = null, $quantity = null, $actionDetails = null) {
        try {
            $pageUrl = $_SERVER['REQUEST_URI'];
            $actionDetailsJson = $actionDetails ? json_encode($actionDetails) : null;
            
            $stmt = $this->mysqli->prepare("
                INSERT INTO user_actions (
                    visitor_id, customer_id, action_type, action_details, target_type,
                    target_id, target_name, page_url, session_id, action_value, quantity, ip_address, user_agent
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("sisssisssidss",
                $this->visitorId, $this->customerId, $actionType, $actionDetailsJson, $targetType,
                $targetId, $targetName, $pageUrl, $this->sessionId, $actionValue, $quantity, $this->ipAddress, $this->userAgent
            );
            
            $stmt->execute();
            $stmt->close();
            
            // Update specific analytics based on action type
            $this->updateActionSpecificAnalytics($actionType, $targetId, $actionValue, $quantity);
            
        } catch (Exception $e) {
            error_log("Action tracking error: " . $e->getMessage());
        }
    }
    
    /**
     * Track product view
     */
    public function trackProductView($productId, $productName = null, $timeOnPage = null) {
        $this->trackPageView($_SERVER['REQUEST_URI'], $productName, 'product', $productId);
        $this->trackAction('product_view', 'product', $productId, $productName, null, null, ['time_on_page' => $timeOnPage]);
    }
    
    /**
     * Track add to cart
     */
    public function trackAddToCart($productId, $productName = null, $price = null, $quantity = 1) {
        $this->trackAction('add_to_cart', 'product', $productId, $productName, $price, $quantity);
    }
    
    /**
     * Track purchase
     */
    public function trackPurchase($orderId, $orderValue, $products = []) {
        // Track main purchase action
        $this->trackAction('purchase', 'order', $orderId, "Order #$orderId", $orderValue, count($products));
        
        // Track individual product purchases
        foreach ($products as $product) {
            $this->trackAction('purchase', 'product', $product['id'], $product['name'], $product['price'], $product['quantity']);
        }
        
        // Update visitor conversion status
        $this->updateVisitorConversion($orderValue);
    }
    
    /**
     * Track search
     */
    public function trackSearch($searchQuery, $resultsCount = 0, $searchType = 'general') {
        try {
            $stmt = $this->mysqli->prepare("
                INSERT INTO search_analytics (
                    visitor_id, customer_id, search_query, search_type, results_count,
                    page_url, session_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $pageUrl = $_SERVER['REQUEST_URI'];
            $stmt->bind_param("sissis", 
                $this->visitorId, $this->customerId, $searchQuery, $searchType, $resultsCount, $pageUrl, $this->sessionId
            );
            
            $stmt->execute();
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Search tracking error: " . $e->getMessage());
        }
    }
    
    /**
     * Update visitor page view count
     */
    private function updateVisitorPageViews() {
        try {
            $stmt = $this->mysqli->prepare("
                UPDATE visitor_analytics 
                SET total_page_views = total_page_views + 1 
                WHERE visitor_id = ?
            ");
            $stmt->bind_param("s", $this->visitorId);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            error_log("Visitor page view update error: " . $e->getMessage());
        }
    }
    
    /**
     * Update product view analytics
     */
    private function updateProductViews($productId) {
        try {
            // Check if this is a unique view for this visitor
            $stmt = $this->mysqli->prepare("
                SELECT COUNT(*) as view_count 
                FROM page_views 
                WHERE visitor_id = ? AND product_id = ? AND page_type = 'product'
            ");
            $stmt->bind_param("si", $this->visitorId, $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $viewCount = $result->fetch_assoc()['view_count'];
            $stmt->close();
            
            $isUniqueView = $viewCount == 1; // First view by this visitor
            
            // Update product analytics
            $stmt = $this->mysqli->prepare("
                INSERT INTO product_analytics (product_id, total_views, unique_views) 
                VALUES (?, 1, ?) 
                ON DUPLICATE KEY UPDATE 
                    total_views = total_views + 1,
                    unique_views = unique_views + ?
            ");
            $uniqueIncrement = $isUniqueView ? 1 : 0;
            $stmt->bind_param("iii", $productId, $uniqueIncrement, $uniqueIncrement);
            $stmt->execute();
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Product view analytics error: " . $e->getMessage());
        }
    }
    
    /**
     * Update action-specific analytics
     */
    private function updateActionSpecificAnalytics($actionType, $targetId, $actionValue, $quantity) {
        try {
            switch ($actionType) {
                case 'add_to_cart':
                    if ($targetId) {
                        // Check if unique cart addition
                        $stmt = $this->mysqli->prepare("
                            SELECT COUNT(*) as count 
                            FROM user_actions 
                            WHERE visitor_id = ? AND action_type = 'add_to_cart' AND target_id = ?
                        ");
                        $stmt->bind_param("si", $this->visitorId, $targetId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $count = $result->fetch_assoc()['count'];
                        $stmt->close();
                        
                        $isUnique = $count == 1;
                        
                        // Update product analytics
                        $stmt = $this->mysqli->prepare("
                            UPDATE product_analytics 
                            SET total_cart_additions = total_cart_additions + 1,
                                unique_cart_additions = unique_cart_additions + ?
                            WHERE product_id = ?
                        ");
                        $uniqueIncrement = $isUnique ? 1 : 0;
                        $stmt->bind_param("ii", $uniqueIncrement, $targetId);
                        $stmt->execute();
                        $stmt->close();
                    }
                    break;
                    
                case 'purchase':
                    if ($targetId && $actionValue && $quantity) {
                        // Update product analytics
                        $stmt = $this->mysqli->prepare("
                            UPDATE product_analytics 
                            SET total_purchases = total_purchases + 1,
                                total_purchase_quantity = total_purchase_quantity + ?,
                                total_revenue = total_revenue + ?
                            WHERE product_id = ?
                        ");
                        $stmt->bind_param("idi", $quantity, $actionValue, $targetId);
                        $stmt->execute();
                        $stmt->close();
                    }
                    break;
            }
            
            // Update conversion rates
            if ($targetId && in_array($actionType, ['add_to_cart', 'purchase'])) {
                $this->updateProductConversionRates($targetId);
            }
            
        } catch (Exception $e) {
            error_log("Action analytics update error: " . $e->getMessage());
        }
    }
    
    /**
     * Update product conversion rates
     */
    private function updateProductConversionRates($productId) {
        try {
            $stmt = $this->mysqli->prepare("
                UPDATE product_analytics 
                SET view_to_cart_rate = CASE 
                        WHEN total_views > 0 THEN (total_cart_additions / total_views) * 100 
                        ELSE 0 
                    END,
                    cart_to_purchase_rate = CASE 
                        WHEN total_cart_additions > 0 THEN (total_purchases / total_cart_additions) * 100 
                        ELSE 0 
                    END,
                    overall_conversion_rate = CASE 
                        WHEN total_views > 0 THEN (total_purchases / total_views) * 100 
                        ELSE 0 
                    END
                WHERE product_id = ?
            ");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            error_log("Conversion rate update error: " . $e->getMessage());
        }
    }
    
    /**
     * Update visitor conversion status
     */
    private function updateVisitorConversion($orderValue) {
        try {
            $stmt = $this->mysqli->prepare("
                UPDATE visitor_analytics 
                SET has_purchased = TRUE,
                    total_orders = total_orders + 1,
                    total_order_value = total_order_value + ?
                WHERE visitor_id = ?
            ");
            $stmt->bind_param("ds", $orderValue, $this->visitorId);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            error_log("Visitor conversion update error: " . $e->getMessage());
        }
    }
    
    /**
     * Get visitor ID
     */
    public function getVisitorId() {
        return $this->visitorId;
    }
    
    /**
     * Get analytics data for a specific visitor
     */
    public function getVisitorAnalytics($visitorId = null) {
        $visitorId = $visitorId ?: $this->visitorId;
        
        try {
            $stmt = $this->mysqli->prepare("SELECT * FROM visitor_analytics WHERE visitor_id = ?");
            $stmt->bind_param("s", $visitorId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            
            return $data;
        } catch (Exception $e) {
            error_log("Get visitor analytics error: " . $e->getMessage());
            return null;
        }
    }
}
?>

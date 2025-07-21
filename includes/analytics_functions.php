<?php
/**
 * Analytics Helper Functions
 * Easy-to-use functions for tracking user behavior
 */

// Include the main analytics tracker
require_once __DIR__ . '/AnalyticsTracker.php';

// Global analytics tracker instance
$GLOBALS['analytics_tracker'] = null;

/**
 * Get or create analytics tracker instance
 */
function getAnalyticsTracker() {
    if (!isset($GLOBALS['analytics_tracker']) || $GLOBALS['analytics_tracker'] === null) {
        $GLOBALS['analytics_tracker'] = new AnalyticsTracker();
    }
    return $GLOBALS['analytics_tracker'];
}

/**
 * Initialize analytics tracking for a page
 * Call this at the beginning of each page
 */
function initializeAnalytics() {
    try {
        $tracker = getAnalyticsTracker();
        
        // Auto-detect page type and information
        $pageInfo = detectPageInfo();
        
        // Track the page view
        $tracker->trackPageView(
            $pageInfo['url'],
            $pageInfo['title'],
            $pageInfo['type'],
            $pageInfo['product_id'],
            $pageInfo['category_id']
        );
        
        return $tracker;
    } catch (Exception $e) {
        error_log("Analytics initialization error: " . $e->getMessage());
        return null;
    }
}

/**
 * Detect page information automatically
 */
function detectPageInfo() {
    $url = $_SERVER['REQUEST_URI'];
    $title = '';
    $type = 'other';
    $productId = null;
    $categoryId = null;
    
    // Detect page type based on URL patterns
    if ($url === '/' || strpos($url, 'index.php') !== false) {
        $type = 'home';
        $title = 'Home Page';
    } elseif (strpos($url, 'product_details.php') !== false || strpos($url, 'product.php') !== false) {
        $type = 'product';
        $productId = $_GET['ProductId'] ?? $_GET['product_id'] ?? null;
        $title = 'Product Details';
    } elseif (strpos($url, 'category') !== false || strpos($url, 'products.php') !== false) {
        $type = 'category';
        $categoryId = $_GET['CategoryId'] ?? $_GET['category_id'] ?? null;
        $title = 'Category Page';
    } elseif (strpos($url, 'cart') !== false) {
        $type = 'cart';
        $title = 'Shopping Cart';
    } elseif (strpos($url, 'checkout') !== false) {
        $type = 'checkout';
        $title = 'Checkout';
    }
    
    return [
        'url' => $url,
        'title' => $title,
        'type' => $type,
        'product_id' => $productId,
        'category_id' => $categoryId
    ];
}

/**
 * Track product view
 */
function trackProductView($productId, $productName = null, $timeOnPage = null) {
    try {
        $tracker = getAnalyticsTracker();
        $tracker->trackProductView($productId, $productName, $timeOnPage);
    } catch (Exception $e) {
        error_log("Product view tracking error: " . $e->getMessage());
    }
}

/**
 * Track add to cart action
 */
function trackAddToCart($productId, $productName = null, $price = null, $quantity = 1) {
    try {
        $tracker = getAnalyticsTracker();
        $tracker->trackAddToCart($productId, $productName, $price, $quantity);
    } catch (Exception $e) {
        error_log("Add to cart tracking error: " . $e->getMessage());
    }
}

/**
 * Track remove from cart action
 */
function trackRemoveFromCart($productId, $productName = null, $quantity = 1) {
    try {
        $tracker = getAnalyticsTracker();
        $tracker->trackAction('remove_from_cart', 'product', $productId, $productName, null, $quantity);
    } catch (Exception $e) {
        error_log("Remove from cart tracking error: " . $e->getMessage());
    }
}

/**
 * Track purchase/order completion
 */
function trackPurchase($orderId, $orderValue, $products = []) {
    try {
        $tracker = getAnalyticsTracker();
        $tracker->trackPurchase($orderId, $orderValue, $products);
    } catch (Exception $e) {
        error_log("Purchase tracking error: " . $e->getMessage());
    }
}

/**
 * Track search action
 */
function trackSearch($searchQuery, $resultsCount = 0, $searchType = 'general') {
    try {
        $tracker = getAnalyticsTracker();
        $tracker->trackSearch($searchQuery, $resultsCount, $searchType);
    } catch (Exception $e) {
        error_log("Search tracking error: " . $e->getMessage());
    }
}

/**
 * Track custom user action
 */
function trackCustomAction($actionType, $targetType = null, $targetId = null, $targetName = null, $actionValue = null, $quantity = null, $actionDetails = null) {
    try {
        $tracker = getAnalyticsTracker();
        $tracker->trackAction($actionType, $targetType, $targetId, $targetName, $actionValue, $quantity, $actionDetails);
    } catch (Exception $e) {
        error_log("Custom action tracking error: " . $e->getMessage());
    }
}

/**
 * Track button click
 */
function trackButtonClick($buttonName, $buttonType = 'button', $pageUrl = null) {
    try {
        $tracker = getAnalyticsTracker();
        $tracker->trackAction('click', 'button', null, $buttonName, null, null, [
            'button_type' => $buttonType,
            'page_url' => $pageUrl ?: $_SERVER['REQUEST_URI']
        ]);
    } catch (Exception $e) {
        error_log("Button click tracking error: " . $e->getMessage());
    }
}

/**
 * Track user registration
 */
function trackUserRegistration($customerId, $customerName = null, $customerEmail = null) {
    try {
        $tracker = getAnalyticsTracker();
        
        // Update visitor record with customer information
        $tracker->trackAction('registration', 'customer', $customerId, $customerName, null, null, [
            'customer_email' => $customerEmail
        ]);
        
        // Update visitor analytics to mark as registered
        include_once __DIR__ . '/../database/dbconnection.php';
        if (class_exists('main')) {
            $obj = new main();
            $mysqli = $obj->connection();
        } else {
            error_log("Database connection class not available for user registration tracking");
            return;
        }
        
        $visitorId = $tracker->getVisitorId();
        $stmt = $mysqli->prepare("
            UPDATE visitor_analytics 
            SET has_registered = TRUE, customer_id = ? 
            WHERE visitor_id = ?
        ");
        $stmt->bind_param("is", $customerId, $visitorId);
        $stmt->execute();
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("User registration tracking error: " . $e->getMessage());
    }
}

/**
 * Get visitor analytics data
 */
function getVisitorAnalytics($visitorId = null) {
    try {
        $tracker = getAnalyticsTracker();
        return $tracker->getVisitorAnalytics($visitorId);
    } catch (Exception $e) {
        error_log("Get visitor analytics error: " . $e->getMessage());
        return null;
    }
}

/**
 * Get current visitor ID
 */
function getCurrentVisitorId() {
    try {
        $tracker = getAnalyticsTracker();
        return $tracker->getVisitorId();
    } catch (Exception $e) {
        error_log("Get visitor ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Generate analytics JavaScript for client-side tracking
 */
function generateAnalyticsJS() {
    $visitorId = getCurrentVisitorId();
    $customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : 'null';
    
    return "
    <script>
    // Nutrify Analytics Tracking
    window.NutrifyAnalytics = {
        visitorId: '{$visitorId}',
        customerId: {$customerId},
        
        // Track page time
        pageStartTime: Date.now(),
        
        // Track scroll depth
        maxScrollDepth: 0,
        
        // Initialize tracking
        init: function() {
            this.trackScrollDepth();
            this.trackTimeOnPage();
            this.trackClicks();
        },
        
        // Track scroll depth
        trackScrollDepth: function() {
            var self = this;
            window.addEventListener('scroll', function() {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                var docHeight = document.documentElement.scrollHeight - window.innerHeight;
                var scrollPercent = Math.round((scrollTop / docHeight) * 100);
                
                if (scrollPercent > self.maxScrollDepth) {
                    self.maxScrollDepth = scrollPercent;
                }
            });
        },
        
        // Track time on page
        trackTimeOnPage: function() {
            var self = this;
            window.addEventListener('beforeunload', function() {
                var timeOnPage = Math.round((Date.now() - self.pageStartTime) / 1000);
                
                // Send time on page data
                navigator.sendBeacon('analytics_endpoint.php', JSON.stringify({
                    action: 'time_on_page',
                    visitor_id: self.visitorId,
                    time_on_page: timeOnPage,
                    scroll_depth: self.maxScrollDepth,
                    page_url: window.location.pathname
                }));
            });
        },
        
        // Track clicks
        trackClicks: function() {
            var self = this;
            document.addEventListener('click', function(e) {
                var target = e.target;
                var clickData = {
                    action: 'click',
                    visitor_id: self.visitorId,
                    element_type: target.tagName.toLowerCase(),
                    element_text: target.textContent.trim().substring(0, 100),
                    element_id: target.id || null,
                    element_class: target.className || null,
                    page_url: window.location.pathname
                };
                
                // Send click data asynchronously
                fetch('analytics_endpoint.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(clickData)
                }).catch(function(error) {
                    console.log('Analytics tracking error:', error);
                });
            });
        },
        
        // Track custom event
        trackEvent: function(eventType, eventData) {
            var data = {
                action: 'custom_event',
                visitor_id: this.visitorId,
                event_type: eventType,
                event_data: eventData,
                page_url: window.location.pathname
            };
            
            fetch('analytics_endpoint.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            }).catch(function(error) {
                console.log('Analytics tracking error:', error);
            });
        }
    };
    
    // Initialize analytics when page loads
    document.addEventListener('DOMContentLoaded', function() {
        NutrifyAnalytics.init();
    });
    </script>
    ";
}

/**
 * Setup analytics database tables
 */
function setupAnalyticsDatabase() {
    try {
        include_once __DIR__ . '/../database/dbconnection.php';
        if (!class_exists('main')) {
            error_log("Database connection class not available for analytics setup");
            return false;
        }
        $obj = new main();
        $mysqli = $obj->connection();
        
        // Read and execute the analytics schema
        $schemaFile = __DIR__ . '/../database/analytics_system_schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            
            // Split by semicolon and execute each statement
            $statements = explode(';', $sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $mysqli->query($statement);
                }
            }
            
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Analytics database setup error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get analytics summary data
 */
function getAnalyticsSummary($days = 30) {
    try {
        include_once __DIR__ . '/../database/dbconnection.php';
        if (!class_exists('main')) {
            error_log("Database connection class not available for analytics summary");
            return [];
        }
        $obj = new main();
        $mysqli = $obj->connection();
        
        $summary = [];
        
        // Total visitors
        $result = $mysqli->query("
            SELECT COUNT(DISTINCT visitor_id) as total_visitors 
            FROM visitor_analytics 
            WHERE first_visit >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        ");
        $summary['total_visitors'] = $result->fetch_assoc()['total_visitors'];
        
        // Total page views
        $result = $mysqli->query("
            SELECT COUNT(*) as total_page_views 
            FROM page_views 
            WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        ");
        $summary['total_page_views'] = $result->fetch_assoc()['total_page_views'];
        
        // Top products
        $result = $mysqli->query("
            SELECT p.ProductName, pa.total_views, pa.total_cart_additions, pa.total_purchases 
            FROM product_analytics pa 
            JOIN product_master p ON pa.product_id = p.ProductId 
            ORDER BY pa.total_views DESC 
            LIMIT 10
        ");
        $summary['top_products'] = $result->fetch_all(MYSQLI_ASSOC);
        
        // Conversion rate
        $result = $mysqli->query("
            SELECT 
                COUNT(DISTINCT visitor_id) as total_visitors,
                COUNT(DISTINCT CASE WHEN has_purchased = TRUE THEN visitor_id END) as converted_visitors
            FROM visitor_analytics 
            WHERE first_visit >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        ");
        $conversionData = $result->fetch_assoc();
        $summary['conversion_rate'] = $conversionData['total_visitors'] > 0 
            ? round(($conversionData['converted_visitors'] / $conversionData['total_visitors']) * 100, 2) 
            : 0;
        
        return $summary;
    } catch (Exception $e) {
        error_log("Analytics summary error: " . $e->getMessage());
        return [];
    }
}

/**
 * Clean old analytics data
 */
function cleanOldAnalyticsData($daysToKeep = 365) {
    try {
        include_once __DIR__ . '/../database/dbconnection.php';
        if (!class_exists('main')) {
            error_log("Database connection class not available for analytics cleanup");
            return false;
        }
        $obj = new main();
        $mysqli = $obj->connection();
        
        $cutoffDate = date('Y-m-d', strtotime("-{$daysToKeep} days"));
        
        // Clean old page views
        $mysqli->query("DELETE FROM page_views WHERE viewed_at < '{$cutoffDate}'");
        
        // Clean old user actions
        $mysqli->query("DELETE FROM user_actions WHERE created_at < '{$cutoffDate}'");
        
        // Clean old search analytics
        $mysqli->query("DELETE FROM search_analytics WHERE searched_at < '{$cutoffDate}'");
        
        return true;
    } catch (Exception $e) {
        error_log("Analytics cleanup error: " . $e->getMessage());
        return false;
    }
}
?>

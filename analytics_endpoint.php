<?php
/**
 * Analytics Endpoint
 * Handles AJAX requests for client-side analytics tracking
 */

// Set headers for AJAX requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Include required files
require_once __DIR__ . '/includes/analytics_functions.php';

try {
    // Get request data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate visitor ID
    if (!isset($data['visitor_id']) || empty($data['visitor_id'])) {
        throw new Exception('Visitor ID is required');
    }
    
    $action = $data['action'] ?? '';
    $visitorId = $data['visitor_id'];
    
    // Get analytics tracker
    $tracker = getAnalyticsTracker();
    
    switch ($action) {
        case 'time_on_page':
            handleTimeOnPage($tracker, $data);
            break;
            
        case 'click':
            handleClick($tracker, $data);
            break;
            
        case 'scroll':
            handleScroll($tracker, $data);
            break;
            
        case 'custom_event':
            handleCustomEvent($tracker, $data);
            break;
            
        case 'product_interaction':
            handleProductInteraction($tracker, $data);
            break;
            
        case 'search':
            handleSearch($tracker, $data);
            break;
            
        default:
            throw new Exception('Unknown action: ' . $action);
    }
    
    echo json_encode(['status' => 'success', 'message' => 'Analytics data recorded']);
    
} catch (Exception $e) {
    error_log("Analytics endpoint error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

/**
 * Handle time on page tracking
 */
function handleTimeOnPage($tracker, $data) {
    $timeOnPage = $data['time_on_page'] ?? 0;
    $scrollDepth = $data['scroll_depth'] ?? 0;
    $pageUrl = $data['page_url'] ?? '';
    
    // Update page view record with time on page and scroll depth
    include_once __DIR__ . '/database/dbconnection.php';
    if (!class_exists('main')) {
        throw new Exception("Database connection class not available");
    }
    $obj = new main();
    $mysqli = $obj->connection();
    
    $stmt = $mysqli->prepare("
        UPDATE page_views 
        SET time_on_page = ?, scroll_depth = ? 
        WHERE visitor_id = ? AND page_url = ? 
        ORDER BY viewed_at DESC 
        LIMIT 1
    ");
    $stmt->bind_param("idss", $timeOnPage, $scrollDepth, $data['visitor_id'], $pageUrl);
    $stmt->execute();
    $stmt->close();
    
    // Track as action for detailed analytics
    $tracker->trackAction('page_time', 'page', null, $pageUrl, null, null, [
        'time_on_page' => $timeOnPage,
        'scroll_depth' => $scrollDepth
    ]);
}

/**
 * Handle click tracking
 */
function handleClick($tracker, $data) {
    $elementType = $data['element_type'] ?? 'unknown';
    $elementText = $data['element_text'] ?? '';
    $elementId = $data['element_id'] ?? null;
    $elementClass = $data['element_class'] ?? null;
    $pageUrl = $data['page_url'] ?? '';
    
    $tracker->trackAction('click', $elementType, null, $elementText, null, null, [
        'element_id' => $elementId,
        'element_class' => $elementClass,
        'page_url' => $pageUrl
    ]);
}

/**
 * Handle scroll tracking
 */
function handleScroll($tracker, $data) {
    $scrollDepth = $data['scroll_depth'] ?? 0;
    $pageUrl = $data['page_url'] ?? '';
    
    $tracker->trackAction('scroll', 'page', null, $pageUrl, null, null, [
        'scroll_depth' => $scrollDepth
    ]);
}

/**
 * Handle custom event tracking
 */
function handleCustomEvent($tracker, $data) {
    $eventType = $data['event_type'] ?? 'custom';
    $eventData = $data['event_data'] ?? [];
    $pageUrl = $data['page_url'] ?? '';
    
    $tracker->trackAction($eventType, 'custom', null, $eventType, null, null, array_merge($eventData, [
        'page_url' => $pageUrl
    ]));
}

/**
 * Handle product interaction tracking
 */
function handleProductInteraction($tracker, $data) {
    $interactionType = $data['interaction_type'] ?? 'view';
    $productId = $data['product_id'] ?? null;
    $productName = $data['product_name'] ?? null;
    $price = $data['price'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    
    switch ($interactionType) {
        case 'view':
            $tracker->trackProductView($productId, $productName);
            break;
            
        case 'add_to_cart':
            $tracker->trackAddToCart($productId, $productName, $price, $quantity);
            break;
            
        case 'remove_from_cart':
            $tracker->trackAction('remove_from_cart', 'product', $productId, $productName, $price, $quantity);
            break;
            
        case 'wishlist_add':
            $tracker->trackAction('wishlist_add', 'product', $productId, $productName);
            break;
            
        case 'image_zoom':
            $tracker->trackAction('image_zoom', 'product', $productId, $productName);
            break;
            
        case 'video_play':
            $tracker->trackAction('video_play', 'product', $productId, $productName);
            break;
    }
}

/**
 * Handle search tracking
 */
function handleSearch($tracker, $data) {
    $searchQuery = $data['search_query'] ?? '';
    $resultsCount = $data['results_count'] ?? 0;
    $searchType = $data['search_type'] ?? 'general';
    
    $tracker->trackSearch($searchQuery, $resultsCount, $searchType);
}

/**
 * Get analytics data (for AJAX requests)
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_analytics'])) {
    try {
        $type = $_GET['type'] ?? 'summary';
        $days = intval($_GET['days'] ?? 30);
        
        switch ($type) {
            case 'summary':
                $data = getAnalyticsSummary($days);
                break;
                
            case 'visitor':
                $visitorId = $_GET['visitor_id'] ?? null;
                $data = getVisitorAnalytics($visitorId);
                break;
                
            case 'popular_products':
                $data = getPopularProducts($days);
                break;
                
            case 'daily_stats':
                $data = getDailyStats($days);
                break;
                
            default:
                throw new Exception('Unknown analytics type');
        }
        
        echo json_encode(['status' => 'success', 'data' => $data]);
        
    } catch (Exception $e) {
        error_log("Analytics data retrieval error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Get popular products data
 */
function getPopularProducts($days = 30) {
    include_once __DIR__ . '/database/dbconnection.php';
    if (!class_exists('main')) {
        return [];
    }
    $obj = new main();
    $mysqli = $obj->connection();
    
    $result = $mysqli->query("
        SELECT 
            p.ProductId,
            p.ProductName,
            p.PhotoPath,
            pa.total_views,
            pa.unique_views,
            pa.total_cart_additions,
            pa.total_purchases,
            pa.total_revenue,
            pa.overall_conversion_rate
        FROM product_analytics pa
        JOIN product_master p ON pa.product_id = p.ProductId
        WHERE pa.last_updated >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        ORDER BY pa.total_views DESC
        LIMIT 20
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get daily statistics
 */
function getDailyStats($days = 30) {
    include_once __DIR__ . '/database/dbconnection.php';
    if (!class_exists('main')) {
        return [];
    }
    $obj = new main();
    $mysqli = $obj->connection();
    
    $result = $mysqli->query("
        SELECT 
            DATE(viewed_at) as date,
            COUNT(DISTINCT visitor_id) as unique_visitors,
            COUNT(*) as page_views,
            COUNT(DISTINCT CASE WHEN page_type = 'product' THEN visitor_id END) as product_viewers
        FROM page_views
        WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        GROUP BY DATE(viewed_at)
        ORDER BY date DESC
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>

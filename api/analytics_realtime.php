<?php
/**
 * Real-time Analytics API
 * Provides live analytics data for dashboards and monitoring
 */

// Set headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Include required files
require_once __DIR__ . '/../includes/analytics_functions.php';
require_once __DIR__ . '/../database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

try {
    $endpoint = $_GET['endpoint'] ?? '';
    $timeframe = $_GET['timeframe'] ?? '24h';
    
    switch ($endpoint) {
        case 'live_visitors':
            echo json_encode(getLiveVisitors($mysqli, $timeframe));
            break;
            
        case 'popular_products':
            echo json_encode(getPopularProductsRealtime($mysqli, $timeframe));
            break;
            
        case 'conversion_funnel':
            echo json_encode(getConversionFunnel($mysqli, $timeframe));
            break;
            
        case 'traffic_sources':
            echo json_encode(getTrafficSources($mysqli, $timeframe));
            break;
            
        case 'device_breakdown':
            echo json_encode(getDeviceBreakdown($mysqli, $timeframe));
            break;
            
        case 'page_performance':
            echo json_encode(getPagePerformance($mysqli, $timeframe));
            break;
            
        case 'search_trends':
            echo json_encode(getSearchTrends($mysqli, $timeframe));
            break;
            
        case 'revenue_metrics':
            echo json_encode(getRevenueMetrics($mysqli, $timeframe));
            break;
            
        case 'user_journey':
            $visitorId = $_GET['visitor_id'] ?? null;
            echo json_encode(getUserJourney($mysqli, $visitorId));
            break;
            
        case 'alerts':
            echo json_encode(getAnalyticsAlerts($mysqli));
            break;
            
        default:
            throw new Exception('Unknown endpoint: ' . $endpoint);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Get live visitors data
 */
function getLiveVisitors($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            COUNT(DISTINCT visitor_id) as current_visitors,
            COUNT(DISTINCT CASE WHEN customer_id IS NOT NULL THEN visitor_id END) as logged_in_visitors,
            COUNT(*) as total_page_views,
            AVG(time_on_page) as avg_time_on_page
        FROM page_views 
        WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL $interval)
    ");
    
    $data = $result->fetch_assoc();
    
    // Get visitor activity timeline
    $result = $mysqli->query("
        SELECT 
            DATE_FORMAT(viewed_at, '%H:%i') as time,
            COUNT(DISTINCT visitor_id) as visitors,
            COUNT(*) as page_views
        FROM page_views 
        WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY DATE_FORMAT(viewed_at, '%H:%i')
        ORDER BY time DESC
        LIMIT 20
    ");
    
    $timeline = $result->fetch_all(MYSQLI_ASSOC);
    
    return [
        'current_visitors' => intval($data['current_visitors']),
        'logged_in_visitors' => intval($data['logged_in_visitors']),
        'total_page_views' => intval($data['total_page_views']),
        'avg_time_on_page' => round($data['avg_time_on_page'] ?? 0, 2),
        'timeline' => array_reverse($timeline)
    ];
}

/**
 * Get popular products in real-time
 */
function getPopularProductsRealtime($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            p.ProductId,
            p.ProductName,
            p.PhotoPath,
            COUNT(DISTINCT pv.visitor_id) as unique_viewers,
            COUNT(pv.id) as total_views,
            COUNT(DISTINCT ua_cart.visitor_id) as cart_additions,
            COUNT(DISTINCT ua_purchase.visitor_id) as purchases
        FROM product_master p
        LEFT JOIN page_views pv ON p.ProductId = pv.product_id 
            AND pv.viewed_at >= DATE_SUB(NOW(), INTERVAL $interval)
        LEFT JOIN user_actions ua_cart ON p.ProductId = ua_cart.target_id 
            AND ua_cart.action_type = 'add_to_cart'
            AND ua_cart.created_at >= DATE_SUB(NOW(), INTERVAL $interval)
        LEFT JOIN user_actions ua_purchase ON p.ProductId = ua_purchase.target_id 
            AND ua_purchase.action_type = 'purchase'
            AND ua_purchase.created_at >= DATE_SUB(NOW(), INTERVAL $interval)
        WHERE pv.id IS NOT NULL
        GROUP BY p.ProductId, p.ProductName, p.PhotoPath
        ORDER BY total_views DESC
        LIMIT 10
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get conversion funnel data
 */
function getConversionFunnel($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            COUNT(DISTINCT CASE WHEN action_type = 'page_view' THEN visitor_id END) as page_views,
            COUNT(DISTINCT CASE WHEN action_type = 'product_view' THEN visitor_id END) as product_views,
            COUNT(DISTINCT CASE WHEN action_type = 'add_to_cart' THEN visitor_id END) as cart_additions,
            COUNT(DISTINCT CASE WHEN action_type = 'purchase' THEN visitor_id END) as purchases
        FROM user_actions 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL $interval)
    ");
    
    $data = $result->fetch_assoc();
    
    // Calculate conversion rates
    $pageViews = intval($data['page_views']);
    $productViews = intval($data['product_views']);
    $cartAdditions = intval($data['cart_additions']);
    $purchases = intval($data['purchases']);
    
    return [
        'funnel' => [
            ['stage' => 'Page Views', 'count' => $pageViews, 'rate' => 100],
            ['stage' => 'Product Views', 'count' => $productViews, 'rate' => $pageViews > 0 ? round(($productViews / $pageViews) * 100, 2) : 0],
            ['stage' => 'Cart Additions', 'count' => $cartAdditions, 'rate' => $productViews > 0 ? round(($cartAdditions / $productViews) * 100, 2) : 0],
            ['stage' => 'Purchases', 'count' => $purchases, 'rate' => $cartAdditions > 0 ? round(($purchases / $cartAdditions) * 100, 2) : 0]
        ]
    ];
}

/**
 * Get traffic sources
 */
function getTrafficSources($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            CASE 
                WHEN referrer_url IS NULL OR referrer_url = '' THEN 'Direct'
                WHEN referrer_url LIKE '%google%' THEN 'Google'
                WHEN referrer_url LIKE '%facebook%' THEN 'Facebook'
                WHEN referrer_url LIKE '%instagram%' THEN 'Instagram'
                WHEN referrer_url LIKE '%youtube%' THEN 'YouTube'
                ELSE 'Other'
            END as source,
            COUNT(DISTINCT visitor_id) as visitors,
            COUNT(*) as sessions
        FROM visitor_analytics 
        WHERE first_visit >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY source
        ORDER BY visitors DESC
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get device breakdown
 */
function getDeviceBreakdown($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            device_type,
            browser,
            COUNT(DISTINCT visitor_id) as visitors,
            AVG(total_page_views) as avg_pages_per_session,
            AVG(total_session_duration) as avg_session_duration
        FROM visitor_analytics 
        WHERE first_visit >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY device_type, browser
        ORDER BY visitors DESC
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get page performance metrics
 */
function getPagePerformance($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            page_url,
            page_type,
            COUNT(*) as views,
            COUNT(DISTINCT visitor_id) as unique_visitors,
            AVG(time_on_page) as avg_time_on_page,
            AVG(scroll_depth) as avg_scroll_depth
        FROM page_views 
        WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY page_url, page_type
        ORDER BY views DESC
        LIMIT 20
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get search trends
 */
function getSearchTrends($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            search_query,
            COUNT(*) as search_count,
            AVG(results_count) as avg_results,
            COUNT(DISTINCT visitor_id) as unique_searchers
        FROM search_analytics 
        WHERE searched_at >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY search_query
        ORDER BY search_count DESC
        LIMIT 20
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get revenue metrics
 */
function getRevenueMetrics($mysqli, $timeframe) {
    $interval = getTimeInterval($timeframe);
    
    $result = $mysqli->query("
        SELECT 
            SUM(action_value) as total_revenue,
            COUNT(DISTINCT visitor_id) as purchasing_visitors,
            AVG(action_value) as avg_order_value,
            COUNT(*) as total_orders
        FROM user_actions 
        WHERE action_type = 'purchase' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL $interval)
        AND action_value > 0
    ");
    
    $data = $result->fetch_assoc();
    
    // Get revenue timeline
    $result = $mysqli->query("
        SELECT 
            DATE_FORMAT(created_at, '%H:%i') as time,
            SUM(action_value) as revenue,
            COUNT(*) as orders
        FROM user_actions 
        WHERE action_type = 'purchase' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL $interval)
        AND action_value > 0
        GROUP BY DATE_FORMAT(created_at, '%H:%i')
        ORDER BY time DESC
        LIMIT 20
    ");
    
    $timeline = $result->fetch_all(MYSQLI_ASSOC);
    
    return [
        'total_revenue' => floatval($data['total_revenue'] ?? 0),
        'purchasing_visitors' => intval($data['purchasing_visitors'] ?? 0),
        'avg_order_value' => floatval($data['avg_order_value'] ?? 0),
        'total_orders' => intval($data['total_orders'] ?? 0),
        'timeline' => array_reverse($timeline)
    ];
}

/**
 * Get user journey for a specific visitor
 */
function getUserJourney($mysqli, $visitorId) {
    if (!$visitorId) {
        return ['error' => 'Visitor ID required'];
    }
    
    $stmt = $mysqli->prepare("
        SELECT 
            page_url,
            page_type,
            time_on_page,
            scroll_depth,
            viewed_at
        FROM page_views 
        WHERE visitor_id = ?
        ORDER BY viewed_at ASC
    ");
    $stmt->bind_param("s", $visitorId);
    $stmt->execute();
    $pageViews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $stmt = $mysqli->prepare("
        SELECT 
            action_type,
            target_type,
            target_name,
            action_value,
            created_at
        FROM user_actions 
        WHERE visitor_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->bind_param("s", $visitorId);
    $stmt->execute();
    $actions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return [
        'visitor_id' => $visitorId,
        'page_views' => $pageViews,
        'actions' => $actions
    ];
}

/**
 * Get analytics alerts
 */
function getAnalyticsAlerts($mysqli) {
    $alerts = [];
    
    // Check for unusual traffic spikes
    $result = $mysqli->query("
        SELECT COUNT(*) as current_hour_views
        FROM page_views 
        WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $currentHourViews = $result->fetch_assoc()['current_hour_views'];
    
    $result = $mysqli->query("
        SELECT AVG(hourly_views) as avg_hourly_views
        FROM (
            SELECT COUNT(*) as hourly_views
            FROM page_views 
            WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            GROUP BY HOUR(viewed_at)
        ) as hourly_stats
    ");
    $avgHourlyViews = $result->fetch_assoc()['avg_hourly_views'];
    
    if ($currentHourViews > ($avgHourlyViews * 2)) {
        $alerts[] = [
            'type' => 'traffic_spike',
            'message' => 'Traffic spike detected: ' . $currentHourViews . ' views this hour (avg: ' . round($avgHourlyViews) . ')',
            'severity' => 'info'
        ];
    }
    
    // Check for low conversion rate
    $result = $mysqli->query("
        SELECT 
            COUNT(DISTINCT CASE WHEN action_type = 'product_view' THEN visitor_id END) as product_viewers,
            COUNT(DISTINCT CASE WHEN action_type = 'purchase' THEN visitor_id END) as purchasers
        FROM user_actions 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $conversionData = $result->fetch_assoc();
    $conversionRate = $conversionData['product_viewers'] > 0 
        ? ($conversionData['purchasers'] / $conversionData['product_viewers']) * 100 
        : 0;
    
    if ($conversionRate < 1 && $conversionData['product_viewers'] > 10) {
        $alerts[] = [
            'type' => 'low_conversion',
            'message' => 'Low conversion rate: ' . round($conversionRate, 2) . '% (last 24 hours)',
            'severity' => 'warning'
        ];
    }
    
    return $alerts;
}

/**
 * Convert timeframe to SQL interval
 */
function getTimeInterval($timeframe) {
    switch ($timeframe) {
        case '1h': return '1 HOUR';
        case '6h': return '6 HOUR';
        case '24h': return '24 HOUR';
        case '7d': return '7 DAY';
        case '30d': return '30 DAY';
        default: return '24 HOUR';
    }
}
?>

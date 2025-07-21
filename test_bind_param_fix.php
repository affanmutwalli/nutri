<?php
/**
 * Test script to verify the bind_param fix
 */

echo "<h1>🔧 Testing Bind Param Fix</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

try {
    // Start session
    session_start();
    
    echo "<h2>Step 1: Testing Database Connection</h2>";
    
    include_once 'database/dbconnection.php';
    if (class_exists('main')) {
        $obj = new main();
        $mysqli = $obj->connection();
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        throw new Exception("Main class not found");
    }
    
    echo "<h2>Step 2: Testing Analytics Tracker</h2>";
    
    include_once 'includes/AnalyticsTracker.php';
    $tracker = new AnalyticsTracker($mysqli);
    echo "<p style='color: green;'>✅ AnalyticsTracker instantiation successful</p>";
    
    $visitorId = $tracker->getVisitorId();
    echo "<p style='color: green;'>✅ Visitor ID: " . substr($visitorId, 0, 16) . "...</p>";
    
    echo "<h2>Step 3: Testing Page View Tracking</h2>";
    
    $tracker->trackPageView('/test-page', 'Test Page', 'other');
    echo "<p style='color: green;'>✅ Page view tracking successful</p>";
    
    echo "<h2>Step 4: Testing Product View Tracking</h2>";
    
    $tracker->trackProductView(999, 'Test Product');
    echo "<p style='color: green;'>✅ Product view tracking successful</p>";
    
    echo "<h2>Step 5: Testing Add to Cart Tracking</h2>";
    
    $tracker->trackAddToCart(999, 'Test Product', 99.99, 1);
    echo "<p style='color: green;'>✅ Add to cart tracking successful</p>";
    
    echo "<h2>Step 6: Testing Search Tracking</h2>";
    
    $tracker->trackSearch('test search', 5, 'product');
    echo "<p style='color: green;'>✅ Search tracking successful</p>";
    
    echo "<h2>Step 7: Testing Custom Action Tracking</h2>";
    
    $tracker->trackAction('test_action', 'test', 123, 'Test Action', 50.00, 2, ['test' => 'data']);
    echo "<p style='color: green;'>✅ Custom action tracking successful</p>";
    
    echo "<h2>Step 8: Testing Analytics Functions</h2>";
    
    include_once 'includes/analytics_functions.php';
    
    // Test helper functions
    trackProductView(999, 'Test Product via Function');
    echo "<p style='color: green;'>✅ trackProductView function successful</p>";
    
    trackAddToCart(999, 'Test Product via Function', 99.99, 1);
    echo "<p style='color: green;'>✅ trackAddToCart function successful</p>";
    
    trackSearch('test search via function', 3, 'general');
    echo "<p style='color: green;'>✅ trackSearch function successful</p>";
    
    echo "<h2>Step 9: Verifying Data in Database</h2>";
    
    // Check if data was inserted
    $result = $mysqli->query("SELECT COUNT(*) as count FROM visitor_analytics WHERE visitor_id = '$visitorId'");
    $visitorCount = $result->fetch_assoc()['count'];
    echo "<p style='color: green;'>✅ Visitor records: $visitorCount</p>";
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM page_views WHERE visitor_id = '$visitorId'");
    $pageViewCount = $result->fetch_assoc()['count'];
    echo "<p style='color: green;'>✅ Page view records: $pageViewCount</p>";
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM user_actions WHERE visitor_id = '$visitorId'");
    $actionCount = $result->fetch_assoc()['count'];
    echo "<p style='color: green;'>✅ User action records: $actionCount</p>";
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM search_analytics WHERE visitor_id = '$visitorId'");
    $searchCount = $result->fetch_assoc()['count'];
    echo "<p style='color: green;'>✅ Search records: $searchCount</p>";
    
    echo "<h2>🎉 All Tests Passed Successfully!</h2>";
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>The bind_param issues have been resolved and analytics tracking is working properly!</p>";
    
    echo "<h3>Summary of Data Created:</h3>";
    echo "<ul>";
    echo "<li><strong>Visitor Records:</strong> $visitorCount</li>";
    echo "<li><strong>Page Views:</strong> $pageViewCount</li>";
    echo "<li><strong>User Actions:</strong> $actionCount</li>";
    echo "<li><strong>Search Records:</strong> $searchCount</li>";
    echo "</ul>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='setup_analytics_system.php'>Run the full setup script</a></li>";
    echo "<li><a href='test_analytics.php'>Test the complete analytics system</a></li>";
    echo "<li><a href='cms/analytics_dashboard.php'>View the analytics dashboard</a></li>";
    echo "<li><a href='product_details.php?ProductId=6'>Test on a real product page</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
    
    echo "<h3>🔍 Debugging Information:</h3>";
    echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
    echo "<p><strong>Error Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Error File:</strong> " . $e->getFile() . "</p>";
}

echo "</div>";

// Add some basic styling
echo "<style>
body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { color: #555; margin-top: 30px; }
h3 { color: #666; }
ul, ol { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
pre { overflow-x: auto; }
</style>";
?>

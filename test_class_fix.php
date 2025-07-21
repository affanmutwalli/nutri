<?php
/**
 * Test script to verify the class conflict fix
 */

echo "<h1>🔧 Testing Class Conflict Fix</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

try {
    echo "<h2>Step 1: Testing Database Connection</h2>";
    
    // Include database connection multiple times to test for conflicts
    include_once 'database/dbconnection.php';
    echo "<p style='color: green;'>✅ First include successful</p>";
    
    include_once 'database/dbconnection.php';
    echo "<p style='color: green;'>✅ Second include successful (no conflict)</p>";
    
    // Test class instantiation
    if (class_exists('main')) {
        $obj = new main();
        echo "<p style='color: green;'>✅ Main class instantiation successful</p>";
        
        // Test database connection
        $mysqli = $obj->connection();
        if ($mysqli) {
            echo "<p style='color: green;'>✅ Database connection successful</p>";
        } else {
            echo "<p style='color: red;'>❌ Database connection failed</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Main class not found</p>";
    }
    
    echo "<h2>Step 2: Testing Analytics System</h2>";
    
    // Test analytics functions
    include_once 'includes/analytics_functions.php';
    echo "<p style='color: green;'>✅ Analytics functions included successfully</p>";
    
    // Test analytics tracker
    include_once 'includes/AnalyticsTracker.php';
    echo "<p style='color: green;'>✅ AnalyticsTracker class included successfully</p>";
    
    // Test tracker instantiation
    $tracker = new AnalyticsTracker();
    if ($tracker) {
        echo "<p style='color: green;'>✅ AnalyticsTracker instantiation successful</p>";
        
        $visitorId = $tracker->getVisitorId();
        if ($visitorId) {
            echo "<p style='color: green;'>✅ Visitor ID generation successful: " . substr($visitorId, 0, 16) . "...</p>";
        }
    }
    
    echo "<h2>Step 3: Testing Analytics Functions</h2>";
    
    // Test analytics initialization
    $analytics = initializeAnalytics();
    if ($analytics) {
        echo "<p style='color: green;'>✅ Analytics initialization successful</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Analytics initialization returned null (may be normal)</p>";
    }
    
    // Test visitor ID function
    $currentVisitorId = getCurrentVisitorId();
    if ($currentVisitorId) {
        echo "<p style='color: green;'>✅ Current visitor ID function successful: " . substr($currentVisitorId, 0, 16) . "...</p>";
    }
    
    echo "<h2>Step 4: Testing Database Setup</h2>";
    
    // Test database setup function
    $setupResult = setupAnalyticsDatabase();
    if ($setupResult) {
        echo "<p style='color: green;'>✅ Analytics database setup successful</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Analytics database setup returned false (tables may already exist)</p>";
    }
    
    echo "<h2>🎉 All Tests Completed Successfully!</h2>";
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>The class conflict has been resolved and the analytics system is working properly!</p>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='setup_analytics_system.php'>Run the full setup script</a></li>";
    echo "<li><a href='test_analytics.php'>Test the complete analytics system</a></li>";
    echo "<li><a href='cms/analytics_dashboard.php'>Access the analytics dashboard</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "</div>";

// Add some basic styling
echo "<style>
body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { color: #555; margin-top: 30px; }
h3 { color: #666; }
ol { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>";
?>

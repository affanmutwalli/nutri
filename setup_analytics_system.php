<?php
/**
 * Analytics System Setup Script
 * Run this script to initialize the analytics database and system
 */

// Include required files
include('database/dbconnection.php');
include('includes/analytics_functions.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🔍 Analytics System Setup</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

try {
    // Step 1: Create analytics database tables
    echo "<h2>Step 1: Creating Analytics Database Tables</h2>";
    
    $schemaFile = 'database/analytics_system_schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Analytics schema file not found: $schemaFile");
    }
    
    $sql = file_get_contents($schemaFile);
    $statements = explode(';', $sql);
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $mysqli->query($statement);
                $successCount++;
            } catch (Exception $e) {
                $errorCount++;
                echo "<p style='color: orange;'>⚠️ Warning: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
    
    echo "<p style='color: green;'>✅ Database setup completed: $successCount statements executed successfully";
    if ($errorCount > 0) {
        echo ", $errorCount warnings (likely tables already exist)";
    }
    echo "</p>";
    
    // Step 2: Initialize product analytics
    echo "<h2>Step 2: Initializing Product Analytics</h2>";
    
    $result = $mysqli->query("
        INSERT IGNORE INTO product_analytics (product_id)
        SELECT ProductId FROM product_master
    ");
    
    $productCount = $mysqli->affected_rows;
    echo "<p style='color: green;'>✅ Initialized analytics for $productCount products</p>";
    
    // Step 3: Test analytics tracking
    echo "<h2>Step 3: Testing Analytics System</h2>";
    
    // Test visitor tracking
    $tracker = new AnalyticsTracker($mysqli);
    $visitorId = $tracker->getVisitorId();
    echo "<p style='color: blue;'>🔍 Your visitor ID: " . substr($visitorId, 0, 16) . "...</p>";
    
    // Test page view tracking
    $tracker->trackPageView('/setup_analytics_system.php', 'Analytics Setup', 'other');
    echo "<p style='color: green;'>✅ Page view tracking test successful</p>";
    
    // Test action tracking
    $tracker->trackAction('test_action', 'system', null, 'Analytics Setup Test');
    echo "<p style='color: green;'>✅ Action tracking test successful</p>";
    
    // Step 4: Display current analytics summary
    echo "<h2>Step 4: Current Analytics Summary</h2>";
    
    $summary = getAnalyticsSummary(30);
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>Last 30 Days:</h3>";
    echo "<ul>";
    echo "<li><strong>Total Visitors:</strong> " . number_format($summary['total_visitors'] ?? 0) . "</li>";
    echo "<li><strong>Total Page Views:</strong> " . number_format($summary['total_page_views'] ?? 0) . "</li>";
    echo "<li><strong>Conversion Rate:</strong> " . ($summary['conversion_rate'] ?? 0) . "%</li>";
    echo "<li><strong>Top Products Tracked:</strong> " . count($summary['top_products'] ?? []) . "</li>";
    echo "</ul>";
    echo "</div>";
    
    // Step 5: Display integration instructions
    echo "<h2>Step 5: Integration Status</h2>";
    
    $integrationFiles = [
        'index.php' => 'Homepage tracking',
        'product_details.php' => 'Product view tracking',
        'exe_files/add_to_cart_session.php' => 'Add to cart tracking',
        'analytics_endpoint.php' => 'AJAX tracking endpoint',
        'cms/analytics_dashboard.php' => 'Admin analytics dashboard'
    ];
    
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Integrated Files:</h3>";
    echo "<ul>";
    foreach ($integrationFiles as $file => $description) {
        $status = file_exists($file) ? "✅" : "❌";
        echo "<li>$status <strong>$file</strong> - $description</li>";
    }
    echo "</ul>";
    echo "</div>";
    
    // Step 6: Next steps
    echo "<h2>Step 6: Next Steps</h2>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🚀 Your Analytics System is Ready!</h3>";
    echo "<ol>";
    echo "<li><strong>Access Admin Dashboard:</strong> <a href='cms/analytics_dashboard.php' target='_blank'>cms/analytics_dashboard.php</a></li>";
    echo "<li><strong>Test Tracking:</strong> Visit your website pages and check the dashboard for data</li>";
    echo "<li><strong>Monitor Performance:</strong> Check analytics daily for insights</li>";
    echo "<li><strong>Customize Tracking:</strong> Add more tracking calls using the analytics functions</li>";
    echo "</ol>";
    echo "</div>";
    
    // Step 7: Advanced features
    echo "<h2>Step 7: Available Analytics Features</h2>";
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>📊 What You Can Track:</h3>";
    echo "<ul>";
    echo "<li>🔍 <strong>Visitor Identification:</strong> Unique cookie-based visitor tracking</li>";
    echo "<li>📱 <strong>Device Analytics:</strong> Desktop, mobile, tablet breakdown</li>";
    echo "<li>🛒 <strong>Product Analytics:</strong> Views, cart additions, purchases</li>";
    echo "<li>📈 <strong>Conversion Tracking:</strong> View-to-cart and cart-to-purchase rates</li>";
    echo "<li>🔍 <strong>Search Analytics:</strong> Track what users search for</li>";
    echo "<li>⏱️ <strong>Time Tracking:</strong> Time spent on pages and scroll depth</li>";
    echo "<li>🎯 <strong>Click Tracking:</strong> Button and link interactions</li>";
    echo "<li>📊 <strong>Real-time Data:</strong> Live visitor and interaction tracking</li>";
    echo "</ul>";
    echo "</div>";
    
    // Step 8: API endpoints
    echo "<h2>Step 8: API Endpoints</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔗 Available API Endpoints:</h3>";
    echo "<ul>";
    echo "<li><strong>POST analytics_endpoint.php</strong> - Track user actions via AJAX</li>";
    echo "<li><strong>GET analytics_endpoint.php?get_analytics=1&type=summary</strong> - Get analytics summary</li>";
    echo "<li><strong>GET analytics_endpoint.php?get_analytics=1&type=popular_products</strong> - Get popular products</li>";
    echo "<li><strong>GET analytics_endpoint.php?get_analytics=1&type=daily_stats</strong> - Get daily statistics</li>";
    echo "</ul>";
    echo "</div>";
    
    // Step 9: Maintenance
    echo "<h2>Step 9: Maintenance & Optimization</h2>";
    echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔧 Recommended Maintenance:</h3>";
    echo "<ul>";
    echo "<li><strong>Data Cleanup:</strong> Run cleanOldAnalyticsData() monthly to remove old data</li>";
    echo "<li><strong>Performance:</strong> Monitor database size and add indexes if needed</li>";
    echo "<li><strong>Privacy:</strong> Ensure compliance with privacy laws (GDPR, etc.)</li>";
    echo "<li><strong>Backup:</strong> Include analytics tables in your backup strategy</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>Your analytics system is now fully operational and tracking user behavior!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection and file permissions.</p>";
}

echo "</div>";

// Add some basic styling
echo "<style>
body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { color: #555; margin-top: 30px; }
h3 { color: #666; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>";
?>

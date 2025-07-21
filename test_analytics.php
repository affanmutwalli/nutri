<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics System Test - My Nutrify</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .analytics-data { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .test-button { margin: 10px 5px; }
    </style>
</head>
<body>

<?php
session_start();
include('includes/analytics_functions.php');

// Initialize analytics
$analytics = initializeAnalytics();
$visitorId = getCurrentVisitorId();
?>

<div class="container">
    <h1>🔍 Analytics System Test Page</h1>
    <p class="info">This page tests the analytics tracking system functionality.</p>
    
    <div class="test-section">
        <h2>📊 Current Analytics Status</h2>
        <div class="analytics-data">
            <p><strong>Your Visitor ID:</strong> <code><?php echo substr($visitorId, 0, 16) . '...'; ?></code></p>
            <p><strong>Session Status:</strong> 
                <?php if (isset($_SESSION['CustomerId'])): ?>
                    <span class="success">✅ Logged In (Customer ID: <?php echo $_SESSION['CustomerId']; ?>)</span>
                <?php else: ?>
                    <span class="info">👤 Guest User</span>
                <?php endif; ?>
            </p>
            <p><strong>Analytics Tracking:</strong> 
                <?php if ($analytics): ?>
                    <span class="success">✅ Active</span>
                <?php else: ?>
                    <span class="error">❌ Not Working</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="test-section">
        <h2>🧪 Test Analytics Functions</h2>
        <p>Click these buttons to test different analytics tracking features:</p>
        
        <button class="btn btn-primary test-button" onclick="testProductView()">Test Product View</button>
        <button class="btn btn-success test-button" onclick="testAddToCart()">Test Add to Cart</button>
        <button class="btn btn-warning test-button" onclick="testSearch()">Test Search</button>
        <button class="btn btn-info test-button" onclick="testCustomAction()">Test Custom Action</button>
        <button class="btn btn-secondary test-button" onclick="testButtonClick()">Test Button Click</button>
        
        <div id="test-results" class="analytics-data" style="display: none;">
            <h4>Test Results:</h4>
            <div id="test-output"></div>
        </div>
    </div>

    <div class="test-section">
        <h2>📈 Recent Analytics Data</h2>
        <?php
        try {
            $summary = getAnalyticsSummary(7);
            echo "<div class='analytics-data'>";
            echo "<h4>Last 7 Days Summary:</h4>";
            echo "<ul>";
            echo "<li><strong>Total Visitors:</strong> " . number_format($summary['total_visitors'] ?? 0) . "</li>";
            echo "<li><strong>Total Page Views:</strong> " . number_format($summary['total_page_views'] ?? 0) . "</li>";
            echo "<li><strong>Conversion Rate:</strong> " . ($summary['conversion_rate'] ?? 0) . "%</li>";
            echo "<li><strong>Top Products:</strong> " . count($summary['top_products'] ?? []) . " tracked</li>";
            echo "</ul>";
            echo "</div>";
            
            if (!empty($summary['top_products'])) {
                echo "<div class='analytics-data'>";
                echo "<h4>Top Products (Last 7 Days):</h4>";
                echo "<table class='table table-sm'>";
                echo "<thead><tr><th>Product</th><th>Views</th><th>Cart Adds</th><th>Purchases</th></tr></thead>";
                echo "<tbody>";
                foreach (array_slice($summary['top_products'], 0, 5) as $product) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($product['ProductName']) . "</td>";
                    echo "<td>" . number_format($product['total_views']) . "</td>";
                    echo "<td>" . number_format($product['total_cart_additions']) . "</td>";
                    echo "<td>" . number_format($product['total_purchases']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
                echo "</div>";
            }
        } catch (Exception $e) {
            echo "<div class='analytics-data error'>Error loading analytics data: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2>🔗 Quick Links</h2>
        <div class="row">
            <div class="col-md-6">
                <h4>Admin Dashboard</h4>
                <p><a href="cms/analytics_dashboard.php" class="btn btn-primary" target="_blank">View Analytics Dashboard</a></p>
                <p><small>Access the full analytics dashboard (requires admin login)</small></p>
            </div>
            <div class="col-md-6">
                <h4>Setup & Configuration</h4>
                <p><a href="setup_analytics_system.php" class="btn btn-secondary" target="_blank">Run Setup Script</a></p>
                <p><small>Initialize or update the analytics system</small></p>
            </div>
        </div>
    </div>

    <div class="test-section">
        <h2>📋 System Information</h2>
        <div class="analytics-data">
            <h4>Database Tables Status:</h4>
            <?php
            try {
                include('database/dbconnection.php');
                $obj = new main();
                $mysqli = $obj->connection();
                
                $tables = [
                    'visitor_analytics' => 'Visitor tracking',
                    'page_views' => 'Page view tracking',
                    'user_actions' => 'User action tracking',
                    'product_analytics' => 'Product analytics',
                    'search_analytics' => 'Search tracking'
                ];
                
                echo "<ul>";
                foreach ($tables as $table => $description) {
                    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
                    if ($result && $result->num_rows > 0) {
                        $count = $mysqli->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc()['count'];
                        echo "<li class='success'>✅ <strong>$table</strong> - $description ($count records)</li>";
                    } else {
                        echo "<li class='error'>❌ <strong>$table</strong> - $description (not found)</li>";
                    }
                }
                echo "</ul>";
            } catch (Exception $e) {
                echo "<p class='error'>Error checking database: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>
    </div>

</div>

<?php 
// Add Analytics JavaScript Tracking
echo generateAnalyticsJS(); 
?>

<script>
// Test functions for analytics
function testProductView() {
    showTestResult('Testing product view tracking...');
    
    if (window.NutrifyAnalytics) {
        window.NutrifyAnalytics.trackEvent('product_interaction', {
            interaction_type: 'view',
            product_id: 999,
            product_name: 'Test Product'
        });
        showTestResult('✅ Product view tracked successfully!', 'success');
    } else {
        showTestResult('❌ Analytics not loaded', 'error');
    }
}

function testAddToCart() {
    showTestResult('Testing add to cart tracking...');
    
    if (window.NutrifyAnalytics) {
        window.NutrifyAnalytics.trackEvent('product_interaction', {
            interaction_type: 'add_to_cart',
            product_id: 999,
            product_name: 'Test Product',
            price: 99.99
        });
        showTestResult('✅ Add to cart tracked successfully!', 'success');
    } else {
        showTestResult('❌ Analytics not loaded', 'error');
    }
}

function testSearch() {
    showTestResult('Testing search tracking...');
    
    if (window.NutrifyAnalytics) {
        window.NutrifyAnalytics.trackEvent('search', {
            search_query: 'test search query',
            results_count: 5,
            search_type: 'product'
        });
        showTestResult('✅ Search tracked successfully!', 'success');
    } else {
        showTestResult('❌ Analytics not loaded', 'error');
    }
}

function testCustomAction() {
    showTestResult('Testing custom action tracking...');
    
    if (window.NutrifyAnalytics) {
        window.NutrifyAnalytics.trackEvent('test_action', {
            test_data: 'This is a test action',
            timestamp: new Date().toISOString()
        });
        showTestResult('✅ Custom action tracked successfully!', 'success');
    } else {
        showTestResult('❌ Analytics not loaded', 'error');
    }
}

function testButtonClick() {
    showTestResult('Testing button click tracking...');
    
    if (window.NutrifyAnalytics) {
        window.NutrifyAnalytics.trackEvent('click', {
            element_type: 'button',
            element_text: 'Test Button Click',
            page_url: window.location.pathname
        });
        showTestResult('✅ Button click tracked successfully!', 'success');
    } else {
        showTestResult('❌ Analytics not loaded', 'error');
    }
}

function showTestResult(message, type = 'info') {
    const resultsDiv = document.getElementById('test-results');
    const outputDiv = document.getElementById('test-output');
    
    resultsDiv.style.display = 'block';
    
    const timestamp = new Date().toLocaleTimeString();
    const className = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
    
    outputDiv.innerHTML += `<p class="${className}">[${timestamp}] ${message}</p>`;
    outputDiv.scrollTop = outputDiv.scrollHeight;
}

// Test analytics initialization on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.NutrifyAnalytics) {
        showTestResult('✅ Analytics system loaded successfully!', 'success');
        showTestResult('📊 Visitor ID: ' + window.NutrifyAnalytics.visitorId, 'info');
        showTestResult('👤 Customer ID: ' + (window.NutrifyAnalytics.customerId || 'Guest'), 'info');
    } else {
        showTestResult('❌ Analytics system failed to load', 'error');
    }
});
</script>

</body>
</html>

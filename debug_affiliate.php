<?php
// Debug affiliate page issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Debugging Affiliate Page Issues</h2>";

// Test 1: Check if file exists and is readable
echo "<h3>1. File Existence Check</h3>";
if (file_exists('affiliate-contact.php')) {
    echo "✅ affiliate-contact.php exists<br>";
    echo "📁 File size: " . filesize('affiliate-contact.php') . " bytes<br>";
    echo "🔒 File permissions: " . substr(sprintf('%o', fileperms('affiliate-contact.php')), -4) . "<br>";
} else {
    echo "❌ affiliate-contact.php NOT found<br>";
}

// Test 2: Check for PHP syntax errors
echo "<h3>2. PHP Syntax Check</h3>";
$output = [];
$return_var = 0;
exec('php -l affiliate-contact.php 2>&1', $output, $return_var);

if ($return_var === 0) {
    echo "✅ No PHP syntax errors found<br>";
} else {
    echo "❌ PHP syntax errors found:<br>";
    foreach ($output as $line) {
        echo "<span style='color: red;'>" . htmlspecialchars($line) . "</span><br>";
    }
}

// Test 3: Check database connection
echo "<h3>3. Database Connection Test</h3>";
try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    if ($mysqli) {
        echo "✅ Database connection successful<br>";
        
        // Check affiliate table
        $result = $mysqli->query("SHOW TABLES LIKE 'affiliate_applications'");
        if ($result && $result->num_rows > 0) {
            echo "✅ affiliate_applications table exists<br>";
        } else {
            echo "❌ affiliate_applications table missing<br>";
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 4: Check required includes
echo "<h3>4. Required Files Check</h3>";
$required_files = [
    'components/header.php',
    'components/footer.php',
    'css/style.css',
    'css/bootstrap.min.css'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 5: Try to include the problematic file with error catching
echo "<h3>5. Include Test with Error Catching</h3>";
ob_start();
try {
    // Capture any output or errors
    $error_occurred = false;
    
    // Start output buffering to catch any output
    ob_start();
    
    // Try to include just the PHP part
    echo "Testing PHP execution...<br>";
    
    // Test session start
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
        echo "✅ Session started successfully<br>";
    } else {
        echo "✅ Session already active<br>";
    }
    
    $content = ob_get_clean();
    echo $content;
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Error during include: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 6: Create a minimal test version
echo "<h3>6. Creating Minimal Test Version</h3>";

$minimal_content = '<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Affiliate Test</title>
</head>
<body>
    <h1>Affiliate Page Test</h1>
    <p>If you can see this, the basic PHP is working.</p>
    <p>Session ID: ' . session_id() . '</p>
    <p>Current time: ' . date('Y-m-d H:i:s') . '</p>
</body>
</html>';

file_put_contents('affiliate-test-minimal.php', $minimal_content);
echo "✅ Created minimal test file: <a href='affiliate-test-minimal.php'>affiliate-test-minimal.php</a><br>";

// Test 7: Check PHP error log
echo "<h3>7. PHP Error Log Check</h3>";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    echo "📋 Error log location: $error_log<br>";
    $recent_errors = tail($error_log, 10);
    if ($recent_errors) {
        echo "<strong>Recent errors:</strong><br>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: auto;'>";
        echo htmlspecialchars($recent_errors);
        echo "</pre>";
    }
} else {
    echo "ℹ️ No error log found or configured<br>";
}

// Test 8: Browser debugging tips
echo "<h3>8. Browser Debugging Tips</h3>";
echo "<ol>";
echo "<li><strong>Open browser developer tools</strong> (F12)</li>";
echo "<li><strong>Go to Console tab</strong> - look for JavaScript errors</li>";
echo "<li><strong>Go to Network tab</strong> - refresh page and check if affiliate-contact.php returns 200 or error</li>";
echo "<li><strong>Check Response</strong> - see if there's any content or error message</li>";
echo "</ol>";

echo "<h3>🔗 Test Links</h3>";
echo "<a href='affiliate-test-minimal.php' target='_blank'>🧪 Test Minimal Version</a><br>";
echo "<a href='affiliate-contact.php' target='_blank'>🎯 Try Original Affiliate Page</a><br>";
echo "<a href='contact.php' target='_blank'>📞 Test Working Contact Page</a><br>";

function tail($filename, $lines = 10) {
    if (!file_exists($filename)) return false;
    
    $file = file($filename);
    if (count($file) < $lines) {
        return implode('', $file);
    }
    
    return implode('', array_slice($file, -$lines));
}
?>

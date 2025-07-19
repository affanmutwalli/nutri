<?php
// Simple test file to check if affiliate page works
echo "Testing affiliate page...<br>";

// Check if the file exists
if (file_exists('affiliate-contact.php')) {
    echo "✅ affiliate-contact.php file exists<br>";
} else {
    echo "❌ affiliate-contact.php file NOT found<br>";
}

// Check if process file exists
if (file_exists('process_affiliate_contact.php')) {
    echo "✅ process_affiliate_contact.php file exists<br>";
} else {
    echo "❌ process_affiliate_contact.php file NOT found<br>";
}

// Check if admin file exists
if (file_exists('cms/affiliate_applications.php')) {
    echo "✅ cms/affiliate_applications.php file exists<br>";
} else {
    echo "❌ cms/affiliate_applications.php file NOT found<br>";
}

// Test database connection
try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    if ($mysqli) {
        echo "✅ Database connection successful<br>";
        
        // Check if affiliate table exists
        $result = $mysqli->query("SHOW TABLES LIKE 'affiliate_applications'");
        if ($result && $result->num_rows > 0) {
            echo "✅ affiliate_applications table exists<br>";
        } else {
            echo "❌ affiliate_applications table NOT found<br>";
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Test Links:</strong><br>";
echo "<a href='affiliate-contact.php'>🔗 Test Affiliate Contact Page</a><br>";
echo "<a href='cms/affiliate_applications.php'>🔗 Test Admin Panel</a><br>";
echo "<a href='contact.php'>🔗 Test Regular Contact Page</a><br>";

echo "<br><strong>If affiliate page is blank, check:</strong><br>";
echo "1. PHP error logs<br>";
echo "2. Browser developer console<br>";
echo "3. Make sure Laragon is running<br>";
echo "4. Try refreshing the page<br>";
?>

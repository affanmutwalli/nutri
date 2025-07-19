<?php
/**
 * Simple database connection test
 */

echo "<h2>Database Connection Test</h2>";

try {
    // Test direct connection
    $mysqli = new mysqli("localhost", "root", "", "my_nutrify_db");
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Direct database connection successful!<br>";
    
    // Test table existence
    $tables = ['enhanced_coupons', 'customer_points', 'points_transactions', 'rewards_catalog'];
    
    foreach ($tables as $table) {
        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' missing<br>";
        }
    }
    
    // Create tables if they don't exist
    echo "<h3>Creating missing tables...</h3>";
    
    // Create enhanced_coupons table
    $createEnhancedCoupons = "
    CREATE TABLE IF NOT EXISTS `enhanced_coupons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `coupon_code` varchar(50) NOT NULL UNIQUE,
        `coupon_name` varchar(255) NOT NULL,
        `description` text,
        `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
        `discount_value` decimal(10,2) NOT NULL,
        `max_discount_amount` decimal(10,2) DEFAULT NULL,
        `minimum_order_amount` decimal(10,2) DEFAULT 0,
        `usage_limit_total` int(11) DEFAULT NULL,
        `usage_limit_per_customer` int(11) DEFAULT 1,
        `current_usage_count` int(11) DEFAULT 0,
        `customer_type` enum('all','new','existing') DEFAULT 'all',
        `is_active` tinyint(1) DEFAULT 1,
        `is_reward_coupon` tinyint(1) DEFAULT 0,
        `valid_from` datetime NOT NULL,
        `valid_until` datetime NOT NULL,
        `created_by` varchar(100) DEFAULT 'admin',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_coupon_code` (`coupon_code`),
        KEY `idx_active_valid` (`is_active`, `valid_from`, `valid_until`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($mysqli->query($createEnhancedCoupons)) {
        echo "✅ enhanced_coupons table created/verified<br>";
    } else {
        echo "❌ Error creating enhanced_coupons: " . $mysqli->error . "<br>";
    }
    
    // Insert sample coupons
    $result = $mysqli->query("SELECT COUNT(*) as count FROM enhanced_coupons");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        echo "<h3>Inserting sample coupons...</h3>";
        
        $sampleCoupons = [
            ['WELCOME10', 'Welcome 10% Off', 'Get 10% off on your first order', 'percentage', 10.00, 100.00, 500.00, NULL, 1, 'new'],
            ['SAVE50', 'Save ₹50', 'Flat ₹50 off on orders above ₹1000', 'fixed', 50.00, NULL, 1000.00, 100, 1, 'all'],
            ['FLAT100', 'Flat ₹100 Off', 'Get ₹100 off on orders above ₹2000', 'fixed', 100.00, NULL, 2000.00, 50, 1, 'all']
        ];
        
        $stmt = $mysqli->prepare("
            INSERT INTO enhanced_coupons 
            (coupon_code, coupon_name, description, discount_type, discount_value, max_discount_amount, minimum_order_amount, usage_limit_total, usage_limit_per_customer, customer_type, valid_from, valid_until) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))
        ");
        
        foreach ($sampleCoupons as $coupon) {
            $stmt->bind_param("ssssdddiis", ...$coupon);
            if ($stmt->execute()) {
                echo "✅ Created coupon: {$coupon[0]}<br>";
            } else {
                echo "❌ Error creating coupon {$coupon[0]}: " . $mysqli->error . "<br>";
            }
        }
    } else {
        echo "📊 Found $count existing coupons<br>";
    }
    
    // Test coupon query
    echo "<h3>Testing coupon queries...</h3>";
    $result = $mysqli->query("SELECT coupon_code, coupon_name, discount_type, discount_value, minimum_order_amount FROM enhanced_coupons WHERE is_active = 1");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Code</th><th>Name</th><th>Type</th><th>Value</th><th>Min Order</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $discount = $row['discount_type'] === 'fixed' ? '₹' . $row['discount_value'] : $row['discount_value'] . '%';
            echo "<tr>";
            echo "<td>{$row['coupon_code']}</td>";
            echo "<td>{$row['coupon_name']}</td>";
            echo "<td>{$row['discount_type']}</td>";
            echo "<td>$discount</td>";
            echo "<td>₹{$row['minimum_order_amount']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";
echo "<p><strong>Next step:</strong> Test the coupon API at <a href='test_coupon_api.php'>test_coupon_api.php</a></p>";
?>

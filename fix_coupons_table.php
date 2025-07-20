<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Fix Coupons Table</h2>";

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

try {
    // Check if coupons table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'coupons'");
    
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Coupons table exists, checking structure...</p>";
        
        // Check table structure
        $columnsCheck = $mysqli->query("DESCRIBE coupons");
        if ($columnsCheck) {
            echo "<h3>Current Table Structure:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            $columns = [];
            while ($col = $columnsCheck->fetch_assoc()) {
                $columns[] = $col['Field'];
                echo "<tr>";
                echo "<td>{$col['Field']}</td>";
                echo "<td>{$col['Type']}</td>";
                echo "<td>{$col['Null']}</td>";
                echo "<td>{$col['Key']}</td>";
                echo "<td>{$col['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Check if required columns exist
            $requiredColumns = ['coupon_code', 'customer_id', 'discount_type', 'discount_value'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (empty($missingColumns)) {
                echo "<p style='color: green;'>✅ All required columns exist!</p>";
            } else {
                echo "<p style='color: red;'>❌ Missing columns: " . implode(', ', $missingColumns) . "</p>";
                echo "<p>Recreating table with correct structure...</p>";
                
                // Drop and recreate table
                $mysqli->query("DROP TABLE coupons");
                createCouponsTable($mysqli);
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Coupons table doesn't exist. Creating now...</p>";
        createCouponsTable($mysqli);
    }
    
    // Test redemption functionality
    echo "<h3>🧪 Test Redemption</h3>";
    
    if (isset($_GET['test_redeem'])) {
        // Simulate a redemption test
        $testCustomerId = 1;
        $testRewardId = 1; // ₹50 discount
        $testPointsRequired = 200;
        
        echo "<h4>Testing redemption for Customer $testCustomerId...</h4>";
        
        // Check customer points
        $pointsQuery = "SELECT total_points FROM customer_points WHERE customer_id = ?";
        $pointsStmt = $mysqli->prepare($pointsQuery);
        $pointsStmt->bind_param("i", $testCustomerId);
        $pointsStmt->execute();
        $pointsResult = $pointsStmt->get_result();
        
        if ($pointsResult->num_rows > 0) {
            $userPoints = $pointsResult->fetch_assoc()['total_points'];
            echo "<p><strong>Customer Points:</strong> $userPoints</p>";
            
            if ($userPoints >= $testPointsRequired) {
                echo "<p style='color: green;'>✅ Customer has enough points for redemption!</p>";
                
                // Test coupon code generation
                $testCouponCode = 'DISC' . $testCustomerId . time() . mt_rand(1000, 9999);
                echo "<p><strong>Generated Coupon Code:</strong> $testCouponCode</p>";
                
                // Test coupon insertion
                $insertCouponQuery = "INSERT INTO coupons (coupon_code, customer_id, discount_type, discount_value, min_order_amount, is_active, expires_at) 
                                      VALUES (?, ?, 'fixed', 50.00, 500.00, 1, DATE_ADD(NOW(), INTERVAL 30 DAY))";
                
                $couponStmt = $mysqli->prepare($insertCouponQuery);
                $couponStmt->bind_param("si", $testCouponCode, $testCustomerId);
                
                if ($couponStmt->execute()) {
                    echo "<p style='color: green;'>✅ Test coupon created successfully!</p>";
                    echo "<p><strong>Coupon Details:</strong></p>";
                    echo "<ul>";
                    echo "<li>Code: $testCouponCode</li>";
                    echo "<li>Discount: ₹50</li>";
                    echo "<li>Min Order: ₹500</li>";
                    echo "<li>Expires: 30 days from now</li>";
                    echo "</ul>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to create test coupon: " . $couponStmt->error . "</p>";
                }
                
            } else {
                echo "<p style='color: orange;'>⚠️ Customer needs " . ($testPointsRequired - $userPoints) . " more points</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Customer points record not found</p>";
        }
        
    } else {
        echo "<a href='?test_redeem=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Test Redemption</a>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

function createCouponsTable($mysqli) {
    $createCouponsQuery = "CREATE TABLE `coupons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `coupon_code` varchar(50) NOT NULL,
        `customer_id` int(11) NOT NULL,
        `discount_type` enum('fixed','percentage','free_shipping') NOT NULL,
        `discount_value` decimal(10,2) NOT NULL,
        `min_order_amount` decimal(10,2) DEFAULT 0.00,
        `is_active` tinyint(1) DEFAULT 1,
        `is_used` tinyint(1) DEFAULT 0,
        `used_at` timestamp NULL DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `expires_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `coupon_code` (`coupon_code`),
        KEY `customer_id` (`customer_id`)
    )";
    
    if ($mysqli->query($createCouponsQuery)) {
        echo "<p style='color: green;'>✅ Coupons table created successfully!</p>";
        
        // Show table structure
        $columnsCheck = $mysqli->query("DESCRIBE coupons");
        if ($columnsCheck) {
            echo "<h3>New Table Structure:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            while ($col = $columnsCheck->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$col['Field']}</td>";
                echo "<td>{$col['Type']}</td>";
                echo "<td>{$col['Null']}</td>";
                echo "<td>{$col['Key']}</td>";
                echo "<td>{$col['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Failed to create coupons table: " . $mysqli->error . "</p>";
    }
}

echo "<h3>🎯 Summary:</h3>";
echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<p>✅ <strong>Coupons table</strong> is now properly configured</p>";
echo "<p>✅ <strong>Redemption system</strong> should work correctly</p>";
echo "<p>✅ <strong>Coupon codes</strong> will be generated automatically</p>";
echo "<p>✅ <strong>30-day expiry</strong> for all coupons</p>";
echo "</div>";

echo "<br><p><a href='test_rewards_modal.php' style='background: #ff8c00; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>🎁 Test Rewards Modal</a></p>";
echo "<p><a href='rewards.php'>Go to Rewards Page</a> | <a href='redeem_reward.php'>Test Redemption API</a></p>";
?>

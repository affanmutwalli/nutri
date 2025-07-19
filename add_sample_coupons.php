<?php
/**
 * Add the missing sample coupons to the database
 */

echo "<h2>Adding Sample Coupons</h2>";

try {
    $mysqli = new mysqli("localhost", "root", "", "my_nutrify_db");
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Database connection successful!<br>";
    
    // Sample coupons to add
    $sampleCoupons = [
        ['WELCOME10', 'Welcome 10% Off', 'Get 10% off on your first order', 'percentage', 10.00, 100.00, 500.00, NULL, 1, 'new'],
        ['SAVE50', 'Save ₹50', 'Flat ₹50 off on orders above ₹1000', 'fixed', 50.00, NULL, 1000.00, 100, 1, 'all'],
        ['FLAT100', 'Flat ₹100 Off', 'Get ₹100 off on orders above ₹2000', 'fixed', 100.00, NULL, 2000.00, 50, 1, 'all']
    ];
    
    echo "<h3>Adding missing coupons...</h3>";
    
    $stmt = $mysqli->prepare("
        INSERT IGNORE INTO enhanced_coupons 
        (coupon_code, coupon_name, description, discount_type, discount_value, max_discount_amount, minimum_order_amount, usage_limit_total, usage_limit_per_customer, customer_type, valid_from, valid_until) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))
    ");
    
    foreach ($sampleCoupons as $coupon) {
        $stmt->bind_param("ssssdddiis", ...$coupon);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "✅ Added coupon: {$coupon[0]} - {$coupon[1]}<br>";
            } else {
                echo "ℹ️ Coupon {$coupon[0]} already exists<br>";
            }
        } else {
            echo "❌ Error adding coupon {$coupon[0]}: " . $mysqli->error . "<br>";
        }
    }
    
    echo "<h3>Current active coupons:</h3>";
    
    $result = $mysqli->query("SELECT coupon_code, coupon_name, discount_type, discount_value, minimum_order_amount FROM enhanced_coupons WHERE is_active = 1 ORDER BY coupon_code");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>Code</th><th>Name</th><th>Type</th><th>Value</th><th>Min Order</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $discount = $row['discount_type'] === 'fixed' ? '₹' . $row['discount_value'] : $row['discount_value'] . '%';
            echo "<tr>";
            echo "<td><strong>{$row['coupon_code']}</strong></td>";
            echo "<td>{$row['coupon_name']}</td>";
            echo "<td>{$row['discount_type']}</td>";
            echo "<td>$discount</td>";
            echo "<td>₹{$row['minimum_order_amount']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    $mysqli->close();
    
    echo "<hr>";
    echo "<p><strong>✅ Sample coupons added successfully!</strong></p>";
    echo "<p>Now test the API with: <a href='test_coupon_api.php'>test_coupon_api.php</a></p>";
    echo "<p>Or test on checkout: <a href='checkout.php'>checkout.php</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

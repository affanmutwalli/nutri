<?php
/**
 * Test Coupons - Quick diagnostic and fix
 */

include_once 'database/dbconnection.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Coupons</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #ec6504; border-bottom: 3px solid #ec6504; padding-bottom: 10px; }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .btn { background: #ec6504; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f8f9fa; }
    .shine { background: linear-gradient(135deg, #fff5e6 0%, #ffe0b3 100%); }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🧪 Test Coupons & Fix Issues</h1>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<div class='success'>✅ Database connection successful</div>";
    
    if ($_POST['action'] ?? '' === 'add_sample_coupons') {
        echo "<h2>Adding Sample Coupons...</h2>";
        
        // Add IsShining column if it doesn't exist
        $checkColumn = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                       WHERE TABLE_SCHEMA = DATABASE() 
                       AND TABLE_NAME = 'enhanced_coupons' 
                       AND COLUMN_NAME = 'IsShining'";
        
        $result = $mysqli->query($checkColumn);
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            $mysqli->query("ALTER TABLE enhanced_coupons ADD COLUMN IsShining TINYINT(1) DEFAULT 0 COMMENT 'Highlight this coupon in the dropdown'");
            echo "<div class='success'>✅ Added IsShining column</div>";
        }
        
        // Insert sample coupons
        $sampleCoupons = [
            ['WELCOME10', 'Welcome Offer', 'Get 10% off on your first order', 'percentage', 10.00, 100.00, 50.00, 1, 1],
            ['SAVE20', 'Mega Savings', 'Flat 20% off on orders above ₹500', 'percentage', 20.00, 500.00, 100.00, 1, 1],
            ['FLAT50', 'Flat Discount', 'Flat ₹50 off on orders above ₹200', 'fixed', 50.00, 200.00, 0.00, 1, 0],
            ['BIGDEAL', 'Big Deal Special', 'Get 25% off on orders above ₹1000', 'percentage', 25.00, 1000.00, 200.00, 1, 1],
            ['QUICK15', 'Quick Discount', 'Instant 15% off on orders above ₹300', 'percentage', 15.00, 300.00, 75.00, 1, 0]
        ];
        
        $insertQuery = "INSERT IGNORE INTO enhanced_coupons (CouponCode, CouponName, Description, DiscountType, DiscountValue, MinimumOrderValue, MaximumDiscountAmount, IsActive, IsShining) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);
        
        $addedCount = 0;
        foreach ($sampleCoupons as $coupon) {
            $stmt->bind_param("ssssdddii", ...$coupon);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $addedCount++;
                }
            }
        }
        
        echo "<div class='success'>✅ Added $addedCount new sample coupons</div>";
    }
    
    // Check current coupons
    echo "<h2>📋 Current Coupons in Database</h2>";
    $result = $mysqli->query("SELECT COUNT(*) as total FROM enhanced_coupons");
    $count = $result->fetch_assoc();
    echo "<div class='info'>Total coupons in database: <strong>" . $count['total'] . "</strong></div>";
    
    if ($count['total'] == 0) {
        echo "<div class='error'>❌ No coupons found! This is why the dropdown is empty.</div>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='add_sample_coupons'>";
        echo "<button type='submit' class='btn'>🎁 Add Sample Coupons</button>";
        echo "</form>";
    } else {
        // Show all coupons
        $allCoupons = $mysqli->query("SELECT *, COALESCE(IsShining, 0) as IsShining FROM enhanced_coupons ORDER BY IsShining DESC, CouponCode");
        
        if ($allCoupons && $allCoupons->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Code</th><th>Name</th><th>Type</th><th>Value</th><th>Min Order</th><th>Max Discount</th><th>Active</th><th>Shining</th><th>Actions</th></tr>";
            
            while ($row = $allCoupons->fetch_assoc()) {
                $shineClass = $row['IsShining'] ? 'shine' : '';
                $statusBadge = $row['IsActive'] ? '<span style="color: green;">✅ Active</span>' : '<span style="color: red;">❌ Inactive</span>';
                $shineBadge = $row['IsShining'] ? '<span style="color: orange;">✨ Shining</span>' : '<span style="color: gray;">Regular</span>';
                
                echo "<tr class='$shineClass'>";
                echo "<td><strong>" . htmlspecialchars($row['CouponCode']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['CouponName'] ?? $row['CouponCode']) . "</td>";
                echo "<td>" . htmlspecialchars($row['DiscountType'] ?? 'percentage') . "</td>";
                echo "<td>" . $row['DiscountValue'] . "</td>";
                echo "<td>₹" . ($row['MinimumOrderValue'] ?? 0) . "</td>";
                echo "<td>₹" . ($row['MaximumDiscountAmount'] ?? 0) . "</td>";
                echo "<td>$statusBadge</td>";
                echo "<td>$shineBadge</td>";
                echo "<td>";
                
                // Get primary key
                $pkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_SCHEMA = DATABASE() 
                           AND TABLE_NAME = 'enhanced_coupons' 
                           AND COLUMN_KEY = 'PRI'";
                $pkResult = $mysqli->query($pkQuery);
                $pkColumn = 'id';
                if ($pkResult && $pkRow = $pkResult->fetch_assoc()) {
                    $pkColumn = $pkRow['COLUMN_NAME'];
                }
                
                echo "<button onclick=\"toggleShine(" . $row[$pkColumn] . ")\" class='btn' style='font-size: 12px; padding: 5px 10px;'>Toggle Shine</button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Test API call
        echo "<h2>🧪 Test API Call</h2>";
        echo "<div class='info'>";
        echo "<p>Test the coupon API with different order amounts:</p>";
        echo "<button onclick=\"testAPI(100)\" class='btn'>Test ₹100 Order</button>";
        echo "<button onclick=\"testAPI(300)\" class='btn'>Test ₹300 Order</button>";
        echo "<button onclick=\"testAPI(500)\" class='btn'>Test ₹500 Order</button>";
        echo "<button onclick=\"testAPI(1000)\" class='btn'>Test ₹1000 Order</button>";
        echo "</div>";
        echo "<div id='api-results' style='margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; display: none;'></div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Test Failed</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div>";

// Add JavaScript
echo "<script>";
echo "function toggleShine(couponId) {";
echo "  fetch('exe_files/toggle_coupon_shine.php', {";
echo "    method: 'POST',";
echo "    headers: { 'Content-Type': 'application/json' },";
echo "    body: JSON.stringify({ coupon_id: couponId })";
echo "  })";
echo "  .then(response => response.json())";
echo "  .then(data => {";
echo "    if (data.response === 'S') {";
echo "      location.reload();";
echo "    } else {";
echo "      alert('Error: ' + data.message);";
echo "    }";
echo "  });";
echo "}";

echo "function testAPI(orderAmount) {";
echo "  const resultsDiv = document.getElementById('api-results');";
echo "  resultsDiv.style.display = 'block';";
echo "  resultsDiv.innerHTML = '<i class=\"fa fa-spinner fa-spin\"></i> Testing API with order amount: ₹' + orderAmount + '...';";
echo "  ";
echo "  fetch('exe_files/fetch_available_coupons.php', {";
echo "    method: 'POST',";
echo "    headers: { 'Content-Type': 'application/json' },";
echo "    body: JSON.stringify({ order_amount: orderAmount })";
echo "  })";
echo "  .then(response => response.json())";
echo "  .then(data => {";
echo "    console.log('API Response:', data);";
echo "    let html = '<h4>API Test Results for ₹' + orderAmount + ' order:</h4>';";
echo "    if (data.response === 'S') {";
echo "      html += '<p><strong>Status:</strong> Success</p>';";
echo "      html += '<p><strong>Total Coupons Found:</strong> ' + data.total_coupons + '</p>';";
echo "      if (data.coupons.length > 0) {";
echo "        html += '<ul>';";
echo "        data.coupons.forEach(coupon => {";
echo "          html += '<li><strong>' + coupon.code + '</strong> - ' + coupon.discount_display + ' (Save: ₹' + coupon.potential_discount + ')' + (coupon.is_shining ? ' ✨' : '') + '</li>';";
echo "        });";
echo "        html += '</ul>';";
echo "      } else {";
echo "        html += '<p style=\"color: red;\">No applicable coupons found for this order amount.</p>';";
echo "      }";
echo "    } else {";
echo "      html += '<p style=\"color: red;\"><strong>Error:</strong> ' + data.message + '</p>';";
echo "    }";
echo "    resultsDiv.innerHTML = html;";
echo "  })";
echo "  .catch(error => {";
echo "    console.error('API Error:', error);";
echo "    resultsDiv.innerHTML = '<p style=\"color: red;\">API call failed: ' + error.message + '</p>';";
echo "  });";
echo "}";
echo "</script>";

echo "</body></html>";
?>

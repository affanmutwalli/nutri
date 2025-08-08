<?php
/**
 * Enhanced Coupons Setup Script
 * Sets up the enhanced coupons table with shining feature
 */

include_once 'database/dbconnection.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Enhanced Coupons Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #ec6504; border-bottom: 3px solid #ec6504; padding-bottom: 10px; }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .btn { background: #ec6504; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
    .btn:hover { background: #d55a04; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f8f9fa; }
    .shine { background: linear-gradient(135deg, #fff5e6 0%, #ffe0b3 100%); }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🎁 Enhanced Coupons Setup</h1>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<div class='info'>✅ Database connection successful</div>";
    
    if ($_POST['action'] ?? '' === 'setup') {
        echo "<h2>Setting up Enhanced Coupons...</h2>";
        
        // Read and execute SQL file
        $sqlFile = 'database/enhanced_coupons_schema.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found: $sqlFile");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            try {
                if ($mysqli->query($statement)) {
                    $successCount++;
                } else {
                    echo "<div class='warning'>⚠️ Statement skipped (might already exist): " . substr($statement, 0, 50) . "...</div>";
                }
            } catch (Exception $e) {
                $errorCount++;
                echo "<div class='error'>❌ Error executing statement: " . $e->getMessage() . "</div>";
            }
        }
        
        echo "<div class='success'>✅ Setup completed! Executed $successCount statements successfully.</div>";
        
        // Display current coupons
        echo "<h3>📋 Current Enhanced Coupons</h3>";
        $result = $mysqli->query("SELECT * FROM enhanced_coupons ORDER BY IsShining DESC, CouponCode");
        
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Code</th><th>Name</th><th>Discount</th><th>Min Order</th><th>Status</th><th>Shining</th><th>Actions</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                $shineClass = $row['IsShining'] ? 'shine' : '';
                $statusBadge = $row['IsActive'] ? '<span style="color: green;">✅ Active</span>' : '<span style="color: red;">❌ Inactive</span>';
                $shineBadge = $row['IsShining'] ? '<span style="color: orange;">✨ Shining</span>' : '<span style="color: gray;">Regular</span>';
                
                $discountText = $row['DiscountType'] === 'percentage' 
                    ? $row['DiscountValue'] . '% OFF' 
                    : '₹' . $row['DiscountValue'] . ' OFF';
                
                echo "<tr class='$shineClass'>";
                echo "<td><strong>" . htmlspecialchars($row['CouponCode']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['CouponName']) . "</td>";
                echo "<td>$discountText</td>";
                echo "<td>₹" . $row['MinimumOrderValue'] . "</td>";
                echo "<td>$statusBadge</td>";
                echo "<td>$shineBadge</td>";
                echo "<td>";
                echo "<button onclick=\"toggleShine(" . $row['CouponId'] . ")\" class='btn' style='font-size: 12px; padding: 5px 10px;'>Toggle Shine</button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<div class='success'>";
        echo "<h3>🎉 Enhanced Coupons Setup Complete!</h3>";
        echo "<p><strong>Features Added:</strong></p>";
        echo "<ul>";
        echo "<li>✨ Shining coupons with special animations</li>";
        echo "<li>🎯 Smart coupon suggestions based on order value</li>";
        echo "<li>📱 Beautiful dropdown interface</li>";
        echo "<li>⚡ Auto-apply functionality</li>";
        echo "<li>🔧 CMS toggle for shining feature</li>";
        echo "</ul>";
        echo "</div>";
        
    } else {
        // Show setup form
        echo "<div class='info'>";
        echo "<h3>📋 Enhanced Coupons Features</h3>";
        echo "<p>This will set up an enhanced coupon system with the following features:</p>";
        echo "<ul>";
        echo "<li><strong>✨ Shining Coupons:</strong> Highlight special offers with animations</li>";
        echo "<li><strong>🎯 Smart Suggestions:</strong> Show applicable coupons based on order value</li>";
        echo "<li><strong>📱 Beautiful UI:</strong> Dropdown with attractive coupon cards</li>";
        echo "<li><strong>⚡ Auto-Apply:</strong> One-click coupon application</li>";
        echo "<li><strong>🔧 CMS Control:</strong> Toggle shining feature from admin panel</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='setup'>";
        echo "<button type='submit' class='btn'>🚀 Setup Enhanced Coupons</button>";
        echo "</form>";
        
        echo "<div class='warning'>";
        echo "<h3>⚠️ What will be created:</h3>";
        echo "<ul>";
        echo "<li>enhanced_coupons table with shining feature</li>";
        echo "<li>Sample coupon data with some shining coupons</li>";
        echo "<li>Database indexes for performance</li>";
        echo "<li>Management views and procedures</li>";
        echo "</ul>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Setup Failed</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div>";

// Add JavaScript for toggle functionality
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
echo "</script>";

echo "</body></html>";
?>

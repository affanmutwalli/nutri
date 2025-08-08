<?php
/**
 * Add Shining Feature to Existing Enhanced Coupons Table
 * This script only adds the IsShining column without altering anything else
 */

include_once 'database/dbconnection.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Add Shining Feature</title>";
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
echo "<h1>✨ Add Shining Feature to Existing Coupons</h1>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<div class='info'>✅ Database connection successful</div>";
    
    if ($_POST['action'] ?? '' === 'add_shining') {
        echo "<h2>Adding Shining Feature...</h2>";
        
        // Check if enhanced_coupons table exists
        $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
        if ($tableCheck->num_rows == 0) {
            throw new Exception("enhanced_coupons table not found. Please make sure your existing coupon table is named 'enhanced_coupons'");
        }
        
        echo "<div class='success'>✅ Found enhanced_coupons table</div>";
        
        // Check if IsShining column already exists
        $columnCheck = $mysqli->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                                      WHERE TABLE_SCHEMA = DATABASE() 
                                      AND TABLE_NAME = 'enhanced_coupons' 
                                      AND COLUMN_NAME = 'IsShining'");
        
        $row = $columnCheck->fetch_assoc();
        
        if ($row['count'] > 0) {
            echo "<div class='warning'>⚠️ IsShining column already exists</div>";
        } else {
            // Add IsShining column
            $addColumn = "ALTER TABLE enhanced_coupons ADD COLUMN IsShining TINYINT(1) DEFAULT 0 COMMENT 'Highlight this coupon in the dropdown'";
            
            if ($mysqli->query($addColumn)) {
                echo "<div class='success'>✅ Added IsShining column successfully</div>";
            } else {
                throw new Exception("Failed to add IsShining column: " . $mysqli->error);
            }
        }
        
        // Add index for better performance
        $indexCheck = $mysqli->query("SHOW INDEX FROM enhanced_coupons WHERE Key_name = 'idx_shining_coupons'");
        if ($indexCheck->num_rows == 0) {
            $addIndex = "CREATE INDEX idx_shining_coupons ON enhanced_coupons(IsShining)";
            if ($mysqli->query($addIndex)) {
                echo "<div class='success'>✅ Added index for IsShining column</div>";
            } else {
                echo "<div class='warning'>⚠️ Could not add index (this is optional): " . $mysqli->error . "</div>";
            }
        } else {
            echo "<div class='warning'>⚠️ Index for IsShining already exists</div>";
        }
        
        // Make a few sample coupons shine
        $sampleShine = "UPDATE enhanced_coupons SET IsShining = 1 LIMIT 2";
        if ($mysqli->query($sampleShine)) {
            echo "<div class='success'>✅ Made 2 sample coupons shine for demonstration</div>";
        }
        
        // Get the primary key column name
        $pkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'enhanced_coupons'
                   AND COLUMN_KEY = 'PRI'";
        $pkResult = $mysqli->query($pkQuery);
        $pkColumn = 'id'; // default fallback
        if ($pkResult && $pkRow = $pkResult->fetch_assoc()) {
            $pkColumn = $pkRow['COLUMN_NAME'];
        }

        echo "<div class='info'>✅ Detected primary key column: $pkColumn</div>";

        // Display current coupons
        echo "<h3>📋 Current Coupons with Shining Feature</h3>";
        $result = $mysqli->query("SELECT $pkColumn, CouponCode,
                                 COALESCE(CouponName, CouponCode) as CouponName,
                                 DiscountValue,
                                 COALESCE(DiscountType, 'percentage') as DiscountType,
                                 COALESCE(MinimumOrderValue, 0) as MinimumOrderValue,
                                 COALESCE(IsActive, 1) as IsActive,
                                 COALESCE(IsShining, 0) as IsShining
                                 FROM enhanced_coupons ORDER BY IsShining DESC, CouponCode");
        
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
                echo "<button onclick=\"toggleShine(" . $row[$pkColumn] . ")\" class='btn' style='font-size: 12px; padding: 5px 10px;'>Toggle Shine</button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<div class='success'>";
        echo "<h3>🎉 Shining Feature Added Successfully!</h3>";
        echo "<p><strong>What was added:</strong></p>";
        echo "<ul>";
        echo "<li>✨ IsShining column to your existing table</li>";
        echo "<li>🔍 Database index for performance</li>";
        echo "<li>🎯 Sample shining coupons for testing</li>";
        echo "</ul>";
        echo "<p><strong>Your checkout page now has:</strong></p>";
        echo "<ul>";
        echo "<li>📱 Beautiful coupon dropdown</li>";
        echo "<li>✨ Animated shining coupons</li>";
        echo "<li>🎯 Smart filtering by order value</li>";
        echo "<li>⚡ One-click coupon application</li>";
        echo "</ul>";
        echo "</div>";
        
    } else {
        // Show setup form
        echo "<div class='info'>";
        echo "<h3>📋 About This Update</h3>";
        echo "<p>This will add the shining feature to your existing enhanced_coupons table without changing any existing data or functionality.</p>";
        echo "<p><strong>What will be added:</strong></p>";
        echo "<ul>";
        echo "<li><strong>IsShining column:</strong> TINYINT(1) DEFAULT 0</li>";
        echo "<li><strong>Database index:</strong> For better performance</li>";
        echo "<li><strong>Sample data:</strong> Make 2 coupons shine for testing</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div class='warning'>";
        echo "<h3>⚠️ Requirements</h3>";
        echo "<ul>";
        echo "<li>Your existing coupon table must be named 'enhanced_coupons'</li>";
        echo "<li>This will NOT modify any existing data</li>";
        echo "<li>This will NOT change any existing functionality</li>";
        echo "<li>You can toggle shining on/off for any coupon anytime</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='add_shining'>";
        echo "<button type='submit' class='btn'>✨ Add Shining Feature</button>";
        echo "</form>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Update Failed</h3>";
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

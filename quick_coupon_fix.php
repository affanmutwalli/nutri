<?php
/**
 * Quick Coupon Fix - Diagnose and fix coupon issues
 */

include_once 'database/dbconnection.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Quick Coupon Fix</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #ec6504; border-bottom: 3px solid #ec6504; padding-bottom: 10px; }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .btn { background: #ec6504; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
    .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔧 Quick Coupon Fix</h1>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<div class='success'>✅ Database connection successful</div>";
    
    // Check if enhanced_coupons table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
    if ($tableCheck->num_rows == 0) {
        echo "<div class='error'>❌ enhanced_coupons table not found!</div>";
        echo "<div class='info'>Please run your enhanced_coupons.sql file first.</div>";
        exit();
    }
    
    echo "<div class='success'>✅ enhanced_coupons table exists</div>";
    
    // Check table structure
    echo "<h2>📋 Table Structure</h2>";
    $structure = $mysqli->query("DESCRIBE enhanced_coupons");
    $columns = [];
    while ($row = $structure->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    echo "<div class='code'>Columns: " . implode(', ', $columns) . "</div>";
    
    // Check if IsShining column exists
    if (!in_array('IsShining', $columns)) {
        echo "<div class='info'>Adding IsShining column...</div>";
        $mysqli->query("ALTER TABLE enhanced_coupons ADD COLUMN IsShining TINYINT(1) DEFAULT 0");
        echo "<div class='success'>✅ IsShining column added</div>";
    }
    
    // Count existing coupons
    $countResult = $mysqli->query("SELECT COUNT(*) as total FROM enhanced_coupons");
    $count = $countResult->fetch_assoc()['total'];
    
    echo "<h2>📊 Current Status</h2>";
    echo "<div class='info'>Total coupons in database: <strong>$count</strong></div>";
    
    if ($_POST['action'] ?? '' === 'add_test_coupons') {
        echo "<h2>Adding Test Coupons...</h2>";
        
        // Add test coupons with very low minimum order values
        $testCoupons = [
            ['WELCOME10', 'Welcome Offer', 'Get 10% off on your first order', 'percentage', 10.00, 50.00, 100.00, 1, 1],
            ['SAVE20', 'Mega Savings', 'Flat 20% off on orders above ₹100', 'percentage', 20.00, 100.00, 200.00, 1, 1],
            ['FLAT50', 'Flat Discount', 'Flat ₹50 off on orders above ₹99', 'fixed', 50.00, 99.00, 0.00, 1, 0],
            ['BIGDEAL', 'Big Deal Special', 'Get 25% off on orders above ₹200', 'percentage', 25.00, 200.00, 500.00, 1, 1],
            ['QUICK15', 'Quick Discount', 'Instant 15% off on orders above ₹150', 'percentage', 15.00, 150.00, 150.00, 1, 0],
            ['TESTCOUPON', 'Test Coupon', 'Test coupon for any order', 'fixed', 10.00, 1.00, 0.00, 1, 1]
        ];
        
        // Detect actual column names
        $columnMap = [];
        foreach ($columns as $col) {
            $colLower = strtolower($col);
            if (in_array($colLower, ['couponcode', 'coupon_code', 'code'])) {
                $columnMap['code'] = $col;
            } elseif (in_array($colLower, ['couponname', 'coupon_name', 'name'])) {
                $columnMap['name'] = $col;
            } elseif (in_array($colLower, ['discounttype', 'discount_type', 'type'])) {
                $columnMap['type'] = $col;
            } elseif (in_array($colLower, ['discountvalue', 'discount_value', 'value', 'discount'])) {
                $columnMap['value'] = $col;
            } elseif (in_array($colLower, ['minimumordervalue', 'minimum_order_value', 'min_order_amount', 'minimum_order_amount', 'min_order'])) {
                $columnMap['min_order'] = $col;
            } elseif (in_array($colLower, ['maximumdiscountamount', 'maximum_discount_amount', 'max_discount_amount', 'max_discount'])) {
                $columnMap['max_discount'] = $col;
            } elseif (in_array($colLower, ['isactive', 'is_active', 'active', 'status'])) {
                $columnMap['active'] = $col;
            } elseif (in_array($colLower, ['description', 'desc'])) {
                $columnMap['description'] = $col;
            }
        }

        echo "<div class='code'>Detected columns: " . print_r($columnMap, true) . "</div>";

        // Build insert query with detected columns
        $insertCols = [];
        $insertVals = [];
        $insertData = [];

        if ($columnMap['code']) { $insertCols[] = $columnMap['code']; $insertVals[] = '?'; }
        if ($columnMap['name']) { $insertCols[] = $columnMap['name']; $insertVals[] = '?'; }
        if ($columnMap['description']) { $insertCols[] = $columnMap['description']; $insertVals[] = '?'; }
        if ($columnMap['type']) { $insertCols[] = $columnMap['type']; $insertVals[] = '?'; }
        if ($columnMap['value']) { $insertCols[] = $columnMap['value']; $insertVals[] = '?'; }
        if ($columnMap['min_order']) { $insertCols[] = $columnMap['min_order']; $insertVals[] = '?'; }
        if ($columnMap['max_discount']) { $insertCols[] = $columnMap['max_discount']; $insertVals[] = '?'; }
        if ($columnMap['active']) { $insertCols[] = $columnMap['active']; $insertVals[] = '?'; }
        if (in_array('IsShining', $columns)) { $insertCols[] = 'IsShining'; $insertVals[] = '?'; }

        $insertQuery = "INSERT IGNORE INTO enhanced_coupons (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $insertVals) . ")";
        echo "<div class='code'>Insert query: $insertQuery</div>";

        $stmt = $mysqli->prepare($insertQuery);
        
        $addedCount = 0;
        foreach ($testCoupons as $coupon) {
            // Prepare data array based on available columns
            $bindData = [];
            $bindTypes = '';

            if ($columnMap['code']) { $bindData[] = $coupon[0]; $bindTypes .= 's'; }
            if ($columnMap['name']) { $bindData[] = $coupon[1]; $bindTypes .= 's'; }
            if ($columnMap['description']) { $bindData[] = $coupon[2]; $bindTypes .= 's'; }
            if ($columnMap['type']) { $bindData[] = $coupon[3]; $bindTypes .= 's'; }
            if ($columnMap['value']) { $bindData[] = $coupon[4]; $bindTypes .= 'd'; }
            if ($columnMap['min_order']) { $bindData[] = $coupon[5]; $bindTypes .= 'd'; }
            if ($columnMap['max_discount']) { $bindData[] = $coupon[6]; $bindTypes .= 'd'; }
            if ($columnMap['active']) { $bindData[] = $coupon[7]; $bindTypes .= 'i'; }
            if (in_array('IsShining', $columns)) { $bindData[] = $coupon[8]; $bindTypes .= 'i'; }

            if (!empty($bindData)) {
                $stmt->bind_param($bindTypes, ...$bindData);
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $addedCount++;
                }
            }
        }
        
        echo "<div class='success'>✅ Added $addedCount new test coupons</div>";
        
        // Update count
        $countResult = $mysqli->query("SELECT COUNT(*) as total FROM enhanced_coupons");
        $count = $countResult->fetch_assoc()['total'];
        echo "<div class='info'>Total coupons now: <strong>$count</strong></div>";
    }
    
    if ($_POST['action'] ?? '' === 'test_api') {
        $testAmount = floatval($_POST['test_amount']);
        echo "<h2>🧪 Testing API with ₹$testAmount order</h2>";
        
        // Simulate the API call
        $currentDate = date('Y-m-d');
        $query = "SELECT 
                    CouponCode, 
                    COALESCE(CouponName, CouponCode) as CouponName, 
                    COALESCE(DiscountType, 'percentage') as DiscountType, 
                    DiscountValue, 
                    COALESCE(MinimumOrderValue, 0) as MinimumOrderValue, 
                    COALESCE(MaximumDiscountAmount, 0) as MaximumDiscountAmount,
                    ExpiryDate,
                    COALESCE(IsShining, 0) as IsShining,
                    COALESCE(Description, CONCAT('Get discount with code ', CouponCode)) as Description
                  FROM enhanced_coupons 
                  WHERE COALESCE(IsActive, 1) = 1 
                  AND (ExpiryDate IS NULL OR ExpiryDate >= ?) 
                  AND COALESCE(MinimumOrderValue, 0) <= ?
                  ORDER BY COALESCE(IsShining, 0) DESC, COALESCE(MinimumOrderValue, 0) ASC";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sd", $currentDate, $testAmount);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<div class='info'>Query found: <strong>" . $result->num_rows . "</strong> applicable coupons</div>";
        
        if ($result->num_rows > 0) {
            echo "<div class='code'>";
            echo "<strong>Applicable Coupons:</strong><br>";
            while ($row = $result->fetch_assoc()) {
                $shineIcon = $row['IsShining'] ? '✨' : '';
                echo "- {$row['CouponCode']} $shineIcon (Min: ₹{$row['MinimumOrderValue']}) - {$row['Description']}<br>";
            }
            echo "</div>";
        } else {
            echo "<div class='error'>❌ No coupons found for ₹$testAmount order</div>";
            
            // Show all coupons with their minimum requirements
            $allCoupons = $mysqli->query("SELECT CouponCode, MinimumOrderValue, IsActive FROM enhanced_coupons ORDER BY MinimumOrderValue");
            if ($allCoupons->num_rows > 0) {
                echo "<div class='info'><strong>All coupons and their minimum order requirements:</strong></div>";
                echo "<div class='code'>";
                while ($row = $allCoupons->fetch_assoc()) {
                    $status = $row['IsActive'] ? 'Active' : 'Inactive';
                    echo "- {$row['CouponCode']}: Min ₹{$row['MinimumOrderValue']} ($status)<br>";
                }
                echo "</div>";
            }
        }
    }
    
    if ($count == 0) {
        echo "<div class='error'>❌ No coupons found! This is why the dropdown is empty.</div>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='add_test_coupons'>";
        echo "<button type='submit' class='btn'>🎁 Add Test Coupons</button>";
        echo "</form>";
    } else {
        echo "<div class='success'>✅ Found $count coupons in database</div>";
        
        // Test API with different amounts
        echo "<h2>🧪 Test API</h2>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='test_api'>";
        echo "<label>Test with order amount: ₹</label>";
        echo "<input type='number' name='test_amount' value='99' min='1' step='1'>";
        echo "<button type='submit' class='btn'>Test API</button>";
        echo "</form>";
        
        echo "<div style='margin: 20px 0;'>";
        echo "<a href='checkout.php' class='btn'>🛒 Go to Checkout</a>";
        echo "<a href='cms/coupon_management.php' class='btn'>⚙️ Manage Coupons</a>";
        echo "</div>";
    }
    
    // Show current coupons
    if ($count > 0) {
        echo "<h2>📋 Current Coupons</h2>";
        $coupons = $mysqli->query("SELECT CouponCode, CouponName, DiscountType, DiscountValue, MinimumOrderValue, IsActive, COALESCE(IsShining, 0) as IsShining FROM enhanced_coupons ORDER BY IsShining DESC, MinimumOrderValue");
        
        echo "<div class='code'>";
        while ($row = $coupons->fetch_assoc()) {
            $shineIcon = $row['IsShining'] ? '✨' : '';
            $statusIcon = $row['IsActive'] ? '✅' : '❌';
            $discountText = $row['DiscountType'] === 'percentage' ? $row['DiscountValue'] . '%' : '₹' . $row['DiscountValue'];
            echo "$statusIcon {$row['CouponCode']} $shineIcon - $discountText OFF (Min: ₹{$row['MinimumOrderValue']})<br>";
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div></body></html>";
?>

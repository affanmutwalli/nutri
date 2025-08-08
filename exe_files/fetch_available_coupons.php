<?php
header("Content-Type: application/json");
session_start();

try {
    include_once '../database/dbconnection.php';
    $obj = new main();
    $conn = $obj->connection();

    if (!$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    echo json_encode(["response" => "E", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Get JSON input data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Get order amount from request
$orderAmount = isset($data['order_amount']) ? floatval($data['order_amount']) : 0;

try {
    // Debug logging
    error_log("=== FETCH COUPONS DEBUG ===");
    error_log("Order amount received: " . $orderAmount);

    // Fetch available coupons based on minimum order value
    $currentDate = date('Y-m-d');

    // First, check if IsShining column exists, if not add it
    $checkColumn = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'enhanced_coupons'
                   AND COLUMN_NAME = 'IsShining'";

    $result = $conn->query($checkColumn);
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        // Add IsShining column if it doesn't exist
        $conn->query("ALTER TABLE enhanced_coupons ADD COLUMN IsShining TINYINT(1) DEFAULT 0 COMMENT 'Highlight this coupon in the dropdown'");
    }

    // First, detect the actual column names in the table
    $columnsQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'enhanced_coupons'";
    $columnsResult = $conn->query($columnsQuery);
    $availableColumns = [];
    while ($row = $columnsResult->fetch_assoc()) {
        $availableColumns[] = $row['COLUMN_NAME'];
    }

    error_log("Available columns: " . implode(', ', $availableColumns));

    // Map common column name variations
    $columnMap = [
        'code' => null,
        'name' => null,
        'type' => null,
        'value' => null,
        'min_order' => null,
        'max_discount' => null,
        'expiry' => null,
        'active' => null,
        'description' => null
    ];

    // Detect column names
    foreach ($availableColumns as $col) {
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
        } elseif (in_array($colLower, ['expirydate', 'expiry_date', 'valid_until', 'expires_at'])) {
            $columnMap['expiry'] = $col;
        } elseif (in_array($colLower, ['isactive', 'is_active', 'active', 'status'])) {
            $columnMap['active'] = $col;
        } elseif (in_array($colLower, ['description', 'desc'])) {
            $columnMap['description'] = $col;
        }
    }

    error_log("Column mapping: " . print_r($columnMap, true));

    // Build the query with detected column names
    $codeCol = $columnMap['code'] ?: 'id';
    $nameCol = $columnMap['name'] ? "COALESCE({$columnMap['name']}, {$codeCol})" : $codeCol;
    $typeCol = $columnMap['type'] ? "COALESCE({$columnMap['type']}, 'percentage')" : "'percentage'";
    $valueCol = $columnMap['value'] ?: '10';
    $minOrderCol = $columnMap['min_order'] ? "COALESCE({$columnMap['min_order']}, 0)" : '0';
    $maxDiscountCol = $columnMap['max_discount'] ? "COALESCE({$columnMap['max_discount']}, 0)" : '0';
    $expiryCol = $columnMap['expiry'] ?: 'NULL';
    $activeCol = $columnMap['active'] ? "COALESCE({$columnMap['active']}, 1)" : '1';
    $descCol = $columnMap['description'] ? "COALESCE({$columnMap['description']}, CONCAT('Get discount with code ', {$codeCol}))" : "CONCAT('Get discount with code ', {$codeCol})";

    // Query to get active coupons that are applicable for the current order amount
    $query = "SELECT
                {$codeCol} as CouponCode,
                {$nameCol} as CouponName,
                {$typeCol} as DiscountType,
                {$valueCol} as DiscountValue,
                {$minOrderCol} as MinimumOrderValue,
                {$maxDiscountCol} as MaximumDiscountAmount,
                {$expiryCol} as ExpiryDate,
                COALESCE(IsShining, 0) as IsShining,
                {$descCol} as Description
              FROM enhanced_coupons
              WHERE {$activeCol} = 1
              AND ({$expiryCol} IS NULL OR {$expiryCol} >= ?)
              AND {$minOrderCol} <= ?
              ORDER BY COALESCE(IsShining, 0) DESC, {$minOrderCol} ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sd", $currentDate, $orderAmount);
    $stmt->execute();
    $result = $stmt->get_result();

    error_log("Query executed. Rows found: " . $result->num_rows);
    error_log("Query: " . $query);
    error_log("Parameters: currentDate=$currentDate, orderAmount=$orderAmount");

    $availableCoupons = [];
    
    while ($row = $result->fetch_assoc()) {
        // Calculate potential discount
        $discount = 0;
        if ($row['DiscountType'] === 'percentage') {
            $discount = ($orderAmount * $row['DiscountValue']) / 100;
            if ($row['MaximumDiscountAmount'] > 0 && $discount > $row['MaximumDiscountAmount']) {
                $discount = $row['MaximumDiscountAmount'];
            }
        } else {
            $discount = $row['DiscountValue'];
        }
        
        // Format discount display
        $discountDisplay = '';
        if ($row['DiscountType'] === 'percentage') {
            $discountDisplay = $row['DiscountValue'] . '% OFF';
            if ($row['MaximumDiscountAmount'] > 0) {
                $discountDisplay .= ' (Max ₹' . $row['MaximumDiscountAmount'] . ')';
            }
        } else {
            $discountDisplay = '₹' . $row['DiscountValue'] . ' OFF';
        }
        
        $availableCoupons[] = [
            'code' => $row['CouponCode'],
            'name' => $row['CouponName'],
            'description' => $row['Description'] ?: $discountDisplay,
            'discount_display' => $discountDisplay,
            'potential_discount' => round($discount, 2),
            'minimum_order' => $row['MinimumOrderValue'],
            'is_shining' => $row['IsShining'] == 1,
            'discount_type' => $row['DiscountType'],
            'discount_value' => $row['DiscountValue']
        ];
    }

    error_log("Total coupons found: " . count($availableCoupons));
    error_log("Available coupons: " . print_r($availableCoupons, true));

    echo json_encode([
        "response" => "S",
        "coupons" => $availableCoupons,
        "order_amount" => $orderAmount,
        "total_coupons" => count($availableCoupons)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "response" => "E", 
        "message" => "Error fetching coupons: " . $e->getMessage()
    ]);
}
?>

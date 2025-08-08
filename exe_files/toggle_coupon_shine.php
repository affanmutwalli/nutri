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

// Validate input
if (!isset($data['coupon_id']) || !is_numeric($data['coupon_id'])) {
    echo json_encode(["response" => "E", "message" => "Invalid coupon ID"]);
    exit();
}

$couponId = intval($data['coupon_id']);

// Get the primary key column name
$pkQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'enhanced_coupons'
           AND COLUMN_KEY = 'PRI'";
$pkResult = $conn->query($pkQuery);
$pkColumn = 'id'; // default fallback
if ($pkResult && $pkRow = $pkResult->fetch_assoc()) {
    $pkColumn = $pkRow['COLUMN_NAME'];
}

try {
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

    // Toggle the shining status
    $query = "UPDATE enhanced_coupons SET IsShining = NOT COALESCE(IsShining, 0) WHERE $pkColumn = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $couponId);
    
    if ($stmt->execute()) {
        // Get the updated status
        $selectQuery = "SELECT CouponCode, COALESCE(IsShining, 0) as IsShining FROM enhanced_coupons WHERE $pkColumn = ?";
        $selectStmt = $conn->prepare($selectQuery);
        $selectStmt->bind_param("i", $couponId);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $newStatus = $row['IsShining'] ? 'enabled' : 'disabled';
            echo json_encode([
                "response" => "S",
                "message" => "Shining feature {$newStatus} for coupon {$row['CouponCode']}",
                "coupon_code" => $row['CouponCode'],
                "is_shining" => $row['IsShining'] == 1
            ]);
        } else {
            echo json_encode(["response" => "E", "message" => "Coupon not found"]);
        }
    } else {
        echo json_encode(["response" => "E", "message" => "Failed to update coupon"]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        "response" => "E", 
        "message" => "Error toggling coupon shine: " . $e->getMessage()
    ]);
}
?>

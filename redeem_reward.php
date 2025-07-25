<?php
// Prevent any output before JSON
ob_start();
error_reporting(0); // Disable error display for clean JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');
session_start();

require_once 'database/dbconnection.php';

// Check if user is logged in
if (!isset($_SESSION['CustomerId']) || empty($_SESSION['CustomerId'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['reward_id']) || !isset($input['points_required'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$customerId = $_SESSION['CustomerId'];
$rewardId = intval($input['reward_id']);
$pointsRequired = intval($input['points_required']);

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    // Start transaction
    $mysqli->begin_transaction();
    
    // Get user's current points
    $pointsQuery = "SELECT total_points FROM customer_points WHERE customer_id = ? FOR UPDATE";
    $pointsStmt = $mysqli->prepare($pointsQuery);
    $pointsStmt->bind_param("i", $customerId);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    
    if ($pointsResult->num_rows === 0) {
        throw new Exception('Customer points record not found');
    }
    
    $userPoints = $pointsResult->fetch_assoc()['total_points'];
    
    // Check if user has enough points
    if ($userPoints < $pointsRequired) {
        throw new Exception('Insufficient points');
    }
    
    // Get reward details
    $rewardQuery = "SELECT * FROM rewards_catalog WHERE id = ?";
    $rewardStmt = $mysqli->prepare($rewardQuery);
    $rewardStmt->bind_param("i", $rewardId);
    $rewardStmt->execute();
    $rewardResult = $rewardStmt->get_result();
    
    if ($rewardResult->num_rows === 0) {
        throw new Exception('Reward not found');
    }
    
    $reward = $rewardResult->fetch_assoc();
    
    // Generate coupon code
    $couponCode = generateCouponCode($reward['reward_type'], $customerId);
    
    // Deduct points from user
    $updatePointsQuery = "UPDATE customer_points SET total_points = total_points - ? WHERE customer_id = ?";
    $updatePointsStmt = $mysqli->prepare($updatePointsQuery);
    $updatePointsStmt->bind_param("ii", $pointsRequired, $customerId);
    $updatePointsStmt->execute();
    
    // Record the redemption transaction
    $transactionQuery = "INSERT INTO points_transactions (customer_id, transaction_type, points, description, order_id) VALUES (?, 'redeemed', ?, ?, ?)";
    $transactionStmt = $mysqli->prepare($transactionQuery);
    $negativePoints = -$pointsRequired;
    $description = "Redeemed: " . $reward['reward_name'];
    $transactionStmt->bind_param("iiss", $customerId, $negativePoints, $description, $couponCode);
    $transactionStmt->execute();
    
    // Create coupon record (if coupons table exists)
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'coupons'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        // Check if table has the required columns
        $columnsCheck = $mysqli->query("DESCRIBE coupons");
        $hasRequiredColumns = false;

        if ($columnsCheck) {
            $columns = [];
            while ($col = $columnsCheck->fetch_assoc()) {
                $columns[] = $col['Field'];
            }

            $requiredColumns = ['coupon_code', 'customer_id', 'discount_type', 'discount_value'];
            $hasRequiredColumns = count(array_intersect($requiredColumns, $columns)) === count($requiredColumns);
        }

        if ($hasRequiredColumns) {
            $createCouponQuery = "INSERT INTO coupons (coupon_code, customer_id, discount_type, discount_value, min_order_amount, is_active, created_at, expires_at)
                                  VALUES (?, ?, ?, ?, 0, 1, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))";

            try {
                $couponStmt = $mysqli->prepare($createCouponQuery);
                $discountType = $reward['reward_type'] === 'discount' ? 'fixed' : 'free_shipping';
                $discountValue = $reward['reward_value'];
                $couponStmt->bind_param("sisf", $couponCode, $customerId, $discountType, $discountValue);
                $couponStmt->execute();
            } catch (Exception $e) {
                // Log error but don't fail the redemption
                error_log("Failed to create coupon record: " . $e->getMessage());
            }
        }
    }
    
    // Commit transaction
    $mysqli->commit();

    // Clean any output buffer
    ob_clean();

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Reward redeemed successfully!',
        'coupon_code' => $couponCode,
        'reward_name' => $reward['reward_name'],
        'points_deducted' => $pointsRequired,
        'remaining_points' => $userPoints - $pointsRequired
    ]);
    exit;
    
} catch (Exception $e) {
    // Rollback transaction
    if (isset($mysqli)) {
        $mysqli->rollback();
    }

    // Clean any output buffer
    ob_clean();

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

function generateCouponCode($rewardType, $customerId) {
    $prefix = '';
    switch ($rewardType) {
        case 'discount':
            $prefix = 'DISC';
            break;
        case 'free_shipping':
            $prefix = 'SHIP';
            break;
        default:
            $prefix = 'REWARD';
    }
    
    $timestamp = time();
    $random = mt_rand(1000, 9999);
    
    return $prefix . $customerId . $timestamp . $random;
}
?>

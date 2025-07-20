<?php
// Simple, robust redemption script
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// Start output buffering to catch any unwanted output
ob_start();

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Start session without any output
session_start();

function sendJsonResponse($success, $message, $data = []) {
    // Clear any output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Start fresh output buffer
    ob_start();

    $response = array_merge([
        'success' => $success,
        'message' => $message
    ], $data);

    echo json_encode($response);
    ob_end_flush();
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['CustomerId']) || empty($_SESSION['CustomerId'])) {
    sendJsonResponse(false, 'User not logged in');
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Invalid request method');
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['reward_id']) || !isset($input['points_required'])) {
    sendJsonResponse(false, 'Missing required parameters');
}

$customerId = intval($_SESSION['CustomerId']);
$rewardId = intval($input['reward_id']);
$pointsRequired = intval($input['points_required']);

try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        sendJsonResponse(false, 'Database connection failed');
    }
    
    // Start transaction
    $mysqli->begin_transaction();
    
    // Get user's current points
    $pointsQuery = "SELECT total_points FROM customer_points WHERE customer_id = ? FOR UPDATE";
    $pointsStmt = $mysqli->prepare($pointsQuery);
    
    if (!$pointsStmt) {
        throw new Exception('Failed to prepare points query');
    }
    
    $pointsStmt->bind_param("i", $customerId);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    
    if ($pointsResult->num_rows === 0) {
        throw new Exception('Customer points record not found');
    }
    
    $userPoints = $pointsResult->fetch_assoc()['total_points'];
    
    // Check if user has enough points
    if ($userPoints < $pointsRequired) {
        throw new Exception('Insufficient points. You have ' . $userPoints . ' points but need ' . $pointsRequired);
    }
    
    // Get reward details
    $rewardQuery = "SELECT * FROM rewards_catalog WHERE id = ?";
    $rewardStmt = $mysqli->prepare($rewardQuery);
    
    if (!$rewardStmt) {
        throw new Exception('Failed to prepare reward query');
    }
    
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
    
    if (!$updatePointsStmt) {
        throw new Exception('Failed to prepare update points query');
    }
    
    $updatePointsStmt->bind_param("ii", $pointsRequired, $customerId);
    
    if (!$updatePointsStmt->execute()) {
        throw new Exception('Failed to update customer points');
    }
    
    // Record the redemption transaction
    $transactionQuery = "INSERT INTO points_transactions (customer_id, transaction_type, points, description, order_id) VALUES (?, 'redeemed', ?, ?, ?)";
    $transactionStmt = $mysqli->prepare($transactionQuery);
    
    if (!$transactionStmt) {
        throw new Exception('Failed to prepare transaction query');
    }
    
    $negativePoints = -$pointsRequired;
    $description = "Redeemed: " . $reward['reward_name'];
    $transactionStmt->bind_param("iiss", $customerId, $negativePoints, $description, $couponCode);
    
    if (!$transactionStmt->execute()) {
        throw new Exception('Failed to record transaction');
    }
    
    // Try to create coupon record (optional - won't fail if table doesn't exist)
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'coupons'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $createCouponQuery = "INSERT INTO coupons (coupon_code, customer_id, discount_type, discount_value, min_order_amount, is_active, expires_at) 
                              VALUES (?, ?, 'fixed', ?, 0, 1, DATE_ADD(NOW(), INTERVAL 30 DAY))";
        
        $couponStmt = $mysqli->prepare($createCouponQuery);
        if ($couponStmt) {
            $couponStmt->bind_param("sif", $couponCode, $customerId, $reward['reward_value']);
            $couponStmt->execute(); // Don't fail if this doesn't work
        }
    }
    
    // Commit transaction
    $mysqli->commit();
    
    // Send success response
    sendJsonResponse(true, 'Reward redeemed successfully!', [
        'coupon_code' => $couponCode,
        'reward_name' => $reward['reward_name'],
        'points_deducted' => $pointsRequired,
        'remaining_points' => $userPoints - $pointsRequired
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    if (isset($mysqli)) {
        $mysqli->rollback();
    }
    
    sendJsonResponse(false, $e->getMessage());
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

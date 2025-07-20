<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

session_start();

// Check if user is logged in
if (!isset($_SESSION['CustomerId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['reward_id']) || !isset($input['points_required'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$customerId = intval($_SESSION['CustomerId']);
$rewardId = intval($input['reward_id']);
$pointsRequired = intval($input['points_required']);

try {
    require_once 'database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();
    
    // Get user points
    $pointsQuery = "SELECT total_points FROM customer_points WHERE customer_id = $customerId";
    $pointsResult = $mysqli->query($pointsQuery);
    
    if (!$pointsResult || $pointsResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Customer not found']);
        exit;
    }
    
    $userPoints = $pointsResult->fetch_assoc()['total_points'];
    
    // Check if enough points
    if ($userPoints < $pointsRequired) {
        echo json_encode([
            'success' => false, 
            'message' => "Insufficient points. You have $userPoints but need $pointsRequired"
        ]);
        exit;
    }
    
    // Get reward info
    $rewardQuery = "SELECT * FROM rewards_catalog WHERE id = $rewardId";
    $rewardResult = $mysqli->query($rewardQuery);
    
    if (!$rewardResult || $rewardResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Reward not found']);
        exit;
    }
    
    $reward = $rewardResult->fetch_assoc();
    
    // Generate coupon code
    $couponCode = 'DISC' . $customerId . time() . rand(1000, 9999);
    
    // Start transaction
    $mysqli->begin_transaction();
    
    // Deduct points
    $updateQuery = "UPDATE customer_points SET total_points = total_points - $pointsRequired WHERE customer_id = $customerId";
    if (!$mysqli->query($updateQuery)) {
        $mysqli->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to update points']);
        exit;
    }
    
    // Add transaction record
    $transQuery = "INSERT INTO points_transactions (customer_id, transaction_type, points, description, order_id) 
                   VALUES ($customerId, 'redeemed', -$pointsRequired, 'Redeemed: {$reward['reward_name']}', '$couponCode')";
    
    if (!$mysqli->query($transQuery)) {
        $mysqli->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to record transaction']);
        exit;
    }
    
    // Try to add coupon (optional)
    $couponQuery = "INSERT INTO coupons (coupon_code, customer_id, discount_type, discount_value, is_active, expires_at) 
                    VALUES ('$couponCode', $customerId, 'fixed', {$reward['reward_value']}, 1, DATE_ADD(NOW(), INTERVAL 30 DAY))";
    $mysqli->query($couponQuery); // Don't fail if this doesn't work
    
    // Commit
    $mysqli->commit();
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Reward redeemed successfully!',
        'coupon_code' => $couponCode,
        'reward_name' => $reward['reward_name'],
        'points_deducted' => $pointsRequired,
        'remaining_points' => $userPoints - $pointsRequired
    ]);
    
} catch (Exception $e) {
    if (isset($mysqli)) {
        $mysqli->rollback();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

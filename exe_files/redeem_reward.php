<?php
session_start();
header('Content-Type: application/json');

include('../database/dbconnection.php');
include('../includes/RewardsSystem.php');
include('../includes/CouponSystem.php');

$obj = new main();
$mysqli = $obj->connection();

// Check if user is logged in
if (!isset($_SESSION['CustomerId']) || empty($_SESSION['CustomerId'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to redeem rewards'
    ]);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$rewardId = $input['reward_id'] ?? null;

if (!$rewardId) {
    echo json_encode([
        'success' => false,
        'message' => 'Reward ID is required'
    ]);
    exit;
}

try {
    $customerId = $_SESSION['CustomerId'];
    
    // Initialize systems
    $rewards = new RewardsSystem($mysqli);
    $coupons = new CouponSystem($mysqli);
    
    // Get reward details
    $rewardQuery = "SELECT * FROM rewards_catalog WHERE id = ? AND is_active = 1";
    $stmt = $mysqli->prepare($rewardQuery);
    $stmt->bind_param("i", $rewardId);
    $stmt->execute();
    $rewardResult = $stmt->get_result();
    $reward = $rewardResult->fetch_assoc();
    
    if (!$reward) {
        echo json_encode([
            'success' => false,
            'message' => 'Reward not found or inactive'
        ]);
        exit;
    }
    
    // Check if customer has enough points
    $customerPoints = $rewards->getCustomerPoints($customerId);
    if ($customerPoints['total_points'] < $reward['points_required']) {
        echo json_encode([
            'success' => false,
            'message' => 'Insufficient points. You need ' . $reward['points_required'] . ' points but have only ' . $customerPoints['total_points'] . ' points.'
        ]);
        exit;
    }
    
    // Check redemption limits
    $redemptionQuery = "SELECT COUNT(*) as redemption_count FROM reward_redemptions 
                        WHERE customer_id = ? AND reward_id = ? AND redemption_status = 'active'";
    $stmt = $mysqli->prepare($redemptionQuery);
    $stmt->bind_param("ii", $customerId, $rewardId);
    $stmt->execute();
    $redemptionResult = $stmt->get_result();
    $redemptionData = $redemptionResult->fetch_assoc();
    
    if ($redemptionData['redemption_count'] >= $reward['max_redemptions_per_customer']) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already redeemed this reward the maximum number of times'
        ]);
        exit;
    }
    
    // Check total redemption limit
    if ($reward['total_redemptions_limit'] && $reward['current_redemptions'] >= $reward['total_redemptions_limit']) {
        echo json_encode([
            'success' => false,
            'message' => 'This reward is no longer available'
        ]);
        exit;
    }
    
    // Start transaction
    $mysqli->begin_transaction();
    
    try {
        // Redeem points for coupon
        $result = $coupons->redeemPointsForCoupon($customerId, $rewardId);
        
        if ($result['success']) {
            // Update reward redemption count
            $updateQuery = "UPDATE rewards_catalog SET current_redemptions = current_redemptions + 1 WHERE id = ?";
            $stmt = $mysqli->prepare($updateQuery);
            $stmt->bind_param("i", $rewardId);
            $stmt->execute();
            
            $mysqli->commit();
            
            echo json_encode([
                'success' => true,
                'message' => $result['message'],
                'coupon_code' => $result['coupon_code'],
                'points_used' => $reward['points_required'],
                'remaining_points' => $customerPoints['total_points'] - $reward['points_required']
            ]);
        } else {
            $mysqli->rollback();
            echo json_encode([
                'success' => false,
                'message' => $result['message']
            ]);
        }
        
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Reward redemption error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while redeeming the reward. Please try again.'
    ]);
}
?>

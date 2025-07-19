<?php
/**
 * Redeem Points for Coupon API
 * Allows customers to redeem their reward points for coupons
 */

session_start();
require_once '../database/dbconnection.php';
require_once '../includes/CouponSystem.php';
require_once '../includes/RewardsSystem.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['CustomerId'])) {
        echo json_encode([
            "success" => false,
            "message" => "Please login to redeem rewards",
            "response" => "E"
        ]);
        exit;
    }
    
    $customerId = intval($_SESSION['CustomerId']);
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['reward_id']) || empty($data['reward_id'])) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid reward selection",
            "response" => "E"
        ]);
        exit;
    }
    
    $rewardId = intval($data['reward_id']);
    
    // Initialize systems
    $couponSystem = new CouponSystem($mysqli);
    $rewardsSystem = new RewardsSystem($mysqli);
    
    // Get customer's current points
    $customerPoints = $rewardsSystem->getCustomerPoints($customerId);
    
    // Get reward details
    $query = "SELECT * FROM rewards_catalog WHERE id = ? AND is_active = 1 AND reward_type = 'coupon'";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $rewardId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reward = $result->fetch_assoc();
    
    if (!$reward) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid reward selected",
            "response" => "E"
        ]);
        exit;
    }
    
    // Check if customer has enough points
    if ($customerPoints['total_points'] < $reward['points_required']) {
        echo json_encode([
            "success" => false,
            "message" => "You need {$reward['points_required']} points but only have {$customerPoints['total_points']} points",
            "response" => "E"
        ]);
        exit;
    }
    
    // Check redemption limits
    $query = "SELECT COUNT(*) as redemption_count 
              FROM reward_redemptions 
              WHERE customer_id = ? AND reward_id = ? AND redemption_status != 'cancelled'";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $customerId, $rewardId);
    $stmt->execute();
    $result = $stmt->get_result();
    $redemptionData = $result->fetch_assoc();
    
    if ($redemptionData['redemption_count'] >= $reward['max_redemptions_per_customer']) {
        echo json_encode([
            "success" => false,
            "message" => "You have already redeemed this reward the maximum number of times",
            "response" => "E"
        ]);
        exit;
    }
    
    // Check total redemption limit
    if ($reward['total_redemptions_limit'] && $reward['current_redemptions'] >= $reward['total_redemptions_limit']) {
        echo json_encode([
            "success" => false,
            "message" => "This reward is no longer available",
            "response" => "E"
        ]);
        exit;
    }
    
    // Redeem points for coupon
    $result = $couponSystem->redeemPointsForCoupon($customerId, $rewardId);
    
    if ($result['success']) {
        // Record the redemption in rewards system
        $expiryDate = date('Y-m-d H:i:s', strtotime('+30 days'));
        $query = "INSERT INTO reward_redemptions 
                  (customer_id, reward_id, points_used, coupon_code, redemption_status, expires_at) 
                  VALUES (?, ?, ?, ?, 'active', ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("iisss", $customerId, $rewardId, $reward['points_required'], $result['coupon_code'], $expiryDate);
        $stmt->execute();
        
        // Update reward redemption count
        $query = "UPDATE rewards_catalog SET current_redemptions = current_redemptions + 1 WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $rewardId);
        $stmt->execute();
        
        // Get updated customer points
        $updatedPoints = $rewardsSystem->getCustomerPoints($customerId);
        
        echo json_encode([
            "success" => true,
            "message" => $result['message'],
            "coupon_code" => $result['coupon_code'],
            "points_used" => $reward['points_required'],
            "remaining_points" => $updatedPoints['total_points'],
            "response" => "S"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => $result['message'],
            "response" => "E"
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error in redeem_points_coupon.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "An error occurred while redeeming the reward. Please try again.",
        "response" => "E"
    ]);
}
?>

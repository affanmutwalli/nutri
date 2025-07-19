<?php
/**
 * Get Customer Points API
 * Returns customer's current points balance
 */

session_start();
require_once '../database/dbconnection.php';
require_once '../includes/RewardsSystem.php';

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['CustomerId'])) {
        echo json_encode([
            "success" => false,
            "message" => "Please login to view your points",
            "points" => 0
        ]);
        exit;
    }
    
    $customerId = intval($_SESSION['CustomerId']);
    
    // Initialize rewards system
    $rewardsSystem = new RewardsSystem($mysqli);
    
    // Get customer points
    $customerPoints = $rewardsSystem->getCustomerPoints($customerId);
    
    echo json_encode([
        "success" => true,
        "points" => $customerPoints['total_points'],
        "lifetime_points" => $customerPoints['lifetime_points'],
        "points_redeemed" => $customerPoints['points_redeemed'],
        "tier_level" => $customerPoints['tier_level']
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_customer_points.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error retrieving points information",
        "points" => 0
    ]);
}
?>

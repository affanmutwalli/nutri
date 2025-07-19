<?php
session_start();

// Check if customer is logged in
$isLoggedIn = isset($_SESSION["CustomerId"]) && !empty($_SESSION["CustomerId"]);

// Get customer details if logged in
$customerData = null;
if ($isLoggedIn) {
    try {
        // Include rewards system
        include_once __DIR__ . '/includes/RewardsSystem.php';
        $rewards = new RewardsSystem();

        // Get customer points data
        $pointsData = $rewards->getCustomerPoints($_SESSION["CustomerId"]);
        $referralCode = $rewards->getCustomerReferralCode($_SESSION["CustomerId"]);

        $customerData = [
            'id' => $_SESSION["CustomerId"],
            'name' => $_SESSION["CustomerName"] ?? '',
            'email' => $_SESSION["CustomerEmail"] ?? '',
            'points' => $pointsData['total_points'],
            'lifetime_points' => $pointsData['lifetime_points'],
            'tier_level' => $pointsData['tier_level'],
            'referral_code' => $referralCode
        ];
    } catch (Exception $e) {
        // Fallback if rewards system fails
        error_log("Rewards system error: " . $e->getMessage());
        $customerData = [
            'id' => $_SESSION["CustomerId"],
            'name' => $_SESSION["CustomerName"] ?? '',
            'email' => $_SESSION["CustomerEmail"] ?? '',
            'points' => 0,
            'lifetime_points' => 0,
            'tier_level' => 'Bronze',
            'referral_code' => ''
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'loggedIn' => $isLoggedIn,
    'customer' => $customerData
]);
?>

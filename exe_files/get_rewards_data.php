<?php
session_start();
header('Content-Type: application/json');

include('../database/dbconnection.php');
include('../includes/RewardsSystem.php');

$obj = new main();
$mysqli = $obj->connection();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['CustomerId']) && !empty($_SESSION['CustomerId']);

$response = [
    'loggedIn' => $isLoggedIn,
    'customer' => null,
    'available_rewards' => [],
    'recent_transactions' => [],
    'earning_methods' => [
        [
            'icon' => '🛒',
            'title' => 'Make a Purchase',
            'description' => 'Earn 3 points for every ₹100 spent',
            'points' => '+3 pts'
        ],
        [
            'icon' => '⭐',
            'title' => 'Write a Review',
            'description' => 'Share your experience with products',
            'points' => '+25 pts'
        ],
        [
            'icon' => '👥',
            'title' => 'Refer a Friend',
            'description' => 'When they make their first purchase',
            'points' => '+100 pts'
        ],
        [
            'icon' => '🎂',
            'title' => 'Signup Bonus',
            'description' => 'Welcome bonus for joining My Nutrify',
            'points' => '+25 pts'
        ]
    ]
];

if ($isLoggedIn) {
    try {
        // Initialize rewards system
        $rewards = new RewardsSystem($mysqli);
        
        // Get customer points data
        $pointsData = $rewards->getCustomerPoints($_SESSION["CustomerId"]);

        // Get referral code with error handling
        try {
            $referralCode = $rewards->getCustomerReferralCode($_SESSION["CustomerId"]);
        } catch (Exception $e) {
            $referralCode = 'REF' . str_pad($_SESSION["CustomerId"], 6, '0', STR_PAD_LEFT);
        }
        
        // Get customer basic info
        $customerQuery = "SELECT CustomerName, CustomerEmail FROM customer_master WHERE CustomerId = ?";
        $stmt = $mysqli->prepare($customerQuery);
        $stmt->bind_param("i", $_SESSION["CustomerId"]);
        $stmt->execute();
        $customerResult = $stmt->get_result();
        $customerInfo = $customerResult->fetch_assoc();
        
        $response['customer'] = [
            'id' => $_SESSION["CustomerId"],
            'name' => $customerInfo['CustomerName'] ?? '',
            'email' => $customerInfo['CustomerEmail'] ?? '',
            'points' => $pointsData['total_points'],
            'lifetime_points' => $pointsData['lifetime_points'],
            'tier_level' => $pointsData['tier_level'],
            'referral_code' => $referralCode
        ];
        
        // Get available rewards
        $rewardsQuery = "SELECT id, reward_name, reward_description, points_required, 
                                reward_type, reward_value, minimum_order_amount, terms_conditions
                         FROM rewards_catalog 
                         WHERE is_active = 1 
                           AND (valid_from IS NULL OR valid_from <= CURDATE())
                           AND (valid_until IS NULL OR valid_until >= CURDATE())
                         ORDER BY points_required ASC";
        $rewardsResult = $mysqli->query($rewardsQuery);
        
        while ($reward = $rewardsResult->fetch_assoc()) {
            $reward['can_redeem'] = $pointsData['total_points'] >= $reward['points_required'];
            $response['available_rewards'][] = $reward;
        }
        
        // Get recent transactions (last 10)
        $transactionsQuery = "SELECT transaction_type, points_amount, description, created_at
                              FROM points_transactions 
                              WHERE customer_id = ? 
                              ORDER BY created_at DESC 
                              LIMIT 10";
        $stmt = $mysqli->prepare($transactionsQuery);
        $stmt->bind_param("i", $_SESSION["CustomerId"]);
        $stmt->execute();
        $transactionsResult = $stmt->get_result();
        
        while ($transaction = $transactionsResult->fetch_assoc()) {
            $response['recent_transactions'][] = [
                'type' => $transaction['transaction_type'],
                'points' => $transaction['points_amount'],
                'description' => $transaction['description'],
                'date' => date('M j, Y', strtotime($transaction['created_at']))
            ];
        }
        
    } catch (Exception $e) {
        error_log("Rewards data error: " . $e->getMessage());
        $response['customer'] = [
            'id' => $_SESSION["CustomerId"],
            'name' => '',
            'email' => '',
            'points' => 0,
            'lifetime_points' => 0,
            'tier_level' => 'Bronze',
            'referral_code' => ''
        ];
    }
}

echo json_encode($response);
?>

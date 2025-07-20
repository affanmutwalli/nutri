<?php
header('Content-Type: application/json');
session_start();

require_once 'database/dbconnection.php';

// Check if user is logged in
if (!isset($_SESSION['CustomerId']) || empty($_SESSION['CustomerId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$customerId = $_SESSION['CustomerId'];

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    // Get user points
    $pointsQuery = "SELECT * FROM customer_points WHERE customer_id = ?";
    $pointsStmt = $mysqli->prepare($pointsQuery);
    $pointsStmt->bind_param("i", $customerId);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    
    $userPoints = null;
    if ($pointsResult->num_rows > 0) {
        $userPoints = $pointsResult->fetch_assoc();
    } else {
        // Create customer points record if it doesn't exist
        $createQuery = "INSERT INTO customer_points (customer_id, total_points, lifetime_points, tier_level) VALUES (?, 0, 0, 'Bronze')";
        $createStmt = $mysqli->prepare($createQuery);
        $createStmt->bind_param("i", $customerId);
        $createStmt->execute();
        
        $userPoints = [
            'customer_id' => $customerId,
            'total_points' => 0,
            'lifetime_points' => 0,
            'tier_level' => 'Bronze'
        ];
    }
    
    // Get recent transactions (last 10)
    $transactionsQuery = "SELECT * FROM points_transactions WHERE customer_id = ? ORDER BY created_at DESC LIMIT 10";
    $transactionsStmt = $mysqli->prepare($transactionsQuery);
    $transactionsStmt->bind_param("i", $customerId);
    $transactionsStmt->execute();
    $transactionsResult = $transactionsStmt->get_result();
    
    $transactions = [];
    while ($transaction = $transactionsResult->fetch_assoc()) {
        $transactions[] = $transaction;
    }
    
    // Get rewards catalog
    $catalogQuery = "SELECT * FROM rewards_catalog ORDER BY points_required ASC";
    $catalogResult = $mysqli->query($catalogQuery);
    
    $catalog = [];
    if ($catalogResult) {
        while ($reward = $catalogResult->fetch_assoc()) {
            $catalog[] = $reward;
        }
    }
    
    // Calculate tier progress
    $tierProgress = calculateTierProgress($userPoints['lifetime_points']);
    
    echo json_encode([
        'success' => true,
        'points' => $userPoints,
        'transactions' => $transactions,
        'catalog' => $catalog,
        'tier_progress' => $tierProgress
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching rewards data: ' . $e->getMessage()
    ]);
}

function calculateTierProgress($lifetimePoints) {
    $tiers = [
        'Bronze' => 0,
        'Silver' => 500,
        'Gold' => 1500,
        'Platinum' => 3000
    ];
    
    $currentTier = 'Bronze';
    $nextTier = 'Silver';
    $nextTierPoints = 500;
    $progress = 0;
    
    foreach ($tiers as $tier => $requiredPoints) {
        if ($lifetimePoints >= $requiredPoints) {
            $currentTier = $tier;
        } else {
            $nextTier = $tier;
            $nextTierPoints = $requiredPoints;
            break;
        }
    }
    
    if ($currentTier === 'Platinum') {
        $progress = 100;
        $nextTier = null;
        $nextTierPoints = null;
    } else {
        $currentTierPoints = $tiers[$currentTier];
        $progress = (($lifetimePoints - $currentTierPoints) / ($nextTierPoints - $currentTierPoints)) * 100;
    }
    
    return [
        'current_tier' => $currentTier,
        'next_tier' => $nextTier,
        'next_tier_points' => $nextTierPoints,
        'progress_percentage' => round($progress, 1),
        'points_to_next_tier' => $nextTierPoints ? $nextTierPoints - $lifetimePoints : 0
    ];
}
?>

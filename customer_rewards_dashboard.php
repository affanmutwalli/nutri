<?php
session_start();
require_once 'database/dbconnection.php';
require_once 'includes/RewardsSystem.php';
require_once 'includes/CouponSystem.php';

// Initialize database connection
$obj = new main();
$mysqli = $obj->connection();

// Check if user is logged in
if (!isset($_SESSION['CustomerId'])) {
    header('Location: login.php');
    exit;
}

$customerId = $_SESSION['CustomerId'];
$rewardsSystem = new RewardsSystem($mysqli);
$couponSystem = new CouponSystem($mysqli);

// Get customer data
$customerPoints = $rewardsSystem->getCustomerPoints($customerId);
$availableRewards = $rewardsSystem->getAvailableRewards($customerId);
$pointsHistory = $rewardsSystem->getPointsHistory($customerId, 20);
$walletCoupons = $couponSystem->getCustomerWalletCoupons($customerId);
$couponHistory = $couponSystem->getCustomerCouponHistory($customerId);
$availableCoupons = $couponSystem->getAvailableCoupons($customerId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rewards & Coupons - My Nutrify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .rewards-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .points-card {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .tier-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        .reward-card, .coupon-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .reward-card:hover, .coupon-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .redeem-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .redeem-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .nav-tabs .nav-link.active {
            background-color: #27ae60;
            border-color: #27ae60;
            color: white;
        }
        .history-item {
            border-left: 3px solid #27ae60;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .coupon-code {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 5px;
            font-family: monospace;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="rewards-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-gift"></i> My Rewards & Coupons</h1>
                    <p class="mb-0">Earn points, redeem rewards, and save with exclusive coupons</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="index.php" class="btn btn-light">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Points Overview -->
        <div class="row">
            <div class="col-md-6">
                <div class="points-card">
                    <h2><i class="fas fa-coins"></i> Your Points</h2>
                    <div style="font-size: 3rem; font-weight: bold; margin: 1rem 0;">
                        <?php echo number_format($customerPoints['total_points']); ?>
                    </div>
                    <p>Available Nutrify Points</p>
                    <div class="tier-badge">
                        <i class="fas fa-crown"></i> <?php echo $customerPoints['tier_level']; ?> Member
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5><?php echo number_format($customerPoints['lifetime_points']); ?></h5>
                                <small class="text-muted">Lifetime Points</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5><?php echo number_format($customerPoints['points_redeemed']); ?></h5>
                                <small class="text-muted">Points Redeemed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5><?php echo count($walletCoupons); ?></h5>
                                <small class="text-muted">Active Coupons</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="rewardsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="redeem-tab" data-bs-toggle="tab" data-bs-target="#redeem" type="button" role="tab">
                    <i class="fas fa-gift"></i> Redeem Rewards
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="coupons-tab" data-bs-toggle="tab" data-bs-target="#coupons" type="button" role="tab">
                    <i class="fas fa-ticket-alt"></i> My Coupons
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                    <i class="fas fa-history"></i> History
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="earn-tab" data-bs-toggle="tab" data-bs-target="#earn" type="button" role="tab">
                    <i class="fas fa-star"></i> Ways to Earn
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="rewardsTabContent">
            <!-- Redeem Rewards Tab -->
            <div class="tab-pane fade show active" id="redeem" role="tabpanel">
                <div class="row mt-4">
                    <?php if (empty($availableRewards)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You don't have enough points to redeem any rewards yet. Keep shopping to earn more points!
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($availableRewards as $reward): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="reward-card">
                                    <h5><?php echo htmlspecialchars($reward['reward_name']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($reward['reward_description']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary"><?php echo $reward['points_required']; ?> Points</span>
                                        <span class="text-success">₹<?php echo number_format($reward['reward_value'], 2); ?></span>
                                    </div>
                                    <?php if ($reward['minimum_order_amount'] > 0): ?>
                                        <small class="text-muted">Min order: ₹<?php echo number_format($reward['minimum_order_amount'], 2); ?></small>
                                    <?php endif; ?>
                                    <div class="mt-3">
                                        <button class="redeem-btn w-100" 
                                                onclick="redeemReward(<?php echo $reward['id']; ?>, '<?php echo htmlspecialchars($reward['reward_name']); ?>')"
                                                <?php echo $customerPoints['total_points'] < $reward['points_required'] ? 'disabled' : ''; ?>>
                                            <?php echo $customerPoints['total_points'] >= $reward['points_required'] ? 'Redeem Now' : 'Insufficient Points'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- My Coupons Tab -->
            <div class="tab-pane fade" id="coupons" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4>My Active Coupons</h4>
                        <?php if (empty($walletCoupons)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You don't have any active coupons. Redeem points for coupons or check available public coupons.
                            </div>
                        <?php else: ?>
                            <?php foreach ($walletCoupons as $coupon): ?>
                                <div class="coupon-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6><?php echo htmlspecialchars($coupon['coupon_name']); ?></h6>
                                            <p class="text-muted mb-2"><?php echo htmlspecialchars($coupon['description']); ?></p>
                                            <div class="coupon-code"><?php echo htmlspecialchars($coupon['coupon_code']); ?></div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success">
                                                <?php echo $coupon['discount_type'] === 'fixed' ? '₹' . $coupon['discount_value'] : $coupon['discount_value'] . '%'; ?> OFF
                                            </span>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Valid until: <?php echo date('d M Y', strtotime($coupon['valid_until'])); ?>
                                        <?php if ($coupon['minimum_order_amount'] > 0): ?>
                                            | Min order: ₹<?php echo number_format($coupon['minimum_order_amount'], 2); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h4>Available Public Coupons</h4>
                        <?php 
                        $publicCoupons = array_filter($availableCoupons, function($coupon) {
                            return $coupon['coupon_source'] === 'public';
                        });
                        ?>
                        <?php if (empty($publicCoupons)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No public coupons available at the moment.
                            </div>
                        <?php else: ?>
                            <?php foreach ($publicCoupons as $coupon): ?>
                                <div class="coupon-card">
                                    <h6><?php echo htmlspecialchars($coupon['coupon_name']); ?></h6>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars($coupon['description']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="coupon-code"><?php echo htmlspecialchars($coupon['coupon_code']); ?></div>
                                        <span class="badge bg-primary">
                                            <?php echo $coupon['discount_type'] === 'fixed' ? '₹' . $coupon['discount_value'] : $coupon['discount_value'] . '%'; ?> OFF
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        Valid until: <?php echo date('d M Y', strtotime($coupon['valid_until'])); ?>
                                        <?php if ($coupon['minimum_order_amount'] > 0): ?>
                                            | Min order: ₹<?php echo number_format($coupon['minimum_order_amount'], 2); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- History Tab -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4>Points History</h4>
                        <?php if (empty($pointsHistory)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No points history yet. Start shopping to earn points!
                            </div>
                        <?php else: ?>
                            <?php foreach ($pointsHistory as $transaction): ?>
                                <div class="history-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php if ($transaction['transaction_type'] === 'earned'): ?>
                                                    <i class="fas fa-plus-circle text-success"></i> +<?php echo $transaction['points_amount']; ?> Points
                                                <?php else: ?>
                                                    <i class="fas fa-minus-circle text-danger"></i> <?php echo $transaction['points_amount']; ?> Points
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1"><?php echo htmlspecialchars($transaction['description']); ?></p>
                                            <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($transaction['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h4>Coupon Usage History</h4>
                        <?php if (empty($couponHistory)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No coupon usage history yet.
                            </div>
                        <?php else: ?>
                            <?php foreach ($couponHistory as $usage): ?>
                                <div class="history-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($usage['coupon_name']); ?></h6>
                                            <p class="mb-1">
                                                Code: <span class="coupon-code"><?php echo htmlspecialchars($usage['coupon_code']); ?></span>
                                            </p>
                                            <p class="mb-1">Saved: ₹<?php echo number_format($usage['discount_applied'], 2); ?></p>
                                            <small class="text-muted">
                                                Order: <?php echo htmlspecialchars($usage['order_id']); ?> |
                                                <?php echo date('d M Y, H:i', strtotime($usage['used_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ways to Earn Tab -->
            <div class="tab-pane fade" id="earn" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-md-8 mx-auto">
                        <h4 class="text-center mb-4">Ways to Earn Nutrify Points</h4>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                                        <h5>Shop & Earn</h5>
                                        <p>Earn 3 points for every ₹100 you spend</p>
                                        <span class="badge bg-primary">3 Points per ₹100</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
                                        <h5>Welcome Bonus</h5>
                                        <p>Get bonus points when you complete your first order</p>
                                        <span class="badge bg-success">25 Points</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-star fa-3x text-warning mb-3"></i>
                                        <h5>Write Reviews</h5>
                                        <p>Share your experience and earn points for each review</p>
                                        <span class="badge bg-warning">25 Points per Review</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-share-alt fa-3x text-info mb-3"></i>
                                        <h5>Refer Friends</h5>
                                        <p>Invite friends and earn points when they make their first purchase</p>
                                        <span class="badge bg-info">100 Points per Referral</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light text-center mt-4">
                            <h5><i class="fas fa-crown text-warning"></i> Tier Benefits</h5>
                            <div class="row">
                                <div class="col-3">
                                    <strong>Bronze</strong><br>
                                    <small>0+ Points</small>
                                </div>
                                <div class="col-3">
                                    <strong>Silver</strong><br>
                                    <small>500+ Points</small>
                                </div>
                                <div class="col-3">
                                    <strong>Gold</strong><br>
                                    <small>1500+ Points</small>
                                </div>
                                <div class="col-3">
                                    <strong>Platinum</strong><br>
                                    <small>5000+ Points</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function redeemReward(rewardId, rewardName) {
            Swal.fire({
                title: 'Redeem Reward',
                text: `Are you sure you want to redeem "${rewardName}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#27ae60',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, redeem it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make API call to redeem reward
                    fetch('exe_files/redeem_points_coupon.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            reward_id: rewardId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#27ae60'
                            }).then(() => {
                                location.reload(); // Refresh to show updated data
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while redeeming the reward.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>

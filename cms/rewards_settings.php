<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$mysqli = $obj->connection();
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

require_once '../includes/setup_rewards_database.php';
require_once '../includes/RewardsSystem.php';

// Auto-setup database tables
autoSetupRewardsSystem($mysqli);

$selected = "rewards_settings.php";
$page = "rewards_settings.php";

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_settings':
                $result = updateRewardsSettings($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            case 'update_tiers':
                $result = updateTierSettings($_POST);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
        }
    }
}

function updateRewardsSettings($data) {
    global $mysqli;
    
    try {
        $settings = [
            'points_per_rupee' => floatval($data['points_per_rupee']),
            'signup_bonus' => intval($data['signup_bonus']),
            'referral_bonus' => intval($data['referral_bonus']),
            'review_bonus' => intval($data['review_bonus']),
            'birthday_bonus' => intval($data['birthday_bonus']),
            'min_order_amount' => floatval($data['min_order_amount']),
            'points_expiry_months' => intval($data['points_expiry_months']),
            'max_points_per_order' => intval($data['max_points_per_order']),
            'enable_tier_system' => isset($data['enable_tier_system']) ? 1 : 0,
            'enable_referrals' => isset($data['enable_referrals']) ? 1 : 0,
            'enable_reviews' => isset($data['enable_reviews']) ? 1 : 0,
            'enable_birthday' => isset($data['enable_birthday']) ? 1 : 0
        ];
        
        foreach ($settings as $key => $value) {
            $query = "INSERT INTO rewards_settings (setting_key, setting_value) VALUES (?, ?) 
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
        
        return ['message' => 'Rewards settings updated successfully!', 'type' => 'success'];
    } catch (Exception $e) {
        return ['message' => 'Error updating settings: ' . $e->getMessage(), 'type' => 'error'];
    }
}

function updateTierSettings($data) {
    global $mysqli;
    
    try {
        $tiers = [
            'bronze_threshold' => intval($data['bronze_threshold']),
            'silver_threshold' => intval($data['silver_threshold']),
            'gold_threshold' => intval($data['gold_threshold']),
            'platinum_threshold' => intval($data['platinum_threshold']),
            'bronze_multiplier' => floatval($data['bronze_multiplier']),
            'silver_multiplier' => floatval($data['silver_multiplier']),
            'gold_multiplier' => floatval($data['gold_multiplier']),
            'platinum_multiplier' => floatval($data['platinum_multiplier'])
        ];
        
        foreach ($tiers as $key => $value) {
            $query = "INSERT INTO rewards_settings (setting_key, setting_value) VALUES (?, ?) 
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
        
        return ['message' => 'Tier settings updated successfully!', 'type' => 'success'];
    } catch (Exception $e) {
        return ['message' => 'Error updating tier settings: ' . $e->getMessage(), 'type' => 'error'];
    }
}

// Get current settings
$currentSettings = [];
$result = $mysqli->query("SELECT setting_key, setting_value FROM rewards_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $currentSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Default values if not set
$defaults = [
    'points_per_rupee' => '1',
    'signup_bonus' => '100',
    'referral_bonus' => '50',
    'review_bonus' => '25',
    'birthday_bonus' => '100',
    'min_order_amount' => '100',
    'points_expiry_months' => '12',
    'max_points_per_order' => '1000',
    'enable_tier_system' => '1',
    'enable_referrals' => '1',
    'enable_reviews' => '1',
    'enable_birthday' => '1',
    'bronze_threshold' => '0',
    'silver_threshold' => '1000',
    'gold_threshold' => '5000',
    'platinum_threshold' => '10000',
    'bronze_multiplier' => '1.0',
    'silver_multiplier' => '1.2',
    'gold_multiplier' => '1.5',
    'platinum_multiplier' => '2.0'
];

foreach ($defaults as $key => $value) {
    if (!isset($currentSettings[$key])) {
        $currentSettings[$key] = $value;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Rewards Settings | My Nutrify CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <style>
        .settings-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .tier-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .tier-bronze { background: linear-gradient(135deg, #cd7f32 0%, #b8860b 100%); }
        .tier-silver { background: linear-gradient(135deg, #c0c0c0 0%, #a9a9a9 100%); color: black; }
        .tier-gold { background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%); color: black; }
        .tier-platinum { background: linear-gradient(135deg, #e5e4e2 0%, #d3d3d3 100%); color: black; }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php include("components/navbar.php"); ?>
    <?php include("components/sidebar.php"); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Rewards Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="enhanced_rewards_dashboard.php">Rewards</a></li>
                            <li class="breadcrumb-item active">Settings</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType == 'success' ? 'success' : 'danger'; ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="enhanced_rewards_dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                        </a>
                        <a href="rewards_management.php" class="btn btn-primary">
                            <i class="fas fa-gift mr-1"></i> Manage Rewards
                        </a>
                        <a href="customer_points.php" class="btn btn-success">
                            <i class="fas fa-users mr-1"></i> Customer Points
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- General Settings -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cog mr-1"></i>
                                    General Rewards Settings
                                </h3>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_settings">
                                <div class="card-body">
                                    <div class="settings-section">
                                        <h5><i class="fas fa-coins mr-2"></i>Points Configuration</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="points_per_rupee">Points per Rupee Spent</label>
                                                    <input type="number" step="0.1" class="form-control" id="points_per_rupee" 
                                                           name="points_per_rupee" value="<?php echo $currentSettings['points_per_rupee']; ?>" required>
                                                    <small class="form-text text-muted">How many points customers earn per rupee spent</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="min_order_amount">Minimum Order Amount</label>
                                                    <input type="number" step="0.01" class="form-control" id="min_order_amount" 
                                                           name="min_order_amount" value="<?php echo $currentSettings['min_order_amount']; ?>" required>
                                                    <small class="form-text text-muted">Minimum order value to earn points</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="max_points_per_order">Max Points per Order</label>
                                                    <input type="number" class="form-control" id="max_points_per_order" 
                                                           name="max_points_per_order" value="<?php echo $currentSettings['max_points_per_order']; ?>" required>
                                                    <small class="form-text text-muted">Maximum points that can be earned in a single order</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="points_expiry_months">Points Expiry (Months)</label>
                                                    <input type="number" class="form-control" id="points_expiry_months" 
                                                           name="points_expiry_months" value="<?php echo $currentSettings['points_expiry_months']; ?>" required>
                                                    <small class="form-text text-muted">How many months before points expire (0 = never)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="settings-section">
                                        <h5><i class="fas fa-gift mr-2"></i>Bonus Points</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="signup_bonus">Signup Bonus Points</label>
                                                    <input type="number" class="form-control" id="signup_bonus"
                                                           name="signup_bonus" value="<?php echo $currentSettings['signup_bonus']; ?>" required>
                                                    <small class="form-text text-muted">Points awarded for new account registration</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="referral_bonus">Referral Bonus Points</label>
                                                    <input type="number" class="form-control" id="referral_bonus"
                                                           name="referral_bonus" value="<?php echo $currentSettings['referral_bonus']; ?>" required>
                                                    <small class="form-text text-muted">Points for successful referrals</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="review_bonus">Review Bonus Points</label>
                                                    <input type="number" class="form-control" id="review_bonus"
                                                           name="review_bonus" value="<?php echo $currentSettings['review_bonus']; ?>" required>
                                                    <small class="form-text text-muted">Points for product reviews</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="birthday_bonus">Birthday Bonus Points</label>
                                                    <input type="number" class="form-control" id="birthday_bonus"
                                                           name="birthday_bonus" value="<?php echo $currentSettings['birthday_bonus']; ?>" required>
                                                    <small class="form-text text-muted">Annual birthday bonus points</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="settings-section">
                                        <h5><i class="fas fa-toggle-on mr-2"></i>Feature Toggles</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="enable_tier_system"
                                                               name="enable_tier_system" <?php echo $currentSettings['enable_tier_system'] ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="enable_tier_system">Enable Tier System</label>
                                                    </div>
                                                    <small class="form-text text-muted">Bronze, Silver, Gold, Platinum tiers</small>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="enable_referrals"
                                                               name="enable_referrals" <?php echo $currentSettings['enable_referrals'] ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="enable_referrals">Enable Referral Program</label>
                                                    </div>
                                                    <small class="form-text text-muted">Allow customers to refer friends</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="enable_reviews"
                                                               name="enable_reviews" <?php echo $currentSettings['enable_reviews'] ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="enable_reviews">Enable Review Rewards</label>
                                                    </div>
                                                    <small class="form-text text-muted">Points for product reviews</small>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="enable_birthday"
                                                               name="enable_birthday" <?php echo $currentSettings['enable_birthday'] ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="enable_birthday">Enable Birthday Rewards</label>
                                                    </div>
                                                    <small class="form-text text-muted">Annual birthday bonus</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Save General Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Tier Settings -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    Customer Tier Settings
                                </h3>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_tiers">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Tier Thresholds (Lifetime Points)</h6>
                                            <div class="form-group">
                                                <label for="bronze_threshold">Bronze Tier</label>
                                                <input type="number" class="form-control" id="bronze_threshold"
                                                       name="bronze_threshold" value="<?php echo $currentSettings['bronze_threshold']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="silver_threshold">Silver Tier</label>
                                                <input type="number" class="form-control" id="silver_threshold"
                                                       name="silver_threshold" value="<?php echo $currentSettings['silver_threshold']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="gold_threshold">Gold Tier</label>
                                                <input type="number" class="form-control" id="gold_threshold"
                                                       name="gold_threshold" value="<?php echo $currentSettings['gold_threshold']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="platinum_threshold">Platinum Tier</label>
                                                <input type="number" class="form-control" id="platinum_threshold"
                                                       name="platinum_threshold" value="<?php echo $currentSettings['platinum_threshold']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Points Multipliers</h6>
                                            <div class="form-group">
                                                <label for="bronze_multiplier">Bronze Multiplier</label>
                                                <input type="number" step="0.1" class="form-control" id="bronze_multiplier"
                                                       name="bronze_multiplier" value="<?php echo $currentSettings['bronze_multiplier']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="silver_multiplier">Silver Multiplier</label>
                                                <input type="number" step="0.1" class="form-control" id="silver_multiplier"
                                                       name="silver_multiplier" value="<?php echo $currentSettings['silver_multiplier']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="gold_multiplier">Gold Multiplier</label>
                                                <input type="number" step="0.1" class="form-control" id="gold_multiplier"
                                                       name="gold_multiplier" value="<?php echo $currentSettings['gold_multiplier']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="platinum_multiplier">Platinum Multiplier</label>
                                                <input type="number" step="0.1" class="form-control" id="platinum_multiplier"
                                                       name="platinum_multiplier" value="<?php echo $currentSettings['platinum_multiplier']; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save mr-1"></i> Save Tier Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tier Preview -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-eye mr-1"></i>
                                    Tier Preview
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="tier-preview tier-bronze">
                                    <h6><i class="fas fa-medal mr-1"></i> Bronze Tier</h6>
                                    <small>
                                        <?php echo number_format($currentSettings['bronze_threshold']); ?>+ points<br>
                                        <?php echo $currentSettings['bronze_multiplier']; ?>x points multiplier
                                    </small>
                                </div>
                                <div class="tier-preview tier-silver">
                                    <h6><i class="fas fa-medal mr-1"></i> Silver Tier</h6>
                                    <small>
                                        <?php echo number_format($currentSettings['silver_threshold']); ?>+ points<br>
                                        <?php echo $currentSettings['silver_multiplier']; ?>x points multiplier
                                    </small>
                                </div>
                                <div class="tier-preview tier-gold">
                                    <h6><i class="fas fa-medal mr-1"></i> Gold Tier</h6>
                                    <small>
                                        <?php echo number_format($currentSettings['gold_threshold']); ?>+ points<br>
                                        <?php echo $currentSettings['gold_multiplier']; ?>x points multiplier
                                    </small>
                                </div>
                                <div class="tier-preview tier-platinum">
                                    <h6><i class="fas fa-medal mr-1"></i> Platinum Tier</h6>
                                    <small>
                                        <?php echo number_format($currentSettings['platinum_threshold']); ?>+ points<br>
                                        <?php echo $currentSettings['platinum_multiplier']; ?>x points multiplier
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Quick Stats
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php
                                // Get quick stats
                                $totalCustomers = 0;
                                $totalPoints = 0;
                                $activeRewards = 0;

                                $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_points");
                                if ($result) {
                                    $totalCustomers = $result->fetch_assoc()['count'];
                                }

                                $result = $mysqli->query("SELECT SUM(total_points) as total FROM customer_points");
                                if ($result) {
                                    $totalPoints = $result->fetch_assoc()['total'] ?? 0;
                                }

                                $result = $mysqli->query("SELECT COUNT(*) as count FROM rewards_catalog WHERE is_active = 1");
                                if ($result) {
                                    $activeRewards = $result->fetch_assoc()['count'];
                                }
                                ?>
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Customers</span>
                                        <span class="info-box-number"><?php echo number_format($totalCustomers); ?></span>
                                    </div>
                                </div>
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-coins"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Points</span>
                                        <span class="info-box-number"><?php echo number_format($totalPoints); ?></span>
                                    </div>
                                </div>
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-gift"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Active Rewards</span>
                                        <span class="info-box-number"><?php echo number_format($activeRewards); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include("components/footer.php"); ?>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
// Real-time tier preview updates
$(document).ready(function() {
    function updateTierPreviews() {
        var bronze = parseInt($('#bronze_threshold').val()) || 0;
        var silver = parseInt($('#silver_threshold').val()) || 0;
        var gold = parseInt($('#gold_threshold').val()) || 0;
        var platinum = parseInt($('#platinum_threshold').val()) || 0;

        var bronzeMultiplier = parseFloat($('#bronze_multiplier').val()) || 1.0;
        var silverMultiplier = parseFloat($('#silver_multiplier').val()) || 1.0;
        var goldMultiplier = parseFloat($('#gold_multiplier').val()) || 1.0;
        var platinumMultiplier = parseFloat($('#platinum_multiplier').val()) || 1.0;

        $('.tier-bronze small').html(bronze.toLocaleString() + '+ points<br>' + bronzeMultiplier + 'x points multiplier');
        $('.tier-silver small').html(silver.toLocaleString() + '+ points<br>' + silverMultiplier + 'x points multiplier');
        $('.tier-gold small').html(gold.toLocaleString() + '+ points<br>' + goldMultiplier + 'x points multiplier');
        $('.tier-platinum small').html(platinum.toLocaleString() + '+ points<br>' + platinumMultiplier + 'x points multiplier');
    }

    // Update previews when values change
    $('input[name$="_threshold"], input[name$="_multiplier"]').on('input', updateTierPreviews);
});
</script>

</body>
</html>

<?php
/**
 * My Nutrify Rewards System
 * Handles dynamic points, rewards, and referrals
 */

class RewardsSystem {
    private $mysqli;
    private $config;
    
    public function __construct() {
        // Include database configuration
        if (!defined('HOST')) {
            include_once __DIR__ . '/../database/dbdetails.php';
        }
        
        $this->mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
        if ($this->mysqli->connect_error) {
            throw new Exception("Database connection failed: " . $this->mysqli->connect_error);
        }
        $this->mysqli->set_charset("utf8mb4");
        
        // Load configuration
        $this->loadConfig();
    }
    
    /**
     * Load points configuration from database
     */
    private function loadConfig() {
        $this->config = [];

        try {
            // Check if points_config table exists
            $tableCheck = $this->mysqli->query("SHOW TABLES LIKE 'points_config'");
            if ($tableCheck && $tableCheck->num_rows > 0) {
                $query = "SELECT config_key, config_value FROM points_config";
                $result = $this->mysqli->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $this->config[$row['config_key']] = $row['config_value'];
                    }
                }
            }
        } catch (Exception $e) {
            // If table doesn't exist or query fails, we'll use defaults
            error_log("Points config table not found, using defaults: " . $e->getMessage());
        }
        
        // Set defaults if not found
        $defaults = [
            'points_per_rupee' => 3,
            'signup_bonus_points' => 25,
            'review_points' => 25,
            'referral_points_referrer' => 100,
            'referral_points_referred' => 50,
            'points_expiry_months' => 12,
            'min_points_redemption' => 100
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset($this->config[$key])) {
                $this->config[$key] = $value;
            }
        }
    }
    
    /**
     * Get customer's current points balance
     */
    public function getCustomerPoints($customerId) {
        try {
            // Check if customer_points table exists
            $tableCheck = $this->mysqli->query("SHOW TABLES LIKE 'customer_points'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                // Table doesn't exist, return default values
                return [
                    'total_points' => 0,
                    'lifetime_points' => 0,
                    'points_redeemed' => 0,
                    'tier_level' => 'Bronze'
                ];
            }

            $query = "SELECT total_points, lifetime_points, points_redeemed, tier_level
                      FROM customer_points WHERE customer_id = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                return $row;
            }

            // Create new points record if doesn't exist
            $this->createCustomerPointsRecord($customerId);
            return [
                'total_points' => 0,
                'lifetime_points' => 0,
                'points_redeemed' => 0,
                'tier_level' => 'Bronze'
            ];
        } catch (Exception $e) {
            error_log("Error getting customer points: " . $e->getMessage());
            return [
                'total_points' => 0,
                'lifetime_points' => 0,
                'points_redeemed' => 0,
                'tier_level' => 'Bronze'
            ];
        }
    }
    
    /**
     * Create initial points record for new customer
     */
    private function createCustomerPointsRecord($customerId) {
        $query = "INSERT INTO customer_points (customer_id, total_points, lifetime_points) 
                  VALUES (?, 0, 0) ON DUPLICATE KEY UPDATE customer_id = customer_id";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("i", $customerId);
        return $stmt->execute();
    }
    
    /**
     * Award points to customer
     */
    public function awardPoints($customerId, $points, $description, $referenceType = null, $referenceId = null, $orderId = null) {
        try {
            $this->mysqli->begin_transaction();
            
            // Create points record if doesn't exist
            $this->createCustomerPointsRecord($customerId);
            
            // Add points transaction
            $query = "INSERT INTO points_transactions
                      (customer_id, transaction_type, points, description, order_id)
                      VALUES (?, 'earned', ?, ?, ?)";
            $stmt = $this->mysqli->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare transaction query: " . $this->mysqli->error);
            }
            $stmt->bind_param("iiss", $customerId, $points, $description, $orderId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert points transaction: " . $stmt->error);
            }
            
            // Update customer points balance (simplified)
            $query = "UPDATE customer_points
                      SET total_points = total_points + ?,
                          lifetime_points = lifetime_points + ?
                      WHERE customer_id = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("iii", $points, $points, $customerId);

            if (!$stmt->execute()) {
                throw new Exception("Failed to update customer points: " . $stmt->error);
            }
            
            $this->mysqli->commit();
            return $points;
            
        } catch (Exception $e) {
            $this->mysqli->rollback();
            error_log("Error awarding points: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Award points for order purchase
     */
    public function awardOrderPoints($customerId, $orderId, $orderAmount) {
        $pointsPerRupee = floatval($this->config['points_per_rupee']);
        $orderAmount = floatval($orderAmount);
        $points = floor(($orderAmount / 100) * $pointsPerRupee);

        if ($points > 0) {
            $description = "Points earned for order #$orderId (₹$orderAmount)";
            return $this->awardPoints($customerId, $points, $description, 'order', $orderId, $orderId);
        }
        return 0;
    }
    
    /**
     * Award signup bonus points
     */
    public function awardSignupBonus($customerId) {
        $points = $this->config['signup_bonus_points'];
        $description = "Welcome bonus for joining My Nutrify!";
        return $this->awardPoints($customerId, $points, $description, 'signup', $customerId);
    }
    
    /**
     * Award review points
     */
    public function awardReviewPoints($customerId, $productId, $reviewId = null) {
        $points = $this->config['review_points'];
        $description = "Points for writing a product review";
        return $this->awardPoints($customerId, $points, $description, 'review', $reviewId ?? $productId);
    }
    
    /**
     * Get customer's points transaction history
     */
    public function getPointsHistory($customerId, $limit = 20) {
        try {
            // Check if points_transactions table exists
            $tableCheck = $this->mysqli->query("SHOW TABLES LIKE 'points_transactions'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                return []; // Table doesn't exist, return empty array
            }

            $query = "SELECT transaction_type, points_amount, description, reference_type,
                             reference_id, order_id, created_at, expiry_date
                      FROM points_transactions
                      WHERE customer_id = ?
                      ORDER BY created_at DESC
                      LIMIT ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("ii", $customerId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            $history = [];
            while ($row = $result->fetch_assoc()) {
                $history[] = $row;
            }
            return $history;
        } catch (Exception $e) {
            error_log("Error getting points history: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get available rewards for customer
     */
    public function getAvailableRewards($customerId) {
        try {
            // Check if rewards_catalog table exists
            $tableCheck = $this->mysqli->query("SHOW TABLES LIKE 'rewards_catalog'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                // Table doesn't exist, return empty array
                return [];
            }

            $customerPoints = $this->getCustomerPoints($customerId);
            $currentPoints = $customerPoints['total_points'];

            $query = "SELECT id, reward_name, reward_type, reward_value, points_required
                      FROM rewards_catalog
                      WHERE points_required <= ?
                      ORDER BY points_required ASC";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $currentPoints);
            $stmt->execute();
            $result = $stmt->get_result();

            $rewards = [];
            while ($row = $result->fetch_assoc()) {
                $row['can_redeem'] = $currentPoints >= $row['points_required'];
                $rewards[] = $row;
            }
            return $rewards;
        } catch (Exception $e) {
            error_log("Error getting available rewards: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate unique referral code for customer
     */
    public function generateReferralCode($customerId) {
        try {
            // Check if table exists first
            $tableCheck = "SHOW TABLES LIKE 'customer_referrals'";
            $result = $this->mysqli->query($tableCheck);

            if ($result->num_rows == 0) {
                // Table doesn't exist, return a simple referral code
                return 'NUT' . str_pad($customerId, 6, '0', STR_PAD_LEFT);
            }

            $code = 'NUT' . strtoupper(substr(md5($customerId . time()), 0, 6));

            // Check if code already exists
            $query = "SELECT id FROM customer_referrals WHERE referral_code = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("s", $code);
            $stmt->execute();

            if ($stmt->get_result()->num_rows > 0) {
                // Generate new code if exists
                return $this->generateReferralCode($customerId);
            }

            // Save referral code
            $query = "INSERT INTO customer_referrals (referrer_customer_id, referral_code) VALUES (?, ?)";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("is", $customerId, $code);
            $stmt->execute();

            return $code;
        } catch (Exception $e) {
            // Fallback to simple referral code if there's any error
            return 'NUT' . str_pad($customerId, 6, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Get customer's referral code
     */
    public function getCustomerReferralCode($customerId) {
        try {
            // Check if table exists first
            $tableCheck = "SHOW TABLES LIKE 'customer_referrals'";
            $result = $this->mysqli->query($tableCheck);

            if ($result->num_rows == 0) {
                // Table doesn't exist, return a simple referral code
                return 'REF' . str_pad($customerId, 6, '0', STR_PAD_LEFT);
            }

            $query = "SELECT referral_code FROM customer_referrals
                      WHERE referrer_customer_id = ?
                      ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                return $row['referral_code'];
            }

            // Generate new code if none exists
            return $this->generateReferralCode($customerId);
        } catch (Exception $e) {
            // Fallback to simple referral code if there's any error
            return 'REF' . str_pad($customerId, 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Redeem points for rewards
     */
    public function redeemPoints($customerId, $points, $description, $referenceType = null, $referenceId = null) {
        try {
            $this->mysqli->begin_transaction();

            // Check if customer has enough points
            $customerPoints = $this->getCustomerPoints($customerId);
            if ($customerPoints['total_points'] < $points) {
                throw new Exception("Insufficient points");
            }

            // Add redemption transaction
            $query = "INSERT INTO points_transactions
                      (customer_id, transaction_type, points, description)
                      VALUES (?, 'redeemed', ?, ?)";
            $stmt = $this->mysqli->prepare($query);
            $negativePoints = -$points; // Store as negative for redemption
            $stmt->bind_param("iis", $customerId, $negativePoints, $description);
            $stmt->execute();

            // Update customer points balance
            $query = "UPDATE customer_points
                      SET total_points = total_points - ?,
                          points_redeemed = points_redeemed + ?
                      WHERE customer_id = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("iii", $points, $points, $customerId);
            $stmt->execute();

            $this->mysqli->commit();
            return true;

        } catch (Exception $e) {
            $this->mysqli->rollback();
            error_log("Error redeeming points: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Close database connection
     */
    public function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }
}
?>

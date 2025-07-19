<?php
/**
 * Enhanced Coupon System Class
 * Integrates with RewardsSystem for comprehensive coupon and rewards management
 */

class CouponSystem {
    private $mysqli;
    private $rewardsSystem;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
        require_once 'RewardsSystem.php';
        $this->rewardsSystem = new RewardsSystem($mysqli);
    }
    
    /**
     * Validate and apply coupon to order
     */
    public function validateAndApplyCoupon($couponCode, $customerId, $orderAmount, $cartItems = []) {
        try {
            // Get coupon details
            $coupon = $this->getCouponByCode($couponCode);
            if (!$coupon) {
                return ['success' => false, 'message' => 'Invalid coupon code'];
            }
            
            // Validate coupon
            $validation = $this->validateCoupon($coupon, $customerId, $orderAmount, $cartItems);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Calculate discount
            $discount = $this->calculateDiscount($coupon, $orderAmount);
            
            return [
                'success' => true,
                'coupon_id' => $coupon['id'],
                'coupon_code' => $coupon['coupon_code'],
                'discount_amount' => $discount,
                'message' => "Coupon applied successfully! You saved ₹{$discount}"
            ];
            
        } catch (Exception $e) {
            error_log("Error applying coupon: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error applying coupon. Please try again.'];
        }
    }
    
    /**
     * Get coupon by code
     */
    private function getCouponByCode($couponCode) {
        try {
            // Check if enhanced_coupons table exists
            $tableCheck = $this->mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                return null; // Table doesn't exist
            }

            $query = "SELECT * FROM enhanced_coupons WHERE coupon_code = ? AND is_active = 1";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("s", $couponCode);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error getting coupon by code: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Validate coupon against various criteria
     */
    private function validateCoupon($coupon, $customerId, $orderAmount, $cartItems = []) {
        // Check validity dates
        $now = date('Y-m-d H:i:s');
        if ($now < $coupon['valid_from'] || $now > $coupon['valid_until']) {
            return ['valid' => false, 'message' => 'Coupon has expired'];
        }
        
        // Check minimum order amount
        if ($orderAmount < $coupon['minimum_order_amount']) {
            return ['valid' => false, 'message' => "Minimum order amount ₹{$coupon['minimum_order_amount']} required"];
        }
        
        // Check total usage limit
        if ($coupon['usage_limit_total'] && $coupon['current_usage_count'] >= $coupon['usage_limit_total']) {
            return ['valid' => false, 'message' => 'Coupon usage limit exceeded'];
        }
        
        // Check customer usage limit
        $customerUsage = $this->getCustomerCouponUsage($coupon['id'], $customerId);
        if ($customerUsage >= $coupon['usage_limit_per_customer']) {
            return ['valid' => false, 'message' => 'You have already used this coupon maximum times'];
        }
        
        // Check customer type restrictions
        if (!$this->validateCustomerType($coupon, $customerId)) {
            return ['valid' => false, 'message' => 'This coupon is not available for your account type'];
        }
        
        // Check tier restrictions
        if (!$this->validateCustomerTier($coupon, $customerId)) {
            return ['valid' => false, 'message' => 'This coupon is not available for your membership tier'];
        }
        
        // Check if customer has this coupon in wallet (for reward coupons)
        if ($coupon['is_reward_coupon'] && !$this->customerHasCouponInWallet($coupon['id'], $customerId)) {
            return ['valid' => false, 'message' => 'You need to redeem this coupon from rewards first'];
        }
        
        return ['valid' => true, 'message' => 'Coupon is valid'];
    }
    
    /**
     * Calculate discount amount
     */
    private function calculateDiscount($coupon, $orderAmount) {
        if ($coupon['discount_type'] === 'fixed') {
            return min($coupon['discount_value'], $orderAmount);
        } else { // percentage
            $discount = ($orderAmount * $coupon['discount_value']) / 100;
            if ($coupon['max_discount_amount']) {
                $discount = min($discount, $coupon['max_discount_amount']);
            }
            return round($discount, 2);
        }
    }
    
    /**
     * Get customer's usage count for a specific coupon
     */
    private function getCustomerCouponUsage($couponId, $customerId) {
        $query = "SELECT COUNT(*) as usage_count FROM coupon_usage WHERE coupon_id = ? AND customer_id = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("ii", $couponId, $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['usage_count'];
    }
    
    /**
     * Validate customer type restrictions
     */
    private function validateCustomerType($coupon, $customerId) {
        if ($coupon['customer_type'] === 'all') {
            return true;
        }
        
        if ($coupon['customer_type'] === 'specific') {
            $specificCustomers = json_decode($coupon['specific_customers'], true);
            return in_array($customerId, $specificCustomers);
        }
        
        if ($coupon['customer_type'] === 'new') {
            // Check if customer has any previous orders
            $query = "SELECT COUNT(*) as order_count FROM order_master WHERE CustomerId = ? AND OrderStatus != 'Cancelled'";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['order_count'] == 0;
        }
        
        return true; // existing customers
    }
    
    /**
     * Validate customer tier restrictions
     */
    private function validateCustomerTier($coupon, $customerId) {
        if (!$coupon['tier_restrictions']) {
            return true;
        }
        
        $allowedTiers = json_decode($coupon['tier_restrictions'], true);
        $customerPoints = $this->rewardsSystem->getCustomerPoints($customerId);
        return in_array($customerPoints['tier_level'], $allowedTiers);
    }
    
    /**
     * Check if customer has coupon in wallet
     */
    private function customerHasCouponInWallet($couponId, $customerId) {
        $query = "SELECT id FROM customer_coupons WHERE coupon_id = ? AND customer_id = ? AND status = 'active'";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("ii", $couponId, $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    /**
     * Record coupon usage
     */
    public function recordCouponUsage($couponId, $customerId, $orderId, $discountApplied, $orderAmount) {
        try {
            $this->mysqli->begin_transaction();
            
            // Insert usage record
            $query = "INSERT INTO coupon_usage (coupon_id, customer_id, order_id, discount_applied, order_amount, customer_ip) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($query);
            $customerIp = $_SERVER['REMOTE_ADDR'] ?? null;
            $stmt->bind_param("iisdds", $couponId, $customerId, $orderId, $discountApplied, $orderAmount, $customerIp);
            $stmt->execute();
            
            // Update coupon usage count
            $query = "UPDATE enhanced_coupons SET current_usage_count = current_usage_count + 1 WHERE id = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $couponId);
            $stmt->execute();
            
            // Mark coupon as used in customer wallet if it's a reward coupon
            $query = "UPDATE customer_coupons SET status = 'used', used_at = NOW(), order_id = ? 
                      WHERE coupon_id = ? AND customer_id = ? AND status = 'active'";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("sii", $orderId, $couponId, $customerId);
            $stmt->execute();
            
            $this->mysqli->commit();
            return true;
            
        } catch (Exception $e) {
            $this->mysqli->rollback();
            error_log("Error recording coupon usage: " . $e->getMessage());
            return false;
        }
    }
    


    /**
     * Redeem points for coupon
     */
    public function redeemPointsForCoupon($customerId, $rewardId) {
        try {
            // Get reward details
            $query = "SELECT * FROM rewards_catalog WHERE id = ? AND is_active = 1 AND reward_type = 'coupon'";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $rewardId);
            $stmt->execute();
            $result = $stmt->get_result();
            $reward = $result->fetch_assoc();

            if (!$reward) {
                return ['success' => false, 'message' => 'Invalid reward'];
            }

            // Check if customer has enough points
            $customerPoints = $this->rewardsSystem->getCustomerPoints($customerId);
            if ($customerPoints['total_points'] < $reward['points_required']) {
                return ['success' => false, 'message' => 'Insufficient points'];
            }

            $this->mysqli->begin_transaction();

            // Generate unique coupon code
            $couponCode = $this->generateUniqueCouponCode();

            // Create coupon
            $expiryDate = date('Y-m-d H:i:s', strtotime('+30 days'));
            $query = "INSERT INTO enhanced_coupons
                      (coupon_code, coupon_name, description, discount_type, discount_value,
                       minimum_order_amount, usage_limit_per_customer, valid_from, valid_until,
                       points_required, is_reward_coupon, reward_catalog_id, is_active, created_by)
                      VALUES (?, ?, ?, 'fixed', ?, ?, 1, NOW(), ?, ?, 1, ?, 1, 'customer_redemption')";

            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("sssddsiii",
                $couponCode, $reward['reward_name'], $reward['reward_description'],
                $reward['reward_value'], $reward['minimum_order_amount'], $expiryDate,
                $reward['points_required'], $rewardId
            );
            $stmt->execute();
            $couponId = $this->mysqli->insert_id;

            // Add to customer wallet
            $query = "INSERT INTO customer_coupons (customer_id, coupon_id, redeemed_from_points, points_used, expires_at)
                      VALUES (?, ?, 1, ?, ?)";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("iiis", $customerId, $couponId, $reward['points_required'], $expiryDate);
            $stmt->execute();

            // Deduct points using rewards system
            $this->rewardsSystem->redeemPoints($customerId, $reward['points_required'],
                "Redeemed for {$reward['reward_name']}", 'coupon', $couponId);

            $this->mysqli->commit();

            return [
                'success' => true,
                'coupon_code' => $couponCode,
                'message' => "Coupon redeemed successfully! Your coupon code is: {$couponCode}"
            ];

        } catch (Exception $e) {
            $this->mysqli->rollback();
            error_log("Error redeeming points for coupon: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error redeeming coupon. Please try again.'];
        }
    }

    /**
     * Generate unique coupon code
     */
    private function generateUniqueCouponCode($prefix = 'RWD') {
        do {
            $code = $prefix . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $query = "SELECT id FROM enhanced_coupons WHERE coupon_code = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
        } while ($result->num_rows > 0);

        return $code;
    }



    /**
     * Get customer's wallet coupons
     */
    public function getCustomerWalletCoupons($customerId) {
        try {
            // Check if required tables exist
            $tableCheck1 = $this->mysqli->query("SHOW TABLES LIKE 'customer_coupons'");
            $tableCheck2 = $this->mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");

            if (!$tableCheck1 || $tableCheck1->num_rows == 0 ||
                !$tableCheck2 || $tableCheck2->num_rows == 0) {
                return []; // Tables don't exist
            }

            $query = "SELECT cc.*, c.coupon_code, c.coupon_name, c.description,
                             c.discount_type, c.discount_value, c.minimum_order_amount, c.valid_until
                      FROM customer_coupons cc
                      JOIN enhanced_coupons c ON cc.coupon_id = c.id
                      WHERE cc.customer_id = ? AND cc.status = 'active'
                        AND (cc.expires_at IS NULL OR cc.expires_at > NOW())
                        AND c.valid_until > NOW()
                      ORDER BY cc.redemption_date DESC";

            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();

            $coupons = [];
            while ($row = $result->fetch_assoc()) {
                $coupons[] = $row;
            }

            return $coupons;
        } catch (Exception $e) {
            error_log("Error getting customer wallet coupons: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get customer's coupon history
     */
    public function getCustomerCouponHistory($customerId) {
        try {
            // Check if required tables exist
            $tableCheck1 = $this->mysqli->query("SHOW TABLES LIKE 'coupon_usage'");
            $tableCheck2 = $this->mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");

            if (!$tableCheck1 || $tableCheck1->num_rows == 0 ||
                !$tableCheck2 || $tableCheck2->num_rows == 0) {
                return []; // Tables don't exist
            }

            $query = "SELECT cu.*, c.coupon_code, c.coupon_name, c.discount_type, c.discount_value
                      FROM coupon_usage cu
                      JOIN enhanced_coupons c ON cu.coupon_id = c.id
                      WHERE cu.customer_id = ?
                      ORDER BY cu.used_at DESC";

            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();

            $history = [];
            while ($row = $result->fetch_assoc()) {
                $history[] = $row;
            }

            return $history;
        } catch (Exception $e) {
            error_log("Error getting customer coupon history: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get available coupons for customer
     */
    public function getAvailableCoupons($customerId, $orderAmount = 0) {
        try {
            // Check if required tables exist
            $tableCheck = $this->mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                return []; // Table doesn't exist
            }

            $query = "SELECT c.*,
                             CASE WHEN cc.id IS NOT NULL THEN 'in_wallet' ELSE 'public' END as coupon_source
                      FROM enhanced_coupons c
                      LEFT JOIN customer_coupons cc ON c.id = cc.coupon_id AND cc.customer_id = ? AND cc.status = 'active'
                      WHERE c.is_active = 1
                        AND c.valid_from <= NOW()
                        AND c.valid_until >= NOW()
                        AND (c.minimum_order_amount <= ? OR ? = 0)
                        AND (c.is_reward_coupon = 0 OR cc.id IS NOT NULL)
                      ORDER BY c.discount_value DESC";

            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("idd", $customerId, $orderAmount, $orderAmount);
            $stmt->execute();
            $result = $stmt->get_result();

            $coupons = [];
            while ($row = $result->fetch_assoc()) {
                $coupons[] = $row;
            }

            return $coupons;
        } catch (Exception $e) {
            error_log("Error getting available coupons: " . $e->getMessage());
            return [];
        }
    }
}

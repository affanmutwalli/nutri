<?php
/**
 * Order Completion Hooks
 * Handles points awarding and coupon usage recording when orders are completed
 */

require_once 'RewardsSystem.php';
require_once 'CouponSystem.php';

class OrderCompletionHooks {
    private $mysqli;
    private $rewardsSystem;
    private $couponSystem;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
        $this->rewardsSystem = new RewardsSystem($mysqli);
        $this->couponSystem = new CouponSystem($mysqli);
    }
    
    /**
     * Process order completion - award points and record coupon usage
     */
    public function processOrderCompletion($orderId, $customerId = null, $customerType = 'Registered') {
        try {
            // Get order details
            $orderDetails = $this->getOrderDetails($orderId);
            if (!$orderDetails) {
                error_log("Order not found: $orderId");
                return false;
            }
            
            // Only process for registered customers
            if ($customerType !== 'Registered' || !$customerId) {
                error_log("Skipping rewards/coupon processing for non-registered customer: $orderId");
                return true;
            }
            
            $this->mysqli->begin_transaction();
            
            // Award points for the order
            $pointsAwarded = $this->awardOrderPoints($orderId, $customerId, $orderDetails);
            
            // Record coupon usage if coupon was applied
            $couponRecorded = $this->recordCouponUsage($orderId, $customerId, $orderDetails);
            
            // Award signup bonus if this is customer's first order
            $signupBonusAwarded = $this->awardSignupBonusIfFirstOrder($customerId);
            
            $this->mysqli->commit();
            
            error_log("Order completion processed for $orderId: Points=$pointsAwarded, Coupon=$couponRecorded, SignupBonus=$signupBonusAwarded");
            return true;
            
        } catch (Exception $e) {
            $this->mysqli->rollback();
            error_log("Error processing order completion for $orderId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get order details
     */
    private function getOrderDetails($orderId) {
        $query = "SELECT OrderId, CustomerId, CustomerType, Amount, PaymentStatus, OrderStatus, 
                         CouponCode, CouponDiscount, CreatedAt
                  FROM order_master 
                  WHERE OrderId = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Award points for order
     */
    private function awardOrderPoints($orderId, $customerId, $orderDetails) {
        try {
            // Check if points already awarded for this order
            $query = "SELECT id FROM points_transactions 
                      WHERE customer_id = ? AND order_id = ? AND transaction_type = 'earned'";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("is", $customerId, $orderId);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                error_log("Points already awarded for order: $orderId");
                return false;
            }
            
            // Award points based on order amount
            $orderAmount = floatval($orderDetails['Amount']);
            $success = $this->rewardsSystem->awardOrderPoints($customerId, $orderAmount, $orderId);
            
            if ($success) {
                error_log("Points awarded for order $orderId: Customer $customerId, Amount ₹$orderAmount");
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error awarding points for order $orderId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Record coupon usage
     */
    private function recordCouponUsage($orderId, $customerId, $orderDetails) {
        try {
            // Check if coupon was used
            if (empty($orderDetails['CouponCode']) || empty($orderDetails['CouponDiscount'])) {
                return true; // No coupon used, nothing to record
            }
            
            // Check if coupon usage already recorded
            $query = "SELECT id FROM coupon_usage WHERE order_id = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("s", $orderId);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                error_log("Coupon usage already recorded for order: $orderId");
                return false;
            }
            
            // Get coupon details
            $couponCode = $orderDetails['CouponCode'];
            $discountAmount = floatval($orderDetails['CouponDiscount']);
            $orderAmount = floatval($orderDetails['Amount']);
            
            // Try enhanced coupons first
            $query = "SELECT id FROM enhanced_coupons WHERE coupon_code = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("s", $couponCode);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($coupon = $result->fetch_assoc()) {
                // Record usage in enhanced coupon system
                $success = $this->couponSystem->recordCouponUsage(
                    $coupon['id'], 
                    $customerId, 
                    $orderId, 
                    $discountAmount, 
                    $orderAmount
                );
                
                if ($success) {
                    error_log("Enhanced coupon usage recorded for order $orderId: $couponCode, Discount ₹$discountAmount");
                    return true;
                }
            } else {
                // Check if it's a rewards coupon
                $rewardsQuery = "SELECT * FROM coupons WHERE coupon_code = ? AND is_active = 1";
                $rewardsStmt = $this->mysqli->prepare($rewardsQuery);
                $rewardsStmt->bind_param("s", $couponCode);
                $rewardsStmt->execute();
                $rewardsResult = $rewardsStmt->get_result();

                if ($rewardsCoupon = $rewardsResult->fetch_assoc()) {
                    // Mark rewards coupon as used
                    $updateQuery = "UPDATE coupons SET is_used = 1, used_at = NOW() WHERE coupon_code = ?";
                    $updateStmt = $this->mysqli->prepare($updateQuery);
                    $updateStmt->bind_param("s", $couponCode);
                    $updateStmt->execute();

                    error_log("Rewards coupon marked as used for order $orderId: $couponCode");
                    return true;
                } else {
                    // Record usage for legacy coupon system
                    $query = "UPDATE coupons SET UsedCount = COALESCE(UsedCount, 0) + 1 WHERE Code = ?";
                    $stmt = $this->mysqli->prepare($query);
                    $stmt->bind_param("s", $couponCode);
                    $stmt->execute();

                    error_log("Legacy coupon usage recorded for order $orderId: $couponCode");
                    return true;
                }
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error recording coupon usage for order $orderId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Award signup bonus if this is customer's first order
     */
    private function awardSignupBonusIfFirstOrder($customerId) {
        try {
            // Check if signup bonus already awarded
            $query = "SELECT id FROM points_transactions 
                      WHERE customer_id = ? AND reference_type = 'signup'";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                return false; // Already awarded
            }
            
            // Check if this is the first completed order
            $query = "SELECT COUNT(*) as order_count FROM order_master 
                      WHERE CustomerId = ? AND CustomerType = 'Registered' 
                      AND OrderStatus IN ('Confirmed', 'Shipped', 'Delivered')";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['order_count'] == 1) {
                // This is the first order, award signup bonus
                $success = $this->rewardsSystem->awardSignupBonus($customerId);
                
                if ($success) {
                    error_log("Signup bonus awarded to customer: $customerId");
                    return true;
                }
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error awarding signup bonus for customer $customerId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process order when payment is confirmed (for online orders)
     */
    public function processPaymentConfirmation($orderId) {
        try {
            // Get order details
            $orderDetails = $this->getOrderDetails($orderId);
            if (!$orderDetails) {
                return false;
            }
            
            // Only process if payment is confirmed and customer is registered
            if ($orderDetails['PaymentStatus'] === 'Paid' && $orderDetails['CustomerType'] === 'Registered') {
                return $this->processOrderCompletion(
                    $orderId, 
                    $orderDetails['CustomerId'], 
                    $orderDetails['CustomerType']
                );
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error processing payment confirmation for $orderId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process order when it's placed (for COD orders)
     */
    public function processOrderPlacement($orderId) {
        try {
            // Get order details
            $orderDetails = $this->getOrderDetails($orderId);
            if (!$orderDetails) {
                return false;
            }
            
            // Process for COD orders or already paid orders
            if ($orderDetails['CustomerType'] === 'Registered') {
                return $this->processOrderCompletion(
                    $orderId, 
                    $orderDetails['CustomerId'], 
                    $orderDetails['CustomerType']
                );
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error processing order placement for $orderId: " . $e->getMessage());
            return false;
        }
    }
}
?>

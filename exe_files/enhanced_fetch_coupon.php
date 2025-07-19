<?php
/**
 * Enhanced Coupon Validation and Application
 * Integrates with the new CouponSystem and RewardsSystem
 */

session_start();
require_once '../database/dbconnection.php';
require_once '../includes/CouponSystem.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['code']) || empty(trim($data['code']))) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter a coupon code",
            "response" => "E"
        ]);
        exit;
    }
    
    $couponCode = trim($data['code']);
    $orderAmount = isset($data['order_amount']) ? floatval($data['order_amount']) : 0;
    $customerId = isset($_SESSION['CustomerId']) ? intval($_SESSION['CustomerId']) : null;
    
    // Initialize coupon system
    $couponSystem = new CouponSystem($mysqli);
    
    // If customer is not logged in, try basic coupon validation for guest users
    if (!$customerId) {
        // For guest users, only allow non-reward coupons
        $result = validateGuestCoupon($couponCode, $orderAmount, $mysqli);
        echo json_encode($result);
        exit;
    }
    
    // For logged-in users, use full coupon system
    $result = $couponSystem->validateAndApplyCoupon($couponCode, $customerId, $orderAmount);
    
    if ($result['success']) {
        // Store coupon details in session for checkout
        $_SESSION['applied_coupon'] = [
            'coupon_id' => $result['coupon_id'],
            'coupon_code' => $result['coupon_code'],
            'discount_amount' => $result['discount_amount'],
            'applied_at' => time()
        ];
        
        echo json_encode([
            "success" => true,
            "message" => $result['message'],
            "discount" => $result['discount_amount'],
            "coupon_code" => $result['coupon_code'],
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
    error_log("Error in enhanced_fetch_coupon.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "An error occurred while processing the coupon. Please try again.",
        "response" => "E"
    ]);
}

/**
 * Basic coupon validation for guest users (fallback to old system)
 */
function validateGuestCoupon($couponCode, $orderAmount, $mysqli) {
    try {
        // Check enhanced coupons first
        $query = "SELECT * FROM enhanced_coupons 
                  WHERE coupon_code = ? 
                    AND is_active = 1 
                    AND is_reward_coupon = 0
                    AND valid_from <= NOW() 
                    AND valid_until >= NOW()
                    AND minimum_order_amount <= ?";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sd", $couponCode, $orderAmount);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($coupon = $result->fetch_assoc()) {
            // Calculate discount
            if ($coupon['discount_type'] === 'fixed') {
                $discount = min($coupon['discount_value'], $orderAmount);
            } else {
                $discount = ($orderAmount * $coupon['discount_value']) / 100;
                if ($coupon['max_discount_amount']) {
                    $discount = min($discount, $coupon['max_discount_amount']);
                }
            }
            
            return [
                "success" => true,
                "message" => "Coupon applied successfully! You saved ₹" . number_format($discount, 2),
                "discount" => $discount,
                "coupon_code" => $coupon['coupon_code'],
                "response" => "S"
            ];
        }
        
        // Fallback to old coupon system
        $query = "SELECT * FROM coupons WHERE Code = ? AND Status = 'Active'";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $couponCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($coupon = $result->fetch_assoc()) {
            $discount = floatval($coupon['Discount']);
            $minAmount = floatval($coupon['Above']);
            
            if ($orderAmount < $minAmount) {
                return [
                    "success" => false,
                    "message" => "Minimum order amount ₹{$minAmount} required",
                    "response" => "E"
                ];
            }
            
            return [
                "success" => true,
                "message" => "Coupon applied successfully! You saved ₹{$discount}",
                "discount" => $discount,
                "coupon_code" => $coupon['Code'],
                "response" => "S"
            ];
        }
        
        return [
            "success" => false,
            "message" => "Invalid or expired coupon code",
            "response" => "E"
        ];
        
    } catch (Exception $e) {
        error_log("Error in validateGuestCoupon: " . $e->getMessage());
        return [
            "success" => false,
            "message" => "Error validating coupon. Please try again.",
            "response" => "E"
        ];
    }
}
?>

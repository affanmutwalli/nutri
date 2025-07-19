<?php
/**
 * Simple integration file for order rewards and coupon processing
 * Include this file in order processing scripts to automatically handle rewards and coupons
 */

// Function to process order completion with rewards and coupons
function processOrderRewardsAndCoupons($orderId, $customerId = null, $customerType = 'Registered') {
    try {
        // Only process if the required classes exist
        if (!file_exists(__DIR__ . '/OrderCompletionHooks.php')) {
            error_log("OrderCompletionHooks.php not found, skipping rewards processing");
            return true;
        }

        // Initialize database connection
        require_once __DIR__ . '/../database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();

        if (!$mysqli) {
            error_log("Database connection failed in processOrderRewardsAndCoupons");
            return true; // Don't break order processing
        }

        require_once __DIR__ . '/OrderCompletionHooks.php';

        $hooks = new OrderCompletionHooks($mysqli);
        return $hooks->processOrderCompletion($orderId, $customerId, $customerType);

    } catch (Exception $e) {
        error_log("Error in processOrderRewardsAndCoupons: " . $e->getMessage());
        return true; // Don't break order processing
    }
}

// Function to process payment confirmation
function processPaymentConfirmationRewards($orderId) {
    try {
        if (!file_exists(__DIR__ . '/OrderCompletionHooks.php')) {
            return true;
        }

        // Initialize database connection
        require_once __DIR__ . '/../database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();

        if (!$mysqli) {
            error_log("Database connection failed in processPaymentConfirmationRewards");
            return true; // Don't break order processing
        }

        require_once __DIR__ . '/OrderCompletionHooks.php';

        $hooks = new OrderCompletionHooks($mysqli);
        return $hooks->processPaymentConfirmation($orderId);

    } catch (Exception $e) {
        error_log("Error in processPaymentConfirmationRewards: " . $e->getMessage());
        return true; // Don't break order processing
    }
}

// Function to process order placement
function processOrderPlacementRewards($orderId) {
    try {
        // Initialize database connection
        require_once __DIR__ . '/../database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();

        if (!$mysqli) {
            error_log("Database connection failed in processOrderPlacementRewards");
            return true; // Don't break order processing
        }

        // Process coupon usage tracking
        processCouponUsage($orderId, $mysqli);

        // Don't process rewards if tables don't exist yet
        if (!file_exists(__DIR__ . '/OrderCompletionHooks.php')) {
            return true;
        }

        require_once __DIR__ . '/OrderCompletionHooks.php';

        $hooks = new OrderCompletionHooks($mysqli);
        return $hooks->processOrderPlacement($orderId);

    } catch (Exception $e) {
        error_log("Error in processOrderPlacementRewards: " . $e->getMessage());
        return true; // Don't break order processing
    }
}

// Function to track coupon usage
function processCouponUsage($orderId, $mysqli) {
    try {
        if (!isset($_SESSION['applied_coupon'])) {
            return true;
        }

        $couponData = $_SESSION['applied_coupon'];
        $couponCode = $couponData['coupon_code'];
        $discount = $couponData['discount'];
        $couponId = $couponData['coupon_id'];

        // Get order details
        $orderQuery = "SELECT CustomerId, FinalTotal FROM orders WHERE OrderId = ?";
        $stmt = $mysqli->prepare($orderQuery);
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $orderResult = $stmt->get_result();

        if ($order = $orderResult->fetch_assoc()) {
            $customerId = $order['CustomerId'];
            $orderAmount = $order['FinalTotal'];

            // Check if enhanced_coupons table exists
            $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
            if ($tableCheck && $tableCheck->num_rows > 0) {
                // Update usage count for enhanced coupons
                if ($couponId) {
                    $updateQuery = "UPDATE enhanced_coupons SET current_usage_count = current_usage_count + 1 WHERE id = ?";
                    $stmt = $mysqli->prepare($updateQuery);
                    $stmt->bind_param("i", $couponId);
                    $stmt->execute();
                }
            }

            // Check if coupon_usage table exists
            $tableCheck = $mysqli->query("SHOW TABLES LIKE 'coupon_usage'");
            if ($tableCheck && $tableCheck->num_rows > 0) {
                // Record coupon usage
                $usageQuery = "INSERT INTO coupon_usage (coupon_id, coupon_code, customer_id, order_id, discount_amount, order_amount) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($usageQuery);
                $stmt->bind_param("isisdd", $couponId, $couponCode, $customerId, $orderId, $discount, $orderAmount);
                $stmt->execute();
            }
        }

        return true;

    } catch (Exception $e) {
        error_log("Error processing coupon usage: " . $e->getMessage());
        return true; // Don't break order processing
    }
}
?>

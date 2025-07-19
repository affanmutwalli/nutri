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
        // Don't process rewards if tables don't exist yet
        if (!file_exists(__DIR__ . '/OrderCompletionHooks.php')) {
            return true;
        }

        // Initialize database connection
        require_once __DIR__ . '/../database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();

        if (!$mysqli) {
            error_log("Database connection failed in processOrderPlacementRewards");
            return true; // Don't break order processing
        }

        require_once __DIR__ . '/OrderCompletionHooks.php';

        $hooks = new OrderCompletionHooks($mysqli);
        return $hooks->processOrderPlacement($orderId);

    } catch (Exception $e) {
        error_log("Error in processOrderPlacementRewards: " . $e->getMessage());
        return true; // Don't break order processing
    }
}
?>

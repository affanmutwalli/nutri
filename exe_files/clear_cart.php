<?php
session_start();

// Try to include cart persistence with proper error handling
if (file_exists('cart_persistence.php')) {
    include_once 'cart_persistence.php';
} elseif (file_exists(__DIR__ . '/cart_persistence.php')) {
    include_once __DIR__ . '/cart_persistence.php';
} else {
    // Fallback: create a simple cart manager
    class CartPersistence {
        public function clearDatabaseCart($customerId) {
            try {
                include_once '../database/dbconnection.php';
                $obj = new main();
                $obj->connection();
                $obj->fInsertNew("DELETE FROM cart WHERE CustomerId = ?", "i", array($customerId));
                return true;
            } catch (Exception $e) {
                error_log("Error clearing database cart: " . $e->getMessage());
                return false;
            }
        }
    }
}

header('Content-Type: application/json');

$cartManager = new CartPersistence();
$customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

$hasCart = isset($_SESSION['cart']) && !empty($_SESSION['cart']);
$hasBuyNow = isset($_SESSION['buy_now']) && !empty($_SESSION['buy_now']);

if ($hasCart || $hasBuyNow) {
    // Clear the cart session
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }

    // Clear the buy_now session
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
    }

    // Also clear from database if user is logged in
    if ($customerId) {
        $cartManager->clearDatabaseCart($customerId);
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'All items have been deleted from the cart.',
    ]);
} else {
    // If both cart and buy_now are empty, return a relevant message
    echo json_encode([
        'success' => false,
        'message' => 'Your cart is already empty.',
    ]);
}

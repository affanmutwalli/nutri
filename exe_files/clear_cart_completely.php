<?php
session_start();
require_once '../includes/config.php';
require_once 'cart_persistence.php';

header('Content-Type: application/json');

try {
    // Clear session cart
    unset($_SESSION['cart']);
    $_SESSION['cart'] = array();
    
    // Clear database cart if user is logged in
    if (isset($_SESSION['CustomerId'])) {
        $cartManager = new CartPersistenceManager();
        $cartManager->clearDatabaseCart($_SESSION['CustomerId']);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart cleared completely'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error clearing cart: ' . $e->getMessage()
    ]);
}
?>

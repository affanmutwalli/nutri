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
        public function removeFromCart($productId, $customerId = null) {
            // Remove from session cart
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }

            // If user is logged in, also remove from database
            if ($customerId) {
                try {
                    include_once '../database/dbconnection.php';
                    $obj = new main();
                    $obj->connection();
                    $obj->fInsertNew("DELETE FROM cart WHERE CustomerId = ? AND ProductId = ?", "ii", array($customerId, $productId));
                } catch (Exception $e) {
                    error_log("Error removing item from database cart: " . $e->getMessage());
                }
            }

            return true;
        }
    }
}

// Set content type for JSON response
header('Content-Type: application/json');

$cartManager = new CartPersistence();

// Check if 'productId' is set in the URL and the cart exists in the session
if (isset($_GET['productId']) && !empty($_SESSION['cart'])) {
    $productId = htmlspecialchars($_GET['productId']); // Sanitize input
    $customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

    // Check if the product exists in the cart
    if (isset($_SESSION['cart'][$productId])) {
        // Use cart persistence manager to remove item
        $cartManager->removeFromCart($productId, $customerId);

        // Recalculate the cart summary
        $totalItems = count($_SESSION['cart']);
        $totalPrice = 0;

        // Assuming each cart item has a 'price' field
        foreach ($_SESSION['cart'] as $item) {
            $totalPrice += $item['price'];
        }

        // Respond with success and updated cart summary
        echo json_encode([
            'success' => true,
            'message' => 'Product removed successfully.',
            'cartSummary' => "$totalItems items, Total: $$totalPrice"
        ]);
    } else {
        // Product ID not found in the cart
        echo json_encode([
            'success' => false,
            'message' => 'Product not found in the cart.'
        ]);
    }
} else {
    // Invalid request (missing productId or empty cart)
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request. Product not found or cart is empty.'
    ]);
}
?>

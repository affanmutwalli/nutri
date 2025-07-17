<?php
session_start();

// Set content type for JSON response
header('Content-Type: application/json');

// Check if 'productId' is set in the URL and the cart exists in the session
if (isset($_GET['productId']) && !empty($_SESSION['cart'])) {
    $productId = htmlspecialchars($_GET['productId']); // Sanitize input

    // Check if the product exists in the cart
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]); // Remove the product from the session cart

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

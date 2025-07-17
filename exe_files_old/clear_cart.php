<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['cart'])) {
    // Clear the cart session
    unset($_SESSION['cart']);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'All items have been deleted from the cart.',
    ]);
} else {
    // If the cart is already empty, return a relevant message
    echo json_encode([
        'success' => false,
        'message' => 'Your cart is already empty.',
    ]);
}

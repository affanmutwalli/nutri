<?php
session_start();

// Initialize total count
$totalProducts = 0;

// Check if the cart exists in the session
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // Sum up the quantities of all products
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $totalProducts += $quantity; // Add quantity to total count
    }
}

// Return the total count as JSON
echo json_encode(['count' => $totalProducts]);
?>

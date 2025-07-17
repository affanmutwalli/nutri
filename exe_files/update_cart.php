<?php
session_start();
include_once 'cart_persistence.php';
header('Content-Type: application/json');

$cartManager = new CartPersistence();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = isset($data['productId']) ? intval($data['productId']) : null;
$quantity = isset($data['quantity']) ? max(1, intval($data['quantity'])) : 1;
$customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

if ($productId === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

// Update cart quantity using persistence manager
$cartManager->updateCartQuantity($productId, $quantity, $customerId);

echo json_encode(['success' => true, 'cart_count' => $cartManager->getCartCount()]);
exit;
?>
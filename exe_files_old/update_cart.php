<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = isset($data['productId']) ? intval($data['productId']) : null;
$quantity = isset($data['quantity']) ? max(1, intval($data['quantity'])) : 1;

if ($productId === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

// Update cart quantity
$_SESSION['cart'][$productId] = $quantity;

echo json_encode(['success' => true]);
exit;
?>
<?php
session_start();

// Set JSON header first
header('Content-Type: application/json');

// Include database connection (avoid conflicts)
include_once '../database/dbconnection.php';
include_once 'cart_persistence.php'; // Include cart persistence functions

$obj = new main();
$mysqli = $obj->connection();
$cartManager = new CartPersistence();
// Check if action and productId are provided
if (isset($_POST['action']) && $_POST['action'] == 'add_to_cart' && isset($_POST['productId'])) {
    $productId = $_POST['productId'];

    // Fetch product details
    $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    if ($product_data > 0) {
        // Get customer ID if logged in
        $customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

        // Use cart persistence manager to add item
        $cartManager->addToCart($productId, 1, $customerId);

        $message = 'Product added to your cart successfully.';

        // Return success response with cart count
        $cartCount = $cartManager->getCartCount();
        echo json_encode(array(
            'status' => 'success',
            'message' => $message,
            'cart_count' => $cartCount
        ));
    } else {
        // Product doesn't exist in the database
        echo json_encode(array('status' => 'error', 'message' => 'Product not found.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request. POST data: ' . print_r($_POST, true)));
}
?>

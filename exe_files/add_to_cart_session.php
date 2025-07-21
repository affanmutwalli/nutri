<?php
session_start();

// Set JSON header first
header('Content-Type: application/json');

// Include database connection (avoid conflicts)
include_once '../database/dbconnection.php';
include_once 'cart_persistence.php'; // Include cart persistence functions
include_once '../includes/analytics_functions.php'; // Include analytics tracking

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

        // Track add to cart analytics
        try {
            // Get product price for analytics
            $priceData = $obj->MysqliSelect1(
                "SELECT MIN(OfferPrice) as price FROM product_price WHERE ProductId = ?",
                array("price"),
                "i",
                array($productId)
            );
            $price = $priceData && isset($priceData[0]['price']) ? $priceData[0]['price'] : 0;

            // Track the add to cart action
            trackAddToCart($productId, $product_data[0]['ProductName'], $price, 1);
        } catch (Exception $e) {
            error_log("Add to cart analytics error: " . $e->getMessage());
        }

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

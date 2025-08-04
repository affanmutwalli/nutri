<?php
session_start();
// PHANTOM PRODUCT MONITOR - INJECTED
if (file_exists(__DIR__ . "/cart_monitor.php")) {
    include_once __DIR__ . "/cart_monitor.php";
    if (class_exists("CartMonitor")) {
        $phantomMonitor = new CartMonitor();
        $phantomMonitor->log("📍 CHECKPOINT: " . basename(__FILE__));
        $phantomMonitor->checkForPhantomProducts();
    }
}


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

    // Validate product ID first
    if (!is_numeric($productId) || $productId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
        exit;
    }

    // Fetch product details with validation
    $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_master WHERE ProductId = ? AND ProductName IS NOT NULL AND ProductName != '' AND ProductName != 'N/A'",
        $FieldNames,
        "i",
        $ParamArray
    );

    if ($product_data > 0) {
        // Enhanced validation to prevent ALL phantom products
        $productName = $product_data[0]['ProductName'] ?? '';
        $productCode = $product_data[0]['ProductCode'] ?? '';

        // Block all phantom product patterns
        if (empty($productName) ||
            $productName === 'N/A' ||
            empty($productCode) ||
            strpos($productCode, 'SJ100') !== false ||
            strpos($productCode, 'XX-000') !== false ||
            $productCode === 'MN-SJ100' ||
            $productCode === 'MN-XX-000' ||
            (strpos($productCode, 'MN-XX-') === 0) ||
            (strpos($productCode, 'MN-SJ') === 0)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid product - phantom product blocked']);
            exit;
        }
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

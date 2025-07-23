<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$orderId = $_GET['order_id'];

try {
    // Fetch order details to get all products from this order
    $FieldNames = array("ProductId", "ProductCode", "Quantity", "Size", "Price");
    $ParamArray = array($orderId);
    $Fields = implode(",", $FieldNames);

    $OrderDetails = $obj->MysqliSelect1(
        "SELECT ProductId, ProductCode, Quantity, Size, Price FROM order_details WHERE OrderId = ?",
        $FieldNames,
        "s",
        $ParamArray
    );

    if (!$OrderDetails) {
        $_SESSION['error_message'] = "Order not found or no products in this order.";
        header("Location: cart.php");
        exit();
    }

    // Clear existing cart
    unset($_SESSION['cart']);
    $_SESSION['cart'] = array();

    // Add each product from the order to the cart
    foreach ($OrderDetails as $item) {
        // Fetch product details to ensure product still exists and is active
        $prodFieldNames = array("ProductId", "ProductName", "PhotoPath", "IsActive");
        $prodParamArray = array($item["ProductId"]);
        $prodFields = implode(",", $prodFieldNames);

        $product_data = $obj->MysqliSelect1(
            "SELECT $prodFields FROM product_master WHERE ProductId = ? AND IsActive = 'Y'",
            $prodFieldNames,
            "i",
            $prodParamArray
        );

        if ($product_data && isset($product_data[0])) {
            $product = $product_data[0];

            // Add to cart session using the simple structure that matches your cart system
            // Your cart system uses: $_SESSION['cart'][$productId] = $quantity
            $productId = $item['ProductId'];
            $quantity = $item['Quantity'];

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = $quantity;
            }
        }
    }

    // Set success message
    $_SESSION['success_message'] = "All items from your previous order have been added to your cart!";

    // Redirect to cart page
    header("Location: cart.php");
    exit();

} catch (Exception $e) {
    $_SESSION['error_message'] = "An error occurred while adding items to cart. Please try again. Error: " . $e->getMessage();
    header("Location: cart.php");
    exit();
}
?>

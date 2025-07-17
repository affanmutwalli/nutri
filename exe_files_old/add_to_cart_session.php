<?php
session_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php'); // Include your DB connection file

$obj = new main();
$mysqli = $obj->connection();
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
        // Check if cart already exists in session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // If product is already in the session cart, update the quantity
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += 1;
            $message = 'Product quantity updated in your session cart.';
        } else {
            // If product is not in the session cart, add it
            $_SESSION['cart'][$productId] = 1;
            $message = 'Product added to your session cart.';
        }

        // Return success response
        echo json_encode(array('status' => 'success', 'message' => $message));
    } else {
        // Product doesn't exist in the database
        echo json_encode(array('status' => 'error', 'message' => 'Product not found.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request. POST data: ' . print_r($_POST, true)));
}
?>

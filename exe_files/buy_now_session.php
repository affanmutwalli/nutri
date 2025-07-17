<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required POST data is set
    if (!isset($_POST["product_id"], $_POST["quantity"], $_POST["size"], $_POST["offer_price"], $_POST["mrp"])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields."
        ]);
        exit;
    }

    // Retrieve and sanitize product details from POST request
    $product_id = filter_var($_POST["product_id"], FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_var($_POST["quantity"], FILTER_SANITIZE_NUMBER_INT);
    $size = trim($_POST["size"]);
    $offer_price = filter_var($_POST["offer_price"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $mrp = filter_var($_POST["mrp"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Clean the size string (remove unwanted text like "Save ₹50")
    $size = preg_replace('/Save ₹\d+/', '', $size);  // Remove "Save ₹50"
    $size = trim($size); // Trim any extra spaces

    // Ensure offer_price and mrp are numeric
    $offer_price = is_numeric($offer_price) ? floatval($offer_price) : 0;
    $mrp = is_numeric($mrp) ? floatval($mrp) : 0;

    // Store the data in the session for "buy now"
    $_SESSION["buy_now"] = [
        "product_id" => $product_id,
        "quantity" => $quantity,
        "size" => $size,
        "offer_price" => $offer_price,
        "mrp" => $mrp
    ];

    // Send a JSON response back to the client
    echo json_encode([
        "status" => "success",
        "message" => "Product added to cart.",
        "buy_now" => $_SESSION["buy_now"]
    ]);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>

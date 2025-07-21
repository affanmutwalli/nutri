<?php
session_start();
include('../database/dbconnection.php');

// Set content type to JSON
header('Content-Type: application/json');

// Initialize database connection
$obj = new main();
$obj->connection();

try {
    if (!isset($_GET['productId']) || empty($_GET['productId'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit;
    }
    
    $productId = intval($_GET['productId']);
    
    // Fetch product price details based on the product ID
    $FieldNames = array("Size", "OfferPrice", "MRP", "Coins");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_prices = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_price WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Initialize variables for dynamic size selection
    $sizes = [];
    $price_data = [];

    // Process the fetched product prices and sizes
    if (!empty($product_prices) && is_array($product_prices)) {
        foreach ($product_prices as $product_price) {
            // Add null checks for all fields
            $size = isset($product_price["Size"]) ? htmlspecialchars($product_price["Size"]) : '';
            $offer_price = isset($product_price["OfferPrice"]) ? floatval($product_price["OfferPrice"]) : 0;
            $mrp = isset($product_price["MRP"]) ? floatval($product_price["MRP"]) : 0;
            $coins = isset($product_price["Coins"]) ? floatval($product_price["Coins"]) : 0;

            // Only add the size if OfferPrice and MRP are greater than 0
            if ($offer_price > 0 && $mrp > 0 && !empty($size)) {
                $sizes[] = $size;
                $price_data[$size] = [
                    'offer_price' => $offer_price,
                    'mrp' => $mrp,
                    'coins' => $coins
                ];
            }
        }
    }

    if (!empty($sizes)) {
        echo json_encode([
            'success' => true,
            'sizes' => $sizes,
            'price_data' => $price_data,
            'default_size' => $sizes[0]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No pricing data available for this product',
            'sizes' => [],
            'price_data' => []
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching pricing data: ' . $e->getMessage()
    ]);
}
?>

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
    
    // Fetch product details
    $FieldNames = array("ProductId", "ProductName", "PhotoPath", "ShortDescription", "Specification");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?", 
        $FieldNames, 
        "i", 
        $ParamArray
    );
    
    if (empty($product_data)) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    $product = $product_data[0];
    
    // Fetch price details
    $FieldNamesPrice = array("OfferPrice", "MRP");
    $ParamArrayPrice = array($productId);
    $FieldsPrice = implode(",", $FieldNamesPrice);
    $product_prices = $obj->MysqliSelect1(
        "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ? ORDER BY OfferPrice ASC LIMIT 1", 
        $FieldNamesPrice, 
        "i", 
        $ParamArrayPrice
    );
    
    // Add price information
    if (!empty($product_prices)) {
        $product['OfferPrice'] = $product_prices[0]['OfferPrice'];
        $product['MRP'] = $product_prices[0]['MRP'];
    } else {
        $product['OfferPrice'] = 'N/A';
        $product['MRP'] = 'N/A';
    }
    
    echo json_encode([
        'success' => true, 
        'product' => $product
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error fetching product details: ' . $e->getMessage()
    ]);
}
?>

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
    
    // Fetch product details data (PhotoPath and ImagePath for additional images)
    $FieldNames = array("Product_DetailsId", "ProductId", "PhotoPath", "Description", "ImagePath");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    
    $product_details_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_details WHERE ProductId = ? LIMIT 1",
        $FieldNames,
        "i",
        $ParamArray
    );

    if (!empty($product_details_data) && is_array($product_details_data) && isset($product_details_data[0])) {
        $product_details = $product_details_data[0];
        
        // Prepare image paths
        $productImage1 = !empty($product_details["PhotoPath"]) 
                        ? "cms/images/products/" . $product_details["PhotoPath"] 
                        : "images/default.jpg";
        
        $productImage2 = !empty($product_details["ImagePath"]) 
                        ? "cms/images/products/" . $product_details["ImagePath"] 
                        : "images/default.jpg";
        
        echo json_encode([
            'success' => true,
            'product_details' => $product_details,
            'productImage1' => $productImage1,
            'productImage2' => $productImage2,
            'hasImages' => !empty($product_details["PhotoPath"]) || !empty($product_details["ImagePath"])
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No additional product details found',
            'hasImages' => false
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching product details: ' . $e->getMessage(),
        'hasImages' => false
    ]);
}
?>

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
    
    // First get the main product image
    $FieldNames = array("PhotoPath");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?", 
        $FieldNames, 
        "i", 
        $ParamArray
    );
    
    $images = array();
    
    if (!empty($product_data)) {
        $images[] = $product_data[0]['PhotoPath'];
    }
    
    // Check if product_images table exists and get additional images
    try {
        $FieldNamesImages = array("ImagePath");
        $ParamArrayImages = array($productId);
        $FieldsImages = implode(",", $FieldNamesImages);
        $additional_images = $obj->MysqliSelect1(
            "SELECT " . $FieldsImages . " FROM product_images WHERE ProductId = ? ORDER BY ImageId", 
            $FieldNamesImages, 
            "i", 
            $ParamArrayImages
        );
        
        if (!empty($additional_images)) {
            foreach ($additional_images as $img) {
                if (!empty($img['ImagePath']) && !in_array($img['ImagePath'], $images)) {
                    $images[] = $img['ImagePath'];
                }
            }
        }
    } catch (Exception $e) {
        // product_images table might not exist, that's okay
        // We'll just use the main image
    }
    
    // If we only have one image, try to find similar images in the same directory
    if (count($images) == 1 && !empty($images[0])) {
        $mainImage = $images[0];
        $imagePath = '../cms/images/products/';
        
        // Get the base name without extension
        $pathInfo = pathinfo($mainImage);
        $baseName = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        
        // Look for similar images (same name with different suffixes)
        $possibleImages = array(
            $baseName . '_1.' . $extension,
            $baseName . '_2.' . $extension,
            $baseName . '_3.' . $extension,
            $baseName . '-1.' . $extension,
            $baseName . '-2.' . $extension,
            $baseName . '-3.' . $extension,
        );
        
        foreach ($possibleImages as $possibleImage) {
            if (file_exists($imagePath . $possibleImage) && !in_array($possibleImage, $images)) {
                $images[] = $possibleImage;
            }
        }
    }
    
    echo json_encode([
        'success' => true, 
        'images' => $images
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error fetching product images: ' . $e->getMessage()
    ]);
}
?>

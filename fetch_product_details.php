<?php
// Assuming you have a method to query the database
ob_start();
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

sec_session_start();

if (isset($_GET['productId'])) {
    $productId = $_GET['productId'];

    // Fetch product details
    $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId","MetaTags","MetaKeywords","ProductCode","CategoryId");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );


    if ($product_data) {
        // Fetch images for the product
        $FieldNames = array("PhotoPath");
        $ParamArray = array($productId);
        $Fields = implode(",", $FieldNames);
        $model_image = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM model_images WHERE ProductId = ?",
            $FieldNames,
            "i",
            $ParamArray
        );
        
        $FieldNames = array("Size","OfferPrice","MRP");
        $ParamArray = array($productId);
        $Fields = implode(",", $FieldNames);
        $model_image = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM product_price WHERE ProductId = ?",
            $FieldNames,
            "i",
            $ParamArray
        );


        // Prepare the response
        $response = array(
            'name' => $product[0]['ProductName'],
            'description' => $product[0]['Description'],
            'offer_price' => $product[0]['OfferPrice'],
            'mrp' => $product[0]['MRP'],
            'size' => $product[0]['Size'],
            'images' => array_column($images, 'ImagePath') // Extract image paths
        );

        echo json_encode($response);
    } else {
        echo json_encode(array('error' => 'Product not found.'));
    }
} else {
    echo json_encode(array('error' => 'Product ID not provided.'));
}
?>

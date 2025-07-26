<?php
session_start();
include('../database/dbconnection.php');

header('Content-Type: application/json');

if (!isset($_GET['productId']) || empty($_GET['productId'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$productId = $_GET['productId'];
$obj = new main();
$obj->connection();

try {
    // Fetch main product data
    $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId", "Description", "VideoURL", "Title");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    
    $product_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    if (!$product_data || empty($product_data)) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }

    // Fetch product images (model_images)
    $FieldNames = array("PhotoPath");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $model_images = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM model_images WHERE ProductId = ? ORDER BY sort_order ASC, ImageId ASC",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Fetch product pricing
    $FieldNames = array("Size", "OfferPrice", "MRP", "Coins");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_prices = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_price WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Process pricing data
    $sizes = [];
    $price_data = [];
    $lowest_price = "N/A";
    $mrp = "N/A";
    $discount = 0;
    $coins = 0;
    $default_size = "N/A";

    if (!empty($product_prices) && is_array($product_prices)) {
        foreach ($product_prices as $product_price) {
            $size = isset($product_price["Size"]) ? htmlspecialchars($product_price["Size"]) : '';
            $offer_price = isset($product_price["OfferPrice"]) ? floatval($product_price["OfferPrice"]) : 0;
            $mrp_val = isset($product_price["MRP"]) ? floatval($product_price["MRP"]) : 0;
            $coins_val = isset($product_price["Coins"]) ? floatval($product_price["Coins"]) : 0;

            if ($offer_price > 0 && $mrp_val > 0 && !empty($size)) {
                $sizes[] = $size;
                $price_data[$size] = [
                    'offer_price' => $offer_price,
                    'mrp' => $mrp_val,
                    'coins' => $coins_val
                ];
            }
        }

        if (!empty($sizes)) {
            $default_size = $sizes[0];
            $lowest_price = $price_data[$default_size]['offer_price'];
            $mrp = $price_data[$default_size]['mrp'];
            $coins = $price_data[$default_size]['coins'];
            $discount = $mrp > $lowest_price ? $mrp - $lowest_price : 0;
        }
    }

    // Fetch product details
    $FieldNames = array("Product_DetailsId", "ProductId", "PhotoPath", "Description", "ImagePath");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    
    $product_details_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_details WHERE ProductId = ? LIMIT 1",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Fetch ingredients
    $FieldNames = array("IngredientId", "ProductId", "PhotoPath", "IngredientName");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $ingredient_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_ingredients WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Fetch benefits
    $FieldNames = array("Product_BenefitId", "ProductId", "PhotoPath", "Title", "ShortDescription");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    
    $benefit_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_benefits WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Fetch reviews
    $FieldNames = array("Product_ReviewId", "ProductId", "PhotoPath", "Name", "Review", "Date");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    
    $review_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM product_review WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Fetch FAQs
    $FieldNames = array("FAQId", "ProductId", "Question", "Answer");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    
    $faq_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM faqs WHERE ProductId = ?",
        $FieldNames,
        "i",
        $ParamArray
    );

    // Prepare all images array
    $all_images = [];
    
    // Add main product image
    if (!empty($product_data[0]["PhotoPath"])) {
        $all_images[] = "cms/images/products/" . $product_data[0]["PhotoPath"];
    }
    
    // Add model images
    if (!empty($model_images)) {
        foreach ($model_images as $img) {
            if (!empty($img["PhotoPath"])) {
                $all_images[] = "cms/images/products/" . $img["PhotoPath"];
            }
        }
    }

    // Add product detail images
    if (!empty($product_details_data)) {
        if (!empty($product_details_data[0]["PhotoPath"])) {
            $all_images[] = "cms/images/products/" . $product_details_data[0]["PhotoPath"];
        }
        if (!empty($product_details_data[0]["ImagePath"])) {
            $all_images[] = "cms/images/products/" . $product_details_data[0]["ImagePath"];
        }
    }

    // Remove duplicates
    $all_images = array_unique($all_images);

    // Prepare response
    $response = [
        'success' => true,
        'product' => $product_data[0],
        'images' => array_values($all_images),
        'pricing' => [
            'sizes' => $sizes,
            'price_data' => $price_data,
            'default_price' => [
                'offer_price' => $lowest_price,
                'mrp' => $mrp,
                'coins' => $coins,
                'discount' => $discount
            ]
        ],
        'product_details' => $product_details_data ? $product_details_data[0] : null,
        'ingredients' => $ingredient_data ?: [],
        'benefits' => $benefit_data ?: [],
        'reviews' => $review_data ?: [],
        'faqs' => $faq_data ?: []
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

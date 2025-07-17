<?php
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$obj->connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get CategoryId and SubCategoryId from POST data
    $selected_categories = isset($_POST['CategoryId']) ? json_decode($_POST['CategoryId'], true) : [];
    $selected_subcategories = isset($_POST['SubCategoryId']) ? json_decode($_POST['SubCategoryId'], true) : [];

    $FieldNames = array("ProductId", "ProductName", "PhotoPath", "CategoryId", "SubCategoryId");
    $ParamArray = [];
    $Fields = implode(",", $FieldNames);
    $sql = "SELECT " . $Fields . " FROM product_master WHERE 1";

    // Check if specific categories are selected
    if (!empty($selected_categories)) {
        $placeholders = implode(',', array_fill(0, count($selected_categories), '?'));
        $ParamArray = array_merge($ParamArray, $selected_categories);
        $sql .= " AND CategoryId IN ($placeholders)";
    }

    // Check if specific subcategories are selected
    if (!empty($selected_subcategories)) {
        $placeholders = implode(',', array_fill(0, count($selected_subcategories), '?'));
        $ParamArray = array_merge($ParamArray, $selected_subcategories);
        $sql .= " AND SubCategoryId IN ($placeholders)";
    }

    // Fetch products based on the filters
    $products = $obj->MysqliSelect1($sql, $FieldNames, str_repeat("i", count($ParamArray)), $ParamArray);

    // Generate the HTML response
    if (!empty($products)) {
        foreach ($products as $product) {
           // Fetch category details using SubCategoryId
           $categoryDetails = $obj->MysqliSelect1(
            "SELECT CategoryId, CategoryName FROM category_master WHERE CategoryId = ?",
            ["CategoryId", "CategoryName"],
            "i",
            [$product["SubCategoryId"]]
        );
        $categoryId = $categoryDetails[0]["CategoryId"] ?? "N/A";  // Get CategoryId if available
        $categoryName = $categoryDetails[0]["CategoryName"] ?? "N/A";  // Get CategoryName if available

        // Fetch all prices (OfferPrice and MRP) for each individual product
        $FieldNamesPrice = array("OfferPrice", "MRP");
        $ParamArrayPrice = array($product["ProductId"]);
        $FieldsPrice = implode(",", $FieldNamesPrice);
        $product_prices = $obj->MysqliSelect1(
            "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
            $FieldNamesPrice, 
            "i", 
            $ParamArrayPrice
        );

        // Initialize variables for lowest price and MRP
        $lowest_price = PHP_INT_MAX; // Initialize to a high value
        $mrp = PHP_INT_MAX;          // Initialize to a high value
        $savings = 0;                // Default savings

        // Check if product prices are available
        if (!empty($product_prices)) {
            // Loop through all rows and find the lowest MRP and OfferPrice greater than 0
            foreach ($product_prices as $product_price) {
                $current_offer_price = floatval($product_price["OfferPrice"]);
                $current_mrp = floatval($product_price["MRP"]);

                if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                    $lowest_price = $current_offer_price;
                }
                if ($current_mrp > 0 && $current_mrp < $mrp) {
                    $mrp = $current_mrp;
                }
            }

            // Reset to "N/A" if no valid prices are found
            if ($lowest_price == PHP_INT_MAX) {
                $lowest_price = "N/A";
            }
            if ($mrp == PHP_INT_MAX) {
                $mrp = "N/A";
            }

            // Calculate savings only if valid prices are found
            if ($mrp != "N/A" && $lowest_price != "N/A" && $mrp > $lowest_price) {
                $savings = $mrp - $lowest_price;
            }
        }

        // Display the product details in the desired format
        echo '<li class="grid-items" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-top:15px;">';
        echo '    <div class="tred-pro">';
        echo '        <div class="tr-pro-img">';
        echo '            <a href="product_details.php?ProductId=' . htmlspecialchars($product["ProductId"]) . '">';
        echo '                <img class="img-fluid" src="cms/images/products/' . htmlspecialchars($product["PhotoPath"]) . '" alt="Product Image">';
        echo '                <img class="img-fluid additional-image" src="cms/images/products/' . htmlspecialchars($product["PhotoPath"]) . '" alt="Additional Image">';
        echo '            </a>';
        echo '        </div>';
        if ($savings > 0) {
            echo '        <div class="Pro-lable">';
            echo '            <span class="p-text">Off ₹' . htmlspecialchars($savings) . '</span>';
            echo '        </div>';
        }
        echo '    </div>';
        echo '    <div class="caption">';
        echo '        <h3><a href="product_details.php?ProductId=' . htmlspecialchars($product["ProductId"]) . '">' . htmlspecialchars($product["ProductName"]) . '</a></h3>';
        echo '        <div class="rating">';
        echo '            <i class="fa fa-star c-star"></i>';
        echo '            <i class="fa fa-star c-star"></i>';
        echo '            <i class="fa fa-star c-star"></i>';
        echo '            <i class="fa fa-star-o"></i>';
        echo '            <i class="fa fa-star-o"></i>';
        echo '        </div>';
        echo '        <div class="pro-price">';
        echo '            <span class="new-price">Starting from ₹' . htmlspecialchars($lowest_price) . '</span>';
        if ($mrp != "N/A") {
            echo '            <span class="old-price" style="text-decoration: line-through; color: #999;">₹' . htmlspecialchars($mrp) . '</span>';
        }
        echo '        </div>';
        echo '
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pro-btn text-center" style="margin: 5px;">
                            <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="' . htmlspecialchars($product['ProductId']) . '">
                                <i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Add to Cart
                            </a>
                        </div>
                    </div>
                </div>';

        echo '    </div>';
        echo '</li>';

    }
} else {
    echo '<li class="grid-items no-products">';
    echo '<p>No products found.</p>';
    echo '</li>';
}
}
?>

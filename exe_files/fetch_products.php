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
        // Modified to include combo products: show products from selected categories OR combo products
        $sql .= " AND (CategoryId IN ($placeholders) OR IsCombo = 'Y')";
    }

    // Check if specific subcategories are selected
    if (!empty($selected_subcategories)) {
        $placeholders = implode(',', array_fill(0, count($selected_subcategories), '?'));
        $ParamArray = array_merge($ParamArray, $selected_subcategories);
        // Modified to use junction table: show products from selected subcategories via junction table OR legacy field
        $sql .= " AND (ProductId IN (SELECT ProductId FROM product_subcategories WHERE SubCategoryId IN ($placeholders)) OR SubCategoryId IN ($placeholders))";
        // Add the subcategory IDs twice - once for junction table, once for legacy field
        $ParamArray = array_merge($ParamArray, $selected_subcategories);
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

        // Display the product details in the new modern card format
        echo '<div class="product-card" data-product-id="' . htmlspecialchars($product["ProductId"]) . '">';

        // Product badge for savings
        if ($savings > 0) {
            echo '    <div class="product-badge">';
            echo '        Save ₹' . number_format($savings);
            echo '    </div>';
        }

        // Product image with eye button
        echo '    <div class="product-image">';
        echo '        <a href="product_details.php?ProductId=' . htmlspecialchars($product["ProductId"]) . '">';
        echo '            <img class="main-image" src="cms/images/products/' . htmlspecialchars($product["PhotoPath"]) . '" alt="' . htmlspecialchars($product["ProductName"]) . '" loading="lazy">';
        echo '        </a>';
        echo '    </div>';

        // Product info
        echo '    <div class="product-info">';
        echo '        <h3 class="product-title">';
        echo '            <a href="product_details.php?ProductId=' . htmlspecialchars($product["ProductId"]) . '" style="text-decoration: none; color: inherit;">';
        echo '                ' . htmlspecialchars($product["ProductName"]);
        echo '            </a>';
        echo '        </h3>';

        // Product price
        echo '        <div class="product-price">';
        echo '            <span class="price-current">₹' . htmlspecialchars($lowest_price) . '</span>';
        if ($mrp != "N/A" && $mrp != $lowest_price) {
            echo '            <span class="price-original">₹' . htmlspecialchars($mrp) . '</span>';
            if ($savings > 0) {
                $discountPercent = round((($mrp - $lowest_price) / $mrp) * 100);
                echo '            <span class="price-discount">' . $discountPercent . '% OFF</span>';
            }
        }
        echo '        </div>';

        // Add to cart button
        echo '        <button class="btn-add-cart add-to-cart-session" data-product-id="' . htmlspecialchars($product['ProductId']) . '">';
        echo '            <i class="fa fa-shopping-cart me-2"></i>Add to Cart';
        echo '        </button>';
        echo '    </div>';
        echo '</div>';

    }
} else {
    echo '<div class="col-12 text-center py-5">';
    echo '<div style="color: #718096; font-size: 1.1rem;">';
    echo '<i class="fa fa-search fa-3x mb-3" style="opacity: 0.3;"></i>';
    echo '<h4>No products found</h4>';
    echo '<p>Try adjusting your filters or search criteria</p>';
    echo '</div>';
    echo '</div>';
}
}
?>

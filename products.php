<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

?>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Products</title>
    <meta name="description"
        content="Explore our wide range of products, featuring high-quality, organic, and eco-friendly options to meet all your needs." />
    <meta name="keywords"
        content="products, organic, eCommerce, online store, eco-friendly, high-quality, shopping, wide range" />
    <meta name="author" content="My Nutrify">

    <!-- favicon -->
    <link rel="shortcut icon" type="image/favicon" href="image/fevicon.png">
    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <!-- simple-line icon -->
    <link rel="stylesheet" type="text/css" href="css/simple-line-icons.css">
    <!-- font-awesome icon -->
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <!-- themify icon -->
    <link rel="stylesheet" type="text/css" href="css/themify-icons.css">
    <!-- ion icon -->
    <link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
    <!-- owl slider -->
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/owl.theme.default.min.css">
    <!-- swiper -->
    <link rel="stylesheet" type="text/css" href="css/swiper.min.css">
    <!-- animation -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <!-- style -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    /* Modern Shopify-style Products Page */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #fafafa;
    }

    .products-hero {
        background: linear-gradient(135deg, #ff6a00 0%, #e65c00 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }

    .products-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .products-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .filter-sidebar {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        position: sticky;
        top: 20px;
        height: fit-content;
    }

    .filter-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }

    .filter-group {
        margin-bottom: 25px;
    }

    .filter-option {
        display: flex;
        align-items: center;
        padding: 8px 0;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-option:hover {
        color: #667eea;
    }

    .filter-option input[type="checkbox"] {
        margin-right: 10px;
        transform: scale(1.1);
    }

    .products-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .products-count {
        color: #718096;
        font-size: 0.95rem;
    }

    .view-toggle {
        display: flex;
        background: #f7fafc;
        border-radius: 8px;
        padding: 4px;
    }

    .view-btn {
        padding: 8px 12px;
        border: none;
        background: transparent;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-btn.active {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        color: #667eea;
    }

    .sort-dropdown {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        background: white;
        min-width: 150px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-top: 20px;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        border-color: #ff6a00;
    }

    .product-image {
        position: relative;
        overflow: hidden;
        aspect-ratio: 1;
        background: #f8f9fa;
    }

    .product-actions {
        position: absolute;
        top: 12px;
        right: 12px;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 3;
    }

    .product-card:hover .product-actions {
        opacity: 1;
        transform: translateY(0);
    }

    .eye-btn {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .eye-btn:hover {
        background: #ff6a00;
        color: white;
        transform: scale(1.1);
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #e53e3e;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }

    .product-info {
        padding: 20px;
        display: flex;
        flex-direction: column;
        height: 160px; /* Fixed height for consistent card sizes */
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        line-height: 1.4;
        height: 2.8em; /* Fixed height for 2 lines */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
        flex-grow: 1; /* Take up available space */
    }

    .price-current {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
    }

    .price-original {
        font-size: 1rem;
        color: #a0aec0;
        text-decoration: line-through;
    }

    .price-discount {
        background: #c6f6d5;
        color: #22543d;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-add-cart {
        width: 100%;
        background: linear-gradient(135deg, #ff6a00 0%, #e65c00 100%);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-top: auto; /* Push button to bottom */
    }

    .btn-add-cart:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 106, 0, 0.4);
    }

    .mobile-filter-btn {
        background: linear-gradient(135deg, #ff6a00 0%, #e65c00 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 20px;
        width: 100%;
    }

    @media (max-width: 768px) {
        .products-hero h1 {
            font-size: 2rem;
        }

        .product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .products-header {
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }
    }

    /* Loading Animation */
    .product-grid {
        transition: opacity 0.3s ease;
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Custom scrollbar for filter sidebar */
    .filter-sidebar {
        max-height: 80vh;
        overflow-y: auto;
    }

    .filter-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .filter-sidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .filter-sidebar::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 3px;
    }

    .filter-sidebar::-webkit-scrollbar-thumb:hover {
        background: #e65c00;
    }

    /* Product Preview Modal */
    .preview-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .preview-content {
        background: white;
        border-radius: 16px;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .preview-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.1);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .preview-close:hover {
        background: #ff6a00;
        color: white;
    }

    .preview-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        padding: 30px;
    }

    .preview-image {
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
    }

    .preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-details h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 15px;
        line-height: 1.3;
    }

    .preview-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .preview-price .current {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ff6a00;
    }

    .preview-price .original {
        font-size: 1.2rem;
        color: #a0aec0;
        text-decoration: line-through;
    }

    .preview-description {
        color: #4a5568;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .preview-actions {
        display: flex;
        gap: 15px;
    }

    .btn-preview-cart {
        flex: 1;
        background: linear-gradient(135deg, #ff6a00 0%, #e65c00 100%);
        color: white;
        border: none;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-preview-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 106, 0, 0.4);
    }

    .btn-view-details {
        flex: 1;
        background: transparent;
        color: #ff6a00;
        border: 2px solid #ff6a00;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-view-details:hover {
        background: #ff6a00;
        color: white;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .preview-body {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
        }

        .preview-actions {
            flex-direction: column;
        }
    }

    /* Enhanced mobile responsiveness */
    @media (max-width: 576px) {
        .products-hero h1 {
            font-size: 1.8rem;
        }

        .product-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .product-info {
            padding: 15px;
            height: 140px; /* Slightly smaller on mobile */
        }

        .filter-sidebar {
            margin-bottom: 20px;
        }
    }

    /* Popup Overlay */
.cart-popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1000;
}

/* Popup Content */
.cart-popup-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    width: 300px;
}

/* Close Button */
.close-popup {
    position: absolute;
    top: 10px;
    right: 10px;
    border: none;
    background: none;
    font-size: 20px;
    cursor: pointer;
}

/* Popup Heading */
.cart-popup-body h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 15px;
}

/* Buttons */
.cart-popup-actions a {
    display: inline-block;
    margin: 10px 5px;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.cart-popup-actions .btn-view-cart {
    background: #305724;
}

.cart-popup-actions .btn-view-cart:hover {
    background: #000000;
}

.cart-popup-actions .btn-checkout {
    background: #ec6504;
}

.cart-popup-actions .btn-checkout:hover {
    background: #ffffff;
}

    </style>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1209485663860371');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1209485663860371&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
</head>

<body class="home-1">
 

    <div id="cart-popup" class="cart-popup-overlay">
    <div class="cart-popup-content">
        <button class="close-popup" onclick="$('#cart-popup').fadeOut();">
            &times;
        </button>
        <h3>Product added to your cart!</h3>
        <div class="cart-popup-actions">
            <a href="cart.php" class="btn-view-cart">View Cart</a>
            <a href="checkout.php" class="btn-checkout">Checkout</a>
        </div>
    </div>
</div>
    <!-- header start -->
    <?php include("components/header.php"); ?>
    <!-- header end -->

    <!-- Hero Section -->
    <section class="products-hero">
        <div class="container-fluid full-width">
            <div class="text-center">
                <h1>Our Products</h1>
                <p>Discover our premium collection of health and wellness products, carefully curated for your well-being</p>
            </div>
        </div>
    </section>

    <!-- Main Products Section -->
    <section class="py-5">
        <div class="container-fluid full-width">
            <div class="row">
                <!-- Filter Sidebar -->
                <div class="col-lg-3 col-md-4">
                    <!-- Mobile Filter Button -->
                    <button class="mobile-filter-btn d-lg-none" data-bs-toggle="collapse" data-bs-target="#filter-sidebar">
                        <i class="fa fa-filter me-2"></i> Filters & Categories
                    </button>

                    <!-- Filter Sidebar -->
                    <div class="filter-sidebar collapse d-lg-block" id="filter-sidebar">
                        <!-- Categories Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fa fa-tags me-2"></i>Categories
                            </h4>
                            <?php
                            // Fetch all categories
                            $FieldNames = array("CategoryId", "CategoryName");
                            $ParamArray = array();
                            $Fields = implode(",", $FieldNames);
                            $category_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM category_master", $FieldNames, "", $ParamArray);

                            foreach ($category_data as $category) { ?>
                                <label class="filter-option">
                                    <input type="checkbox" class="category-checkbox"
                                           value="<?php echo htmlspecialchars($category["CategoryId"]); ?>"
                                           id="category-<?php echo htmlspecialchars($category["CategoryId"]); ?>">
                                    <span><?php echo htmlspecialchars($category["CategoryName"]); ?></span>
                                </label>
                            <?php } ?>
                        </div>

                        <!-- Sub-Categories Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fa fa-list me-2"></i>Sub-Categories
                            </h4>
                            <?php
                            // Fetch all subcategories
                            $FieldNames = array("SubCategoryId", "SubCategoryName");
                            $ParamArray = array();
                            $Fields = implode(",", $FieldNames);
                            $subcategory_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM sub_category", $FieldNames, "", $ParamArray);

                            foreach ($subcategory_data as $subcategory) { ?>
                                <label class="filter-option">
                                    <input type="checkbox" class="subcategory-checkbox"
                                           value="<?php echo htmlspecialchars($subcategory["SubCategoryId"]); ?>"
                                           id="subcategory-<?php echo htmlspecialchars($subcategory["SubCategoryId"]); ?>">
                                    <span><?php echo htmlspecialchars($subcategory["SubCategoryName"]); ?></span>
                                </label>
                            <?php } ?>
                        </div>

                        <!-- Quick Product Types -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fa fa-cube me-2"></i>Product Types
                            </h4>
                            <label class="filter-option">
                                <input type="radio" name="product_type" value="juice" class="product-type-radio">
                                <span>Juices & Syrups</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="product_type" value="powder" class="product-type-radio">
                                <span>Powders & Churna</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="product_type" value="tablet" class="product-type-radio">
                                <span>Tablets & Capsules</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="product_type" value="oil" class="product-type-radio">
                                <span>Oils & Extracts</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="product_type" value="combo" class="product-type-radio">
                                <span>Combo Packs</span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Products Area -->
                <div class="col-lg-9 col-md-8">
                    <div class="products-container">
                        <!-- Products Header -->
                        <div class="products-header">
                            <div class="products-count">
                                <span id="product-count">Loading products...</span>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <div class="view-toggle">
                                    <button class="view-btn active" data-view="grid">
                                        <i class="fa fa-th"></i>
                                    </button>
                                    <button class="view-btn" data-view="list">
                                        <i class="fa fa-list"></i>
                                    </button>
                                </div>
                                <select class="sort-dropdown" id="sort-products">
                                    <option value="featured">Featured</option>
                                    <option value="price-low">Price: Low to High</option>
                                    <option value="price-high">Price: High to Low</option>
                                    <option value="name-az">Name: A to Z</option>
                                    <option value="name-za">Name: Z to A</option>
                                </select>
                            </div>
                        </div>
                        <!-- Products Grid -->
                        <div class="product-grid" id="product-list">
                                <?php 
                                // Fetch filtered products based on selected categories or sub-categories
                                if (isset($_GET["SubCategoryId"]) || isset($_GET["CategoryId"])) {
                                    $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                                    $ParamArray = array();
                            
                                    if (isset($_GET["SubCategoryId"])) {
                                        $ParamArray = array($_GET["SubCategoryId"]);

                                        // Build explicit field list with table prefix
                                        $prefixedFields = array();
                                        foreach ($FieldNames as $field) {
                                            $prefixedFields[] = "pm." . $field;
                                        }
                                        $Fields = implode(",", $prefixedFields);

                                        // Query to get products assigned to this subcategory via junction table OR legacy field
                                        $all_products = $obj->MysqliSelect1(
                                            "SELECT DISTINCT " . $Fields . "
                                             FROM product_master pm
                                             LEFT JOIN product_subcategories ps ON pm.ProductId = ps.ProductId
                                             WHERE ps.SubCategoryId = ? OR pm.SubCategoryId = ?",
                                            $FieldNames,
                                            "ii",
                                            array($_GET["SubCategoryId"], $_GET["SubCategoryId"])
                                        );
                                    }
                                    
                                    if (isset($_GET["CategoryId"])) {
                                        $ParamArray = array($_GET["CategoryId"]);
                                        $Fields = implode(",", $FieldNames);
                                        // Modified query to include both single products from the category AND all combo products
                                        $all_products = $obj->MysqliSelect1(
                                            "SELECT " . $Fields . " FROM product_master WHERE CategoryId = ? OR IsCombo = 'Y'",
                                            $FieldNames,
                                            "i",
                                            $ParamArray
                                        );
                                    }
                                } else {
                                    // Default query to fetch all products
                                    $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                                    $ParamArray = array();
                                    $Fields = implode(",", $FieldNames);
                                    $query = "SELECT " . $Fields . " FROM product_master";
                                    $all_products = $obj->MysqliSelect1($query, $FieldNames, "", $ParamArray);
                                }
                            
                                if (!empty($all_products)) {
                                    foreach ($all_products as $products) {
                                        // Fetch price details
                                        $FieldNamesPrice = array("OfferPrice", "MRP");
                                        $ParamArrayPrice = array($products["ProductId"]);
                                        $FieldsPrice = implode(",", $FieldNamesPrice);
                                        $product_prices = $obj->MysqliSelect1(
                                            "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                            $FieldNamesPrice, 
                                            "i", 
                                            $ParamArrayPrice
                                        );
                            
                                        $lowest_price = PHP_INT_MAX; // Initialize to a very high value
                                        $mrp = PHP_INT_MAX;          // Initialize to a very high value
                                        $savings = 0;                // Default savings
                                        
                                        if (!empty($product_prices)) {
                                            foreach ($product_prices as $product_price) {
                                                $current_offer_price = floatval($product_price["OfferPrice"]);
                                                $current_mrp = floatval($product_price["MRP"]);

                                                // Ensure both OfferPrice and MRP are greater than 0 and find the lowest values
                                                if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                                                    $lowest_price = $current_offer_price;
                                                }
                                                if ($current_mrp > 0 && $current_mrp < $mrp) {
                                                    $mrp = $current_mrp;
                                                }
                                            }

                                            // Calculate savings only if valid prices are found
                                            if ($mrp > $lowest_price && $mrp != PHP_INT_MAX && $lowest_price != PHP_INT_MAX) {
                                                $savings = $mrp - $lowest_price;
                                            } else {
                                                $lowest_price = "N/A";
                                                $mrp = "N/A";
                                            }
                                        } else {
                                            // No prices found - still show the product but with "N/A" prices
                                            $lowest_price = "N/A";
                                            $mrp = "N/A";
                                        }
                                        ?>
                                        <div class="product-card" data-product-id="<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                            <?php if ($savings > 0): ?>
                                                <div class="product-badge">
                                                    Save ₹<?php echo number_format($savings); ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="product-image">
                                                <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                                    <img class="main-image"
                                                         src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>"
                                                         alt="<?php echo htmlspecialchars($products["ProductName"]); ?>"
                                                         loading="lazy">
                                                </a>

                                                <!-- Eye Button for Preview -->
                                                <div class="product-actions">
                                                    <button class="eye-btn" onclick="showPreview(<?php echo htmlspecialchars($products['ProductId']); ?>)">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="product-info">
                                                <h3 class="product-title">
                                                    <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>"
                                                       style="text-decoration: none; color: inherit;">
                                                        <?php echo htmlspecialchars($products["ProductName"]); ?>
                                                    </a>
                                                </h3>

                                                <div class="product-price">
                                                    <span class="price-current">₹<?php echo htmlspecialchars($lowest_price); ?></span>
                                                    <?php if ($mrp != "N/A" && $mrp != $lowest_price): ?>
                                                        <span class="price-original">₹<?php echo htmlspecialchars($mrp); ?></span>
                                                        <?php if ($savings > 0): ?>
                                                            <span class="price-discount"><?php echo round((($mrp - $lowest_price) / $mrp) * 100); ?>% OFF</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>

                                                <button class="btn-add-cart add-to-cart-session"
                                                        data-product-id="<?php echo htmlspecialchars($products['ProductId']); ?>">
                                                    <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                                                </button>
                                            </div>
                                        </div>
                                        <?php
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
                                ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    <!--<div class="list-all-page">-->
                    <!--    <span class="page-title">Showing 1 - 17 of 17 result</span>-->
                    <!--    <div class="page-number">-->
                    <!--        <a href="grid-list.html" class="active">1</a>-->
                    <!--        <a href="grid-list-2.html">2</a>-->
                    <!--        <a href="grid-list-3.html">3</a>-->
                    <!--        <a href="grid-list-4.html">4</a>-->
                    <!--        <a href="javascript:void(0)"><i class="fa fa-angle-double-right"></i></a>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </section>

    <!-- Product Preview Modal -->
    <div class="preview-modal" id="previewModal">
        <div class="preview-content">
            <button class="preview-close" onclick="closePreview()">
                <i class="fa fa-times"></i>
            </button>
            <div class="preview-body">
                <div class="preview-image">
                    <img id="previewImage" src="" alt="">
                </div>
                <div class="preview-details">
                    <h2 id="previewTitle">Product Name</h2>
                    <div class="preview-price">
                        <span class="current" id="previewPrice">₹0</span>
                        <span class="original" id="previewOriginalPrice" style="display: none;">₹0</span>
                    </div>
                    <div class="preview-description" id="previewDescription">
                        Loading product details...
                    </div>
                    <div class="preview-actions">
                        <button class="btn-preview-cart add-to-cart-session" id="previewAddToCart" data-product-id="">
                            <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                        <a href="#" class="btn-view-details" id="previewViewDetails">
                            View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- grid-list start -->
    <!-- quick veiw start -->
    <section class="quick-view">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Product quickview</h5>
                    <a href="javascript:void(0)" data-bs-dismiss="modal" aria-label="Close"><i class="ion-close-round"></i></a>
                </div>
                <div class="quick-veiw-area">
                    <div class="quick-image">
                        <div class="tab-content" id="product-images">
                            <!-- Dynamic Images will be loaded here -->
                        </div>
                        <ul class="nav nav-tabs quick-slider owl-carousel owl-theme" id="product-thumbnails">
                            <!-- Dynamic Thumbnails will be loaded here -->
                        </ul>
                    </div>
                    <div class="quick-caption">
                        <h4 id="product-title">Product Title</h4>
                        <div class="quick-price">
                            <span class="new-price" id="product-price">$350.00 USD</span>
                            <span class="old-price" id="product-old-price"><del>$399.99 USD</del></span>
                        </div>
                        <div class="quick-rating">
                            <i class="fa fa-star c-star"></i>
                            <i class="fa fa-star c-star"></i>
                            <i class="fa fa-star c-star"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <div class="pro-description" id="product-description">
                            <p>Loading description...</p>
                        </div>
                        <div class="pro-size">
                            <label>Size: </label>
                            <select id="product-size">
                                <option>1 ltr</option>
                                <option>3 ltr</option>
                                <option>5 ltr</option>
                            </select>
                        </div>
                        <div class="plus-minus">
                            <span>
                                <a href="javascript:void(0)" class="minus-btn text-black">-</a>
                                <input type="text" name="name" value="1">
                                <a href="javascript:void(0)" class="plus-btn text-black">+</a>
                            </span>
                            <a href="cart.html" class="quick-cart"><i class="fa fa-shopping-bag"></i></a>
                            <a href="wishlist.html" class="quick-wishlist"><i class="fa fa-heart"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- quick veiw end -->
    <?php include("components/footer.php"); ?>
    <!-- footer copyright end -->
    <!-- back to top start -->
    <a href="javascript:void(0)" class="scroll" id="top">
        <span><i class="fa fa-angle-double-up"></i></span>
    </a>
    <!-- back to top end -->
    <div class="mm-fullscreen-bg"></div>
    <!-- jquery -->
    <script src="js/modernizr-2.8.3.min.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <!-- popper -->
    <script src="js/popper.min.js"></script>
    <!-- fontawesome -->
    <script src="js/fontawesome.min.js"></script>
    <!-- owl carousal -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- swiper -->
    <script src="js/swiper.min.js"></script>
    <!-- price range -->
    <script src="js/range-slider.js"></script>
    <!-- custom -->
    <script src="js/custom.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const categoryCheckboxes = document.querySelectorAll(".category-checkbox");
        const subCategoryCheckboxes = document.querySelectorAll(".subcategory-checkbox");
        const productTypeRadios = document.querySelectorAll(".product-type-radio");

        // Update product count
        function updateProductCount() {
            const productCards = document.querySelectorAll('.product-card');
            const count = productCards.length;
            document.getElementById('product-count').textContent = `Showing ${count} products`;
        }

        // Initialize product count
        updateProductCount();

        // Function to handle the category checkbox change
        categoryCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", function() {
                // Collect all checked categories
                const selectedCategories = Array.from(categoryCheckboxes)
                    .filter((cb) => cb.checked)
                    .map((cb) => cb.value);

                // Send AJAX request for category filter
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "exe_files/fetch_products.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Update the product list dynamically
                        document.getElementById("product-list").innerHTML = xhr.responseText;
                        updateProductCount();

                        // Add loading animation
                        document.getElementById("product-list").style.opacity = "0";
                        setTimeout(() => {
                            document.getElementById("product-list").style.opacity = "1";
                            initializeHoverEffects(); // Reinitialize hover effects for new products
                        }, 100);
                    } else {
                        console.error("Failed to fetch products.");
                    }
                };

                xhr.send("CategoryId=" + JSON.stringify(selectedCategories));
            });
        });

        // Function to handle the subcategory checkbox change
        subCategoryCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", function() {
                // Collect all checked subcategories
                const selectedSubCategories = Array.from(subCategoryCheckboxes)
                    .filter((cb) => cb.checked)
                    .map((cb) => cb.value);

                // Send AJAX request for subcategory filter
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "exe_files/fetch_products.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Update the product list dynamically
                        document.getElementById("product-list").innerHTML = xhr.responseText;
                        updateProductCount();

                        // Add loading animation
                        document.getElementById("product-list").style.opacity = "0";
                        setTimeout(() => {
                            document.getElementById("product-list").style.opacity = "1";
                            initializeHoverEffects(); // Reinitialize hover effects for new products
                        }, 100);
                    } else {
                        console.error("Failed to fetch products.");
                    }
                };

                xhr.send("SubCategoryId=" + JSON.stringify(selectedSubCategories));
            });
        });

        // Product type radio functionality
        productTypeRadios.forEach((radio) => {
            radio.addEventListener("change", function() {
                if (this.checked) {
                    // Clear other filters
                    categoryCheckboxes.forEach(cb => cb.checked = false);
                    subCategoryCheckboxes.forEach(cb => cb.checked = false);

                    // Redirect to product type filter
                    window.location.href = `products.php?product_type=${this.value}`;
                }
            });
        });

        // View toggle functionality
        const viewButtons = document.querySelectorAll('.view-btn');
        const productGrid = document.getElementById('product-list');

        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                viewButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const view = this.dataset.view;
                if (view === 'list') {
                    productGrid.style.gridTemplateColumns = '1fr';
                } else {
                    productGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
                }
            });
        });

        // Sort functionality
        const sortDropdown = document.getElementById('sort-products');
        sortDropdown.addEventListener('change', function() {
            const sortValue = this.value;
            const products = Array.from(document.querySelectorAll('.product-card'));

            products.sort((a, b) => {
                switch(sortValue) {
                    case 'price-low':
                        const priceA = parseFloat(a.querySelector('.price-current').textContent.replace('₹', ''));
                        const priceB = parseFloat(b.querySelector('.price-current').textContent.replace('₹', ''));
                        return priceA - priceB;
                    case 'price-high':
                        const priceA2 = parseFloat(a.querySelector('.price-current').textContent.replace('₹', ''));
                        const priceB2 = parseFloat(b.querySelector('.price-current').textContent.replace('₹', ''));
                        return priceB2 - priceA2;
                    case 'name-az':
                        const nameA = a.querySelector('.product-title').textContent.trim();
                        const nameB = b.querySelector('.product-title').textContent.trim();
                        return nameA.localeCompare(nameB);
                    case 'name-za':
                        const nameA2 = a.querySelector('.product-title').textContent.trim();
                        const nameB2 = b.querySelector('.product-title').textContent.trim();
                        return nameB2.localeCompare(nameA2);
                    default:
                        return 0;
                }
            });

            // Re-append sorted products
            const productContainer = document.getElementById('product-list');
            products.forEach(product => productContainer.appendChild(product));
        });

        // Initialize hover image effects for existing products
        initializeHoverEffects();
    });

    // Product Preview Modal Functions
    function showPreview(productId) {
        const modal = document.getElementById('previewModal');

        // Show modal with loading state
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Reset content
        document.getElementById('previewTitle').textContent = 'Loading...';
        document.getElementById('previewDescription').textContent = 'Loading product details...';

        // Fetch product details
        fetch(`exe_files/get_product_preview.php?productId=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('previewImage').src = `cms/images/products/${data.product.PhotoPath}`;
                    document.getElementById('previewTitle').textContent = data.product.ProductName;
                    document.getElementById('previewPrice').textContent = `₹${data.product.OfferPrice}`;

                    if (data.product.MRP && data.product.MRP != data.product.OfferPrice) {
                        document.getElementById('previewOriginalPrice').textContent = `₹${data.product.MRP}`;
                        document.getElementById('previewOriginalPrice').style.display = 'inline';
                    } else {
                        document.getElementById('previewOriginalPrice').style.display = 'none';
                    }

                    document.getElementById('previewDescription').textContent = data.product.ShortDescription || 'No description available.';
                    document.getElementById('previewAddToCart').setAttribute('data-product-id', productId);
                    document.getElementById('previewViewDetails').href = `product_details.php?ProductId=${productId}`;
                } else {
                    document.getElementById('previewTitle').textContent = 'Error loading product';
                    document.getElementById('previewDescription').textContent = 'Unable to load product details.';
                }
            })
            .catch(error => {
                console.error('Error fetching product details:', error);
                document.getElementById('previewTitle').textContent = 'Error loading product';
                document.getElementById('previewDescription').textContent = 'Unable to load product details.';
            });
    }

    function closePreview() {
        const modal = document.getElementById('previewModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePreview();
        }
    });

    // Hover Image Effects
    function initializeHoverEffects() {
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            const productId = card.getAttribute('data-product-id');
            const mainImage = card.querySelector('.main-image');
            let hoverImages = [];
            let currentImageIndex = 0;
            let hoverInterval;

            // Fetch additional images for this product
            fetch(`exe_files/get_product_images.php?productId=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.images.length > 1) {
                        hoverImages = data.images;

                        // Add hover event listeners
                        card.addEventListener('mouseenter', function() {
                            if (hoverImages.length > 1) {
                                currentImageIndex = 0;
                                hoverInterval = setInterval(() => {
                                    currentImageIndex = (currentImageIndex + 1) % hoverImages.length;
                                    mainImage.src = `cms/images/products/${hoverImages[currentImageIndex]}`;
                                }, 800); // Change image every 800ms
                            }
                        });

                        card.addEventListener('mouseleave', function() {
                            if (hoverInterval) {
                                clearInterval(hoverInterval);
                            }
                            // Reset to original image
                            mainImage.src = `cms/images/products/${hoverImages[0]}`;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching product images:', error);
                });
        });
    }
    // This function triggers when a user clicks on a product
        function showQuickView(productId) {
            // Make an AJAX request to fetch product details by productId
            $.ajax({
                url: 'fetch_product_details.php',  // PHP file to fetch product data
                type: 'GET',
                data: { productId: productId },
                success: function(response) {
                    // Parse the JSON response
                    var product = JSON.parse(response);

                    // Populate the modal with product data
                    $('#product-title').text(product.name);
                    $('#product-price').text('$' + product.offer_price);
                    $('#product-old-price').text('$' + product.mrp);
                    $('#product-description').text(product.description);
                    $('#product-size').val(product.size);
                    
                    // Update images and thumbnails dynamically
                    var imageTabContent = '';
                    var imageThumbnails = '';
                    product.images.forEach(function(image, index) {
                        imageTabContent += '<div class="tab-pane fade ' + (index === 0 ? 'show active' : '') + '" id="image-' + (index + 1) + '">';
                        imageTabContent += '<a href="javascript:void(0)" class="long-img"><img src="images/products/' + image + '" class="img-fluid" alt="image"></a>';
                        imageTabContent += '</div>';

                        imageThumbnails += '<li class="nav-item items"><a class="nav-link ' + (index === 0 ? 'active' : '') + '" data-bs-toggle="tab" href="#image-' + (index + 1) + '"><img src="images/products/' + image + '" class="img-fluid" alt="image"></a></li>';
                    });

                    $('#product-images').html(imageTabContent);
                    $('#product-thumbnails').html(imageThumbnails);
                    
                    // Show the modal
                    $('#exampleModal').modal('show');
                }
            });
        }

    </script>
    <script>
       $(document).ready(function () {
            // Add product to cart for logged-in users
            $('.add-to-cart').on('click', function () {
                var productId = $(this).data('product-id'); // Get product ID from data attribute

                $.ajax({
                    url: 'add_to_cart.php', // PHP file to handle the cart addition
                    type: 'POST',
                    data: {
                        action: 'add_to_cart',
                        productId: productId
                    },
                    success: function (response) {
                        try {
                            var data = JSON.parse(response);
                            if (data.status === 'success') {
                                // Show the added to cart popup
                                $('#cart-popup').fadeIn();

                                // Automatically hide popup after a few seconds
                                setTimeout(function () {
                                    $('#cart-popup').fadeOut(function () {
                                        location.reload(); // Reload the page after popup is hidden
                                    });
                                }, 3000);
                            } else {
                                alert(data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error('AJAX error:', status, error);
                    }
                });
            });

            // Add to cart for non-logged-in users (session-based cart)
            $(document).on('click', '.add-to-cart-session', function () { // Use event delegation
                var productId = $(this).data('product-id'); // Get product ID
                console.log('Product ID:', productId); // Debugging: Log the product ID

                $.ajax({
                    url: 'exe_files/add_to_cart_session.php', // PHP file to handle the cart addition in session
                    type: 'POST',
                    data: {
                        action: 'add_to_cart',
                        productId: productId
                    },
                    success: function (response) {
                        try {
                            var data = JSON.parse(response); // Parse the response
                            if (data.status === 'success') {
                                // Show the added to cart popup
                                $('#cart-popup').fadeIn();

                                // Automatically hide popup after a few seconds
                                setTimeout(function () {
                                    $('#cart-popup').fadeOut(function () {
                                        location.reload(); // Reload the page after popup is hidden
                                    });
                                }, 3000);
                            } else {
                                alert(data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e); // Log error if JSON parsing fails
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });

function removeFromCart(productId) {
            // Send an AJAX request to remove the product
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'remove-from-cart.php?productId=' + productId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // If successful, remove the product from the cart display
                    var cartItem = document.getElementById('cart-item-' + productId);
                    if (cartItem) {
                        cartItem.remove(); // Remove the product item from the DOM
                        location.reload();
                    }

                    // Optionally, update the cart summary or notify the user
                    alert('Product removed from cart');
                } else {
                    alert('Failed to remove product from cart');
                }
            };
            xhr.send();
        }

        const modal = document.getElementById('modal');
        const modalVideo = document.getElementById('modal-video');
        const modalTitle = document.getElementById('modal-title');
        const modalPrice = document.getElementById('modal-price');

        function showModal(title, price, oldPrice, videoSrc) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            modalVideo.src = videoSrc;
            modalTitle.textContent = title;
            modalPrice.innerHTML = `${price} <del>${oldPrice}</del>`;
        }
        function toggleMute() {
            const video = document.getElementById('modal-video');
            const muteIcon = document.getElementById('mute-unmute');
            
            if (video.muted) {
                video.muted = false;
                muteIcon.classList.remove('fa-volume-mute');
                muteIcon.classList.add('fa-volume-up');
            } else {
                video.muted = true;
                muteIcon.classList.remove('fa-volume-up');
                muteIcon.classList.add('fa-volume-mute');
            }
        }
        function closeModal(event) {
    if (event.target === document.getElementById('modal') || event.target.id === 'close-modal') {
        document.getElementById('modal').style.display = 'none';
    }
}

        function closeModal(event) {
            // Close modal only if clicked outside the content
            if (event.target === modal) {
                modal.classList.remove('active');
                modalVideo.pause();
                modalVideo.src = ''; // Stop the video
            }
        }

        const scrollableCards = document.querySelector('.scrollable-cards');

        function scrollLeft() {
            scrollableCards.scrollBy({ left: -320, behavior: 'smooth' });
        }

        function scrollRight() {
            scrollableCards.scrollBy({ left: 320, behavior: 'smooth' });
        }
</script>

    <script>
    (function(w,d,s,c,r,a,m){
      w['KiwiObject']=r;
      w[r]=w[r] || function () {
        (w[r].q=w[r].q||[]).push(arguments)};
      w[r].l=1*new Date();
        a=d.createElement(s);
        m=d.getElementsByTagName(s)[0];
      a.async=1;
      a.src=c;
      m.parentNode.insertBefore(a,m)
    })(window,document,'script',"https://app.interakt.ai/kiwi-sdk/kiwi-sdk-17-prod-min.js?v="+ new Date().getTime(),'kiwi');
    window.addEventListener("load",function () {
      kiwi.init('', 'e8HrxTVfF0QjtZSXjjFfT9VUvRgmxQgo', {});
    });
  </script>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
</body>

</html>
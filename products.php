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
    /* Enhanced Slider Navigation Styles */
    .owl-carousel .owl-nav {
        position: absolute;
        top: 50%;
        width: 100%;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 10;
    }

    .owl-carousel .owl-nav button.owl-prev,
    .owl-carousel .owl-nav button.owl-next {
        background: rgba(255, 255, 255, 0.9) !important;
        border: 2px solid #ff6b35 !important;
        border-radius: 50% !important;
        width: 50px !important;
        height: 50px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        opacity: 0.8 !important;
        cursor: pointer !important;
        pointer-events: all !important;
        position: absolute !important;
        font-size: 18px !important;
        color: #ff6b35 !important;
    }

    .owl-carousel .owl-nav button.owl-prev {
        left: -25px !important;
    }

    .owl-carousel .owl-nav button.owl-next {
        right: -25px !important;
    }

    .owl-carousel .owl-nav button.owl-prev:hover,
    .owl-carousel .owl-nav button.owl-next:hover {
        background: #ff6b35 !important;
        transform: scale(1.1) !important;
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4) !important;
        opacity: 1 !important;
    }

    .owl-carousel .owl-nav button.owl-prev:hover,
    .owl-carousel .owl-nav button.owl-next:hover {
        color: white !important;
    }

    /* Quick View Slider Specific Styling */
    .quick-slider .owl-nav {
        position: absolute !important;
        top: 50% !important;
        width: 100% !important;
        transform: translateY(-50%) !important;
        pointer-events: none !important;
        z-index: 10 !important;
        margin-top: 0 !important;
    }

    .quick-slider .owl-nav button.owl-prev {
        position: absolute !important;
        left: -15px !important;
        pointer-events: all !important;
        width: 35px !important;
        height: 35px !important;
    }

    .quick-slider .owl-nav button.owl-next {
        position: absolute !important;
        right: -15px !important;
        pointer-events: all !important;
        width: 35px !important;
        height: 35px !important;
    }

    /* Responsive adjustments for slider navigation */
    @media (max-width: 768px) {
        .owl-carousel .owl-nav button.owl-prev,
        .owl-carousel .owl-nav button.owl-next {
            width: 40px !important;
            height: 40px !important;
            font-size: 16px !important;
        }

        .owl-carousel .owl-nav button.owl-prev {
            left: -20px !important;
        }

        .owl-carousel .owl-nav button.owl-next {
            right: -20px !important;
        }
    }

    @media (max-width: 480px) {
        .owl-carousel .owl-nav button.owl-prev,
        .owl-carousel .owl-nav button.owl-next {
            width: 35px !important;
            height: 35px !important;
            font-size: 14px !important;
        }

        .owl-carousel .owl-nav button.owl-prev {
            left: -15px !important;
        }

        .owl-carousel .owl-nav button.owl-next {
            right: -15px !important;
        }
    }

    /* Smooth transitions for all slider elements */
    .owl-carousel .owl-item {
        transition: all 0.3s ease;
    }

    .owl-carousel .owl-item:hover {
        transform: translateY(-2px);
    }

    /* Slider container positioning */
    .owl-carousel {
        position: relative;
        padding: 0 30px;
    }

    /* Hide navigation on small containers */
    .owl-carousel.owl-loaded .owl-nav {
        display: block;
    }

    /* Ensure proper spacing for navigation buttons */
    @media (min-width: 769px) {
        .owl-carousel {
            margin: 0 25px;
        }
    }

    /* Modern Shopify-style Products Page */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #fafafa;
    }

    .products-hero {
    background-image: url("cms/images/banners/My-Nutrify-&-Shilajit.jpg");

        color: white;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        padding: 140px; 0;
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

        .trending-title {
            font-size: 1.5rem;
            padding: 12px 20px;
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

    /* Modal Carousel Styles */
    .product-main-slider-modal {
        position: relative;
    }

    .product-main-slider-modal .owl-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 100%;
        pointer-events: none;
    }

    .product-main-slider-modal .owl-nav button {
        position: absolute;
        background: rgba(255, 255, 255, 0.9) !important;
        color: #333 !important;
        border: 1px solid #ddd !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 16px !important;
        transition: all 0.3s ease !important;
        pointer-events: all;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
    }

    .product-main-slider-modal .owl-nav button:hover {
        background: #ff6a00 !important;
        color: white !important;
        border-color: #ff6a00 !important;
        transform: scale(1.1) !important;
    }

    .product-main-slider-modal .owl-nav .owl-prev {
        left: -20px;
    }

    .product-main-slider-modal .owl-nav .owl-next {
        right: -20px;
    }

    .product-main-slider-modal .slider-item {
        padding: 5px;
    }

    .product-main-slider-modal .slider-item .long-img {
        display: block;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .product-main-slider-modal .slider-item .long-img:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

    // Global variables for modal functionality
    let currentProductImages = [];
    let currentImageIndex = 0;
    let currentQuantity = 1;






    function showPreview(productId) {
        const modal = document.getElementById('previewModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Show loading state
        modal.innerHTML = `
            <div style="
                background: white;
                border-radius: 20px;
                max-width: 95vw;
                width: 100%;
                max-height: 95vh;
                overflow-y: auto;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 400px;
            ">
                <div style="text-align: center; padding: 40px;">
                    <div style="
                        width: 50px;
                        height: 50px;
                        border: 4px solid #f3f3f3;
                        border-top: 4px solid #ff6a00;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                        margin: 0 auto 20px;
                    "></div>
                    <p style="color: #666; font-size: 16px;">Loading complete product details...</p>
                </div>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;

        // Fetch complete product details page content
        fetch(`exe_files/get_complete_product_details.php?productId=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderCompleteProductModal(data, productId);
                } else {
                    showErrorModal('Unable to load product details. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error fetching complete product details:', error);
                showErrorModal('Error loading product details. Please try again.');
            });
    }

    function showErrorModal(message) {
        const modal = document.getElementById('previewModal');
        modal.innerHTML = `
            <div style="
                background: white;
                border-radius: 20px;
                max-width: 500px;
                width: 100%;
                padding: 40px;
                text-align: center;
                position: relative;
            ">
                <button onclick="closePreview()" style="
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    color: #666;
                ">×</button>
                <div style="color: #dc3545; font-size: 48px; margin-bottom: 20px;">⚠️</div>
                <h3 style="color: #333; margin-bottom: 15px;">Error</h3>
                <p style="color: #666; margin-bottom: 30px;">${message}</p>
                <button onclick="closePreview()" style="
                    background: #ff6a00;
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 16px;
                ">Close</button>
            </div>
        `;
    }

    function renderCompleteProductModal(data, productId) {
        const modal = document.getElementById('previewModal');
        const product = data.product;
        const images = data.images || [];
        const pricing = data.pricing || {};
        const defaultPrice = pricing.default_price || { offer_price: 0, mrp: 0, coins: 0, discount: 0 };

        // Set global variables
        currentProductImages = images;
        currentImageIndex = 0;
        currentQuantity = 1;

        // Get main product image
        const mainImage = images.length > 0 ? images[0] : `cms/images/products/${product.PhotoPath}`;

        modal.innerHTML = `
            <div style="
                background: white;
                border-radius: 15px;
                max-width: 800px;
                width: 90%;
                max-height: 90vh;
                position: relative;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
                overflow: hidden;
                display: flex;
                flex-direction: column;
            ">
                <!-- Close Button -->
                <button onclick="closePreview()" style="
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background: rgba(255, 255, 255, 0.9);
                    border: none;
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    z-index: 10;
                    font-size: 14px;
                    color: #666;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                " onmouseover="this.style.background='#ff6a00'; this.style.color='white';" onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.color='#666';">
                    ×
                </button>

                <!-- Modal Content -->
                <div style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 0;
                    height: 100%;
                    min-height: 400px;
                    max-height: calc(90vh - 40px);
                    overflow: hidden;
                ">
                    <!-- Product Image Section -->
                    <div style="
                        background: #f8f9fa;
                        padding: 30px;
                        min-height: 300px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                    ">
                        <!-- Main Image Display -->
                        <div style="position: relative; margin-bottom: 15px;">
                            <img id="modalMainImage" src="${mainImage}" alt="${product.ProductName}" style="
                                max-width: 100%;
                                max-height: 250px;
                                object-fit: contain;
                                border-radius: 8px;
                                border: 1px solid rgba(0, 0, 0, 0.1);
                            ">

                            ${images.length > 1 ? `
                                <!-- Navigation Arrows -->
                                <button onclick="changeModalImageNav(-1)" style="
                                    position: absolute;
                                    left: -15px;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    background: rgba(255, 255, 255, 0.9);
                                    border: none;
                                    border-radius: 50%;
                                    width: 30px;
                                    height: 30px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    cursor: pointer;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                    z-index: 10;
                                    font-size: 14px;
                                    color: #666;
                                " onmouseover="this.style.background='#ff6a00'; this.style.color='white';" onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.color='#666';">
                                    ‹
                                </button>

                                <button onclick="changeModalImageNav(1)" style="
                                    position: absolute;
                                    right: -15px;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    background: rgba(255, 255, 255, 0.9);
                                    border: none;
                                    border-radius: 50%;
                                    width: 30px;
                                    height: 30px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    cursor: pointer;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                    z-index: 10;
                                    font-size: 14px;
                                    color: #666;
                                " onmouseover="this.style.background='#ff6a00'; this.style.color='white';" onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.color='#666';">
                                    ›
                                </button>
                            ` : ''}
                        </div>

                        <!-- Thumbnail Navigation -->
                        ${images.length > 1 ? `
                            <div style="
                                display: flex;
                                justify-content: center;
                                gap: 8px;
                                flex-wrap: wrap;
                                max-width: 300px;
                            ">
                                ${images.map((img, index) => `
                                    <div onclick="changeModalImage(${index})" style="
                                        cursor: pointer;
                                        border: 2px solid ${index === 0 ? '#ff6a00' : '#e2e8f0'};
                                        border-radius: 6px;
                                        padding: 2px;
                                        transition: all 0.3s ease;
                                        background: white;
                                    " class="modal-thumb" data-index="${index}">
                                        <img src="${img}" alt="Thumbnail ${index + 1}" style="
                                            width: 45px;
                                            height: 45px;
                                            object-fit: contain;
                                            border-radius: 4px;
                                            display: block;
                                        ">
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>

                    <!-- Product Information Section -->
                    <div style="
                        padding: 30px;
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-start;
                        overflow-y: auto;
                        max-height: calc(90vh - 40px);
                    ">
                        <h2 style="
                            font-size: 1.3rem;
                            font-weight: 600;
                            color: #333;
                            margin-bottom: 12px;
                            line-height: 1.3;
                        ">${product.ProductName}</h2>

                        <p style="
                            color: #666;
                            font-size: 0.85rem;
                            margin-bottom: 12px;
                            line-height: 1.4;
                        ">${product.ShortDescription || 'Boosts metabolism, improve digestion & assists in weight loss'}</p>

                        <!-- Rating -->
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 15px;">
                            <div style="display: flex; gap: 1px;">
                                ${[1,2,3,4,5].map(i => `<span style="color: #ffd700; font-size: 12px;">★</span>`).join('')}
                            </div>
                            <span style="color: #666; font-size: 0.8rem;">4.8 | 6 Reviews</span>
                        </div>

                        <!-- MRP Label -->
                        <div style="margin-bottom: 6px;">
                            <span style="color: #666; font-size: 0.8rem;">MRP (Including all taxes)</span>
                        </div>

                        <!-- Price -->
                        <div style="margin-bottom: 18px;">
                            <span style="
                                font-size: 1.5rem;
                                font-weight: 700;
                                color: #333;
                            ">₹${defaultPrice.offer_price > 0 ? defaultPrice.offer_price.toFixed(0) : '249'}</span>
                            ${defaultPrice.mrp > defaultPrice.offer_price ? `
                                <span style="
                                    color: #999;
                                    text-decoration: line-through;
                                    font-size: 0.9rem;
                                    margin-left: 8px;
                                ">₹${defaultPrice.mrp.toFixed(0)}</span>
                                <span style="
                                    background: #ff6a00;
                                    color: white;
                                    padding: 2px 6px;
                                    border-radius: 3px;
                                    font-size: 0.7rem;
                                    margin-left: 8px;
                                    font-weight: 600;
                                ">Save ₹${(defaultPrice.mrp - defaultPrice.offer_price).toFixed(0)}</span>
                            ` : ''}
                        </div>

                        <!-- Quantity -->
                        <div style="margin-bottom: 18px;">
                            <label style="
                                display: block;
                                color: #333;
                                font-weight: 500;
                                margin-bottom: 6px;
                                font-size: 0.85rem;
                            ">Quantity</label>
                            <div style="
                                display: flex;
                                align-items: center;
                                border: 1px solid #ddd;
                                border-radius: 5px;
                                width: fit-content;
                                background: white;
                            ">
                                <button onclick="changeQuantity(-1)" style="
                                    background: none;
                                    border: none;
                                    padding: 6px 10px;
                                    cursor: pointer;
                                    color: #666;
                                    font-size: 14px;
                                    font-weight: 600;
                                " onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='none'">−</button>
                                <input type="text" id="previewQuantity" value="1" style="
                                    border: none;
                                    width: 35px;
                                    text-align: center;
                                    font-weight: 500;
                                    background: none;
                                    outline: none;
                                    font-size: 14px;
                                ">
                                <button onclick="changeQuantity(1)" style="
                                    background: none;
                                    border: none;
                                    padding: 6px 10px;
                                    cursor: pointer;
                                    color: #666;
                                    font-size: 14px;
                                    font-weight: 600;
                                " onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='none'">+</button>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 6px;
                            margin-bottom: 18px;
                            color: #28a745;
                            font-size: 0.8rem;
                            font-weight: 500;
                        ">
                            <span style="
                                width: 6px;
                                height: 6px;
                                background: #28a745;
                                border-radius: 50%;
                            "></span>
                            In stock, ready to ship
                        </div>

                        <!-- Add to Cart Button -->
                        <button onclick="addToCartFromPreview(${productId})" style="
                            background: #333;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 6px;
                            font-size: 0.9rem;
                            font-weight: 500;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            width: 100%;
                            margin-bottom: 12px;
                        " onmouseover="this.style.background='#555'" onmouseout="this.style.background='#333'">
                            Add to cart
                        </button>

                        <!-- Additional Info -->
                        <div style="font-size: 0.75rem; color: #666; line-height: 1.3;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                <span style="color: #28a745;">✓</span>
                                <span>100% Ayurvedic & Herbal</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                <span style="color: #28a745;">✓</span>
                                <span>5% OFF Code "SAVE5" (Order above ₹499)</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                <span style="color: #28a745;">✓</span>
                                <span>Free Delivery On All Orders Above ₹399/-</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="color: #28a745;">🔒</span>
                                <span>Secure online payments</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Initialize modal image navigation
        currentModalImageIndex = 0;
        currentModalImages = images;
    }

    // Global variables for modal image navigation
    let currentModalImageIndex = 0;
    let currentModalImages = [];

    // Change modal image when thumbnail is clicked
    function changeModalImage(index) {
        if (!currentModalImages || index < 0 || index >= currentModalImages.length) return;

        currentModalImageIndex = index;

        // Update main image
        const mainImage = document.getElementById('modalMainImage');
        if (mainImage) {
            mainImage.src = currentModalImages[index];
        }

        // Update thumbnail active state
        document.querySelectorAll('.modal-thumb').forEach((thumb, i) => {
            if (i === index) {
                thumb.style.borderColor = '#ff6a00';
            } else {
                thumb.style.borderColor = '#e2e8f0';
            }
        });
    }

    // Navigate images with arrow buttons
    function changeModalImageNav(direction) {
        if (!currentModalImages || currentModalImages.length <= 1) return;

        let newIndex = currentModalImageIndex + direction;

        // Loop around if at beginning or end
        if (newIndex < 0) {
            newIndex = currentModalImages.length - 1;
        } else if (newIndex >= currentModalImages.length) {
            newIndex = 0;
        }

        changeModalImage(newIndex);
    }

    // Keyboard navigation for modal
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('productPreviewModal') && document.getElementById('productPreviewModal').style.display !== 'none') {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                changeModalImageNav(-1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                changeModalImageNav(1);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closePreview();
            }
        }
    });

    function renderModalWithPricing(product, productId, pricingData, detailsData) {
        const modal = document.getElementById('previewModal');

        // Set current product images from details data
        currentProductImages = detailsData.images || [`cms/images/products/${product.PhotoPath}`];
        currentImageIndex = 0;
        currentQuantity = 1;

        // Generate main image tabs HTML (like product_details.php)
        const mainImageTabsHtml = `
            <div class="tab-content">
                <div class="tab-pane show active" id="modal-image-main">
                    <a href="javascript:void(0)" class="long-img" style="border: 1px solid rgba(0, 0, 0, 0.1); border-radius: 10px; display: block;">
                        <figure class="zoom" onclick="zoomImage()" onmousemove="modalZoom(event)" style="
                            background-image: url('${currentProductImages[0]}');
                            margin: 0;
                            position: relative;
                            overflow: hidden;
                            cursor: zoom-in;
                            border-radius: 10px;
                            height: 400px;
                            background-size: contain;
                            background-position: center;
                            background-repeat: no-repeat;
                        ">
                            <img id="previewMainImage" src="${currentProductImages[0]}" class="img-fluid" alt="${product.ProductName}" style="
                                width: 100%;
                                height: auto;
                                max-height: 400px;
                                object-fit: contain;
                                transition: transform 0.3s ease;
                            ">
                        </figure>
                    </a>
                </div>
                ${currentProductImages.slice(1).map((img, index) => `
                    <div class="tab-pane" id="modal-image-${index + 1}">
                        <a href="javascript:void(0)" class="long-img" style="border: 1px solid #ccc; border-radius: 5px; margin-top: 15px; display: block;">
                            <figure class="zoom" onmousemove="modalZoom(event)" style="
                                background-image: url('${img}');
                                margin: 0;
                                position: relative;
                                overflow: hidden;
                                cursor: zoom-in;
                                border-radius: 10px;
                                height: 400px;
                                background-size: contain;
                                background-position: center;
                                background-repeat: no-repeat;
                            ">
                                <img src="${img}" class="img-fluid" alt="${product.ProductName}" style="
                                    width: 100%;
                                    height: auto;
                                    max-height: 400px;
                                    object-fit: contain;
                                ">
                            </figure>
                        </a>
                    </div>
                `).join('')}
            </div>
        `;

        // Generate thumbnails HTML (like product_details.php)
        const thumbnailsHtml = currentProductImages.length > 1 ? `
            <ul class="nav nav-tabs pro-page-slider owl-carousel owl-theme" style="list-style: none; margin: 0; padding: 10px 0;">
                <li class="nav-item items">
                    <a class="nav-link active" data-bs-toggle="tab" href="#modal-image-main" onclick="changePreviewImage(0)" style="
                        display: block;
                        padding: 5px;
                        border: 2px solid #ff6a00;
                        border-radius: 8px;
                        margin-right: 10px;
                        transition: all 0.3s ease;
                    " class="preview-thumbnail-0">
                        <img src="${currentProductImages[0]}" class="img-fluid" alt="Main image" style="
                            width: 70px;
                            height: 70px;
                            object-fit: cover;
                            border-radius: 5px;
                        ">
                    </a>
                </li>
                ${currentProductImages.slice(1).map((img, index) => `
                    <li class="nav-item items">
                        <a class="nav-link" data-bs-toggle="tab" href="#modal-image-${index + 1}" onclick="changePreviewImage(${index + 1})" style="
                            display: block;
                            padding: 5px;
                            border: 2px solid #e2e8f0;
                            border-radius: 8px;
                            margin-right: 10px;
                            transition: all 0.3s ease;
                        " class="preview-thumbnail-${index + 1}">
                            <img src="${img}" class="img-fluid" alt="Product image ${index + 2}" style="
                                width: 70px;
                                height: 70px;
                                object-fit: contain;
                                border-radius: 5px;
                            ">
                        </a>
                    </li>
                `).join('')}
            </ul>
        ` : '';

        // Generate size options HTML
        let sizeOptionsHtml = '';
        let defaultPrice = { offer_price: 0, mrp: 0, coins: 0 };

        if (pricingData.success && pricingData.sizes && pricingData.sizes.length > 0) {
            defaultPrice = pricingData.price_data[pricingData.sizes[0]];
            sizeOptionsHtml = `
                <h6 class="pro-size" style="margin-top: 20px; margin-bottom: 10px; font-weight: 600; color: #2d3748;">Size:</h6>
                <div class="size-container" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
                    ${pricingData.sizes.map((size, index) => {
                        const priceInfo = pricingData.price_data[size];
                        const discount = priceInfo.mrp - priceInfo.offer_price;
                        return `
                            <div class="size-box ${index === 0 ? 'selected' : ''}"
                                 data-offer-price="${priceInfo.offer_price}"
                                 data-mrp="${priceInfo.mrp}"
                                 data-coins="${priceInfo.coins}"
                                 data-size="${size}"
                                 onclick="handleModalSizeSelection(this)"
                                 style="
                                     cursor: pointer;
                                     padding: 12px;
                                     border: 2px solid ${index === 0 ? '#ff6a00' : '#e2e8f0'};
                                     border-radius: 8px;
                                     text-align: center;
                                     transition: all 0.3s ease;
                                     background: ${index === 0 ? '#fff5f0' : 'white'};
                                     min-width: 120px;
                                 ">
                                <div style="color: #305724; font-weight: bold; font-size: 12px;">Save ₹${discount.toFixed(2)}</div>
                                <div style="font-weight: 600; margin: 5px 0;">${size}</div>
                                <div class="size-price" style="font-size: 14px;">
                                    <span style="color: #28a745; font-weight: bold;">₹${priceInfo.offer_price.toFixed(2)}</span>
                                    <del style="color: #dc3545; margin-left: 5px;">₹${priceInfo.mrp.toFixed(2)}</del>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }

        // Calculate discount for display
        const discount = defaultPrice.mrp - defaultPrice.offer_price;

        modal.innerHTML = `
            <div style="
                background: white;
                border-radius: 20px;
                max-width: 1000px;
                width: 100%;
                max-height: 95vh;
                overflow-y: auto;
                position: relative;
                animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            ">
                <button onclick="closePreview()" style="
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    background: rgba(255, 255, 255, 0.9);
                    border: none;
                    width: 45px;
                    height: 45px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    z-index: 10;
                    font-size: 18px;
                    color: #666;
                    backdrop-filter: blur(10px);
                " onmouseover="this.style.background='#ff6a00'; this.style.color='white';" onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.color='#666';">
                    <i class="fas fa-times"></i>
                </button>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; padding: 40px;">
                    <!-- Product Images Section (Exact match to product_details.php) -->
                    <div class="col-xl-20 col-lg-16 col-md-12 col-xs-12 pro-image" style="max-width: 450px;">
                        <div class="row">
                            <!-- Main Image -->
                            <div class="col-lg-6 col-xl-6 col-md-6 col-12 larg-image" style="width: 100%;">
                                ${mainImageTabsHtml}
                                ${thumbnailsHtml}
                            </div>
                        </div>

                        <!-- Model Images Carousel Section (matching product_details.php) -->
                        <div class="product-main-slider-container" style="margin-top: 20px; margin-bottom: 20px;">
                            <h6 style="margin-bottom: 15px; font-weight: 600; color: #2d3748;">All Product Images (${currentProductImages.length}):</h6>
                                <div class="owl-carousel product-main-slider-modal" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; background: #f8f9fa;">
                                    ${currentProductImages.map((img, index) => `
                                        <div class="slider-item">
                                            <a href="javascript:void(0)" class="long-img" onclick="openImageModal('${img}')" style="
                                                border: 1px solid rgba(0, 0, 0, 0.1);
                                                border-radius: 10px;
                                                display: block;
                                                transition: all 0.3s ease;
                                                cursor: pointer;
                                            " onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.15)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                                <figure class="zoom" style="
                                                    background-image: url('${img}');
                                                    margin: 0;
                                                    position: relative;
                                                    overflow: hidden;
                                                    border-radius: 10px;
                                                    height: 120px;
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-repeat: no-repeat;
                                                ">
                                                    <img src="${img}" class="img-fluid" alt="Product image ${index + 1}" style="
                                                        width: 100%;
                                                        height: auto;
                                                        max-height: 120px;
                                                        object-fit: contain;
                                                        border-radius: 10px;
                                                    ">
                                                </figure>
                                            </a>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                    </div>

                    <!-- Product Information Section -->
                    <div class="pro-info">
                        <h4 style="font-size: 1.6rem; font-weight: 700; color: #2d3748; margin-bottom: 15px;">
                            <span>${product.ProductName}</span>
                        </h4>

                        <div class="rating" style="margin-bottom: 15px;">
                            <i class="fa fa-star d-star" style="color: #ffd700;"></i>
                            <i class="fa fa-star d-star" style="color: #ffd700;"></i>
                            <i class="fa fa-star d-star" style="color: #ffd700;"></i>
                            <i class="fa fa-star d-star" style="color: #ffd700;"></i>
                            <i class="fa fa-star-o" style="color: #ddd;"></i>
                        </div>

                        <div class="pro-availabale" style="margin-bottom: 15px;">
                            <span class="available" style="color: #666;">Availability:</span>
                            <span class="pro-instock" style="color: #28a745; font-weight: 600;">In stock</span>
                        </div>

                        <div class="mrp-label" style="margin-bottom: 10px;">
                            <span style="color: #666; font-size: 14px;">MRP (including all taxes):</span>
                        </div>

                        <div class="pro-price" id="modal-pro-price" style="margin-bottom: 20px;">
                            ${defaultPrice.offer_price > 0 ? `
                                <span class="new-price" style="font-size: 1.8rem; font-weight: 700; color: #28a745;">₹${defaultPrice.offer_price.toFixed(2)} INR</span>
                                ${defaultPrice.mrp > defaultPrice.offer_price ? `<span class="old-price" style="font-size: 1.3rem; color: #dc3545; margin-left: 10px;"><del>₹${defaultPrice.mrp.toFixed(2)} INR</del></span>` : ''}
                                ${discount > 0 ? `
                                    <div class="Discount-Pro-lable" style="display: inline-block; margin-left: 10px;">
                                        <span class="Discount-p-discount" style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">₹${discount.toFixed(2)} OFF</span>
                                    </div>
                                ` : ''}
                            ` : '<span class="new-price" style="color: #666;">Price not available</span>'}
                        </div>

                        ${sizeOptionsHtml}

                        ${defaultPrice.coins > 0 ? `
                            <button style="background-color: #ec7524; margin-bottom: 20px; border: none; padding: 10px 15px; border-radius: 5px;" type="button" class="btn text-white">
                                <i class="fa fa-coins"></i>
                                <span id="modal-coins-message">Earn ${defaultPrice.coins} My Nutrify Coins On this Order.</span>
                                <i class="fa fa-info-circle"></i>
                            </button>
                        ` : ''}

                        <div class="pro-qty" style="margin-bottom: 20px;">
                            <span class="qty" style="font-weight: 600; margin-right: 15px;">Quantity:</span>
                            <div class="plus-minus" style="display: inline-flex; align-items: center; border: 2px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <span style="display: flex; align-items: center;">
                                    <a href="javascript:void(0)" class="minus-btn text-black" onclick="changeQuantity(-1)" style="
                                        background: #f8f9fa;
                                        border: none;
                                        width: 40px;
                                        height: 40px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        cursor: pointer;
                                        transition: all 0.3s ease;
                                        font-weight: 600;
                                        color: #4a5568;
                                        text-decoration: none;
                                    " onmouseover="this.style.background='#ff6a00'; this.style.color='white';" onmouseout="this.style.background='#f8f9fa'; this.style.color='#4a5568';">-</a>
                                    <input type="text" id="previewQuantity" value="1" style="border: none; width: 60px; height: 40px; text-align: center; font-weight: 600; background: white;">
                                    <a href="javascript:void(0)" class="plus-btn text-black" onclick="changeQuantity(1)" style="
                                        background: #f8f9fa;
                                        border: none;
                                        width: 40px;
                                        height: 40px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        cursor: pointer;
                                        transition: all 0.3s ease;
                                        font-weight: 600;
                                        color: #4a5568;
                                        text-decoration: none;
                                    " onmouseover="this.style.background='#ff6a00'; this.style.color='white';" onmouseout="this.style.background='#f8f9fa'; this.style.color='#4a5568';">+</a>
                                </span>
                            </div>
                        </div>

                        <div class="pro-btn" style="display: flex; gap: 15px;">
                            <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="${productId}" onclick="addToCartFromPreview(${productId})" style="
                                flex: 2;
                                background: linear-gradient(135deg, #ff6a00 0%, #e65c00 100%);
                                color: white;
                                border: none;
                                padding: 16px 24px;
                                border-radius: 10px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                font-size: 1rem;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 8px;
                                text-decoration: none;
                            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(255, 106, 0, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                <i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Add to Cart
                            </a>

                            <a href="product_details.php?ProductId=${productId}" class="btn btn-style1" style="
                                flex: 1;
                                background: transparent;
                                color: #ff6a00;
                                border: 2px solid #ff6a00;
                                padding: 16px 24px;
                                border-radius: 10px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                text-decoration: none;
                                text-align: center;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 1rem;
                            " onmouseover="this.style.background='#ff6a00'; this.style.color='white'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='transparent'; this.style.color='#ff6a00'; this.style.transform='translateY(0)';">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Initialize owl carousel for model images after modal content is rendered
        setTimeout(() => {
            initializeModalCarousel();
        }, 100);
    }

    // Initialize owl carousel for modal
    function initializeModalCarousel() {
        // Check if carousel element exists and has items
        const carouselElement = $('.product-main-slider-modal');
        if (carouselElement.length === 0) return;

        // Destroy existing carousel if it exists
        if (carouselElement.hasClass('owl-loaded')) {
            carouselElement.trigger('destroy.owl.carousel');
            carouselElement.removeClass('owl-loaded owl-drag');
        }

        // Initialize new carousel
        carouselElement.owlCarousel({
            items: 3,
            loop: false,
            margin: 15,
            nav: true,
            navText: [
                '<i class="fa fa-chevron-left"></i>',
                '<i class="fa fa-chevron-right"></i>'
            ],
            dots: false,
            autoplay: false,
            mouseDrag: true,
            touchDrag: true,
            smartSpeed: 600,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                480: {
                    items: 2,
                    nav: true
                },
                768: {
                    items: 3,
                    nav: true
                },
                1024: {
                    items: 3,
                    nav: true
                }
            }
        });
    }















    function closePreview() {
        const modal = document.getElementById('previewModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        currentProductImages = [];
        currentImageIndex = 0;
        currentQuantity = 1;
    }



    // Change preview image function
    function changePreviewImage(index) {
        currentImageIndex = index;
        const mainImage = document.getElementById('previewMainImage');
        if (mainImage && currentProductImages[index]) {
            mainImage.src = currentProductImages[index];

            // Update zoom background
            const zoomFigure = mainImage.closest('.zoom');
            if (zoomFigure) {
                zoomFigure.style.backgroundImage = `url('${currentProductImages[index]}')`;
            }
        }

        // Update thumbnail borders
        document.querySelectorAll('[class*="preview-thumbnail-"]').forEach((thumb, i) => {
            if (i === index) {
                thumb.style.border = '2px solid #ff6a00';
                thumb.classList.add('active');
            } else {
                thumb.style.border = '2px solid #e2e8f0';
                thumb.classList.remove('active');
            }
        });
    }

    // Change quantity function
    function changeQuantity(change) {
        const quantityInput = document.getElementById('previewQuantity');
        if (quantityInput) {
            let newQuantity = parseInt(quantityInput.value) + change;
            if (newQuantity < 1) newQuantity = 1;
            quantityInput.value = newQuantity;
            currentQuantity = newQuantity;
        }
    }



    // Modal zoom functionality
    function modalZoom(event) {
        const figure = event.currentTarget;
        const img = figure.querySelector('img');
        if (!img) return;

        const rect = figure.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;

        figure.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
        figure.style.backgroundSize = '200%';
    }

    // Open image in full screen modal
    function openImageModal(imageSrc) {
        // Create full screen image modal
        const imageModal = document.createElement('div');
        imageModal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            cursor: pointer;
        `;

        imageModal.innerHTML = `
            <img src="${imageSrc}" style="
                max-width: 90%;
                max-height: 90%;
                object-fit: contain;
                border-radius: 10px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            ">
            <button onclick="this.parentElement.remove()" style="
                position: absolute;
                top: 20px;
                right: 20px;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 20px;
                color: #333;
            ">×</button>
        `;

        imageModal.onclick = function(e) {
            if (e.target === imageModal) {
                imageModal.remove();
            }
        };

        document.body.appendChild(imageModal);
    }

    // Enhanced size selection handler for modal
    function handleModalSizeSelection(element) {
        // Remove selected class from all size boxes
        document.querySelectorAll('.size-box').forEach(box => {
            box.classList.remove('selected');
            box.style.border = '2px solid #e2e8f0';
            box.style.background = 'white';
        });

        // Add selected class to clicked size box
        element.classList.add('selected');
        element.style.border = '2px solid #ff6a00';
        element.style.background = '#fff5f0';

        // Get pricing data from the selected size
        const offerPrice = parseFloat(element.getAttribute('data-offer-price'));
        const mrp = parseFloat(element.getAttribute('data-mrp'));
        const coins = parseInt(element.getAttribute('data-coins'), 10);

        // Update price display
        updateModalPriceAndCoins(offerPrice, mrp, coins);
    }

    // Update price and coins display in modal
    function updateModalPriceAndCoins(offerPrice, mrp, coins) {
        const priceDiv = document.getElementById('modal-pro-price');
        const coinsMessage = document.getElementById('modal-coins-message');

        if (priceDiv && offerPrice > 0 && mrp > 0) {
            const discount = mrp - offerPrice;
            priceDiv.innerHTML = `
                <span class="new-price" style="font-size: 1.8rem; font-weight: 700; color: #28a745;">₹${offerPrice.toFixed(2)} INR</span>
                ${mrp > offerPrice ? `<span class="old-price" style="font-size: 1.3rem; color: #dc3545; margin-left: 10px;"><del>₹${mrp.toFixed(2)} INR</del></span>` : ''}
                ${discount > 0 ? `
                    <div class="Discount-Pro-lable" style="display: inline-block; margin-left: 10px;">
                        <span class="Discount-p-discount" style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">₹${discount.toFixed(2)} OFF</span>
                    </div>
                ` : ''}
            `;
        }

        // Update coins message
        if (coinsMessage && coins > 0) {
            coinsMessage.textContent = `Earn ${coins} My Nutrify Coins On this Order.`;
        }
    }

    // Enhanced image navigation (matching product_details.php)
    function changePreviewImage(index) {
        currentImageIndex = index;

        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        // Show selected tab pane
        const targetTab = index === 0 ?
            document.getElementById('modal-image-main') :
            document.getElementById(`modal-image-${index}`);

        if (targetTab) {
            targetTab.classList.add('show', 'active');
        }

        // Update thumbnail active states
        document.querySelectorAll('.nav-link').forEach((link, i) => {
            link.classList.remove('active');
            link.style.borderColor = '#e2e8f0';
        });

        const activeLink = document.querySelector(`.preview-thumbnail-${index}`);
        if (activeLink) {
            activeLink.classList.add('active');
            activeLink.style.borderColor = '#ff6a00';
        }

        // Update main image for zoom functionality
        const mainImage = document.getElementById('previewMainImage');
        if (mainImage && currentProductImages[index]) {
            mainImage.src = currentProductImages[index];

            // Update zoom background
            const zoomFigure = mainImage.closest('.zoom');
            if (zoomFigure) {
                zoomFigure.style.backgroundImage = `url('${currentProductImages[index]}')`;
            }
        }
    }

    // Modal zoom functionality (matching product_details.php)
    function modalZoom(event) {
        const zoomer = event.currentTarget;
        const offsetX = event.offsetX || event.touches?.[0]?.pageX - zoomer.offsetLeft || 0;
        const offsetY = event.offsetY || event.touches?.[0]?.pageY - zoomer.offsetTop || 0;
        const x = offsetX / zoomer.offsetWidth * 100;
        const y = offsetY / zoomer.offsetHeight * 100;
        zoomer.style.backgroundPosition = x + '% ' + y + '%';
    }



    function zoomImage() {
        const mainImage = document.getElementById('previewMainImage');
        if (!mainImage) return;

        const imgSrc = mainImage.src;

        // Create zoom overlay
        const zoomOverlay = document.createElement('div');
        zoomOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
        `;

        const zoomedImg = document.createElement('img');
        zoomedImg.src = imgSrc;
        zoomedImg.style.cssText = `
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        `;

        zoomOverlay.appendChild(zoomedImg);
        document.body.appendChild(zoomOverlay);

        zoomOverlay.addEventListener('click', () => {
            document.body.removeChild(zoomOverlay);
        });
    }

    function addToCartFromPreview(productId) {
        // Get selected size information (optional)
        const selectedSizeBox = document.querySelector('.size-box.selected');

        // Use default values if no size is selected
        const size = selectedSizeBox ? selectedSizeBox.getAttribute('data-size') : '';
        const offerPrice = selectedSizeBox ? selectedSizeBox.getAttribute('data-offer-price') : '';
        const mrp = selectedSizeBox ? selectedSizeBox.getAttribute('data-mrp') : '';
        const quantity = document.getElementById('previewQuantity').value;

        // Add loading state to button
        const addButton = document.querySelector('.add-to-cart-session');
        if (!addButton) return;

        const originalText = addButton.innerHTML;
        addButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
        addButton.disabled = true;

        // Use the same AJAX structure as product_details.php
        $.ajax({
            url: 'exe_files/add_to_cart_session.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'add_to_cart',
                productId: productId,
                size: size,
                quantity: quantity,
                offer_price: offerPrice,
                mrp: mrp
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Show success animation
                    addButton.innerHTML = '<i class="fa fa-check"></i> Added to Cart!';
                    addButton.style.background = '#22c55e';

                    // No popup - just add to cart silently
                    // displayCartPopup(); // Removed popup

                    setTimeout(() => {
                        // Reset button and close modal
                        addButton.innerHTML = originalText;
                        addButton.style.background = '';
                        addButton.disabled = false;
                        closePreview();
                    }, 1500);
                } else {
                    showNotification(response.message || 'Failed to add product to cart', 'error');
                    addButton.innerHTML = originalText;
                    addButton.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showNotification('An error occurred while processing your request. Please try again.', 'error');
                addButton.innerHTML = originalText;
                addButton.disabled = false;
            }
        });
    }

    // Display cart popup (matching product_details.php functionality)
    function displayCartPopup() {
        // Create popup if it doesn't exist
        let cartPopup = document.getElementById('cart-popup');
        if (!cartPopup) {
            cartPopup = document.createElement('div');
            cartPopup.id = 'cart-popup';
            cartPopup.className = 'cart-popup-overlay';
            cartPopup.innerHTML = `
                <div class="cart-popup-content" style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: #fff;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                    text-align: center;
                    max-width: 400px;
                    width: 90%;
                ">
                    <button class="close-popup" onclick="closeCartPopup()" style="
                        position: absolute;
                        top: 10px;
                        right: 15px;
                        background: none;
                        border: none;
                        font-size: 20px;
                        cursor: pointer;
                        color: #666;
                    ">&times;</button>
                    <h3 style="color: #333; margin-bottom: 15px;">Product added to your cart!</h3>
                    <div class="cart-popup-actions">
                        <a href="cart.php" class="btn-view-cart" style="
                            display: inline-block;
                            margin: 10px 5px;
                            padding: 10px 20px;
                            border-radius: 5px;
                            text-decoration: none;
                            color: white;
                            font-size: 14px;
                            font-weight: bold;
                            background: #305724;
                            transition: background-color 0.3s ease;
                        ">View Cart</a>
                        <a href="checkout.php" class="btn-checkout" style="
                            display: inline-block;
                            margin: 10px 5px;
                            padding: 10px 20px;
                            border-radius: 5px;
                            text-decoration: none;
                            color: white;
                            font-size: 14px;
                            font-weight: bold;
                            background: #ec6504;
                            transition: background-color 0.3s ease;
                        ">Checkout</a>
                    </div>
                </div>
            `;
            cartPopup.style.cssText = `
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 10000;
            `;
            document.body.appendChild(cartPopup);
        }

        // Show popup with fade effect
        cartPopup.style.display = 'block';
        cartPopup.style.opacity = '0';
        setTimeout(() => {
            cartPopup.style.transition = 'opacity 0.3s ease';
            cartPopup.style.opacity = '1';
        }, 10);

        // Auto-hide popup after 3 seconds
        setTimeout(() => {
            closeCartPopup();
        }, 3000);
    }

    function closeCartPopup() {
        const cartPopup = document.getElementById('cart-popup');
        if (cartPopup) {
            cartPopup.style.opacity = '0';
            setTimeout(() => {
                cartPopup.style.display = 'none';
            }, 300);
        }
    }

    // Function to open additional product images in modal view
    function openImageModal(imageSrc) {
        // Create image modal overlay
        const imageModal = document.createElement('div');
        imageModal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 10001;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
            animation: fadeIn 0.3s ease;
        `;

        const imageContainer = document.createElement('div');
        imageContainer.style.cssText = `
            max-width: 90%;
            max-height: 90%;
            position: relative;
        `;

        const zoomedImg = document.createElement('img');
        zoomedImg.src = imageSrc;
        zoomedImg.style.cssText = `
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        `;

        const closeButton = document.createElement('button');
        closeButton.innerHTML = '<i class="fas fa-times"></i>';
        closeButton.style.cssText = `
            position: absolute;
            top: -15px;
            right: -15px;
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
            font-size: 16px;
            color: #666;
            backdrop-filter: blur(10px);
        `;

        closeButton.onmouseover = function() {
            this.style.background = '#ff6a00';
            this.style.color = 'white';
            this.style.transform = 'scale(1.1)';
        };

        closeButton.onmouseout = function() {
            this.style.background = 'rgba(255, 255, 255, 0.9)';
            this.style.color = '#666';
            this.style.transform = 'scale(1)';
        };

        // Close modal function
        const closeImageModal = () => {
            imageModal.style.opacity = '0';
            setTimeout(() => {
                if (imageModal.parentNode) {
                    document.body.removeChild(imageModal);
                }
            }, 300);
        };

        closeButton.addEventListener('click', closeImageModal);
        imageModal.addEventListener('click', closeImageModal);

        // Prevent closing when clicking on the image
        imageContainer.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        imageContainer.appendChild(zoomedImg);
        imageContainer.appendChild(closeButton);
        imageModal.appendChild(imageContainer);
        document.body.appendChild(imageModal);

        // Fade in effect
        imageModal.style.opacity = '0';
        setTimeout(() => {
            imageModal.style.transition = 'opacity 0.3s ease';
            imageModal.style.opacity = '1';
        }, 10);
    }

    // Enhanced notification system
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notif => notif.remove());

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10001;
            font-weight: 500;
            max-width: 300px;
            animation: slideInRight 0.3s ease;
        `;

        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 3000);
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
                                // No popup - just add to cart silently
                                // $('#cart-popup').fadeIn();

                                // Show brief success message instead of popup
                                if (typeof showNotification === 'function') {
                                    showNotification('Product added to cart!', 'success');
                                }

                                // No page reload needed
                                // setTimeout(function () {
                                //     $('#cart-popup').fadeOut(function () {
                                //         location.reload(); // Reload the page after popup is hidden
                                //     });
                                // }, 3000);
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
            $(document).on('click', '.add-to-cart-session', function (e) { // Use event delegation
                // Skip if this button has onclick handler (modal buttons)
                if ($(this).attr('onclick')) {
                    return; // Let the onclick handler take care of it
                }

                e.preventDefault(); // Prevent any default behavior

                var productId = $(this).data('product-id'); // Get product ID
                var button = $(this); // Store reference to button

                console.log('Product ID:', productId); // Debugging: Log the product ID

                // Add loading state to button
                var originalText = button.html();
                button.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
                button.prop('disabled', true);

                $.ajax({
                    url: 'exe_files/add_to_cart_session.php', // PHP file to handle the cart addition in session
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'add_to_cart',
                        productId: productId
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            // Show success state on button
                            button.html('<i class="fa fa-check"></i> Added to Cart!');
                            button.css('background', '#22c55e');

                            // No popup - just add to cart silently
                            // if (typeof displayCartPopup === 'function') {
                            //     displayCartPopup();
                            // } else {
                            //     $('#cart-popup').fadeIn();
                            // }

                            // Reset button after 1.5 seconds
                            setTimeout(function() {
                                button.html(originalText);
                                button.css('background', '');
                                button.prop('disabled', false);
                            }, 1500);
                        } else {
                            // Show error and reset button
                            if (typeof showNotification === 'function') {
                                showNotification(response.message || 'Failed to add product to cart', 'error');
                            } else {
                                alert(response.message || 'Failed to add product to cart');
                            }
                            button.html(originalText);
                            button.prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        if (typeof showNotification === 'function') {
                            showNotification('An error occurred. Please try again.', 'error');
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                        button.html(originalText);
                        button.prop('disabled', false);
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
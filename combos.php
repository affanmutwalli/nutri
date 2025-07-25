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
    <title>My Nutrify - Combos</title>
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
    <!-- full width override -->
    <link rel="stylesheet" type="text/css" href="css/full-width-override.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    .text-container {
        padding-left: 60px;
        /* Adjust for larger screens */
        padding-right: 20px;
        /* Add some padding on the right for mobile devices */
        margin-left: 60px;
        /* Adjust for larger screens */
        margin-right: 0;
        /* Remove margin on the right for mobile devices */
    }

    @media (max-width: 768px) {
        .text-container {
            padding-left: 20px;
            /* Decrease padding on smaller screens */
            padding-right: 20px;
            /* Make sure it has some padding on mobile */
            margin-left: 0;
            /* Remove the margin-left on mobile */
            margin-right: 0;
            /* Remove margin-right on mobile */
        }
    }

    @media (max-width: 576px) {
        .text-container {
            padding-left: 10px;
            /* Further decrease padding on very small screens */
            padding-right: 10px;
            margin-left: 0;
            /* No margin on mobile */
        }
    }

    .btn-orange {
        background-color: #ff6a00;
        /* Orange background */
        color: white;
        /* White text */
        border: none;
        /* Remove border */
        border-radius: 25px;
        /* Rounded corners */
        padding: 10px 20px;
        /* Padding for button */
        font-size: 16px;
        /* Font size */
        display: flex;
        /* Align icon and text */
        align-items: center;
        /* Center vertically */
        justify-content: center;
        /* Center horizontally */
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin: 10px;
        /* Transition effects */
    }

    .btn-orange:hover {
        background-color: #e65c00;
        /* Darker orange on hover */
        transform: scale(1.05);
        /* Slightly increase the size on hover */
    }

    .btn-orange i {
        margin-right: 8px;
        /* Space between icon and text */
    }

    @media (max-width: 576px) {
        .btn-orange {
            padding: 12px 25px;
            /* Adjust padding for smaller screens */
        }
    }

    .pro-btn .btn {
        display: inline-block;
        width: 100%;
        max-width: 300px;
        padding: 10px 20px;
        font-size: 16px;
        text-align: center;
    }

    /* Combos Page Styles - Krishna Ayurveda Match */
    .combos-header {
        background: #fff;
        color: #333;
        padding: 40px 0 20px 0;
        margin-bottom: 30px;
        border-bottom: 1px solid #e5e5e5;
    }

    .combos-header h1 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0;
        text-align: left;
        color: #333;
    }

    .filter-sort-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .sort-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .products-count {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
    }

    .sort-dropdown {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sort-dropdown select {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: white;
        font-size: 14px;
        min-width: 180px;
    }



    /* Filter Sidebar Styles */
    .filter-sidebar {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 0;
        margin-bottom: 30px;
    }

    .filter-header {
        padding: 20px;
        border-bottom: 1px solid #e5e5e5;
    }

    .filter-group {
        border-bottom: 1px solid #e5e5e5;
        padding: 20px;
    }

    .filter-group:last-child {
        border-bottom: none;
    }

    .filter-group h4 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .filter-option {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-size: 14px;
        color: #666;
        position: relative;
        padding-left: 25px;
    }

    .filter-option input[type="checkbox"] {
        position: absolute;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    .checkmark {
        position: absolute;
        left: 0;
        height: 16px;
        width: 16px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 2px;
    }

    .filter-option:hover input ~ .checkmark {
        border-color: #305724;
    }

    .filter-option input:checked ~ .checkmark {
        background-color: #305724;
        border-color: #305724;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .filter-option input:checked ~ .checkmark:after {
        display: block;
    }

    .filter-option .checkmark:after {
        left: 5px;
        top: 2px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .count {
        color: #999;
        font-size: 12px;
        margin-left: auto;
    }

    .price-filter {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .price-range {
        position: relative;
        height: 20px;
    }

    .price-slider {
        position: absolute;
        width: 100%;
        height: 4px;
        background: #ddd;
        outline: none;
        border-radius: 2px;
        -webkit-appearance: none;
    }

    .price-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        background: #305724;
        cursor: pointer;
        border-radius: 50%;
    }

    .price-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        background: #305724;
        cursor: pointer;
        border-radius: 50%;
        border: none;
    }

    .price-inputs {
        text-align: center;
        font-size: 14px;
        color: #666;
    }

    .filter-apply-btn {
        width: calc(100% - 40px);
        background: #305724;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        margin: 20px;
        margin-top: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-apply-btn:hover {
        background: #254019;
    }

    .view-controls {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
        padding: 15px 0;
        border-bottom: 1px solid #e5e5e5;
    }

    .view-btn {
        padding: 8px 15px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        font-size: 12px;
        color: #666;
        border-right: 1px solid #ddd;
        transition: all 0.3s ease;
    }

    .view-btn:last-child {
        border-right: none;
    }

    .view-btn.active,
    .view-btn:hover {
        background: #305724;
        color: white;
    }

    /* Loading and Error States */
    .loading-item,
    .error-item,
    .no-products {
        text-align: center;
        padding: 40px 20px;
        color: #666;
        font-size: 16px;
        width: 100%;
        list-style: none;
    }

    .loading-item {
        background: #f8f9fa;
        border-radius: 8px;
    }

    .error-item {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        color: #856404;
    }

    .no-products {
        background: #e9ecef;
        border-radius: 8px;
    }

    /* Product Cards - Krishna Ayurveda Style */
    .enhanced-grid-items {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 30px;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .enhanced-grid-items:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        border-color: #ddd;
    }

    .sale-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #e74c3c;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        z-index: 2;
        text-transform: uppercase;
    }

    .product-image-container {
        position: relative;
        overflow: visible;
        height: 350px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 25px;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .product-image-container img {
        max-width: calc(100% - 10px);
        max-height: calc(100% - 10px);
        width: auto;
        height: auto;
        object-fit: contain;
        object-position: center;
        transition: transform 0.3s ease;
        filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1));
    }

    .enhanced-grid-items:hover .product-image-container img {
        transform: scale(1.05);
    }

    .quick-actions {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .enhanced-grid-items:hover .quick-actions {
        opacity: 1;
    }

    .quick-shop-btn {
        background: #305724;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .quick-shop-btn:hover {
        background: #254019;
        transform: translateY(-2px);
    }

    .quick-action-btn {
        background: rgba(255,255,255,0.9);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
        margin: 0 5px;
    }

    .quick-action-btn:hover {
        background: #305724;
        color: white;
        transform: scale(1.1);
    }

    .enhanced-caption {
        padding: 20px;
    }

    .enhanced-caption h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        line-height: 1.4;
        height: 44px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .enhanced-caption h3 a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .enhanced-caption h3 a:hover {
        color: #305724;
    }

    .rating-section {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .stars {
        display: flex;
        gap: 2px;
    }

    .stars i {
        color: #ffc107;
        font-size: 14px;
    }

    .stars i.empty {
        color: #ddd;
    }

    .review-count {
        font-size: 13px;
        color: #666;
        margin-left: 5px;
    }

    .enhanced-price {
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .enhanced-price .new-price {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        display: block;
        margin-bottom: 2px;
    }

    .enhanced-price .price-details {
        font-size: 12px;
        color: #666;
    }

    .enhanced-price .old-price {
        text-decoration: line-through;
        margin-right: 5px;
    }

    .enhanced-price .savings-text {
        color: #e74c3c;
        font-weight: 600;
    }

    .savings-badge {
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .enhanced-add-to-cart {
        width: 100%;
        background: #305724;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .enhanced-add-to-cart:hover {
        background: #254019;
        transform: translateY(-1px);
    }

    /* Pagination Styles */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }

    .pagination {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .pagination a, .pagination span {
        padding: 10px 15px;
        border: 1px solid #ddd;
        color: #333;
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #305724;
        color: white;
        border-color: #305724;
    }

    .pagination .current {
        background: #305724;
        color: white;
        border-color: #305724;
    }

    /* List View Styles */
    .list-view .grid-product {
        display: block !important;
    }

    .list-view .enhanced-grid-items {
        display: flex !important;
        align-items: center;
        margin-bottom: 20px;
        padding: 20px;
    }

    .list-view .product-image-container {
        width: 240px;
        height: 220px;
        flex-shrink: 0;
        margin-right: 20px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        border-radius: 8px;
        box-sizing: border-box;
        overflow: visible;
    }

    .list-view .enhanced-caption {
        flex: 1;
        padding: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 150px;
    }

    .list-view .enhanced-caption h3 {
        height: auto;
        margin-bottom: 8px;
    }

    .list-view .rating-section {
        margin-bottom: 8px;
    }

    .list-view .enhanced-price {
        margin-bottom: 10px;
    }

    .list-view .enhanced-add-to-cart {
        width: auto;
        padding: 8px 20px;
        align-self: flex-start;
    }

    .list-view .quick-actions {
        position: static;
        opacity: 1;
        transform: none;
        margin-left: 15px;
        flex-direction: column;
        gap: 5px;
    }

    .list-view .sale-badge {
        position: static;
        margin-bottom: 10px;
        align-self: flex-start;
    }

    /* Grid View Responsive */
    @media (max-width: 1200px) {
        .grid-view .grid-product li.enhanced-grid-items {
            width: calc(50% - 15px);
            margin-left: 15px;
        }
    }

    @media (max-width: 768px) {
        .grid-view .grid-product li.enhanced-grid-items {
            width: calc(100% - 15px);
            margin-left: 15px;
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .combos-header h1 {
            font-size: 1.5rem;
        }

        .sort-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .sort-dropdown {
            justify-content: space-between;
        }



        .enhanced-grid-items {
            margin-bottom: 20px;
        }

        .product-image-container {
            height: 320px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            border-radius: 8px;
            box-sizing: border-box;
            overflow: visible;
        }

        .enhanced-caption {
            padding: 15px;
        }

        /* List view mobile adjustments */
        .list-view .enhanced-grid-items {
            flex-direction: column;
            text-align: center;
        }

        .list-view .product-image-container {
            width: 100%;
            height: 320px;
            margin-right: 0;
            margin-bottom: 15px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            border-radius: 8px;
            box-sizing: border-box;
            overflow: visible;
        }

        .list-view .enhanced-caption {
            height: auto;
            padding: 0 15px;
        }

        .list-view .enhanced-add-to-cart {
            width: 100%;
            align-self: stretch;
        }

        .list-view .quick-actions {
            flex-direction: row;
            justify-content: center;
            margin: 10px 0;
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

    <!-- Combos Header -->
    <div class="combos-header">
        <div class="container">
            <h1>Combos</h1>
        </div>
    </div>

    <!-- grid-list start -->
    <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <!-- Filter Sidebar -->
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="filter-sidebar">
                        <!-- Product Count and Sort -->
                        <div class="filter-header">
                            <div class="products-count">
                                <span id="product-count">Loading...</span> products
                            </div>
                            <div class="sort-dropdown">
                                <label for="sort-select">Sort</label>
                                <select id="sort-select" onchange="sortProducts()">
                                    <option value="featured">Featured</option>
                                    <option value="best-selling">Best selling</option>
                                    <option value="name-asc">Alphabetically, A-Z</option>
                                    <option value="name-desc">Alphabetically, Z-A</option>
                                    <option value="price-low">Price, low to high</option>
                                    <option value="price-high">Price, high to low</option>
                                    <option value="date-old">Date, old to new</option>
                                    <option value="date-new">Date, new to old</option>
                                </select>
                            </div>
                        </div>

                        <!-- Product Type Filter -->
                        <div class="filter-group">
                            <h4>Product type</h4>
                            <div class="filter-options" id="product-type-filters">
                                <label class="filter-option">
                                    <input type="checkbox" name="product_type" value="combos" checked>
                                    <span class="checkmark"></span>
                                    Combos <span class="count" id="combos-count">(0)</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="product_type" value="cosmetics">
                                    <span class="checkmark"></span>
                                    Cosmetics <span class="count" id="cosmetics-count">(0)</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="product_type" value="herbal-powders">
                                    <span class="checkmark"></span>
                                    Herbal Powders/Churna <span class="count" id="herbal-count">(0)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h4>Category</h4>
                            <div class="filter-options" id="category-filters">
                                <!-- Categories will be loaded dynamically -->
                            </div>
                        </div>

                        <!-- Subcategory Filter -->
                        <div class="filter-group">
                            <h4>Subcategory</h4>
                            <div class="filter-options" id="subcategory-filters">
                                <!-- Subcategories will be loaded dynamically -->
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-group">
                            <h4>Price</h4>
                            <div class="price-filter">
                                <div class="price-range">
                                    <input type="range" id="price-min" min="0" max="2000" value="0" class="price-slider">
                                    <input type="range" id="price-max" min="0" max="2000" value="2000" class="price-slider">
                                </div>
                                <div class="price-inputs">
                                    <span>₹<span id="price-min-value">0</span> - ₹<span id="price-max-value">2000</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Availability Filter -->
                        <div class="filter-group">
                            <h4>Availability</h4>
                            <div class="filter-options" id="availability-filters">
                                <label class="filter-option">
                                    <input type="checkbox" name="availability" value="in-stock" checked>
                                    <span class="checkmark"></span>
                                    In stock <span class="count" id="in-stock-count">(0)</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="availability" value="out-of-stock">
                                    <span class="checkmark"></span>
                                    Out of stock <span class="count" id="out-stock-count">(0)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Filter Button -->
                        <button class="filter-apply-btn" onclick="applyFilters()">Filter</button>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="col-lg-9 col-md-8 col-12">

                <!-- Product Section -->
                <div class="col-lg-12 col-md-12 col-12">
                    <!--<div class="grid-list-banner d-none d-md-block"-->
                    <!--    style="background-image: url(image/product_banner.webp);">-->
                    <!--    <div class="d-flex justify-content-end text-white text-right">-->
                    <!--        <div class="" style="padding-left: 350px; margin-left:60px; margin-top:30px;">-->
                                <!--<h4 class="text-white">Bestseller</h4>-->
                                <!--<p>-->
                                <!--    Praesent dapibus, neque id cursus Ucibus, tortor neque egestas augue,-->
                                <!--    eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi,-->
                                <!--    tincidunt quis, facilisis luc...-->
                                <!--</p>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!-- Product Listing -->
                    <div class="grid-list-area" id="products-container">
                        <div class="grid-pro">
                            <ul class="grid-product" id="product-list">
                                <?php
                                    // Default query to fetch all products - SIMPLE VERSION FIRST
                                    $FieldNames = array("ProductId", "ProductName", "PhotoPath", "ShortDescription");
                                    $ParamArray = array();
                                    $Fields = implode(",", $FieldNames);
                                    $query = "SELECT " . $Fields . " FROM product_master where IsCombo = 'Y' ORDER BY ProductId DESC";
                                    $all_products = $obj->MysqliSelect1($query, $FieldNames, "", $ParamArray);

                                    $product_count = 0;

                                if (!empty($all_products)) {
                                    foreach ($all_products as $products) {
                                        $product_count++;

                                        // Get category and subcategory info for this product
                                        // First get category info
                                        $categoryQuery = "SELECT pm.CategoryId, cm.CategoryName
                                                         FROM product_master pm
                                                         LEFT JOIN category_master cm ON pm.CategoryId = cm.CategoryId
                                                         WHERE pm.ProductId = ?";
                                        $categoryResult = $obj->MysqliSelect1($categoryQuery,
                                            array("CategoryId", "CategoryName"),
                                            "i", array($products["ProductId"]));

                                        $categoryId = $categoryResult[0]["CategoryId"] ?? '';
                                        $categoryName = $categoryResult[0]["CategoryName"] ?? '';

                                        // Check if multiple subcategories system is being used
                                        $multiSubQuery = "SELECT COUNT(*) as count FROM product_subcategories ps WHERE ps.ProductId = ?";
                                        $multiSubResult = $obj->MysqliSelect1($multiSubQuery, array("count"), "i", array($products["ProductId"]));
                                        $useMultipleSubcategories = ($multiSubResult[0]['count'] ?? 0) > 0;

                                        $subcategoryId = '';
                                        $subcategoryName = '';
                                        $allSubcategoryIds = array();
                                        $allSubcategoryNames = array();

                                        if ($useMultipleSubcategories) {
                                            // Use junction table for subcategories
                                            $subcategoryQuery = "SELECT sc.SubCategoryId, sc.SubCategoryName, ps.is_primary
                                                               FROM product_subcategories ps
                                                               INNER JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                                                               WHERE ps.ProductId = ?
                                                               ORDER BY ps.is_primary DESC, sc.SubCategoryName";
                                            $subcategoryResult = $obj->MysqliSelect1($subcategoryQuery,
                                                array("SubCategoryId", "SubCategoryName", "is_primary"),
                                                "i", array($products["ProductId"]));

                                            if (!empty($subcategoryResult)) {
                                                foreach ($subcategoryResult as $subcat) {
                                                    $allSubcategoryIds[] = $subcat["SubCategoryId"];
                                                    $allSubcategoryNames[] = $subcat["SubCategoryName"];

                                                    // Use primary subcategory as main, or first one if no primary
                                                    if (empty($subcategoryId) || $subcat["is_primary"] == 1) {
                                                        $subcategoryId = $subcat["SubCategoryId"];
                                                        $subcategoryName = $subcat["SubCategoryName"];
                                                    }
                                                }
                                            }
                                        } else {
                                            // Use direct subcategory relationship
                                            $subcategoryQuery = "SELECT pm.SubCategoryId, sc.SubCategoryName
                                                               FROM product_master pm
                                                               LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId
                                                               WHERE pm.ProductId = ?";
                                            $subcategoryResult = $obj->MysqliSelect1($subcategoryQuery,
                                                array("SubCategoryId", "SubCategoryName"),
                                                "i", array($products["ProductId"]));

                                            if (!empty($subcategoryResult)) {
                                                $subcategoryId = $subcategoryResult[0]["SubCategoryId"] ?? '';
                                                $subcategoryName = $subcategoryResult[0]["SubCategoryName"] ?? '';
                                                if ($subcategoryId) {
                                                    $allSubcategoryIds[] = $subcategoryId;
                                                    $allSubcategoryNames[] = $subcategoryName;
                                                }
                                            }
                                        }

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

                                        // Fetch review count for rating display
                                        $FieldNamesReview = array("COUNT(*) as review_count");
                                        $ParamArrayReview = array($products["ProductId"]);
                                        $review_data = $obj->MysqliSelect1(
                                            "SELECT COUNT(*) as review_count FROM product_review WHERE ProductId = ?",
                                            $FieldNamesReview,
                                            "i",
                                            $ParamArrayReview
                                        );
                                        $review_count = (!empty($review_data) && isset($review_data[0]['review_count'])) ? $review_data[0]['review_count'] : 0;

                                        $lowest_price = PHP_INT_MAX; // Initialize to a very high value
                                        $mrp = PHP_INT_MAX;          // Initialize to a very high value
                                        $savings = 0;                // Default savings
                                        $savings_percentage = 0;     // Savings percentage

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
                                                $savings_percentage = round(($savings / $mrp) * 100);
                                            } else {
                                                $lowest_price = "N/A";
                                                $mrp = "N/A";
                                            }
                                        }

                                        // Generate star rating (placeholder - you can implement actual rating logic)
                                        $rating = 4.5; // Default rating
                                        $full_stars = floor($rating);
                                        $half_star = ($rating - $full_stars) >= 0.5;
                                        $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                        ?>
                                        <li class="grid-items enhanced-grid-items"
                                            data-name="<?php echo htmlspecialchars($products["ProductName"]); ?>"
                                            data-price="<?php echo $lowest_price != 'N/A' ? $lowest_price : -1; ?>"
                                            data-date="<?php echo $products["ProductId"]; ?>"
                                            data-category-id="<?php echo htmlspecialchars($categoryId); ?>"
                                            data-subcategory-id="<?php echo htmlspecialchars($subcategoryId); ?>"
                                            data-all-subcategory-ids="<?php echo htmlspecialchars(implode(',', $allSubcategoryIds)); ?>"
                                            data-category-name="<?php echo htmlspecialchars($categoryName); ?>"
                                            data-subcategory-name="<?php echo htmlspecialchars($subcategoryName); ?>"
                                            data-all-subcategory-names="<?php echo htmlspecialchars(implode(',', $allSubcategoryNames)); ?>">

                                            <?php if ($savings > 0): ?>
                                                <div class="sale-badge">Sale</div>
                                            <?php endif; ?>

                                            <div class="product-image-container">
                                                <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                                    <img class="img-fluid" src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>" alt="<?php echo htmlspecialchars($products["ProductName"]); ?>">
                                                </a>

                                                <div class="quick-actions">
                                                    <button class="quick-shop-btn" onclick="window.location.href='product_details.php?ProductId=<?php echo $products['ProductId']; ?>'">
                                                        Quick shop
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="enhanced-caption">
                                                <h3>
                                                    <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                                        <?php echo htmlspecialchars($products["ProductName"]); ?>
                                                    </a>
                                                </h3>

                                                <div class="rating-section">
                                                    <div class="stars">
                                                        <?php for ($i = 0; $i < $full_stars; $i++): ?>
                                                            <i class="fa fa-star"></i>
                                                        <?php endfor; ?>
                                                        <?php if ($half_star): ?>
                                                            <i class="fa fa-star-half-o"></i>
                                                        <?php endif; ?>
                                                        <?php for ($i = 0; $i < $empty_stars; $i++): ?>
                                                            <i class="fa fa-star empty"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="review-count">(<?php echo $review_count; ?>)</span>
                                                </div>

                                                <div class="enhanced-price">
                                                    <?php if ($lowest_price != "N/A"): ?>
                                                        <span class="new-price">from ₹<?php echo number_format($lowest_price); ?></span>
                                                        <?php if ($mrp != "N/A" && $savings > 0): ?>
                                                            <div class="price-details">
                                                                <span class="old-price">Regular price ₹<?php echo number_format($mrp); ?></span>
                                                                <span class="savings-text">Save ₹<?php echo $savings; ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="new-price">Price on request</span>
                                                    <?php endif; ?>
                                                </div>

                                                <button class="enhanced-add-to-cart add-to-cart-session" data-product-id="<?php echo htmlspecialchars($products['ProductId']); ?>">
                                                    Add to cart
                                                </button>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                } else {
                                    echo '<li class="grid-items no-products"><p>No combo products found.</p></li>';
                                }
                                ?>
                                <script>
                                    // Update product count after PHP renders
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const productCount = <?php echo $product_count; ?>;
                                        document.getElementById('product-count').textContent = productCount;
                                    });
                                </script>
                            </ul>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <div class="pagination">
                            <a href="#" class="prev-page">← Previous</a>
                            <span class="current">1</span>
                            <a href="#">2</a>
                            <a href="#">3</a>
                            <a href="#" class="next-page">Next →</a>
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
                </div> <!-- Close products column -->
            </div> <!-- Close row -->
        </div> <!-- Close container -->
    </section>

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
       $(document).ready(function () {
            // Update product count
            updateProductCount();

            // Initialize view
            initializeView();
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

        // Enhanced Combos Page Functions
        function updateProductCount() {
            const productItems = document.querySelectorAll('.enhanced-grid-items:not(.no-products)');
            const count = productItems.length;
            document.getElementById('product-count').textContent = count;
        }



        function sortProducts() {
            const sortValue = document.getElementById('sort-select').value;
            const productList = document.getElementById('product-list');
            const products = Array.from(productList.querySelectorAll('.enhanced-grid-items:not(.no-products)'));

            products.sort((a, b) => {
                switch (sortValue) {
                    case 'name-asc':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'name-desc':
                        return b.dataset.name.localeCompare(a.dataset.name);
                    case 'price-low':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price-high':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'newest':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    default: // featured
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                }
            });

            // Clear and re-append sorted products
            products.forEach(product => {
                productList.appendChild(product);
            });
        }

        function quickView(productId) {
            // Implement quick view functionality
            // You can redirect to product details or open a modal
            window.location.href = 'product_details.php?ProductId=' + productId;
        }

        // Client-side filter functionality (works with existing rendered products)
        function applyFilters() {
            console.log('Applying client-side filters...');

            // Get all product items
            const productItems = document.querySelectorAll('.enhanced-grid-items');
            const productCount = document.getElementById('product-count');

            // Collect filter data
            const filterData = {
                product_type: Array.from(document.querySelectorAll('input[name="product_type"]:checked')).map(cb => cb.value),
                category: Array.from(document.querySelectorAll('input[name="category"]:checked')).map(cb => cb.value),
                subcategory: Array.from(document.querySelectorAll('input[name="subcategory"]:checked')).map(cb => cb.value),
                availability: Array.from(document.querySelectorAll('input[name="availability"]:checked')).map(cb => cb.value),
                price_min: parseInt(document.getElementById('price-min').value) || 0,
                price_max: parseInt(document.getElementById('price-max').value) || 2000,
                sort: document.getElementById('sort-select').value
            };

            console.log('Filter data:', filterData);

            let visibleCount = 0;

            // Filter each product
            productItems.forEach(item => {
                let shouldShow = true;

                // Debug: Log product data (only for first few products to avoid spam)
                if (visibleCount < 3) {
                    console.log('Product:', {
                        name: item.dataset.name,
                        categoryId: item.dataset.categoryId,
                        subcategoryId: item.dataset.subcategoryId,
                        allSubcategoryIds: item.dataset.allSubcategoryIds,
                        price: item.dataset.price
                    });
                }

                // Product type filter - simplified for combos page
                if (filterData.product_type.length > 0) {
                    // Since we're on combos page, only show if combos is selected
                    // If no product types are selected or combos is not selected, hide
                    if (!filterData.product_type.includes('combos')) {
                        shouldShow = false;
                        if (visibleCount < 3) console.log('Hidden by product type filter - combos not selected');
                    }
                }

                // Category filter
                if (shouldShow && filterData.category.length > 0) {
                    const itemCategoryId = String(item.dataset.categoryId || '');
                    const hasMatchingCategory = filterData.category.some(catId => String(catId) === itemCategoryId);
                    if (visibleCount < 3) {
                        console.log('Category filter check:', {
                            selectedCategories: filterData.category,
                            itemCategoryId: itemCategoryId,
                            match: hasMatchingCategory
                        });
                    }
                    if (!hasMatchingCategory) {
                        shouldShow = false;
                        if (visibleCount < 3) console.log('Hidden by category filter');
                    }
                }

                // Subcategory filter - improved to check all subcategories
                if (shouldShow && filterData.subcategory.length > 0) {
                    const itemSubcategoryId = String(item.dataset.subcategoryId || '');
                    const allSubcategoryIds = (item.dataset.allSubcategoryIds || '').split(',').filter(id => id.trim() !== '');

                    // Check if any of the product's subcategories match the selected filters
                    let hasMatchingSubcategory = false;

                    // Check primary subcategory
                    if (itemSubcategoryId && filterData.subcategory.some(subId => String(subId) === itemSubcategoryId)) {
                        hasMatchingSubcategory = true;
                    }

                    // Check all subcategories
                    if (!hasMatchingSubcategory && allSubcategoryIds.length > 0) {
                        hasMatchingSubcategory = allSubcategoryIds.some(subId =>
                            filterData.subcategory.some(selectedSubId => String(selectedSubId) === String(subId))
                        );
                    }

                    if (visibleCount < 3) {
                        console.log('Subcategory filter check:', {
                            selectedSubcategories: filterData.subcategory,
                            itemSubcategoryId: itemSubcategoryId,
                            allSubcategoryIds: allSubcategoryIds,
                            match: hasMatchingSubcategory
                        });
                    }

                    if (!hasMatchingSubcategory) {
                        shouldShow = false;
                        if (visibleCount < 3) console.log('Hidden by subcategory filter');
                    }
                }

                // Price filter - improved to handle products without prices
                if (shouldShow) {
                    const itemPrice = parseFloat(item.dataset.price);

                    // Only apply price filter if product has a valid price (not -1 for N/A)
                    if (!isNaN(itemPrice) && itemPrice >= 0) {
                        if (itemPrice < filterData.price_min || itemPrice > filterData.price_max) {
                            shouldShow = false;
                            if (visibleCount < 3) console.log('Hidden by price filter:', itemPrice, 'not in range', filterData.price_min, '-', filterData.price_max);
                        }
                    }
                    // Products with N/A prices (itemPrice === -1) are always shown regardless of price filter
                }

                // Availability filter
                if (shouldShow && filterData.availability.length > 0) {
                    // For now, all combo products are considered in stock
                    if (!filterData.availability.includes('in-stock')) {
                        shouldShow = false;
                        if (visibleCount < 3) console.log('Hidden by availability filter');
                    }
                }

                if (visibleCount < 3) {
                    console.log('Final decision for', item.dataset.name, ':', shouldShow ? 'SHOW' : 'HIDE');
                }

                // Show/hide the product
                if (shouldShow) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Update product count
            productCount.textContent = visibleCount;

            // Apply sorting if needed
            if (filterData.sort && filterData.sort !== 'featured') {
                applySorting(filterData.sort);
            }

            console.log('Filtered products:', visibleCount, 'out of', productItems.length);
        }

        // Apply sorting to visible products
        function applySorting(sortType) {
            const productContainer = document.getElementById('product-list');
            const productItems = Array.from(productContainer.querySelectorAll('.enhanced-grid-items'));

            productItems.sort((a, b) => {
                switch (sortType) {
                    case 'name-asc':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'name-desc':
                        return b.dataset.name.localeCompare(a.dataset.name);
                    case 'price-low':
                        const priceA = parseFloat(a.dataset.price);
                        const priceB = parseFloat(b.dataset.price);
                        // Handle N/A prices (-1) by putting them at the end
                        if (priceA === -1 && priceB === -1) return 0;
                        if (priceA === -1) return 1;
                        if (priceB === -1) return -1;
                        return priceA - priceB;
                    case 'price-high':
                        const priceA2 = parseFloat(a.dataset.price);
                        const priceB2 = parseFloat(b.dataset.price);
                        // Handle N/A prices (-1) by putting them at the end
                        if (priceA2 === -1 && priceB2 === -1) return 0;
                        if (priceA2 === -1) return 1;
                        if (priceB2 === -1) return -1;
                        return priceB2 - priceA2;
                    case 'date-new':
                        return (parseInt(b.dataset.date) || 0) - (parseInt(a.dataset.date) || 0);
                    case 'date-old':
                        return (parseInt(a.dataset.date) || 0) - (parseInt(b.dataset.date) || 0);
                    default:
                        return 0;
                }
            });

            // Re-append sorted items
            productItems.forEach(item => {
                productContainer.appendChild(item);
            });
        }

        // No longer needed - we're using client-side filtering with existing rendered products

        // Price range slider functionality
        function initializePriceSliders() {
            const priceMin = document.getElementById('price-min');
            const priceMax = document.getElementById('price-max');
            const priceMinValue = document.getElementById('price-min-value');
            const priceMaxValue = document.getElementById('price-max-value');

            function updatePriceDisplay() {
                priceMinValue.textContent = priceMin.value;
                priceMaxValue.textContent = priceMax.value;
            }

            priceMin.addEventListener('input', updatePriceDisplay);
            priceMax.addEventListener('input', updatePriceDisplay);

            priceMin.addEventListener('change', applyFilters);
            priceMax.addEventListener('change', applyFilters);
        }

        // Function to attach add to cart event listeners
        function attachAddToCartListeners() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-session');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addToCartSession(productId);
                });
            });
        }

        // Update sort products function to use client-side filtering
        function sortProducts() {
            console.log('Sort products called');
            applyFilters(); // Use the same filter function which includes sorting
        }

        // Load filter counts from database
        function loadFilterCounts() {
            console.log('Loading filter counts...');
            fetch('get_filter_counts.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Filter counts loaded:', data);
                if (data.success) {
                    const counts = data.filter_counts;

                    // Update product type counts
                    const combosCount = document.getElementById('combos-count');
                    const cosmeticsCount = document.getElementById('cosmetics-count');
                    const herbalCount = document.getElementById('herbal-count');

                    if (combosCount) combosCount.textContent = `(${counts.product_type.combos})`;
                    if (cosmeticsCount) cosmeticsCount.textContent = `(${counts.product_type.cosmetics})`;
                    if (herbalCount) herbalCount.textContent = `(${counts.product_type['herbal-powders']})`;

                    // Load categories dynamically
                    loadCategoryFilters(counts.categories);

                    // Load subcategories dynamically
                    loadSubcategoryFilters(counts.subcategories);

                    // Update availability counts
                    const inStockCount = document.getElementById('in-stock-count');
                    const outStockCount = document.getElementById('out-stock-count');

                    if (inStockCount) inStockCount.textContent = `(${counts.availability['in-stock']})`;
                    if (outStockCount) outStockCount.textContent = `(${counts.availability['out-of-stock']})`;

                    // Update price range
                    const priceMin = document.getElementById('price-min');
                    const priceMax = document.getElementById('price-max');
                    const priceMinValue = document.getElementById('price-min-value');
                    const priceMaxValue = document.getElementById('price-max-value');

                    if (priceMin && priceMax && priceMinValue && priceMaxValue) {
                        priceMin.min = counts.price_range.min;
                        priceMin.max = counts.price_range.max;
                        priceMin.value = counts.price_range.min;

                        priceMax.min = counts.price_range.min;
                        priceMax.max = counts.price_range.max;
                        priceMax.value = counts.price_range.max;

                        priceMinValue.textContent = counts.price_range.min;
                        priceMaxValue.textContent = counts.price_range.max;
                    }

                    // Hide filters with 0 count
                    try {
                        if (counts.product_type.cosmetics === 0) {
                            const cosmeticsFilter = document.querySelector('input[value="cosmetics"]');
                            if (cosmeticsFilter) {
                                cosmeticsFilter.closest('.filter-option').style.display = 'none';
                            }
                        }
                        if (counts.product_type['herbal-powders'] === 0) {
                            const herbalFilter = document.querySelector('input[value="herbal-powders"]');
                            if (herbalFilter) {
                                herbalFilter.closest('.filter-option').style.display = 'none';
                            }
                        }
                    } catch (e) {
                        console.warn('Error hiding zero-count filters:', e);
                    }

                    console.log('Filter counts applied successfully');
                } else {
                    console.error('Failed to load filter counts:', data.error || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error loading filter counts:', error);
                // Fallback: still initialize the page even if filter counts fail
                console.log('Continuing with default filter setup...');
            });
        }

        // Load category filters dynamically
        function loadCategoryFilters(categories) {
            const categoryContainer = document.getElementById('category-filters');
            let categoryHTML = '';

            if (categories && categories.length > 0) {
                categories.forEach(category => {
                    if (category.count > 0) {
                        categoryHTML += `
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="${category.id}">
                                <span class="checkmark"></span>
                                ${category.name} <span class="count">(${category.count})</span>
                            </label>
                        `;
                    }
                });
            }

            if (categoryHTML === '') {
                categoryHTML = '<p style="color: #666; font-size: 14px;">No categories available</p>';
            }

            categoryContainer.innerHTML = categoryHTML;

            // Add event listeners to new checkboxes
            const newCheckboxes = categoryContainer.querySelectorAll('input[type="checkbox"]');
            newCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('Category filter changed:', this.value, this.checked);
                    applyFilters();
                });
            });
        }

        // Load subcategory filters dynamically
        function loadSubcategoryFilters(subcategories) {
            console.log('Loading', subcategories?.length || 0, 'subcategories');
            const subcategoryContainer = document.getElementById('subcategory-filters');
            let subcategoryHTML = '';

            if (subcategories && subcategories.length > 0) {
                subcategories.forEach(subcategory => {
                    if (subcategory.count > 0) {
                        subcategoryHTML += `
                            <label class="filter-option">
                                <input type="checkbox" name="subcategory" value="${subcategory.id}">
                                <span class="checkmark"></span>
                                ${subcategory.name} <span class="count">(${subcategory.count})</span>
                            </label>
                        `;
                    }
                });
            }

            if (subcategoryHTML === '') {
                subcategoryHTML = '<p style="color: #666; font-size: 14px;">No subcategories available</p>';
            }

            subcategoryContainer.innerHTML = subcategoryHTML;

            // Add event listeners to new checkboxes
            const newCheckboxes = subcategoryContainer.querySelectorAll('input[type="checkbox"]');
            newCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('Subcategory filter changed:', this.value, this.checked);
                    applyFilters();
                });
            });
        }

        // Initialize filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing combos page...');

            // Load filter counts first
            loadFilterCounts();

            initializePriceSliders();

            // Add event listeners to existing filter checkboxes (product type and availability)
            const existingFilterCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
            existingFilterCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('Filter changed:', this.name, this.value, this.checked);
                    applyFilters();
                });
            });

            // Add event listener to sort dropdown
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    console.log('Sort changed:', this.value);
                    applyFilters();
                });
            }

            // Attach initial add to cart listeners
            attachAddToCartListeners();

            // Initial filter application to ensure everything is displayed correctly
            setTimeout(() => {
                console.log('Applying initial filters...');
                applyFilters();
            }, 500); // Small delay to ensure dynamic content is loaded

            console.log('Combos page initialized. Products loaded via PHP.');
        });
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
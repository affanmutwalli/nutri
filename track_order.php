<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>My Nutrify - Your Destination for Health & Wellness</title>
    <meta name="description"
        content="MyNutrify offers a wide range of organic and Ayurveda products for your health and wellness. Explore a variety of natural products to nourish your body and mind." />
    <meta name="keywords"
        content="organic food, health products, Ayurveda, natural supplements, wellness, herbal products, nutrition, healthy living" />
    <meta name="author" content="MyNutrify">

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
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* Base Styles */
    .tracking-header {
    margin-bottom: 20px;
    text-align: center;  /* Centered by default */
}

/* Left-align on smaller screens */
@media (max-width: 768px) {
    .tracking-header {
        text-align: left;  
    }
}


    .tracking-title {
        color: #2A4B1E;
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .tracking-subtitle {
        color: #6C757D;
        font-size: 1.2rem;
        line-height: 1.5;
    }

    /* Radio Switch */
    .track-switch {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 20px;
    }

    .form-check-inline {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        accent-color: #85B26F; /* Primary color */
        cursor: pointer;
        transition: transform 0.3s;
    }

    .form-check-input:checked {
        transform: scale(1.1);
    }

    .form-check-label {
        font-weight: 600;
        color: #495057; /* Text color */
        font-size: 1.1rem;
    }

    /* Form Input & Button */
    .tracking-form {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .input-group {
        display: flex;
        gap: 10px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: hidden;
    }

    .form-control {
        width: 100%;
        padding: 15px 20px;
        font-size: 1rem;
        border: 2px solid #E9ECEF;
        border-radius: 8px 0 0 8px;
        transition: border 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #85B26F; /* Primary color */
    }

    .btn-track {
        background: #85B26F; /* Primary color */
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        padding: 15px 25px;
        border: none;
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn-track:hover {
        background: #6D945C; /* Darker shade of primary color */
    }

    /* Product Carousel */
    .tracking-product-carousel {
        margin: 40px 0;
    }

    .tracking-carousel-item {
        display: flex;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .tracking-carousel-item:hover {
        transform: translateY(-5px);
    }

    .tracking-product-card {
        background: #fff; /* Background color */
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
        text-align: center;
        padding: 20px;
    }

    .tracking-product-card:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .tracking-product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .tracking-product-card:hover .tracking-product-image {
        transform: scale(1.05);
    }

    .tracking-product-info h3 {
        font-size: 1.25rem;
        color: #2a4b1e;
        margin-bottom: 10px;
    }

    .tracking-product-info a {
        text-decoration: none;
        color: rgb(236, 236, 236);
        transition: color 0.3s ease;
    }

    .tracking-product-info a:hover {
        color: #85b26f; /* Primary color */
    }

    .rating i {
        color: #ffc107;
        font-size: 1rem;
    }

    .tracking-price-info {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        margin: 10px 0;
    }

    .tracking-lowest-price {
        color: #28a745; /* Success color */
        font-weight: 600;
    }

    .mrp {
        text-decoration: line-through;
        color: #dc3545; /* Danger color */
        font-weight: 500;
    }

    .savings {
        color: #ff5722; /* Warning color */
        font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .tracking-carousel-item {
            min-width: calc(50% - 20px);
        }

        .tracking-product-image {
            height: 180px;
        }

        .tracking-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 768px) {
        .tracking-carousel-item {
            min-width: calc(50% - 15px);
        }

        .tracking-product-info h3 {
            font-size: 1.1rem;
        }

        .tracking-product-image {
            height: 160px;
        }

        .ingredients-carousel {
            margin-top: 100px; 
        }

        .row.g-4 {
            flex-wrap: wrap;
        }

        .tracking-card {
            margin-bottom: 2rem;
        }

        .track-switch {
            flex-direction: column;
            gap: 1rem;
        }

        .form-control {
            padding-right: 115px;
        }

        .btn-track {
            padding: 0 1rem;
        }
    }

    @media (max-width: 576px) {
        .tracking-carousel-item {
            min-width: calc(50% - 10px);
        }

        .tracking-title {
            font-size: 1.6rem;
        }

        .tracking-subtitle {
            font-size: 1rem;
        }

        .form-control {
            height: 50px;
            font-size: 0.95rem;
        }

        .btn-track {
            font-size: 0.9rem;
        }

        .tracking-product-image {
            height: 140px;
        }

        .ingredient-item img {
            height: auto;
        }
    }

    /* Ingredients Carousel */
    .ingredients-carousel {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        margin-top: -250px;
    }

    .ingredient-item img {
        height: auto;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .ingredient-item:hover img {
        transform: scale(1.03);
    }

    /* Trending Products */
    .trending-products .product-card {
        background: #FFF;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #EEE;
    }

    .trending-products .product-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>
  

</head>

<body class="home-1">

    
    <!-- header start -->
    <?php include("components/header.php"); ?>



    <!--<section class="about-breadcrumb">-->
    <!--    <div class="about-back section-tb-padding">-->
    <!--        <div class="container">-->
    <!--            <div class="row">-->
    <!--                <div class="col">-->
    <!--                    <div class="about-l">-->
    <!--                        <h1 class="about-p"><span>Track Your Order</span></h1>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->

    <section class="section-tb-padding">
        <div class="container">
            <div class="row g-4">
                <!-- Track Order Section -->
                <div class="col-lg-8">
                    <div class="tracking-card">
                        <div class="tracking-header">
                            <h1 class="tracking-title">Track Your Package</h1>
                            <p class="tracking-subtitle">Enter your tracking details below</p>
                        </div>

                        <div class="tracking-body">
                            <div class="track-switch">
                                <div class="form-check-inline">
                                    <input type="radio" class="form-check-input" name="trackby" id="orderId" checked>
                                    <label class="form-check-label" for="orderId">Order ID</label>
                                </div>
                                <div class="form-check-inline">
                                    <input type="radio" class="form-check-input" name="trackby" id="trackingId">
                                    <label class="form-check-label" for="trackingId">Tracking ID</label>
                                </div>
                            </div>

                            <form class="tracking-form">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                        placeholder="Enter Tracking ID / AWB / Order ID">
                                    <button type="submit" class="btn btn-track">Track Your Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="tracking-product-carousel owl-carousel owl-theme">
                        <?php 
        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
        $ParamArray = array();
        $Fields = implode(",", $FieldNames);
        $product_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_master ORDER BY RAND()", $FieldNames, "", $ParamArray);

        foreach($product_data as $products){
            $FieldNamesPrice = array("OfferPrice", "MRP");
            $ParamArrayPrice = array($products["ProductId"]);
            $FieldsPrice = implode(",", $FieldNamesPrice);
            $product_prices = $obj->MysqliSelect1(
                "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                $FieldNamesPrice, 
                "i", 
                $ParamArrayPrice
            );
            
            // Price calculation logic
            $lowest_price = PHP_INT_MAX; 
            $mrp = PHP_INT_MAX; 
            $savings = 0;   
            
            if (!empty($product_prices)) {
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

                if ($lowest_price == PHP_INT_MAX) {
                    $lowest_price = "N/A";
                }
                if ($mrp == PHP_INT_MAX) {
                    $mrp = "N/A";
                }
                if ($mrp != "N/A" && $lowest_price != "N/A" && $mrp > $lowest_price) {
                    $savings = $mrp - $lowest_price;
                }
            }
        ?>
                        <div class="tracking-carousel-item">
                            <div class="tracking-product-card">
                                <a href="product_details.php?ProductId=<?php echo $products["ProductId"]; ?>">
                                    <img class="tracking-product-image"
                                        src="cms/images/products/<?php echo $products["PhotoPath"]; ?>"
                                        alt="<?php echo $products["ProductName"]; ?>">
                                </a>
                                <div class="tracking-product-info">
                                    <div class="rating">
                                        <i class="fa fa-star c-star"></i>
                                        <i class="fa fa-star c-star"></i>
                                        <i class="fa fa-star c-star"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div>
                                    <?php if ($savings > 0) { ?>
                                    <span class="savings">Off ₹<?php echo htmlspecialchars($savings); ?></span>
                                    <?php } ?>

                                    <div class="tracking-price-info">
                                        <span
                                            class="tracking-lowest-price">₹<?php echo htmlspecialchars($lowest_price); ?></span>
                                        <?php if ($mrp != "N/A") { ?>
                                        <span class="mrp">₹<?php echo htmlspecialchars($mrp); ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="tracking-add-to-cart">
                                        <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session"
                                            data-product-id="<?php echo $products["ProductId"]; ?>">
                                            <i class="fa fa-shopping-bag"></i> Add to cart
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>


                <!-- Ingredients Carousel -->
                <div class="col-lg-4 pt-0">
                    <!-- Added pt-0 class -->
                    <div class="ingredients-carousel owl-carousel owl-theme">
                        <div class="ingredient-item">
                            <img src="cms/images/products/89545.jpg" alt="Natural Ingredients" class="img-fluid rounded-3">
                        </div>
                        <div class="ingredient-item">
                            <img src="cms/images/products/89546.jpg" alt="Organic Products" class="img-fluid rounded-3">
                        </div>
                        <div class="ingredient-item">
                            <img src="cms/images/products/61728.jpg" alt="Fresh Components" class="img-fluid rounded-3">
                        </div>
                        <div class="ingredient-item">
                            <img src="cms/images/products/36578.jpg" alt="Quality Materials" class="img-fluid rounded-3">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <?php include("components/footer.php"); ?>

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
    <!-- custom -->
    <script src="js/custom.js"></script>
    <script>
    $(document).ready(function() {
        $('.tracking-product-carousel').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000, // Adjusted for better user experience
            responsive: {
                0: {
                    items: 1 // Show 1 product on extra small screens
                },
                576: {
                    items: 2 // Show 2 products on small screens
                },
                768: {
                    items: 2 // Show 3 products on medium screens
                },
                992: {
                    items: 3 // Show 4 products on large screens
                }
            }
        });
    });
</script>


    <script>
    $(document).ready(function() {
        // Initialize Bootstrap carousel with proper configuration
        $('#productCarousel').carousel({
            interval: 5000,
            wrap: true,
            pause: 'hover'
        });

        // Add touch swipe support
        $('#productCarousel').on('touchstart', function(event) {
            const xClick = event.originalEvent.touches[0].pageX;
            $(this).one('touchmove', function(event) {
                const xMove = event.originalEvent.touches[0].pageX;
                if (Math.floor(xClick - xMove) > 5) {
                    $(this).carousel('next');
                } else if (Math.floor(xClick - xMove) < -5) {
                    $(this).carousel('prev');
                }
            });
        });

        // Initialize Owl Carousel for ingredients
        $('.ingredients-carousel').owlCarousel({
            items: 1,
            loop: true,
            margin: 15,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 4000,
            smartSpeed: 800
        });

        // Responsive carousel item widths
        function updateCarouselItems() {
            const $carousel = $('.tracking-carousel-inner');
            const itemWidth = $('.tracking-carousel-item').outerWidth();
            const visibleItems = Math.floor($carousel.width() / itemWidth);
            $('.tracking-carousel-item').css('min-width', `calc(${100/visibleItems}% - 20px)`);
        }

        $(window).on('resize load', updateCarouselItems);
        updateCarouselItems();
    });
    </script>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
  
</body>

</html>
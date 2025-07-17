<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Organic and Healthy Products</title>
    <meta name="description"
        content="My Nutrify offers a wide range of organic, healthy, and nutritious products for your wellness and lifestyle." />
    <meta name="keywords"
        content="organic products, healthy food, nutrition, eCommerce, wellness, healthy living, organic supplements, eco-friendly" />
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
    @keyframes fadeOut {
        0% {
            opacity: 1;
            visibility: visible;
        }

        100% {
            opacity: 0;
            visibility: hidden;
        }
    }


    .loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 1);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        animation: fadeOut 1.5s ease-out 3s forwards;
        /* Fades out after 3 seconds */
    }

    .loader-img {
        width: 150px;
        height: 150px;
        animation: spin 2s linear infinite;
    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    .privacy-area {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        padding: 60px 0;
    }

    .terms-title-head h1 {
        color: #305724;
        font-size: 2.8rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        position: relative;
        animation: fadeInUp 1s ease;
    }

    .terms-title-head h1::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: #EA652D;
        border-radius: 2px;
    }

    .terms-policy-head {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        padding: 30px;
        transition: all 0.3s ease;
        animation: slideIn 0.8s ease forwards;
        opacity: 0;
    }

    .terms-policy-head:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .terms-policy-head h2 {
        color: #305724;
        font-size: 1.6rem;
        margin-bottom: 20px;
        position: relative;
        padding-left: 40px;
    }

    .terms-policy-head h2 span {
        color: #EA652D;
        font-size: 2.2rem;
        position: absolute;
        left: 0;
        top: -5px;
    }

    .terms-policy-desc {
        margin: 25px 0;
        padding-left: 30px;
        border-left: 3px solid #EA652D;
    }

    .terms-policy-desc h4 {
        color: #305724;
        font-size: 1.2rem;
        margin-bottom: 15px;
        position: relative;
    }

    .terms-policy-desc h4::before {
        content: '\f058';
        font-family: 'Font Awesome 5 Free';
        color: #EA652D;
        margin-right: 10px;
    }

    .terms-policy-desc p {
        color: #555;
        line-height: 1.8;
        margin-bottom: 15px;
        font-size: 1rem;
    }

    .terms-policy-head p span {
        color: #EA652D;
        font-weight: 600;
    }

    .terms-policy-head h6 {
        color: #305724;
        font-size: 1.1rem;
        margin: 20px 0 10px;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .terms-policy-head:nth-child(1) {
        animation-delay: 0.2s;
    }

    .terms-policy-head:nth-child(2) {
        animation-delay: 0.4s;
    }

    .terms-policy-head:nth-child(3) {
        animation-delay: 0.6s;
    }

    .terms-policy-head:nth-child(4) {
        animation-delay: 0.8s;
    }

    .terms-policy-head:nth-child(5) {
        animation-delay: 1s;
    }

    @media (max-width: 768px) {
        .terms-title-head h1 {
            font-size: 2rem;
        }

        .terms-policy-head {
            padding: 20px;
        }

        .terms-policy-desc {
            padding-left: 20px;
        }
    }

    /* Adding Font Awesome */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Contact Information Styling */
    .terms-policy-head:last-child p {
        padding-left: 30px;
        position: relative;
    }

    .terms-policy-head:last-child p span {
        color: #305724;
        font-weight: 600;
        display: inline-block;
        min-width: 100px;
    }

    .terms-policy-head:last-child p::before {
        content: '\f105';
        font-family: 'Font Awesome 5 Free';
        color: #EA652D;
        position: absolute;
        left: 0;
        top: 3px;
    }
    </style>
</head>
<!--</head>-->

<body class="home-1">
    <div class="loading">
        <div class="text-align">
            <img class="loader-img" src="image/preloader.gif" />
        </div>
    </div>
    <!-- header start -->
    <?php include("components/header.php") ?>
    <!-- header end -->


    <section class="privacy-area section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="terms-title-head">
                        <h1>Shipping Policy</h1>
                    </div>
                    <div class="terms-content">
                        <div class="terms-policy-head">
                            <h2>Delivery:- 
                            </h2>
                            <p>
                                Delivery Policy for My Nutrify Herbal & Ayurveda
                                At My Nutrify Herbal & Ayurveda, we prioritize timely and secure delivery of our natural
                                wellness products. Below are the details of our delivery process:
                            </p>
                        </div>
                        <div class="terms-policy">
                            <div class="terms-policy-head">
                                <h2><span>1.</span>Order Processing

                                </h2>
                                <p> <span>Processing Time:</span>Orders are processed within 1–2 business days
                                    (excluding weekends and holidays).
                                </p>
                                <p> <span>Verification:</span>We may contact you to confirm order details or address
                                    discrepancies to ensure accuracy.
                                </p>
                            </div>
                            <div class="terms-policy-head">
                                <h2><span>2.</span>Shipping Options & Costs
                                </h2>
                                <p>Domestic Shipping (India):
                                </p>
                                <p>Standard Shipping: 3–7 business days Available to select countries. Costs calculated
                                    at checkout based on weight/destination.
                                </p>
                                <p>Customers are responsible for customs duties, taxes, or import fees.
                                </p>
                            </div>
                            <div class="terms-policy-head">
                                <h2><span>3.</span>Tracking Your Order
                                </h2>
                                <p>3–7 business days after dispatch (may vary by location).
                                </p>

                            </div>
                            <div class="terms-policy-head">
                                <h2><span>4.</span>Tracking Your Order
                                </h2>
                                <p>
                                    A tracking number is emailed once your order ships. Use this to monitor delivery
                                    status via our partner carriers (e.g., India Post, DHL).
                                </p>
                                <p>
                                    Contact your local carrier first for delays. If unresolved, reach out to us.
                                </p>
                                </p>
                            </div>
                            <div class="terms-policy-head">
                                <h2><span>5.</span>Delivery Delays
                                </h2>
                                <p>Delays due to logistics, weather, or customs are beyond our control. We’ll assist in
                                    tracking your order if notified.
                                </p>
                            </div>
                            <div class="terms-policy-head">
                                <h2><span>6.</span>Failed Deliveries
                                </h2>
                                <p>Incorrect Addresses: Reshipping costs apply if packages return due to errors.
                                </p>
                                <p>Unclaimed Packages: Returned items may incur a 15% restocking fee.
                                </p>

                            </div>
                        </div>
                        <div class="terms-policy-head">
                            <h2><span>7.</span>Damaged or Lost Packages
                            </h2>
                            <p> Report damaged items within 48 hours with photos to support@mynutrify.com for replacements/refunds.
                            </p>
                            <p>Lost packages are investigated with carriers; replacements issued if confirmed lost.
                            </p>
                        </div>
                        <div class="terms-policy-head">
                            <h2><span>8.</span>Returns & Refunds
                            </h2>
                            <p>See our <span>Return Policy</span>for details. Unopened, unused items may be returned within 14 days for refunds.                          
                        </div>
                        <div class="terms-policy-head">
                            <h2><span>9.</span>Packaging
                            </h2>
                            <p>Products are securely packed in eco-friendly materials to preserve quality during transit.
                            </p>
                            

                        </div>
                        <div class="terms-policy-head">
                                <h2><span>5.</span>Contact Us:-
                                </h2>
                                <p>For return, refund, or cancellation-related queries, contact us:
                                </p>
                                <p><span>Customer Care:</span> [9834243754]
                                </p>
                                <p><span>Email:</span>[support@mynutrify.com]
                                </p>
                                <p><span>Address:</span>(55- North Shivaji Nagar, Near apta Police chowky, Sangli-
                                    416416)
                                </p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- login end -->
    <!-- footer start -->
    <?php include("components/footer.php") ?>
    <!-- footer end -->
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
    <!-- custom -->
    <script src="js/custom.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
    // Hide loading screen when the page is fully loaded
    $(window).on("load", function() {
        $(".loading").fadeOut(500, function() {
            $(".content").fadeIn(500);
        });
    });

    // Show loader on AJAX requests
    $(document).ajaxStart(function() {
        $(".loading").fadeIn();
    }).ajaxStop(function() {
        $(".loading").fadeOut();
    });
    </script>
    <script>
    (function(w, d, s, c, r, a, m) {
        w['KiwiObject'] = r;
        w[r] = w[r] || function() {
            (w[r].q = w[r].q || []).push(arguments)
        };
        w[r].l = 1 * new Date();
        a = d.createElement(s);
        m = d.getElementsByTagName(s)[0];
        a.async = 1;
        a.src = c;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', "https://app.interakt.ai/kiwi-sdk/kiwi-sdk-17-prod-min.js?v=" + new Date().getTime(),
        'kiwi');
    window.addEventListener("load", function() {
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
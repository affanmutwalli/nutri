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
    <!-- full width override -->
    <link rel="stylesheet" type="text/css" href="css/full-width-override.css">
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
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    padding: 30px;
    transition: all 0.3s ease;
    animation: slideIn 0.8s ease forwards;
    opacity: 0;
}

.terms-policy-head:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
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

.terms-policy-head:nth-child(1) { animation-delay: 0.2s; }
.terms-policy-head:nth-child(2) { animation-delay: 0.4s; }
.terms-policy-head:nth-child(3) { animation-delay: 0.6s; }
.terms-policy-head:nth-child(4) { animation-delay: 0.8s; }
.terms-policy-head:nth-child(5) { animation-delay: 1s; }

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
                        <h1>Return, Refund, & Cancellation Policy</h1>
                    </div>
                    <div class="terms-content">
                        <div class="terms-policy">
                            <div class="terms-policy-head">
                                <h2><span>1.</span>Return Policy:-
                                </h2>
                                <p> We at My Nutrify Herbal & Ayurveda strive to provide our customers with high-quality
                                    products. However, if you receive a defective, damaged, or incorrect item, we offer
                                    a return and replacement policy under the following conditions:
                                </p>
                                <div class="terms-policy-desc">
                                    <h4>Eligibility for Returns:- </h4>
                                    <p>Returns are accepted only if the product is damaged, defective, expired, or
                                        incorrect at the time of delivery. </p>
                                    <p>Request for return has to be made within 24 hours of order receipt.
                                    </p>
                                    <p>Products should be unused, sealed, and in original packing with intact labels and
                                        accessories.
                                    </p>
                                </div>
                                <div class="terms-policy-desc">
                                    <h4>Returns Process:-
                                    </h4>
                                    <p>Start a Return: Call our customer support at [support@mynutrify.com] within 3
                                        days of product receipt.
                                    </p>
                                    <p>Provide Evidence: Post pictures or videos of the faulty/wrong product and your
                                        order information.
                                    </p>
                                </div>
                                <div class="terms-policy-desc">
                                    <h4>Return Authorization:-
                                    </h4>
                                    <p>After the request is processed, we will schedule a return pickup or ask you to
                                        send it back to our warehouse.
                                    </p>
                                    <p>Return Shipping: For a defective or wrong product, we will bear the shipping
                                        expense. Otherwise, customers might need to pay the shipping fee.
                                    </p>
                                </div>
                                <div class="terms-policy-desc">
                                    <h4>Replacement/Refund:-
                                    </h4>
                                    <p> We will replace or refund the product within 7-10 business days upon receiving
                                        and examining the returned product.
                                    </p>
                                    <p><span>Note:</span>We do not accept return for used or opened products because of
                                        hygiene and safety concerns.
                                    </p>
                                </div>

                            </div>
                            <div class="terms-policy-head">
                                <h2><span>2.</span>Refund Policy:-
                                </h2>
                                <p>A refund is valid in case the product is damaged, defective, or out of stock and
                                    there is no replacement available.
                                </p>
                                <p>Refunds will only be processed after checking the returned product.
                                </p>
                                <div class="terms-policy-desc">
                                    <h4>Refund Processing Time:-
                                    </h4>
                                    <p>After approval, the refund shall be made in 7-10 business days and credited back
                                        to the source of payment (credit/debit card, UPI, or bank transfer).
                                    </p>
                                    <p>Refunds in case of Cash on Delivery (COD) orders shall be done through bank
                                        transfer. Bank details will be sought from the customer.
                                    </p>
                                    <p><span>Note:</span>Any shipping charge (if applied) is not refundable.
                                    </p>
                                </div>
                            </div>
                            <div class="terms-policy-head">
                                <h2><span>3.</span>Order Cancellation Policy:-
                                </h2>
                                <h6>Cancellation Prior to Dispatch
                                </h6>
                                <p>
                                    The orders are cancellable in 24 hours of ordering the same by sending an email to
                                    our customer care.
                                </p>
                                <p>In case the order has already been shipped, it is not possible for us to cancel the
                                    same.
                                </p>
                                <h6>Cancellation After Dispatch:<h6>
                                        <p>We can't cancel the order once we have dispatched it. You may, however,
                                            return the item as per our return policy.
                                        </p>
                                        <p>In case of refusal of delivery at the delivery point, the shipping and
                                            handling charges will be deducted before giving a refund
                                        </p>
                            </div>
                            <div class="terms-policy-head">
                                <h2><span>4.</span>Non-Returnable & Non-Refundable Products:-
                                </h2>
                                <p>Used or opened items.</p>
                                <p>
                                    Discounted, sale, or special promotion items except in case of damage or defect.
                                </p>
                                <p>Customized or personalized items.
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
<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// $FieldNames = array("CustomerId", "Name", "MobileNo", "IsActive");
// $ParamArray = [$_SESSION["CustomerId"]];
// $Fields = implode(",", $FieldNames);

// // Assuming MysqliSelect1 function handles the query correctly
// $customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Organic and Healthy Products</title>
    <meta name="description" content="My Nutrify offers a wide range of organic, healthy, and nutritious products for your wellness and lifestyle."/>
    <meta name="keywords" content="organic products, healthy food, nutrition, eCommerce, wellness, healthy living, organic supplements, eco-friendly"/>
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
   
</head>
<!--</head>-->
<body class="home-1">

    <!-- header start -->
        <?php include("components/header.php") ?>
        <!-- header end -->

       <section class="contact section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="map-area">
                            <div class="map-title">
                                <h1>Contact us</h1>
                            </div>
                            <div class="map">
<iframe
                            src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d121015.92937807197!2d73.75064686738706!3d18.585405799454485!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x3bc2b9523b6d1891%3A0x147dd229780d8ca3!2sOld%20Mumbai%20-%20Pune%20Hwy%2C%20above%20tvs%20showroom%2C%20Sanjay%20Nagar%2C%20Phugewadi%2C%20Dapodi%2C%20Pimpri-Chinchwad%2C%20Maharashtra%20411012!3m2!1d18.5854237!2d73.8330486!5e0!3m2!1sen!2sin!4v1740136634459!5m2!1sen!2sin"
                            width="600" height="450" style="border:0;" allowfullscreen="" aria-hidden="false"
                                tabindex="0"></iframe>                            </div>
                            <div class="map-details section-t-padding">
                                <div class="contact-info">
                                    <div class="contact-details">
                                        <h4>Drop us message</h4>
                                        <form>
                                            <label>Your name</label>
                                            <input type="text" name="name" placeholder="Enter your name">
                                            <label>Email address</label>
                                            <input type="text" name="Email" placeholder="Enter your email address">
                                            <label>Message</label>
                                            <textarea rows="5" placeholder="Your message hare..."></textarea>
                                        </form>
                                        <a href="index1.html" class="btn-style1">Submit <i class="ti-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="contact-info">
                                    <div class="information">
                                        <h4>Get in touch</h4>
                                        <!--<p class="info-description">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rerum earum eveniet dolorum suscipit nesciunt incidunt animi repudiandae ab at, tenetur distinctio voluptate vel illo similique.</p>-->
                                        <div class="contact-in">
                                            <ul class="info-details">
                                                <li><i class="fa fa-street-view"></i></li>
                                                <li>
                                                    <h4>Address</h4>
                                                    <!--<p>55 North Shivaji Nagar,-->
                                                    <!--    Near Apta Police Chowk,-->
                                                    <!--    Sangli, Maharashtra - 416416.</p>-->
                                                     <p>S.NO.31/32, 1st Floor, Old Mumbai Pune Road, Dapoli (Maharashtra) Pune - 411012</p>

                                                </li>
                                            </ul>
                                            <ul class="info-details">
                                                <li><i class="fa fa-phone"></i></li>
                                                <li>
                                                    <h4>Phone</h4>
                                                    <a href="tel:12345678999">+91-9834243754</a>
                                                </li>
                                            </ul>
                                            <ul class="info-details">
                                                <li><i class="fa fa-envelope"></i></li>
                                                <li>
                                                    <h4>Email</h4>
                                                    <a href="mailto:support@mynutrify.com">support@mynutrify.com</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- brand logo end -->
    
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
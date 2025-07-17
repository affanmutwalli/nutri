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

        
 <section class="privacy-area section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="terms-title">
                            <h1>Payment  Policy</h1>
                        </div>
                        <div class="terms-content">
                            <ul class="terms-policy">
                                <li>
                                <h2><span>1.</span> Our Payment Policy</h2>
                                <p class="terms-desc">
                                    <strong>Pricing :</strong><ul>
                                        <li>All products listed on the Website are sold at their maximum retail price unless otherwise specified.</li>
                                        <li>Prices on the Website are inclusive of taxes.</li>
                                        <li>The price at the time of ordering will be the price charged on the date of delivery. Any price difference between ordering and delivery will not result in additional charges or refunds.</li>
                                    </ul>
                                </p>
                                <p class="terms-desc">
                                    <strong>Order Checkout :</strong><ul>
                                        <li>Before confirming your order, verify the total amount payable.</li>
                                        <li>The total charges for the order will be displayed at checkout. No further charges will apply at the time of delivery.</li>
                                        <li>If you select the cash-on-delivery option, the full payment must be made to the delivery person before receiving the ordered products.</li>
                                    </ul>
                                </p>
                                <p class="terms-desc">
                                    <strong>Payment Options :</strong><ul>
                                        <li>During checkout, you can choose from the various available payment options.</li>
                                        <li>You are responsible for using your own debit/credit card or banking details to complete the transaction.</li>
                                    </ul>
                                </p>
                                <p class="terms-desc">
                                    <strong>Security and Fraud Prevention :</strong><ul>
                                        <li>Once payment is made, My Nutrify ensures all security checks are verified through a secured server.</li>
                                        <li>My Nutrify is not liable for any misuse of your payment details, and the responsibility for proving fraudulent use of your payment method lies with you.</li>
                                        <li>My Nutrify will not be held responsible for any credit card fraud.</li>
                                    </ul>
                                </p>
                            </li>
                            </ul>
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
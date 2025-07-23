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

    </style>
</head>
<!--</head>-->
<body class="home-1">

    <!-- header start -->
        <?php include("components/header.php") ?>
        <!-- header end -->

        <section class="terms-area section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="terms-title">
                            <h1>Terms & Conditions</h1>
                        </div>
                        <div class="terms-content">
                            <ul class="terms-policy">
                                <li>
                                <h2><span>1.</span> Our Terms & Conditions</h2>
                                <p class="terms-desc">
                                    <strong>Personal Information:</strong> We collect your name, email, phone, demographics, and browsing data. Keep your account details updated for communications. We do not disclose your personal data without consent.
                                </p>
                                <p class="terms-desc">
                                    <strong>Payment Information:</strong> My Nutrify does not store your credit/debit card details or CVV. Payments are processed securely.
                                </p>
                                <p class="terms-desc">
                                    <strong>Order Acceptance:</strong> Orders placed are offers to purchase. My Nutrify may accept or reject them for reasons like product unavailability or pricing errors. If rejected, you will receive a full refund.
                                </p>
                                <p class="terms-desc">
                                    <strong>Product Availability:</strong> Prices and availability may change without notice. My Nutrify is not liable for damages caused by order cancellation due to product unavailability.
                                </p>
                                <p class="terms-desc">
                                    <strong>License and Use:</strong> You are granted a personal, non-commercial license to use the Website. Resale or commercial use is prohibited.
                                </p>
                                <p class="terms-desc">
                                    <strong>Account Responsibility:</strong> You are responsible for maintaining the confidentiality of your account and password. Notify us if you suspect misuse.
                                </p>
                                <p class="terms-desc">
                                    <strong>Product Responsibility:</strong> You are responsible for checking the product descriptions to avoid side effects. Consult a specialist if necessary.
                                </p>
                                <p class="terms-desc">
                                    <strong>Content Restrictions:</strong> Do not upload unlawful, harmful, or defamatory content. You are responsible for all information you transmit via the Website.
                                </p>
                                <p class="terms-desc">
                                    <strong>True Information:</strong> You must provide accurate details. My Nutrify reserves the right to verify and reject any false information.
                                </p>
                                <p class="terms-desc">
                                    <strong>Complaint Timeframe:</strong> Complaints must be raised within 7 days of delivery.
                                </p>
                                <p class="terms-desc">
                                    <strong>Warranty and Liability:</strong> My Nutrify disclaims liability for product defects or misuse. We are not responsible for consequential or incidental damages.
                                </p>
                                <p class="terms-desc">
                                    <strong>Security:</strong> Do not attempt to interfere with the Website’s operations. Violating security may result in termination of your account.
                                </p>
                                <p class="terms-desc">
                                    <strong>Dispute Resolution:</strong> Disputes must be resolved within one year of occurrence. Terminating your use of the Website is your sole remedy.
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
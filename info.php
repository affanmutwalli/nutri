<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// Get the 'name' parameter from the URL, default to 'default'
$page = isset($_GET['name']) ? $_GET['name'] : 'default';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Nutrify - <?php echo ucfirst($page); ?> Policy</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<style>
    
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
    animation: fadeOut 1.5s ease-out 3s forwards; /* Fades out after 3 seconds */
}

.loader-img {
    width: 150px;
    height: 150px;
    animation: spin 2s linear infinite;
}
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
<body class="info-page">
<div class="loading">
        <div class="text-align">
            <img class="loader-img" src="image/preloader.gif"/>
        </div>
    </div>
    <!-- Header -->
    <?php include("components/header.php"); ?>

    <!-- Main Content -->
    <main class="container mt-5">
        <?php
switch ($page) {
    case 'terms': ?>
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
        <?php break;

    case 'privacy': ?>
        <section class="privacy-area section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="terms-title">
                            <h1>Privacy Policy</h1>
                        </div>
                        <div class="terms-content">
                            <ul class="terms-policy">
                                <li>
                                <h2><span>1.</span> Our Privacy Policy</h2>
                                <p class="terms-desc">
                                    <strong>Commitment to Privacy :</strong> My Nutrify prioritizes safeguarding personal information and has implemented a Privacy Policy to ensure sufficient protection.
                                </p>
                                <p class="terms-desc">
                                    <strong>Acceptance of Policies :</strong> By using the website, users agree to the Privacy Policy and related policies and are bound by their terms and conditions. 
                                </p>
                                <p class="terms-desc">
                                    <strong>Consent to Use Personal Information :</strong> Users consent to My Nutrify’s use of personal information as outlined in the Terms of Use and Policies, which may be updated at My Nutrify's discretion.
                                </p>
                                <p class="terms-desc">
                                    <strong>Privacy Policy Access :</strong> Users can access the detailed Privacy Policy on the website at https://MyNutrify.com/privacy-policy.
                                </p>
                            </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php break;

 case 'return': ?>
        <section class="privacy-area section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="terms-title">
                            <h1>Return Policy</h1>
                        </div>
                        <div class="terms-content">
                            <ul class="terms-policy">
                                <li>
                                <h2><span>1.</span> Our Return Policy</h2>
                                <p class="terms-desc">
                                    <strong>Cancellation Before Dispatch :</strong> Orders can be canceled before they are dispatched.
                                </p>
                                <p class="terms-desc">
                                    <strong>Cancellation After Dispatch :</strong> Once the order has been dispatched, it cannot be canceled.
                                </p>
                                <p class="terms-desc">
                                    <strong>Returns and Refunds :</strong> If you are unable to cancel the order before dispatch or if the order has already been delivered, you may contact customer service for assistance regarding returns or refunds.
                                </p>
                                <p class="terms-desc">
                                    <strong>Customer Support :</strong> Reach out to the customer care team for guidance on cancellations, refunds, or returns.
                                </p>
                                <p class="terms-desc">
                                    <strong>Conditions for Returns:</strong> To be eligible for a refund or replacement, the product must be returned in its original condition, including all packaging. Refunds will be processed within 5–7 business days after the return is received and verified.
                                </p>

                            </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php break;
        
            case 'shipping': ?>
        <section class="privacy-area section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="terms-title">
                            <h1>Shipping Policy</h1>
                        </div>
                        <div class="terms-content">
                            <ul class="terms-policy">
                                <li>
                                <h2><span>1.</span> Our Shipping Policy</h2>
                                <p class="terms-desc">
                                    <strong>Logistics Management :</strong> My Nutrify handles the logistics for delivering all products ordered on the Website.
                                </p>
                                <p class="terms-desc">
                                    <strong>Estimated Delivery Time:</strong> At the time of placing your order, an estimated delivery timeline of 3 to 7 days will be provided, depending on your location.
                                </p>
                                <p class="terms-desc">
                                    <strong>Tracking Your Order :</strong> Once your order is shipped, a consignment number will be shared with you to track the status of your shipment.
                                </p>
                                <p class="terms-desc">
                                    <strong>Timely Delivery :</strong> My Nutrify strives to ensure that your order is delivered promptly. However, delivery times may be affected by external circumstances beyond our control.
                                </p>
                                <p class="terms-desc">
                                    <strong>Policy Updates :</strong> For further details, please refer to our Shipping and Delivery Policy available on the Website, which may be updated from time to time.
                                </p>
                            </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php break;

            
            case 'payment': ?>
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
        <?php break;
          
            default:
                echo '<h1>Welcome to My Nutrify!</h1>';
                echo '<p>Select a policy from the links below to view more information.</p>';
        }
        ?>
    </main>

    <!-- Footer -->
    <?php include("components/footer.php"); ?>
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

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
    <title>My Nutrify Rewards - Earn and Redeem Coins</title>
    <meta name="description" content="Discover My Nutrify Coins, our exclusive rewards program. Earn coins on every purchase and redeem them for discounts on your favorite healthy and nutritious products." />
    <meta name="keywords" content="My Nutrify Coins, rewards program, earn coins, redeem discounts, loyalty program, healthy lifestyle, wellness rewards, nutritious products" />
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

/* Preloader Styles */
/* Customer.css */


.rewards-container {
    max-width: 1500px;
    margin: 0 auto;
    padding: 2rem;
}

.rewards-header {
    display: flex;
    align-items: center;
    gap: 4rem;
    margin-bottom: 6rem;
    animation: fadeInUp 1s ease;
}

.rewards-header-image-container {
    flex: 1;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.rewards-header-image {
    width: 100%;
    height: auto;
    display: block;
}

.rewards-header-image-container:hover {
    transform: translateY(-5px);
}

.rewards-hero-content {
    flex: 1;
    margin-top: 25px;
}

.rewards-hero-content h1 {
    font-size: 3.5rem;
    margin-bottom: 1.5rem;
    color: #2d3436;
    line-height: 1.2;
    animation: fadeInUp 1s ease 0.2s backwards;
}

.rewards-hero-content p {
    font-size: 1.2rem;
    color: #636e72;
    margin-bottom: 2rem;
    animation: fadeInUp 1s ease 0.4s backwards;
}

.rewards-button-group {
    display: flex;
    gap: 1rem;
    animation: fadeInUp 1s ease 0.6s backwards;
}

.rewards-primary-btn {
    background: linear-gradient(135deg, #305724, #305724);
    color: white;
    padding: 1rem 2rem;
    border-radius: 50px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rewards-primary-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(48, 87, 36, 0.3);
}

.rewards-secondary-btn {
    background: transparent;
    color: #305724;
    border: 2px solid #305724;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rewards-secondary-btn:hover {
    background: #305724;
    color: white;
}

.rewards-how-it-works {
    margin: 4rem 0;
}

.rewards-section-title {
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 1rem;
    color: #305724;
}

.rewards-section-description {
    text-align: center;
    color: #636e72;
    margin-bottom: 3rem;
}

.rewards-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin: 0 45px;
}

.rewards-step {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
}

.rewards-step:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.rewards-step-number {
    width: 60px;
    height: 60px;
    background: #2d5c2b;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.5rem;
    font-weight: bold;
}

.rewards-step-title {
    font-size: 1.5rem;
    margin: 1rem 0;
    color: #305724;
}

.rewards-step-description {
    color: #636e72;
    font-size: 1rem;
}

.rewards-ways-to-earn {
    margin: 4rem 0;
}

.rewards-earning-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin: 0 45px;
}

.rewards-earning-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.rewards-earning-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.rewards-icon {
    font-size: 2rem;
    color: #636e72;
}

.rewards-earning-title {
    font-size: 1.5rem;
    margin: 1rem 0;
}

.rewards-earning-description {
    color: #636e72;
    font-size: 1rem;
}

.rewards-redeem-section {
    margin: 4rem 0;
    text-align: center;
}

.rewards-redeem-box {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #ddd;
    padding: 15px;
    margin: 20px auto;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.rewards-content h3 {
    font-size: 18px;
}

.rewards-content p {
    font-size: 14px;
    color: #666;
}

.rewards-arrow {
    margin-left: auto;
    font-size: 18px;
    color: #888;
}

.rewards-referral-section {
    margin: 4rem 0;
    text-align: center;
    padding: 40px 20px;
    background: #f9f9f9;
}

.rewards-referral-boxes {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.rewards-box {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #ddd;
    width: 180px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.rewards-floating-buttons {
    position: fixed;
    bottom: 20px;
    left: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 1000;
}

.rewards-rewards-btn,
.rewards-download-btn {
    background: #2d5c2b;
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 30px;
    font-size: 14px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.rewards-download-btn {
    background: black;
}

.rewards-rewards-btn:hover,
.rewards-download-btn:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .rewards-header {
        flex-direction: column;
        gap: 2rem;
    }

    .rewards-hero-content h1 {
        font-size: 2.5rem;
    }

    .rewards-button-group {
        flex-direction: column;
        width: 100%;
    }

    .rewards-steps,
    .rewards-earning-cards {
        margin: 0 15px;
    }

    .rewards-referral-boxes {
        flex-direction: column;
        align-items: center;
    }

    .rewards-box {
        width: 80%;
    }
}

@media (max-width: 480px) {
    .rewards-container {
        padding: 1rem;
    }

    .rewards-hero-content h1 {
        font-size: 2rem;
    }

    .rewards-section-title {
        font-size: 2rem;
    }
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


/*#preloader {*/
/*    position: fixed;*/
/*    top: 0;*/
/*    left: 0;*/
/*    width: 100vw;*/
/*    height: 100vh;*/
/*    background: #fff;*/
/*    display: flex;*/
/*    align-items: center;*/
/*    justify-content: center;*/
/*    z-index: 9999; */
/*}*/

/* Preloader Animation */
/*.pl {*/
/*    display: block;*/
/*    width: 6.25em;*/
/*    height: 6.25em;*/
/*}*/

/*.pl__ring,*/
/*.pl__ball {*/
/*    animation: ring 2s ease-out infinite;*/
/*}*/

/*.pl__ball {*/
/*    animation-name: ball;*/
/*}*/

/*@keyframes ring {*/
/*    from {*/
/*        stroke-dasharray: 0 257 0 0 1 0 0 258;*/
/*    }*/
/*    25% {*/
/*        stroke-dasharray: 0 0 0 0 257 0 258 0;*/
/*    }*/
/*    50%,*/
/*    to {*/
/*        stroke-dasharray: 0 0 0 0 0 515 0 0;*/
/*    }*/
/*}*/

/*@keyframes ball {*/
/*    from,*/
/*    50% {*/
/*        animation-timing-function: ease-in;*/
/*        stroke-dashoffset: 1;*/
/*    }*/
/*    64% {*/
/*        animation-timing-function: ease-in;*/
/*        stroke-dashoffset: -109;*/
/*    }*/
/*    78% {*/
/*        animation-timing-function: ease-in;*/
/*        stroke-dashoffset: -145;*/
/*    }*/
/*    92% {*/
/*        animation-timing-function: ease-in;*/
/*        stroke-dashoffset: -157;*/
/*    }*/
/*    57%,*/
/*    71%,*/
/*    85%,*/
/*    99%,*/
/*    to {*/
/*        animation-timing-function: ease-out;*/
/*        stroke-dashoffset: -163;*/
/*    }*/
/*}*/

/*.about-section {*/
/*    padding: 40px 20px;*/
/*    background-color: #f9f9f9;*/
/*    font-family: Arial, sans-serif;*/
/*    color: #333;*/
/*}*/

/*.container {*/
/*    max-width: 1200px;*/
/*    margin: 0 auto;*/
/*}*/

/*.row {*/
/*    display: flex;*/
/*    flex-wrap: wrap;*/
/*    margin-bottom: 20px;*/
/*}*/

/*.col {*/
/*    flex: 1;*/
/*    padding: 15px;*/
/*}*/

/*.about-title h1 {*/
/*    font-size: 2rem;*/
/*    font-weight: bold;*/
/*    margin-bottom: 20px;*/
/*}*/

/*.about-image {*/
/*    max-width: 100%;*/
/*    height: auto;*/
/*    border-radius: 8px;*/
/*    margin-top: 20px;*/
/*}*/

/*.about-details p, */
/*.about-mission p, */
/*.about-vision p, */
/*.about-core-values p {*/
/*    font-size: 1rem;*/
/*    line-height: 1.6;*/
/*    margin-bottom: 15px;*/
/*}*/

/*.about-core-values ul {*/
/*    list-style-type: disc;*/
/*    padding-left: 20px;*/
/*}*/

/*.about-core-values li {*/
/*    margin-bottom: 10px;*/
/*    font-size: 1rem;*/
/*}*/

    </style>
    
</head>
<body class="home-1">

    <!-- header start -->
        <?php include("components/header.php") ?>
        <!-- header end -->
        
    <div class='rewards-container'>
      <div class='rewards-header'>
        <div class='rewards-header-image-container'>
 <img src=image/reward.jpg alt="Header" class='rewards-header-image' />
        </div>
        <div class='rewards-hero-content'>
          <h1>Join And Earn Rewards</h1>
          <p>Win My Nutrify Points for every spend and redeem them for exclusive rewards.</p>
          <div class="rewards-button-group">
            <a href="login.php" class="rewards-primary-btn">Login</a>
          </div>
        </div>
      </div>

      <div class='rewards-how-it-works'>
        <h2 class='rewards-section-title'>How it Works?</h2>
        <p class='rewards-section-description'>Earn and redeem points through simple steps</p>
        <div class='rewards-steps'>
          <div class='rewards-step'>
            <div class='rewards-step-number'>1</div>
            <h3 class='rewards-step-title'>Sign Up</h3>
            <p class='rewards-step-description'>Create an account and start earning immediately.</p>
          </div>
          <div class='rewards-step'>
            <div class='rewards-step-number'>2</div>
            <h3 class='rewards-step-title'>Earn Points</h3>
            <p class='rewards-step-description'>Collect points with every purchase.</p>
          </div>
          <div class='rewards-step'>
            <div class='rewards-step-number'>3</div>
            <h3 class='rewards-step-title'>Redeem</h3>
            <p class='rewards-step-description'>Exchange points for exciting rewards.</p>
          </div>
        </div>
      </div>

      <div class='rewards-ways-to-earn'>
        <h2 class='rewards-section-title'>Ways To Earn</h2>
        <p class='rewards-section-description'>Discover multiple avenues to accumulate points</p>
        <div class='rewards-earning-cards'>
          <div class='rewards-earning-card'>
            <span class='rewards-icon'>✏️</span>
            <h3 class='rewards-earning-title'>Write A Review</h3>
            <p class='rewards-earning-description'>Get 25 My Nutrify Points. Get 50 points for Image Reviews.</p>
          </div>
          <div class='rewards-earning-card'>
            <span class='rewards-icon'>⚡</span>
            <h3 class='rewards-earning-title'>Order Placed</h3>
            <p class='rewards-earning-description'>Get 3% off the order value. Rewards will be credited 3 days after delivery.</p>
          </div>
          <div class='rewards-earning-card'>
            <span class='rewards-icon'>👤</span>
            <h3 class='rewards-earning-title'>Signup Reward</h3>
            <p class='rewards-earning-description'>Get 25 My Nutrify Points upon signup.</p>
          </div>
        </div>
      </div>

      <div class='rewards-redeem-section'>
        <h2 class='rewards-section-title'>Ways To Redeem</h2>
        <p class='rewards-section-description'>Redeem up to 5% of the cart value, a maximum of 100 My Nutrify points</p>
        <div class='rewards-redeem-box'>
          <div class='rewards-icon'>🎁</div>
          <div class='rewards-content'>
            <h3>Every 1 My Nutrify Point = ₹1 Off</h3>
            <p>Minimum cart value depends on the discount amount, max ₹100 discount</p>
            <small>Starts at 1 My Nutrify Point</small>
          </div>
        </div>
      </div>

      <div class='rewards-referral-section'>
        <h2 class='rewards-section-title'>Referral Program</h2>
        <p class='rewards-section-description'>Give your friends a reward and claim your own when they make a purchase</p>
        <div class='rewards-referral-boxes'>
          <div class='rewards-box'>
            <p>They get</p>
            <strong>₹50 Off Coupon</strong>
          </div>
          <div class='rewards-box'>
            <p>You get</p>
            <strong>100 My Nutrify Points</strong>
          </div>
        </div>
      </div>
    </div>
       <!--<section class="about-content section-tb-padding">-->
       <!--     <div class="container">-->
       <!--         <div class="row">-->
       <!--             <div class="about-section">-->
       <!--                 <div class="container">-->
       <!--                     <div class="row">-->
       <!--                         <div class="col about-title">-->
       <!--                             <h1>Join And Earn Rewards</h1>-->
       <!--                             <img src="image/main_logo.png" class="about-image" alt="pro-image">-->
       <!--                         </div>-->
       <!--                     </div>-->
       <!--                     <div class="row">-->
       <!--                         <div class="col about-details">-->
       <!--                             <h6>-->
       <!--                                 Win My Nutrify Points for every spend and redeem them exclusive Rewards-->
       <!--                             </h6>-->
       <!--                             <p>-->
       <!--                                 From our humble beginnings, we have consistently worked to bring scientifically backed and -->
       <!--                                 nutritionally rich products to market. By combining cutting-edge research with the finest ingredients, -->
       <!--                                 we have made health accessible to everyone. Today, Mynutrify stands as a testament to dedication, -->
       <!--                                 perseverance, and a commitment to better living.-->
       <!--                             </p>-->
       <!--                         </div>-->
       <!--                     </div>-->
       <!--                     <div class="row">-->
       <!--                         <div class="col about-mission">-->
       <!--                             <h1>Our Mission</h1>-->
       <!--                             <p>-->
       <!--                                 Empower individuals to achieve their health and fitness aspirations.-->
       <!--                                 Deliver top-quality supplements that promote physical performance, effective weight management, and holistic well-being.-->
       <!--                                 Inspire a lifestyle that embraces health as a cornerstone for happiness and success.-->
       <!--                             </p>-->
       <!--                         </div>-->
       <!--                     </div>-->
       <!--                     <div class="row">-->
       <!--                         <div class="col about-vision">-->
       <!--                             <h1>Our Vision</h1>-->
       <!--                             <p>-->
       <!--                                 We envision a healthier world where every individual has access to reliable, effective, and safe -->
       <!--                                 nutritional support. As pioneers in the supplementary food industry, we aim to set benchmarks in -->
       <!--                                 quality, innovation, and customer satisfaction. By staying true to our core values and continually -->
       <!--                                 evolving our offerings, we aspire to become the global leader in health and wellness products.-->
       <!--                             </p>-->
       <!--                         </div>-->
       <!--                     </div>-->
       <!--                     <div class="row">-->
       <!--                         <div class="col about-core-values">-->
       <!--                             <h1>Our Core Values</h1>-->
       <!--                             <ul>-->
       <!--                                 <li><strong>Quality First:</strong> Every product is crafted with utmost precision, ensuring safety, effectiveness, and excellence.</li>-->
       <!--                                 <li><strong>Customer-Centric:</strong> We place our customers at the heart of everything we do, prioritizing their needs and satisfaction.</li>-->
       <!--                                 <li><strong>Innovation:</strong> Staying ahead through research and development to meet the ever-changing health requirements.</li>-->
       <!--                                 <li><strong>Sustainability:</strong> Committed to eco-friendly practices that ensure a healthier planet for future generations.</li>-->
       <!--                             </ul>-->
       <!--                         </div>-->
       <!--                     </div>-->
       <!--                 </div>-->
       <!--             </div>-->

       <!--         </div>-->
       <!--     </div>-->
       <!-- </section>-->
        <!-- about content end -->
        <!-- about counter start -->
        <!--<section>-->
        <!--    <div class="about-counter section-tb-padding">-->
        <!--        <div class="container">-->
        <!--            <div class="row">-->
        <!--                <div class="col">-->
        <!--                    <div class="text-center">-->
        <!--                        <div class="counter">-->
        <!--                            <h2 class="timer count-title count-number" data-to="21" data-speed="1500">12</h2>-->
        <!--                            <p class="count-text ">Years in business</p>-->
        <!--                        </div>-->
        <!--                        <div class="counter">-->
        <!--                            <h2 class="timer count-title count-number" data-to="210" data-speed="1500"></h2>-->
        <!--                            <p class="count-text ">Clients and partners</p>-->
        <!--                        </div>-->
        <!--                        <div class="counter">-->
        <!--                            <h2 class="timer count-title count-number" data-to="18" data-speed="1500"></h2>-->
        <!--                            <p class="count-text ">Show room</p>-->
        <!--                        </div>-->
        <!--                        <div class="counter">-->
        <!--                            <h2 class="timer count-title count-number" data-to="17" data-speed="1500"></h2>-->
        <!--                            <p class="count-text ">Billon sales</p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->
        <!-- about counter end -->
    
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
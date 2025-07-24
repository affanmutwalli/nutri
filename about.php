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

    <!-- Tawk.to Integration -->
    <?php include("components/tawk-to.php"); ?>

<style>

:root {
    --primary-accent: #EA652D;
    --secondary-accent: #305724;
    --tertiary-accent: #F5E6D3;
    --text-dark: #2A2A2A;
    --text-light: #5C5C5C;
    --section-padding: 8rem 0;
    --organic-shape: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
    --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}





/* Modern Hero Section */
.about-hero-banner {
    position: relative;
    padding: var(--section-padding);
    margin-bottom: 8rem;
    overflow: hidden;
}

.about-hero-image-container {
    position: relative;
    border-radius: 2rem;
    overflow: hidden;
    transform: translateZ(0);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
}


.about-hero-text {
    position: absolute;
    bottom: -4rem;
    right: 5%;
    background: rgba(255, 255, 255, 0.95);
    padding: 3rem;
    border-radius: 1.5rem;
    max-width: 600px;
    backdrop-filter: blur(10px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    transform: rotateZ(-2deg);
    transition: var(--transition);
}

.about-hero-text h1 {
    font-size: 3rem;
    color: var(--secondary-accent);
    margin-bottom: 1.5rem;
    background: linear-gradient(45deg, var(--secondary-accent), var(--primary-accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Modern Content Sections */
.about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    padding: var(--section-padding);
    max-width: 1400px;
    margin: 0 auto;
}

.about-text-content {
    position: relative;
    /* padding: 2rem; */
}

.about-hero-Head h1 {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    position: relative;
    display: inline-block;
}

.about-hero-Head h1 span {
    display: block;
    font-size: 2.5rem;
    color: var(--primary-accent);
    margin-bottom: 0.5rem;
    letter-spacing: 3px;
}

.about-text-content p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-light);
    margin-bottom: 2rem;
    position: relative;
    padding-left: 2rem;
}

.about-text-content p::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.7em;
    width: 10px;
    height: 10px;
    background: var(--primary-accent);
    border-radius: 50%;
}

.about-content-image {
    border-radius: 2rem;
    width: 100%;
    height: 500px;
    object-fit: cover;
    box-shadow: 20px 20px 0 var(--tertiary-accent);
    transition: var(--transition);
}

/* Mission/Vision Grid */
.about-mission-vision-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 4rem;
    padding: var(--section-padding);
    max-width: 1400px;
    margin: 0 auto;
}

.about-mission-bullet {
    background: white;
    padding: 3rem;
    border-radius: 2rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
    transition: var(--transition);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.about-mission-bullet:hover {
    transform: translateY(-1rem);
    box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.1);
}

/* Core Values */
.about-core-values {
    padding: var(--section-padding);
    background: linear-gradient(45deg, var(--tertiary-accent), #fff);
}

.about-core-values ul {
    max-width: 800px;
    margin: 0 auto;
}

.about-core-values li {
    padding: 2rem;
    margin-bottom: 1.5rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.about-core-values li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: var(--primary-accent);
    transition: var(--transition);
}

.about-core-values li:hover {
    transform: translateX(1rem);
}

/* Animations */
[data-aos] {
    transition: var(--transition);
}

.about-content-image {
    max-width: 80%;
    margin: 0 auto;
  }

.about-content-image.aos-animate {
    transform: perspective(1000px) rotateY(0);
}

.about-mission-vision-grid div::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
  background: var(--primary-accent);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.about-mission-vision-grid div:hover::before {
  transform: scaleX(1);
}
/* Responsive Design */
@media (max-width: 1200px) {
    .about-hero-text {
        position: relative;
        bottom: auto;
        right: auto;
        transform: none;
        margin: -4rem 2rem 0;
        max-width: none;
    }
}

@media (max-width: 768px) {
    .about-content {
        grid-template-columns: 1fr;
        padding: 4rem 2rem;
    }

    .about-hero-text h1 {
        font-size: 2rem;
    }

    .about-content-image {
        width: 90%;
        margin: 0 auto;
    }
}

@media (max-width: 480px) {
    .about-hero-text {
        padding: 2rem;
    }

    .about-mission-bullet {
        padding: 2rem;
    }
}


/* Initialize AOS */

  
    </style>
</head>
</head>

<body class="home-1">
    
    <!-- header start -->
    <?php include("components/header.php") ?>
    <!-- header end -->
    <div class="about-container">
        

        <section class="about-content">
            <div class="about-text-content">
                <div class="about-hero-Head">
                    <h1><span>ABOUT US</span>My Nutrify Herbal & Ayurveda
                    </h1>
                </div>
                <p>You are welcomed to<strong> My Nutrify Herbal & Ayurveda</strong> a haven where timeless wisdom combines
                    with contemporary well-being. We strive to bring natural, original, and comprehensive answers that
                    feed your body, nourish your mind, and awaken your soul. Our own began with the vision of
                    discovering centuries-old Ayurvedic knowledge and merging it with cutting-edge herbal research in
                    order to craft items that truly do make an impact.
                </p>
            </div>
            <div class="about-image-container">
                <img src="cms/images/About1.jpg" alt="Mynutrify Mobile App" class="about-content-image">
            </div>
        </section>
        <section class="about-content">
            <div class="about-image-container">
                <img src="cms/images/About2.jpg" alt="Mynutrify Mobile App" class="about-content-image">
            </div>
            <div class="about-text-content">
                <div class="about-hero-Head">
                    <h1>Our<span>Story
                        </span>
                    </h1>
                </div>
                <p>At<strong> My Nutrify Herbal & Ayurveda, </strong> our roots are established in a profound respect
                    for nature's healing energies and Ayurveda's rich heritage. Guided by the inspiration that genuine
                    well-being comes from balance, our founders set out to revive ancient treatments and reformulate
                    them for modern ways of life. Our dedication to quality, sustainability, and authenticity has led us
                    every step of the way, and now we are honored to serve a global community in search of natural
                    health solutions.
                </p>
            </div>

        </section>

        <section class="about-mission-vision-grid">
            <div class="about-mission-bullet">
                <div class="about-hero-Head">
                    <h1>Our<span>Mission
                        </span>
                    </h1>
                </div>
                <p>Our mission at My Nutrify Herbal & Ayurveda is to empower our community with nature-based remedies that
                    heal and restore well-being. We are dedicated to:</p>
                <p><span style="font-weight: bold; color: #2c3e50; margin-right: 5px;">Delivering Excellence:</span> Developing high-quality herbal and Ayurvedic products that are
                    safe and effective, from sustainably sourced ingredients.
                </p>
                <p><span style="font-weight: bold; color: #2c3e50; margin-right: 5px;">Educating and Inspiring:</span> Educating and inspiring you on Ayurveda and natural well-being,
                    so you can make healthy choices.
                </p>
                <p><span style="font-weight: bold; color: #2c3e50; margin-right: 5px;">Preserving Tradition:</span> Maintaining the true practices of Ayurveda while incorporating
                    innovation and modern research.
                </p>
                <p><span style="font-weight: bold; color: #2c3e50; margin-right: 5px;">Promoting Sustainability:</span> Making sure our practices respect the environment and
                    healthier planet.
                </p>
                <p><span style="font-weight: bold; color: #2c3e50; margin-right: 5px;">Building Community:</span> Creating a network of support where wellness is a journey we all
                    share and each person's well-being is our highest goal.
                </p>
            </div>
            <div class="about-mission-bullet">
                <div class="about-hero-Head">
                    <h1>Our<span>Vision
                        </span>
                    </h1>
                </div>
                <p>Our dream is to be a respected international leader in herbal well-being, where nature's purity and
                    Ayurveda's heritage meet. We aim to create a future where people across the globe adopt an
                    integrated lifestyle, leveraging the power of natural extracts to attain the highest levels of
                    health. We envision a world where contemporary lifestyles meet timeless wisdom, and where all
                    individuals are able to lead a dynamic, balanced life.</p>
            </div>
        </section>

        <section class="about-core-values">
            <div class="about-hero-Head">
            <h1 style="text-align: center; display: block; ">
    Our <span>Core Values</span>
</h1>

            </div>
            <ul>
                <li><strong>Authenticity:</strong> We respect the ancient traditions of Ayurveda and pledge transparency
                    in every action of our process.

                </li>
                <li><strong>Quality: </strong> Each product is carefully produced to the highest standards, with purity
                    and potency guaranteed.
                </li>
                <li><strong>Wellness:</strong> We subscribe to a holistic philosophy that enriches not only the body,
                    but mind and spirit as well.</li>
                <li><strong>Sustainability:</strong> Our dedication to environmentally friendly practices influences our
                    sourcing, production, and packaging processes.
                </li>
                <li><strong>Community: </strong> We cherish our connections with customers, practitioners, and partners,
                    aiming to create a ripple effect of wellness and education.
                </li>
            </ul>
        </section>
        <section class="about-mission-vision-grid">           
            <div class="about-mission-bullet">
                <div class="about-hero-Head">
                    <h1><span>Join</span>Our<span>Journey
                        </span>
                    </h1>
                </div>
                <p>At My Nutrify Herbal & Ayurveda, we believe that wellness is a journey that continues throughout a lifetime. We encourage you to discover our product line, read our educational content, and join a community committed to natural, sustainable wellness solutions. Together, let us forge a future in which ancient wisdom guides modern wellness, and each day brings us closer to a healthier, more balanced existence.
                </p>

                <h1>Welcome to the My Nutrify family—where nature cares and Ayurveda heals.
                </h1>

            </div>
        </section>
    </div>
    <!-- about content end -->
    <!-- about counter start -->
    <!-- <section>
        <div class="about-counter section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="text-center">
                            <div class="counter">
                                <h2 class="timer count-title count-number" data-to="21" data-speed="1500">12</h2>
                                <p class="count-text ">Years in business</p>
                            </div>
                            <div class="counter">
                                <h2 class="timer count-title count-number" data-to="210" data-speed="1500"></h2>
                                <p class="count-text ">Clients and partners</p>
                            </div>
                            <div class="counter">
                                <h2 class="timer count-title count-number" data-to="18" data-speed="1500"></h2>
                                <p class="count-text ">Show room</p>
                            </div>
                            <div class="counter">
                                <h2 class="timer count-title count-number" data-to="17" data-speed="1500"></h2>
                                <p class="count-text ">Billon sales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- about counter end -->
    <!-- brand logo start -->
    <section class="section-tb-padding home-brand1">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="brand-carousel owl-carousel owl theme">
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l1.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l2.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l3.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l4.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l5.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l6.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l7.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="image/brand/home-123/l8.png" alt="home brand" class="img-fluid">
                                </a>
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
    AOS.init({
        duration: 800,
        once: true,
        easing: 'ease-in-out-quad'
    });   
</script>
    <script>
    // Wait for the entire page to load
    window.addEventListener("load", function() {
        // Hide the preloader
        const preloader = document.getElementById("preloader");
        preloader.style.display = "none";

        // Show the main content
        const content = document.getElementById("content");
        content.style.display = "block";
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
})(window,document,'script','dataLayer','GTM-XXXXXX');

dataLayer.push({event: "gtm.load", ...})
{
  event: "gtm.load",
  gtm: {uniqueEventId: 13, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.dom", ...})

{
  event: "gtm.dom",
  gtm: {uniqueEventId: 12, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.load", ...})
{
  event: "gtm.load",
  gtm: {uniqueEventId: 11, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.js", ...})
{
  event: "gtm.js",
  gtm: {uniqueEventId: 10, start: 1748177630404, priorityId: undefined}
}
{
  event: "gtm.js",
  gtm: {uniqueEventId: 3, start: 1748177630404, priorityId: undefined}
}
{event: "gtm.init", gtm: {uniqueEventId: 2}}
{event: "gtm.init_consent", gtm: {uniqueEventId: 1}}
</script>
</body>

</html>
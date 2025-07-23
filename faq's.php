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
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Lobster+Two" rel="stylesheet">
    <style>
    @charset "UTF-8";

    :root {
        --primary: #EA652D;
        --secondary: #305724;
        --background: #f8f9fa;
        --text: #2c3e50;
    }

    /*
body {
  background-color: var(--background);
  font-family: 'Inter', sans-serif;
  line-height: 1.6;
  color: var(--text);
}

h1 {
  text-align: center;
  margin: 2rem 0;
  font-size: 2.5rem;
  color: var(--secondary);
  position: relative;
  display: inline-block;
  width: 100%;
}

h1:after {
  content: '';
  display: block;
  width: 60px;
  height: 4px;
  background: var(--primary);
  margin: 1rem auto;
  border-radius: 2px;
} */
    .faq-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #305724;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-in-out;
        margin-bottom: 1rem;

    }

    .faq-heading {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .faq-heading img {
        width: 50px;
        height: auto;
    }

    .faq-heading h2 {
        font-size: 28px;
        color: white;
        font-weight: bold;
    }

    .faq-img img {
        width: 80px;
        height: auto;
        animation: float 3s infinite ease-in-out;
    }

    /* FAQ Accordion */
    .faq-accordion__content-inner {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-top: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .faq-accordion-queans {
        border-bottom: 1px solid #ddd;
        padding: 15px 0;
    }

    .faq-accordion-queans:last-child {
        border-bottom: none;
    }

    .faq-accordion-queans h6 {
        font-size: 18px;
        font-weight: bold;
        color: #EA652D;
        cursor: pointer;
        transition: color 0.3s ease-in-out;
    }

    .faq-accordion-queans h6:hover {
        color: #305724;
    }

    .faq-accordion-queans p {
        font-size: 16px;
        color: #333;
        display: none;
        transition: all 0.3s ease-in-out;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .faq-head {
            flex-direction: column;
            text-align: center;
        }

        .faq-heading {
            justify-content: center;
        }

        .faq-img img {
            width: 60px;
        }

        .faq-accordion__content-inner {
            padding: 15px;
        }

        .faq-accordion-queans h6 {
            font-size: 16px;
        }

        .faq-accordion-queans p {
            font-size: 14px;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    .faq-accordion {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .faq-accordion__item {
        background: white;
        border-radius: 12px;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .faq-accordion__item:hover {
        transform: translateY(-2px);
    }

    .faq-accordion__title {
        padding: 1.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .faq-accordion__title h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #305724;
    }

    .faq-accordion__title::after {
        content: '+';
        font-size: 1.5rem;
        color: #EA652D;
        transition: transform 0.3s ease;
    }

    .faq-accordion__item.is-expanded .faq-accordion__title::after {
        transform: rotate(360deg);
    }

    .faq-accordion__content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-accordion__content-inner {
        padding: 0 1.5rem 1.5rem;
        color: #4a5568;
    }

    /* Focus States */
    .faq-accordion__title:focus-visible {
        outline: 2px solid #EA652D;
        outline-offset: 2px;
        border-radius: 6px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        /* h1 {
    font-size: 2rem;
  } */

        .faq-accordion__title h5 {
            font-size: 1rem;
        }

        .faq-accordion__content-inner {
            font-size: 0.9rem;
        }
    }

    /* Loading Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .faq-accordion__item {
        animation: fadeIn 0.5s ease forwards;
        opacity: 0;
    }

    .faq-accordion__item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .faq-accordion__item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .faq-accordion__item:nth-child(3) {
        animation-delay: 0.3s;
    }

    .faq-accordion__item:nth-child(4) {
        animation-delay: 0.4s;
    }

    .faq-accordion-queans {
        border-bottom: 1px solid #e2e8f0;
        /* Light grey border for separation */
        padding: 1rem 0;
        transition: background-color 0.3s ease-in-out;
    }

    .faq-accordion-queans h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        /* Dark grey color for better readability */
        margin-bottom: 0.5rem;
    }

    .faq-accordion-queans p {
        font-size: 0.95rem;
        color: #4a5568;
        /* Slightly lighter grey for paragraph text */
        line-height: 1.5;
    }

    .faq-accordion-queans:hover {
        background-color: #f7fafc;
        /* Light hover effect */
    }

    @media (max-width: 768px) {
        .faq-accordion-queans h6 {
            font-size: 1rem;
        }

        .faq-accordion-queans p {
            font-size: 0.85rem;
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

   
   
    </style>
</head>
</head>

<body class="home-1">
    
    <!-- header start -->
    <?php include("components/header.php") ?>
    <!-- header end -->

    <section class="faq-accordion" role="tablist" aria-live="polite" data-behavior="accordion">
        <div class="faq-head">
            <div class="faq-heading">
                <img src="image/main-logo.png" alt="" class="">
                <h2>Frequently Asked Questions</h2>
            </div>
            <div class="faq-img">
                <img src="image/faq.png" alt="" class="">
            </div>
        </div>

        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab4" tabindex="0" class="faq-accordion__title" aria-controls="panel4" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>BP Care</h5>
            </span>

            <div id="panel4" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab4"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1: What is My Nutrify Herbal & Ayurveda BP Care Juice?
                        </h6>
                        <p>Ans: It’s a 100% Ayurvedic, herbal formulation crafted to help manage blood pressure and
                            support heart health using natural ingredients like Sarpgandha, Shankhpishi, and four
                            additional herbal extracts.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q2: What benefits can I expect from using this juice?
                        </h6>
                        <p>Ans: it Regular use may help balance blood pressure levels, improve cholesterol profiles,
                            support healthy circulation, and promote overall cardiovascular well-being—all thanks to its
                            potent, natural herbal blend.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q3: How should I consume the BP Care Juice?
                        </h6>
                        <p>Ans: Shake the bottle well before use. Then, dilute 30 ml of the juice in a glass of water.
                            It’s best consumed on an empty stomach—typically in the morning—and you may also take it as
                            advised by your healthcare professional.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q4: Can I take this juice alongside my regular medications?
                        </h6>
                        <p>Ans: My Nutrify BP Care Juice is formulated with natural ingredients and is generally safe to
                            use with allopathic medications. However, it’s important to consult your doctor before
                            combining it with any prescription treatments.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q5: Are there any side effects?
                        </h6>
                        <p>Ans: Since it’s made entirely from natural herbs, side effects are rare. If you experience
                            any unusual symptoms or allergies, discontinue use and consult a healthcare professional.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q6: Is the juice safe for pregnant or breastfeeding women?
                        </h6>
                        <p>Ans: While the product is made with natural ingredients, pregnant or breastfeeding women
                            should seek advice from their healthcare provider before using the juice.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q7: What is the shelf life of My Nutrify BP Care Juice?
                        </h6>
                        <p>Ans: The juice is designed to maintain its quality for up to 24 months from the manufacturing
                            date. Always check the packaging for the exact expiration date.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q8: How long does it take to see results?
                        </h6>
                        <p>Ans: Individual experiences may vary, but many users notice improvements within 2–3 months of
                            consistent use.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q9: How should I store the product?

                        </h6>
                        <p>Ans: Store the bottle in a cool, dry place away from direct sunlight. Once opened, following
                            any specific storage instructions on the packaging (such as refrigeration) can help preserve
                            its potency.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q10: Where can I purchase My Nutrify BP Care Juice?
                        </h6>
                        <p>Ans: The juice is available directly on the My Nutrify official website as well as through
                            select retail partners and authorized online stores.
                        </p>
                    </div>
                </div>
            </div>
        </article>

        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab2" tabindex="0" class="faq-accordion__title" aria-controls="panel2" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>Thyro Balance Care Juice
                </h5>
            </span>

            <div id="panel2" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab2"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1. What is My Nutrify Thyro Balance Care Juice?
                        </h6>
                        <p>Ans:My Nutrify Thyro Balance Care Juice is a natural supplement formulated to support healthy
                            thyroid function and promote overall hormonal balance. Crafted with a unique blend of herbal
                            ingredients, it is designed to enhance metabolism and well-being.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q2. How does My Nutrify Thyro Balance Care Juice support thyroid health?

                        </h6>
                        <p>Ans:This juice is enriched with carefully selected natural herbs and vitamins that help
                            regulate hormone levels and support optimal thyroid performance. Its formulation aims to
                            boost energy levels and improve metabolic functions naturally.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q3. What are the key ingredients in My Nutrify Thyro Balance Care Juice?
                        </h6>
                        <p>Ans:The product features a potent mix of traditional herbal extracts and essential nutrients.
                            While the specific blend may vary, you can expect ingredients known for their
                            thyroid-supportive properties—helping to maintain a balanced endocrine system.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q4. Who can benefit from using My Nutrify Thyro Balance Care Juice?
                        </h6>
                        <p>Ans:Individuals seeking natural support for thyroid function and hormonal balance may find
                            this juice beneficial. It’s ideal for those with mild thyroid concerns as well as anyone
                            looking to enhance overall wellness through natural nutrition.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q5. How should I use My Nutrify Thyro Balance Care Juice?
                        </h6>
                        <p>Ans:For best results, it is recommended to take one serving daily as directed on the product
                            label. Many users prefer taking it on an empty stomach to maximize absorption and
                            effectiveness.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q6. Are there any side effects associated with My Nutrify Thyro Balance Care Juice?
                        </h6>
                        <p>Ans:Since the juice is made with natural ingredients, it is generally well tolerated.
                            However, as with any dietary supplement, it’s important to consult your healthcare provider
                            before use—especially if you have pre-existing conditions or are taking medications.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q7. How long does it take to see results?
                        </h6>
                        <p>Ans:Results can vary by individual. Many users report experiencing enhanced energy levels and
                            improved well-being within a few weeks of consistent use, though optimal benefits may take
                            longer depending on your body’s response.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q8. Is My Nutrify Thyro Balance Care Juice suitable for vegetarians or vegans?

                        </h6>
                        <p>Ans:Yes, the formulation is designed with natural, plant-based ingredients and is suitable
                            for vegetarians. Please refer to the packaging or product details for confirmation regarding
                            vegan compatibility.

                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q9. Where can I purchase My Nutrify Thyro Balance Care Juice?

                        </h6>
                        <p>Ans:You can purchase My Nutrify Thyro Balance Care Juice through the official website or from
                            authorized online retailers. Buying from verified sources ensures you receive a genuine
                            product with full benefits.

                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q10. What sets My Nutrify Thyro Balance Care Juice apart from other thyroid support supplements?

                        </h6>
                        <p>Ans:Unlike many other supplements, My Nutrify Thyro Balance Care Juice is uniquely formulated with
                            a synergistic blend of high-quality herbal extracts aimed at providing holistic thyroid
                            support. Its focus on natural ingredients and overall hormonal balance makes it a standout
                            choice for those seeking a safe, effective way to enhance thyroid health.

                        </p>
                    </div>
                </div>
            </div>
        </article>

        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab3" tabindex="0" class="faq-accordion__title" aria-controls="panel3" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5> Wheat Grass Juice
                </h5>
            </span>

            <div id="panel3" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab3"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1. What is My Nutrify Herbal & Ayurveda Wheat Grass Juice?</h6>
                        <p>Ans: It’s a 100% natural wheatgrass juice made from premium 9th day–picked wheatgrass leaves.
                            Crafted using herbal and Ayurvedic methods, it’s designed as a pure, nutrient-packed health
                            drink.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q2. What health benefits does this wheatgrass juice offer?</h6>
                        <p>Ans: Wheatgrass juice is known to help detoxify the body, boost immunity, aid digestion, and
                            provide essential vitamins, minerals, antioxidants, and amino acids that support increased
                            energy and overall vitality.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q3. Is this juice completely natural and free from additives?</h6>
                        <p>Ans: Yes. My Nutrify Wheat Grass Juice is made with no added sugar, preservatives, or
                            chemicals—it’s pure, natural, and adheres to Ayurvedic principles.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q4. How should I store the wheatgrass juice?</h6>
                        <p>Ans: To preserve its freshness and nutrients, keep the juice refrigerated and consume it
                            within a few days after opening. Always check the label for any specific storage
                            recommendations.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q5. Is wheatgrass juice gluten-free?</h6>
                        <p>Ans: Absolutely. Although it comes from wheatgrass, it’s harvested before the
                            gluten-containing seed develops, making it naturally gluten-free.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q6. What is the recommended daily intake?</h6>
                        <p>Ans: Many users start with about 30–50 ml of wheatgrass juice on an empty stomach each
                            morning. However, you can adjust your intake according to your personal health goals. If you
                            have any medical concerns, consult a healthcare professional.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q7. Are there any side effects?</h6>
                        <p>Ans: Wheatgrass juice is generally safe for most people. In some cases, especially when taken
                            in excess, it might cause mild digestive discomfort such as bloating or gas. If you’re
                            allergic to grasses or have specific health conditions, please consult your doctor before
                            use.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q8. Who can benefit from using this juice?</h6>
                        <p>Ans: It’s ideal for adults looking to enhance their overall well-being. However, if you are
                            pregnant, nursing, or have any chronic health issues, it’s best to seek advice from your
                            healthcare provider before adding it to your routine.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q9. Can I mix this wheatgrass juice with other beverages?</h6>
                        <p>Ans: Yes. While it’s most effective when taken on an empty stomach, you can blend it with
                            other juices or add it to a smoothie if you prefer a milder taste.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q10. How is the juice prepared?</h6>
                        <p>Ans: The juice is extracted from freshly harvested wheatgrass using methods that minimize
                            oxidation and preserve its active enzymes and nutrients. This traditional process ensures
                            you receive a potent, high-quality herbal supplement.</p>
                    </div>
                </div>
            </div>
        </article>

        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab4" tabindex="0" class="faq-accordion__title" aria-controls="panel4" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>She Care Plus</h5>
            </span>

            <div id="panel4" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab4"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>
                            Q1:What is My Nutrify Herbal & Ayurveda She Care Plus Juice?
                        </h6>
                        <p>Ans : It is an all-natural, premium herbal juice specifically for women's well-being. The
                            ayurvedic blend combines traditional herbs with the latest nutritional research to support
                            energy, digestion, and hormonal health.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q2: What are the major ingredients in this herbal juice?
                        </h6>
                        <p>Ans: Our juice contains a synergistic blend of natural extracts and ayurvedic herbs that
                            include turmeric, ashwagandha, ginger, tulsi, and other effective botanicals. These are
                            selected for their rejuvenating, anti-inflammatory, and antioxidant properties.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q3: How is the juice supportive of women's health?
                        </h6>
                        <p>Ans: Through the power of ayurveda, this herbal beverage is developed to support overall
                            wellness. Daily use can possibly balance hormones, improve metabolism, aid in
                            detoxification, and enhance digestion—keys to healthy living.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q4: Is My Nutrify She Care Plus Juice to be taken any particular way?
                        </h6>
                        <p>Ans: For optimal effectiveness, take 30-50 ml once or twice a day, preferably on an empty
                            stomach. Shake well before consumption to have the natural ingredients uniformly
                            distributed. Take as required and adhere to any special instructions given by your
                            healthcare provider.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q5: Is this juice safe to consume daily?
                        </h6>
                        <p>Ans: Yes, if taken as instructed, our ayurvedic and herbal formula is safe to consume on a
                            daily basis. But if you are pregnant, lactating, or have any underlying medical conditions,
                            please seek advice from your healthcare professional before taking any new supplement.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q6: Can I consume this juice along with my other medications or supplements?
                        </h6>
                        <p>Ans: Although our juice is derived from natural products, we do recommend that you consult
                            with your healthcare practitioner if you're on any other medications or supplements. This
                            will prevent any possible interactions and ensure it's part of your overall health regimen.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q7: Where can I buy My Nutrify Herbal & Ayurveda She Care Plus Juice?
                        </h6>
                        <p>Ans: You may purchase our product from our website or from licensed resellers. Be on the
                            lookout for verified sellers to ensure you're getting the authentic, high-quality product.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q8: How do I store the juice?
                        </h6>
                        <p>Ans: Store your juice in a dry, cool place and out of direct sunlight. Upon opening, best to
                            refrigerate and consume within the time frame given on the package to preserve its
                            effectiveness and freshness.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q9: Why is this juice unique compared to other herbal drinks?
                        </h6>
                        <p>Ans: Our product is unique in its sole concentration on women's health, blending the wisdom
                            of ancient ayurveda with contemporary nutritional knowledge. This special blend is designed
                            to nurture female wellness, energy equilibrium, and overall vigor.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q10: Are there any side effects to the juice?
                        </h6>
                        <p>Ans: The juice is made with natural, herbal ingredients and is generally well-tolerated.
                            However, individual reactions can vary. If you experience any discomfort or adverse effects,
                            discontinue use immediately and consult your healthcare professional.
                        </p>
                    </div>

                </div>
            </div>
        </article>
        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab4" tabindex="0" class="faq-accordion__title" aria-controls="panel4" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>Wild Amla </h5>
            </span>

            <div id="panel4" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab4"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1. What makes My Nutrify Herbal & Ayurveda Wild Amla Juices unique?</h6>
                        <p>Ans: Our wild Amla juice is crafted using traditional Ayurvedic methods and premium herbal
                            ingredients. It is rich in natural antioxidants and vitamin C, offering a holistic approach
                            to boosting your immunity and overall wellness.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q2. How is the wild Amla juice prepared?</h6>
                        <p>Ans: We use a cold-pressed extraction process to preserve the natural nutrients of wild Amla.
                            The juice is blended with a carefully selected mix of herbal extracts according to Ayurvedic
                            principles, ensuring maximum potency and purity.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q3. What are the key health benefits of wild Amla juice?</h6>
                        <p>Ans: Wild Amla juice is known for its high vitamin C content, which helps support immunity,
                            aids digestion, and promotes healthy skin. Additionally, the Ayurvedic blend contributes to
                            balancing the body’s doshas, enhancing overall vitality.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q4. Is My Nutrify Wild Amla Juice made with organic ingredients?</h6>
                        <p>Ans: Yes, we prioritize natural purity. Our wild Amla and other herbal components are sourced
                            from trusted organic farms, ensuring that you receive a product free from synthetic
                            additives and preservatives.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q5. How often should I consume wild Amla juice for optimal benefits?</h6>
                        <p>Ans: For most individuals, a daily serving of wild Amla juice is recommended as part of a
                            balanced diet. However, dosage may vary based on personal health needs. We advise consulting
                            with an Ayurvedic practitioner or healthcare professional for personalized guidance.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q6. Are there any side effects of consuming wild Amla juice?</h6>
                        <p>Ans: Our juice is formulated to be gentle on the body. That said, if you have a specific
                            health condition or are on medication, it’s best to consult with a healthcare provider
                            before incorporating any new supplement into your routine.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q7. How should I store My Nutrify Wild Amla Juices?</h6>
                        <p>Ans: To maintain freshness and nutritional value, store the juice in a cool, dark place or
                            refrigerate after opening. Always follow the storage guidelines provided on the packaging.
                        </p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q8. What is the shelf life of the product?</h6>
                        <p>Ans: Our wild Amla juice is carefully processed to ensure maximum freshness. The shelf life
                            typically spans several weeks when stored correctly. Check the expiration date on the bottle
                            for the most accurate information.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q9. Can this juice aid in digestion and boost immunity?</h6>
                        <p>Ans: Absolutely. The Ayurvedic formulation of our wild Amla juice is designed to support
                            digestive health and boost the immune system, making it an ideal addition to your daily
                            wellness routine.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q10. Where can I purchase My Nutrify Herbal & Ayurveda Wild Amla Juices?</h6>
                        <p>Ans: You can buy our wild Amla juice directly from our official website and select health
                            stores. Look for our product at trusted retailers to ensure you receive the genuine,
                            high-quality formulation.</p>
                    </div>
                </div>

        </article>

        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab4" tabindex="0" class="faq-accordion__title" aria-controls="panel4" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>Diabetic Care
                </h5>
            </span>

            <div id="panel4" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab4"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1. What is My Nutrify Herbal & Ayurveda Diabetic Care Juice?</h6>
                        <p>Ans: My Nutrify Diabetic Care Juice is an herbal natural formula meant to aid in blood sugar
                            control. It is supplemented with Ayurvedic herbs that have proven to effectively manage
                            diabetes.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q2. How does Diabetic Care Juice assist in diabetes control?</h6>
                        <p>Ans: This juice assists in the support of insulin production, balances blood glucose levels,
                            enhances metabolism, and aids in overall pancreatic health. Repeated use could help maintain
                            healthy sugar levels naturally.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q3. Is the juice safe to drink every day?</h6>
                        <p>Ans: Yes, Nutrify Diabetic Care Juice is prepared from pure and natural products without any
                            chemicals or additives that are harmful. It is safe for daily use according to the
                            recommended dosage.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q4. Can I take this juice along with my diabetes medication?</h6>
                        <p>Ans: Yes, you can consume it along with your prescribed diabetes medication. However, it is
                            always advisable to consult your doctor before making any changes to your routine.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q5. Does this juice have any side effects?</h6>
                        <p>Ans: Since Nutrify Diabetic Care Juice is made from natural herbs, it is generally safe and
                            well-tolerated. However, if you experience any discomfort or allergic reactions, discontinue
                            use and consult a healthcare professional.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q6. Who can consume Diabetic Care Juice?</h6>
                        <p>Ans: It is suitable for individuals looking to naturally manage blood sugar levels. However,
                            pregnant women, lactating mothers, and individuals with severe medical conditions should
                            seek medical advice before use.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q7. How long does it take to see results?</h6>
                        <p>Ans: Results may vary depending on individual health conditions and lifestyle. Consistent
                            use, along with a balanced diet and regular exercise, can help in achieving better results
                            over time.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q8. Can I consume this juice if I am prediabetic?</h6>
                        <p>Ans: Yes, this juice is beneficial for individuals who are prediabetic as it helps regulate
                            blood sugar levels and prevents spikes.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q9. How does this product differ from other diabetes care juices?</h6>
                        <p>Ans: Our formula is enriched with pure and potent Ayurvedic herbs without any artificial
                            preservatives, making it more effective in naturally managing blood sugar levels.</p>
                    </div>
                    <div class="faq-accordion-queans">
                        <h6>Q10. What are the key ingredients in Diabetic Care Juice?</h6>
                        <p>Ans: Our Diabetic Care Juice contains powerful Ayurvedic herbs like Karela (Bitter Gourd),
                            Jamun (Indian Blackberry), Methi (Fenugreek), Gudmar, Neem, and Amla, all known for their
                            blood sugar-regulating properties.</p>
                    </div>
                </div>

            </div>
        </article>



    </section>

    <!-- Accordion 2 -->
    <section class="faq-accordion" role="tablist" aria-live="polite" data-behavior="accordion">
        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab5" tabindex="0" class="faq-accordion__title" aria-controls="panel5" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>Apple cider vinegar
                </h5>
            </span>

            <div id="panel5" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab5"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1. What is Nutrify Herbal & Ayurveda Apple Cider Vinegar?</h6>
                        <p>Ans. My Nutrify Herbal & Ayurveda Apple Cider Vinegar is a premium blend of naturally
                            fermented
                            apple cider vinegar enhanced with traditional Ayurvedic herbs. It is designed to support
                            overall wellness, improve digestion, and promote a balanced lifestyle.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q2.How is Nutrify Herbal & Ayurveda Apple Cider Vinegar different from regular apple cider
                            vinegar?</h6>
                        <p>Ans. Unlike standard apple cider vinegar, our formulation combines the natural benefits of
                            apple
                            cider vinegar with carefully selected herbal and Ayurvedic ingredients. This unique blend
                            not only helps maintain digestive health but also supports metabolism and detoxification for
                            a holistic approach to wellness.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q3. What are the health benefits of using Nutrify Herbal & Ayurveda Apple Cider Vinegar?
                        </h6>
                        <p>Ans. Our product is known to:
                        <ul>
                            <li>Aid in digestion and improve gut health</li>
                            <li>Support weight management and metabolism</li>
                            <li>Promote detoxification and boost energy levels</li>
                            <li>Enhance immune function with natural antioxidants</li>
                        </ul>
                        The synergistic effects of the herbal and Ayurvedic components make it a powerful natural remedy
                        for everyday health.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q4. How do I incorporate Nutrify Herbal & Ayurveda Apple Cider Vinegar into my daily
                            routine?
                        </h6>
                        <p>Ans. For best results, mix 1–2 tablespoons of Nutrify Herbal & Ayurveda Apple Cider Vinegar
                            with a
                            glass of water and drink it before meals. You can also use it as a tangy addition to salad
                            dressings or marinades. Always follow the dosage instructions provided on the product label.
                        </p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q5. Is it safe to consume daily?</h6>
                        <p>Ans. Yes, when taken as directed, Nutrify Herbal & Ayurveda Apple Cider Vinegar is safe for
                            daily
                            consumption. However, if you have any pre-existing health conditions or are taking
                            medications, it’s advisable to consult with your healthcare provider before adding it to
                            your regimen.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q6. What ingredients are used in Nutrify Herbal & Ayurveda Apple Cider Vinegar?</h6>
                        <p>Ans. Our product is made from high-quality, naturally fermented apple cider vinegar blended
                            with
                            traditional Ayurvedic herbs such as turmeric, ginger, and other potent botanicals. This
                            carefully selected mix ensures a balanced flavor and maximum health benefits without any
                            artificial additives.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q7. Can Nutrify Herbal & Ayurveda Apple Cider Vinegar help with weight management?</h6>
                        <p>Ans. Yes, many users incorporate our apple cider vinegar into their daily routine as part of
                            a
                            healthy lifestyle. The natural acids and herbal components help boost metabolism and reduce
                            cravings, which can support weight management when combined with a balanced diet and regular
                            exercise.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q8. How does Nutrify Herbal & Ayurveda Apple Cider Vinegar support digestive health?</h6>
                        <p>Ans. The natural acetic acid in apple cider vinegar helps balance stomach acid levels and
                            supports
                            a healthy gut environment. When combined with Ayurvedic herbs known for their digestive
                            properties, it aids in reducing bloating and promoting smooth digestion.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q9. Are there any side effects associated with using this product?</h6>
                        <p>Ans. Nutrify Herbal & Ayurveda Apple Cider Vinegar is made from natural ingredients and is
                            generally well-tolerated. Some users may experience mild digestive adjustments when first
                            starting the product. If any discomfort occurs, it is recommended to start with a smaller
                            dose and gradually increase, or consult with a healthcare professional.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q10. Where can I purchase Nutrify Herbal & Ayurveda Apple Cider Vinegar?</h6>
                        <p>Ans. You can purchase our product directly from our official website or from authorized
                            retailers.
                            Look for the Nutrify brand logo to ensure you’re getting the authentic formulation designed
                            for maximum wellness benefits.</p>
                    </div>
                </div>

            </div>

        </article>

        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab6" tabindex="0" class="faq-accordion__title" aria-controls="panel6" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>Cholesterol Care
                </h5>
            </span>

            <div id="panel6" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab6"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q.1 What is My Nutrify Herbal & Ayurveda Cholesterol Care Juice?</h6>
                        <p>Ans. My Nutrify Herbal & Ayurveda Cholesterol Care Juice is a natural, Ayurvedic herbal
                            supplement
                            formulated to support healthy cholesterol levels and promote overall cardiovascular
                            well-being. This herbal cholesterol juice combines time-tested Ayurvedic ingredients to help
                            maintain a balanced lipid profile.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.2 What are the key ingredients in cholesterol care juice?</h6>
                        <p>Ans. The juice is crafted from a blend of potent Ayurvedic herbs such as amla, turmeric, and
                            other
                            natural extracts known for their antioxidant and lipid-regulating properties. For a detailed
                            list of ingredients, please refer to the product label.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.3 How does this herbal juice support healthy cholesterol levels?</h6>
                        <p>Ans. This Ayurveda cholesterol care juice is designed to enhance the body’s natural lipid
                            metabolism and reduce oxidative stress. Its unique blend of herbal ingredients works
                            synergistically to help maintain balanced cholesterol levels as part of a healthy lifestyle.
                        </p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.4 How should I consume My Nutrify Herbal & Ayurveda Cholesterol Care Juice?</h6>
                        <p>Ans. For optimal results, follow the dosage instructions on the packaging. Typically, before
                            meals
                            are recommended. However, it is best to consult your healthcare professional for
                            personalized usage guidelines.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.5 Is My Nutrify Herbal & Ayurveda Cholesterol Care Juice safe for everyone?</h6>
                        <p>Ans. Formulated with natural Ayurvedic herbs, this supplement is generally safe for most
                            adults.
                            Nonetheless, individuals with specific health conditions, those who are pregnant or nursing,
                            or those taking prescription medications should consult a healthcare provider before
                            starting any new supplement regimen.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.6 Are there any side effects associated with this herbal cholesterol juice?</h6>
                        <p>Ans. Most users experience no adverse effects when using My Nutrify Herbal & Ayurveda
                            Cholesterol
                            Care Juice as directed. However, if you have known allergies to herbal ingredients or
                            experience any unusual symptoms, please discontinue use and seek medical advice.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.7 Can I take this juice alongside my prescribed cholesterol medications?</h6>
                        <p>Ans. While the juice is a natural supplement aimed at supporting cardiovascular health, it is
                            important to consult with your healthcare provider before combining it with any prescribed
                            cholesterol medications to avoid potential interactions.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.8 How long does it take to see improvements in cholesterol levels?</h6>
                        <p>Ans. Results may vary by individual. With consistent use, along with a balanced diet and
                            regular
                            exercise, many users start noticing improvements in their cholesterol levels within a few
                            weeks to a few months. A holistic approach to health is key for optimal results.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.9 What are the storage instructions and shelf life for the juice?</h6>
                        <p>Ans. To maintain its potency, store the juice in a cool, dry place away from direct sunlight.
                            Always check the product label for specific storage instructions and the expiration date to
                            ensure freshness and efficacy.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q.10 Where can I purchase My Nutrify Herbal & Ayurveda Cholesterol Care Juice?</h6>
                        <p>Ans. You can buy this Ayurvedic herbal cholesterol care juice directly from the official My
                            Nutrify website or through select authorized retailers. For the latest purchasing details
                            and offers, visit our website or contact customer support.</p>
                    </div>
                </div>

            </div>
        </article>
        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab6" tabindex="0" class="faq-accordion__title" aria-controls="panel6" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>Shilajit
                </h5>
            </span>
            <div id="panel6" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab6"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1. How do I use My Nutrify Shilajit?</h6>
                        <p>Ans. Use the provided spoon to scoop a pea-sized amount of the resin. Dissolve it in
                            100–200 ml of
                            lukewarm water or milk, stirring until completely dissolved. For best results, consume it
                            twice daily—preferably in the morning or as advised by a healthcare professional.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q2. What are the benefits of My Nutrify Shilajit?</h6>
                        <p>Ans. My Nutrify Shilajit is designed to boost energy levels, enhance muscle strength, and
                            improve
                            endurance. With 80+ trace minerals and a rich fulvic acid content, it aids nutrient
                            absorption, supports detoxification, and contributes to overall vitality and well-being.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q3. What role does fulvic acid play in Shilajit?</h6>
                        <p>Ans. Fulvic acid is a key ingredient in our purified Himalayan Shilajit. It acts as a
                            powerful
                            antioxidant, helping to reduce inflammation, enhance nutrient absorption at the cellular
                            level, and support gut health, making it an essential component for natural energy and
                            wellness.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q4. How can I identify pure Shilajit?</h6>
                        <p>Ans. Pure Shilajit is typically sticky, tar-like, and ranges from dark brown to black in
                            color
                            with a distinctive earthy aroma. When a small amount is dissolved in warm water, it should
                            completely dissolve without leaving any residue, indicating its purity.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q5. What should I do if the Shilajit resin solidifies in the bottle?</h6>
                        <p>Ans. If you notice that the resin has solidified, gently warm the bottle in a container of
                            warm
                            water. This will soften the resin, allowing you to carefully scrape or pour out the desired
                            amount without compromising its quality.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q6. Is My Nutrify Shilajit safe to consume?</h6>
                        <p>Ans. Yes, My Nutrify Shilajit is a high-quality, purified Ayurvedic supplement manufactured
                            under
                            strict quality control standards. It is safe for consumption when taken as directed.
                            However, if you have any underlying health conditions or concerns, consult your healthcare
                            provider before use.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q7. Where is My Nutrify Shilajit sourced from?</h6>
                        <p>Ans. Our Shilajit is harvested from the pristine Himalayan mountain ranges, ensuring a
                            naturally
                            potent, high-altitude resin enriched with 80+ trace minerals and fulvic acid. This authentic
                            Ayurvedic source guarantees optimal purity and efficacy.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q8. What is the recommended dosage for My Nutrify Shilajit?</h6>
                        <p>Ans. For optimal benefits, take a pea-sized portion dissolved in 100–200 ml of lukewarm water
                            or
                            milk, twice daily. Always follow the product label instructions or consult your healthcare
                            provider for personalized guidance.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q9. Can both men and women use My Nutrify Shilajit?</h6>
                        <p>Ans. Yes, this purified Ayurvedic supplement is designed for both men and women. It supports
                            energy levels, stamina, and overall well-being across genders, making it a versatile
                            addition to your daily routine.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q10. Is My Nutrify Shilajit vegan-friendly?</h6>
                        <p>Ans. Absolutely. Our Shilajit is 100% vegetarian and vegan-friendly, containing no
                            animal-derived
                            ingredients. It’s an ideal natural supplement for those following a plant-based lifestyle.
                        </p>
                    </div>
                </div>

            </div>
        </article>
        <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
            <span id="tab6" tabindex="0" class="faq-accordion__title" aria-controls="panel6" role="tab"
                aria-selected="false" aria-expanded="false" data-binding="expand-accordion-trigger">
                <h5>Neem Karela & Jamun
                </h5>
            </span>
            <div id="panel6" class="faq-accordion__content" role="tabpanel" aria-hidden="true" aria-labelledby="tab6"
                data-binding="expand-accordion-container">
                <div class="faq-accordion__content-inner">
                    <div class="faq-accordion-queans">
                        <h6>Q1. What is My Nutrify Herbal & Ayurveda Neem Karela & Jamun Juice?</h6>
                        <p>Ans. My Nutrify Herbal & Ayurveda Neem Karela & Jamun Juice is an all-natural, Ayurvedic
                            herbal supplement designed to support overall wellness. This unique formulation combines the
                            powerful benefits of Neem, Karela (bitter gourd), and Jamun to help balance blood sugar
                            levels, promote detoxification, and enhance digestive health.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q2. What are the key benefits of its Ayurvedic ingredients?</h6>
                        <p>Ans. Neem: Known for its detoxifying, antibacterial, and immune-boosting properties. <br>
                            Karela (Bitter Gourd): Traditionally used in Ayurveda to support healthy blood sugar
                            regulation and improve digestion. <br>
                            Jamun: Rich in antioxidants, this ingredient aids in digestion and helps maintain balanced
                            blood sugar levels.<br>
                            These ingredients work synergistically to offer a holistic approach to health and wellness.
                        </p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q3. How should I consume this herbal juice for optimal results?</h6>
                        <p>Ans. For best results, take the recommended dose as indicated on the label once daily,
                            preferably on an empty stomach. Following the manufacturer's directions or consulting with
                            an Ayurvedic expert ensures you enjoy the maximum benefits of this natural remedy.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q4. Who can benefit from using this Ayurvedic herbal supplement?</h6>
                        <p>Ans. This product is ideal for adults seeking a natural way to support a balanced diet and
                            healthy lifestyle. Individuals interested in Ayurvedic health practices, natural
                            detoxification, or blood sugar management can benefit. However, those with specific medical
                            conditions, pregnant or breastfeeding women, or individuals taking prescription medications
                            should consult a healthcare professional before use.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q5. Can this juice replace a meal?</h6>
                        <p>Ans. No, My Nutrify Herbal & Ayurveda Neem Karela & Jamun Juice is a dietary supplement meant
                            to complement your meals—not replace them. It is designed to be part of a balanced diet and
                            a healthy lifestyle.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q6. Are there any side effects or precautions?</h6>
                        <p>Ans. When used as directed, most users experience no significant side effects. Some may
                            encounter mild digestive discomfort if taken on an empty stomach. It is important to review
                            the product label for any warnings and consult a healthcare provider if you have
                            pre-existing health concerns or are taking other medications.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q7. How should I store My Nutrify Herbal Juice?</h6>
                        <p>Ans. Store your juice in a cool, dry place away from direct sunlight to maintain its potency
                            and freshness. Always check the packaging for any specific storage instructions to ensure
                            the quality of this natural Ayurvedic product.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q8. Is the product suitable for vegans or individuals with dietary restrictions?</h6>
                        <p>Ans. Yes, the formulation is based on natural, herbal ingredients and is generally suitable
                            for vegans. For details on certifications such as gluten-free or organic, please refer to
                            the product label or contact the manufacturer directly.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q9. Where can I purchase My Nutrify Herbal & Ayurveda Neem Karela & Jamun Juice?</h6>
                        <p>Ans. This premium herbal supplement is available for purchase online through the official My
                            Nutrify website and authorized retailers. It may also be available on select e-commerce
                            platforms. Always buy from trusted sources to ensure product authenticity.</p>
                    </div>

                    <div class="faq-accordion-queans">
                        <h6>Q10. How long does it take to notice the benefits?</h6>
                        <p>Ans. Results can vary based on individual health conditions and lifestyle factors. Many users
                            report experiencing improvements in digestion, detoxification, and overall wellness within a
                            few weeks of consistent use. For personalized expectations, consider consulting an Ayurvedic
                            specialist.</p>
                    </div>
                </div>

            </div>
        </article>

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
    document.addEventListener('DOMContentLoaded', function() {
        const accordionItems = document.querySelectorAll('.faq-accordion__item');

        accordionItems.forEach(item => {
            const title = item.querySelector('.faq-accordion__title');
            const content = item.querySelector('.faq-accordion__content');

            title.addEventListener('click', () => toggleAccordion(item));

            // Keyboard navigation
            title.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleAccordion(item);
                }
            });
        });

        function toggleAccordion(item) {
            const isExpanded = item.classList.contains('is-expanded');
            const content = item.querySelector('.faq-accordion__content');

            // Close all items
            accordionItems.forEach(acc => {
                acc.classList.remove('is-expanded');
                acc.querySelector('.faq-accordion__content').style.maxHeight = null;
            });

            // Toggle current item
            if (!isExpanded) {
                item.classList.add('is-expanded');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        }
    });
    document.addEventListener("DOMContentLoaded", function() {
        const questions = document.querySelectorAll(".faq-accordion-queans h6");

        questions.forEach((question) => {
            question.addEventListener("click", function() {
                this.nextElementSibling.classList.toggle("active");
                if (this.nextElementSibling.classList.contains("active")) {
                    this.nextElementSibling.style.display = "block";
                } else {
                    this.nextElementSibling.style.display = "none";
                }
            });
        });
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
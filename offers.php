<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$mysqli = $obj->connection();

?>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Special Offers</title>
    <meta name="description"
        content="Discover amazing deals and special offers on our premium health and wellness products. Limited time offers with great savings!" />
    <meta name="keywords"
        content="offers, deals, discounts, special offers, health products, wellness, savings, limited time" />
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
    
    <style>
        /* Special Offers Page Styles */
        .offers-hero {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
        }

        .offers-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .offers-hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .offers-hero p {
            font-size: 1.3rem;
            margin-bottom: 0;
            position: relative;
            z-index: 2;
            opacity: 0.95;
        }
        
        .offers-badge {
            background: linear-gradient(45deg, #ff6b35, #f7931e);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 10;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            animation: pulse 2s infinite;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(255, 107, 53, 0.6); }
            100% { transform: scale(1); box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4); }
        }

        .product-card {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .product-card:hover {
            border-color: #ff6b35;
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.15);
        }
        
        .offers-count {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 15px;
            margin: 40px auto;
            text-align: center;
            max-width: 400px;
            border: 1px solid #dee2e6;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .offers-count h3 {
            color: #ff6b35;
            margin-bottom: 10px;
            font-size: 2.5rem;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .offers-count p {
            color: #495057;
            margin: 0;
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        .no-offers {
            text-align: center;
            padding: 100px 20px;
            color: #666;
        }
        
        .no-offers i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .no-offers h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .no-offers p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .btn-browse-products {
            background: #ff6b35;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-browse-products:hover {
            background: #e55a2b;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        /* Enhanced product card styles for offers */
        .product-badge {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
            animation: pulse 2s infinite;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 10px rgba(220, 53, 69, 0.4);
        }

        .product-image {
            position: relative;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
            height: 250px;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 20px;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.4;
            color: #333;
        }

        .product-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .product-title a:hover {
            color: #ff6b35;
        }

        .offer-title {
            color: #ff6b35;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .offer-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .product-price {
            margin-bottom: 15px;
        }

        .price-current {
            font-size: 1.3rem;
            font-weight: bold;
            color: #ff6b35;
        }

        .price-original {
            font-size: 1rem;
            color: #999;
            text-decoration: line-through;
            margin-left: 8px;
        }

        .price-discount {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-add-cart {
            width: 100%;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-add-cart:hover {
            background: linear-gradient(135deg, #e55a2b 0%, #e8841a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .product-actions {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-actions {
            opacity: 1;
        }

        .eye-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #ff6b35;
            color: #ff6b35;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .eye-btn:hover {
            background: #ff6b35;
            color: white;
            transform: scale(1.1);
        }
        
        /* Popup Overlay */
        .cart-popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
        }

        /* Popup Content */
        .cart-popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 300px;
        }

        /* Close Button */
        .close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            border: none;
            background: none;
            font-size: 20px;
            cursor: pointer;
        }

        /* Popup Heading */
        .cart-popup-body h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        /* Buttons */
        .cart-popup-actions a {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .cart-popup-actions .btn-view-cart {
            background: #305724;
        }

        .cart-popup-actions .btn-view-cart:hover {
            background: #000000;
        }

        .cart-popup-actions .btn-checkout {
            background: #ec6504;
        }

        .cart-popup-actions .btn-checkout:hover {
            background: #ffffff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .offers-hero h1 {
                font-size: 2.5rem;
            }

            .offers-hero p {
                font-size: 1.1rem;
            }

            .offers-hero {
                padding: 60px 0;
            }

            .offers-count {
                margin: 30px 15px;
                padding: 25px;
            }

            .offers-count h3 {
                font-size: 2rem;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
                gap: 20px !important;
                padding: 0 10px !important;
            }

            .product-image {
                height: 220px;
            }

            .product-info {
                padding: 15px;
            }

            .btn-add-cart {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .offers-hero h1 {
                font-size: 2rem;
            }

            .product-grid {
                grid-template-columns: 1fr !important;
                gap: 15px !important;
            }

            .product-image {
                height: 200px;
            }

            .offers-count h3 {
                font-size: 1.8rem;
            }
        }
    </style>
    
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1209485663860371');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1209485663860371&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>

<body class="home-1">
    <div id="cart-popup" class="cart-popup-overlay">
        <div class="cart-popup-content">
            <button class="close-popup" onclick="$('#cart-popup').fadeOut();">
                &times;
            </button>
            <h3>Product added to your cart!</h3>
            <div class="cart-popup-actions">
                <a href="cart.php" class="btn-view-cart">View Cart</a>
                <a href="checkout.php" class="btn-checkout">Checkout</a>
            </div>
        </div>
    </div>
    
    <!-- header start -->
    <?php include("components/header.php"); ?>
    <!-- header end -->

    <!-- Hero Section -->
    <section class="offers-hero">
        <div class="container">
            <h1><i class="fas fa-fire"></i> Special Offers</h1>
            <p>Discover amazing deals on our premium health and wellness products</p>
        </div>
    </section>

    <!-- Main Offers Section -->
    <section class="py-5">
        <div class="container-fluid full-width">
            <?php
            // Fetch all active offers using the view we created
            $query = "SELECT * FROM active_product_offers ORDER BY created_date DESC";
            $result = $mysqli->query($query);
            $offers = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $offers[] = $row;
                }
            }
            ?>
            
            <?php if (!empty($offers)): ?>
            <!-- Offers Count -->
            <div class="offers-count">
                <h3><?php echo count($offers); ?></h3>
                <p>Amazing offers available now!</p>
            </div>

            <!-- Offers Grid -->
            <div class="container">
                <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; padding: 0 15px;">
                <?php foreach ($offers as $offer): ?>
                <div class="product-card" data-product-id="<?php echo htmlspecialchars($offer["product_id"]); ?>">
                    <div class="offers-badge">
                        <?php if ($offer['discount_percentage'] > 0): ?>
                            <?php echo $offer['discount_percentage']; ?>% OFF
                        <?php else: ?>
                            SPECIAL OFFER
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($offer['savings_amount'] > 0): ?>
                        <div class="product-badge">
                            Save ₹<?php echo number_format($offer['savings_amount']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="product-image">
                        <a href="product_details.php?ProductId=<?php echo htmlspecialchars($offer["product_id"]); ?>">
                            <img class="main-image"
                                 src="cms/images/products/<?php echo htmlspecialchars($offer["PhotoPath"]); ?>"
                                 alt="<?php echo htmlspecialchars($offer["ProductName"]); ?>"
                                 loading="lazy">
                        </a>

                        <!-- Eye Button for Preview -->
                        <div class="product-actions">
                            <button class="eye-btn" onclick="showPreview(<?php echo htmlspecialchars($offer['product_id']); ?>)">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="product_details.php?ProductId=<?php echo htmlspecialchars($offer["product_id"]); ?>"
                               style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($offer["ProductName"]); ?>
                            </a>
                        </h3>
                        
                        <?php if ($offer['offer_title']): ?>
                        <div class="offer-title" style="color: #ff6b35; font-weight: bold; margin-bottom: 5px;">
                            <?php echo htmlspecialchars($offer['offer_title']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($offer['offer_description']): ?>
                        <div class="offer-description" style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">
                            <?php echo htmlspecialchars($offer['offer_description']); ?>
                        </div>
                        <?php endif; ?>

                        <div class="product-price">
                            <span class="price-current">₹<?php echo number_format($offer['min_offer_price'], 2); ?></span>
                            <?php if ($offer['min_mrp'] > $offer['min_offer_price']): ?>
                                <span class="price-original">₹<?php echo number_format($offer['min_mrp'], 2); ?></span>
                                <span class="price-discount"><?php echo $offer['discount_percentage']; ?>% OFF</span>
                            <?php endif; ?>
                        </div>

                        <button class="btn-add-cart add-to-cart-session"
                                data-product-id="<?php echo htmlspecialchars($offer['product_id']); ?>">
                            <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
            
            <?php else: ?>
            <!-- No Offers Available -->
            <div class="no-offers">
                <i class="fas fa-tags"></i>
                <h3>No Special Offers Available</h3>
                <p>Check back soon for amazing deals and discounts!</p>
                <a href="products.php" class="btn-browse-products">Browse All Products</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- footer start -->
    <?php include("components/footer.php"); ?>
    <!-- footer end -->

    <!-- Modal for Product Preview -->
    <?php include("components/product_preview_modal.php"); ?>

    <!-- jquery -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <!-- bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <!-- owl carousel -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- swiper -->
    <script src="js/swiper.min.js"></script>
    <!-- waypoint -->
    <script src="js/waypoint.min.js"></script>
    <!-- wow -->
    <script src="js/wow.min.js"></script>
    <!-- custom -->
    <script src="js/custom.js"></script>
    <!-- Add to cart functionality -->
    <script src="js/add-to-cart.js"></script>
    <!-- Product preview functionality -->
    <script src="js/product-preview.js"></script>

    <script>
        // Analytics tracking for offers page
        if (typeof trackPageView === 'function') {
            trackPageView('offers');
        }
        
        // Track offer interactions
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.btn-add-cart') && !e.target.closest('.eye-btn')) {
                    const productId = this.dataset.productId;
                    if (typeof trackProductView === 'function') {
                        trackProductView(productId, 'offers_page');
                    }
                }
            });
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

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

    <!-- Tawk.to Integration -->
    <?php include("components/tawk-to.php"); ?>

    <style>
        /* Fix navbar z-index issue */
        .header-area {
            position: relative !important;
            z-index: 9999 !important;
        }

        .header-main-area {
            position: relative !important;
            z-index: 9999 !important;
        }

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
            background:no repeat;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('cms/images/banners/Special Offers.jpg');
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
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            position: relative;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
            border-color: #ff6a00;
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
            position: absolute;
            top: 12px;
            left: 12px;
            background: #e53e3e;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 2;
        }

        .product-image {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1;
            background: #f8f9fa;
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
            display: flex;
            flex-direction: column;
            height: 42%;
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
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .price-current {
            font-size: 1.4rem;
            font-weight: bold;
            color: #ff6b35;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .price-original {
            font-size: 1rem;
            color: #999;
            text-decoration: line-through;
            font-weight: 500;
        }

        .price-discount {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }

        /* Responsive price display */
        @media (max-width: 768px) {
            .price-current {
                font-size: 1.2rem;
            }

            .price-original {
                font-size: 0.9rem;
            }

            .price-discount {
                font-size: 10px;
                padding: 3px 6px;
            }
        }

        .btn-add-cart {
            width: 100%;
            background: #305724 !important;
            color: white !important;
            border: none !important;
            padding: 12px 20px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
            margin-top: auto !important;
            align-self: flex-end !important;
        }

        .btn-add-cart:hover {
            background: #1e3a16 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(48, 87, 36, 0.4) !important;
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
    <?php include("components/chat_integration.php"); ?>
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
            <h1><i></i> </h1>
        </div>
    </section>

    <!-- Main Offers Section -->
    <section class="py-5">
        <div class="container-fluid full-width">
            <?php
            // Get offers from the active_product_offers view and fix pricing
            $offers = [];

            // First try the original active_product_offers view
            $query = "SELECT * FROM active_product_offers ORDER BY created_date DESC";
            $result = $mysqli->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Get the correct pricing for this specific product
                    $price_query = "SELECT MIN(OfferPrice) as min_offer_price, MIN(MRP) as min_mrp
                                  FROM product_price
                                  WHERE ProductId = ? AND OfferPrice > 0 AND MRP > 0";
                    $price_stmt = $mysqli->prepare($price_query);
                    $price_stmt->bind_param("i", $row['product_id']);
                    $price_stmt->execute();
                    $price_result = $price_stmt->get_result();

                    if ($price_result && $price_row = $price_result->fetch_assoc()) {
                        if ($price_row['min_offer_price'] > 0 && $price_row['min_mrp'] > 0) {
                            // Update pricing data in the offer
                            $row['min_offer_price'] = $price_row['min_offer_price'];
                            $row['min_mrp'] = $price_row['min_mrp'];
                            $row['savings_amount'] = $price_row['min_mrp'] - $price_row['min_offer_price'];
                            $row['discount_percentage'] = round((($price_row['min_mrp'] - $price_row['min_offer_price']) / $price_row['min_mrp']) * 100);
                            $offers[] = $row;
                        }
                    }
                }
            } else {
                // Fall back to direct product query to show all products with discounts
                $fallback_query = "SELECT
                    p.ProductId as product_id,
                    p.ProductName,
                    p.PhotoPath,
                    MIN(pp.OfferPrice) as min_offer_price,
                    MIN(pp.MRP) as min_mrp,
                    ROUND(((MIN(pp.MRP) - MIN(pp.OfferPrice)) / MIN(pp.MRP)) * 100) as discount_percentage,
                    (MIN(pp.MRP) - MIN(pp.OfferPrice)) as savings_amount,
                    NULL as offer_title,
                    NULL as offer_description,
                    NOW() as created_date
                FROM product_master p
                INNER JOIN product_price pp ON p.ProductId = pp.ProductId
                WHERE pp.OfferPrice > 0 AND pp.MRP > 0 AND pp.OfferPrice < pp.MRP
                GROUP BY p.ProductId, p.ProductName, p.PhotoPath
                HAVING min_offer_price > 0 AND min_mrp > 0 AND discount_percentage > 0
                ORDER BY discount_percentage DESC";

                $result = $mysqli->query($fallback_query);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $offers[] = $row;
                    }
                }
            }

            // If still no offers found, try to get any products with pricing
            if (empty($offers)) {
                $simple_query = "SELECT
                    p.ProductId as product_id,
                    p.ProductName,
                    p.PhotoPath,
                    MIN(pp.OfferPrice) as min_offer_price,
                    MIN(pp.MRP) as min_mrp,
                    CASE
                        WHEN MIN(pp.MRP) > MIN(pp.OfferPrice) AND MIN(pp.MRP) > 0
                        THEN ROUND(((MIN(pp.MRP) - MIN(pp.OfferPrice)) / MIN(pp.MRP)) * 100)
                        ELSE 0
                    END as discount_percentage,
                    CASE
                        WHEN MIN(pp.MRP) > MIN(pp.OfferPrice)
                        THEN (MIN(pp.MRP) - MIN(pp.OfferPrice))
                        ELSE 0
                    END as savings_amount,
                    NULL as offer_title,
                    NULL as offer_description,
                    NOW() as created_date
                FROM product_master p
                INNER JOIN product_price pp ON p.ProductId = pp.ProductId
                WHERE pp.OfferPrice > 0 AND pp.MRP > 0 AND pp.MRP > pp.OfferPrice
                GROUP BY p.ProductId, p.ProductName, p.PhotoPath
                HAVING min_offer_price > 0 AND min_mrp > min_offer_price
                ORDER BY min_offer_price ASC
                LIMIT 20";

                $result = $mysqli->query($simple_query);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $offers[] = $row;
                    }
                }
            }

            // Debug: Uncomment the lines below to see offer data structure
            // if ($mysqli->error) {
            //     echo '<div class="alert alert-warning">Database Error: ' . $mysqli->error . '</div>';
            // }
            // echo '<div class="alert alert-info">Found ' . count($offers) . ' offers</div>';
            // if (count($offers) > 0) {
            //     echo '<pre>'; print_r(array_slice($offers, 0, 1)); echo '</pre>';
            // }
            ?>
            
            <?php if (!empty($offers)): ?>
            <!-- Offers Count -->
            <div class="offers-count">
                <h3><?php echo count($offers); ?></h3>
                <p>Amazing offers available now!</p>
            </div>

            <!-- Offers Grid -->
            <div class="container">
                <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; padding: 0 15px; align-items: stretch;">
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
                    </div>

                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="product_details.php?ProductId=<?php echo htmlspecialchars($offer["product_id"]); ?>"
                               style="text-decoration: none; color: inherit;">
                                <?php
                                // Truncate long product names to 50 characters
                                $productName = $offer["ProductName"];
                                if (strlen($productName) > 50) {
                                    $productName = substr($productName, 0, 50) . '...';
                                }
                                echo htmlspecialchars($productName);
                                ?>
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
                            <?php
                            // Ensure price values are numeric and valid
                            $current_price = isset($offer['min_offer_price']) && is_numeric($offer['min_offer_price']) ? floatval($offer['min_offer_price']) : 0;
                            $original_price = isset($offer['min_mrp']) && is_numeric($offer['min_mrp']) ? floatval($offer['min_mrp']) : 0;
                            $discount_percent = isset($offer['discount_percentage']) && is_numeric($offer['discount_percentage']) ? intval($offer['discount_percentage']) : 0;
                            ?>

                            <?php if ($current_price > 0): ?>
                                <span class="price-current">₹<?php echo number_format($current_price, 2); ?></span>
                                <?php if ($original_price > $current_price): ?>
                                    <span class="price-original">₹<?php echo number_format($original_price, 2); ?></span>
                                    <?php if ($discount_percent > 0): ?>
                                        <span class="price-discount"><?php echo $discount_percent; ?>% OFF</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="price-current" style="color: #666;">Price not available</span>
                            <?php endif; ?>
                        </div>

                        <button class="btn-add-cart add-to-cart-session"
                                data-product-id="<?php echo htmlspecialchars($offer['product_id']); ?>"
                                style="background: #305724; border: none; color: white; width: 100%; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
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


    <script>
        // Analytics tracking for offers page
        if (typeof trackPageView === 'function') {
            trackPageView('offers');
        }
        
        // Track offer interactions
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.btn-add-cart')) {
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

    <?php include("components/chat_script.php"); ?>
</body>

</html>

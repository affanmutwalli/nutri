<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$connection = $obj->connection();

// Get combo ID from URL
$combo_id = isset($_GET['combo_id']) ? $_GET['combo_id'] : '';

if (empty($combo_id)) {
    header('Location: combos.php');
    exit;
}

// Get combo details
$stmt = $connection->prepare("SELECT * FROM combo_details_view WHERE combo_id = ?");
$stmt->bind_param("s", $combo_id);
$stmt->execute();
$result = $stmt->get_result();
$combo = $result->fetch_assoc();
$stmt->close();

if (!$combo) {
    header('Location: combos.php');
    exit;
}

// Track combo view
$session_id = session_id();
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

$stmt = $connection->prepare("INSERT INTO combo_analytics (combo_id, action_type, user_session, ip_address, user_agent) VALUES (?, 'view', ?, ?, ?)");
$stmt->bind_param("ssss", $combo_id, $session_id, $ip_address, $user_agent);
$stmt->execute();
$stmt->close();
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($combo['combo_name']); ?> - My Nutrify</title>
    <meta name="description" content="<?php echo htmlspecialchars($combo['combo_description']); ?>" />
    <meta name="keywords" content="combo, offer, discount, <?php echo htmlspecialchars($combo['product1_name'] . ', ' . $combo['product2_name']); ?>" />
    
    <!-- CSS Files -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    
    <style>
        /* Combo-specific styling that matches product_details.php */
        .combo-badge {
            background: linear-gradient(45deg, #ff6b35, #f7931e);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 15px;
        }

        .combo-products-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 20px;
            align-items: center;
            margin: 20px 0;
        }

        .combo-product-mini {
            text-align: center;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 10px;
            background: #f9f9f9;
        }

        .combo-product-mini img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 5px;
            background: #fff;
        }

        .combo-product-mini h6 {
            font-size: 0.9rem;
            margin: 0;
            color: #333;
        }

        .combo-plus-sign {
            font-size: 2rem;
            color: #ec7524;
            font-weight: bold;
        }

        /* Fix Owl Carousel pointer events issue */
        .owl-stage-outer {
            pointer-events: none;
        }

        .owl-stage {
            pointer-events: none;
        }

        .owl-item {
            pointer-events: none;
        }

        .owl-item img {
            pointer-events: auto;
        }

        /* Ensure buttons and interactive elements work */
        .pro-info {
            position: relative;
            z-index: 10;
        }

        .pro-info * {
            pointer-events: auto;
        }

        .btn, button, a, input {
            pointer-events: auto !important;
            position: relative;
            z-index: 100;
        }

        @media (max-width: 768px) {
            .combo-products-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .combo-plus-sign {
                transform: rotate(90deg);
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include('components/header.php'); ?>

    <!-- product info start -->
    <section class="pro-page desktop-margin">
        <div class="container">
            <div class="row">
                <!-- Product Images Section -->
                <div class="col-xl-12 col-lg-12 col-md-12 col-12 pro-image">
                    <div class="row">
                        <!-- Main Image with Slider -->
                        <div class="col-lg-6 col-xl-6 col-md-6 col-12 larg-image">
                            <div class="product-main-slider-container">
                                <div class="owl-carousel product-main-slider">
                                    <!-- Product 1 Image -->
                                    <div class="slider-item">
                                        <div class="long-img" style="border: 1px solid rgba(0, 0, 0, 0.1); border-radius: 10px;">
                                            <img src="cms/images/products/<?php echo htmlspecialchars($combo['product1_image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($combo['product1_name']); ?>" style="width: 100%; height: auto; border-radius: 10px;">
                                        </div>
                                    </div>

                                    <!-- Product 2 Image -->
                                    <div class="slider-item">
                                        <div class="long-img" style="border: 1px solid rgba(0, 0, 0, 0.1); border-radius: 10px;">
                                            <img src="cms/images/products/<?php echo htmlspecialchars($combo['product2_image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($combo['product2_name']); ?>" style="width: 100%; height: auto; border-radius: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs pro-page-slider owl-carousel owl-theme">
                                <li class="nav-item items">
                                    <a class="nav-link active" href="javascript:void(0)" data-slide="0">
                                        <img src="cms/images/products/<?php echo htmlspecialchars($combo['product1_image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($combo['product1_name']); ?>" style="pointer-events: none;">
                                    </a>
                                </li>
                                <li class="nav-item items">
                                    <a class="nav-link" href="javascript:void(0)" data-slide="1">
                                        <img src="cms/images/products/<?php echo htmlspecialchars($combo['product2_image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($combo['product2_name']); ?>" style="pointer-events: none;">
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Product Information Section -->
                        <div class="col-lg-6 col-xl-6 col-md-6 col-12 pro-info">
                            <div class="combo-badge">COMBO OFFER</div>
                            <h4><span><?php echo htmlspecialchars($combo['combo_name']); ?></span></h4>
                            <div class="rating">
                                <i class="fa fa-star d-star"></i>
                                <i class="fa fa-star d-star"></i>
                                <i class="fa fa-star d-star"></i>
                                <i class="fa fa-star d-star"></i>
                                <i class="fa fa-star-o"></i>
                            </div>

                            <div class="pro-availabale">
                                <span class="available">Availability:</span>
                                <span class="pro-instock">In stock</span>
                            </div>

                            <!-- Combo Products Display -->
                            <div class="combo-products-grid">
                                <div class="combo-product-mini">
                                    <img src="cms/images/products/<?php echo htmlspecialchars($combo['product1_image']); ?>" alt="<?php echo htmlspecialchars($combo['product1_name']); ?>">
                                    <h6><?php echo htmlspecialchars($combo['product1_name']); ?></h6>
                                </div>
                                <div class="combo-plus-sign">+</div>
                                <div class="combo-product-mini">
                                    <img src="cms/images/products/<?php echo htmlspecialchars($combo['product2_image']); ?>" alt="<?php echo htmlspecialchars($combo['product2_name']); ?>">
                                    <h6><?php echo htmlspecialchars($combo['product2_name']); ?></h6>
                                </div>
                            </div>

                            <div class="mrp-label">
                                <span>MRP (including all taxes):</span>
                            </div>
                            <div class="pro-price" id="pro-price">
                                <span class="new-price">₹<?php echo number_format($combo['combo_price'], 2); ?> INR</span>
                                <span class="old-price"><del>₹<?php echo number_format($combo['total_price'], 2); ?> INR</del></span>
                                <div class="Discount-Pro-lable">
                                    <span class="Discount-p-discount">₹<?php echo number_format($combo['savings'], 2); ?></span>
                                </div>
                            </div>

                            <div class="product-description-container">
                                <p><?php echo htmlspecialchars($combo['combo_description']); ?></p>
                            </div>

                            <button style="background-color: #ec7524; margin-top: 20px;" type="button" class="btn text-white">
                                <i class="fa fa-gift"></i>
                                <span>Save ₹<?php echo number_format($combo['savings'], 0); ?> with this combo!</span>
                                <i class="fa fa-info-circle"></i>
                            </button>

                            <div class="pro-qty">
                                <span class="qty">Quantity:</span>
                                <div class="plus-minus">
                                    <span>
                                        <a href="javascript:void(0)" class="minus-btn text-black">-</a>
                                        <input type="text" name="name" value="1">
                                        <a href="javascript:void(0)" class="plus-btn text-black">+</a>
                                    </span>
                                </div>
                            </div>

                            <div class="pro-btn">
                                <a href="javascript:void(0);" class="btn btn-style1" onclick="addComboToCart('<?php echo $combo_id; ?>')">
                                    <i class="fa fa-shopping-bag" style="margin-right:8px;"></i>Add Combo to Cart
                                </a>
                                <a href="combo_checkout.php?combo_id=<?php echo $combo_id; ?>" class="btn btn-style1 buy-now-btn">Buy Combo Now</a>
                            </div>

                            <div style="margin-top: 20px;">
                                <a href="product_details.php?ProductId=<?php echo $combo['product1_id']; ?>" class="btn btn-outline-secondary btn-sm" style="margin-right: 10px;">
                                    View <?php echo htmlspecialchars($combo['product1_name']); ?>
                                </a>
                                <a href="product_details.php?ProductId=<?php echo $combo['product2_id']; ?>" class="btn btn-outline-secondary btn-sm">
                                    View <?php echo htmlspecialchars($combo['product2_name']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- product info end -->

    <?php include('components/footer.php'); ?>

    <!-- Include necessary JS files -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/custom.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize product slider
            $('.product-main-slider').owlCarousel({
                items: 1,
                loop: false,
                nav: false,
                dots: false,
                autoplay: false
            });

            // Initialize thumbnail slider
            $('.pro-page-slider').owlCarousel({
                items: 2,
                loop: false,
                nav: true,
                dots: false,
                margin: 10,
                responsive: {
                    0: { items: 2 },
                    768: { items: 2 }
                }
            });

            // Thumbnail click functionality
            $('.pro-page-slider .nav-link').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                var slideIndex = $(this).data('slide');
                $('.product-main-slider').trigger('to.owl.carousel', [slideIndex, 300]);
                $('.pro-page-slider .nav-link').removeClass('active');
                $(this).addClass('active');
            });

            // Quantity controls
            $('.plus-btn').click(function() {
                var input = $(this).siblings('input');
                var currentVal = parseInt(input.val()) || 1;
                input.val(currentVal + 1);
            });

            $('.minus-btn').click(function() {
                var input = $(this).siblings('input');
                var currentVal = parseInt(input.val()) || 1;
                if (currentVal > 1) {
                    input.val(currentVal - 1);
                }
            });
        });

        function addComboToCart(comboId) {
            const quantity = parseInt($('.pro-qty input').val()) || 1;
            const product1Id = <?php echo $combo['product1_id']; ?>;
            const product2Id = <?php echo $combo['product2_id']; ?>;

            // Add first product
            addProductToCart(product1Id, quantity, function() {
                // Add second product after first is added
                addProductToCart(product2Id, quantity, function() {
                    alert('Combo added to cart successfully!');
                    // Update cart count if function exists
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                });
            });
        }

        function addProductToCart(productId, quantity, callback) {
            $.ajax({
                url: 'exe_files/add_to_cart_session.php',
                type: 'POST',
                data: {
                    action: 'add_to_cart',
                    productId: productId,
                    quantity: quantity
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        if (callback) callback();
                    } else {
                        alert('Error adding product to cart: ' + data.message);
                    }
                },
                error: function() {
                    alert('An error occurred while adding to cart');
                }
            });
        }

        // Prevent image dragging
        $('img').on('dragstart', function(e) {
            e.preventDefault();
        });

        // Ensure buttons are clickable
        $('.btn, button, a').on('click', function(e) {
            e.stopPropagation();
        });
    </script>
</body>
</html>

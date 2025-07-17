<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

?>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Combos</title>
    <meta name="description"
        content="Explore our wide range of products, featuring high-quality, organic, and eco-friendly options to meet all your needs." />
    <meta name="keywords"
        content="products, organic, eCommerce, online store, eco-friendly, high-quality, shopping, wide range" />
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
    <style>
    .text-container {
        padding-left: 60px;
        /* Adjust for larger screens */
        padding-right: 20px;
        /* Add some padding on the right for mobile devices */
        margin-left: 60px;
        /* Adjust for larger screens */
        margin-right: 0;
        /* Remove margin on the right for mobile devices */
    }

    @media (max-width: 768px) {
        .text-container {
            padding-left: 20px;
            /* Decrease padding on smaller screens */
            padding-right: 20px;
            /* Make sure it has some padding on mobile */
            margin-left: 0;
            /* Remove the margin-left on mobile */
            margin-right: 0;
            /* Remove margin-right on mobile */
        }
    }

    @media (max-width: 576px) {
        .text-container {
            padding-left: 10px;
            /* Further decrease padding on very small screens */
            padding-right: 10px;
            margin-left: 0;
            /* No margin on mobile */
        }
    }

    .btn-orange {
        background-color: #ff6a00;
        /* Orange background */
        color: white;
        /* White text */
        border: none;
        /* Remove border */
        border-radius: 25px;
        /* Rounded corners */
        padding: 10px 20px;
        /* Padding for button */
        font-size: 16px;
        /* Font size */
        display: flex;
        /* Align icon and text */
        align-items: center;
        /* Center vertically */
        justify-content: center;
        /* Center horizontally */
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin: 10px;
        /* Transition effects */
    }

    .btn-orange:hover {
        background-color: #e65c00;
        /* Darker orange on hover */
        transform: scale(1.05);
        /* Slightly increase the size on hover */
    }

    .btn-orange i {
        margin-right: 8px;
        /* Space between icon and text */
    }

    @media (max-width: 576px) {
        .btn-orange {
            padding: 12px 25px;
            /* Adjust padding for smaller screens */
        }
    }

    .pro-btn .btn {
        display: inline-block;
        width: 100%;
        max-width: 300px;
        padding: 10px 20px;
        font-size: 16px;
        text-align: center;
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

    <!-- grid-list start -->
    <!-- Filter Button (visible on mobile only) -->

    <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <!-- Filter Section for Mobile and Desktop -->

                <!-- Product Section -->
                <div class="col-lg-12 col-md-8 col-12">
                    <!--<div class="grid-list-banner d-none d-md-block"-->
                    <!--    style="background-image: url(image/product_banner.webp);">-->
                    <!--    <div class="d-flex justify-content-end text-white text-right">-->
                    <!--        <div class="" style="padding-left: 350px; margin-left:60px; margin-top:30px;">-->
                                <!--<h4 class="text-white">Bestseller</h4>-->
                                <!--<p>-->
                                <!--    Praesent dapibus, neque id cursus Ucibus, tortor neque egestas augue,-->
                                <!--    eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi,-->
                                <!--    tincidunt quis, facilisis luc...-->
                                <!--</p>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!-- Product Listing -->
                    <div class="grid-list-area">
                        <div class="grid-pro">
                            <ul class="grid-product" id="product-list">
                                <?php 
                                    
                                    // Default query to fetch all products
                                    $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                                    $ParamArray = array();
                                    $Fields = implode(",", $FieldNames);
                                    $query = "SELECT " . $Fields . " FROM product_master where IsCombo = 'Y'";
                                    $all_products = $obj->MysqliSelect1($query, $FieldNames, "", $ParamArray);
                            
                                if (!empty($all_products)) {
                                    foreach ($all_products as $products) {
                                        // Fetch price details
                                        $FieldNamesPrice = array("OfferPrice", "MRP");
                                        $ParamArrayPrice = array($products["ProductId"]);
                                        $FieldsPrice = implode(",", $FieldNamesPrice);
                                        $product_prices = $obj->MysqliSelect1(
                                            "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                            $FieldNamesPrice, 
                                            "i", 
                                            $ParamArrayPrice
                                        );
                            
                                        $lowest_price = PHP_INT_MAX; // Initialize to a very high value
                                        $mrp = PHP_INT_MAX;          // Initialize to a very high value
                                        $savings = 0;                // Default savings
                                        
                                        if (!empty($product_prices)) {
                                            foreach ($product_prices as $product_price) {
                                                $current_offer_price = floatval($product_price["OfferPrice"]);
                                                $current_mrp = floatval($product_price["MRP"]);
                                        
                                                // Ensure both OfferPrice and MRP are greater than 0 and find the lowest values
                                                if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                                                    $lowest_price = $current_offer_price;
                                                }
                                                if ($current_mrp > 0 && $current_mrp < $mrp) {
                                                    $mrp = $current_mrp;
                                                }
                                            }
                                        
                                            // Calculate savings only if valid prices are found
                                            if ($mrp > $lowest_price && $mrp != PHP_INT_MAX && $lowest_price != PHP_INT_MAX) {
                                                $savings = $mrp - $lowest_price;
                                            } else {
                                                $lowest_price = "N/A";
                                                $mrp = "N/A";
                                            }
                                        }
                                        ?>
                                        <li class="grid-items" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-top:15px;">
                                            <!-- Product details -->
                                            <div class="tred-pro">
                                                <div class="tr-pro-img">
                                                    <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                                        <img class="img-fluid" src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>" alt="<?php echo htmlspecialchars($products["ProductName"]); ?>">
                                                    </a>
                                                </div>
                                              
                                            </div>
                                            <div class="caption">
                                                <h3>
                                                    <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>"><?php echo htmlspecialchars($products["ProductName"]); ?></a>
                                                </h3>
                                                <div class="pro-price">
                                                    <span class="new-price">from ₹<?php echo htmlspecialchars($lowest_price); ?></span>
                                                    <?php if ($mrp != "N/A"): ?>
                                                        <span class="old-price" style="text-decoration: line-through; color: #999; margin:5px;">₹<?php echo htmlspecialchars($mrp); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="pro-btn text-center" style="margin: 5px;">
                                                        <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="<?php echo htmlspecialchars($products['ProductId']); ?>">
                                                            <i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Add to Cart
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                } else {
                                    echo '<li class="grid-items no-products"><p>No products found.</p></li>';
                                }
                                ?>
                            </ul>

                        </div>
                    </div>
                    <!--<div class="list-all-page">-->
                    <!--    <span class="page-title">Showing 1 - 17 of 17 result</span>-->
                    <!--    <div class="page-number">-->
                    <!--        <a href="grid-list.html" class="active">1</a>-->
                    <!--        <a href="grid-list-2.html">2</a>-->
                    <!--        <a href="grid-list-3.html">3</a>-->
                    <!--        <a href="grid-list-4.html">4</a>-->
                    <!--        <a href="javascript:void(0)"><i class="fa fa-angle-double-right"></i></a>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </section>

    <!-- grid-list start -->
    <!-- quick veiw start -->
    <section class="quick-view">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Product quickview</h5>
                    <a href="javascript:void(0)" data-bs-dismiss="modal" aria-label="Close"><i class="ion-close-round"></i></a>
                </div>
                <div class="quick-veiw-area">
                    <div class="quick-image">
                        <div class="tab-content" id="product-images">
                            <!-- Dynamic Images will be loaded here -->
                        </div>
                        <ul class="nav nav-tabs quick-slider owl-carousel owl-theme" id="product-thumbnails">
                            <!-- Dynamic Thumbnails will be loaded here -->
                        </ul>
                    </div>
                    <div class="quick-caption">
                        <h4 id="product-title">Product Title</h4>
                        <div class="quick-price">
                            <span class="new-price" id="product-price">$350.00 USD</span>
                            <span class="old-price" id="product-old-price"><del>$399.99 USD</del></span>
                        </div>
                        <div class="quick-rating">
                            <i class="fa fa-star c-star"></i>
                            <i class="fa fa-star c-star"></i>
                            <i class="fa fa-star c-star"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </div>
                        <div class="pro-description" id="product-description">
                            <p>Loading description...</p>
                        </div>
                        <div class="pro-size">
                            <label>Size: </label>
                            <select id="product-size">
                                <option>1 ltr</option>
                                <option>3 ltr</option>
                                <option>5 ltr</option>
                            </select>
                        </div>
                        <div class="plus-minus">
                            <span>
                                <a href="javascript:void(0)" class="minus-btn text-black">-</a>
                                <input type="text" name="name" value="1">
                                <a href="javascript:void(0)" class="plus-btn text-black">+</a>
                            </span>
                            <a href="cart.html" class="quick-cart"><i class="fa fa-shopping-bag"></i></a>
                            <a href="wishlist.html" class="quick-wishlist"><i class="fa fa-heart"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- quick veiw end -->
    <?php include("components/footer.php"); ?>
    <!-- footer copyright end -->
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
    <!-- price range -->
    <script src="js/range-slider.js"></script>
    <!-- custom -->
    <script src="js/custom.js"></script>

    
    <script>
       $(document).ready(function () {
            // Add product to cart for logged-in users
            $('.add-to-cart').on('click', function () {
                var productId = $(this).data('product-id'); // Get product ID from data attribute

                $.ajax({
                    url: 'add_to_cart.php', // PHP file to handle the cart addition
                    type: 'POST',
                    data: {
                        action: 'add_to_cart',
                        productId: productId
                    },
                    success: function (response) {
                        try {
                            var data = JSON.parse(response);
                            if (data.status === 'success') {
                                // Show the added to cart popup
                                $('#cart-popup').fadeIn();

                                // Automatically hide popup after a few seconds
                                setTimeout(function () {
                                    $('#cart-popup').fadeOut(function () {
                                        location.reload(); // Reload the page after popup is hidden
                                    });
                                }, 3000);
                            } else {
                                alert(data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error('AJAX error:', status, error);
                    }
                });
            });

            // Add to cart for non-logged-in users (session-based cart)
            $(document).on('click', '.add-to-cart-session', function () { // Use event delegation
                var productId = $(this).data('product-id'); // Get product ID
                console.log('Product ID:', productId); // Debugging: Log the product ID

                $.ajax({
                    url: 'exe_files/add_to_cart_session.php', // PHP file to handle the cart addition in session
                    type: 'POST',
                    data: {
                        action: 'add_to_cart',
                        productId: productId
                    },
                    success: function (response) {
                        try {
                            var data = JSON.parse(response); // Parse the response
                            if (data.status === 'success') {
                                // Show the added to cart popup
                                $('#cart-popup').fadeIn();

                                // Automatically hide popup after a few seconds
                                setTimeout(function () {
                                    $('#cart-popup').fadeOut(function () {
                                        location.reload(); // Reload the page after popup is hidden
                                    });
                                }, 3000);
                            } else {
                                alert(data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e); // Log error if JSON parsing fails
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });

function removeFromCart(productId) {
            // Send an AJAX request to remove the product
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'remove-from-cart.php?productId=' + productId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // If successful, remove the product from the cart display
                    var cartItem = document.getElementById('cart-item-' + productId);
                    if (cartItem) {
                        cartItem.remove(); // Remove the product item from the DOM
                        location.reload();
                    }

                    // Optionally, update the cart summary or notify the user
                    alert('Product removed from cart');
                } else {
                    alert('Failed to remove product from cart');
                }
            };
            xhr.send();
        }

        const modal = document.getElementById('modal');
        const modalVideo = document.getElementById('modal-video');
        const modalTitle = document.getElementById('modal-title');
        const modalPrice = document.getElementById('modal-price');

        function showModal(title, price, oldPrice, videoSrc) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            modalVideo.src = videoSrc;
            modalTitle.textContent = title;
            modalPrice.innerHTML = `${price} <del>${oldPrice}</del>`;
        }
        function toggleMute() {
            const video = document.getElementById('modal-video');
            const muteIcon = document.getElementById('mute-unmute');
            
            if (video.muted) {
                video.muted = false;
                muteIcon.classList.remove('fa-volume-mute');
                muteIcon.classList.add('fa-volume-up');
            } else {
                video.muted = true;
                muteIcon.classList.remove('fa-volume-up');
                muteIcon.classList.add('fa-volume-mute');
            }
        }
        function closeModal(event) {
    if (event.target === document.getElementById('modal') || event.target.id === 'close-modal') {
        document.getElementById('modal').style.display = 'none';
    }
}

        function closeModal(event) {
            // Close modal only if clicked outside the content
            if (event.target === modal) {
                modal.classList.remove('active');
                modalVideo.pause();
                modalVideo.src = ''; // Stop the video
            }
        }

        const scrollableCards = document.querySelector('.scrollable-cards');

        function scrollLeft() {
            scrollableCards.scrollBy({ left: -320, behavior: 'smooth' });
        }

        function scrollRight() {
            scrollableCards.scrollBy({ left: 320, behavior: 'smooth' });
        }
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
 <!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// Auto-cleanup invalid cart items to prevent sync issues
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $cleanCart = array();
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        // Check if product exists
        $FieldNames = array("ProductId");
        $ParamArray = array($productId);
        $Fields = implode(",", $FieldNames);

        $product_data = $obj->MysqliSelect1(
            "SELECT $Fields FROM product_master WHERE ProductId = ?",
            $FieldNames,
            "i",
            $ParamArray
        );

        // Only keep valid products that exist in database
        if ($product_data && isset($product_data[0])) {
            $cleanCart[$productId] = $quantity;
        }
    }

    // Update cart session with only valid items
    $_SESSION['cart'] = $cleanCart;
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>My Nutrify - Your Destination for Health & Wellness</title>
    <meta name="description" content="MyNutrify offers a wide range of organic and Ayurveda products for your health and wellness. Explore a variety of natural products to nourish your body and mind."/>
    <meta name="keywords" content="organic food, health products, Ayurveda, natural supplements, wellness, herbal products, nutrition, healthy living"/>
    <meta name="author" content="MyNutrify">

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
    <!-- top notificationbar start -->
    
    <!-- header start -->
       <?php include("components/header.php"); ?>
        <!-- header end -->
        
    <!-- cart start -->
    <section class="cart-page section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col-xl-9 col-xs-12 col-sm-12 col-md-12 col-lg-8">
                        <div class="cart-area">
                            <div class="cart-details">
                                <div class="cart-item">
                                    <span class="cart-head">My cart:</span>
                                    <span class="c-items"><?php
                                                                // Display the total count of unique products in the cart
                                                                echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                                                                ?> item</span>
                                </div>
                                <?php 
                                $subtotal = 0;
                                $saving_price = 0; // Initialize subtotal to 0
                                if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                    // Loop through each product in the cart
                                    foreach ($_SESSION['cart'] as $productId => $quantity) {
                                        // Ensure the productId is valid
                                        $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId");
                                        $ParamArray = array($productId); // Use the productId from the cart
                                        $Fields = implode(",", $FieldNames);

                                        // Execute the query for the product details
                                        $product_data = $obj->MysqliSelect1(
                                            "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
                                            $FieldNames,
                                            "i",
                                            $ParamArray
                                        );

                                        // Fetch price details for the product
                                        $FieldNamesPrice = array("OfferPrice", "MRP","Size");
                                        $ParamArrayPrice = array($productId);
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
                                                $current_size = $product_price["Size"];
                                               // Check for the lowest offer price and MRP
                                                if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                                                    $lowest_price = $current_offer_price;
                                                    $selected_size = $current_size; // Update the size when a new lowest offer price is found
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

                                        // Check if product data is fetched
                                        if ($product_data) {
                                            // Calculate price for the product and add to the subtotal
                                            if ($lowest_price !== "N/A") {
                                                $price = $lowest_price * $quantity;
                                                $subtotal += $price;
                                                $mrp_price = $mrp - $lowest_price;
                                                $mrp_price = $mrp_price * $quantity;
                                                $saving_price += $mrp_price;  // Add this product's total to the subtotal
                                            }
                                    ?>
                                <div class="cart-all-pro">
                                    <div class="cart-pro">
                                        <div class="cart-pro-image">
                                            <a href="product.html">
                                                <img src="cms/images/products/<?php echo $product_data[0]['PhotoPath']; ?>" class="img-fluid" alt="image" width="350" height="550">
                                            </a>

                                        </div>
                                        <div class="pro-details">
                                            <h4><a href="product_details.php?ProductId=<?php echo $product_data[0]['ProductId']; ?>"><?php echo $product_data[0]['ProductName']; ?></a></h4>
                                            <span class="pro-size"><span class="size">Size:</span> <?php echo $selected_size ?></span>
                                            <span class="pro-size"><span class="size">Quantity:</span> <?php echo $quantity ?></span>
                                            <!--<span class="pro-shop" style="margin-bottom:12px;"><?php echo $product_data[0]['ShortDescription']; ?></span>-->
                                            <span class="cart-pro-price"><b> </b></span>
                                        </div>
                                    </div>
                                    <div class="qty-item">
                                        <div class="center">
                                            <!--<div class="plus-minus">-->
                                            <!--    <span>-->
                                            <!--        <a href="javascript:void(0)" class="minus-btn text-black">-</a>-->
                                            <!--        <input type="text" name="name" value="<?php echo $quantity; ?>">-->
                                            <!--        <a href="javascript:void(0)" class="plus-btn text-black">+</a>-->
                                            <!--    </span>-->
                                            <!--</div>-->
                                            <a href="javascript:void(0)" class="pro-remove" onclick="removeFromCart(<?php echo htmlspecialchars($productId); ?>)">Remove</a>
                                        </div>
                                    </div>
                                    <div class="all-pro-price">
                                        <span>₹ <?php echo $price; ?> INR</span>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        echo '<p>Cart is Empty</p>';
                    }
                    ?>
                                <div class="cart-details">
                                <div class="other-link">
                                    <ul class="c-link">
                                        <li class="side-wrap cart-wrap">
                                            <div class="shopping-widget">
                                                <div class="shopping-cart">
                                                    <a href="javascript:void(0)" class="cart-count">
                                                        <span class="btn btn-style1">
                                                            Update Cart
                                                            
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="cart-other-link"><a href="javascript:void(0)" class="btn btn-style1" id="continue-shopping-btn">Continue Shopping</a></li>       
                                        <!--<li class="cart-other-link"><a href="index1.html" class="btn btn-style1">Clear cart</a></li>-->
                                         <li class="delete-item-cart">
    <a href="javascript:void(0);" onclick="clearCart()" class="btn btn-style1">
        Clear cart
    </a>
</li>



                                    </ul>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-xs-12 col-sm-12 col-md-12 col-lg-4">
                        <div class="cart-total">
                            <?php
                                // Define delivery charges and free delivery threshold
                                $delivery_charges = 80;
                                $free_delivery_threshold = 399;

                                // Calculate final total with delivery charges logic
                                if ($subtotal < $free_delivery_threshold) {
                                    $final_total = $subtotal + $delivery_charges;
                                    $delivery_message = "₹{$delivery_charges} INR";
                                } else {
                                    $final_total = $subtotal;
                                    $delivery_message = "Free Delivery";
                                }
                                ?>

                                <div class="shop-total">
                                    <span>Subtotal</span>
                                    <span class="total-amount">₹ <?php echo $subtotal; ?> INR</span>
                                </div>

                                <div style="margin-top:15px;" class="delivery-info">
                                    <span>Delivery Charges:</span>
                                    <span class="total-amount" style="float: right;"><b><?php echo $delivery_message; ?></b></span>
                                </div>

                                <div class="shop-total">
                                    <span>Total</span>
                                    <span class="total-amount">₹ <?php echo $final_total; ?> INR</span>
                                </div>

                            <a href="checkout.php" id="checkout_btn" class="check-link btn btn-style1">Checkout</a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
        <!-- cart end -->
    <!-- footer start -->
    <?php include("components/footer.php"); ?>
    
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
        <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>


<script>
    document.getElementById('continue-shopping-btn').onclick = function () {
        // Redirect to the products page
        window.location.href = "products.php";
        
    };
    
        document.getElementById('checkout_btn').onclick = function () {
        // Check if user is logged in before proceeding to checkout
        fetch("check_session.php")
            .then(response => response.json())
            .then(data => {
                if (data.loggedIn) {
                    // User is logged in, proceed to checkout
                    window.location.href = "checkout.php";
                } else {
                    // User is not logged in, redirect to login
                    alert("Please login to proceed with checkout.");
                    window.location.href = "login.php";
                }
            })
            .catch(error => {
                console.error("Error checking session:", error);
                // Fallback: redirect to login
                window.location.href = "login.php";
            });
    };
    
    function clearCart() {
    if (!confirm("Are you sure you want to clear your entire cart?")) {
        return; // Exit if the user cancels the action
    }

    // Send an AJAX request to clear the cart
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'exe_files/clear_cart.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Clear the cart display
                    const cartContainer = document.getElementById('cart-container');
                    if (cartContainer) {
                        cartContainer.innerHTML = '<p>Your cart is empty.</p>';
                    }

                    // Update the cart summary
                    const cartSummary = document.getElementById('cart-summary');
                    if (cartSummary) {
                        cartSummary.innerText = '0 Items'; // Update the summary
                    }

                    alert(response.message || 'Cart has been cleared.');
                    location.reload(); // Optionally reload to update other UI elements
                } else {
                    alert(response.message || 'Failed to clear the cart.');
                }
            } catch (e) {
                console.error('Error parsing server response:', e);
                alert('An error occurred while processing the response.');
            }
        } else {
            alert('Failed to clear the cart. Please try again later.');
        }
    };
    xhr.onerror = function () {
        alert('An error occurred while connecting to the server.');
    };
    xhr.send();
}


</script>
    <script>
       // JavaScript (using jQuery for simplicity)
        $(document).ready(function () {
            // Toggle password visibility
            $('#togglePassword').on('click', function () {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                // Toggle Font Awesome icon class
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            $('.btn-style1').on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Get form data
                const email = $('input[name="email"]').val().trim();
                const password = $('input[name="password"]').val().trim();

                // Validate email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address.',
                        confirmButtonColor: '#ec6504',
                    });
                    return;
                }

                if (password === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty Password',
                        text: 'Password cannot be empty.',
                        confirmButtonColor: '#ec6504',
                    });
                    return;
                }

                // AJAX request
                $.ajax({
                    url: 'exe_files/exe_login.php', // Replace with your backend URL
                    type: 'POST',
                    data: {
                        email: email,
                        password: password
                    },
                    enctype: 'application/x-www-form-urlencoded', // Specify the enctype
                    success: function (response) {
                        // Handle the response
                        try {
                            const res = JSON.parse(response);
                            if (res.response === "S") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Login Successful',
                                    text: res.msg,
                                    confirmButtonColor: '#ec6504',
                                }).then(() => {
                                    window.location.href = 'index.php'; // Redirect to dashboard or another page
                                });
                            } else if (res.response === "E") {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Login Failed',
                                    text: res.msg,
                                    confirmButtonColor: '#ec6504',
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Unexpected Error',
                                    text: 'Unexpected response from server.',
                                    confirmButtonColor: '#ec6504',
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Parsing Error',
                                text: 'Error parsing server response. Please try again.',
                                confirmButtonColor: '#ec6504',
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'An error occurred. Please try again.',
                            confirmButtonColor: '#ec6504',
                        });
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
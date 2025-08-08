<!DOCTYPE html>
<html lang="en">
<?php
session_start();
// PHANTOM PRODUCT MONITOR - INJECTED
if (file_exists(__DIR__ . "/cart_monitor.php")) {
    include_once __DIR__ . "/cart_monitor.php";
    if (class_exists("CartMonitor")) {
        $phantomMonitor = new CartMonitor();
        $phantomMonitor->log("📍 CHECKPOINT: " . basename(__FILE__));
        $phantomMonitor->checkForPhantomProducts();
    }
}

include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// Determine if user is logged in or guest
$isLoggedIn = isset($_SESSION["CustomerId"]) && !empty($_SESSION["CustomerId"]);
$isGuest = !$isLoggedIn;

// Initialize customer and address data
$customerData = [];
$addressData = [];

if ($isLoggedIn) {
    // Get customer data for logged-in users
    $FieldNames = array("CustomerId", "Name", "Email", "MobileNo", "IsActive");
    $ParamArray = [$_SESSION["CustomerId"]];
    $Fields = implode(",", $FieldNames);

    $customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);

    $FieldNames = array("CustomerId", "Address", "State", "City","PinCode","Landmark");
    $ParamArray = [$_SESSION["CustomerId"]];
    $Fields = implode(",", $FieldNames);

    // Get address data for registered users
    $addressData = $obj->MysqliSelect1("SELECT $Fields FROM customer_address WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);
}

?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>My Nutrify - Your Destination for Health & Wellness</title>
    <meta name="description"
        content="MyNutrify offers a wide range of organic and Ayurveda products for your health and wellness. Explore a variety of natural products to nourish your body and mind." />
    <meta name="keywords"
        content="organic food, health products, Ayurveda, natural supplements, wellness, herbal products, nutrition, healthy living" />
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
    <!-- full width override -->
    <link rel="stylesheet" type="text/css" href="css/full-width-override.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
    .payment-method {
        margin-top: 20px;
        padding: 15px;
        background: #f8f8f8;
        border-radius: 8px;
    }

    .payment-option {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .payment-option input {
        transform: scale(1.3);
    }

    .payment-icons {
        display: flex;
        gap: 10px;
        margin-left: 20px;
    }

    .pay-icon {
        width: 40px;
        height: 20px;
    }

    /* Checkout Type Selector Styles */
    .checkout-type-selector {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .checkout-options {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .checkout-option {
        flex: 1;
        min-width: 250px;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
        overflow: hidden;
    }

    .checkout-option:hover {
        border-color: #ec6504;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(236, 101, 4, 0.1);
    }

    .checkout-option.active {
        border-color: #ec6504;
        background: linear-gradient(135deg, #fff 0%, #fff5f0 100%);
        box-shadow: 0 5px 15px rgba(236, 101, 4, 0.15);
    }

    .checkout-option.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #ec6504;
    }

    .option-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #ec6504, #ff8c00);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .checkout-option:not(.active) .option-icon {
        background: #6c757d;
    }

    .option-content {
        flex: 1;
    }

    .option-content h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .option-content p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    .option-check {
        width: 24px;
        height: 24px;
        border: 2px solid #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        transition: all 0.3s ease;
    }

    .checkout-option.active .option-check {
        background: #ec6504;
        border-color: #ec6504;
    }

    /* Guest checkout form styles */
    .guest-checkout-notice {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border: 1px solid #2196f3;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .guest-checkout-notice i {
        color: #2196f3;
        font-size: 18px;
    }

    .guest-checkout-notice p {
        margin: 0;
        color: #1976d2;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .checkout-options {
            flex-direction: column;
        }

        .checkout-option {
            min-width: 100%;
        }
    }

    /* Enhanced Coupon Dropdown Styles */
    .coupon-item {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .coupon-item:hover {
        border-color: #ec6504;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(236, 101, 4, 0.1);
    }

    .coupon-item.shining {
        background: linear-gradient(135deg, #fff5e6 0%, #ffe0b3 100%);
        border-color: #ff9500;
        animation: couponShine 2s ease-in-out infinite;
        box-shadow: 0 0 15px rgba(255, 149, 0, 0.3);
    }

    .coupon-item.shining::before {
        content: '✨';
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 16px;
        animation: sparkle 1.5s ease-in-out infinite;
    }

    .coupon-item.shining .coupon-badge {
        background: linear-gradient(135deg, #ff9500, #ffb84d) !important;
        color: white !important;
        font-weight: bold;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes couponShine {
        0%, 100% { box-shadow: 0 0 15px rgba(255, 149, 0, 0.3); }
        50% { box-shadow: 0 0 25px rgba(255, 149, 0, 0.5); }
    }

    @keyframes sparkle {
        0%, 100% { transform: scale(1) rotate(0deg); opacity: 1; }
        50% { transform: scale(1.2) rotate(180deg); opacity: 0.8; }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .coupon-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .coupon-code {
        font-weight: bold;
        color: #333;
        font-size: 14px;
        font-family: monospace;
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px dashed #ccc;
    }

    .coupon-badge {
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
    }

    .coupon-description {
        color: #666;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .coupon-savings {
        color: #28a745;
        font-weight: bold;
        font-size: 13px;
    }

    .coupon-minimum {
        color: #999;
        font-size: 11px;
        margin-top: 5px;
    }

    .no-coupons-message {
        text-align: center;
        padding: 30px 20px;
        color: #666;
    }

    .no-coupons-message i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
    }

    </style>

    <!-- Tawk.to Integration -->
    <?php include("components/tawk-to.php"); ?>
</head>

<body class="home-1">
    <!-- top notificationbar start -->
    
    <!-- header start -->
    <?php include("components/header.php"); ?>
    <!-- header end -->
    <!-- Full-screen black transparent overlay with loader -->
    <div id="overlay"
        style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 9999; text-align: center; padding-top: 20%;">
        <div id="loader">
            <img src="image/Spinner.gif" alt="Loading..." /> <!-- Replace with your loader image -->
            <p style="color: white; font-size: 20px;">Loading payment options...</p>
        </div>
    </div>
    <!-- cart start -->
    <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="checkout-area">
                        <div class="billing-area">
                            <form>
                                <h2>Billing & Shipping details</h2>

                                <!-- Checkout Type Selector -->
                                <div class="checkout-type-selector" style="margin-bottom: 30px;">
                                    <div class="checkout-options">
                                        <?php if ($isLoggedIn): ?>
                                            <div class="checkout-option active" data-type="registered">
                                                <div class="option-icon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <div class="option-content">
                                                    <h4>Continue as <?php echo htmlspecialchars($customerData[0]['Name'] ?? 'Registered User'); ?></h4>
                                                    <p>Your details are pre-filled</p>
                                                </div>
                                                <div class="option-check">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                            </div>
                                            <div class="checkout-option" data-type="guest">
                                                <div class="option-icon">
                                                    <i class="fa fa-user-o"></i>
                                                </div>
                                                <div class="option-content">
                                                    <h4>Checkout as Guest</h4>
                                                    <p>No account required</p>
                                                </div>
                                                <div class="option-check">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="checkout-option active" data-type="guest">
                                                <div class="option-icon">
                                                    <i class="fa fa-user-o"></i>
                                                </div>
                                                <div class="option-content">
                                                    <h4>Guest Checkout</h4>
                                                    <p>Quick and easy checkout</p>
                                                </div>
                                                <div class="option-check">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                            </div>
                                            <div class="checkout-option" data-type="login">
                                                <div class="option-icon">
                                                    <i class="fa fa-sign-in"></i>
                                                </div>
                                                <div class="option-content">
                                                    <h4>Login to Account</h4>
                                                    <p>Access saved addresses</p>
                                                </div>
                                                <div class="option-check">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="billing-form">
                                    <!-- Guest Checkout Notice -->
                                    <div class="guest-checkout-notice" id="guest-notice" style="display: <?php echo $isGuest ? 'flex' : 'none'; ?>;">
                                        <i class="fa fa-info-circle"></i>
                                        <p>You're checking out as a guest. Your order details will be sent to your email.</p>
                                    </div>

                                    <!-- Registered User Notice -->
                                    <?php if ($isLoggedIn): ?>
                                    <div class="guest-checkout-notice" id="registered-notice" style="background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%); border-color: #4caf50; display: flex;">
                                        <i class="fa fa-check-circle" style="color: #4caf50;"></i>
                                        <p style="color: #2e7d32;">Welcome back! Your saved details are pre-filled below.</p>
                                    </div>
                                    <?php endif; ?>

                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Full Name *</label>
                                            <input type="text" name="Name" placeholder="Enter your full name" required
                                                value="<?php echo !empty($customerData[0]['Name']) ? htmlspecialchars($customerData[0]['Name']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul input-2">
                                        <li class="billing-li">
                                            <label>Email address *</label>
                                            <input type="email" name="mail" placeholder="Enter your email address" required
                                                value="<?php echo !empty($customerData[0]['Email']) ? htmlspecialchars($customerData[0]['Email']) : ''; ?>">
                                            <small style="color: #666; font-size: 12px;">Order confirmation will be sent to this email</small>
                                        </li>
                                        <li class="billing-li">
                                            <label>Phone number *</label>
                                            <input type="tel" name="phone" placeholder="Enter your phone number" required
                                                value="<?php echo !empty($customerData[0]['MobileNo']) ? htmlspecialchars($customerData[0]['MobileNo']) : ''; ?>">
                                            <small style="color: #666; font-size: 12px;">For delivery updates</small>
                                        </li>
                                    </ul>
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Street address *</label>
                                            <input type="text" name="address" placeholder="Enter your complete address" required
                                                value="<?php echo !empty($addressData[0]['Address']) ? htmlspecialchars($addressData[0]['Address']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Landmark</label>
                                            <input type="text" name="landmark" placeholder="Nearby landmark (optional)"
                                                value="<?php echo !empty($addressData[0]['Landmark']) ? htmlspecialchars($addressData[0]['Landmark']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <!-- <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Country</label>
                                            <select>
                                                <option>Select a country</option>
                                                <option>United country</option>
                                                <option>Russia</option>
                                                <option>italy</option>
                                                <option>France</option>
                                                <option>Ukraine</option>
                                                <option>Germany</option>
                                                <option>Australia</option>
                                            </select>
                                        </li>
                                    </ul> -->
                                    <ul class="billing-ul input-2">
                                        <li class="billing-li">
                                            <label>State *</label>
                                            <input type="text" name="state" id="state" placeholder="Enter state" required
                                                value="<?php echo !empty($addressData[0]['State']) ? htmlspecialchars($addressData[0]['State']) : ''; ?>">
                                        </li>
                                        <li class="billing-li">
                                            <label>City *</label>
                                            <input type="text" name="city" id="city" placeholder="Enter city" required
                                                value="<?php echo !empty($addressData[0]['City']) ? htmlspecialchars($addressData[0]['City']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Pin code *</label>
                                            <input type="text" name="pincode" id="pincode" placeholder="Enter 6-digit PIN code" required
                                                pattern="[0-9]{6}" maxlength="6"
                                                value="<?php echo !empty($addressData[0]['PinCode']) ? htmlspecialchars($addressData[0]['PinCode']) : ''; ?>">
                                            <small style="color: #666; font-size: 12px;">City and state will be auto-filled</small>
                                        </li>
                                    </ul>

                                </div>
                            </form>
                        </div>
                        <div class="order-area">
                            <div class="check-pro">
                                <h2>In your cart</h2>
                                <ul class="check-ul">
                                    <?php
                                $subtotal = 0;
                                $saving_price = 0; // Initialize subtotal to 0

                                // Check if this is a "Buy Now" checkout or regular cart checkout
                                $isBuyNow = isset($_SESSION['buy_now']) && !empty($_SESSION['buy_now']);

                                if ($isBuyNow) {
                                    // Handle Buy Now checkout - single product
                                    $buyNowData = $_SESSION['buy_now'];
                                    $productId = $buyNowData['product_id'];
                                    $quantity = $buyNowData['quantity'];
                                    $selectedSize = $buyNowData['size'];
                                    $offerPrice = $buyNowData['offer_price'];
                                    $mrp = $buyNowData['mrp'];

                                    // Get product details
                                    $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId");
                                    $ParamArray = array($productId);
                                    $Fields = implode(",", $FieldNames);

                                    $product_data = $obj->MysqliSelect1(
                                        "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
                                        $FieldNames,
                                        "i",
                                        $ParamArray
                                    );

                                    if ($product_data) {
                                        // Calculate price for Buy Now product
                                        $price = $offerPrice * $quantity;
                                        $subtotal += $price;
                                        $mrp_price = ($mrp - $offerPrice) * $quantity;
                                        $saving_price += $mrp_price;
                                        ?>
                                        <li class="checkout-item">
                                            <input type="hidden" name="product_id" value="<?php echo $product_data[0]['ProductId']; ?>">
                                            <div class="check-pro-img">
                                                <a href="product.php?id=<?php echo $product_data[0]['ProductId']; ?>">
                                                    <img src="cms/images/products/<?php echo $product_data[0]['PhotoPath']; ?>" class="img-fluid" alt="image">
                                                </a>
                                            </div>
                                            <div class="check-content">
                                                <a href="product.php?id=<?php echo $product_data[0]['ProductId']; ?>" class="product-name">
                                                    <?php echo $product_data[0]['ProductName']; ?>
                                                </a>
                                                
                                                <div class="check-detail">
                                                    <span class="check-size">Size: <?php echo $selectedSize; ?></span>
                                                </div>
                                                <div class="check-detail">
                                                    <span class="check-quantity">Quantity: <?php echo $quantity; ?></span>
                                                </div>
                                                <!-- ✅ Hidden Offer Price -->
                                                <span class="offer-price" data-price="<?php echo $offerPrice; ?>"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                } elseif (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                    // Handle regular cart checkout - multiple products
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
                                        $selected_size = "N/A";     // Initialize selected size
                                        
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
                                    <li class="checkout-item">
                                        <input type="hidden" name="product_id"
                                            value="<?php echo $product_data[0]['ProductId']; ?>">
                                        <div class="check-pro-img">
                                            <a href="product.php?id=<?php echo $product_data[0]['ProductId']; ?>">
                                                <img src="cms/images/products/<?php echo $product_data[0]['PhotoPath']; ?>"
                                                    class="img-fluid" alt="image">
                                            </a>
                                        </div>
                                        <div class="check-content">

                                            <a href="product.php?id=<?php echo $product_data[0]['ProductId']; ?>"
                                                class="product-name">
                                                <?php echo $product_data[0]['ProductName']; ?>
                                            </a>

                                            <div class="check-detail">
                                                <span class="check-size">Size: <?php echo $selected_size; ?></span>
                                            </div>

                                            <div class="check-detail">
                                                <span class="check-quantity">Quantity: <?php echo $quantity; ?></span>
                                            </div>

                                            <!-- ✅ Hidden Offer Price -->
                                            <span class="offer-price" data-price="<?php echo $lowest_price !== 'N/A' ? $lowest_price : '0'; ?>"></span>

                                        </div>
                                    </li>
                                    <?php }
                                        }
                                } else {
                                    // No cart or buy_now data - show empty cart message
                                    echo '<li class="empty-cart-message" style="text-align: center; padding: 40px; color: #666;">';
                                    echo '<div style="font-size: 18px; margin-bottom: 10px;"><i class="fa fa-shopping-cart" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i></div>';
                                    echo '<h4>Your cart is empty</h4>';
                                    echo '<p>Add some products to your cart to proceed with checkout.</p>';
                                    echo '<div style="margin-top: 20px;">';
                                    echo '<a href="index.php" style="color: #ec6504; text-decoration: none; font-weight: bold; margin-right: 20px;">← Continue Shopping</a>';
                                    if ($isGuest) {
                                        echo '<span style="color: #999;">|</span>';
                                        echo '<a href="?test_guest=1" style="color: #28a745; text-decoration: none; font-weight: bold; margin-left: 20px;">Test Guest Checkout</a>';
                                    }
                                    echo '</div>';
                                    echo '</li>';
                                    $subtotal = 0; // Set subtotal to 0 for empty cart

                                    // Add a test product for guest checkout testing
                                    if ($isGuest && isset($_GET['test_guest'])) {
                                        echo '<li class="checkout-item" style="border: 2px dashed #28a745; background: #f8fff8;">';
                                        echo '<input type="hidden" name="product_id" value="999">';
                                        echo '<div class="check-pro-img">';
                                        echo '<img src="image/test-product.jpg" alt="Test Product" style="width: 80px; height: 80px; object-fit: cover; background: #f0f0f0;">';
                                        echo '</div>';
                                        echo '<div class="check-content">';
                                        echo '<a href="#" class="product-name">Test Product (Guest Checkout Demo)</a>';
                                        echo '<div class="check-detail">';
                                        echo '<span class="check-code-blod">Code: <span>TEST001</span></span>';
                                        echo '</div>';
                                        echo '<div class="check-detail">';
                                        echo '<span class="check-size">Size: Medium</span>';
                                        echo '</div>';
                                        echo '<div class="check-detail">';
                                        echo '<span class="check-quantity">Quantity: 1</span>';
                                        echo '</div>';
                                        echo '<span class="offer-price" data-price="99" style="display: none;">₹99</span>';
                                        echo '</div>';
                                        echo '</li>';
                                        $subtotal = 99; // Set test product price
                                    }
                                }?>
                                </ul>
                            </div>
                            <h2>Your order</h2>

                            <?php
                                    // Define delivery charges and free delivery threshold
                                    $delivery_charges = 0;
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

                            <div class="order-summary">
                                <ul class="order-history">
                                    <li class="order-details">
                                        <span>Sub Total: </span>
                                        <span>₹
                                            <?php echo ($mrp > 0) ? '<del>' . $mrp . '</del>' : '-'; ?><b>
                                                ₹ <?php echo $subtotal; ?> INR</b>
                                        </span>
                                    </li>
                                    <li class="order-details">
                                        <span>Delivery Charges:</span>
                                        <span><?php echo $delivery_message; ?></span>
                                    </li>
                                </ul>

                                <!-- Enhanced Coupon Code Section -->
                                <div style="margin-top: 20px;" class="coupon-code-section">
                                    <form action="" class="apply-coupon-form">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <label for="coupon-code">Apply Offer Code</label>
                                            <button type="button" id="show-available-coupons" class="btn" style="background: #ec6504; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                <i class="fa fa-gift"></i> Available Offers
                                            </button>
                                        </div>

                                        <!-- Available Coupons Dropdown -->
                                        <div id="available-coupons-dropdown" style="display: none; margin-bottom: 15px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 15px; max-height: 300px; overflow-y: auto;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                                <h6 style="margin: 0; color: #333;">Available Coupons for Your Order</h6>
                                                <button type="button" id="close-coupons-dropdown" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #666;">&times;</button>
                                            </div>
                                            <div id="coupons-list">
                                                <div style="text-align: center; padding: 20px; color: #666;">
                                                    <i class="fa fa-spinner fa-spin"></i> Loading available coupons...
                                                </div>
                                            </div>
                                        </div>

                                        <div class="coupon-input-container">
                                            <input type="text" name="code" id="coupon-code"
                                                placeholder="Enter offer code or select from available offers" class="coupon-input">
                                            <a href="javascript:void(0)"
                                                class="btn btn-style1 apply-coupon-btn">Apply</a>
                                        </div>
                                        <p id="coupon-response" style="margin-top: 10px; color: red; display: none;">
                                        </p>
                                    </form>
                                </div>



                                <ul class="order-history">
                                    <li class="order-details">
                                        <span>Total</span>
                                        <span>₹ <span id="final-total"><?php echo $final_total; ?></span> INR</span>
                                    </li>
                                </ul>
                                <div class="payment-method">

                                    <h6 style="margin-bottom:10px;">Select Payment Method</h6>

                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="COD" checked>
                                        <span>Cash on Delivery (COD)</span>
                                    </label>

                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="Online">
                                        <span>Online Payment</span>
                                        <div class="payment-icons">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/UPI_logo.svg/200px-UPI_logo.svg.png"
                                                alt="UPI" class="pay-icon">
                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRB6YnQoM78NCEw3f--iWcGhpQFjBxfo9k6fw&s"
                                                alt="Card" class="pay-icon">
                                            <img src="https://pic.onlinewebfonts.com/thumbnails/icons_462182.svg"
                                                alt="Net Banking" class="pay-icon">
                                        </div>
                                    </label>
                                </div>

                                <!-- Place Order Button -->
                                <div class="checkout-btn">
                                    <button type="button" onclick="placeOrder()" class="btn-style1">Place Order</button>
                                </div>

                            </div>
                        </div>
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
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
    // Simple and robust coupon application
    function applyCouponSimple() {
        const couponCode = document.querySelector('#coupon-code').value.trim();
        const responseElement = document.querySelector('#coupon-response');

        if (!couponCode) {
            responseElement.textContent = 'Please enter a coupon code.';
            responseElement.style.color = 'red';
            responseElement.style.display = 'block';
            return;
        }

        // Show loading
        responseElement.textContent = 'Applying coupon...';
        responseElement.style.color = '#007bff';
        responseElement.style.display = 'block';

        // Get order amount
        const finalTotalElement = document.getElementById('final-total');
        const orderAmount = finalTotalElement ? parseFloat(finalTotalElement.innerText.trim()) || 0 : 0;

        // Use XMLHttpRequest for better compatibility
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'exe_files/fetch_coupon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);

                    if (data.response === 'S') {
                        // Success
                        responseElement.textContent = data.msg;
                        responseElement.style.color = 'green';

                        // Apply discount
                        applyDiscountToCart(data.discount, couponCode);

                        // Disable input
                        document.querySelector('#coupon-code').disabled = true;
                        document.querySelector('.apply-coupon-btn').disabled = true;
                        document.querySelector('.apply-coupon-btn').textContent = 'Applied';

                    } else {
                        // Error
                        responseElement.textContent = data.msg;
                        responseElement.style.color = 'red';
                    }
                } catch (e) {
                    responseElement.textContent = 'Invalid response from server';
                    responseElement.style.color = 'red';
                }
            } else {
                responseElement.textContent = 'Server error occurred';
                responseElement.style.color = 'red';
            }
        };

        xhr.onerror = function() {
            responseElement.textContent = 'Network error occurred';
            responseElement.style.color = 'red';
        };

        xhr.send(JSON.stringify({
            code: couponCode,
            order_amount: orderAmount
        }));
    }

    document.querySelector('.apply-coupon-btn').addEventListener('click', function(e) {
        e.preventDefault();
        applyCouponSimple();
    });

    </script>
    <script>
    let orderData = {};
    let isGuestCheckout = <?php echo $isGuest ? 'true' : 'false'; ?>;

    // Initialize checkout type switching and form functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize checkout type selector
        initializeCheckoutTypeSelector();

        // Initialize pincode auto-fill functionality
        const pincodeInput = document.getElementById('pincode');
        if (pincodeInput) {
            pincodeInput.addEventListener('keyup', function () {
                let pincode = this.value.trim();
                if (pincode.length === 6) {
                    fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data[0].Status === "Success") {
                                document.getElementById('city').value = data[0].PostOffice[0].District;
                                document.getElementById('state').value = data[0].PostOffice[0].State;
                            } else {
                                alert("Invalid PIN code. Please enter a valid one.");
                            }
                        })
                        .catch(error => console.error("Error fetching location data:", error));
                }
            });
        }
    });

    // Initialize checkout type selector functionality
    function initializeCheckoutTypeSelector() {
        const checkoutOptions = document.querySelectorAll('.checkout-option');
        const guestNotice = document.getElementById('guest-notice');
        const registeredNotice = document.getElementById('registered-notice');

        checkoutOptions.forEach(option => {
            option.addEventListener('click', function() {
                const type = this.getAttribute('data-type');

                // Remove active class from all options
                checkoutOptions.forEach(opt => opt.classList.remove('active'));

                // Add active class to clicked option
                this.classList.add('active');

                // Handle different checkout types
                if (type === 'guest') {
                    handleGuestCheckout();
                } else if (type === 'registered') {
                    handleRegisteredCheckout();
                } else if (type === 'login') {
                    // Redirect to login page
                    window.location.href = 'login.php?redirect=checkout';
                }
            });
        });
    }

    function handleGuestCheckout() {
        isGuestCheckout = true;
        const guestNotice = document.getElementById('guest-notice');
        const registeredNotice = document.getElementById('registered-notice');

        // Show guest notice, hide registered notice
        if (guestNotice) guestNotice.style.display = 'flex';
        if (registeredNotice) registeredNotice.style.display = 'none';

        // Clear form fields for guest checkout
        clearFormFields();

        console.log('Switched to guest checkout mode');
    }

    function handleRegisteredCheckout() {
        isGuestCheckout = false;
        const guestNotice = document.getElementById('guest-notice');
        const registeredNotice = document.getElementById('registered-notice');

        // Hide guest notice, show registered notice
        if (guestNotice) guestNotice.style.display = 'none';
        if (registeredNotice) registeredNotice.style.display = 'flex';

        // Restore pre-filled data for registered users
        restoreRegisteredUserData();

        console.log('Switched to registered user checkout mode');
    }

    function clearFormFields() {
        // Clear all form fields for guest checkout
        const fields = ['Name', 'mail', 'phone', 'address', 'landmark', 'state', 'city', 'pincode'];
        fields.forEach(fieldName => {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            if (field) field.value = '';
        });
    }

    function restoreRegisteredUserData() {
        // This function would restore the original pre-filled data
        // For now, we'll just reload the page to get the original data back
        // In a more sophisticated implementation, we'd store the original values
        location.reload();
    }

    function placeOrder() {
        console.log("=== placeOrder function started ===");
        console.log("Checkout mode:", isGuestCheckout ? "Guest" : "Registered");

        // Handle CustomerId based on checkout type
        let CustomerId = null;
        if (!isGuestCheckout) {
            CustomerId = <?php echo isset($_SESSION["CustomerId"]) ? $_SESSION["CustomerId"] : 'null'; ?>;
        }
        console.log("CustomerId:", CustomerId);

        // Get form elements with null checks
        let nameElement = document.querySelector('input[name="Name"]');
        let emailElement = document.querySelector('input[name="mail"]');
        let phoneElement = document.querySelector('input[name="phone"]');
        let addressElement = document.querySelector('input[name="address"]');
        let landmarkElement = document.querySelector('input[name="landmark"]');
        let pincodeElement = document.querySelector('input[name="pincode"]');
        let stateElement = document.querySelector('input[name="state"]');
        let cityElement = document.querySelector('input[name="city"]');
        let finalTotalElement = document.getElementById('final-total');

        // Check if all required elements exist
        if (!nameElement || !emailElement || !phoneElement || !addressElement ||
            !pincodeElement || !stateElement || !cityElement || !finalTotalElement) {
            Swal.fire({
                icon: 'error',
                title: 'Form Error',
                text: 'Some form fields are missing. Please refresh the page and try again.',
                confirmButtonColor: '#ec6504'
            });
            return;
        }

        // Extract values safely
        let name = nameElement.value ? nameElement.value.trim() : '';
        let email = emailElement.value ? emailElement.value.trim() : '';
        let phone = phoneElement.value ? phoneElement.value.trim() : '';
        let address = addressElement.value ? addressElement.value.trim() : '';
        let landmark = landmarkElement.value ? landmarkElement.value.trim() : '';
        let pincode = pincodeElement.value ? pincodeElement.value.trim() : '';
        let state = stateElement.value ? stateElement.value.trim() : '';
        let city = cityElement.value ? cityElement.value.trim() : '';
        let final_total = finalTotalElement.innerText ? finalTotalElement.innerText.trim() : '';
        let selectedPaymentMethod = document.querySelector("input[name='payment_method']:checked")?.value;

        // Validate required fields (CustomerId not required for guest checkout)
        let missingFields = [];
        if (!name) missingFields.push('Name');
        if (!email) missingFields.push('Email');
        if (!phone) missingFields.push('Phone');
        if (!address) missingFields.push('Address');
        if (!pincode) missingFields.push('PIN Code');
        if (!state) missingFields.push('State');
        if (!city) missingFields.push('City');
        if (!final_total) missingFields.push('Total Amount');

        // For registered users, CustomerId is required
        if (!isGuestCheckout && !CustomerId) {
            missingFields.push('Customer ID (Please login)');
        }

        if (missingFields.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill out the following required fields: ' + missingFields.join(', '),
                confirmButtonColor: '#ec6504'
            });
            return;
        }

        // Validate payment method selection
        if (!selectedPaymentMethod) {
            Swal.fire({
                icon: 'warning',
                title: 'Payment Method Required',
                text: 'Please select a payment method to proceed.',
                confirmButtonColor: '#ec6504'
            });
            return;
        }

        // Validate CustomerId only for registered users
        if (!isGuestCheckout && (!CustomerId || isNaN(CustomerId))) {
            Swal.fire({
                icon: 'error',
                title: 'Session Error',
                text: 'Your session has expired. Please login again.',
                confirmButtonColor: '#ec6504'
            }).then(() => {
                window.location.href = 'login.php';
            });
            return;
        }

        let products = [];
        let allCheckoutItems = document.querySelectorAll(".checkout-item");

        // Filter out empty cart messages and only get actual product items
        let checkoutItems = Array.from(allCheckoutItems).filter(item => {
            return item.querySelector("input[name='product_id']") !== null;
        });

        console.log("=== PRODUCT EXTRACTION DEBUG ===");
        console.log("All checkout items found:", allCheckoutItems.length);
        console.log("Valid product items found:", checkoutItems.length);
        console.log("Valid checkout items:", checkoutItems);

        // Validate that there are products in the cart
        if (checkoutItems.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Cart',
                text: 'Your cart is empty. Please add some products before placing an order.',
                confirmButtonColor: '#ec6504'
            });
            return;
        }

        checkoutItems.forEach((item, index) => {
            try {
                console.log(`Processing checkout item ${index + 1}:`, item);

                let productIdElement = item.querySelector("input[name='product_id']");
                let productNameElement = item.querySelector(".product-name");
                let productCodeElement = item.querySelector(".check-code-blod span");
                let sizeElement = item.querySelector(".check-size");
                let quantityElement = item.querySelector(".check-quantity");
                let imageElement = item.querySelector(".check-pro-img img");
                let offerPriceElement = item.querySelector(".offer-price");

                console.log(`Item ${index + 1} elements found:`, {
                    productIdElement: !!productIdElement,
                    productNameElement: !!productNameElement,
                    sizeElement: !!sizeElement,
                    quantityElement: !!quantityElement,
                    offerPriceElement: !!offerPriceElement
                });

                // Enhanced validation with detailed logging
                if (!productIdElement) {
                    console.error(`❌ Missing product ID element for item ${index + 1}`);
                    return;
                }

                if (!productNameElement) {
                    console.error(`Missing product name element for item ${index + 1}`);
                    return;
                }

                if (!productIdElement.value) {
                    console.error(`Empty product ID for item ${index + 1}`);
                    return;
                }

                let productId = productIdElement.value;
                let productName = productNameElement.innerText ? productNameElement.innerText.trim() : "";
                let productCode = productCodeElement ? productCodeElement.innerText.trim() : `PROD${productId}`;
                let size = sizeElement ? (sizeElement.innerText.split(": ")[1] || "").trim() : "Standard";
                let quantity = quantityElement ? (quantityElement.innerText.split(": ")[1] || "1").trim() : "1";
                let imagePath = imageElement ? imageElement.src : "";
                let offerPrice = offerPriceElement ? parseFloat(offerPriceElement.getAttribute("data-price")) || 0 : 0;

                console.log(`Extracted data for item ${index + 1}:`, {
                    productId, productName, productCode, size, quantity, imagePath, offerPrice
                });

                // Validate extracted data
                if (!productId || !productName || offerPrice <= 0) {
                    console.error(`❌ Invalid product data for item ${index + 1}:`, {
                        productId, productName, productCode, size, quantity, offerPrice
                    });
                    return;
                }

                const productData = {
                    id: productId,
                    name: productName,
                    code: productCode,
                    size: size,
                    quantity: quantity,
                    image: imagePath,
                    offer_price: offerPrice
                };

                console.log(`✅ Successfully extracted product ${index + 1}:`, productData);
                products.push(productData);
            } catch (error) {
                console.error(`❌ Error processing product ${index + 1}:`, error);
            }
        });

        console.log(`Total products extracted: ${products.length}`);

        // Validate that we have valid products
        if (products.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Cart',
                text: 'Your cart is empty. Please add some products before placing an order.',
                confirmButtonColor: '#ec6504',
                showCancelButton: true,
                cancelButtonText: 'Continue Shopping',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = 'index.php';
                }
            });
            return;
        }

        // PHANTOM_PRODUCT_FILTER: Remove ProductId 6 before sending
        products = products.filter(function(product) {
            if (product.id == 6) {
                console.log("PHANTOM PRODUCT BLOCKED: ProductId 6 removed from order");
                return false;
            }
            return true;
        });

        // Create order data object based on checkout type
        orderData = {
            name: name,
            email: email,
            phone: phone,
            address: address,
            landmark: landmark,
            pincode: pincode,
            state: state,
            city: city,
            final_total: final_total,
            paymentMethod: selectedPaymentMethod,
            products: products,
            customerType: isGuestCheckout ? 'Guest' : 'Registered'
        };

        // Add CustomerId only for registered users
        if (!isGuestCheckout && CustomerId) {
            orderData.CustomerId = CustomerId;
        }

        // Debug logging - log products being sent to prevent phantom products
        console.log("Products being sent in order:", products);
        console.log("Total products count:", products.length);
        console.log("Order data prepared:", orderData);
        console.log("Payment method selected:", selectedPaymentMethod);
        console.log("Products count:", products.length);
        console.log("About to call checkPaymentType()...");

        checkPaymentType();
    }

    function checkPaymentType() {
        console.log("checkPaymentType called, payment method:", orderData.paymentMethod);
        console.log("Customer type:", orderData.customerType);

        if (orderData.paymentMethod === "Online") {
            console.log("Calling initiateRazorpayPayment...");
            initiateRazorpayPayment();
        } else if (orderData.paymentMethod === "COD") {
            if (isGuestCheckout) {
                console.log("Calling sendGuestOrderData...");
                sendGuestOrderData();
            } else {
                console.log("Calling sendOrderData...");
                sendOrderData();
            }
        } else {
            console.error("Unknown payment method:", orderData.paymentMethod);
        }
    }

function sendOrderData() {
    console.log("Sending COD order data:", orderData);

    fetch("exe_files/rcus_place_order_cod.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(orderData)
    })
    .then(response => {
        console.log("Response status:", response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Get as text first to handle non-JSON responses
    })
    .then(responseText => {
        console.log("Raw response:", responseText);

        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error("JSON parse error:", parseError);
            console.error("Response text:", responseText);
            throw new Error("Server returned invalid JSON response: " + responseText.substring(0, 100));
        }

        console.log("Order response data:", data);

        if (data.response === "S") {
            console.log("Order placed successfully:", data);

            // Call WhatsApp sender after order placement
            sendOrderPlacedWhatsappTemplate({
                order_id: data.order_id,
                mobile: data.mobile,       // Mobile number from response
                name: data.name            // Customer name
            });

            // Show points earned popup first (if points were awarded)
            if (data.points_awarded && data.points_awarded > 0) {
                Swal.fire({
                    icon: 'success',
                    title: '🎉 Yay! You Earned Points!',
                    html: `<div style="font-size: 18px; color: #ff8c00; font-weight: bold; margin: 10px 0;">
                              +${data.points_awarded} Points Added!
                           </div>
                           <div style="font-size: 14px; color: #666;">
                              Keep shopping to earn more rewards!
                           </div>`,
                    confirmButtonText: 'Awesome!',
                    confirmButtonColor: '#ff8c00',
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    allowOutsideClick: false
                }).then(() => {
                    // Then show order success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed Successfully!',
                        text: 'Your order has been placed. You will receive a confirmation soon.',
                        confirmButtonColor: '#ec6504',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        // Clear cart display before redirecting
                        clearCartDisplay();
                        window.location.href = "order-placed.php?order_id=" + data.order_id;
                    });
                });
            } else {
                // No points awarded, show regular success message
                Swal.fire({
                    icon: 'success',
                    title: 'Order Placed Successfully!',
                    text: 'Your order has been placed. You will receive a confirmation soon.',
                    confirmButtonColor: '#ec6504',
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    // Clear cart display before redirecting
                    clearCartDisplay();
                    window.location.href = "order-placed.php?order_id=" + data.order_id;
                });
            }

        } else {
            console.error("Order placement failed:", data);
            Swal.fire({
                icon: 'error',
                title: 'Order Failed!',
                text: data.message || "Unknown error occurred.",
                confirmButtonColor: '#ec6504'
            });
        }
    })
    .catch(error => {
        console.error("Order placement error:", error);
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Unable to place order. Please check your connection and try again.',
            confirmButtonColor: '#ec6504'
        });
    });
}

function sendGuestOrderData() {
    console.log("=== SENDING GUEST ORDER DATA ===");
    console.log("Full order data object:", orderData);
    console.log("JSON string being sent:", JSON.stringify(orderData));

    // Validate order data before sending
    const requiredFields = ['name', 'email', 'phone', 'address', 'final_total', 'products'];
    const missingFields = requiredFields.filter(field => !orderData[field] || (field === 'products' && orderData[field].length === 0));

    if (missingFields.length > 0) {
        console.error("❌ Missing required fields before sending:", missingFields);
        Swal.fire({
            icon: 'error',
            title: 'Data Error',
            text: 'Missing required data: ' + missingFields.join(', '),
            confirmButtonColor: '#ec6504'
        });
        return;
    }

    console.log("✅ All required fields present, sending request...");

    fetch("exe_files/rcus_place_order_guest.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(orderData)
    })
    .then(response => {
        console.log("Response status:", response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Get as text first to handle non-JSON responses
    })
    .then(responseText => {
        console.log("Raw response:", responseText);

        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error("JSON parse error:", parseError);
            console.error("Response text:", responseText);
            throw new Error("Server returned invalid JSON response: " + responseText.substring(0, 100));
        }

        console.log("Guest order response data:", data);

        if (data.response === "S") {
            console.log("Guest order placed successfully:", data);

            // Show success message for guest orders
            Swal.fire({
                icon: 'success',
                title: 'Order Placed Successfully!',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <p><strong>Order ID:</strong> ${data.order_id}</p>
                        <p><strong>Total Amount:</strong> ₹${data.amount}</p>
                        <p><strong>Payment Method:</strong> ${data.payment_status === 'Due' ? 'Cash on Delivery' : 'Online Payment'}</p>
                    </div>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <p style="margin: 0; color: #666; font-size: 14px;">
                            <i class="fa fa-info-circle" style="color: #17a2b8;"></i>
                            Order confirmation has been sent to <strong>${orderData.email}</strong>
                        </p>
                    </div>
                `,
                confirmButtonText: 'Continue Shopping',
                confirmButtonColor: '#ec6504',
                showCancelButton: true,
                cancelButtonText: 'Track Order',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Continue shopping - redirect to home page
                    window.location.href = 'index.php';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Track order - redirect to order tracking page
                    window.location.href = `track-order.php?order_id=${data.order_id}`;
                }
            });

        } else {
            console.error("Guest order placement failed:", data);
            Swal.fire({
                icon: 'error',
                title: 'Order Failed!',
                text: data.message || "Unknown error occurred.",
                confirmButtonColor: '#ec6504'
            });
        }
    })
    .catch(error => {
        console.error("Guest order placement error:", error);
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Unable to place order. Please check your connection and try again.',
            confirmButtonColor: '#ec6504'
        });
    });
}

function sendOrderPlacedWhatsappTemplate(orderData) {
    const whatsappData = {
        order_id: orderData.order_id || "",    // Use order_id if available
        mobile: orderData.mobile,              // Map 'mobile'
        customer_name: orderData.name,         // Map 'name'
        delivery_days: "5"                     // Default to 5 days; adjust if needed
    };

    console.log("Sending WhatsApp data:", whatsappData);

    fetch("exe_files/send_order_placed_whatsapp.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(whatsappData)
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response data:", data);
        if (data.response === "S") {
            console.log("Order placed WhatsApp template sent successfully.");
        } else {
            console.error("Error sending WhatsApp message: " + (data.message || "Unknown error"));
        }
    })
    .catch(error => {
        console.error("Error sending WhatsApp template:", error);
    });
}


    function initiateRazorpayPayment() {
        console.log("initiateRazorpayPayment started");
        console.log("Sending order data to backend:", orderData);

        fetch("exe_files/rcus_place_order_online_simple.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                console.log("Backend response:", data); // Debug log
                if (data?.response === "S" && data?.payment_status === "Pending") {
                    const options = {
                        "key": "rzp_live_DJ1mSUEz1DK4De",
                        "amount": data.amount * 100,
                        "currency": "INR",
                        "order_id": data.transaction_id,
                        "name": "My Nutrify",
                        "description": "Order Payment",
                        "image": "https://mynutrify.com/image/main_logo.png",
                        "prefill": {
                            "name": data?.name || "",
                            "email": data?.email || "",
                            "contact": data?.phone || ""
                        },
                        "theme": {
                            "color": "#305724",
                            "logo": "https://mynutrify.com/image/main_logo.png"
                        },
                        "handler": function(response) {
                            fetch("exe_files/razorpay_callback_bulletproof.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        order_db_id: data.order_id,
                                        razorpay_payment_id: response.razorpay_payment_id,
                                        razorpay_order_id: response.razorpay_order_id,
                                        razorpay_signature: response.razorpay_signature
                                    })
                                })
                                .then(res => {
                                    // Check if response is ok
                                    if (!res.ok) {
                                        throw new Error(`HTTP error! status: ${res.status}`);
                                    }

                                    // Get response text first
                                    return res.text();
                                })
                                .then(text => {
                                    console.log("Raw response:", text);

                                    // Try to parse as JSON
                                    try {
                                        return JSON.parse(text);
                                    } catch (e) {
                                        console.error("JSON parse error:", e);
                                        console.error("Response text:", text);
                                        throw new Error("Invalid JSON response from server");
                                    }
                                })
                                .then(result => {
                                    if (result?.status === "success") {
                                        // Show points earned popup first (if points were awarded)
                                        if (result.points_awarded && result.points_awarded > 0) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: '🎉 Yay! You Earned Points!',
                                                html: `<div style="font-size: 18px; color: #ff8c00; font-weight: bold; margin: 10px 0;">
                                                          +${result.points_awarded} Points Added!
                                                       </div>
                                                       <div style="font-size: 14px; color: #666;">
                                                          Keep shopping to earn more rewards!
                                                       </div>`,
                                                confirmButtonText: 'Awesome!',
                                                confirmButtonColor: '#ff8c00',
                                                timer: 4000,
                                                timerProgressBar: true,
                                                showConfirmButton: true,
                                                allowOutsideClick: false
                                            }).then(() => {
                                                // Then show order success message (same as COD)
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Payment Successful!',
                                                    text: 'Your payment has been processed and order confirmed. You will receive a confirmation soon.',
                                                    confirmButtonColor: '#ec6504',
                                                    timer: 3000,
                                                    showConfirmButton: false
                                                }).then(() => {
                                                    // Clear cart display before redirecting
                                                    clearCartDisplay();
                                                    window.location.href = "order-placed.php?order_id=" + data.order_id;
                                                });
                                            });
                                        } else {
                                            // No points awarded, show regular success message
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Payment Successful!',
                                                text: 'Your payment has been processed and order confirmed. You will receive a confirmation soon.',
                                                confirmButtonColor: '#ec6504',
                                                timer: 3000,
                                                showConfirmButton: false
                                            }).then(() => {
                                                // Clear cart display before redirecting
                                                clearCartDisplay();
                                                window.location.href = "order-placed.php?order_id=" + data.order_id;
                                            });
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Payment Verification Failed',
                                            text: "Payment verification failed. Please try again.",
                                            confirmButtonColor: '#ec6504'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error("Verification Error:", error);

                                    let errorMessage = "Error verifying payment.";
                                    if (error.message.includes("Invalid JSON")) {
                                        errorMessage = "Server response error. Please contact support with your payment details.";
                                    } else if (error.message.includes("HTTP error")) {
                                        errorMessage = "Server connection error. Please try again or contact support.";
                                    }

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Payment Verification Error',
                                        text: errorMessage,
                                        confirmButtonColor: '#ec6504',
                                        footer: 'If payment was deducted, please contact customer support.'
                                    });
                                });
                        }
                    };

                    const rzp = new Razorpay(options);
                    rzp.open();
                } else {
                    console.error("Payment initiation failed. Response:", data); // Debug log
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Error',
                        text: data?.message || "Error initiating payment. Please try again.",
                        confirmButtonColor: '#ec6504'
                    });
                }
            })
            .catch(error => {
                console.error("Fetch error in initiateRazorpayPayment:", error);
                console.error("Error details:", {
                    message: error.message,
                    stack: error.stack,
                    name: error.name
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: "Failed to connect to payment service: " + error.message,
                    confirmButtonColor: '#ec6504'
                });
            });
    }

    // Global variables to track original totals
    let originalSubtotal = null;
    let originalFinalTotal = null;
    let appliedDiscount = 0;
    let appliedCouponCode = '';

    // Function to apply discount to cart totals
    function applyDiscountToCart(discount, couponCode) {
        const finalTotalElement = document.getElementById('final-total');

        if (!finalTotalElement) {
            console.error('Final total element not found');
            return;
        }

        // Store original totals if not already stored
        if (originalFinalTotal === null) {
            originalFinalTotal = parseFloat(finalTotalElement.innerText.trim()) || 0;
        }

        // Calculate new total after discount
        const discountAmount = parseFloat(discount) || 0;
        const newTotal = Math.max(0, originalFinalTotal - discountAmount);

        // Update the final total display
        finalTotalElement.innerText = newTotal.toFixed(0);

        // Store applied discount info
        appliedDiscount = discountAmount;
        appliedCouponCode = couponCode;

        // Add or update discount line in order summary
        addDiscountLineToSummary(discountAmount, couponCode);

        console.log(`Discount applied: ₹${discountAmount}, New total: ₹${newTotal}`);
    }

    // Function to remove discount from cart totals
    function removeDiscountFromCart() {
        const finalTotalElement = document.getElementById('final-total');

        if (!finalTotalElement || originalFinalTotal === null) {
            return;
        }

        // Restore original total
        finalTotalElement.innerText = originalFinalTotal.toFixed(0);

        // Remove discount line from summary
        removeDiscountLineFromSummary();

        // Reset discount tracking
        appliedDiscount = 0;
        appliedCouponCode = '';

        console.log(`Discount removed, Total restored to: ₹${originalFinalTotal}`);
    }

    // Function to add discount line to order summary
    function addDiscountLineToSummary(discount, couponCode) {
        // Remove existing discount line if any
        removeDiscountLineFromSummary();

        // Find the order summary list
        const orderSummaryList = document.querySelector('.order-summary .order-history');
        if (!orderSummaryList) {
            console.error('Order summary list not found');
            return;
        }

        // Create discount line
        const discountLine = document.createElement('li');
        discountLine.className = 'order-details coupon-discount';
        discountLine.innerHTML = `
            <span>Coupon Discount (${couponCode})</span>
            <span style="color: #28a745;">-₹${discount.toFixed(2)} INR</span>
        `;

        // Insert before the last item (which should be delivery charges)
        const lastItem = orderSummaryList.lastElementChild;
        if (lastItem) {
            orderSummaryList.insertBefore(discountLine, lastItem);
        } else {
            orderSummaryList.appendChild(discountLine);
        }
    }

    // Function to remove discount line from order summary
    function removeDiscountLineFromSummary() {
        const discountLine = document.querySelector('.coupon-discount');
        if (discountLine) {
            discountLine.remove();
        }
    }

    // Fallback function using XMLHttpRequest
    function tryXMLHttpRequest(couponCode, orderAmount, responseElement) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'exe_files/fetch_coupon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log('XMLHttpRequest status:', xhr.status);
                console.log('XMLHttpRequest response:', xhr.responseText);

                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        console.log('XMLHttpRequest parsed data:', data);

                        if (data.response === 'S') {
                            // Success: Show success message and apply discount
                            responseElement.textContent = data.msg || `Coupon applied successfully! Discount: ₹ ${data.discount}`;
                            responseElement.style.color = 'green';

                            // Apply the discount to the cart totals
                            applyDiscountToCart(data.discount, couponCode);

                            // Disable the coupon input and button
                            const couponInput = document.getElementById('coupon-code');
                            const applyButton = document.querySelector('.apply-coupon-btn');
                            if (couponInput) couponInput.disabled = true;
                            if (applyButton) {
                                applyButton.disabled = true;
                                applyButton.textContent = 'Applied';
                            }

                        } else {
                            // Failure: Show error message
                            responseElement.textContent = data.msg || 'Invalid coupon code.';
                            responseElement.style.color = 'red';

                            // Re-enable the apply button
                            const applyButton = document.querySelector('.apply-coupon-btn');
                            if (applyButton) {
                                applyButton.disabled = false;
                            }
                        }
                        responseElement.style.display = 'block';

                    } catch (e) {
                        console.error('XMLHttpRequest JSON parse error:', e);
                        showFinalError(responseElement, 'Invalid response from server');
                    }
                } else {
                    console.error('XMLHttpRequest failed with status:', xhr.status);
                    showFinalError(responseElement, 'Server error occurred');
                }
            }
        };

        xhr.onerror = function() {
            console.error('XMLHttpRequest network error');
            showFinalError(responseElement, 'Network error occurred');
        };

        const requestData = JSON.stringify({
            code: couponCode,
            order_amount: orderAmount
        });

        console.log('Sending XMLHttpRequest with data:', requestData);
        xhr.send(requestData);
    }

    // Function to show final error when all methods fail
    function showFinalError(responseElement, message) {
        responseElement.textContent = message + '. Please try again later.';
        responseElement.style.color = 'red';
        responseElement.style.display = 'block';

        // Re-enable the apply button
        const applyButton = document.querySelector('.apply-coupon-btn');
        if (applyButton) {
            applyButton.disabled = false;
        }

        // Remove any previously applied discount
        removeDiscountFromCart();
    }

    // Function to clear cart display after successful order
    function clearCartDisplay() {
        try {
            // Clear cart items from display
            const cartContainer = document.querySelector('.checkout-items');
            if (cartContainer) {
                cartContainer.innerHTML = '<p class="text-center text-muted">Your cart is now empty</p>';
            }

            // Update cart summary
            const cartSummary = document.querySelector('.cart-summary');
            if (cartSummary) {
                cartSummary.innerHTML = '<p class="text-center text-muted">Cart cleared</p>';
            }

            // Update any cart count displays
            const cartCounts = document.querySelectorAll('.cart-count, .cart-counter');
            cartCounts.forEach(element => {
                element.textContent = '0';
            });

            // Update total displays
            const totalElements = document.querySelectorAll('.total-amount, .final-total');
            totalElements.forEach(element => {
                element.textContent = '₹0.00';
            });

            console.log('Cart display cleared successfully');
        } catch (error) {
            console.error('Error clearing cart display:', error);
        }
    }

    // Enhanced Coupon Dropdown Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const showCouponsBtn = document.getElementById('show-available-coupons');
        const couponsDropdown = document.getElementById('available-coupons-dropdown');
        const closeCouponsBtn = document.getElementById('close-coupons-dropdown');
        const couponsList = document.getElementById('coupons-list');
        const couponInput = document.getElementById('coupon-code');

        // Show available coupons
        showCouponsBtn.addEventListener('click', function() {
            if (couponsDropdown.style.display === 'none' || !couponsDropdown.style.display) {
                showAvailableCoupons();
                couponsDropdown.style.display = 'block';
                showCouponsBtn.innerHTML = '<i class="fa fa-gift"></i> Hide Offers';
            } else {
                couponsDropdown.style.display = 'none';
                showCouponsBtn.innerHTML = '<i class="fa fa-gift"></i> Available Offers';
            }
        });

        // Close coupons dropdown
        closeCouponsBtn.addEventListener('click', function() {
            couponsDropdown.style.display = 'none';
            showCouponsBtn.innerHTML = '<i class="fa fa-gift"></i> Available Offers';
        });

        function showAvailableCoupons() {
            // Get current order amount
            const finalTotalElement = document.getElementById('final-total');
            const orderAmount = finalTotalElement ? parseFloat(finalTotalElement.innerText.replace(/[^\d.]/g, '')) || 0 : 0;

            console.log("=== COUPON DROPDOWN DEBUG ===");
            console.log("Final total element:", finalTotalElement);
            console.log("Order amount detected:", orderAmount);
            console.log("Final total text:", finalTotalElement ? finalTotalElement.innerText : 'Element not found');

            // Show loading
            couponsList.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #666;">
                    <i class="fa fa-spinner fa-spin"></i> Loading available coupons...
                </div>
            `;

            // Fetch available coupons
            fetch('exe_files/fetch_available_coupons.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_amount: Math.max(orderAmount, 1000) // Use at least 1000 for testing
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Available coupons response:', data);
                displayAvailableCoupons(data.coupons || [], orderAmount);
            })
            .catch(error => {
                console.error('Error fetching coupons:', error);
                couponsList.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #dc3545;">
                        <i class="fa fa-exclamation-triangle"></i><br>
                        Failed to load coupons. Please try again.
                    </div>
                `;
            });
        }

        function displayAvailableCoupons(coupons, orderAmount) {
            if (coupons.length === 0) {
                couponsList.innerHTML = `
                    <div class="no-coupons-message">
                        <i class="fa fa-gift"></i><br>
                        <h6>No coupons available</h6>
                        <p>Add more items to unlock exciting offers!</p>
                    </div>
                `;
                return;
            }

            let couponsHtml = '';
            coupons.forEach(coupon => {
                const shiningClass = coupon.is_shining ? 'shining' : '';
                const badgeClass = coupon.is_shining ? 'coupon-badge' : 'coupon-badge';

                couponsHtml += `
                    <div class="coupon-item ${shiningClass}" onclick="selectCoupon('${coupon.code}')">
                        <div class="coupon-header">
                            <span class="coupon-code">${coupon.code}</span>
                            <span class="${badgeClass}">${coupon.discount_display}</span>
                        </div>
                        <div class="coupon-description">${coupon.description}</div>
                        <div class="coupon-savings">You save: ₹${coupon.potential_discount}</div>
                        <div class="coupon-minimum">Minimum order: ₹${coupon.minimum_order}</div>
                    </div>
                `;
            });

            couponsList.innerHTML = couponsHtml;
        }

        // Global function to select coupon
        window.selectCoupon = function(couponCode) {
            couponInput.value = couponCode;
            couponsDropdown.style.display = 'none';
            showCouponsBtn.innerHTML = '<i class="fa fa-gift"></i> Available Offers';

            // Auto-apply the selected coupon
            document.querySelector('.apply-coupon-btn').click();
        };
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
<?php 
$customerData = '';
// Check if session_id cookie is set and matches the session
if (isset($_COOKIE['session_id']) && isset($_SESSION['session_id']) && $_COOKIE['session_id'] === $_SESSION['session_id']) {
    
    // Validate the session based on IP and UserAgent (if they exist)
    $ipValid = !isset($_SESSION['IP']) || $_SESSION['IP'] === $_SERVER['REMOTE_ADDR'];
    $userAgentValid = !isset($_SESSION['UserAgent']) || $_SESSION['UserAgent'] === $_SERVER['HTTP_USER_AGENT'];

    if ($ipValid && $userAgentValid) {
        
        // Prepare query to fetch customer data
        $FieldNames = array("CustomerId", "Name", "MobileNo", "IsActive","Email");
        $ParamArray = [$_SESSION["CustomerId"]];
        $Fields = implode(",", $FieldNames);
        
        // Assuming MysqliSelect1 function handles the query correctly
        $customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);
    }
}
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    // Ensure the productId is fetched from session
    if (isset($_SESSION['productId'])) {
        $productId = $_SESSION['productId'];
        
        $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId");
        $ParamArray = array($productId); // Use the productId from session
        $Fields = implode(",", $FieldNames);

        $product_header_data = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
            $FieldNames,
            "i",
            $ParamArray
        );
    }
}
?>
<style>
/* Default: Hide the nav-toggler (applies to desktop by default) */


.pro-qty {
    display: flex;
    align-items: center;
    margin-top: 10px;

}

.suggestion-box {
    position: absolute;
    background: white;
    border: 1px solid #ddd;
    max-height: 200px;
    overflow-y: auto;
    width: 100%;
    display: none;
    z-index: 1000;
    /* Ensure it appears above other elements */
}

.search-item {
    display: flex;
    align-items: center;
    padding: 8px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.search-item:hover {
    background: #f5f5f5;
}

.search-image {
    width: 80px;
    height: 80px;
    margin-right: 10px;
}

.search-name {
    font-size: 14px;
}

@media (max-width: 768px) {
    .search-image {
        width: 60px;
        height: 60px;
    }

    .search-name {
        font-size: 13px;
    }
}

/* Mobile View (up to 480px) */
@media (max-width: 480px) {
    .suggestion-box {
        max-height: 150px;
        /* Reduced height for smaller screens */
    }

    .search-image {
        width: 50px;
        height: 50px;
    }

    .search-name {
        font-size: 12px;
    }

    .search-item {
        padding: 6px;
    }
}

/*.plus-minus {*/
/*    display: flex;*/
/*    align-items: center;*/
/*    gap: 5px;*/
/*}*/

.plus-btn,
.minus-btn {
    display: inline-block;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 30px;
    /*border: 1px solid #ddd;*/
    border-radius: 5px;
    cursor: pointer;
    background-color: #f7f7f7;
}

.plus-btn:hover,
.minus-btn:hover {
    background-color: #ddd;
}

input[name="quantity"] {
    width: 50px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 5px;
}


/* Show the nav-toggler on mobile screens (max-width: 768px) */
@media (max-width: 768px) {
    .sidebar-element {
        display: block;
    }

    /* Hide the entire section on mobile devices */
    .top1 {
        display: none;
    }
}

/* Center align the content in the section */
.top1 {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #305724 !important;
    height: 28px;
    /* Adjust as needed */
}

/* Style for the text */
.top-content {
    text-align: center;
}

.top-slogn {
    color: #fff;
    /* Adjust the text color as needed */
    font-size: 14px;
    /* Adjust the font size as needed */
    margin: 0;
}


/* Hide all slides by default */
.mySlides {
    display: none;
}

/* Fade effect */
.fade {
    animation-name: fade;
    animation-duration: 6s;
    /* Adjust the total duration for a slower effect */
    animation-timing-function: ease-in-out;
    animation-iteration-count: infinite;
    /* Ensure the animation repeats */
    animation-direction: alternate;
    /* Alternate between fade-in and fade-out */
}

@keyframes fade {
    0% {
        opacity: 0;
        /* Start with the text hidden */
    }

    30% {
        opacity: 1;
        /* Fade in to full opacity */
    }

    70% {
        opacity: 1;
        /* Stay at full opacity for longer */
    }

    100% {
        opacity: 0;
        /* Fade out to hidden */
    }
}


#mobileSearch {
    width: 90%;
    padding: 4px 20px;
    margin-bottom: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 30px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: white;
    outline: none;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

#mobileSearch:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.2);
    transform: scale(1.02);
}

.search-suggestion-box {
    position: absolute;
    top: 100%;
    left: 5%;
    right: 5%;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    z-index: 10;

}

.search-suggestion-box.show {
    max-height: 400px;
    overflow-y: auto;
}

.search-suggestion-box div {
    padding: 15px;
    border-bottom: 1px solid #eee;
    transition: background 0.2s ease;
    cursor: pointer;
}

.search-suggestion-box div:hover {
    background: #f8f8f8;
}

.search-suggestion-box div:last-child {
    border-bottom: none;
}

@media (min-width: 768px) {
    .search-header-element.search-wrap {
        display: none;

    }
}

@media (max-width: 767px) {
    .search-header-element.search-wrap {
        display: flex;
    }
}

.top-c {
    color: #f26c35;
    /* Free Delivery text color */
}

/* Make the list items appear one per row in the dropdown */
.mega-menu .supmenu-li {
    display: block;
    /* Ensures each item is in a separate row */
    padding: 8px 0;
    /* Adds space between items */
}

.mega-menu .supmenu-li a {
    display: block;
    /* Makes each link take up full width */
    padding: 10px 15px;
    /* Padding around the link */
    color: #333;
    /* Text color */
    text-decoration: none;
    /* Remove underline from links */
    font-size: 14px;
    /* Adjust font size */
}

.mega-menu .supmenu-li a:hover {
    background-color: #f4f4f4;
    /* Light background on hover */
    color: #007bff;
    /* Change text color on hover */
}

/* Styling for the collapsed mega menu */
.mega-menu {
    list-style-type: none;
    /* Removes default list bullets */
    margin: 0;
    padding: 0;
}

/* Optional: style the 'Shop by Category' link */
.link-title {
    font-weight: bold;
    /* Make the 'Shop by Category' text bold */
}

.link-title i {
    margin-left: 5px;
    /* Adds space between text and arrow icon */
}

/* Optional: style the collapsed icon */
.collapse.show .fa-angle-down {
    transform: rotate(180deg);
    /* Rotates the arrow when the menu is expanded */
}

.menu-link.active a {
    color: #ec6504 !important;
}

.menu-link.active a .sp-link-title {
    font-weight: bold;
}

.acc-mob {
    display: flex;
    align-items: center;
    gap: 10px;
    /* Adjust spacing between icons */
}

.acc-mob a {
    display: flex;
    align-items: center;
    text-decoration: none;
}

.truck-icon i {
    font-size: 24px;
}
</style>


<!-- top notificationbar start -->
<section class="top1">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="top-content">
                    <div class="slideshow-container">
                        <div class="mySlides fade">
                            <p class="top-slogn">
                                <i class="fas fa-truck" style="margin-right: 6px;"></i>
                                <span class="top-c">Free Delivery</span> on all orders above ₹399/-
                            </p>
                        </div>
                        <div class="mySlides fade">
                            <p class="top-slogn">
                                <i class="fas fa-tag" style="margin-right: 6px;"></i>
                                <span class="top-c">Exclusive Offers</span> on select products.
                            </p>
                        </div>
                        <div class="mySlides fade">
                            <p class="top-slogn">
                                <i class="fas fa-leaf" style="margin-right: 6px;"></i>
                                <span class="top-c">100%</span> Ayurvedic & Herbal products.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- top notificationbar end -->

<!-- header start -->
<header class="header-area">
    <div class="header-main-area">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="header-main">
                        <ul class="sidebar-element">
                            <li class="side-wrap nav-toggler">
                                <button class="navbar-toggler" type="button" data-toggle="collapse"
                                    data-target="#navbarContent">
                                    <span class="line"></span>
                                </button>
                            </li>
                        </ul>

                        <!-- logo start -->
                        <div class="header-element logo">
                            <a href="index.php">
                                <img src="image/main_logo.png" alt="logo-image" class="img-fluid"
                                    style="width: 250px; height: auto; margin: 15px;">
                            </a>
                        </div>
                        <!-- logo end -->
                        <!-- search start -->

                        <div class="header-element search-wrap">
                            <input type="text" id="desktopSearch" name="search" placeholder="" autocomplete="off">
                            <div id="desktop-search-results" class="suggestion-box"></div>
                        </div>


                        <!-- search end -->
                        <!-- header-icon start -->
                        <div class="header-element right-block-box">
                            <ul class="shop-element">
                                <li class="side-wrap user-wrap">
                                    <div class="acc-desk">
                                        <div class="user-icon">
                                            <?php if (isset($_SESSION["CustomerId"]) && !empty($customerData)) { ?>
                                            <a href="account.php" class="user-icon-desk">
                                                <span><i class="icon-user"></i></span>
                                            </a>
                                            <?php } else { ?>
                                            <a href="login.php" class="user-icon-desk">
                                                <span><i class="icon-user"></i></span>
                                            </a>
                                            <?php } ?>
                                        </div>
                                        <div class="user-info">
                                            <span class="acc-title">
                                                <?php 
                                                    if (isset($_SESSION["CustomerId"]) && !empty($customerData) && isset($customerData[0]["Name"])) {
                                                        echo htmlspecialchars($customerData[0]["Name"]);
                                                    } else {
                                                        echo "Account";
                                                    }
                                                ?>
                                            </span>
                                            <div class="account-login">
                                                <?php if (isset($_SESSION["CustomerId"])): ?>
                                                <a href="javascript:void(0);" onclick="confirmLogout();">Log Out</a>
                                                <?php else: ?>
                                                <a href="register.php">Register</a>
                                                <a href="login.php">Log in</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile Icons -->
                                    <div class="acc-mob">
                                        <ul class="shop-element">
                                            <!--<li class="side-wrap">-->
                                            <!--    <a href="search.php" class="search-icon">-->
                                            <!--        <span><i class="fa fa-search"></i></span>-->
                                            <!--    </a>-->
                                            <!--</li>-->
                                            <li class="side-wrap">
                                                <a href="account.php" class="user-icon">
                                                    <span><i class="icon-user"></i></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>


                                </li>

                                <!-- Logout Script -->
                                <script>
                                function confirmLogout() {
                                    if (confirm("Are you sure you want to log out?")) {
                                        logout();
                                    }
                                }

                                function logout() {
                                    // Show loading indicator
                                    console.log("Logging out...");

                                    // Use simple logout that bypasses cart persistence issues
                                    window.location.href = "logout_simple_fixed.php";
                                }
                                </script>

                                <li class="side-wrap cart-wrap">
                                    <div class="shopping-widget">
                                        <div class="shopping-cart">
                                            <a href="javascript:void(0)" class="cart-count">
                                                <span class="cart-icon-wrap">
                                                    <span class="cart-icon"><i class="icon-handbag"></i></span>
                                                    <span id="cart-total" class="cart-count-item bigcounter">
                                                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li class="side-wrap">
                                    <a href="track_order.php" class="truck-icon">
                                        <span><i class="fa fa-truck"></i></span>
                                    </a>
                                </li>
                            </ul>

                        </div>
                        <!-- header-icon end -->
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom-area">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="main-menu-area">
                            <div class="main-navigation navbar-expand-xl">
                                <div class="box-header menu-close">
                                    <button class="close-box" type="button"><i class="ion-close-round"></i></button>
                                </div>
                                <!-- menu start -->
                                <div class="navbar-collapse" id="navbarContent">
                                    <div class="megamenu-content">
                                        <div class="mainwrap">
                                            <ul class="main-menu">
                                                <li
                                                    class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                                                    <a href="index.php" class="link-title">
                                                        <span class="sp-link-title">Home</span>
                                                    </a>
                                                </li>

                                                <li
                                                    class="menu-link parent <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php' && isset($_GET['SubCategoryId'])) ? 'active' : ''; ?>">
                                                    <a href="products.php" class="link-title">
                                                        <span class="sp-link-title">Shop By Category</span>
                                                        <i class="fa fa-angle-down"></i>
                                                    </a>
                                                    <a href="#blog-style" data-bs-toggle="collapse"
                                                        class="link-title link-title-lg">
                                                        <span class="sp-link-title">Shop By Category</span>
                                                        <i class="fa fa-angle-down"></i>
                                                    </a>
                                                    <ul class="dropdown-submenu sub-menu collapse" id="blog-style">
                                                        <?php 
                                                            $FieldNames = array("SubCategoryId", "SubCategoryName", "PhotoPath");
                                                            $ParamArray = array();
                                                            $Fields = implode(",", $FieldNames);
                                                            $sub_category = $obj->MysqliSelect1("SELECT ".$Fields." FROM sub_category", $FieldNames, "", $ParamArray);
                                                            $currentSubCategoryName = isset($_GET['SubCategoryId']) ? $_GET['SubCategoryId'] : ''; // Get the current category from the URL
                                                            ?>
                                                        <?php foreach ($sub_category as $category) { ?>
                                                        <li
                                                            class="submenu-li <?php echo ($category['SubCategoryId'] == $currentSubCategoryName) ? 'active' : ''; ?>">
                                                            <a href="products.php?SubCategoryId=<?php echo urlencode($category["SubCategoryId"]); ?>"
                                                                class="g-l-link">
                                                                <span><?php echo htmlspecialchars($category["SubCategoryName"]); ?></span>
                                                            </a>
                                                        </li>
                                                        <?php } ?>
                                                    </ul>
                                                </li>


                                                <li
                                                    class="menu-link parent <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php' && isset($_GET['CategoryId'])) ? 'active' : ''; ?>">
                                                    <a href="products.php" class="link-title">
                                                        <span class="sp-link-title">Shop by Product</span>
                                                        <i class="fa fa-angle-down"></i>
                                                    </a>
                                                    <a href="#collapse-banner-menu" data-bs-toggle="collapse"
                                                        class="link-title link-title-lg">
                                                        <span class="sp-link-title">Collection</span>
                                                        <i class="fa fa-angle-down"></i>
                                                    </a>
                                                    <ul class="dropdown-submenu sub-menu collapse" id="blog-style">
                                                        <?php 
                                                                $FieldNames = array("CategoryId", "CategoryName");
                                                                $ParamArray = array();
                                                                $Fields = implode(",", $FieldNames);
                                                                $category = $obj->MysqliSelect1("Select ".$Fields." from category_master", $FieldNames, "", $ParamArray);
                                                                ?>
                                                        <?php foreach ($category as $sub_category) {?>
                                                        <li class="submenu-li">
                                                            <a href="products.php?CategoryId=<?php echo $sub_category["CategoryId"]; ?>"
                                                                class="g-l-link"><span><?php echo htmlspecialchars($sub_category["CategoryName"]); ?></span></a>
                                                        </li>
                                                        <?php } ?>
                                                    </ul>

                                                </li>
                                                <li
                                                    class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'combos.php') ? 'active' : ''; ?>">
                                                    <a href="combos.php" class="link-title">
                                                        <span class="sp-link-title">Combos</span>
                                                    </a>
                                                </li>
                                                <li
                                                    class="menu-link offers-menu <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                                                    <a href="index.php#contact" class="link-title">
                                                        <span class="sp-link-title">Offers <span
                                                                class="hot">Hot</span></span>
                                                    </a>
                                                </li>
                                                <li
                                                    class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'blogs.php' || basename($_SERVER['PHP_SELF']) == 'blog_details.php') ? 'active' : ''; ?>">
                                                    <a href="blogs.php" class="link-title">
                                                        <span class="sp-link-title">Blogs</span>
                                                    </a>
                                                </li>


                                                <li
                                                    class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'rewards.php') ? 'active' : ''; ?>">
                                                    <a href="rewards.php" class="link-title">
                                                        <span class="sp-link-title">Rewards</span>
                                                    </a>
                                                </li>
                                                <li
                                                    class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'authenticate.php') ? 'active' : ''; ?>">
                                                    <a href="authenticate.php" class="link-title">
                                                        <span class="sp-link-title">Authenticity</span>
                                                    </a>
                                                </li>
                                                <li
                                                    class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'customer-care.php') ? 'active' : ''; ?>">
                                                    <a href="customer-care.php" class="link-title">
                                                        <span class="sp-link-title">Customer Care</span>
                                                    </a>
                                                </li>
                                                <li
                                                    class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && isset($_GET['open_chat'])) ? 'active' : ''; ?>">
                                                    <a href="index.php?open_chat=1" class="link-title">
                                                        <span class="sp-link-title">Consult by AI <img src="./cms/images/microchip.png" alt="AI Icon"
                                                                style="width: 16px; height: 16px; margin-left: 5px; vertical-align: middle;"></span>
                                                    </a>
                                                </li>
                                                


                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- menu end -->
                                <!--<div class="img-hotline">-->
                                <!--    <div class="image-line">-->
                                <!--        <a href="javascript:void(0)"><img src="image/icon_contact.png" class="img-fluid"-->
                                <!--                alt="image-icon"></a>-->
                                <!--    </div>-->
                                <!--    <div class="image-content">-->
                                <!--        <span class="hot-l">Support:</span>-->
                                <!--        <span>+91-9834243754 (9 to 6 Pm)</span>-->
                                <!--    </div>-->
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mini-cart">
        <a href="javascript:void(0)" class="shopping-cart-close"><i class="ion-close-round"></i></a>
        <div class="cart-item-title">
            <p>
                <span class="cart-count-desc">There are</span>
                <span class="cart-count-item bigcounter"><?php
                                                                // Display the total count of unique products in the cart
                                                                echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                                                                ?></span>
                <span class="cart-count-desc">Products</span>
            </p>
        </div>
        <ul class="cart-item-loop">
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
                            $product_header_data = $obj->MysqliSelect1(
                                "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
                                $FieldNames,
                                "i",
                                $ParamArray
                            );

                            // Fetch price details for the product
                            $FieldNamesPrice = array("OfferPrice", "MRP");
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

                            // Check if product data is fetched
                            if ($product_header_data) {
                                // Calculate price for the product and add to the subtotal
                                if ($lowest_price !== "N/A") {
                                    $price = $lowest_price * $quantity;
                                    $subtotal += $price;
                                    $mrp_price = $mrp - $lowest_price;
                                    $mrp_price = $mrp_price * $quantity;
                                    $saving_price += $mrp_price;  // Add this product's total to the subtotal
                                }
                        ?>
            <li class="cart-item">
                <div class="cart-img">
                    <a href="product.php?id=<?php echo $productId; ?>">
                        <!-- Link to product details page -->
                        <img src="cms/images/products/<?php echo $product_header_data[0]['PhotoPath']; ?>"
                            alt="cart-image" class="img-fluid">
                    </a>
                </div>
                <div class="cart-title">
                    <h6><a
                            href="product.php?id=<?php echo $productId; ?>"><?php echo $product_header_data[0]['ProductName']; ?></a>
                    </h6>
                    <div class="cart-pro-info">
                        <div class="cart-qty-price">
                            <span class="price-box">
                                <span class="price">₹<span
                                        id="price-<?php echo $productId; ?>"><?php echo number_format($price, 2); ?></span>
                                    INR</span>
                                <?php if ($savings > 0): ?>
                                <small><del>₹<?php echo number_format($mrp * $quantity, 2); ?></del></small>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="delete-item-cart">
                            <a href="javascript:void(0);"
                                onclick="removeFromCart(<?php echo htmlspecialchars($productId); ?>)">
                                <i class="icon-trash icons"></i>
                            </a>
                        </div>
                    </div>
                    <div class="pro-qty">
                        <div class="pro-qty">
                            <!--<div class="plus-minus">-->
                            <!--    <span>-->
                            <!--        <a href="javascript:void(0)" class="minus-btn text-black">-</a>-->
                            <!--        <input type="text" -->
                            <!--            id="quantity-<?php echo $productId; ?>" -->
                            <!--            name="quantity"-->
                            <!--            value="<?php echo $quantity; ?>" -->
                            <!--            readonly -->
                            <!--            data-product-id="<?php echo $productId; ?>"-->
                            <!--            data-offer-price="<?php echo $lowest_price; ?>"-->
                            <!--            data-mrp="<?php echo $mrp; ?>">-->
                            <!--        <a href="javascript:void(0)" class="plus-btn text-black">+</a>-->
                            <!--    </span>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
            </li>

            <?php
                            }
                        }
                    } else {
                        echo '<p>Cart is Empty</p>';
                    }
                    ?>
        </ul>

        <ul class="subtotal-title-area">
            <li class="subtotal-info">
                <div class="subtotal-titles">
                    <h6>Sub total:</h6>
                    <span class="subtotal-price">₹<?php echo number_format($subtotal, 2); ?> INR</span>
                </div>
            </li>
            <li class="subtotal-info">
                <div class="subtotal-titles">
                    <h6>Total Savings:</h6>
                    <span class="subtotal-price">₹<?php echo number_format($saving_price, 2); ?> INR</span>
                </div>
            </li>
            <li class="mini-cart-btns">
                <div class="cart-btns">
                    <a href="cart.php" class="btn btn-style2">View cart</a>
                    <a href="checkout.php" class="btn btn-style2">Checkout</a>
                </div>
            </li>
        </ul>
    </div>
    <!-- search start -->
    <div class="modal fade" id="search-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <div class="search-content">
                                    <div class="search-engine">
                                        <input type="text" id="searchInput" name="search" placeholder="Search products,brands or advice">
                                        <a href="javascript:void(0);" class="search-btn"><i
                                                class="ion-ios-search-strong"></i></a>
                                    </div>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i
                                            class="ion-close-round"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="search-header-element search-wrap">
        <input type="text" id="mobileSearch" name="mobile_search" placeholder="" autocomplete="off">
        <div id="mobile-search-results" class="suggestion-box"></div>
    </div>

    <!-- search end -->
</header>

<!-- header end -->
<script>
var slideIndex = 0;

function showSlides() {
    var slides = document.getElementsByClassName("mySlides");
    for (var i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) {
        slideIndex = 1
    }
    slides[slideIndex - 1].style.display = "block";
    setTimeout(showSlides, 3000); // Change slide every 3 seconds
}

showSlides(); // Initial call to start slideshow
</script>
<!-- mobile menu start -->
<div class="header-bottom-area mobile">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="main-menu-area">
                    <div class="main-navigation navbar-expand-xl">
                        <div class="box-header menu-close">
                            <button class="close-box" type="button"><i class="ion-close-round"></i></button>
                        </div>
                        <!-- menu start -->
                        <div class="navbar-collapse" id="navbarContent01">
                            <div class="megamenu-content">
                                <div class="mainwrap">
                                    <ul class="main-menu">
                                        <li class="menu-link">
                                            <a href="index.php" class="link-title">
                                                <span class="sp-link-title">Home</span>
                                            </a>
                                        </li>
                                        <li class="menu-link parent">
                                            <a href="products.php" class="link-title">
                                                <span class="sp-link-title">Shop By Category</span>
                                                <i class="fa fa-angle-down"></i>
                                            </a>
                                            <a href="#collapse-banner-menu1" data-bs-toggle="collapse"
                                                class="link-title link-title-lg">
                                                <span class="sp-link-title">Shop By Category</span>
                                                <i class="fa fa-angle-down"></i>
                                            </a>
                                            <ul class="dropdown-submenu banner-menu collapse"
                                                id="collapse-banner-menu1">
                                                <?php 
                                                        $FieldNames = array("SubCategoryId", "SubCategoryName", "PhotoPath");
                                                        $ParamArray = array();
                                                        $Fields = implode(",", $FieldNames);
                                                        $sub_category = $obj->MysqliSelect1("Select ".$Fields." from sub_category", $FieldNames, "", $ParamArray);
                                                        ?>
                                                <?php foreach ($sub_category as $category) {?>
                                                <li class="menu-banner">
                                                    <a href="products.php?SubCategoryId=<?php echo urlencode($category["SubCategoryId"]); ?>"
                                                        class="menu-banner-img"><img src="image/menu-banner01.jpg"
                                                            alt="menu-image" class="img-fluid"></a>
                                                    <a href="products.php?SubCategoryId=<?php echo urlencode($category["SubCategoryId"]); ?>"
                                                        class="menu-banner-title"><span><?php echo htmlspecialchars($category["SubCategoryName"]); ?></span></span></a>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                        <li class="menu-link parent">
                                            <a href="products.php" class="link-title">
                                                <span class="sp-link-title">Shop By Product</span>
                                                <i class="fa fa-angle-down"></i>
                                            </a>
                                            <a href="#collapse-banner-menu2" data-bs-toggle="collapse"
                                                class="link-title link-title-lg">
                                                <span class="sp-link-title">Shop By Product</span>
                                                <i class="fa fa-angle-down"></i>
                                            </a>
                                            <ul class="dropdown-submenu banner-menu collapse"
                                                id="collapse-banner-menu2">
                                                <?php 
                                                        $FieldNames = array("CategoryId", "CategoryName");
                                                        $ParamArray = array();
                                                        $Fields = implode(",", $FieldNames);
                                                        $category = $obj->MysqliSelect1("Select ".$Fields." from category_master", $FieldNames, "", $ParamArray);
                                                        ?>
                                                <?php foreach ($category as $sub_category) {?>
                                                <li class="menu-banner">
                                                    <a href="products.php?CategoryId=<?php echo urlencode($sub_category["CategoryId"]); ?>"
                                                        class="menu-banner-title"><span><?php echo htmlspecialchars($sub_category["CategoryName"]); ?></span></span></a>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                        <li class="menu-link">
                                            <a href="combos.php" class="link-title">
                                                <span class="sp-link-title">Combos</span>
                                            </a>
                                        </li>
                                        <li class="menu-link offers-menu">
                                            <a href="index.php#contact" class="link-title">
                                                <span class="sp-link-title">Offers <span class="hot">Hot</span></span>
                                            </a>
                                        </li>
                                        <li class="menu-link">
                                            <a href="blogs.php" class="link-title">
                                                <span class="sp-link-title">Blogs</span>
                                            </a>
                                        </li>
                                        <li class="menu-link">
                                            <a href="rewards.php" class="link-title">
                                                <span class="sp-link-title">Rewards</span>
                                            </a>
                                        </li>
                                        <li class="menu-link">
                                            <a href="authenticate.php" class="link-title">
                                                <span class="sp-link-title">Authenticity</span>
                                            </a>
                                        </li>
                                        <li class="menu-link">
                                            <a href="customer-care.php" class="link-title">
                                                <span class="sp-link-title">Customer Care</span>
                                            </a>
                                        </li>
                                        <li class="menu-link">
                                            <a href="index.php?open_chat=1" class="link-title">
                                                <span class="sp-link-title">Consult by AI <img src="./cms/images/microchip.png" alt="AI Icon"
                                                                style="width: 16px; height: 16px; margin-left: 5px; vertical-align: middle;"></span>
                                            </a>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- menu end -->
                        <div class="img-hotline">
                            <div class="image-line">
                                <a href="javascript:void(0)"><img src="image/icon_contact.png" class="img-fluid"
                                        alt="image-icon"></a>
                            </div>
                            <div class="image-content">
                                <span class="hot-l">Hotline:</span>
                                <span>0123 456 789</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- mobile menu end -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        const target = e.target;
        if (target.classList.contains('plus-btn') || target.classList.contains('minus-btn')) {
            const proQty = target.closest('.pro-qty');
            const input = proQty.querySelector('input[type="text"]');
            const productId = input.dataset.productId;
            const currentQty = parseInt(input.value);
            let newQty = currentQty;

            if (target.classList.contains('plus-btn')) {
                newQty++;
            } else {
                newQty = Math.max(1, currentQty - 1);
            }

            if (newQty !== currentQty) {
                const plusBtn = proQty.querySelector('.plus-btn');
                const minusBtn = proQty.querySelector('.minus-btn');
                updateQuantity(productId, newQty, input, currentQty, plusBtn, minusBtn);
            }
        }
    });

    function updateQuantity(productId, newQty, input, oldQty, plusBtn, minusBtn) {
        // Disable buttons during request
        plusBtn.disabled = true;
        minusBtn.disabled = true;

        fetch('../exe_files/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    productId,
                    quantity: newQty
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.value = newQty;
                    updateItemPrice(productId, newQty);
                    updateCartTotals();
                } else {
                    input.value = oldQty;
                }
            })
            .catch(() => {
                input.value = oldQty;
            })
            .finally(() => {
                // Re-enable buttons after request completes
                plusBtn.disabled = false;
                minusBtn.disabled = false;
            });
    }

    function updateItemPrice(productId, quantity) {
        const input = document.querySelector(`input[data-product-id="${productId}"]`);
        const offerPrice = parseFloat(input.dataset.offerPrice);
        const mrp = parseFloat(input.dataset.mrp);

        const priceElement = document.getElementById(`price-${productId}`);
        if (priceElement) {
            priceElement.textContent = `₹${(offerPrice * quantity).toFixed(2)}`;
        }

        const mrpElement = document.querySelector(`#mrp-${productId}`);
        if (mrpElement && mrp > offerPrice) {
            mrpElement.textContent = `₹${(mrp * quantity).toFixed(2)}`;
        }
    }

    function updateCartTotals() {
        let subtotal = 0,
            totalSavings = 0;

        document.querySelectorAll('.cart-item').forEach(item => {
            const input = item.querySelector('input[type="text"]');
            const quantity = parseInt(input.value);
            subtotal += parseFloat(input.dataset.offerPrice) * quantity;
            totalSavings += (parseFloat(input.dataset.mrp) - parseFloat(input.dataset.offerPrice)) *
                quantity;
        });

        document.querySelector('.subtotal-price').textContent = `₹${subtotal.toFixed(2)}`;
        document.querySelector('.savings-price').textContent = `₹${totalSavings.toFixed(2)}`;

        const totalItems = Array.from(document.querySelectorAll('.cart-item input')).reduce((sum, input) =>
            sum + parseInt(input.value), 0);
        document.querySelector('.bigcounter').textContent = totalItems;
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function selectSuggestion(name) {
    const searchInput = document.getElementById("searchInput");
    const mobileSearch = document.getElementById("mobileSearch");

    if (!searchInput.value.trim()) { // Checks if searchInput is empty or null
        mobileSearch.value = name;
    } else {
        searchInput.value = name;
    }
    document.getElementById("suggestions").style.display = "none";
}

function removeFromCart(productId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to remove this item from your cart?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ec6504',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send an AJAX request to remove the product
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'exe_files/remove-from-cart.php?productId=' + encodeURIComponent(productId), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Remove the product from the cart display
                            const cartItem = document.getElementById('cart-item-' + productId);
                            if (cartItem) {
                                cartItem.remove();
                            }

                            // Update the cart summary
                            if (response.cartSummary) {
                                const cartSummary = document.getElementById('cart-summary');
                                if (cartSummary) {
                                    cartSummary.innerText = response.cartSummary;
                                }
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: response.message || 'Product removed from cart',
                                confirmButtonColor: '#ec6504',
                            }).then(() => {
                                location.reload(); // Reload the page after confirmation
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to remove product from cart',
                                confirmButtonColor: '#ec6504',
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing server response:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing the response.',
                            confirmButtonColor: '#ec6504',
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove product from cart. Please try again later.',
                        confirmButtonColor: '#ec6504',
                    });
                }
            };
            xhr.onerror = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while connecting to the server.',
                    confirmButtonColor: '#ec6504',
                });
            };
            xhr.send();
        }
    });
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let desktopSearch = document.getElementById("desktopSearch");
    let modalSearch = document.getElementById("searchInput");
    let mobileSearch = document.getElementById("mobileSearch");
    let desktopResultBox = document.getElementById("desktop-search-results");
    let mobileResultBox = document.getElementById("mobile-search-results");

    // Function for handling Desktop Search
    function handleDesktopSearch() {
        let query = desktopSearch.value.trim();

        if (query.length === 0) {
            desktopResultBox.innerHTML = "";
            desktopResultBox.style.display = "none";
            return;
        }

        fetch("exe_files/search_filter.php?s=" + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => renderResults(data, desktopSearch, desktopResultBox))
            .catch(error => console.error("Error fetching data (Desktop):", error));
    }

    // Function for handling Modal Search
    function handleModalSearch() {
        let query = modalSearch.value.trim();

        if (query.length === 0) {
            return;
        }

        fetch("exe_files/search_filter.php?s=" + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    // Redirect to search results page or product page
                    if (data[0].ProductId) {
                        window.location.href = `product_details.php?ProductId=${data[0].ProductId}`;
                    }
                }
            })
            .catch(error => console.error("Error fetching data (Modal):", error));
    }

    // Function for handling Mobile Search
    function handleMobileSearch() {
        let query = mobileSearch.value.trim();

        if (query.length === 0) {
            mobileResultBox.innerHTML = "";
            mobileResultBox.style.display = "none";
            return;
        }

        fetch("exe_files/search_filter.php?s=" + encodeURIComponent(query))
            .then(response => response.json())
            .then((data) => {
                if (data) {
                    renderResults(data, mobileSearch, mobileResultBox);
                }
            })
            .catch(error => console.error("Error fetching data (Mobile):", error));
    }

    // Render results for both Desktop & Mobile
    function renderResults(data, inputElement, resultContainer) {
        resultContainer.innerHTML = "";

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(product => {
                let item = document.createElement("div");
                item.classList.add("search-item");

                // Check if PhotoPath exists and is not empty
                const imagePath = product.PhotoPath ? `cms/images/products/${product.PhotoPath}` :
                    'cms/images/products/default.jpg';

                item.innerHTML = `
                <img src="${imagePath}" alt="${product.ProductName}" class="search-image">
                <span class="search-name">${product.ProductName}</span>
            `;

                item.addEventListener("click", () => {
                    inputElement.value = product.ProductName;
                    resultContainer.style.display = "none";
                    window.location.href = `product_details.php?ProductId=${product.ProductId}`;
                });

                resultContainer.appendChild(item);
            });
            resultContainer.style.display = "block";
        } else {
            resultContainer.innerHTML = "<p class='no-result'>No result found</p>";
            resultContainer.style.display = "block";
        }
    }

    // Event Listeners for Desktop, Modal and Mobile Search Inputs
    if (desktopSearch) {
        desktopSearch.addEventListener("input", handleDesktopSearch);
    }

    if (modalSearch) {
        modalSearch.addEventListener("input", handleModalSearch);
        // Also handle Enter key for modal search
        modalSearch.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                handleModalSearch();
            }
        });
    }

    // Handle search button click in modal
    const modalSearchBtn = document.querySelector('.search-btn');
    if (modalSearchBtn) {
        modalSearchBtn.addEventListener("click", function(event) {
            event.preventDefault();
            handleModalSearch();
        });
    }

    if (mobileSearch) {
        mobileSearch.addEventListener("input", handleMobileSearch);
    }

    document.addEventListener("click", function(event) {
        if (
            (!desktopSearch || !desktopSearch.contains(event.target)) &&
            (!mobileSearch || !mobileSearch.contains(event.target)) &&
            (!desktopResultBox || !desktopResultBox.contains(event.target)) &&
            (!mobileResultBox || !mobileResultBox.contains(event.target))
        ) {
            if (desktopResultBox) desktopResultBox.style.display = "none";
            if (mobileResultBox) mobileResultBox.style.display = "none";
        }
    });
});
</script>

<script>
const texts = [
     "Search She Care Plus Juice",
    "Search Diabetic Care Juice",
    "Search Thyro Balance Care Juice",
    "Search Cholesterol Care Juice",
    "Search BP Care Juice",
    "Search Apple Cider Vinegar",
    "Search Karela Neem & Jamun Juice",
    "Search Wheatgrass Juice",
    "Search Wild Amla Juice",
    "Search Shilajit Gold Pro",
    "Search Shilajit Resin Pro",
    "Search Pure Shilajit Resin"
];

let currentTextIndex = 0;
let i = 0;
let timeoutId;

const inputs = [document.getElementById('searchInput'), document.getElementById('mobileSearch')];

function typeWriter() {
    const currentText = texts[currentTextIndex];

    if (i <= currentText.length) {
        inputs.forEach(input => {
            if (input) input.placeholder = currentText.substring(0, i);
        });
        i++;
        timeoutId = setTimeout(typeWriter, 100);
    } else {
        timeoutId = setTimeout(() => {
            currentTextIndex = (currentTextIndex + 1) % texts.length;
            i = 0;
            typeWriter();
        }, 1500);
    }
}

window.addEventListener('DOMContentLoaded', typeWriter);

// Reset animation on focus
inputs.forEach(input => {
    if (input) {
        input.addEventListener('focus', () => {
            clearTimeout(timeoutId);
            currentTextIndex = 0;
            i = 0;
            input.placeholder = '';
            typeWriter();
        });
    }
});
</script>
<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// Check if user is logged in - redirect to login if not
if(!isset($_SESSION["CustomerId"]) || empty($_SESSION["CustomerId"])){
    header("Location: login.php");
    exit();
}

// Get customer data for logged-in users
$FieldNames = array("CustomerId", "Name", "MobileNo", "IsActive");
$ParamArray = [$_SESSION["CustomerId"]];
$Fields = implode(",", $FieldNames);

$customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);

$FieldNames = array("CustomerId", "Address", "State", "City","PinCode","Landmark");
$ParamArray = [$_SESSION["CustomerId"]];
$Fields = implode(",", $FieldNames);

// Get address data for registered users
$addressData = $obj->MysqliSelect1("SELECT $Fields FROM customer_address WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);

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

  
    </style>
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
                                <div class="billing-form">
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Full Name</label>
                                            <input type="text" name="Name" placeholder="Full Name"
                                                value="<?php echo !empty($customerData[0]['Name']) ? htmlspecialchars($customerData[0]['Name']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul input-2">
                                        <li class="billing-li">
                                            <label>Email address</label>
                                            <input type="text" name="mail" placeholder="Email address"
                                                value="<?php echo !empty($customerData[0]['Email']) ? htmlspecialchars($customerData[0]['Email']) : ''; ?>">
                                        </li>
                                        <li class="billing-li">
                                            <label>Phone number</label>
                                            <input type="text" name="phone" placeholder="Phone number"
                                                value="<?php echo !empty($customerData[0]['MobileNo']) ? htmlspecialchars($customerData[0]['MobileNo']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Street address</label>
                                            <input type="text" name="address" placeholder="Street address"
                                                value="<?php echo !empty($addressData[0]['Address']) ? htmlspecialchars($addressData[0]['Address']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Landmark</label>
                                            <input type="text" name="landmark" placeholder="Optional"
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
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>State</label>
                                            <input type="text" name="state" id="state" placeholder="State"
                                                value="<?php echo !empty($addressData[0]['State']) ? htmlspecialchars($addressData[0]['State']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>City</label>
                                            <input type="text" name="city" id="city" placeholder="City"
                                                value="<?php echo !empty($addressData[0]['City']) ? htmlspecialchars($addressData[0]['City']) : ''; ?>">
                                        </li>
                                    </ul>
                                    <ul class="billing-ul">
                                        <li class="billing-li">
                                            <label>Pin code</label>
                                            <input type="text" name="pincode" id="pincode" placeholder="Pin code"
                                                value="<?php echo !empty($addressData[0]['PinCode']) ? htmlspecialchars($addressData[0]['PinCode']) : ''; ?>">
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

                                <!-- Coupon Code Section -->
                                <div style="margin-top: 20px;" class="coupon-code-section">
                                    <form action="" class="apply-coupon-form">
                                        <label style="margin-bottom:10px;" for="coupon-code">Apply Offer Code</label>
                                        <div class="coupon-input-container">
                                            <input type="text" name="code" id="coupon-code"
                                                placeholder="Enter offer code" class="coupon-input">
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
    document.querySelector('.apply-coupon-btn').addEventListener('click', function(e) {
        e.preventDefault();

        const couponCode = document.querySelector('#coupon-code').value.trim();
        const responseElement = document.querySelector('#coupon-response');

        // Clear previous response
        responseElement.style.display = 'none';
        responseElement.textContent = '';

        if (!couponCode) {
            responseElement.textContent = 'Please enter a coupon code.';
            responseElement.style.display = 'block';
            return;
        }

        // Make AJAX request to backend API
        fetch('exe_files/fetch_coupon.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: couponCode
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.response === 'S') {
                    // Success: Show success message or apply discount
                    responseElement.textContent =
                        `Coupon applied successfully! Discount: ₹ ${data.discount}`;
                    responseElement.style.color = 'green';
                } else {
                    // Failure: Show error message
                    responseElement.textContent = data.msg || 'Invalid coupon code.';
                    responseElement.style.color = 'red';
                }
                responseElement.style.display = 'block';
            })
            .catch((error) => {
                // Error: Show general error message
                responseElement.textContent = 'An error occurred. Please try again later.';
                responseElement.style.color = 'red';
                responseElement.style.display = 'block';
                console.error('Error:', error);
            });
    });
    </script>
    <script>
    let orderData = {};

    // Initialize pincode auto-fill functionality
    document.addEventListener('DOMContentLoaded', function() {
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

    function placeOrder() {
        console.log("=== placeOrder function started ===");
        let CustomerId = <?php echo isset($_SESSION["CustomerId"]) ? $_SESSION["CustomerId"] : 'null'; ?>;
        console.log("CustomerId from session:", CustomerId);

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

        // Validate required fields
        if (!name || !email || !phone || !address || !pincode || !state || !city || !final_total || !CustomerId) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'All fields are required. Please fill out the missing details.',
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

        // Validate CustomerId is a valid number
        if (!CustomerId || isNaN(CustomerId)) {
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
        let checkoutItems = document.querySelectorAll(".checkout-item");

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
                let productIdElement = item.querySelector("input[name='product_id']");
                let productNameElement = item.querySelector(".product-name");
                let productCodeElement = item.querySelector(".check-code-blod span");
                let sizeElement = item.querySelector(".check-size");
                let quantityElement = item.querySelector(".check-quantity");
                let imageElement = item.querySelector(".check-pro-img img");
                let offerPriceElement = item.querySelector(".offer-price");

                // Enhanced validation with detailed logging
                if (!productIdElement) {
                    console.error(`Missing product ID element for item ${index + 1}`);
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
                let productCode = productCodeElement ? productCodeElement.innerText.trim() : "";
                let size = sizeElement ? (sizeElement.innerText.split(": ")[1] || "").trim() : "";
                let quantity = quantityElement ? (quantityElement.innerText.split(": ")[1] || "1").trim() : "1";
                let imagePath = imageElement ? imageElement.src : "";
                let offerPrice = offerPriceElement ? (offerPriceElement.dataset.price || "1") : "1";

                // Validate extracted data
                if (!productId || !productName) {
                    console.error(`Invalid product data for item ${index + 1}:`, {
                        productId, productName, productCode, size, quantity, offerPrice
                    });
                    return;
                }

                products.push({
                    id: productId,
                    name: productName,
                    code: productCode,
                    size: size,
                    quantity: quantity,
                    image: imagePath,
                    offer_price: offerPrice
                });
            } catch (error) {
                console.error(`Error processing product ${index + 1}:`, error);
            }
        });

        // Validate that we have valid products
        if (products.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Product Error',
                text: 'Unable to process cart items. Please refresh the page and try again.',
                confirmButtonColor: '#ec6504'
            });
            return;
        }

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
            CustomerId: CustomerId,
            customerType: 'Registered'
        };

        // Debug logging
        console.log("Order data prepared:", orderData);
        console.log("Payment method selected:", selectedPaymentMethod);
        console.log("Products count:", products.length);
        console.log("About to call checkPaymentType()...");

        checkPaymentType();
    }

    function checkPaymentType() {
    console.log("checkPaymentType called, payment method:", orderData.paymentMethod);
    if (orderData.paymentMethod === "Online") {
        console.log("Calling initiateRazorpayPayment...");
        initiateRazorpayPayment();
    } else if (orderData.paymentMethod === "COD") {
        console.log("Calling sendOrderData...");
        sendOrderData();
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

            // SweetAlert Animated Success Message
            Swal.fire({
                icon: 'success',
                title: 'Order Placed Successfully!',
                text: 'Your order has been placed. You will receive a confirmation soon.',
                confirmButtonColor: '#ec6504',
                timer: 3000,  // Auto-close after 3 seconds
                showConfirmButton: false
            }).then(() => {
                window.location.href = "order-placed.php?order_id=" + data.order_id;
            });

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
                            fetch("exe_files/razorpay_callback.php", {
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
                                .then(res => res.json())
                                .then(result => {
                                    if (result?.status === "success") {
                                        window.location.href = "order-placed.php?order_id=" + data
                                            .order_id;
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
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: "Error verifying payment.",
                                        confirmButtonColor: '#ec6504'
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
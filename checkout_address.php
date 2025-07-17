<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

$FieldNames = array("CustomerId", "Name", "MobileNo", "IsActive");
$ParamArray = [$_SESSION["CustomerId"]];
$Fields = implode(",", $FieldNames);

// Assuming MysqliSelect1 function handles the query correctly
$customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);

$FieldNames = array("CustomerId", "Address", "State", "City","PinCode","Landmark");
$ParamArray = [$_SESSION["CustomerId"]];
$Fields = implode(",", $FieldNames);

// Assuming MysqliSelect1 function handles the query correctly
$customerAddress = $obj->MysqliSelect1("SELECT $Fields FROM customer_address WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Organic and Healthy Products</title>
    <meta name="description" content="My Nutrify offers a wide range of organic, healthy, and nutritious products for your wellness and lifestyle."/>
    <meta name="keywords" content="organic products, healthy food, nutrition, eCommerce, wellness, healthy living, organic supplements, eco-friendly"/>
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .add-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
        }

        .edit-btn-container {
            margin-left: auto; /* Ensures the button is pushed to the right */
        }

  
    </style>
</head>
</head>
<body class="home-1">

    <!-- header start -->
        <?php include("components/header.php") ?>
        <!-- header end -->

        <section class="address-area section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="address-title">
                            <h1>Your addresses</h1>
                        </div>
                        <div class="account-link">
                            <a href="account.php">Return to account details</a>
                        </div>
                        <div class="add-area">
                        <div class="add-title">
                            <h4>Your Shipping Address</h4>
                            <?php if (!empty($customerAddress)) { ?>
                                <div class="edit-btn-container">
                                    <a href="javascript:void(0)" class="btn btn-style1" id="editAddressBtn">Edit address</a>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="col-8">
                        <div class="address-content">
                            <form id="addressForm">
                                <ul class="address-input">
                                    <li class="type-add">
                                        <label>Name</label>
                                        <input type="text" name="f-name" placeholder="First name" 
                                            value="<?php echo !empty($customerData[0]['Name']) ? htmlspecialchars($customerData[0]['Name']) : ''; ?>" 
                                            <?php echo !empty($customerData[0]['Name']) ? 'disabled' : ''; ?>>
                                    </li>
                                    <li class="type-add">
                                        <label>Email</label>
                                        <input type="email" name="email" placeholder="Email" 
                                            value="<?php echo !empty($customerData[0]['Email']) ? htmlspecialchars($customerData[0]['Email']) : ''; ?>" 
                                            <?php echo !empty($customerData[0]['Email']) ? 'disabled' : ''; ?>>
                                    </li>
                                    <li class="type-add">
                                        <label>Phone number</label>
                                        <input type="text" name="phone" placeholder="Phone number" 
                                            value="<?php echo !empty($customerData[0]['MobileNo']) ? htmlspecialchars($customerData[0]['MobileNo']) : ''; ?>" 
                                            <?php echo !empty($customerData[0]['MobileNo']) ? 'disabled' : ''; ?>>
                                    </li>
                                    <li class="type-add">
                                        <label>Address</label>
                                        <input type="text" name="Address" placeholder="Address" 
                                            value="<?php echo !empty($customerAddress[0]['Address']) ? htmlspecialchars($customerAddress[0]['Address']) : ''; ?>" 
                                            <?php echo !empty($customerAddress[0]['Address']) ? 'disabled' : ''; ?>>
                                    </li>
                                    <li class="type-add">
                                        <label>Landmark</label>
                                        <input type="text" name="Landmark" placeholder="Landmark" 
                                            value="<?php echo !empty($customerAddress[0]['Landmark']) ? htmlspecialchars($customerAddress[0]['Landmark']) : ''; ?>" 
                                            <?php echo !empty($customerAddress[0]['Landmark']) ? 'disabled' : ''; ?>>
                                    </li>
                                    <li class="type-add">
                                        <label>City</label>
                                        <input type="text" name="City" placeholder="City" 
                                            value="<?php echo !empty($customerAddress[0]['City']) ? htmlspecialchars($customerAddress[0]['City']) : ''; ?>" 
                                            <?php echo !empty($customerAddress[0]['City']) ? 'disabled' : ''; ?>>
                                    </li>
                                    <li class="type-add">
                                        <label>State</label>
                                        <input type="text" name="State" placeholder="State" 
                                            value="<?php echo !empty($customerAddress[0]['State']) ? htmlspecialchars($customerAddress[0]['State']) : ''; ?>" 
                                            <?php echo !empty($customerAddress[0]['State']) ? 'disabled' : ''; ?>>
                                    </li>
                                    <li class="type-add">
                                        <label>Pin / Zip code</label>
                                        <input type="text" name="PinCode" placeholder="Postal/Zip code" 
                                            value="<?php echo !empty($customerAddress[0]['PinCode']) ? htmlspecialchars($customerAddress[0]['PinCode']) : ''; ?>" 
                                            <?php echo !empty($customerAddress[0]['PinCode']) ? 'disabled' : ''; ?>>
                                    </li>
                                </ul>
                                <div class="add-link">
                                    <button type="submit" class="btn btn-style1" id="submitBtn">
                                        <?php echo empty($customerAddress) ? 'Add address' : 'Update Address'; ?>
                                    </button>
                                    <a href="index1.html" class="btn btn-style1">Cancel</a>
                                </div>
                            </form>
                        </div>
                        </div>
                        <div class="col-4">
                            <div class="order-area">
                            <div class="check-pro">
                                <h2>In your cart</h2>
                                <ul class="check-ul">
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
                                    <li>
                                        <div class="check-pro-img">
                                            <a href="product.php"><img
                                                    src="cms/images/products/<?php echo $product_data[0]['PhotoPath']; ?>"
                                                    class="img-fluid" alt="image"></a>
                                        </div>
                                        <div class="check-content">
                                            <a href="product.php"><?php echo $product_data[0]['ProductName']; ?></a>
                                            <span class="check-code-blod">Product code:
                                                <span><?php echo $product_data[0]['ProductCode']; ?></span></span>
                                            <span class="check-price">₹<?php echo $price; ?></span>
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

                            <div class="order-summary">
                                <ul class="order-history">
                                <li class="order-details">
                                    <span>Sub Total: </span>
                                    <span>₹ <?php echo $subtotal; ?> INR</span>
                                </li>
                                <li class="order-details">
                                    <span>Delivery Charges:</span>
                                    <span><?php echo $delivery_message; ?></span>
                                </li>
                            </ul>

                            <!-- Coupon Code Section (Placed outside the UL for proper HTML structure) -->
                            <div style="margin-top: 20px;" class="coupon-code-section">
                                <form action="" class="apply-coupon-form">
                                    <label for="coupon-code">Apply Offer Code</label>
                                    <div class="coupon-input-container">
                                        <input type="text" name="code" id="coupon-code" placeholder="Enter offer code" class="coupon-input">
                                        <a href="javascript:void(0)" class="btn btn-style1 apply-coupon-btn">Apply</a>
                                    </div>
                                    <p id="coupon-response" style="margin-top: 10px; color: red; display: none;"></p>
                                </form>
                            </div>


                            <ul class="order-history">
                                <li class="order-details">
                                    <span>Total</span>
                                    <span>₹ <?php echo $final_total; ?> INR</span>
                                </li>
                            </ul>



                                <!-- Payment Methods Section -->
                                <!-- <form action="" class="payment-methods-form">
                                    <ul class="order-form">
                                        <li>
                                            <input type="checkbox" name="payment-method" id="direct-bank-transfer">
                                            <label for="direct-bank-transfer">Direct bank transfer</label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="payment-method" id="cheque-payment">
                                            <label for="cheque-payment">Cheque payment</label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="payment-method" id="paypal-payment">
                                            <label for="paypal-payment">Paypal</label>
                                        </li>
                                        <li class="pay-icon">
                                            <a href="javascript:void(0)"><i class="fa fa-credit-card"></i></a>
                                            <a href="javascript:void(0)"><i class="fa fa-cc-visa"></i></a>
                                            <a href="javascript:void(0)"><i class="fa fa-cc-paypal"></i></a>
                                            <a href="javascript:void(0)"><i class="fa fa-cc-mastercard"></i></a>
                                        </li>
                                    </ul>
                                </form> -->

                                <!-- Place Order Button -->
                                <div class="checkout-btn">
                                    <button onclick="makePayment(<?php echo $final_total; ?>, 'Purchase Order')" class="btn-style1">Place Order</button>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $("#addressForm").on("submit", function (e) {
        e.preventDefault(); // Prevent default form submission

        let isValid = true;

        // Check all input fields for empty values
        $("#addressForm input").each(function () {
            if ($.trim($(this).val()) === "") {
                isValid = false;
                $(this).addClass("error"); // Highlight the field with error
            } else {
                $(this).removeClass("error"); // Remove error highlight
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'All fields are required. Please fill in all the fields.',
                confirmButtonColor: '#ec6504',
            });
            return; // Stop form submission
        }

        // Serialize form data
        let formData = $(this).serialize();

        // AJAX call
        $.ajax({
            url: "exe_files/exe_save_address.php", // Your PHP script URL
            type: "POST",
            data: formData,
            dataType: "json",
            beforeSend: function () {
                $("#submitBtn").prop("disabled", true).text("Processing...");
            },
            success: function (response) {
                if (response.response === "S") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.msg,
                        confirmButtonColor: '#ec6504',
                    }).then(() => {
                        location.reload(); // Reload the page after confirmation
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the address.',
                        confirmButtonColor: '#ec6504',
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'AJAX Error',
                    text: 'A server error occurred. Please try again later.',
                    confirmButtonColor: '#ec6504',
                });
                console.error(status + "-" + error);
            },
            complete: function () {
                $("#submitBtn").prop("disabled", false).text("Submit");
            },
        });
    });

    $('#editAddressBtn').on('click', function() {
        // Enable all inputs within the address form
        $('#addressForm input').prop('disabled', false);
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
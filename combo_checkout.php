<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');

try {
    $obj = new main();
    $mysqli = $obj->connection();

    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }

    // Check if user is logged in - redirect to login if not
    if(!isset($_SESSION["CustomerId"]) || empty($_SESSION["CustomerId"])){
        header("Location: login.php");
        exit();
    }

    // Check if combo_id is provided
    if(!isset($_GET['combo_id']) || empty($_GET['combo_id'])){
        header("Location: combos.php");
        exit();
    }

    $combo_id = $_GET['combo_id'];

    // Fetch combo details
    $combo_query = "SELECT * FROM combo_details_view WHERE combo_id = ? AND is_active = TRUE";
    $combo_stmt = $mysqli->prepare($combo_query);

    if (!$combo_stmt) {
        throw new Exception("Failed to prepare combo query: " . $mysqli->error);
    }

    $combo_stmt->bind_param("s", $combo_id);
    $combo_stmt->execute();
    $combo_result = $combo_stmt->get_result();

    if($combo_result->num_rows == 0){
        $combo_stmt->close();
        header("Location: combos.php");
        exit();
    }

    $combo = $combo_result->fetch_assoc();
    $combo_stmt->close();
} catch (Exception $e) {
    error_log("Combo checkout error: " . $e->getMessage());
    header("Location: combos.php");
    exit();
}

// Get customer address data
$customerId = $_SESSION["CustomerId"];
$addressData = array();
$customerData = array();

try {
    // Try to get customer address
    $addressQuery = "SELECT * FROM customer_address WHERE CustomerId = ? LIMIT 1";
    $addressStmt = $mysqli->prepare($addressQuery);
    if ($addressStmt) {
        $addressStmt->bind_param("i", $customerId);
        $addressStmt->execute();
        $addressResult = $addressStmt->get_result();
        if ($addressResult->num_rows > 0) {
            $addressData = array($addressResult->fetch_assoc());
        }
        $addressStmt->close();
    }

    // Get customer details if address not found or as fallback
    $customerQuery = "SELECT CustomerId, Name as CustomerName, Email, MobileNo as Phone FROM customer_master WHERE CustomerId = ? LIMIT 1";
    $customerStmt = $mysqli->prepare($customerQuery);
    if ($customerStmt) {
        $customerStmt->bind_param("i", $customerId);
        $customerStmt->execute();
        $customerResult = $customerStmt->get_result();
        if ($customerResult->num_rows > 0) {
            $customerData = array($customerResult->fetch_assoc());
        }
        $customerStmt->close();
    }
} catch (Exception $e) {
    error_log("Customer data fetch error: " . $e->getMessage());
    // Continue with empty data - user can fill manually
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Combo Checkout - My Nutrify</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="image/favicon.png" type="image/x-icon" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="css/color.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <!-- header start -->
    <?php include("components/header.php"); ?>
    <!-- header end -->
    
    <!-- Full-screen black transparent overlay with loader -->
    <div id="overlay"
        style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 9999; text-align: center; padding-top: 20%;">
        <div id="loader">
            <img src="image/Spinner.gif" alt="Loading..." />
            <p style="color: white; font-size: 20px;">Processing your combo order...</p>
        </div>
    </div>
    
    <!-- combo checkout start -->
    <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="checkout-area">
                        <div class="billing-area">
                            <form>
                                <h2>Billing & Shipping details</h2>
                                <div class="billing-form">
                                    <ul class="input-2">
                                        <li>
                                            <label>Full name</label>
                                            <input type="text" name="name" id="name" placeholder="Enter your full name" required
                                                value="<?php echo !empty($addressData[0]['CustomerName']) ? htmlspecialchars($addressData[0]['CustomerName']) : (!empty($customerData[0]['CustomerName']) ? htmlspecialchars($customerData[0]['CustomerName']) : ''); ?>">
                                        </li>
                                        <li>
                                            <label>Email address</label>
                                            <input type="email" name="email" id="email" placeholder="Enter your email" required
                                                value="<?php echo !empty($addressData[0]['Email']) ? htmlspecialchars($addressData[0]['Email']) : (!empty($customerData[0]['Email']) ? htmlspecialchars($customerData[0]['Email']) : ''); ?>">
                                        </li>
                                        <li>
                                            <label>Phone number</label>
                                            <input type="tel" name="phone" id="phone" placeholder="Enter your phone number" required
                                                value="<?php echo !empty($addressData[0]['Phone']) ? htmlspecialchars($addressData[0]['Phone']) : (!empty($customerData[0]['Phone']) ? htmlspecialchars($customerData[0]['Phone']) : ''); ?>">
                                        </li>
                                        <li>
                                            <label>Address</label>
                                            <input type="text" name="address" id="address" placeholder="Enter your address" required
                                                value="<?php echo !empty($addressData[0]['Address']) ? htmlspecialchars($addressData[0]['Address']) : ''; ?>">
                                        </li>
                                        <li>
                                            <label>Landmark</label>
                                            <input type="text" name="landmark" id="landmark" placeholder="Enter landmark"
                                                value="<?php echo !empty($addressData[0]['Landmark']) ? htmlspecialchars($addressData[0]['Landmark']) : ''; ?>">
                                        </li>
                                        <li>
                                            <label>City</label>
                                            <input type="text" name="city" id="city" placeholder="Enter your city" required
                                                value="<?php echo !empty($addressData[0]['City']) ? htmlspecialchars($addressData[0]['City']) : ''; ?>">
                                        </li>
                                        <li>
                                            <label>State</label>
                                            <input type="text" name="state" id="state" placeholder="Enter your state" required
                                                value="<?php echo !empty($addressData[0]['State']) ? htmlspecialchars($addressData[0]['State']) : ''; ?>">
                                        </li>
                                        <li>
                                            <label>Pin code</label>
                                            <input type="text" name="pincode" id="pincode" placeholder="Enter your pin code" required
                                                value="<?php echo !empty($addressData[0]['PinCode']) ? htmlspecialchars($addressData[0]['PinCode']) : ''; ?>">
                                        </li>
                                    </ul>
                                </div>
                            </form>
                        </div>
                        <div class="order-area">
                            <div class="check-pro">
                                <h2>Your Combo Order</h2>
                                <ul class="check-ul">
                                    <li class="checkout-item combo-checkout-item" 
                                        data-combo-id="<?php echo htmlspecialchars($combo['combo_id']); ?>"
                                        data-combo-name="<?php echo htmlspecialchars($combo['combo_name']); ?>"
                                        data-combo-price="<?php echo $combo['combo_price']; ?>"
                                        data-product1-id="<?php echo $combo['product1_id']; ?>"
                                        data-product2-id="<?php echo $combo['product2_id']; ?>"
                                        data-product1-name="<?php echo htmlspecialchars($combo['product1_name']); ?>"
                                        data-product2-name="<?php echo htmlspecialchars($combo['product2_name']); ?>">
                                        
                                        <div class="pro-img">
                                            <img src="<?php echo htmlspecialchars($combo['product1_image']); ?>" alt="<?php echo htmlspecialchars($combo['product1_name']); ?>">
                                        </div>
                                        <div class="pro-name">
                                            <h4><?php echo htmlspecialchars($combo['combo_name']); ?></h4>
                                            <p>Includes: <?php echo htmlspecialchars($combo['product1_name']); ?> + <?php echo htmlspecialchars($combo['product2_name']); ?></p>
                                            <span>₹<?php echo number_format($combo['combo_price'], 2); ?></span>
                                            <?php if($combo['savings'] > 0): ?>
                                                <small style="color: green;">You save ₹<?php echo number_format($combo['savings'], 2); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="qty-inc-dec">
                                            <div class="quantity-box">
                                                <div class="quantity">
                                                    <button type="button" class="minus">-</button>
                                                    <input type="text" value="1" readonly>
                                                    <button type="button" class="plus">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pro-price">
                                            <span class="item-total">₹<?php echo number_format($combo['combo_price'], 2); ?></span>
                                        </div>
                                    </li>
                                </ul>
                                
                                <!-- Order Summary -->
                                <ul class="order-summary">
                                    <li class="subtotal-line">
                                        <span>Subtotal</span>
                                        <span id="subtotal">₹<?php echo number_format($combo['combo_price'], 2); ?></span>
                                    </li>
                                    <li class="delivery-line">
                                        <span>Delivery Charges</span>
                                        <span id="delivery-charges">
                                            <?php 
                                            $delivery_charge = ($combo['combo_price'] >= 399) ? 0 : 40;
                                            echo ($delivery_charge == 0) ? 'FREE' : '₹' . $delivery_charge;
                                            ?>
                                        </span>
                                    </li>
                                    <li class="total-line">
                                        <span><strong>Total</strong></span>
                                        <span id="final-total"><strong>₹<?php echo number_format($combo['combo_price'] + $delivery_charge, 2); ?></strong></span>
                                    </li>
                                </ul>
                                
                                <!-- Payment Methods -->
                                <div class="payment-method">
                                    <h3>Payment Method</h3>
                                    <div class="payment-options">
                                        <label class="payment-option">
                                            <input type="radio" name="payment_method" value="COD" checked>
                                            <span class="checkmark"></span>
                                            Cash on Delivery
                                        </label>
                                        <label class="payment-option">
                                            <input type="radio" name="payment_method" value="Online">
                                            <span class="checkmark"></span>
                                            Online Payment (Razorpay)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="order-place">
                                    <button type="button" id="place-combo-order" class="btn btn-style1">Place Combo Order</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- combo checkout end -->
    
    <!-- footer start -->
    <?php include("components/footer.php"); ?>
    <!-- footer end -->
    
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
    let comboOrderData = {};

    // Quantity controls
    $(document).on('click', '.plus', function() {
        const input = $(this).siblings('input');
        const currentVal = parseInt(input.val()) || 1;
        const newVal = currentVal + 1;
        input.val(newVal);
        updateComboTotals();
    });

    $(document).on('click', '.minus', function() {
        const input = $(this).siblings('input');
        const currentVal = parseInt(input.val()) || 1;
        if (currentVal > 1) {
            const newVal = currentVal - 1;
            input.val(newVal);
            updateComboTotals();
        }
    });

    // Update combo totals
    function updateComboTotals() {
        const quantity = parseInt($('.quantity input').val()) || 1;
        const comboPrice = parseFloat($('.combo-checkout-item').data('combo-price')) || 0;
        const subtotal = comboPrice * quantity;

        // Update item total
        $('.item-total').text('₹' + subtotal.toFixed(2));

        // Update subtotal
        $('#subtotal').text('₹' + subtotal.toFixed(2));

        // Calculate delivery charges
        const deliveryCharge = (subtotal >= 399) ? 0 : 40;
        $('#delivery-charges').text(deliveryCharge === 0 ? 'FREE' : '₹' + deliveryCharge);

        // Update final total
        const finalTotal = subtotal + deliveryCharge;
        $('#final-total').text('₹' + finalTotal.toFixed(2));
    }

    // Place combo order
    $('#place-combo-order').click(function() {
        // Validate form
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        const address = $('#address').val().trim();
        const city = $('#city').val().trim();
        const state = $('#state').val().trim();
        const pincode = $('#pincode').val().trim();

        if (!name || !email || !phone || !address || !city || !state || !pincode) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill in all required fields.',
                confirmButtonColor: '#ec6504'
            });
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                confirmButtonColor: '#ec6504'
            });
            return;
        }

        // Phone validation
        if (phone.length < 10) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Phone',
                text: 'Please enter a valid phone number.',
                confirmButtonColor: '#ec6504'
            });
            return;
        }

        // Get combo details
        const comboItem = $('.combo-checkout-item');
        const quantity = parseInt($('.quantity input').val()) || 1;
        const finalTotal = parseFloat($('#final-total').text().replace('₹', '').replace(',', '')) || 0;
        const selectedPaymentMethod = $('input[name="payment_method"]:checked').val();

        // Prepare combo order data
        comboOrderData = {
            name: name,
            email: email,
            phone: phone,
            address: address,
            landmark: $('#landmark').val().trim(),
            city: city,
            state: state,
            pincode: pincode,
            final_total: finalTotal,
            paymentMethod: selectedPaymentMethod,
            CustomerId: <?php echo $_SESSION['CustomerId']; ?>,
            customerType: 'Registered',
            combo: {
                combo_id: comboItem.data('combo-id'),
                combo_name: comboItem.data('combo-name'),
                combo_price: comboItem.data('combo-price'),
                product1_id: comboItem.data('product1-id'),
                product2_id: comboItem.data('product2-id'),
                product1_name: comboItem.data('product1-name'),
                product2_name: comboItem.data('product2-name'),
                quantity: quantity
            }
        };

        // Show overlay
        $('#overlay').show();

        // Process order based on payment method
        if (selectedPaymentMethod === 'COD') {
            sendComboOrderData();
        } else {
            initiateComboRazorpayPayment();
        }
    });

    // Send COD combo order
    function sendComboOrderData() {
        console.log("Sending combo COD order data:", comboOrderData);

        fetch("exe_files/combo_place_order_cod.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(comboOrderData)
        })
        .then(response => {
            console.log("Response status:", response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            console.log("Raw response:", text);
            try {
                const data = JSON.parse(text);
                $('#overlay').hide();

                if (data.response === "S") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed Successfully!',
                        text: `Your combo order ${data.order_id} has been placed successfully.`,
                        confirmButtonColor: '#ec6504'
                    }).then(() => {
                        window.location.href = 'account.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Order Failed',
                        text: data.message || 'Failed to place combo order. Please try again.',
                        confirmButtonColor: '#ec6504'
                    });
                }
            } catch (e) {
                console.error("JSON parse error:", e);
                $('#overlay').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your order. Please try again.',
                    confirmButtonColor: '#ec6504'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            $('#overlay').hide();
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to connect to server. Please check your internet connection and try again.',
                confirmButtonColor: '#ec6504'
            });
        });
    }

    // Initiate Razorpay payment for combo
    function initiateComboRazorpayPayment() {
        console.log("Initiating Razorpay payment for combo:", comboOrderData);

        fetch("exe_files/combo_place_order_online.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(comboOrderData)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Backend response:", data);
            if (data?.response === "S" && data?.payment_status === "Pending") {
                const options = {
                    "key": "rzp_live_DJ1mSUEz1DK4De",
                    "amount": data.amount * 100,
                    "currency": "INR",
                    "order_id": data.transaction_id,
                    "name": "My Nutrify",
                    "description": "Combo Order Payment",
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
                    "handler": function (response) {
                        console.log("Payment successful:", response);
                        $('#overlay').hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful!',
                            text: `Your combo order ${data.order_id} has been placed successfully.`,
                            confirmButtonColor: '#ec6504'
                        }).then(() => {
                            window.location.href = 'account.php';
                        });
                    },
                    "modal": {
                        "ondismiss": function() {
                            $('#overlay').hide();
                            console.log("Payment cancelled by user");
                        }
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
                $('#overlay').hide();
            } else {
                $('#overlay').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Setup Failed',
                    text: data.message || 'Failed to setup payment. Please try again.',
                    confirmButtonColor: '#ec6504'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            $('#overlay').hide();
            Swal.fire({
                icon: 'error',
                title: 'Payment Error',
                text: 'Failed to setup payment. Please try again.',
                confirmButtonColor: '#ec6504'
            });
        });
    }
    </script>

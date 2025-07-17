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


.loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 1);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeOut 1.5s ease-out 3s forwards; /* Fades out after 3 seconds */
}

.loader-img {
    width: 150px;
    height: 150px;
    animation: spin 2s linear infinite;
}
@keyframes fadeOut {
    0% {
        opacity: 1;
        visibility: visible;
    }
    100% {
        opacity: 0;
        visibility: hidden;
    }
}

    </style>
</head>
<!--</head>-->
<body class="home-1">
<div class="loading">
        <div class="text-align">
            <img class="loader-img" src="image/preloader.gif"/>
        </div>
    </div>

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
            alert("All fields are required. Please fill in all the fields.");
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
                    alert(response.msg);
                    location.reload();
                } else {
                    alert("An error occurred while saving the address.");
                }
            },
            error: function (xhr, status, error) {
                alert("AJAX Error: " + status + " - " + error);
                console.error(status +"-"+ error);
                alert("A server error occurred. Please try again later.");
            },
            complete: function () {
                $("#submitBtn").prop("disabled", false).text("Submit");
            },
        });
    });
});

$('#editAddressBtn').on('click', function() {
    // Enable all inputs within the address form
    $('#addressForm input').prop('disabled', false);
});


</script>
<script>
    // Hide loading screen when the page is fully loaded
    $(window).on("load", function() {
        $(".loading").fadeOut(500, function() {
            $(".content").fadeIn(500);
        });
    });

    // Show loader on AJAX requests
    $(document).ajaxStart(function() {
        $(".loading").fadeIn();
    }).ajaxStop(function() {
        $(".loading").fadeOut();
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
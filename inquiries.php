<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();
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
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- animation -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <!-- style -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }



    .success {
        background: #d4edda;
        color: #155724;
    }

    .error {
        background: #f8d7da;
        color: #721c24;
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



    .form-select {
        color: #757575;
        font-size: 14px;
        /* Decreased font size */
        padding: 10px;
        border: 1px solid #EEEEEE;
    }

    .inquiryForm select {
        padding-top: 10px;
    }
    </style>
</head>

<body class="home-1">

    <div class="loading">
        <div class="text-align">
            <img class="loader-img" src="image/preloader.gif" />
        </div>
    </div>
    <!-- header start -->
    <?php include("components/header.php"); ?>
    <!-- header end -->

    <!-- login start -->
    <section class="section-tb-padding">
        <div class="row">
            <div class="col">
                <div class="register-area">
                    <div class="register-box">
                        <h1>Inquiry Form</h1>
                        <p>Dealer/Distributor</p>
                        <form id="inquiryForm" method="POST">
                            <input type="text" name="full_name" id="full_name" placeholder="Enter Full Name" required>
                            <input type="tel" name="mobile" id="mobile" placeholder="Enter Your Mobile Number" required>
                            <input type="email" name="email" id="email" placeholder="Enter Your Email" required>
                            <input type="text" name="company_name" id="company_name" placeholder="Company Name">
                            <select class="form-select" name="interested_product" required style="margin-top: 20px;">
                                <option value="">Select Product</option>
                                <option value="All">All</option>
                                <option value="My Nutrify's Special Amla High Fiber Juice">My Nutrify's Special Amla
                                    High Fiber Juice</option>
                                <option value="MY NUTRIFY'S HERBAL & AYURVEDA She Care Juice">MY NUTRIFY'S HERBAL &
                                    AYURVEDA She Care Juice</option>
                                <option value="MY NUTRIFY'S HERBAL & AYURVEDA Thyro Balance Juice">MY NUTRIFY'S HERBAL &
                                    AYURVEDA Thyro Balance Juice </option>
                                <option value="My Nutrify's BP Care Juice">My Nutrify's BP Care Juice</option>
                                <option value="Apple Cider Vinegar">Apple Cider Vinegar </option>
                                <option value="My Nutrify's HERBAL & AYURVEDA Cholesterol Care Juice">My Nutrify's
                                    HERBAL & AYURVEDA Cholesterol Care Juice </option>
                                <option value="My Nutrify's Diabic Care Juice">My Nutrify's Diabic Care Juice</option>
                                <option value="My Nutrify's Karela Jamun Mix Juice">My Nutrify's Karela Jamun Mix Juice
                                </option>
                                <option value="My Nutrify's Wheatgrass Juice">My Nutrify's Wheatgrass Juice</option>
                                <option value="My Nutrify Herbal & Ayurveda’s Pure Shilajit">My Nutrify Herbal &
                                    Ayurveda’s Pure Shilajit</option>
                            </select>
                            <input type="text" name="city" id="city" placeholder="Enter City" required>
                            <input type="text" name="message" id="message" placeholder="Enter Message">
                            <button type="submit" class="btn btn-style3"
                                style="display: block; margin-left: auto; margin-right: auto; margin-top: 20px;">
                                Submit Inquiry
                            </button>

                        </form>
                        <div class="inquiries-response-message" id="responseMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- login end -->
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
    <script>
    // JavaScript Validation and Submission
    $(document).ready(function() {
        $('#inquiryForm').on('submit', function(e) {
            e.preventDefault();

            // Form Validation
            const name = $('#full_name').val().trim();
            const mobile = $('#mobile').val().trim();
            const email = $('#email').val().trim();
            const product = $('[name="interested_product"]').val();

            // Name validation
            if (!/^[A-Za-z\s]+$/.test(name)) {
                showError('Invalid Name', 'Name must contain only letters and spaces');
                return;
            }

            // Mobile validation (Indian numbers)
            if (!/^[6-9]\d{9}$/.test(mobile)) {
                showError('Invalid Mobile', 'Please enter a valid 10-digit mobile number');
                return;
            }

            // Email validation
            if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
                showError('Invalid Email', 'Please enter a valid email address');
                return;
            }

            // Product selection validation
            if (!product) {
                showError('Product Required', 'Please select a product from the list');
                return;
            }

            // Submit form if validations pass
            submitForm();
        });
    });

    function submitForm() {
        const formData = {
            full_name: $('#full_name').val(),
            mobile: $('#mobile').val(),
            email: $('#email').val(),
            company_name: $('#company_name').val(),
            interested_product: $('[name="interested_product"]').val(),
            city: $('#city').val(),
            message: $('#message').val()
        };

        $.ajax({
    url: "cms/inquiries_product.php",
    type: "POST",
    data: formData,
    dataType: 'json',
    beforeSend: function() {
        $('button[type="submit"]').prop('disabled', true);
    },
    success: function(response) {
        console.log(response); // Log success response

        if (response.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.message,
                confirmButtonColor: '#ec6504'
            });
            $('#inquiryForm')[0].reset();
        } else {
            showError('Submission Error', response.message);
        }
    },
    error: function(xhr, status, error) {
        console.log("Error Details: ", xhr.responseText); // Log error response
        showError('Error', 'An error occurred while submitting the form');
    },
    complete: function() {
        $('button[type="submit"]').prop('disabled', false);
    }
});

    }

    function showError(title, text) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#ec6504'
        });
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
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
    <!-- full width override -->
    <link rel="stylesheet" type="text/css" href="css/full-width-override.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 1);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loader-img {
    width: 150px;
    height: 150px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
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
<body class="home-1">
    <div class="loading">
        <div class="text-align">
            <img class="loader-img" src="image/preloader.gif"/>
        </div>
    </div>
    

    <!-- header start -->
       <?php include("components/header.php"); ?>
        <!-- header end -->
        
    <!-- login start -->
    <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="register-area">
                        <div class="register-box">
                            <h1>Create account</h1>
                            <p>Please register below account detail</p>
                            <form id="frmData" method="post" enctype="multipart/form-data">
                                <input type="text" name="name" placeholder="Full Name" required>
                                <input type="text" name="mobile_no" placeholder="Mobile Number" required>
                                <input type="email" name="email" placeholder="Email" required>
                                <input type="password" name="password" placeholder="Password" required minlength="6">
                                <input type="password" name="confirm_pass" placeholder="Confirm Password" required minlength="6" oninput="check(this)">
                                <button style="margin: 10px auto; display: block;" type="submit" id="submitForm" class="btn-style1">Create</button>
                            </form>

                        </div>
                        <div class="register-account">
                            <h4>Already an account holder?</h4>
                            <a href="login.php" class="ceate-a">Log in</a>
                            <div class="register-info">
                                <a href="terms-conditions.html" class="terms-link"><span>*</span> Terms & conditions.</a>
                                <p>Your privacy and security are important to us. For more information on how we use your data read our <a href="privacy-policy.html">privacy policy</a></p>
                            </div>
                        </div>
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
    document.getElementById('frmData').addEventListener('submit', function(e) {
        e.preventDefault();

        // Show the loader
        document.querySelector('.loading').style.display = 'flex'; // Show the loader

        // Get form fields
        let name = document.querySelector('[name="name"]').value;
        let mobileNo = document.querySelector('[name="mobile_no"]').value;
        let email = document.querySelector('[name="email"]').value;
        let password = document.querySelector('[name="password"]').value;
        let confirmPass = document.querySelector('[name="confirm_pass"]').value;

        // Basic validation for empty fields
        if (!name || !mobileNo || !email || !password || !confirmPass) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Form',
                text: 'Please fill in all fields.',
                confirmButtonColor: '#ec6504',
            });
            document.querySelector('.loading').style.display = 'none'; // Hide the loader
            return;
        }

        // Confirm password validation
        if (password !== confirmPass) {
            Swal.fire({
                icon: 'warning',
                title: 'Password Mismatch',
                text: "Passwords do not match.",
                confirmButtonColor: '#ec6504',
            });
            document.querySelector('.loading').style.display = 'none'; // Hide the loader
            return;
        }

        // Create FormData object to send the form data
        let formData = new FormData(this);

        // Send form data using Fetch API
        fetch('exe_files/exe_register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Assuming the server returns JSON
            .then(data => {
                document.querySelector('.loading').style.display = 'none'; // Hide the loader

                if (data.response === 'S') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: 'You can now verify your account',
                        confirmButtonColor: '#ec6504',
                    }).then(() => {
                        window.location.href = "verify_code.php";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: data.msg,
                        confirmButtonColor: '#ec6504',
                    });
                }
            })
            .catch(error => {
                document.querySelector('.loading').style.display = 'none'; // Hide the loader
                Swal.fire({
                    icon: 'error',
                    title: 'Error Occurred',
                    text: 'An error occurred: ' + error,
                    confirmButtonColor: '#ec6504',
                });
            });
    });

    // Confirm password validation function
    function check(input) {
        if (input.value !== document.querySelector('[name="password"]').value) {
            input.setCustomValidity("Passwords don't match");
        } else {
            input.setCustomValidity('');
        }
    }
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
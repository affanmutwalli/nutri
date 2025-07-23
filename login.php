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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>

    </style>
</head>
<body class="home-1">
    <!-- top notificationbar start -->

    <!-- header start -->
       <?php include("components/header.php"); ?>
        <!-- header end -->
        
    <!-- login start -->
    <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="login-area">
                        <div class="login-box">
                            <h1>Login</h1>
                            <p>Please login below account detail</p>
                            <form id="loginForm" method="post" enctype="multipart/form-data">
                                <label>Email address</label>
                                <input type="text" name="email" placeholder="Email address">
                                <label>Password</label>
                                <div style="position: relative;">
                                    <input type="password" name="password" placeholder="Password" id="password">
                                    <i id="togglePassword" class="fa fa-eye" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                </div>
                                <button style="margin: 10px auto; display: block;" type="submit" class="btn-style1">Sign in</button>
                                <a href="forgot-password.php" class="re-password">Forgot your password?</a>
                            </form>

                        </div>
                        <div class="login-account">
                            <h4>Don't have an account?</h4>
                            <a href="register.php" class="ceate-a">Create account</a>
                            <div class="login-info">
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
        <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
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
                    dataType: 'json', // Expect JSON response
                    data: {
                        email: email,
                        password: password
                    },
                    enctype: 'application/x-www-form-urlencoded', // Specify the enctype
                    success: function (response) {
                        // Debug: Log the response
                        console.log("Login response:", response);

                        // Response is already parsed as JSON by jQuery due to dataType: 'json'
                        const res = response;

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
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        console.error("Response text:", xhr.responseText);
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
//gff
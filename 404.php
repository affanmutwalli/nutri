<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();
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
    animation: fadeOut 1.5s ease-out 3s forwards;
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

    
        <!-- Order complete start -->
        <section class="section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="fnf-area">
                            <h1 class="fnf-title">4<span class="color-font"> 04</span></h1>
                            <p>Oops, The Page you are looking for can't be found!</p>
                            <a href="index.php" class="back-home">Go to home</a>
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
    // Add an event listener to the input field to detect the Enter key
    document.getElementById('Code').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();  // Prevent form submission if inside a form
            verify();  // Call the verify function when Enter is pressed
        }
    });

    function verify() {
        // Get the values of the input fields
        var name = $("#customer_name").val();
        var email = $("#email").val();
        var mobile = $("#mobile_number").val();
        var code = $("#Code").val();

        // Validate Customer Name (Only letters and spaces)
        var namePattern = /^[A-Za-z\s]+$/;
        if (!name.match(namePattern)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Name',
                text: 'Name must contain only letters and spaces.',
                confirmButtonColor: '#ec6504',
            });
            return; // Exit the function if name is invalid
        }

        // Validate Mobile Number (Only numeric and exactly 10 digits, starting with 7, 8, or 9)
        var mobilePattern = /^[789][0-9]{9}$/;  // Ensure it starts with 7, 8, or 9 and has exactly 10 digits
        if (!mobile.match(mobilePattern)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Mobile Number',
                text: 'Please enter a valid mobile number.',
                confirmButtonColor: '#ec6504',
            });
            return; // Exit the function if mobile is invalid
        }

        // Validate Email (if provided, ensure it's in proper email format)
        if (email && !validateEmail(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                confirmButtonColor: '#ec6504',
            });
            return; // Exit the function if email is invalid
        }

        // Check if the code field is not empty
        if (code == "") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a code.',
                confirmButtonColor: '#ec6504',
            });
            return; // Exit the function if code is empty
        }

        // Now, trigger the AJAX request
        sendRequest(); // Call the function to send AJAX request
    }

    // Email validation function
    function validateEmail(email) {
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        return email.match(emailPattern);
    }

    // Function to handle AJAX request
    function sendRequest() {
        var code = $("#Code").val();
        var name = $("#customer_name").val();
        var email = $("#email").val();
        var mobile = $("#mobile_number").val();

        $.ajax({
            url: "cms/authenticate_product.php",  // The backend script that will process the request
            type: "POST",
            data: { 
                Code: code, 
                customer_name: name, 
                email: email, 
                mobile_number: mobile 
            },  // Send all the form data
            beforeSend: function() {
                // Optional: Show a loading spinner or message before the request is sent
                console.log("Sending request...");
            },
            success: function(response) {
                // Parse the JSON response
                var jsonResponse = JSON.parse(response);
                
                // Check the 'response' key in the JSON response
                if (jsonResponse.response === "S") {
                    // Success: Genuine product
                    Swal.fire({
                        icon: 'success',
                        title: 'Genuine Product',
                        text: jsonResponse.msg,  // "Genuine Product."
                        confirmButtonColor: '#ec6504',  // Custom button color
                    });
                } else if (jsonResponse.response === "E") {
                    // Error: Invalid code or empty input
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Code or Invalid Product',
                        text: jsonResponse.msg,  // "The Code Is Invalid, No Product Found" or "Please Enter Code"
                        confirmButtonColor: '#ec6504',  // Custom button color
                    });
                }
            },
            error: function(xhr, status, error) {
                // Handle errors
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                    confirmButtonColor: '#ec6504',  // Custom button color
                });
            }
        });
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
})(window,document,'script','dataLayer','GTM-XXXXXX');

dataLayer.push({event: "gtm.load", ...})
{
  event: "gtm.load",
  gtm: {uniqueEventId: 13, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.dom", ...})

{
  event: "gtm.dom",
  gtm: {uniqueEventId: 12, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.load", ...})
{
  event: "gtm.load",
  gtm: {uniqueEventId: 11, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.js", ...})
{
  event: "gtm.js",
  gtm: {uniqueEventId: 10, start: 1748177630404, priorityId: undefined}
}
{
  event: "gtm.js",
  gtm: {uniqueEventId: 3, start: 1748177630404, priorityId: undefined}
}
{event: "gtm.init", gtm: {uniqueEventId: 2}}
{event: "gtm.init_consent", gtm: {uniqueEventId: 1}}
</script>
</body>
</html>
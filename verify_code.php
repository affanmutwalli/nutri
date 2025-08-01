<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();
if(isset($_SESSION["Email"])) {
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
   
</head>
<!--</head>-->
<body class="home-1">

    <!-- header start -->
        <?php include("components/header.php") ?>
        <!-- header end -->

    <!-- login start -->
    <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="register-area">
                        <div class="register-box">
                            <h1>Verify OTP</h1>
                            <p>Please enter OTP below to verify</p>
                            <form role="form" id="frmData" enctype="multipart/form-data">
                                <!-- OTP  -->
                                <input type="text" name="OTP" id="OTP" placeholder="Enter OTP" required pattern="^\d{6}$" title="OTP must be exactly 6 digits">
                                
                                <!-- Submit Button -->
                                <button type="button" onClick="verify();" class="btn btn-style3" style="display: block; margin-left: auto; margin-right: auto; margin-top: 20px;">Verify</button>
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
    document.getElementById('OTP').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();  // Prevent form submission if inside a form
        verify();  // Call the verify function when Enter is pressed
    }
});

function verify() {
    // Get the values of the input fields
    var otp = $("#OTP").val();
    var email = "<?php echo $_SESSION['Email']; ?>"; // Ensure this is properly embedded into the page

    // Check if the OTP field is not empty
    if (otp == "") {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter OTP.',
            confirmButtonColor: '#ec6504',
        });
        return; // Exit the function if OTP is empty
    }

    // Now, trigger the AJAX request
    sendRequest(otp, email); // Call the function to send AJAX request
}

// Function to handle AJAX request
function sendRequest(otp, email) {
    $.ajax({
        url: "exe_files/verify_otp_simple.php",  // The backend script that will process the request
        type: "POST",
        dataType: "json",  // Expect JSON response
        data: {
            OTP: otp,
            email: email
        },  // Send all the form data
        beforeSend: function() {
            // Optional: Show a loading spinner or message before the request is sent
            console.log("Sending request...");
        },
        success: function(response) {
            console.log("OTP verification response:", response);

            // Response is already parsed as JSON by jQuery due to dataType: 'json'
            const jsonResponse = response;
            
            // Check the 'response' key in the JSON response
            if (jsonResponse.response === "S") {
                // Success: OTP verified successfully
                Swal.fire({
                    icon: 'success',
                    title: 'Account Verified Successfully',
                    text: jsonResponse.msg,  // "OTP verified successfully. Account activated."
                    confirmButtonColor: '#ec6504',  // Custom button color
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the index page when the user clicks "OK"
                        window.location.href = "index.php";  // Change this to your index page URL
                    }
                });
            } else if (jsonResponse.response === "E") {
                // Error: Invalid OTP or account already active
                Swal.fire({
                    icon: 'error',
                    title: 'Verification Failed',
                    text: jsonResponse.msg,  // Display the error message
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
<?php }
else{
    header("Location: 404.php");
    exit(); 
} ?>
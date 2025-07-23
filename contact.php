<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// $FieldNames = array("CustomerId", "Name", "MobileNo", "IsActive");
// $ParamArray = [$_SESSION["CustomerId"]];
// $Fields = implode(",", $FieldNames);

// // Assuming MysqliSelect1 function handles the query correctly
// $customerData = $obj->MysqliSelect1("SELECT $Fields FROM customer_master WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);
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
    <!-- full width override -->
    <link rel="stylesheet" type="text/css" href="css/full-width-override.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
   
</head>
<!--</head>-->
<body class="home-1">

    <!-- header start -->
        <?php include("components/header.php") ?>
        <!-- header end -->

       <!-- Page Title Section -->
       <section class="page-title" style="background: #f8f9fa; padding: 40px 0; border-bottom: 1px solid #eee;">
           <div class="container">
               <div class="row">
                   <div class="col-12">
                       <nav aria-label="breadcrumb">
                           <ol class="breadcrumb" style="background: none; padding: 0; margin: 0; font-size: 14px;">
                               <li class="breadcrumb-item">
                                   <a href="index.php" style="color: #666; text-decoration: none;">Home</a>
                               </li>
                               <li class="breadcrumb-item active" aria-current="page" style="color: #333;">
                                   Contact Us
                               </li>
                           </ol>
                       </nav>
                   </div>
               </div>
           </div>
       </section>

       <!-- Row 1: Company Information -->
       <section class="company-info-section" style="padding: 60px 0 40px 0; background: #ffffff;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-lg-8 col-md-10">
                       <div class="company-info text-center">
                           <h2 style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 2px;">NUTRIFY</h2>

                           <div style="margin-bottom: 40px;">
                               <p style="color: #666; line-height: 1.8; margin-bottom: 30px; font-size: 18px;">
                                   S.NO.31/32, 1st Floor,<br>
                                   Old Mumbai Pune Road,<br>
                                   Dapoli (Maharashtra)<br>
                                   Pune - 411012
                               </p>
                           </div>

                           <div style="margin-bottom: 30px;">
                               <p style="color: #333; font-weight: 600; margin-bottom: 8px; font-size: 18px;">
                                   <strong>Helpline:</strong>
                               </p>
                               <a href="tel:+919834243754" style="color: #333; text-decoration: none; font-size: 18px; display: block; margin-bottom: 5px;">
                                   +91-9834243754
                               </a>
                               <p style="color: #999; font-size: 14px; font-style: italic; margin: 0;">
                                   (8:30 AM to 6:30 PM Mon. to Sat.)
                               </p>
                           </div>

                           <div style="margin-bottom: 30px;">
                               <p style="color: #333; font-weight: 600; margin-bottom: 8px; font-size: 18px;">
                                   <strong>WhatsApp:</strong>
                               </p>
                               <a href="tel:+919834243754" style="color: #333; text-decoration: none; font-size: 18px;">
                                   +91-9834243754
                               </a>
                           </div>

                           <div>
                               <p style="color: #333; font-weight: 600; margin-bottom: 8px; font-size: 18px;">
                                   <strong>Email:</strong>
                               </p>
                               <a href="mailto:support@mynutrify.com" style="color: #333; text-decoration: none; font-size: 18px;">
                                   support@mynutrify.com
                               </a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>

       <!-- Row 2: Contact Form -->
       <section class="contact-form-section" style="padding: 40px 0; background: #f8f9fa;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-lg-6 col-md-8">
                       <div class="contact-form-container">
                           <h2 style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 2px; text-align: center;">Contact us</h2>

                           <form id="contactForm" method="POST" action="process_contact.php">
                               <div style="margin-bottom: 25px;">
                                   <input type="text" name="name" id="name" placeholder="Name" required
                                          style="width: 100%; padding: 15px 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; background: #fff; color: #333;">
                               </div>

                               <div style="margin-bottom: 25px;">
                                   <input type="email" name="email" id="email" placeholder="Email" required
                                          style="width: 100%; padding: 15px 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; background: #fff; color: #333;">
                               </div>

                               <div style="margin-bottom: 25px;">
                                   <input type="tel" name="phone" id="phone" placeholder="Phone number" required
                                          style="width: 100%; padding: 15px 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; background: #fff; color: #333;">
                               </div>

                               <div style="margin-bottom: 25px;">
                                   <textarea name="message" id="message" rows="5" placeholder="Message" required
                                             style="width: 100%; padding: 15px 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; background: #fff; color: #333; resize: vertical; min-height: 120px;"></textarea>
                               </div>

                               <div class="text-center">
                                   <button type="submit" id="submitBtn"
                                           style="background: #333; color: white; padding: 15px 40px; border: none; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: background 0.3s ease;">
                                       <span id="btnText">Send</span>
                                       <span id="btnLoader" style="display: none;">
                                           <i class="fa fa-spinner fa-spin"></i> Sending...
                                       </span>
                                   </button>
                               </div>
                           </form>

                           <div style="margin-top: 20px; font-size: 12px; color: #999; text-align: center;">
                               This site is protected by hCaptcha and the hCaptcha
                               <a href="#" style="color: #999;">Privacy Policy</a> and
                               <a href="#" style="color: #999;">Terms of Service</a> apply.
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>

       <!-- Row 3: Google Map -->
       <section class="map-section" style="padding: 40px 0 60px 0; background: #ffffff;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-lg-10 col-md-12">
                       <div class="map-container">
                           <h2 style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 2px; text-align: center;">Find Us</h2>

                           <div style="position: relative; height: 450px; overflow: hidden; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                               <iframe
                                   src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d121015.92937807197!2d73.75064686738706!3d18.585405799454485!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x3bc2b9523b6d1891%3A0x147dd229780d8ca3!2sOld%20Mumbai%20-%20Pune%20Hwy%2C%20above%20tvs%20showroom%2C%20Sanjay%20Nagar%2C%20Phugewadi%2C%20Dapodi%2C%20Pimpri-Chinchwad%2C%20Maharashtra%20411012!3m2!1d18.5854237!2d73.8330486!5e0!3m2!1sen!2sin!4v1740136634459!5m2!1sen!2sin"
                                   width="100%" height="450" style="border:0; border-radius: 12px;" allowfullscreen="" aria-hidden="false" tabindex="0">
                               </iframe>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>

       <!-- We Are Also Available On Section -->
       <section class="available-on-section" style="padding: 40px 0; background: #f8f9fa; border-top: 1px solid #eee;">
           <div class="container">
               <div class="row">
                   <div class="col-12 text-center">
                       <h3 style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 25px;">We Are Also Available On:</h3>
                       <div class="platform-logos" style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 30px;">
                           <div style="display: flex; align-items: center; padding: 10px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                               <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/1024px-Amazon_icon.svg.png" alt="Amazon" style="height: 30px; margin-right: 10px;">
                               <span style="font-weight: 600; color: #333;">Amazon</span>
                           </div>
                           <div style="display: flex; align-items: center; padding: 10px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                               <img src="https://logos-world.net/wp-content/uploads/2020/11/Flipkart-Logo.png" alt="Flipkart" style="height: 30px; margin-right: 10px;">
                               <span style="font-weight: 600; color: #333;">Flipkart</span>
                           </div>
                           <div style="display: flex; align-items: center; padding: 10px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                               <img src="https://www.1mg.com/images/tata_1mg_logo.svg" alt="Tata 1mg" style="height: 30px; margin-right: 10px;">
                               <span style="font-weight: 600; color: #333;">Tata 1mg</span>
                           </div>
                           <div style="display: flex; align-items: center; padding: 10px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                               <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Snapdeal_logo.svg/1200px-Snapdeal_logo.svg.png" alt="Snapdeal" style="height: 30px; margin-right: 10px;">
                               <span style="font-weight: 600; color: #333;">Snapdeal</span>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>
        <!-- brand logo end -->
    
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

<!-- Krishna Ayurveda Style Contact Page CSS -->
<style>
/* Krishna Ayurveda Contact Page Styling */
.contact-section {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Breadcrumb Styling */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #999;
    font-weight: normal;
    padding: 0 8px;
}

.breadcrumb-item a:hover {
    color: #333;
    text-decoration: underline;
}

.company-info h2,
.contact-form-container h2 {
    font-family: inherit;
    font-weight: 700;
    color: #333;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 24px;
}

.company-info p,
.company-info a {
    font-family: inherit;
    line-height: 1.6;
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.company-info a:hover {
    color: #666;
}

/* Form Styling - Exact Krishna Ayurveda Style */
.contact-form-container input,
.contact-form-container textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background: #fff;
    color: #333;
    font-family: inherit;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.contact-form-container input:focus,
.contact-form-container textarea:focus {
    outline: none;
    border-color: #999;
}

.contact-form-container input::placeholder,
.contact-form-container textarea::placeholder {
    color: #999;
    font-size: 14px;
}

/* Submit Button - Krishna Ayurveda Style */
#submitBtn {
    background: #333 !important;
    color: white !important;
    padding: 12px 30px !important;
    border: none !important;
    border-radius: 4px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    transition: background 0.3s ease !important;
    font-family: inherit !important;
}

#submitBtn:hover {
    background: #555 !important;
}

#submitBtn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    background: #999 !important;
}

/* Vertical Row Layout Styling */
.company-info-section,
.contact-form-section,
.map-section {
    transition: all 0.3s ease;
}

/* Section Separators */
.company-info-section {
    border-bottom: 1px solid #eee;
}

.contact-form-section {
    border-bottom: 1px solid #eee;
}

/* Responsive Design for Vertical Layout */
@media (max-width: 768px) {
    .company-info-section,
    .contact-form-section,
    .map-section {
        padding: 30px 0 !important;
    }

    .company-info h2,
    .contact-form-container h2,
    .map-container h2 {
        font-size: 24px !important;
        margin-bottom: 25px !important;
        letter-spacing: 1px !important;
    }

    .company-info p {
        font-size: 16px !important;
    }

    .company-info div[style*="margin-bottom: 30px"] {
        margin-bottom: 20px !important;
    }

    .map-container div[style*="height: 450px"] {
        height: 300px !important;
    }

    .contact-form-container input,
    .contact-form-container textarea {
        padding: 12px 15px !important;
        font-size: 14px !important;
    }

    #submitBtn {
        padding: 12px 30px !important;
        font-size: 14px !important;
    }
}

@media (max-width: 480px) {
    .company-info h2,
    .contact-form-container h2,
    .map-container h2 {
        font-size: 20px !important;
    }

    .map-container div[style*="height: 450px"] {
        height: 250px !important;
    }
}

/* Remove any conflicting styles */
.contact-form-container form {
    margin: 0;
    padding: 0;
}

.contact-form-container div {
    margin-bottom: 20px;
}

.contact-form-container div:last-child {
    margin-bottom: 0;
}

/* hCaptcha notice styling */
.contact-form-container div:last-of-type {
    margin-top: 20px;
    font-size: 12px;
    color: #999;
    line-height: 1.4;
}

.contact-form-container div:last-of-type a {
    color: #999;
    text-decoration: underline;
}

.contact-form-container div:last-of-type a:hover {
    color: #666;
}
</style>

<!-- Contact Form JavaScript -->
<script>
$(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        // Form validation
        const name = $('#name').val().trim();
        const phone = $('#phone').val().trim();
        const email = $('#email').val().trim();
        const message = $('#message').val().trim();

        // Name validation
        if (!/^[A-Za-z\s]+$/.test(name)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Name',
                text: 'Name must contain only letters and spaces'
            });
            return;
        }

        // Phone validation (Indian numbers)
        if (!/^[6-9]\d{9}$/.test(phone)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone',
                text: 'Please enter a valid 10-digit mobile number'
            });
            return;
        }

        // Email validation
        if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address'
            });
            return;
        }

        // Submit form if validations pass
        submitContactForm();
    });

    function submitContactForm() {
        const formData = {
            name: $('#name').val(),
            phone: $('#phone').val(),
            email: $('#email').val(),
            subject: $('#subject').val(),
            message: $('#message').val()
        };

        // Show loading state
        $('#btnText').hide();
        $('#btnLoader').show();
        $('#submitBtn').prop('disabled', true);

        $.ajax({
            url: 'process_contact.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent!',
                        text: 'Thank you for contacting us. We will get back to you soon.',
                        confirmButtonColor: '#ec6504'
                    });
                    $('#contactForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Something went wrong. Please try again.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again.'
                });
            },
            complete: function() {
                // Reset button state
                $('#btnText').show();
                $('#btnLoader').hide();
                $('#submitBtn').prop('disabled', false);
            }
        });
    }
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
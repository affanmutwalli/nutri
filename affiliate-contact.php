<?php
session_start();

// Initialize database connection for header.php
require_once 'database/dbconnection.php';
$obj = new main();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Affiliate Partnership - Nutrify</title>
    <meta name="description" content="Join Nutrify's affiliate program and earn commissions by promoting our health and wellness products.">
    <meta name="keywords" content="affiliate, partnership, commission, health products, wellness, nutrify">

    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="image/favicon.ico">
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
    <!-- owl carousel -->
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
                                   Affiliate Partnership
                               </li>
                           </ol>
                       </nav>
                   </div>
               </div>
           </div>
       </section>

       <!-- Affiliate Hero Section -->
       <section class="affiliate-hero-section" style="padding: 80px 0; background: linear-gradient(135deg, #ec6504 0%, #ff8533 100%); color: white; text-align: center;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-lg-8">
                       <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Join Our Affiliate Program</h1>
                       <p style="font-size: 1.3rem; opacity: 0.9; margin-bottom: 30px; line-height: 1.6;">
                           Partner with Nutrify and earn attractive commissions by promoting our premium health and wellness products. 
                           Join thousands of successful affiliates worldwide!
                       </p>
                       <div class="benefits-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-top: 40px;">
                           <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                               <i class="fas fa-percentage" style="font-size: 2rem; margin-bottom: 10px;"></i>
                               <h4>Up to 15% Commission</h4>
                               <p style="margin: 0; opacity: 0.9;">Competitive rates on all products</p>
                           </div>
                           <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                               <i class="fas fa-chart-line" style="font-size: 2rem; margin-bottom: 10px;"></i>
                               <h4>Real-time Tracking</h4>
                               <p style="margin: 0; opacity: 0.9;">Monitor your earnings instantly</p>
                           </div>
                           <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                               <i class="fas fa-headset" style="font-size: 2rem; margin-bottom: 10px;"></i>
                               <h4>Dedicated Support</h4>
                               <p style="margin: 0; opacity: 0.9;">Personal affiliate manager</p>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>

       <!-- Row 1: Program Benefits -->
       <section class="program-benefits-section" style="padding: 60px 0; background: #ffffff;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-lg-10">
                       <div class="text-center" style="margin-bottom: 50px;">
                           <h2 style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 20px;">Why Partner With Nutrify?</h2>
                           <p style="font-size: 18px; color: #666; line-height: 1.6;">
                               We offer one of the most rewarding affiliate programs in the health and wellness industry
                           </p>
                       </div>
                       
                       <div class="row">
                           <div class="col-md-6 mb-4">
                               <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; height: 100%;">
                                   <i class="fas fa-star" style="color: #ec6504; font-size: 2.5rem; margin-bottom: 20px;"></i>
                                   <h4 style="color: #333; margin-bottom: 15px;">Premium Products</h4>
                                   <p style="color: #666; line-height: 1.6; margin: 0;">
                                       Promote high-quality, trusted health and wellness products with proven results and excellent customer satisfaction.
                                   </p>
                               </div>
                           </div>
                           <div class="col-md-6 mb-4">
                               <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; height: 100%;">
                                   <i class="fas fa-money-bill-wave" style="color: #ec6504; font-size: 2.5rem; margin-bottom: 20px;"></i>
                                   <h4 style="color: #333; margin-bottom: 15px;">Attractive Commissions</h4>
                                   <p style="color: #666; line-height: 1.6; margin: 0;">
                                       Earn up to 15% commission on every sale with performance-based bonuses and tier upgrades available.
                                   </p>
                               </div>
                           </div>
                           <div class="col-md-6 mb-4">
                               <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; height: 100%;">
                                   <i class="fas fa-tools" style="color: #ec6504; font-size: 2.5rem; margin-bottom: 20px;"></i>
                                   <h4 style="color: #333; margin-bottom: 15px;">Marketing Support</h4>
                                   <p style="color: #666; line-height: 1.6; margin: 0;">
                                       Get access to professional marketing materials, banners, product images, and promotional content.
                                   </p>
                               </div>
                           </div>
                           <div class="col-md-6 mb-4">
                               <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; height: 100%;">
                                   <i class="fas fa-clock" style="color: #ec6504; font-size: 2.5rem; margin-bottom: 20px;"></i>
                                   <h4 style="color: #333; margin-bottom: 15px;">Timely Payments</h4>
                                   <p style="color: #666; line-height: 1.6; margin: 0;">
                                       Receive your commissions on time every month with multiple payment options including bank transfer and UPI.
                                   </p>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>

       <!-- Row 2: Affiliate Application Form -->
       <section class="affiliate-form-section" style="padding: 60px 0; background: #f8f9fa;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-lg-8">
                       <div class="affiliate-form-wrapper" style="background: white; padding: 50px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                           <div class="form-header" style="text-align: center; margin-bottom: 40px;">
                               <h2 style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 15px;">Apply for Partnership</h2>
                               <p style="color: #666; font-size: 18px;">Fill out the form below and we'll get back to you within 24 hours</p>
                           </div>
                           
                           <form id="affiliateForm" method="POST" action="process_affiliate_contact.php">
                               <div class="row">
                                   <div class="col-md-6 mb-4">
                                       <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Full Name *</label>
                                       <input type="text" name="name" id="name" placeholder="Enter your full name" required 
                                              style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease;">
                                   </div>
                                   <div class="col-md-6 mb-4">
                                       <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Email Address *</label>
                                       <input type="email" name="email" id="email" placeholder="Enter your email address" required 
                                              style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease;">
                                   </div>
                               </div>
                               
                               <div class="row">
                                   <div class="col-md-6 mb-4">
                                       <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Phone Number *</label>
                                       <input type="tel" name="phone" id="phone" placeholder="Enter your phone number" required 
                                              style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease;">
                                   </div>
                                   <div class="col-md-6 mb-4">
                                       <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Company/Organization</label>
                                       <input type="text" name="company" id="company" placeholder="Enter company name (optional)" 
                                              style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease;">
                                   </div>
                               </div>
                               
                               <div class="row">
                                   <div class="col-md-6 mb-4">
                                       <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Website/Social Media *</label>
                                       <input type="url" name="website" id="website" placeholder="https://your-website.com" required 
                                              style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease;">
                                   </div>
                                   <div class="col-md-6 mb-4">
                                       <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Monthly Traffic/Followers *</label>
                                       <select name="traffic" id="traffic" required 
                                               style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease;">
                                           <option value="">Select range</option>
                                           <option value="1k-5k">1K - 5K</option>
                                           <option value="5k-10k">5K - 10K</option>
                                           <option value="10k-50k">10K - 50K</option>
                                           <option value="50k-100k">50K - 100K</option>
                                           <option value="100k+">100K+</option>
                                       </select>
                                   </div>
                               </div>
                               
                               <div class="mb-4">
                                   <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Marketing Experience *</label>
                                   <textarea name="experience" id="experience" rows="4" placeholder="Tell us about your marketing experience and how you plan to promote our products..." required 
                                             style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease; resize: vertical;"></textarea>
                               </div>
                               
                               <div class="mb-4">
                                   <label style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">Additional Message</label>
                                   <textarea name="message" id="message" rows="3" placeholder="Any additional information you'd like to share..." 
                                             style="width: 100%; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease; resize: vertical;"></textarea>
                               </div>
                               
                               <div class="text-center">
                                   <button type="submit" id="submitBtn" 
                                           style="background: linear-gradient(135deg, #ec6504, #ff8533); color: white; padding: 18px 50px; border: none; border-radius: 50px; font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(236, 101, 4, 0.3);">
                                       <span id="btnText">Submit Application</span>
                                       <span id="btnLoader" style="display: none;">
                                           <i class="fa fa-spinner fa-spin"></i> Submitting...
                                       </span>
                                   </button>
                               </div>
                           </form>
                       </div>
                   </div>
               </div>
           </div>
       </section>

       <!-- Row 3: Contact Information -->
       <section class="affiliate-contact-section" style="padding: 60px 0; background: #ffffff;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-lg-8 text-center">
                       <h2 style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 30px;">Have Questions?</h2>
                       <p style="font-size: 18px; color: #666; margin-bottom: 40px; line-height: 1.6;">
                           Our affiliate team is here to help you succeed. Get in touch with us for any questions about our program.
                       </p>
                       
                       <div class="row">
                           <div class="col-md-4 mb-3">
                               <div style="padding: 30px 20px;">
                                   <i class="fas fa-envelope" style="color: #ec6504; font-size: 2.5rem; margin-bottom: 15px;"></i>
                                   <h4 style="color: #333; margin-bottom: 10px;">Email Us</h4>
                                   <a href="mailto:affiliates@mynutrify.com" style="color: #666; text-decoration: none; font-size: 16px;">
                                       affiliates@mynutrify.com
                                   </a>
                               </div>
                           </div>
                           <div class="col-md-4 mb-3">
                               <div style="padding: 30px 20px;">
                                   <i class="fas fa-phone" style="color: #ec6504; font-size: 2.5rem; margin-bottom: 15px;"></i>
                                   <h4 style="color: #333; margin-bottom: 10px;">Call Us</h4>
                                   <a href="tel:+919834243754" style="color: #666; text-decoration: none; font-size: 16px;">
                                       +91-9834243754
                                   </a>
                               </div>
                           </div>
                           <div class="col-md-4 mb-3">
                               <div style="padding: 30px 20px;">
                                   <i class="fab fa-whatsapp" style="color: #ec6504; font-size: 2.5rem; margin-bottom: 15px;"></i>
                                   <h4 style="color: #333; margin-bottom: 10px;">WhatsApp</h4>
                                   <a href="https://wa.me/919834243754" style="color: #666; text-decoration: none; font-size: 16px;">
                                       +91-9834243754
                                   </a>
                               </div>
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
    
    <!-- jquery -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <!-- proper js -->
    <script src="js/popper.min.js"></script>
    <!-- bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    <!-- owl carousel js -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- swiper js -->
    <script src="js/swiper.min.js"></script>
    <!-- custom js -->
    <script src="js/custom.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<!-- Affiliate Form JavaScript -->
<script>
$(document).ready(function() {
    $('#affiliateForm').on('submit', function(e) {
        e.preventDefault();
        
        // Form validation
        const name = $('#name').val().trim();
        const phone = $('#phone').val().trim();
        const email = $('#email').val().trim();
        const website = $('#website').val().trim();
        const traffic = $('#traffic').val();
        const experience = $('#experience').val().trim();
        
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
        
        // Website validation
        if (!/^https?:\/\/.+\..+/.test(website)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Website',
                text: 'Please enter a valid website URL (including http:// or https://)'
            });
            return;
        }
        
        // Traffic validation
        if (!traffic) {
            Swal.fire({
                icon: 'error',
                title: 'Traffic Required',
                text: 'Please select your monthly traffic/followers range'
            });
            return;
        }
        
        // Experience validation
        if (experience.length < 50) {
            Swal.fire({
                icon: 'error',
                title: 'Experience Required',
                text: 'Please provide at least 50 characters describing your marketing experience'
            });
            return;
        }
        
        // Submit form if validations pass
        submitAffiliateForm();
    });
    
    function submitAffiliateForm() {
        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            company: $('#company').val(),
            website: $('#website').val(),
            traffic: $('#traffic').val(),
            experience: $('#experience').val(),
            message: $('#message').val()
        };
        
        // Show loading state
        $('#btnText').hide();
        $('#btnLoader').show();
        $('#submitBtn').prop('disabled', true);
        
        $.ajax({
            url: 'process_affiliate_contact.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Application Submitted!',
                        text: 'Thank you for your interest! Our affiliate team will review your application and get back to you within 24 hours.',
                        confirmButtonColor: '#ec6504'
                    });
                    $('#affiliateForm')[0].reset();
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
    
    // Input focus effects
    $('input, textarea, select').on('focus', function() {
        $(this).css('border-color', '#ec6504');
    }).on('blur', function() {
        $(this).css('border-color', '#e0e0e0');
    });
});
</script>

</body>
</html>

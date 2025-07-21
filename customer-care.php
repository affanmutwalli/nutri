<?php
session_start();
include("database/dbconnection.php");
$obj = new main();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Care - MyNutrify</title>
    <meta name="description" content="24/7 Customer Care Support - MyNutrify">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="image/favicon.png">
    
    <!-- CSS Files -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    
    <!-- Custom CSS for Over-the-Top Design -->
    <style>
        /* Hero Section with Animated Background */
        .customer-care-hero {
           background: url("cms/images/banners/CS.webp");

            background-size: 100% 100%;
            animation: gradientShift 8s ease infinite;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Floating Particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        /* Hero Content */
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: slideInDown 1s ease-out;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: slideInUp 1s ease-out 0.3s both;
        }
        
        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Glowing Button */
        .glow-btn {
            background: linear-gradient(45deg, #ec6504, #ff8534);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 0 20px rgba(236, 101, 4, 0.5);
            animation: pulse 2s infinite;
            transition: all 0.3s ease;
        }
        
        .glow-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(236, 101, 4, 0.7);
            color: white;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 20px rgba(236, 101, 4, 0.5); }
            50% { box-shadow: 0 0 30px rgba(236, 101, 4, 0.8); }
            100% { box-shadow: 0 0 20px rgba(236, 101, 4, 0.5); }
        }
        
        /* Support Cards */
        .support-cards {
            padding: 100px 0;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        }
        
        .support-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            height: 350px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .support-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .support-card:hover::before {
            left: 100%;
        }
        
        .support-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        
        .support-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #305724, #4a7c59);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
            animation: rotateIcon 3s ease-in-out infinite;
        }
        
        @keyframes rotateIcon {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(10deg); }
        }
        
        /* Contact Methods */
        .contact-methods {
            background: #305724;
            padding: 100px 0;
            position: relative;
        }
        
        .contact-method {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            color: white;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
            height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .contact-method:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
        }
        
        .contact-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ec6504;
        }

        /* Card content structure */
        .support-card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .support-card-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-method-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .contact-method-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* FAQ Section */
        .faq-section {
            padding: 100px 0;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        }
        
        .faq-item {
            background: white;
            border-radius: 15px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .faq-question {
            background: linear-gradient(135deg, #305724, #4a7c59);
            color: white;
            padding: 20px;
            cursor: pointer;
            font-weight: 600;
            position: relative;
        }
        
        .faq-question::after {
            content: '+';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .faq-question.active::after {
            transform: translateY(-50%) rotate(45deg);
        }
        
        .faq-answer {
            padding: 20px;
            display: none;
            background: #f8f9fa;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero-subtitle { font-size: 1.2rem; }
            .support-card { padding: 30px 20px; }
        }
    </style>
</head>

<body class="home-1">
    <?php include("components/header.php"); ?>
    
    <!-- Hero Section -->
    <section class="customer-care-hero">
        <div class="particles">
            <!-- Animated particles will be generated by JavaScript -->
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                   
                </div>
            </div>
        </div>
    </section>

    <!-- Support Cards Section -->
    <section class="support-cards" id="support">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title" style="font-size: 3rem; color: #305724; margin-bottom: 20px;">How Can We Help You?</h2>
                    <p style="font-size: 1.2rem; color: #666;">Choose from our comprehensive support options</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="support-card">
                        <div class="support-card-content">
                            <div class="support-icon">
                                <i class="fa fa-comments"></i>
                            </div>
                            <div class="support-card-text">
                                <h4 style="color: #305724; margin-bottom: 15px;">Live Chat Support</h4>
                                <p style="color: #666; margin-bottom: 20px;">Get instant answers from our expert support team. Available 24/7 for all your queries.</p>
                            </div>
                            <button class="btn btn-style1">Start Chat</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="support-card">
                        <div class="support-card-content">
                            <div class="support-icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="support-card-text">
                                <h4 style="color: #305724; margin-bottom: 15px;">Phone Support</h4>
                                <p style="color: #666; margin-bottom: 20px;">Speak directly with our customer care executives for personalized assistance.</p>
                            </div>
                            <a href="tel:+919834243754" class="btn btn-style1">Call Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="support-card">
                        <div class="support-card-content">
                            <div class="support-icon">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="support-card-text">
                                <h4 style="color: #305724; margin-bottom: 15px;">Email Support</h4>
                                <p style="color: #666; margin-bottom: 20px;">Send us detailed queries and get comprehensive solutions within 24 hours.</p>
                            </div>
                            <a href="mailto:support@mynutrify.com" class="btn btn-style1">Send Email</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="support-card">
                        <div class="support-card-content">
                            <div class="support-icon">
                                <i class="fa fa-truck"></i>
                            </div>
                            <div class="support-card-text">
                                <h4 style="color: #305724; margin-bottom: 15px;">Order Tracking</h4>
                                <p style="color: #666; margin-bottom: 20px;">Track your orders in real-time and get delivery updates instantly.</p>
                            </div>
                            <a href="track_order.php" class="btn btn-style1">Track Order</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="support-card">
                        <div class="support-card-content">
                            <div class="support-icon">
                                <i class="fa fa-undo"></i>
                            </div>
                            <div class="support-card-text">
                                <h4 style="color: #305724; margin-bottom: 15px;">Returns & Refunds</h4>
                                <p style="color: #666; margin-bottom: 20px;">Easy return process with hassle-free refunds. Your satisfaction is our priority.</p>
                            </div>
                            <button class="btn btn-style1">Return Item</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="support-card">
                        <div class="support-card-content">
                            <div class="support-icon">
                                <i class="fa fa-question-circle"></i>
                            </div>
                            <div class="support-card-text">
                                <h4 style="color: #305724; margin-bottom: 15px;">Product Guidance</h4>
                                <p style="color: #666; margin-bottom: 20px;">Get expert advice on product selection and usage from our nutrition specialists.</p>
                            </div>
                            <button class="btn btn-style1">Get Guidance</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Methods Section -->
    <section class="contact-methods">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 style="color: white; font-size: 3rem; margin-bottom: 20px;">Multiple Ways to Reach Us</h2>
                    <p style="color: rgba(255,255,255,0.8); font-size: 1.2rem;">Choose your preferred communication method</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="contact-method">
                        <div class="contact-method-content">
                            <div class="contact-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact-method-text">
                                <h4>WhatsApp</h4>
                                <p>Quick responses on WhatsApp</p>
                            </div>
                            <a href="https://wa.me/919834243754" class="btn btn-style3">Message Us</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-method">
                        <div class="contact-method-content">
                            <div class="contact-icon">
                                <i class="fab fa-facebook-messenger"></i>
                            </div>
                            <div class="contact-method-text">
                                <h4>Facebook</h4>
                                <p>Connect via Facebook Messenger</p>
                            </div>
                            <a href="https://www.facebook.com/p/My-nutrify-100086009867166/" class="btn btn-style3">Chat Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-method">
                        <div class="contact-method-content">
                            <div class="contact-icon">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div class="contact-method-text">
                                <h4>Instagram</h4>
                                <p>DM us on Instagram</p>
                            </div>
                            <a href="https://www.instagram.com/mynutrify.official/" class="btn btn-style3">Follow & DM</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-method">
                        <div class="contact-method-content">
                            <div class="contact-icon">
                                <i class="fa fa-headphones"></i>
                            </div>
                            <div class="contact-method-text">
                                <h4>Live Support</h4>
                                <p>24/7 live chat support</p>
                            </div>
                            <button class="btn btn-style3" onclick="openLiveChat()">Start Chat</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 style="font-size: 3rem; color: #305724; margin-bottom: 20px;">Frequently Asked Questions</h2>
                    <p style="font-size: 1.2rem; color: #666;">Find quick answers to common questions</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            How can I track my order?
                        </div>
                        <div class="faq-answer">
                            <p>You can track your order by visiting our <a href="track_order.php">Order Tracking page</a> and entering your order ID. You'll also receive SMS and email updates with tracking information once your order is shipped.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            What is your return policy?
                        </div>
                        <div class="faq-answer">
                            <p>We offer a 30-day return policy for unopened products. If you're not satisfied with your purchase, you can return it within 30 days for a full refund. Please contact our customer care team to initiate the return process.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            How long does delivery take?
                        </div>
                        <div class="faq-answer">
                            <p>Standard delivery takes 3-7 business days. We offer free delivery on orders above ₹399. For urgent orders, express delivery options are available at checkout.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            Are your products authentic?
                        </div>
                        <div class="faq-answer">
                            <p>Yes, all our products are 100% authentic and sourced directly from manufacturers. You can verify product authenticity using our <a href="authenticate.php">Authentication page</a> with the unique code on each product.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            Do you offer cash on delivery?
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we offer cash on delivery (COD) for orders across India. COD charges may apply for orders below ₹399. You can select this option during checkout.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            How can I contact customer support?
                        </div>
                        <div class="faq-answer">
                            <p>You can reach us through multiple channels: Phone (+91 9834243754), Email (support@mynutrify.com), WhatsApp, or live chat. Our support team is available 24/7 to assist you.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Support Section -->
    <section style="background: linear-gradient(135deg, #ec6504, #ff8534); padding: 80px 0; text-align: center;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 style="color: white; font-size: 2.5rem; margin-bottom: 20px;">Need Urgent Help?</h2>
                    <p style="color: white; font-size: 1.3rem; margin-bottom: 30px; opacity: 0.9;">Our emergency support team is ready to assist you</p>
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <a href="tel:+919834243754" class="btn" style="background: white; color: #ec6504; padding: 15px 30px; border-radius: 50px; font-weight: 600; font-size: 1.1rem; width: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                                <i class="fa fa-phone"></i> Emergency Call
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="https://wa.me/919834243754" class="btn" style="background: white; color: #ec6504; padding: 15px 30px; border-radius: 50px; font-weight: 600; font-size: 1.1rem; width: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                                <i class="fab fa-whatsapp"></i> WhatsApp Now
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button onclick="openLiveChat()" class="btn" style="background: white; color: #ec6504; padding: 15px 30px; border-radius: 50px; font-weight: 600; font-size: 1.1rem; width: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                                <i class="fa fa-comments"></i> Live Chat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("components/footer.php"); ?>

    <!-- JavaScript Files -->
    <script src="js/modernizr-2.8.3.min.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/fontawesome.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/swiper.min.js"></script>
    <script src="js/custom.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.querySelector('.particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';

                // Random size between 4px and 12px
                const size = Math.random() * 8 + 4;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';

                // Random position
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';

                // Random animation delay
                particle.style.animationDelay = Math.random() * 6 + 's';

                // Random animation duration
                particle.style.animationDuration = (Math.random() * 3 + 3) + 's';

                particlesContainer.appendChild(particle);
            }
        }

        // Smooth scroll to support section
        function scrollToSupport() {
            document.getElementById('support').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Toggle FAQ items
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const isActive = element.classList.contains('active');

            // Close all FAQ items
            document.querySelectorAll('.faq-question').forEach(q => {
                q.classList.remove('active');
                q.nextElementSibling.style.display = 'none';
            });

            // Open clicked item if it wasn't active
            if (!isActive) {
                element.classList.add('active');
                answer.style.display = 'block';
            }
        }

        // Open live chat (placeholder function)
        function openLiveChat() {
            // You can integrate with your preferred live chat service here
            alert('Live chat feature will be integrated with your preferred chat service (e.g., Tawk.to, Intercom, etc.)');
        }

        // Initialize particles when page loads
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();

            // Add scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe support cards
            document.querySelectorAll('.support-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Observe contact methods
            document.querySelectorAll('.contact-method').forEach(method => {
                method.style.opacity = '0';
                method.style.transform = 'translateY(30px)';
                method.style.transition = 'all 0.6s ease';
                observer.observe(method);
            });

            // Observe FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-30px)';
                item.style.transition = 'all 0.6s ease';
                observer.observe(item);
            });
        });

        // Add floating animation to support icons
        setInterval(function() {
            document.querySelectorAll('.support-icon').forEach(icon => {
                icon.style.transform = 'translateY(' + (Math.sin(Date.now() * 0.001) * 5) + 'px)';
            });
        }, 50);
    </script>

</body>
</html>

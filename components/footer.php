<style>
/* Container for the brand logos */
.brand-logos {
    display: flex;
    flex-wrap: wrap; /* Allow items to wrap to the next row */
    justify-content: center;
    align-items: center;
    gap: 20px; /* Spacing between logos */
    padding: 20px;
    background-color: #f9f9f9; /* Optional background */
}

/* Style for each logo */
.brand-logo {
    flex: 1 1 100px; /* Ensure logos scale consistently; minimum width 100px */
    max-width: 150px; /* Prevent logos from being too large */
    text-align: center;
}

.brand-logo img {
    width: 100%; /* Make logos responsive */
    height: auto; /* Maintain aspect ratio */
    object-fit: contain;
    transition: transform 0.3s ease; /* Add hover effect */
}

/* Hover effect for logos */
.brand-logo img:hover {
    transform: scale(1.05);
    opacity: 0.8;
}

</style>
<section class="footer-one section-tb-padding">
            <div class="container-fluid full-width">
                <div class="row">
                    <div class="col">
                        <div class="footer-service section-b-padding">
                            <ul class="service-ul">
                                <li class="service-li">
                                    <a href="javascript:void(0)"><i class="fa fa-truck"></i></a>
                                    <span>Free delivery above ₹399 </span>
                                </li>
                                <li class="service-li">
                                    <a href="javascript:void(0)"><i class="fa fa-rupee"></i></a>
                                    <span>Cash on delivery</span>
                                </li>
                                <li class="service-li">
                                    <a href="javascript:void(0)"><i class="fas fa-leaf"></i></a>
                                    <span> Pure Ayurvedic & Herbal</span>
                                </li>
                                <li class="service-li">
                                    <a href="javascript:void(0)"><i class="fa fa-headphones"></i></a>
                                    <span>Online support</span>
                                </li>
                            </ul>
                        </div>
                        <div class="f-logo">
                            <ul class="footer-ul">
                                <li class="footer-li footer-logo">
                                    <a href="index.php">
                                        <img class="img-fluid" src="image/main_logo.png" alt="">
                                    </a>
                                </li>
                                <li class="footer-li footer-address">
                                    <ul class="f-ul-li-ul">
                                        <li class="footer-icon">
                                            <i class="ion-ios-location"></i>
                                        </li>
                                        <li class="footer-info">
                                            <h6>Address</h6>
                                            <span> S.NO.31/32, 1st Floor,</span>
                                            <span>Old Mumbai Pune Road,</span>
                                            <span>Dapoli (Maharashtra) Pune - 411012</span>
                                             
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer-li footer-contact">
                                    <ul class="f-ul-li-ul">
                                        <li class="footer-icon">
                                            <i class="ion-ios-telephone"></i>
                                        </li>
                                        <li class="footer-info">
                                            <h6>Contact</h6>
                                            <a href="tel:+91 9834243754">Phone: +91 9834243754</a>
                                            <a href="mailto:support@mynutrify.com">Email: support@mynutrify.com</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="footer-bottom section-t-padding">
                            <div class="footer-link" id="footer-accordian">
                                <div class="f-link">
                                    <h2 class="h-footer">Services</h2>
                                    <a href="#services" data-bs-toggle="collapse" class="h-footer">
                                        <span>Services</span>
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="f-link-ul collapse" id="services" data-bs-parent="#footer-accordian">
                                        <li class="f-link-ul-li"><a href="about.php">About My Nutrify</a></li>
                                        <li class="f-link-ul-li"><a href="faq's.php">Faq's</a></li>
                                        <li class="f-link-ul-li"><a href="contact.php">Contact us</a></li>
                                        <li class="f-link-ul-li"><a href="customer-care.php">Customer Care</a></li>
                                        <li class="f-link-ul-li"><a href="blogs.php">Blogs</a></li>
                                        <li class="f-link-ul-li"><a href="affiliate-contact.php">Affiliate Program</a></li>
                                        <!--<li class="f-link-ul-li"><a href="sitemap.html">Sitemap</a></li>-->
                                    </ul>
                                </div>
                                <div class="f-link">
                                    <h2 class="h-footer">Privacy & Terms</h2>
                                    <a href="#privacy" data-bs-toggle="collapse" class="h-footer">
                                        <span>Privacy & terms</span>
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <!--<ul class="f-link-ul collapse" id="privacy" data-bs-parent="#footer-accordian">-->
                                    <!--    <li class="f-link-ul-li"><a href="payment-policy.html">Payment policy</a></li>-->
                                    <!--    <li class="f-link-ul-li"><a href="privacy-policy.html">Privacy policy</a></li>-->
                                    <!--    <li class="f-link-ul-li"><a href="return-policy.html">Return policy</a></li>-->
                                    <!--    <li class="f-link-ul-li"><a href="shipping-policy.html">Shipping policy</a></li>-->
                                    <!--    <li class="f-link-ul-li"><a href="terms-conditions.html">Terms & conditions</a></li>-->
                                    <!--</ul>-->
                                     <ul class="f-link-ul collapse" id="privacy" data-bs-parent="#footer-accordian">
                                        <li class="f-link-ul-li"><a href="payment_policy.php">Payment Policy</a></li>
                                        <li class="f-link-ul-li"><a href="privacy_policy.php">Privacy Policy</a></li>
                                        <li class="f-link-ul-li"><a href="return_policy.php">Return Policy</a></li>
                                        <li class="f-link-ul-li"><a href="shipping_policy.php">Shipping Policy</a></li>
                                        <li class="f-link-ul-li"><a href="terms.php">Terms & Conditions</a></li>
                                    </ul>
                                </div>
                                <div class="f-link">
                                    <h2 class="h-footer">My Account</h2>
                                    <a href="#account" data-bs-toggle="collapse" class="h-footer">
                                        <span>My Account</span>
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="f-link-ul collapse" id="account" data-bs-parent="#footer-accordian">
                                        <li class="f-link-ul-li"><a href="account.php">My Account</a></li>
                                    </ul>
                                </div>
                                <div class="f-link">
                                    <h2 class="h-footer">We are also Available On</h2>
                                    <a href="#account" data-bs-toggle="collapse" class="h-footer">
                                        <span>Our E-Commerce Platforms</span>
                                        <i class="fa fa-angle-down"></i>
                                    </a>

                                    <div class="brand-logos">
                                        <div class="brand-logo">
                                            <a href="https://www.amazon.in/s?me=ARQV121QOKKKC&marketplaceID=A21TJRUUN4KGV" target="_blank">
                                                <img src="cms/images/platforms/1.png" alt="Available on Amazon">
                                            </a>
                                        </div>
                                        <div class="brand-logo">
                                            <a href="https://www.jiomart.com/s/pure-nutrition-co/76258/products" target="_blank">
                                                <img src="cms/images/platforms/2.png" alt="Available on Jio Mart">
                                            </a>
                                        </div>
                                        <div class="brand-logo">
                                            <a href="https://www.1mg.com/marketer/my-nutrify-94132" target="_blank">
                                                <img src="cms/images/platforms/3.png" alt="Available on Tata 1mg">
                                            </a>
                                        </div>
                                        <div class="brand-logo">
                                            <a href="https://www.snapdeal.com/seller/S741f4" target="_blank">
                                                <img src="cms/images/platforms/4.png" alt="Available on Snapdeal">
                                            </a>
                                        </div>
                                        <div class="brand-logo">
                                            <img src="cms/images/platforms/5.png" alt="Available on Platform 5">
                                        </div>
                                         <div class="brand-logo">
                                            <img src="cms/images/platforms/6.png" alt="Available on Platform 6">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
         <!-- footer copyright start -->
         <section class="footer-copyright">
            <div class="container-fluid full-width">
                <div class="row">
                    <div class="col">
                        <ul class="f-bottom">
                            <li class="f-c f-copyright">
                                <p>Copyright <i class="fa fa-copyright"></i> 2024 MyNurify</p>
                            </li>
                            <li class="f-c f-social">
                                <a href="https://wa.me/919876543210" class="f-icn-link"><i class="fab fa-whatsapp"></i></a>
                                <a href="https://www.facebook.com/p/My-nutrify-100086009867166/" class="f-icn-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com/mynutrify.official/" class="f-icn-link"><i class="fab fa-instagram"></i></a>
                                <a href="https://www.youtube.com/@mynutrify_official" class="f-icn-link"><i class="fab fa-youtube"></i></a>
                            </li>
                            <li class="f-c f-payment">
                                <img src="image/payment.png" class="img-fluid" alt="Accepted Payment Methods">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- footer copyright end -->
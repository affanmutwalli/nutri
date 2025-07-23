<!-- Footer Start -->
	<footer class="main-footer">
	    <?php 
	    
	    $EmailSupport = $obj->fSelectRowCountNew("Select EmailSupport from contact_master where ContactId=1");
	    
	    $FieldNames=array("LocationId","LocationHeading","LocationAddress","IsActive");
        $ParamArray=array();
        $Fields=implode(",",$FieldNames);
        $location=$obj->MysqliSelect1("Select ".$Fields." from location_master ",$FieldNames,"s",$ParamArray);
        
        $FieldNames=array("SocialId","Facebook","Instagram","Twitter","LinkedIn","YouTube");
        $ParamArray=array();
        $ParamArray[0]=1;
        $Fields=implode(",",$FieldNames);
        $social=$obj->MysqliSelect1("Select ".$Fields." from social_links where SocialId= ? ",$FieldNames,"i",$ParamArray);
        
        // Use only existing columns from product_master table
        $FieldNames = array("ProductId", "ProductName", "MetaTags", "MetaKeywords", "ShortDescription", "PhotoPath");
        $ParamArray = array();
        $Fields = implode(",", $FieldNames);
        $products = $obj->MysqliSelect1("Select " . $Fields . " from product_master", $FieldNames, "s", $ParamArray);
        
        $FieldNames = array("Id", "Heading", "Description", "PhotoPath");
        $ParamArray = array();
        $Fields = implode(",", $FieldNames);
        $our_services = $obj->MysqliSelect1("Select " . $Fields . " from our_services ", $FieldNames, "s", $ParamArray);
        
	    ?>
        <div class="container">
			<div class="row">
				<div class="col-md-12">
					<!-- Mega Footer Start -->
					<div class="mega-footer">
						<div class="row">
							<div class="col-lg-3 col-md-12">
								<!-- Footer About Start -->
								<div class="footer-about">
									<figure>
                                        <img style="width: 200px; height: auto;" src="<?php echo $LogoImage ?>" alt="">
                                    </figure>

									<p><?php echo $location[0]["LocationAddress"]?></p>
                                    <ul>
                                        <li><a href="#"><?php echo $EmailSupport ?></a></li>
                                        <li><a href="#"><?php echo $ContactNo ?></a></li>
                                    </ul>
								</div>
								<!-- Footer About End -->
							</div>
							
							<div class="col-lg-2 col-md-4">
								<!-- Footer Links Start -->
								<div class="footer-links">
									<h2>Socials</h2>
									<ul>
										<li><a href="<?php echo $social[0]["Facebook"]?>">Facebook</a></li>
										<li><a href="<?php echo $social[0]["Instagram"]?>">Instagram</a></li>
										<li><a href="<?php echo $social[0]["LinkedIn"]?>">LinkedIn</a></li>
										<li><a href="<?php echo $social[0]["Twitter"]?>">Twitter</a></li>
									</ul>
								</div>
								<!-- Footer Links End -->
							</div>

							<div class="col-lg-2 col-md-4">
								<!-- Footer Links Start -->
								<div class="footer-links">
									<h2>Products</h2>
									<ul>
									    <?php foreach($products as $product){ ?>
										<li><a href="products.php"><?php echo $product["ProductName"]; ?></a></li>
                                        <?php }
									    ?>
									</ul>
								</div>
								<!-- Footer Links End -->
							</div>

							
							
							<div class="col-lg-2 col-md-4">
								<!-- Footer Links Start -->
								<div class="footer-links">
									<h2>Pages</h2>
									<ul>
										<li><a href="index.php">Home</a></li>
										<li><a href="about.php">About Us</a></li>
										<li><a href="services.php">Services</a></li>
										<li><a href="blog.php">Blog</a></li>
										<li><a href="careers.php">Careers</a></li>
										<li><a href="contact-us.php">Contact Us</a></li>
									</ul>
								</div>
								<!-- Footer Links End -->
							</div>

							<div class="col-lg-3 col-md-4">
								<!-- Footer Links Start -->
								<div class="footer-links">
									<h2>Services</h2>
									<ul>
									    <?php foreach($our_services as $our_service){?>
										<li><a href="services.php"><?php echo $our_service["Heading"];?></a></li>
										<?php } ?>
									</ul>
								</div>
								<!-- Footer Links End -->
							</div>
						</div>
					</div>
					<!-- Mega Footer End -->

					<!-- Copyright Footer Start -->
					<div class="footer-copyright">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <!-- Footer Copyright Content Start -->
								<div class="footer-copyright-text">
									<p>Copyright © 2024 Virtucrop. All rights reserved.</p>
								</div>
								<!-- Footer Copyright Content End -->
                            </div>
                            <div class="col-lg-6">
                                <!-- Footer Policy Links Start -->
                                <div class="footer-policy-links">
                                    <ul>
                                        <li><a href="#">privacy policy</a></li>
                                        <li><a href="#">terms of service</a></li>
                                        <li class="highlighted"><a href="#top">go to top</a></li>
                                    </ul>
                                </div>
                                <!-- Footer Policy Links End -->
                            </div>
                        </div>						
					</div>
					<!-- Copyright Footer End -->
				</div>
			</div>
		</div>
    </footer>
    <!-- Footer End -->
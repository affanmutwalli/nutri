<!DOCTYPE html>
<html lang="en">
   <?php 
   session_start();
      include('includes/urls.php');
      include('database/dbconnection.php');
      $obj = new main();
      $obj->connection();
      
      if (isset($_GET['ProductId'])) {
          $productId = $_GET['ProductId'];
      
          // Fetch product details
          $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId","MetaTags","MetaKeywords","ProductCode","CategoryId");
          $ParamArray = array($productId);
          $Fields = implode(",", $FieldNames);
          $product_data = $obj->MysqliSelect1(
              "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
              $FieldNames,
              "i",
              $ParamArray
          );
      
          if ($product_data) {
              // Fetch images for the product
              $FieldNames = array("PhotoPath");
              $ParamArray = array($productId);
              $Fields = implode(",", $FieldNames);
              $model_image = $obj->MysqliSelect1(
                  "SELECT " . $Fields . " FROM model_images WHERE ProductId = ?",
                  $FieldNames,
                  "i",
                  $ParamArray
              );
              
             // Fetch product price details based on the product ID
               $FieldNames = array("Size", "OfferPrice", "MRP", "Coins");
               $ParamArray = array($productId);
               $Fields = implode(",", $FieldNames);
               $product_prices = $obj->MysqliSelect1(
                  "SELECT " . $Fields . " FROM product_price WHERE ProductId = ?",
                  $FieldNames,
                  "i",
                  $ParamArray
               );

               // Initialize variables for dynamic size selection
               $sizes = [];
               $price_data = [];

               // Process the fetched product prices and sizes
               foreach ($product_prices as $product_price) {
                  $size = htmlspecialchars($product_price["Size"]);
                  $offer_price = floatval($product_price["OfferPrice"]);
                  $mrp = floatval($product_price["MRP"]);
                  $coins = floatval($product_price["Coins"]);

                  // Only add the size if OfferPrice and MRP are greater than 0
                  if ($offer_price > 0 && $mrp > 0) {
                     $sizes[] = $size;
                     $price_data[$size] = [
                           'offer_price' => $offer_price,
                           'mrp' => $mrp,
                           'coins' => $coins
                     ];
                  }
               }

               // Set default size and price (you can customize this as needed)
               if (!empty($sizes)) {
                  $default_size = $sizes[0];
                  $lowest_price = $price_data[$default_size]['offer_price'];
                  $mrp = $price_data[$default_size]['mrp'];
                  $coins = $price_data[$default_size]['coins'];

                  if ($mrp > 0 && $lowest_price > 0 && $mrp > $lowest_price) {
                     $discount = $mrp - $lowest_price; // Calculate the price discount
                  } else {
                     $discount = 0; // No discount
                  }
               } else {
                  $default_size = "N/A";
                  $lowest_price = "N/A";
                  $mrp = "N/A";
                  $discount = 0;
                  $coins = 0;
               }

          } 
          // Prepare product details for sharing
          $productTitle = urlencode($product_data[0]["ProductName"]);
          $productDescription = urlencode($product_data[0]["ShortDescription"]);
          $productUrl = urlencode("https://mynutrify.com/product_details.php?ProductId=" . $productId);
          $productImage = "cms/images/products/".$product_data[0]["PhotoPath"];
          
      ?>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- title -->
      <title>My Nutrify - <?php echo htmlspecialchars($product_data[0]["ProductName"]); ?></title>
      <meta name="description" content="<?php echo htmlspecialchars($product_data[0]["ShortDescription"]); ?>"/>
      <meta name="keywords" content="<?php echo htmlspecialchars($product_data[0]["MetaKeywords"]); ?>"/>
      <meta name="author" content="My Nutrify">
      <!-- Open Graph Tags -->
      <meta property="og:title" content="<?php echo htmlspecialchars($product_data[0]["ProductName"]); ?>"/>
      <meta property="og:description" content="<?php echo htmlspecialchars($product_data[0]["ShortDescription"]); ?>"/>
      <meta property="og:type" content="product"/>
      <meta property="og:url" content="<?php echo $productUrl; ?>"/>
      <meta property="og:image" content="<?php echo $productImage; ?>"/>
      <!-- URL of the product image -->
      <meta property="og:image:alt" content="<?php echo htmlspecialchars($product_data[0]["ProductName"]); ?>"/>
      <meta property="og:site_name" content="My Nutrify"/>
      <!-- Twitter Card Tags -->
      <meta name="twitter:card" content="summary_large_image"/>
      <meta name="twitter:title" content="<?php echo htmlspecialchars($product_data[0]["ProductName"]); ?>"/>
      <meta name="twitter:description" content="<?php echo htmlspecialchars($product_data[0]["ShortDescription"]); ?>"/>
      <meta name="twitter:image" content="<?php echo $productImage; ?>"/>
      <!-- URL of the product image -->
      <meta name="twitter:creator" content="@MyNutrify"/>
      <meta name="twitter:site" content="@MyNutrify"/>
      <!-- Optional for additional info -->
      <meta property="product:price:currency" content="INR"/>
      <meta property="product:price:amount" content="<?php echo $lowest_price; ?>"/>
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
      <style>
         /* Style for the Tooltip Text */
         .tooltip-inner {
         background-color: #333; /* Dark background for the tooltip */
         color: #fff; /* White text color */
         font-size: 14px; /* Text size */
         border-radius: 5px; /* Rounded corners */
         padding: 10px; /* Padding around the text */
         text-align: center; /* Center the text inside the tooltip */
         max-width: 200px; /* Maximum width of the tooltip */
         }
         /* Customizing Tooltip Arrow */
         .tooltip-arrow {
         border-top-color: #333; /* Color of the arrow pointing downwards */
         }
         /* Adjust Tooltip Arrow for Bottom Placement */
         .tooltip.bs-tooltip-bottom .arrow::before {
         border-bottom-color: #333; /* Matching color for the bottom arrow */
         }
         .desktop-margin {
         margin: 0; /* Default margin for mobile (no margin) */
         }
         @media (min-width: 768px) {
         .desktop-margin {
         margin: 20px; /* Add margins for desktop view */
         }
         }
         .share {
         display: flex;
         align-items: center;
         gap: 10px;
         }
         .share-label {
         font-size: 16px;
         font-weight: bold;
         margin-right: 10px;
         }
         .f-c.f-social {
         display: flex;
         gap: 10px;
         list-style: none;
         padding: 0;
         margin: 0;
         }
         .f-icn-link {
         display: flex;
         justify-content: center;
         align-items: center;
         width: 40px;
         height: 40px;
         border-radius: 50%;
         font-size: 18px;
         color: white; /* Default icon color */
         text-decoration: none;
         transition: transform 0.3s ease, color 0.3s ease;
         }
         .f-icn-link:hover {
         transform: scale(1.1);
         color: #ffffff; /* Ensure the icon turns white on hover */
         }
         /* Individual platform colors */
         .whatsapp {
         background-color: #25D366;
         }
         .facebook {
         background-color: #1877F2;
         }
         .instagram {
         background: radial-gradient(circle at 30% 30%, #fdf497, #fd5949, #d6249f, #285AEB);
         }
         .youtube {
         background-color: #FF0000;
         }
         .twitter {
         background-color: #1DA1F2; /* Twitter's official blue */
         }
         .telegram {
         background-color: #0088cc; /* Telegram's official blue */
         }
         .mrp-label {
         font-size: 14px;
         color: #555;
         margin-top: 5px;
         font-weight: normal;
         text-align: left;
         }
         .mrp-label span {
         background-color: #f0f0f0;
         padding: 5px;
         border-radius: 3px;
         display: inline-block;
         }
         .nutrify-coin-card {
         display: flex;
         align-items: center;
         background-color: #ec6504; /* Green background */
         padding: 5px;
         border-radius: 8px;
         color: white;
         font-size: 16px;
         justify-content: space-between;
         margin: 20px 0;
         }
         .nutrify-coin {
         flex-grow: 1;
         display: flex;
         align-items: center;
         justify-content: center;
         }
         .nutrify-coin span {
         margin: 0 10px;
         }
         .charm-icon, .info-icon {
         font-size: 20px;
         color: white;
         }
         .charm-icon {
         /* margin-right: px; Space before the text */
         }
         .info-icon {
         /* margin-left: 10px; Space after the text */
         }
         .size-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);  /* 2 columns per row */
                gap: 20px;
                margin-top: 10px;
            }
            
            
            .size-box {
                border: 1px solid #ddd; /* Default border */
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                background-color: #fff;
            }
            
            .size-name {
                font-weight: bold;
                font-size: 16px;
                margin-bottom: 10px;
            }
            
            .size-price {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            /* Apply the border and optional glow effect to the selected size box */
            .size-box.selected {
                border: 2px solid orange;  /* Orange border for selected box */
                box-shadow: 0 0 5px rgba(255, 165, 0, 0.6);  /* Optional: adds a glow effect */
            }
            
            
            .offer-price {
                font-weight: bold;
                color: #28a745; /* Green for offer price */
            }
            
            .mrp-price {
                color: #dc3545; /* Red for MRP */
            }
            
            .price-unavailable {
                color: #888; /* Grey for unavailable price */
                font-style: italic;
            }
            .e-star {
                cursor: pointer;
                color: #ccc; /* Default gray color */
            }
            
            .e-star.selected {
                color: #f39c12; /* Gold color when selected */
            }
            
            /* Optional: To add hover effect for better UI */
            .e-star:hover {
                color: #f39c12; /* Gold on hover */
            }
            
             /* Overlay */
            .overlay {
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background: rgba(0, 0, 0, 0.7);
              display: none; /* Set this to block to show the overlay */
              justify-content: center;
              align-items: center;
              z-index: 1040; /* Ensure this is above other elements */
            }
            
            /* Modal */
            .modal {
              background: #fff;
              padding: 5px;
              border-radius: 20px;
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
              text-align: center;
              animation: slideIn 0.3s ease-in-out;
            }
            
            /* Keyframe animation for sliding in the modal */
            @keyframes slideIn {
              from {
                transform: translateY(100%);
              }
              to {
                transform: translateY(0);
              }
            }
            
            .modal h3 {
              font-size: 20px;
              margin-bottom: 20px;
              color: #333;
            }
            
            .modal input {
              width: 100%;
              padding: 10px;
              margin: 10px 0;
              border: 1px solid #ccc;
              border-radius: 8px;
              font-size: 16px;
            }
            
            .modal button {
              width: 100%;
              padding: 12px;
              background: #ff5722;
              border: none;
              border-radius: 10px;
              font-size: 16px;
              color: #fff;
              cursor: pointer;
            }
            
            .modal button:hover {
              background: #e64a19;
            }
            
            .close-modal {
              position: absolute;
              top: 20px;
              right: 20px;
              font-size: 20px;
              color: #555;
              cursor: pointer;
            }
            /* Popup Overlay */
            .cart-popup-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 1000;
            }
            
            /* Popup Content */
            .cart-popup-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                width: 300px;
            }
            
            /* Close Button */
            .close-popup {
                position: absolute;
                top: 10px;
                right: 10px;
                border: none;
                background: none;
                font-size: 20px;
                cursor: pointer;
            }
            
            /* Popup Heading */
            .cart-popup-body h3 {
                font-size: 18px;
                color: #333;
                margin-bottom: 15px;
            }
            
            /* Buttons */
            .cart-popup-actions a {
                display: inline-block;
                margin: 10px 5px;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                color: #fff;
                font-size: 14px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }
            
            .cart-popup-actions .btn-view-cart {
                background: #305724;
            }
            
            .cart-popup-actions .btn-view-cart:hover {
                background: #000000;
            }
            
            .cart-popup-actions .btn-checkout {
                background: #ec6504;
            }
            
            .cart-popup-actions .btn-checkout:hover {
                background: #ffffff;
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
    animation: fadeOut 1.5s ease-out 3s forwards; /* Fades out after 3 seconds */
}

.loader-img {
    width: 150px;
    height: 150px;
    animation: spin 2s linear infinite;
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

      <?php include("components/header.php"); ?>
          <div id="cart-popup" class="cart-popup-overlay">
    <div class="cart-popup-content">
        <button class="close-popup" onclick="$('#cart-popup').fadeOut();">
            &times;
        </button>
        <h3>Product added to your cart!</h3>
        <div class="cart-popup-actions">
            <a href="cart.php" class="btn-view-cart">View Cart</a>
            <a href="checkout.php" class="btn-checkout">Checkout</a>
        </div>
    </div>
</div>
      <!-- Customer Details Modal -->
<div class="modal" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerDetailsModalLabel">Enter Your Details</h5>
            </div>
            <div class="modal-body">
                <form id="customerDetailsForm">
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="customerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customerEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="customerPhone" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitCustomerDetails">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Address Modal -->
<div class="modal" id="shippingAddressModal" tabindex="-1" aria-labelledby="shippingAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shippingAddressModalLabel">Enter Shipping Address</h5>
            </div>
            <div class="modal-body">
                <form id="shippingAddressForm">
                    <div class="mb-3">
                        <label for="shippingAddress" class="form-label">Address</label>
                        <textarea class="form-control" id="shippingAddress" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" required>
                    </div>
                    <div class="mb-3">
                        <label for="zipCode" class="form-label">Pin code</label>
                        <input type="text" class="form-control" id="pincode" required>
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <select class="form-control" id="state" required>
                            <option value="" disabled selected>Select State</option>
                        </select>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitShippingAddress">Submit</button>
            </div>
        </div>
    </div>
</div>





      <!-- product info start -->
      <section class="pro-page desktop-margin">
         <div class="container">
            <div class="row">
                  <!-- Product Images Section -->
                  <div class="col-xl-9 col-lg-12 col-md-12 col-xs-12 pro-image">
                     <div class="row">
                        <!-- Main Image -->
                        <div class="col-lg-6 col-xl-6 col-md-6 col-12 larg-image">
                              <div class="tab-content">
                                 <div class="tab-pane show active" id="image-11">
                                    <a href="javascript:void(0)" class="long-img" style="border: 1px solid rgba(0, 0, 0, 0.1); border-radius: 10px;">
                                          <figure class="zoom" onmousemove="zoom(event)" style="background-image: url('cms/images/products/<?php echo htmlspecialchars($product_data[0]["PhotoPath"]); ?>');">
                                             <img src="cms/images/products/<?php echo htmlspecialchars($product_data[0]["PhotoPath"]); ?>" class="img-fluid" alt="image">
                                          </figure>
                                    </a>
                                 </div>
                                 <?php if (!empty($model_image)) {
                                    foreach ($model_image as $index => $model_images) { ?>
                                          <div class="tab-pane" id="image-<?php echo $index + 1; ?>">
                                             <a href="javascript:void(0)" class="long-img" style="border: 1px solid #ccc; border-radius: 5px; margin-top: 15px;">
                                                <figure class="zoom" onmousemove="zoom(event)" style="background-image: url('cms/images/products/<?php echo htmlspecialchars($model_images["PhotoPath"]); ?>')">
                                                      <img src="cms/images/products/<?php echo htmlspecialchars($model_images["PhotoPath"]); ?>" class="img-fluid" alt="image">
                                                </figure>
                                             </a>
                                          </div>
                                 <?php } } ?>
                              </div>
                              <ul class="nav nav-tabs pro-page-slider owl-carousel owl-theme">
                                 <li class="nav-item items">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#image-11">
                                          <img src="cms/images/products/<?php echo htmlspecialchars($product_data[0]["PhotoPath"]); ?>" class="img-fluid" alt="image">
                                    </a>
                                 </li>
                                 <?php if (!empty($model_image)) {
                                    foreach ($model_image as $index => $model_images) { ?>
                                          <li class="nav-item items">
                                             <a class="nav-link" data-bs-toggle="tab" href="#image-<?php echo $index + 1; ?>">
                                                <img src="cms/images/products/<?php echo htmlspecialchars($model_images["PhotoPath"]); ?>" class="img-fluid" alt="image">
                                             </a>
                                          </li>
                                 <?php } } ?>
                              </ul>
                        </div>
                        <!-- Product Information Section -->
                        <div class="col-lg-6 col-xl-6 col-md-6 col-12 pro-info">
                              <h4><span><?php echo htmlspecialchars($product_data[0]["ProductName"]); ?></span></h4>
                              <div class="rating">
                                 <i class="fa fa-star d-star"></i>
                                 <i class="fa fa-star d-star"></i>
                                 <i class="fa fa-star d-star"></i>
                                 <i class="fa fa-star d-star"></i>
                                 <i class="fa fa-star-o"></i>
                              </div>
                              <p style="font-size: 12px; color: grey !important;">Product Code: <?php echo $product_data[0]["ProductCode"]; ?></p>
                              <div class="pro-availabale">
                                 <span class="available">Availability:</span>
                                 <span class="pro-instock">In stock</span>
                              </div>
                              <div class="mrp-label">
                                 <span>MRP (including all taxes):</span>
                              </div>
                              <div class="pro-price" id="pro-price">
                                 <?php if ($lowest_price != "N/A" && $mrp != "N/A") { ?>
                                    <span class="new-price">₹<?php echo number_format($lowest_price, 2); ?> INR</span>
                                    <span class="old-price"><del>₹<?php echo number_format($mrp, 2); ?> INR</del></span>
                                    <div class="Pro-lable">
                                          <span class="p-discount">₹<?php echo $discount; ?> off</span>
                                    </div>
                                 <?php } else { ?>
                                    <span class="new-price">Price not available</span>
                                 <?php } ?>
                              </div>
                              <p><?php echo htmlspecialchars($product_data[0]["ShortDescription"]); ?></p>
                              <h6 class="pro-size" style="margin-top: 5px;">Size: </h6>
                              <div class="pro-items">
                                 <?php if (!empty($sizes)) { ?>
                                    <div class="size-container">
                                          <?php foreach ($sizes as $index => $size) { 
                                             $offer_price = $price_data[$size]['offer_price'];
                                             $mrp = $price_data[$size]['mrp'];
                                             $coins = $price_data[$size]['coins'];
                                             $discount = $mrp - $offer_price;
                                          ?>
                                             <div class="size-box" 
                                                data-offer-price="<?php echo $offer_price; ?>" 
                                                data-mrp="<?php echo $mrp; ?>" 
                                                data-coins="<?php echo $coins; ?>" 
                                                data-index="<?php echo $index; ?>" 
                                                onclick="handleSizeSelection(event)" 
                                                style="cursor: pointer;">
                                                <div style="color: #305724; font-weight: bold;">Save ₹<?php echo $discount; ?></div>
                                                <div><?php echo $size; ?></div>
                                                <div class="size-price">
                                                   <div>₹<?php echo number_format($offer_price, 2); ?> <del>₹<?php echo number_format($mrp, 2); ?></del></div>
                                                </div>
                                             </div>


                                          <?php } ?>
                                    </div>
                                 <?php } ?>
                              </div>
                              <button style="background-color: #ec7524; margin-top: 20px;" type="button" class="btn text-white" data-toggle="tooltip" data-placement="bottom" title="1 Coin = 1 Rupee. Earn Nutrify coins on each purchase.">
                                 <i class="fa fa-coins"></i>
                                 <span id="coins-message">Earn <?php echo $coins; ?> My Nutrify Coins On this Order.</span>
                                 <i class="fa fa-info-circle"></i>
                              </button>
                              <div class="pro-qty">
                                 <span class="qty">Quantity:</span>
                                 <div class="plus-minus">
                                    <span>
                                          <a href="javascript:void(0)" class="minus-btn text-black">-</a>
                                          <input type="text" name="name" value="1">
                                          <a href="javascript:void(0)" class="plus-btn text-black">+</a>
                                    </span>
                                 </div>
                              </div>
                              <div class="pro-btn">
                                 <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="<?php echo $product_data[0]["ProductId"]; ?>">
                                            <i class="fa fa-shopping-bag" style="margin-right:8px;"></i>Add to Cart
                                        </a>
                                 <a href="javascript:void(0)" class="btn btn-style1">Buy Now</a>
                              </div>
                        </div>
                     </div>
                  </div>
                  <!-- Right-Side Info Section -->
                  <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12 pro-shipping">
                     <div class="product-service">
                        <div class="icon-title">
                              <span><i class="ti-truck"></i></span>
                              <h4>Delivery info</h4>
                        </div>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                     </div>
                     <div class="product-service">
                        <div class="icon-title">
                              <span><i class="ti-reload"></i></span>
                              <h4>30 days returns</h4>
                        </div>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                     </div>
                     <div class="product-service">
                        <div class="icon-title">
                              <span><i class="ti-id-badge"></i></span>
                              <h4>10 year warranty</h4>
                        </div>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                     </div>
                  </div>
            </div>
         </div>
      </section>


      <!-- product info end -->
      <!-- product page tab start -->
      <section class="section-b-padding pro-page-content">
         <div class="container">
            <div class="row">
               <div class="col">
                  <div class="pro-page-tab">
                     <ul class="nav nav-tabs">
                        <li class="nav-item">
                           <a class="nav-link active" data-bs-toggle="tab" href="#tab-1">Description</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" data-bs-toggle="tab" href="#tab-2">Reviews</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" data-bs-toggle="tab" href="#tab-3">Video</a>
                        </li>
                     </ul>
                     <div class="tab-content">
                        <div class="tab-pane show active" id="tab-1">
                           <div class="tab-2content">
                              <h4 style="margin:10px;">Key specification</h4>
                             <p> <?php echo $product_data[0]["Specification"]; ?></p>
                           </div>
                        </div>
                        <div class="tab-pane  show" id="tab-2">
                           <h4 class="reviews-title">Customer reviews</h4>
                           <div class="customer-reviews t-desk-2">
                           <span class="p-rating">
                              <i class="fa fa-star e-star unselected" data-rating="1"></i>
                              <i class="fa fa-star e-star unselected" data-rating="2"></i>
                              <i class="fa fa-star e-star unselected" data-rating="3"></i>
                              <i class="fa fa-star e-star unselected" data-rating="4"></i>
                              <i class="fa fa-star e-star unselected" data-rating="5"></i>
                           </span>
                              <p class="review-desck">Based on 2 reviews</p>
                              <a href="#add-review" data-bs-toggle="collapse">Write a review</a>
                           </div>
                           <div class="review-form collapse" id="add-review">
                              <h4>Write a review</h4>
                              <form>
                                 <label>Name</label>
                                 <input type="text" name="name" placeholder="Enter your name">
                                 <label>Email</label>
                                 <input type="text" name="mail" placeholder="Enter your Email">
                                 <label>Rating</label>
                                 <span>
                                 <i class="fa fa-star e-star unselected" data-rating="1"></i>
                                 <i class="fa fa-star e-star unselected" data-rating="2"></i>
                                 <i class="fa fa-star e-star unselected" data-rating="3"></i>
                                 <i class="fa fa-star e-star unselected" data-rating="4"></i>
                                 <i class="fa fa-star e-star unselected" data-rating="5"></i>
                                 </span>
                                 <label>Review title</label>
                                 <input type="text" name="mail" placeholder="Review title">
                                 <label>Add comments</label>
                                 <textarea name="comment" placeholder="Write your comments"></textarea>
                              </form>
                           </div>
                           <div class="customer-reviews">
                              <span class="p-rating">
                              <i class="fa fa-star e-star"></i>
                              <i class="fa fa-star e-star"></i>
                              <i class="fa fa-star e-star"></i>
                              <i class="fa fa-star e-star"></i>
                              <i class="fa fa-star-o"></i>
                              </span>
                              <h4 class="review-head">He also good and high product see like look</h4>
                              <span class="reviews-editor">Kelin patel <span class="review-name">on</span> fab 5, 2021</span>
                              <p class="r-description">He also good and high product see like look</p>
                           </div>
                           <div class="customer-reviews">
                              <span class="p-rating">
                              <i class="fa fa-star e-star"></i>
                              <i class="fa fa-star e-star"></i>
                              <i class="fa fa-star e-star"></i>
                              <i class="fa fa-star-o"></i>
                              <i class="fa fa-star-o"></i>
                              </span>
                              <h4 class="review-head">He also good and fresh fruit organic product see like look</h4>
                              <span class="reviews-editor">Melvin louis <span class="review-name">on</span> fab 5, 2021</span>
                              <p class="r-description">He also good and fresh fruit organic product see like look</p>
                           </div>
                        </div>
                        <div class="tab-pane fade show" id="tab-3">
                           <div class="embed-responsive embed-responsive-16by9">
                              <iframe height="630" src="https://www.youtube.com/embed/0etCKCAsImI" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- product page tab end -->
      <!-- releted product start -->
      <section class="section-b-padding pro-releted">
         <div class="container">
            <div class="row">
               <div class="col">
                  <div class="section-title">
                     <h2>Related Products</h2>
                  </div>
                  <div class="trending-products owl-carousel owl-theme">
                     <?php
                        // Get current product's category and subcategory for related products
                        $currentCategoryId = $product_data[0]['CategoryId'];
                        $currentSubCategoryId = $product_data[0]['SubCategoryId'];
                        $currentProductId = $product_data[0]['ProductId'];

                        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                        $Fields = implode(",", $FieldNames);

                        // First try to get products from same subcategory, then same category, excluding current product
                        $relatedQuery = "
                            (SELECT " . $Fields . " FROM product_master
                             WHERE SubCategoryId = ? AND ProductId != ?
                             ORDER BY ProductName ASC LIMIT 8)
                            UNION
                            (SELECT " . $Fields . " FROM product_master
                             WHERE CategoryId = ? AND SubCategoryId != ? AND ProductId != ?
                             ORDER BY ProductName ASC LIMIT 4)
                            ORDER BY ProductName ASC
                            LIMIT 12
                        ";

                        $ParamArray = array($currentSubCategoryId, $currentProductId, $currentCategoryId, $currentSubCategoryId, $currentProductId);
                        $related_products = $obj->MysqliSelect1($relatedQuery, $FieldNames, "iiiii", $ParamArray);

                        // If no related products found, get some random products as fallback
                        if (empty($related_products)) {
                            $related_products = $obj->MysqliSelect1(
                                "SELECT " . $Fields . " FROM product_master WHERE ProductId != ? ORDER BY ProductName ASC LIMIT 12",
                                $FieldNames, "i", array($currentProductId)
                            );
                        }

                        foreach($related_products as $products){
                            $FieldNamesPrice = array("OfferPrice", "MRP");
                            $ParamArrayPrice = array($products["ProductId"]);
                            $FieldsPrice = implode(",", $FieldNamesPrice);
                            $product_prices = $obj->MysqliSelect1(
                                "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                $FieldNamesPrice, 
                                "i", 
                                $ParamArrayPrice
                            );
                        
                        ?>
                     <div class="items" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-top:15px;">
                        <div class="tred-pro">
                           <div class="tr-pro-img">
                              <a href="product_details.php?ProductId=<?php echo $products["ProductId"]; ?>">
                              <img class="img-fluid" src="cms/images/products/<?php echo $products["PhotoPath"]; ?>" alt="<?php echo $products["ProductName"]; ?>">
                              <img class="img-fluid additional-image" src="cms/images/products/<?php echo $products["PhotoPath"]; ?>" alt="<?php echo $products["ProductName"]; ?>"
                                 alt="additional image">
                              </a>
                           </div>
                           <?php 
                              $lowest_price = PHP_INT_MAX; // Initialize to a high value
                              $mrp = PHP_INT_MAX;          // Initialize to a high value
                              $savings = 0;   
                              if (!empty($product_prices)) {
                                  // Loop through all rows and find the lowest MRP and OfferPrice greater than 0
                                  foreach ($product_prices as $product_price) {
                                      $current_offer_price = floatval($product_price["OfferPrice"]);
                                      $current_mrp = floatval($product_price["MRP"]);
                              
                                      if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                                          $lowest_price = $current_offer_price;
                                      }
                                      if ($current_mrp > 0 && $current_mrp < $mrp) {
                                          $mrp = $current_mrp;
                                      }
                                  }
                              
                                  // Reset to "N/A" if no valid prices are found
                                  if ($lowest_price == PHP_INT_MAX) {
                                      $lowest_price = "N/A";
                                  }
                                  if ($mrp == PHP_INT_MAX) {
                                      $mrp = "N/A";
                                  }
                              
                                  // Calculate savings only if valid prices are found
                                  if ($mrp != "N/A" && $lowest_price != "N/A" && $mrp > $lowest_price) {
                                      $savings = $mrp - $lowest_price;
                                  }
                              }
                              if ($savings > 0) {
                                  echo '        <div class="Pro-lable">';
                                  echo '            <span class="p-text">Off ₹' . htmlspecialchars($savings) . '</span>';
                                  echo '        </div>';
                              }
                              ?>
                        </div>
                        <div class="caption">
                           <h3><a href="product.html"><?php echo htmlspecialchars($products["ProductName"]); ?></a></h3>
                           <div class="rating">
                              <i class="fa fa-star c-star"></i>
                              <i class="fa fa-star c-star"></i>
                              <i class="fa fa-star c-star"></i>
                              <i class="fa fa-star-o"></i>
                              <i class="fa fa-star-o"></i>
                           </div>
                           <?php
                              echo '        <div class="pro-price">';
                              echo '            <span class="new-price">Starting from ₹' . htmlspecialchars($lowest_price) . '</span>';
                              if ($mrp != "N/A") {
                                  echo '            <span class="old-price" style="text-decoration: line-through; color: #999;">₹' . htmlspecialchars($mrp) . '</span>';
                              }
                              echo '        </div>';
                                echo '<div class="row">
                                        <div class="col-sm-12">
                                            <div class="pro-btn text-center" style="margin:5px;">
                                                <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="' . $product_data[0]["ProductId"] . '">
                                                    <i class="fa fa-shopping-bag"></i> Add to cart
                                                </a>
                                            </div>
                                        </div>
                                      </div>';
                              ?>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- releted product end -->
      <!-- footer start -->
      <?php include("components/footer.php"); ?>
      <!-- footer end -->
      <!-- back to top start -->
      <a href="javascript:void(0)" class="scroll" id="top">
      <span><i class="fa fa-angle-double-up"></i></span>
      </a>
      <!-- back to top end -->
      <script>
       document.getElementById('pincode').addEventListener('input', function () {
            const pincode = this.value.trim();
            const stateDropdown = document.getElementById('state');
        
            // Clear previous options
            stateDropdown.innerHTML = '<option value="" disabled selected>Select State</option>';
        
            // Proceed only if pincode has exactly 6 digits
            if (pincode.length === 6) {
                // Fetch state using a pincode API
                fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data[0].Status === 'Success') {
                            const state = data[0].PostOffice[0].State;
        
                            // Populate the state dropdown with the fetched state
                            const option = document.createElement('option');
                            option.value = state;
                            option.textContent = state;
                            stateDropdown.appendChild(option);
                            stateDropdown.value = state; // Auto-select the fetched state
                        } else {
                            alert('Invalid pincode or no data available for this pincode.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching state data:', error);
                        alert('Failed to fetch state information. Please try again.');
                    });
            }
        });

        </script>

      <script>
         // Pre-select the first size and show its coins initially
         document.addEventListener('DOMContentLoaded', () => {
            const firstSizeBox = document.querySelector('.size-box');
            if (firstSizeBox) {
               firstSizeBox.classList.add('selected'); // Add 'selected' class to the first size box
               const offerPrice = parseFloat(firstSizeBox.getAttribute('data-offer-price'));
               const mrp = parseFloat(firstSizeBox.getAttribute('data-mrp'));
               const coins = parseInt(firstSizeBox.getAttribute('data-coins'), 10);

               // Call updatePriceAndCoins function with the first size's data
               updatePriceAndCoins(offerPrice, mrp, coins);
            }
         });

         $(function () {
         $('[data-toggle="tooltip"]').tooltip()
         })
         function zoom(e){
           var zoomer = e.currentTarget;
           e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX
           e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX
           x = offsetX/zoomer.offsetWidth*100
           y = offsetY/zoomer.offsetHeight*100
           zoomer.style.backgroundPosition = x + '% ' + y + '%';
         }

         function updatePriceAndCoins(offerPrice, mrp, coins) {
    const priceDiv = document.getElementById('pro-price');
    const coinsMessage = document.getElementById('coins-message');

    // Update the price display
    if (offerPrice > 0 && mrp > 0) {
        const discount = mrp - offerPrice;
        priceDiv.innerHTML = `
            <span class="new-price">₹${offerPrice.toFixed(2)} INR</span>
            <span class="old-price"><del>₹${mrp.toFixed(2)} INR</del></span>
            <div class="Pro-lable">
                <span class="p-discount">₹${discount.toFixed(2)} off</span>
            </div>
        `;
    } else {
        priceDiv.innerHTML = '<span class="new-price">Price not available</span>';
    }

    // Update the coins earned display
    if (coins > 0) {
        coinsMessage.textContent = `Earn ${coins} My Nutrify Coins On this Order.`;
    } else {
        coinsMessage.textContent = 'No Coins Available for this Size.';
    }
}

function handleSizeSelection(event) {
    if (!event || !event.currentTarget) return;

    const sizeBoxes = document.querySelectorAll('.size-box');
    
    // Remove 'selected' class from all size boxes
    sizeBoxes.forEach(sizeBox => {
        sizeBox.classList.remove('selected');
    });

    // Add 'selected' class to the clicked size box
    const selectedBox = event.currentTarget;
    selectedBox.classList.add('selected');

    // Fetch offer price, MRP, and coins for the selected size
    const offerPrice = parseFloat(selectedBox.getAttribute('data-offer-price'));
    const mrp = parseFloat(selectedBox.getAttribute('data-mrp'));
    const coins = parseInt(selectedBox.getAttribute('data-coins'), 10);

    // Call updatePriceAndCoins with the selected size's data
    updatePriceAndCoins(offerPrice, mrp, coins);
}


// Add event listeners to all size boxes for click event
document.querySelectorAll('.size-box').forEach(sizeBox => {
    sizeBox.addEventListener('click', handleSizeSelection);
});

// Optional: Pre-select the first size box on page load
document.addEventListener('DOMContentLoaded', () => {
    const firstSizeBox = document.querySelector('.size-box');
    if (firstSizeBox) {
        firstSizeBox.classList.add('selected'); // Pre-select the first size box
        const offerPrice = parseFloat(firstSizeBox.getAttribute('data-offer-price'));
        const mrp = parseFloat(firstSizeBox.getAttribute('data-mrp'));
        const coins = parseInt(firstSizeBox.getAttribute('data-coins'), 10);

        // Call updatePriceAndCoins with the first size's data
        updatePriceAndCoins(offerPrice, mrp, coins);
    }
});



      </script>
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
         document.querySelectorAll('.size-option').forEach(function (sizeOption) {
             sizeOption.addEventListener('click', function () {
                 var selectedSize = this.getAttribute('data-size');
         
                 // Fetch the corresponding price data for the selected size
                 var priceData = <?php echo json_encode($price_data); ?>;
                 var selectedPrice = priceData[selectedSize];
         
                 if (selectedPrice) {
                     var offerPrice = selectedPrice.offer_price;
                     var mrp = selectedPrice.mrp;
         
                     // Update the displayed prices
                     document.querySelector('.pro-price .new-price').innerText = '₹' + offerPrice.toFixed(2) + ' INR';
                     document.querySelector('.pro-price .old-price del').innerText = '₹' + mrp.toFixed(2) + ' INR';
         
                     // Calculate and update the discount as savings amount
                     if (mrp > offerPrice && mrp > 0 && offerPrice > 0) {
                         var discount = mrp - offerPrice;
                         document.querySelector('.pro-price .p-discount').innerText = '₹' + discount+ ' off';
                     } else {
                         document.querySelector('.pro-price .p-discount').innerText = ''; // Clear discount if invalid
                     }
                 }
             });
         });
         
      </script>
      <script>
    // Wait for the document to be ready
    $(document).ready(function() {
        // Track the selected rating
        let selectedRating = 0;

        // Handle clicking on stars
        $('.e-star').on('click', function() {
            selectedRating = $(this).data('rating');  // Get the rating value from data-rating

            // Change the class of stars based on the selected rating
            $('.e-star').each(function(index) {
                if (index < selectedRating) {
                    $(this).addClass('selected');
                } else {
                    $(this).removeClass('selected');
                }
            });

            // Optionally, update some visual feedback (like a number or message)
            console.log('Selected Rating:', selectedRating);  // You can send this to the backend for saving

            // Update the review text dynamically (for example)
            $('.review-desck').html('Based on ' + selectedRating + ' review(s)');
        });
    });
</script>
<script>
    // function openModal() {
    //   document.getElementById('modalOverlay').style.display = 'flex';
    // }

    // function closeModal() {
    //   document.getElementById('modalOverlay').style.display = 'none';
    // }

    // function proceedToPayment() {
    //   const name = document.getElementById('name').value;
    //   const mobile = document.getElementById('mobile').value;
    //   const address = document.getElementById('address').value;

    //   if (!name || !mobile || !address) {
    //     alert('Please fill all the fields.');
    //     return;
    //   }

    //   // Razorpay Integration
    //   const options = {
    //     key: "YOUR_RAZORPAY_KEY", // Replace with your Razorpay Key
    //     amount: 100000, // Amount in paise (₹1000 = 100000)
    //     currency: "INR",
    //     name: "Pure Nutrition Co",
    //     description: "Order Payment",
    //     handler: function (response) {
    //       alert("Payment successful! Payment ID: " + response.razorpay_payment_id);
    //       closeModal();
    //     },
    //     prefill: {
    //       name: name,
    //       contact: mobile,
    //       email: "user@example.com"
    //     },
    //   };

    //   const rzp = new Razorpay(options);
    //   rzp.open();
    // }
</script>
<script>
  function sendProductDetails() {
    const productId = new URLSearchParams(window.location.search).get('ProductId') || 'Unknown';
    const quantity = document.querySelector('input[name="name"]').value;
    const price = document.querySelector('.new-price')?.textContent.trim() || 'Price not available';
    const selectedSizeBox = document.querySelector('.size-box.selected');

    // Size selection is now optional
    // if (!selectedSizeBox) {
    //     alert('Please select a size before proceeding.');
    //     return;
    // }

    const size = selectedSizeBox ? selectedSizeBox.querySelector('div:nth-child(2)').textContent.trim() : '';
    const productDetails = { productId, quantity, price, size };

    // Show Customer Details Modal
    $('#customerDetailsModal').modal('show');

    document.getElementById('submitCustomerDetails').addEventListener('click', function () {
        const customerName = document.getElementById('customerName').value.trim();
        const customerEmail = document.getElementById('customerEmail').value.trim();
        const customerPhone = document.getElementById('customerPhone').value.trim();

        if (!customerName || !customerEmail || !customerPhone) {
            alert('Please fill out all customer details.');
            return;
        }

        $('#customerDetailsModal').modal('hide');
        $('#shippingAddressModal').modal('show');

        document.getElementById('submitShippingAddress').addEventListener('click', function () {
            const shippingAddress = document.getElementById('shippingAddress').value.trim();
            const city = document.getElementById('city').value.trim();
            const pincode = document.getElementById('pincode').value.trim();
            const state = document.getElementById('state').value.trim();

            if (!shippingAddress || !city || !pincode) {
                alert('Please fill out all shipping details.');
                return;
            }

            $('#shippingAddressModal').modal('hide');

            const finalData = {
                ...productDetails,
                customerName,
                customerEmail,
                customerPhone,
                shippingAddress,
                city,
                pincode,
                state
            };

            // AJAX request to send data to the backend
            fetch('exe_files/direct_buy_now.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(finalData),
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Order placed successfully! Redirecting to payment page...');
                        window.location.href = '/checkout';
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred while processing your request.');
                    console.error(error);
                });
        });
    });
}

// Attach event listener to Buy Now buttons
document.querySelectorAll('.btn-style1').forEach(button => {
    if (button.textContent.trim() === 'Buy Now') {
        button.addEventListener('click', sendProductDetails);
    }
});



</script>

<script>
       $(document).ready(function () {
    // Handle add-to-cart for session-based cart
    $(document).on('click', '.add-to-cart-session', function () {
        const productId = $(this).data('product-id'); // Extract product ID
        
        if (!productId) {
            console.error('Product ID is missing.');
            alert('Unable to add to cart. Product ID is missing.');
            return;
        }

        console.log('Product ID:', productId); // Debugging log

        // Perform AJAX request
        $.ajax({
            url: 'exe_files/add_to_cart_session.php', // Backend endpoint
            type: 'POST',
            dataType: 'json', // Expect a JSON response
            data: {
                action: 'add_to_cart',
                productId: productId
            },
            success: function (response) {
                if (response.status === 'success') {
                    displayCartPopup(); // Show success popup
                } else {
                    alert(response.message || 'Failed to add product to cart.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('An error occurred while processing your request. Please try again.');
            }
        });
    });

    // Display cart popup and reload page after timeout
    function displayCartPopup() {
        const $cartPopup = $('#cart-popup');
        $cartPopup.fadeIn();

        // Auto-hide popup and reload after 3 seconds
        setTimeout(() => {
            $cartPopup.fadeOut(() => location.reload());
        }, 3000);
    }
});

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
   <?php 
      }
       else {
          echo json_encode(array('error' => 'Product ID not provided.'));
      } 
      ?>
</html>
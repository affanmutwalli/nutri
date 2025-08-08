<!DOCTYPE html>
<html lang="en">
   <?php 
   session_start();
      include('includes/urls.php');
      include('database/dbconnection.php');
      $obj = new main();
      $obj->connection();

      // Handle case when no product ID is provided
      if (!isset($_GET['ProductId'])) {
          header("Location: index.php");
          exit();
      }

      if (isset($_GET['ProductId'])) {
          $productId = $_GET['ProductId'];
      
          $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId", "Description", "VideoURL", "Title");

          $ParamArray = array($productId);
          $Fields = implode(",", $FieldNames);
          
          $product_data = $obj->MysqliSelect1(
              "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
              $FieldNames,
              "i",
              $ParamArray
          );

          // Simple check: if no product found or ProductName is empty, show error
          if (!$product_data || empty($product_data) || !isset($product_data[0]) ||
              !isset($product_data[0]['ProductName']) || empty(trim($product_data[0]['ProductName']))) {
              ?>
              <!DOCTYPE html>
              <html lang="en">
              <head>
                  <meta charset="utf-8">
                  <title>Product Not Found</title>
                  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
                  <link rel="stylesheet" type="text/css" href="css/style.css">
              </head>
              <body>
                  <?php include("components/header.php"); ?>
                  <div class="container" style="margin-top: 100px; margin-bottom: 100px; text-align: center;">
                      <div class="row">
                          <div class="col-12">
                              <h1 style="color: #ec6504; font-size: 3rem; margin-bottom: 30px;">Product Currently Unavailable</h1>
                              <p style="font-size: 1.2rem; margin-bottom: 20px;">We're sorry, but this product is currently not available.</p>
                              <p style="font-size: 1.1rem; margin-bottom: 30px;">This item may be temporarily out of stock or has been discontinued. Please check back later or explore our other amazing products!</p>
                              <a href="index.php" class="btn btn-primary" style="margin-right: 10px; padding: 12px 30px;">Return to Homepage</a>
                              <a href="shop.php" class="btn btn-secondary" style="padding: 12px 30px;">Browse All Products</a>
                          </div>
                      </div>
                  </div>
                  <?php include("components/footer.php"); ?>
              </body>
              </html>
              <?php
              exit();
          }

          if ($product_data) {
              // Fetch images for the product
              $FieldNames = array("PhotoPath");
              $ParamArray = array($productId);
              $Fields = implode(",", $FieldNames);
              $model_image = $obj->MysqliSelect1(
                  "SELECT " . $Fields . " FROM model_images WHERE ProductId = ? ORDER BY sort_order ASC, ImageId ASC",
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

               // Initialize price variables to prevent undefined variable warnings
               $lowest_price = "N/A";
               $mrp = "N/A";
               $discount = 0;
               $coins = 0;
               $default_size = "N/A";

               // Process the fetched product prices and sizes
               if (!empty($product_prices) && is_array($product_prices)) {
                  foreach ($product_prices as $product_price) {
                     // Add null checks for all fields
                     $size = isset($product_price["Size"]) ? htmlspecialchars($product_price["Size"]) : '';
                     $offer_price = isset($product_price["OfferPrice"]) ? floatval($product_price["OfferPrice"]) : 0;
                     $mrp = isset($product_price["MRP"]) ? floatval($product_price["MRP"]) : 0;
                     $coins = isset($product_price["Coins"]) ? floatval($product_price["Coins"]) : 0;

                     // Only add the size if OfferPrice and MRP are greater than 0
                     if ($offer_price > 0 && $mrp > 0 && !empty($size)) {
                        $sizes[] = $size;
                        $price_data[$size] = [
                              'offer_price' => $offer_price,
                              'mrp' => $mrp,
                              'coins' => $coins
                        ];
                     }
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


          $FieldNames = array("Product_DetailsId", "ProductId", "PhotoPath", "Description","ImagePath");
          $ParamArray = array($productId);
          $Fields = implode(",", $FieldNames);
          
          $product_details_data = $obj->MysqliSelect1(
              "SELECT " . $Fields . " FROM product_details WHERE ProductId = ? LIMIT 1",
              $FieldNames,
              "i",
              $ParamArray
          );



          $productUrl = urlencode("https://mynutrify.com/product_details.php?ProductId=" . $productId);
          
          if (!empty($product_details_data) && is_array($product_details_data) && isset($product_details_data[0])) {
            $productImage1 = !empty($product_details_data[0]["PhotoPath"]) 
                            ? "cms/images/products/" . $product_details_data[0]["PhotoPath"] 
                            : "images/default.jpg"; 
        
            $productImage2 = !empty($product_details_data[0]["ImagePath"]) 
                            ? "cms/images/products/" . $product_details_data[0]["ImagePath"] 
                            : "images/default.jpg";  // Fallback image
        }
        
          
          // ✅ Now assign the first row to $ingredient
          if (!empty($product_details_data)) {
              $product_details = $product_details_data[0];
          }

          
          $FieldNames = array("IngredientId", "ProductId", "PhotoPath", "IngredientName");
          $ParamArray = array($productId);
          $Fields = implode(",", $FieldNames);         
          $ingredient_data = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM product_ingredients WHERE ProductId = ?",
              $FieldNames,
              "i",
              $ParamArray
             
          );

    
        
          $FieldNames = array("Product_BenefitId", "ProductId", "PhotoPath", "Title", "ShortDescription");
          $ParamArray = array($productId);
          $Fields = implode(",", $FieldNames);
          
          $benefit_data = $obj->MysqliSelect1(
              "SELECT " . $Fields . " FROM product_benefits WHERE ProductId = ?",
              $FieldNames,
              "i",
              $ParamArray
          );
          

          $FieldNames = array("Product_ReviewId", "ProductId", "PhotoPath", "Name", "Review","Date");
          $ParamArray = array($productId);
          $Fields = implode(",", $FieldNames);
          
          $review_data = $obj->MysqliSelect1(
              "SELECT " . $Fields . " FROM product_review WHERE ProductId = ?",
              $FieldNames,
              "i",
              $ParamArray
          );

          $FieldNames = array("FAQId", "ProductId", "Question", "Answer");
          $ParamArray = array($productId);
          $Fields = implode(",", $FieldNames);
          
          $faq_data = $obj->MysqliSelect1(
              "SELECT " . $Fields . " FROM faqs WHERE ProductId = ?",
              $FieldNames,
              "i",
              $ParamArray
          );


          } else {
              // Product not found - redirect to error page
              header("Location: product_not_found.php?id=" . urlencode($productId));
              exit();
          }
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
      <meta property="product:price:amount" content="<?php echo ($lowest_price !== "N/A" && is_numeric($lowest_price)) ? $lowest_price : '0'; ?>"/>
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
    <!-- product name fix -->
    <link rel="stylesheet" type="text/css" href="css/product-name-fix.css">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

      <!-- Tawk.to Integration -->
      <?php include("components/tawk-to.php"); ?>

      <style>
         /* Discount Label Container */
.Discount-Pro-lable {
    position: relative;
    display: inline-flex;
    margin: 10px;
    font-family: 'Helvetica Neue', Arial, sans-serif;
    perspective: 1000px;
}

/* Discount Badge Main Style */
.Discount-p-discount {
    display: flex;
    align-items: center;
    padding: 8px 15px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
    color: white;
    font-size: 14px;
    font-weight: 700;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform-style: preserve-3d;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

/* Premium Ribbon Effect */
.Discount-p-discount::before {
    content: '';
    position: absolute;
    top: -10px;
    right: -25px;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.15);
    transform: rotate(45deg);
}

/* Hover Animation */
.Discount-p-discount:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Price Label Text Enhancement */
.Discount-p-discount::after {
    content: 'OFF';
    margin-left: 8px;
    font-size: 10px;
    opacity: 0.9;
    letter-spacing: 0.5px;
}
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
         margin: 20px 30px; /* Moderate left and right margins for desktop view */
         }
         }
         @media (min-width: 1200px) {
         .desktop-margin {
         margin: 20px 50px; /* More spacing for larger screens but not too much */
         }
         }
         @media (min-width: 1400px) {
         .desktop-margin {
         margin: 20px 80px; /* Maximum spacing for very large screens */
         }
         }

         /* Fix product info text overflow */
         .pro-info {
         word-wrap: break-word;
         overflow-wrap: break-word;
         max-width: 100%;
         }

         .pro-info h4 {
         word-wrap: break-word;
         overflow-wrap: break-word;
         line-height: 1.4;
         margin-bottom: 15px;
         }

         .pro-info p, .pro-info div {
         word-wrap: break-word;
         overflow-wrap: break-word;
         }

         /* Ensure container doesn't overflow */
         .pro-page .container {
         max-width: 100%;
         overflow-x: hidden;
         }

         .pro-page .row {
         margin-left: 0;
         margin-right: 0;
         }

         .pro-page [class*="col-"] {
         padding-left: 15px;
         padding-right: 15px;
         }

         /* Reduce main product section height */
         .pro-page {
         padding: 15px 0;
         }

         .pro-page .container {
         padding: 10px 15px;
         }

         .pro-image {
         margin-bottom: 20px;
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
         transition: transform 1.2s ease, color 1.2s ease;
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
         .specification-tab-2content {
    padding: 15px;

    transition: all 1.2s ease-in-out;
}

.specification-tab-2content h4 {
    font-family: 'Poppins', sans-serif;
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.specification-tab-2content p {
    font-family: 'Poppins', sans-serif;
    font-size: 1rem;
    margin: 10px 0;
    padding: 10px;
    line-height: 1.6;

}

.specification-tab-2content p:hover {
    transform: translateY(-3px);
}

/* Simple Description Section Styles */
.simple-description-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 0;
    text-align: center;
}

.description-text {
    font-size: 16px;
    color: #555;
    line-height: 1.6;
    text-align: center;
    margin-bottom: 15px;
}

.read-more-button {
    background: none;
    border: none;
    color: #EA652D;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    padding: 0;
    margin: 0;
    transition: color 0.3s ease;
    display: block;
    margin: 10px auto 0;
}

.read-more-button:hover {
    color: #d4541f;
}

/* Responsive Design */
@media (max-width: 768px) {
    .specification-tab-2content {
        padding: 10px;
    }

    .specification-tab-2content h4 {
        font-size: 1.3rem;
    }

    .specification-tab-2content p {
        font-size: 0.9rem;
        padding: 8px;
    }

    .simple-description-content {
        padding: 15px 10px;
    }

    .description-text {
        font-size: 15px;
    }

    .read-more-button {
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .specification-tab-2content {
        padding: 8px;
        border-radius: 5px;
    }

    .specification-tab-2content h4 {
        font-size: 1.2rem;
        text-align: center;
    }

    .specification-tab-2content p {
        font-size: 0.85rem;
        padding: 6px;
        text-align: justify;
    }

    .simple-description-content {
        padding: 10px 5px;
    }

    .description-text {
        font-size: 14px;
        line-height: 1.5;
    }

    .read-more-button {
        font-size: 14px;
    }
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
                transition: background-color 1.2s ease;
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
            
            
         
            
            

      </style>
      <style>
      
      

    /* Product navigation menu removed - all sections now display by default */

    /* Review Section Visibility Fix */
    #section6 {
        display: block !important;
        visibility: visible !important;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        padding: 20px 15px;
        margin: 20px 0;
    }

    .review-header-wrapper {
        display: flex !important;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .slider-container {
        display: block !important;
        margin-top: 15px;
    }

    .product-tab-section {
        padding: 15px 10px;
        margin-bottom: 15px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.05);
        transition: all 0.5s ease;
    }

    /* Main container */
 /* ✅ Desktop Grid Layout (Without Shadow) */
.ingredients-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 15px 15px;
    max-width: 1200px;
    margin: 0 auto;
}

.ingredients {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 15px;
    background: white;
    border-radius: 12px;
    transition: transform 1.2s ease;
    /* No shadow on desktop */
    box-shadow: none;
}

/* Image Wrapper */
.image-container {
    width: 280px;
    height: 280px;
    border: 4px solid #EA652D;
    border-radius: 50%;
    overflow: hidden;
    transition: transform 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
    border-radius: 50%;
}

/* Text Styling */
.ingredients-text {
    margin-top: 12px;
    font-size: 18px;
    font-weight: bold;
    color: #305724;
}

/* Hover Effects */
.ingredients:hover {
    transform: translateY(-5px) scale(1.02);
}

.image-container:hover img {
    transform: scale(1.1);
}

/* ✅ Mobile Carousel View (With Shadow) */
@media (max-width: 768px) {
    .ingredients-container {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
        gap: 16px;
        padding: 20px;
        max-width: 100%;
    }

    .ingredients {
        flex: 0 0 calc(70% - 8px);
        min-width: calc(70% - 8px);
        scroll-snap-align: start;
        /* Add shadow in mobile view */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Scrollbar Styling */
    .ingredients-container::-webkit-scrollbar {
        height: 4px;
    }

    .ingredients-container::-webkit-scrollbar-thumb {
        background: #EA652D;
        border-radius: 4px;
    }

    .image-container {
        width: 240px;
        height: 240px;
    }

    .ingredients-text {
        font-size: 16px;
    }
}

/* Small screens (phones) */
@media (max-width: 480px) {
    .ingredients {
        flex: 0 0 calc(90% - 8px);
        min-width: calc(90% - 8px);
        /* Add more prominent shadow on smaller screens */
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    }

    .image-container {
        width: 200px;
        height: 200px;
    }

    .ingredients-text {
        font-size: 14px;
    }
}

/* ✅ Specific styling for ProductId=6 (Amla), ProductId=14 (Wheatgrass), and ProductId=15 (Apple Cider Vinegar) - Centered ingredient */
<?php if (isset($_GET['ProductId']) && ($_GET['ProductId'] == '6' || $_GET['ProductId'] == '14' || $_GET['ProductId'] == '15')): ?>
.ingredients-container {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    margin: 0 auto !important;
    max-width: 1200px !important;
    padding: 40px 20px !important;
}

.ingredients {
    margin-left: 0 !important; /* Centered positioning */
    margin-right: 0 !important;
}
<?php endif; ?>

    /* Title Styling */
    .product-details-title {
        font-size: 42px;
        font-weight: bold;
        color: #305724;
        margin-bottom: 25px;
        transition: transform 1.2s ease-in-out;
        text-align: center;
    }

    .product-details-title span {
        color: #EA652D;
    }

    .product-details-subtitle {
        font-size: 36px;
        font-weight: bold;
        color: #EA652D;
        margin-bottom: 25px;
        text-align: center;
        display: block !important;
        width: 100% !important;
    }

    .product-details-subtitle span {
        color: #EA652D;
    }

    /* Global responsive text styling for product details - Enhanced with higher specificity */
    .product-name,
    .product-title,
    .pro-info h4,
    .pro-info h4 span,
    .pro-page .pro-image .pro-info h4,
    .pro-page .pro-image .pro-info h4 span,
    .caption h3,
    .caption h3 a,
    .items .caption h3,
    .items .caption h3 a,
    .trending-products .items .caption h3 a,
    .product-details-title,
    div.caption h3 a,
    div.items .caption h3 a {
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        hyphens: auto !important;
        line-height: 1.4 !important;
        display: block !important;
        max-width: 100% !important;
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: unset !important;
        width: auto !important;
    }

    /* Override main CSS file text truncation rules for all product listings */
    .list-product .list-items .caption h3 a,
    .grid-list-area .grid-pro ul.grid-product li.grid-items .caption h3 a,
    .grid-2-product .grid-pro ul.grid-product li.grid-items .caption h3 a,
    .grid-4-product .grid-pro ul.grid-product li.grid-items .caption h3 a,
    .footer-style-1-pro .header-pro .caption h3 a {
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: unset !important;
        width: auto !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        line-height: 1.4 !important;
    }

    /* Responsive adjustments for product details text */
    @media (max-width: 768px) {
        .product-details-title {
            font-size: 32px !important;
            margin-bottom: 20px !important;
        }

        .product-details-subtitle {
            font-size: 28px !important;
            margin-bottom: 20px !important;
        }

        .pro-info h4 {
            font-size: 1.4rem !important;
            line-height: 1.3 !important;
        }

        .caption h3 a,
        .items .caption h3 a {
            font-size: 16px !important;
            line-height: 1.3 !important;
        }

        .product-name,
        .product-title {
            font-size: 14px !important;
            line-height: 1.3 !important;
        }
    }

    @media (max-width: 480px) {
        .product-details-title {
            font-size: 28px !important;
            margin-bottom: 15px !important;
        }

        .product-details-subtitle {
            font-size: 24px !important;
            margin-bottom: 15px !important;
        }

        .pro-info h4 {
            font-size: 1.2rem !important;
            line-height: 1.2 !important;
        }

        .caption h3 a,
        .items .caption h3 a {
            font-size: 14px !important;
            line-height: 1.2 !important;
        }

        .product-name,
        .product-title {
            font-size: 12px !important;
            line-height: 1.2 !important;
        }

        .caption {
            padding: 15px !important;
        }
    }

    /* Product Description */
    .product-details-description {
        font-size: 16px;
        color: #444;
        line-height: 1.6;
        max-width: 900px;
        margin: auto;
        transition: opacity 1.2s ease-in-out;
    }

    /* Product Container */
    .product-details-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-start;
        gap: 20px;
        padding: 20px;
    }

    /* Product Image Styling */
    .product-details-image {
        flex: 1 1 250px;
        max-width: 350px;
        height: auto;
    }

    .product-details-image img {
        width: 100%;
        height: auto;
        max-height: 450px;
        object-fit: contain;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        transition: transform 1.2s ease-in-out;
    }

    /* Product Main Slider Styles */
    .product-main-slider-container {
        position: relative;
        width: 100%;
        height: auto;
    }

    .product-main-slider {
        position: relative;
    }

    /* Override zoom figure styling for full bottle visibility */
    .long-img figure.zoom {
        background-size: contain !important;
        background-position: center center !important;
        padding: 20px;
        background-color: #fff;
    }

    .long-img figure.zoom img {
        object-fit: contain !important;
        padding: 20px;
        background: #fff;
    }

    /* Ensure slider items show full bottles */
    .slider-item .long-img {
        background: #fff;
        padding: 10px;
    }

    /* Thumbnail navigation images - full bottle visibility */
    .pro-page-slider .nav-item img {
        object-fit: contain !important;
        padding: 10px;
        background: #fff;
        border-radius: 8px;
    }

    /* Related products at bottom - full bottle visibility */
    .tr-pro-img img {
        object-fit: contain !important;
        padding: 15px;
        background: #fff;
    }

    .product-main-slider .owl-nav {
        position: absolute;
        top: 50%;
        width: 100%;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 10;
    }

    .product-main-slider .owl-nav button.owl-prev,
    .product-main-slider .owl-nav button.owl-next {
        background: rgba(255, 255, 255, 0.9) !important;
        border: 2px solid #ff6b35 !important;
        border-radius: 50% !important;
        width: 50px !important;
        height: 50px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        opacity: 1 !important;
        cursor: pointer !important;
        pointer-events: all !important;
        position: absolute !important;
        font-size: 18px !important;
        color: #ff6b35 !important;
    }

    .product-main-slider .owl-nav button.owl-prev {
        left: -25px !important;
    }

    .product-main-slider .owl-nav button.owl-next {
        right: -25px !important;
    }

    .product-main-slider .owl-dots {
        position: absolute;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 5;
    }

    .product-main-slider .owl-dots .owl-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.7);
        border: 2px solid #ff6b35;
        cursor: pointer;
    }

    .product-main-slider .owl-dots .owl-dot.active {
        background: #ff6b35;
    }

    /* Responsive adjustments for product main slider */
    @media (max-width: 768px) {
        .product-main-slider .owl-nav button.owl-prev,
        .product-main-slider .owl-nav button.owl-next {
            width: 40px !important;
            height: 40px !important;
            font-size: 16px !important;
        }

        .product-main-slider .owl-nav button.owl-prev {
            left: -20px !important;
        }

        .product-main-slider .owl-nav button.owl-next {
            right: -20px !important;
        }

        .product-main-slider .owl-dots {
            bottom: 10px;
        }

        .product-main-slider .owl-dots .owl-dot {
            width: 10px;
            height: 10px;
        }
    }

    @media (max-width: 480px) {
        .product-main-slider .owl-nav button.owl-prev,
        .product-main-slider .owl-nav button.owl-next {
            width: 35px !important;
            height: 35px !important;
            font-size: 14px !important;
        }

        .product-main-slider .owl-nav button.owl-prev {
            left: -15px !important;
        }

        .product-main-slider .owl-nav button.owl-next {
            right: -15px !important;
        }
    }

    /* Ingredients Section */
    .ingredients-section {
        flex: 1;
        max-width: 600px;
        text-align: center;
    }

    .product-details-section {
        max-width: 1500px;
        margin: 0 auto;
        padding: 15px 15px;
        text-align: center;
        background: #fff;
        box-sizing: border-box;
    }

    .ingredients-details-description {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
        margin: 0 auto 3rem;
        padding: 15px;

        width: 90%;
        /* Default width for larger screens */
        max-width: 1500px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .ingredients-details-description {
            width: 95%;
            font-size: 1rem;
        }
        /* Product list details navigation removed */

   
    }

    @media (max-width: 480px) {
        .ingredients-details-description {
            width: 100%;
            font-size: 0.9rem;
            padding: 10px;
        }
    }

    /* Hover Animations */
    .product-details-title:hover {
        transform: scale(1.05);

    }

    .desc-product-images-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .desc-product-image {
        flex: 1 1 200px;
        max-width: 600px;
        min-width: 350px;
        border: 1px solid #e0e0e0;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        background-color: #f9f9f9;
    }

    .desc-product-image img {
        max-width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 8px;
    }

    .ingredients-details-section {
        flex: 1 1 400px;
        max-width: 600px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .product-details-container {
            flex-direction: column;
            text-align: center;
        }

        .product-details-image {
            max-width: 100%;
        }

        .ingredients-section {
            text-align: center;
        }
    }

  /* ✅ Desktop Grid Layout */
.benifit-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 15px;
    padding: 15px 15px;
    background-color: #f9f9f9;
}

/* Card Styling */
.benifit-cart {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    padding: 15px;
    text-align: center;
    transition: transform 1.2s ease, box-shadow 1.2s ease;
    border-top: 4px solid #EA652D;
}

/* Hover Animation */
.benifit-cart:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* Image Styling */
.benifit-cart img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    margin-bottom: 16px;
    transition: transform 1.2s ease;
}

/* Hover Effect */
.benifit-cart:hover img {
    transform: scale(1.1);
}

/* Title Styling */
.benifit-cart h4 {
    font-size: 20px;
    color: #305724;
    margin-bottom: 12px;
    font-weight: 600;
}

/* Description Text */
.benifit-cart p {
    font-size: 15px;
    color: #444;
    line-height: 1.6;
}

/* ✅ Mobile View: Carousel Slider */
@media (max-width: 768px) {
    .benifit-container {
        display: flex;
        flex-direction: row;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        gap: 16px;
        padding: 20px 16px;
        max-width: 100%;
    }

    .benifit-cart {
        flex: 0 0 80%;
        min-width: 280px;
        max-width: 320px;
        scroll-snap-align: start;
        transition: transform 0.3s;
    }

    /* Scrollbar Styling */
    .benifit-container::-webkit-scrollbar {
        height: 5px;
    }

    .benifit-container::-webkit-scrollbar-thumb {
        background: #EA652D;
        border-radius: 4px;
    }

    /* Improve text readability */
    .benifit-cart h4 {
        font-size: 18px;
    }

    .benifit-cart p {
        font-size: 14px;
    }
}

/* Small screens (phones) */
@media (max-width: 480px) {
    .benifit-cart {
        min-width: 90%;
    }
}


  /* ✅ Desktop Grid Layout */
.use-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 15px 15px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Card Styling */
.use-cart {
    background: white;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.use-cart:hover {
    transform: translateY(-5px);
}

.use-cart img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin-bottom: 15px;
    transition: transform 0.3s ease;
    border-radius: 50%;
    padding: 8px;
    background: #fff;
    border: 2px solid #f0f0f0;
}

.use-cart:hover img {
    transform: scale(1.1);
}

.use-cart p {
    font-size: 16px;
    color: #305724;
    font-weight: 500;
}
/* Optional: Add padding to first/last items */
.use-container > *:first-child {
    margin-left: 16px;
}

.use-container > *:last-child {
    margin-right: 16px;
}

/* Optional: Hide scrollbar on non-interaction */
.use-container {
    scrollbar-color: transparent transparent;
}

.use-container:hover {
    scrollbar-color: #EA652D transparent;
}

/* ✅ Mobile Carousel */
@media (max-width: 768px) {
    .use-container {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
        padding: 20px 16px;
        gap: 16px;
        grid-template-columns: unset;
    }

    .use-container::-webkit-scrollbar {
        height: 4px;
    }

    .use-container::-webkit-scrollbar-thumb {
        background: #EA652D;
        border-radius: 4px;
    }

    .use-cart {
        flex: 0 0 calc(80% - 8px);
        min-width: calc(80% - 8px);
        scroll-snap-align: start;
    }
}

@media (max-width: 480px) {
    .use-cart {
        flex: 0 0 calc(90% - 8px);
        min-width: calc(90% - 8px);
    }
    
    .use-cart img {
        width: 80px;
        height: 80px;
    }
    
    .use-cart p {
        font-size: 14px;
    }
}

/* How to Use Section Styles */
.how-to-use-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 15px;
}

.how-to-use-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.use-cart {
    background: white;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border: 2px solid transparent;
    transition: all 1.2s ease;
    position: relative;
}



.use-cart:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    border-color: #ff6b35;
}

.use-cart img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin-bottom: 20px;
    transition: transform 1.2s ease;
}

.use-cart:hover img {
    transform: scale(1.1);
}

.use-cart p {
    font-size: 16px;
    color: #305724;
    font-weight: 500;
    line-height: 1.4;
    margin: 0;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .how-to-use-container {
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(3, 1fr);
        gap: 20px;
    }

    .use-cart {
        padding: 25px 15px;
        min-height: 140px;
    }

    .use-cart img {
        width: 50px;
        height: 50px;
    }

    .use-cart p {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .how-to-use-container {
        grid-template-columns: 1fr;
        grid-template-rows: repeat(6, 1fr);
        gap: 15px;
        padding: 20px 10px;
    }

    .use-cart {
        padding: 20px 15px;
        min-height: 120px;
    }

    .use-cart img {
        width: 45px;
        height: 45px;
    }

    .use-cart p {
        font-size: 13px;
    }
}

    /* Modern Review Grid Styles */
    .reviews-grid-container {
        width: 100%;
        margin: 40px auto 0;
        padding: 0;
    }

    .reviews-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        padding: 0 20px;
    }

    .modern-review-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        transition: all 1.2s ease;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
        font-family: 'Segoe UI', sans-serif;
    }

    .modern-review-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        border-color: #EA652D;
    }

    .modern-review-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%);
    }

    /* Review Card Header Styles */
    .review-card-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .reviewer-avatar {
        position: relative;
        cursor: pointer;
        transition: all 1.2s ease;
    }

    .reviewer-avatar:hover {
        transform: scale(1.05);
    }

    .reviewer-avatar:hover .image-expand-overlay {
        opacity: 1;
    }

    .reviewer-avatar img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #EA652D;
        transition: all 1.2s ease;
    }

    .image-expand-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 1.2s ease;
    }

    .image-expand-overlay i {
        color: white;
        font-size: 16px;
    }

    .verified-badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        background: #28a745;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
    }

    .reviewer-info h6 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 5px 0;
        color: #333;
    }

    .rating-group {
        display: flex;
        gap: 2px;
        margin-bottom: 5px;
    }

    .rating-group i {
        color: #FFD700;
        font-size: 14px;
    }

    .review-date {
        font-size: 0.85rem;
        color: #888;
    }

    /* Review Content Styles */
    .review-content p {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
        margin: 0;
        font-style: italic;
    }

    /* Review Footer Styles */
    .review-card-footer {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .helpful-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .helpful-buttons {
        display: flex;
        gap: 10px;
    }

    .helpful-buttons button {
        background: none;
        border: 1px solid #e0e0e0;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 1.2s ease;
    }

    .helpful-buttons button:hover {
        background: #f8f9fa;
        border-color: #EA652D;
        color: #EA652D;
    }

    /* === ANIMATION === */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Button Styles */
    .load-more-btn {
        background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%);
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 30px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 1.2s ease;
        box-shadow: 0 4px 15px rgba(234, 101, 45, 0.3);
    }

    .load-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(234, 101, 45, 0.4);
    }

    /* === RESPONSIVE DESIGN === */
    @media (max-width: 1200px) {
        .reviews-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            padding: 0 15px;
        }
    }

    @media (max-width: 992px) {
        .reviews-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .modern-review-card {
            padding: 25px;
        }

        .review-header-wrapper {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .overall-rating-display {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .rating-number-large {
            font-size: 3rem !important;
        }
    }

    @media (max-width: 768px) {
        .reviews-grid {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 0 10px;
        }

        .modern-review-card {
            padding: 20px;
        }

        .review-card-header {
            gap: 12px;
        }

        .reviewer-avatar img {
            width: 50px;
            height: 50px;
        }

        .verified-badge {
            width: 18px;
            height: 18px;
            font-size: 9px;
        }
    }

    @media (max-width: 480px) {
        .container-fluid {
            padding: 0 10px !important;
        }

        .product-details-title {
            font-size: 2.8rem !important;
            margin-bottom: 40px !important;
        }

        .review-header-wrapper {
            padding: 25px !important;
        }

        .overall-rating-display {
            gap: 15px !important;
        }

        .rating-number-large {
            font-size: 2.5rem !important;
        }

        .helpful-section {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        /* Full Screen Modal responsive styles */
        .modal-content {
            width: 100%;
            height: 100%;
        }

        .modal-header {
            padding: 12px 15px;
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        .modal-controls {
            width: 100%;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 1.1rem;
        }

        .modal-body {
            padding: 15px;
        }

        #modalImage {
            max-width: calc(100vw - 30px);
            max-height: calc(100vh - 140px);
            border-radius: 8px;
        }

        .zoom-controls {
            padding: 6px 10px;
            gap: 6px;
        }

        .zoom-controls button {
            width: 32px;
            height: 32px;
        }

        #resetZoom {
            padding: 6px 10px;
            font-size: 11px;
        }

        .zoom-hint {
            font-size: 12px;
            padding: 10px 16px;
            bottom: 20px;
        }

        .close-modal {
            width: 36px !important;
            height: 36px !important;
            font-size: 18px !important;
        }
    }

    /* View Full Review Button Styles - Enhanced for visibility */
    .view-full-review {
        background: linear-gradient(135deg, #EA652D, #ff7a45) !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 20px !important;
        margin-top: 12px !important;
        cursor: pointer !important;
        font-size: 12px !important;
        color: white !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 8px rgba(234, 101, 45, 0.3) !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
        z-index: 10 !important;
        width: auto !important;
        height: auto !important;
        font-family: inherit !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        outline: none !important;
    }

    .view-full-review:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(234, 101, 45, 0.4) !important;
        background: linear-gradient(135deg, #d4541f, #EA652D) !important;
    }

    .view-full-review:focus {
        outline: 2px solid #EA652D !important;
        outline-offset: 2px !important;
    }

    /* Review Modal Styles - Increased Size */
    .review-modal-content {
        position: relative;
        width: 95%;
        max-width: 900px;
        max-height: 95vh;
        margin: 2.5vh auto;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 80px rgba(0,0,0,0.4);
        animation: modalFadeIn 0.3s ease-out;
        display: flex;
        flex-direction: column;
    }

    .review-modal-header {
        background: linear-gradient(135deg, #EA652D, #ff7a45);
        padding: 25px 35px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .review-modal-body {
        padding: 35px;
        overflow-y: auto;
        flex: 1;
        max-height: 70vh;
    }

    .review-modal-footer {
        padding: 20px 35px;
        background: #f9f9f9;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
    }

    .review-text {
        font-size: 1.3rem;
        line-height: 1.8;
        color: #444;
    }

    .close-review-modal {
        background: rgba(255,255,255,0.2);
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: white;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .close-review-modal:hover {
        background: rgba(255,255,255,0.3);
    }


    /* Review Header Styles */
    .review-header-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
        margin-top: 0;
        padding: 40px;
        border: none;
        font-family: 'Segoe UI', sans-serif;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        margin-bottom: 50px;
    }

    .review-left-section {
        flex: 1;
        min-width: 300px;
    }

    .overall-rating-display {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .rating-number-large {
        font-size: 4rem;
        font-weight: 700;
        color: #EA652D;
    }

    .star-rating {
        display: inline-block;
        margin-bottom: 8px;
    }

    .star-rating i {
        color: #FFD700;
        font-size: 24px;
        margin-right: 2px;
    }

    .review-count {
        font-weight: 500;
        font-size: 1.2rem;
        color: #666;
    }

    .review-summary-btn {
        background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(234, 101, 45, 0.3);
    }

    .review-summary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(234, 101, 45, 0.4);
    }

    .write-review-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 15px;
    }

    .write-review-btn:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .review-dropdown select {
        padding: 10px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        font-size: 14px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .review-dropdown select:hover,
    .review-dropdown select:focus {
        border-color: #EA652D;
        outline: none;
        box-shadow: 0 0 0 3px rgba(234, 101, 45, 0.1);
    }

    .review-right-section {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }

    /* Image Modal Styles */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
        backdrop-filter: blur(5px);
        animation: modalBackdropFadeIn 0.3s ease-out;
    }

    .image-modal.show {
        display: block;
    }

    .modal-content {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        animation: modalFadeIn 0.3s ease-out;
    }

    .modal-header {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        flex-shrink: 0;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .close-modal:hover {
        background: #f8f9fa;
        color: #EA652D;
        transform: rotate(90deg);
    }

    .modal-body {
        background: transparent;
        padding: 20px;
        overflow: hidden;
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #imageContainer {
        width: 100%;
        height: 100%;
        overflow: hidden;
        cursor: grab;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #imageContainer.grabbing {
        cursor: grabbing;
    }

    #modalImage {
        max-width: calc(100vw - 40px);
        max-height: calc(100vh - 120px);
        object-fit: contain;
        transition: transform 0.1s ease;
        user-select: none;
        pointer-events: none;
        transform-origin: center center;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }

    .zoom-controls {
        display: flex;
        gap: 8px;
        align-items: center;
        background: rgba(255,255,255,0.9);
        padding: 8px 12px;
        border-radius: 25px;
        backdrop-filter: blur(10px);
    }

    .zoom-controls button {
        background: rgba(248,249,250,0.9);
        border: 1px solid #e0e0e0;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .zoom-controls button:hover:not(:disabled) {
        background: #EA652D;
        border-color: #EA652D;
        transform: scale(1.05);
    }

    .zoom-controls button:hover:not(:disabled) i {
        color: white;
    }

    .zoom-controls button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    #resetZoom {
        background: rgba(248,249,250,0.9);
        border: 1px solid #e0e0e0;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        color: #666;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    #resetZoom:hover {
        background: #EA652D;
        border-color: #EA652D;
        color: white;
    }

    .zoom-hint {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 12px 20px;
        border-radius: 25px;
        font-size: 14px;
        opacity: 0.9;
        transition: opacity 0.3s ease;
        pointer-events: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        z-index: 10;
    }

    .zoom-hint.fade-out {
        opacity: 0;
    }

    .grabbing {
        cursor: grabbing !important;
    }

    #imageContainer.grabbing * {
        cursor: grabbing !important;
    }

    @keyframes modalBackdropFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-50%) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }
    }

    /* Review Graph Styles */
    .review-graph-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin: 30px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: none;
    }

    .review-graph-container.show {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .review-graph-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .review-graph-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .close-graph-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .close-graph-btn:hover {
        background: #f0f0f0;
        color: #333;
    }

    .review-stats-container {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 40px;
        align-items: start;
    }

    .overall-rating {
        text-align: center;
        padding: 20px;
    }

    .rating-number {
        font-size: 3rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        display: block;
    }

    .rating-stars-large {
        font-size: 20px;
        color: #FFD700;
        margin-bottom: 15px;
    }

    .total-reviews {
        color: #666;
        font-size: 1rem;
        margin-bottom: 20px;
    }

    .rating-breakdown {
        flex: 1;
    }

    .rating-row {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        gap: 15px;
    }

    .rating-label {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 80px;
        font-size: 14px;
        color: #333;
    }

    .rating-label i {
        color: #FFD700;
        font-size: 12px;
    }

    .rating-bar-container {
        flex: 1;
        height: 8px;
        background: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .rating-bar {
        height: 100%;
        background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
        border-radius: 4px;
        transition: width 0.8s ease-out;
        width: 0%;
    }

    .rating-count {
        min-width: 40px;
        text-align: right;
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }

    .review-highlights {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #f0f0f0;
    }

    .highlights-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .highlight-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .highlight-tag {
        background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .review-stats-container {
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .rating-number {
            font-size: 2.5rem;
        }

        .review-graph-container {
            padding: 20px;
        }

        /* Review Modal Mobile Styles */
        .review-modal-content {
            width: 98%;
            max-width: 600px;
            margin: 1vh auto;
            max-height: 98vh;
        }

        .review-modal-header {
            padding: 20px 25px;
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        .reviewer-info {
            flex-direction: column;
            gap: 8px !important;
            align-items: flex-start !important;
        }

        .review-modal-body {
            padding: 25px;
            max-height: 60vh;
        }

        .review-text {
            font-size: 1rem;
            line-height: 1.6;
        }

        .view-full-review {
            padding: 6px 12px !important;
            font-size: 11px !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            margin-top: 10px !important;
        }
    }

    .review-tag-pills {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .review-tag-pills span {
        background-color: #e0e0e0;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        color: #333;
    }

    .review-right-section {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }

    .write-review-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .write-review-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }

    .review-dropdown select {
        padding: 10px 15px;
        font-size: 14px;
        border-radius: 25px;
        border: 2px solid #e0e0e0;
        background-color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }

    .review-dropdown select:focus {
        border-color: #EA652D;
        box-shadow: 0 0 0 3px rgba(234, 101, 45, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .review-header-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .review-right-section {
            justify-content: flex-start;
        }
    }

           /* FAQ Accordion */
    .faq-accordion__content-inner {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-top: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .faq-accordion-queans {
        border-bottom: 1px solid #ddd;
        padding: 15px 0;
    }

    .faq-accordion-queans:last-child {
        border-bottom: none;
    }

    .faq-accordion-queans h6 {
        font-size: 18px;
        font-weight: bold;
        color: #EA652D;
        cursor: pointer;
        transition: color 0.3s ease-in-out;
    }

    .faq-accordion-queans h6:hover {
        color: #305724;
    }

    .faq-accordion-queans p {
        font-size: 16px;
        color: #333;
        display: none;
        transition: all 0.3s ease-in-out;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .faq-head {
            flex-direction: column;
            text-align: center;
        }

        .faq-heading {
            justify-content: center;
        }

        .faq-img img {
            width: 60px;
        }

        .faq-accordion__content-inner {
            padding: 15px;
        }

        .faq-accordion-queans h6 {
            font-size: 16px;
        }

        .faq-accordion-queans p {
            font-size: 14px;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    .faq-accordion {}

    .faq-accordion__item {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
        background: white;
        border-radius: 12px;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .faq-accordion__item:hover {
        transform: translateY(-2px);
    }

    .faq-accordion__title {
        padding: 1.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .faq-accordion__title h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #305724;
    }

    .faq-accordion__title::after {
        content: '+';
        font-size: 1.5rem;
        color: #EA652D;
        transition: transform 0.3s ease;
    }

    .faq-accordion__item.is-expanded .faq-accordion__title::after {
        transform: rotate(360deg);
    }

    .faq-accordion__content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-accordion__content-inner {
        padding: 0 1.5rem 1.5rem;
        color: #4a5568;
    }

    /* Focus States */
    .faq-accordion__title:focus-visible {
        outline: 2px solid #EA652D;
        outline-offset: 2px;
        border-radius: 6px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        /* h1 {
    font-size: 2rem;
  } */

        .faq-accordion__title h5 {
            font-size: 1rem;
        }

        .faq-accordion__content-inner {
            font-size: 0.9rem;
        }
    }

    /* Loading Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .faq-accordion__item {
        animation: fadeIn 0.5s ease forwards;
        opacity: 0;
    }

    .faq-accordion__item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .faq-accordion__item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .faq-accordion__item:nth-child(3) {
        animation-delay: 0.3s;
    }

    .faq-accordion__item:nth-child(4) {
        animation-delay: 0.4s;
    }

    .faq-accordion-queans {
        border-bottom: 1px solid #e2e8f0;
        /* Light grey border for separation */
        padding: 1rem 0;
        transition: background-color 0.3s ease-in-out;
    }

    .faq-accordion-queans h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        /* Dark grey color for better readability */
        margin-bottom: 0.5rem;
    }

    .faq-accordion-queans p {
        font-size: 0.95rem;
        color: #4a5568;
        /* Slightly lighter grey for paragraph text */
        line-height: 1.5;
    }

    .faq-accordion-queans:hover {
        background-color: #f7fafc;
        /* Light hover effect */
    }

    @media (max-width: 768px) {
        .faq-accordion-queans h6 {
            font-size: 1rem;
        }

        .faq-accordion-queans p {
            font-size: 0.85rem;
        }
    }

    /* Ensure all product sections are visible and properly styled */
    [id^="section"] {
        min-height: 80px;
        padding: 10px 0;
        margin-bottom: 15px;
    }

    /* Make sure sections are visible when selected */
    .product-tab-section {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Ensure proper spacing between sections */
    .section-b-padding {
        padding: 15px 0;
    }

    /* Read More Button Styling for Short Descriptions */
    .product-description-container {
        position: relative;
        margin-bottom: 20px;
    }

    .read-more-btn {
        background: linear-gradient(135deg, #ec6504, #ff8533);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(236, 101, 4, 0.3);
        font-family: 'Inter', sans-serif;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        position: relative;
        overflow: hidden;
    }

    .read-more-btn:hover {
        background: linear-gradient(135deg, #d35400, #ec6504);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(236, 101, 4, 0.4);
    }

    .read-more-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(236, 101, 4, 0.3);
    }

    .read-more-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .read-more-btn:hover::before {
        left: 100%;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .read-more-btn {
            padding: 8px 16px;
            font-size: 13px;
            margin-top: 12px;
        }
    }

    @media (max-width: 480px) {
        .read-more-btn {
            padding: 6px 14px;
            font-size: 12px;
            margin-top: 10px;
            width: 100%;
            max-width: 200px;
        }
    }


      </style>
      <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1209485663860371');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1209485663860371&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
   </head>
   <body class="home-1">
       

      <?php include("components/header.php"); ?>
          <div id="cart-popup" class="cart-popup-overlay">
    <div class="cart-popup-content">
        <button class="close-popup" onclick="document.getElementById('cart-popup').style.display='none';">
            &times;
        </button>
        <h3>Product added to your cart!</h3>
        <div class="cart-popup-actions">
            <a href="cart.php" class="btn-view-cart">View Cart</a>
            <a href="checkout.php" class="btn-checkout">Checkout</a>
        </div>
    </div>
</div>
      <!-- product info start -->
      <section class="pro-page desktop-margin">
         <div class="container">
            <div class="row">
                  <!-- Product Images Section -->
                  <div class="col-xl-12 col-lg-12 col-md-12 col-12 pro-image">
                     <div class="row">
                        <!-- Main Image with Slider -->
                        <div class="col-lg-6 col-xl-6 col-md-6 col-12 larg-image">
                              <div class="product-main-slider-container">
                                 <div class="owl-carousel product-main-slider">
                                    <!-- Main Product Image -->
                                    <div class="slider-item">
                                       <a href="javascript:void(0)" class="long-img" style="border: 1px solid rgba(0, 0, 0, 0.1); border-radius: 10px;">
                                             <?php
                                                $mainPhotoPath = isset($product_data[0]["PhotoPath"]) && !empty($product_data[0]["PhotoPath"])
                                                               ? htmlspecialchars($product_data[0]["PhotoPath"])
                                                               : 'default.jpg';
                                             ?>
                                             <figure class="zoom" onmousemove="zoom(event)" style="background-image: url('cms/images/products/<?php echo $mainPhotoPath; ?>');">
                                                <img src="cms/images/products/<?php echo $mainPhotoPath; ?>" class="img-fluid" alt="image">
                                             </figure>
                                       </a>
                                    </div>

                                    <!-- Additional Model Images -->
                                    <?php if (!empty($model_image) && is_array($model_image)) {
                                       foreach ($model_image as $index => $model_images) {
                                          // Add null check for PhotoPath
                                          $photoPath = isset($model_images["PhotoPath"]) && !empty($model_images["PhotoPath"])
                                                      ? htmlspecialchars($model_images["PhotoPath"])
                                                      : 'default.jpg';
                                          ?>
                                             <div class="slider-item">
                                                <a href="javascript:void(0)" class="long-img" style="border: 1px solid rgba(0, 0, 0, 0.1); border-radius: 10px;">
                                                   <figure class="zoom" onmousemove="zoom(event)" style="background-image: url('cms/images/products/<?php echo $photoPath; ?>')">
                                                         <img src="cms/images/products/<?php echo $photoPath; ?>" class="img-fluid" alt="image">
                                                   </figure>
                                                </a>
                                             </div>
                                    <?php } } ?>
                                 </div>
                              </div>
                              <ul class="nav nav-tabs pro-page-slider owl-carousel owl-theme">
                                 <li class="nav-item items">
                                    <a class="nav-link active" href="javascript:void(0)">
                                          <img src="cms/images/products/<?php echo $mainPhotoPath; ?>" class="img-fluid" alt="image">
                                    </a>
                                 </li>
                                 <?php if (!empty($model_image) && is_array($model_image)) {
                                    foreach ($model_image as $index => $model_images) {
                                       // Add null check for PhotoPath
                                       $photoPath = isset($model_images["PhotoPath"]) && !empty($model_images["PhotoPath"])
                                                   ? htmlspecialchars($model_images["PhotoPath"])
                                                   : 'default.jpg';
                                       ?>
                                          <li class="nav-item items">
                                             <a class="nav-link" href="javascript:void(0)">
                                                <img src="cms/images/products/<?php echo $photoPath; ?>" class="img-fluid" alt="image">
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
                              
                              <div class="pro-availabale">
                                 <span class="available">Availability:</span>
                                 <span class="pro-instock">In stock</span>
                              </div>
                              <div class="mrp-label">
                                 <span>MRP (including all taxes):</span>
                              </div> 
                              <div class="pro-price" id="pro-price">
                                 <?php
                                    // Ensure variables are defined and check them properly
                                    if (isset($lowest_price) && isset($mrp) &&
                                        $lowest_price !== "N/A" && $mrp !== "N/A" &&
                                        is_numeric($lowest_price) && is_numeric($mrp) &&
                                        $lowest_price > 0 && $mrp > 0) {
                                       $display_lowest_price = (float)$lowest_price;
                                       $display_mrp = (float)$mrp;
                                       $display_discount = $display_mrp - $display_lowest_price;
                                 ?>
                                    <span class="new-price">₹<?php echo number_format($display_lowest_price, 2); ?> INR</span>
                                    <span class="old-price"><del>₹<?php echo number_format($display_mrp, 2); ?> INR</del></span>
                                    <?php if ($display_discount > 0) { ?>
                                    <div class="Discount-Pro-lable">
                                       <span class="Discount-p-discount">₹<?php echo number_format($display_discount, 2); ?></span>
                                    </div>
                                    <?php } ?>
                                 <?php } else { ?>
                                    <span class="new-price">Price not available</span>
                                 <?php } ?>
                              </div>
                              <div class="product-description-container">
                                 <?php
                                 $productShortDesc = $product_data[0]["ShortDescription"] ?? "";
                                 $productShortDescTruncated = mb_substr($productShortDesc, 0, 100, 'UTF-8');
                                 $productHasMoreContent = mb_strlen($productShortDesc, 'UTF-8') > 100;
                                 ?>

                                 <p id="product-short-description-short">
                                     <?php echo htmlspecialchars($productShortDescTruncated); ?>
                                     <?php if ($productHasMoreContent): ?>
                                         <span id="product-short-description-dots">...</span>
                                     <?php endif; ?>
                                 </p>

                                 <?php if ($productHasMoreContent): ?>
                                     <p id="product-short-description-full" style="display: none;">
                                         <?php echo htmlspecialchars($productShortDesc); ?>
                                     </p>

                                     <button class="read-more-btn" id="product-short-read-more-btn" onclick="toggleDescription('product-short')">
                                         Read More
                                     </button>
                                 <?php endif; ?>
                              </div>
                              <h6 class="pro-size" style="margin-top: 5px;">Size: </h6>
                              <div class="pro-items">
                                 <?php if (!empty($sizes)) { ?>
                                    <div class="size-container">
                                          <?php foreach ($sizes as $index => $size) {
                                             $offer_price = $price_data[$size]['offer_price'] ?? 0;
                                             $mrp = $price_data[$size]['mrp'] ?? 0;
                                             $coins = $price_data[$size]['coins'] ?? 0;
                                             $discount = $mrp - $offer_price;
                                          ?>
                                             <div class="size-box"
                                                data-offer-price="<?php echo $offer_price; ?>"
                                                data-mrp="<?php echo $mrp; ?>"
                                                data-coins="<?php echo $coins; ?>"
                                                data-index="<?php echo $index; ?>"
                                                onclick="handleSizeSelection(event)"
                                                style="cursor: pointer;">
                                                <div style="color: #305724; font-weight: bold;">Save ₹<?php echo number_format((float)$discount, 2); ?></div>
                                                <div><?php echo htmlspecialchars($size ?? ""); ?></div>
                                                <div class="size-price">
                                                   <div>₹<?php echo number_format((float)$offer_price, 2); ?> <del>₹<?php echo number_format((float)$mrp, 2); ?></del></div>
                                                </div>
                                             </div>

                                          <?php } ?>
                                    </div>
                                 <?php } else { ?>
                                    <div class="alert alert-warning" role="alert">
                                       <i class="fa fa-exclamation-triangle"></i> No sizes available for this product at the moment.
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
                                 
                                        <a href="checkout.php" class="btn btn-style1 buy-now-btn"
                                           data-product-id="<?php echo $product_data[0]["ProductId"]; ?>"
                                           data-size="" data-quantity="1">Buy Now</a>


                              </div>
                        </div>
                     </div>
                  </div>
                  <!-- Right-Side Info Section -->
                  <!--<div class="col-xl-3 col-lg-12 col-md-12 col-xs-12 pro-shipping">-->
                  <!--   <div class="product-service">-->
                  <!--      <div class="icon-title">-->
                  <!--            <span><i class="ti-truck"></i></span>-->
                  <!--            <h4>Delivery info</h4>-->
                  <!--      </div>-->
                  <!--      <p>Orders are processed and shipped within 1-3 business days. Delivery times may vary based on location.</p>-->
                  <!--   </div>-->
                  <!--   <div class="product-service">-->
                  <!--      <div class="icon-title">-->
                  <!--            <span><i class="ti-reload"></i></span>-->
                  <!--            <h4>3-Day Returns</h4>-->
                  <!--      </div>-->
                  <!--      <p>Returns are accepted within 3 days for damaged, defective or incorrect products. Items must be unused and in original packaging. Contact us at support@purenutritionco.com with photos/videos to initiate a refund.</p>-->
                  <!--   </div>-->
                  <!--   <div class="product-service">-->
                  <!--      <div class="icon-title">-->
                  <!--          <span><i class="ti-id-badge"></i></span>-->
                  <!--          <h4>Certified Products</h4>-->
                  <!--      </div>-->
                  <!--      <div class="icon-title">-->
                  <!--          <p><i class="fa fa-check"></i></p>-->
                  <!--          <p style="margin-left: 10px;">FSSAI License</p>-->
                  <!--      </div>-->
                  <!--      <div class="icon-title">-->
                  <!--          <p><i class="fa fa-check"></i></p>-->
                  <!--          <p style="margin-left: 10px;">AYUSH Manufacturing License</p>-->
                  <!--      </div>-->
                  <!--      <div class="icon-title">-->
                  <!--          <p><i class="fa fa-check"></i></p>-->
                  <!--          <p style="margin-left: 10px;">GMP Certification</p>-->
                  <!--      </div>-->
                  <!--      <div class="icon-title">-->
                  <!--          <p><i class="fa fa-check"></i></p>-->
                  <!--          <p style="margin-left: 10px;">BPA-Free</p>-->
                  <!--      </div>-->
                  <!--      <div class="icon-title">-->
                  <!--          <p><i class="fa fa-check"></i></p>-->
                  <!--          <p style="margin-left: 10px;">Halal Certification</p>-->
                  <!--      </div>-->
                  <!--      <div class="icon-title">-->
                  <!--          <p><i class="fa fa-check"></i></p>-->
                  <!--          <p style="margin-left: 10px;">Make in India</p>-->
                  <!--      </div>                        -->
                  <!--  </div>-->
                  </div>
            </div>
         </div>
      </section>


      <!-- product info end -->
      <!-- product page tab start -->
<!--      <section class="section-b-padding pro-page-content" style="padding-top:30px;">-->
<!--         <div class="container">-->
<!--            <div class="row">-->
<!--               <div class="col">-->
<!--                  <div class="pro-page-tab">-->
<!--                     <ul class="nav nav-tabs">-->
<!--                        <li class="nav-item">-->
<!--                           <a class="nav-link active" data-bs-toggle="tab" href="#tab-1">Description</a>-->
<!--                        </li>-->
<!--                        <li class="nav-item">-->
<!--                           <a class="nav-link" data-bs-toggle="tab" href="#tab-2">Reviews</a>-->
<!--                        </li>-->
<!--                        <li class="nav-item">-->
<!--                           <a class="nav-link" data-bs-toggle="tab" href="#tab-3">Video</a>-->
<!--                        </li>-->
<!--                     </ul>-->
<!--                     <div class="tab-content">-->
<!--                        <div class="tab-pane show active" id="tab-1">-->
<!--                           <div class="specification-tab-2content">-->
<!--                              <h4 style="margin:10px;">Key specification</h4>-->
<!--                             
<!--                           </div>-->
<!--                        </div>-->
<!--                        <div class="tab-pane  show" id="tab-2">-->
<!--                           <h4 class="reviews-title">Customer reviews</h4>-->
<!--                           <div class="customer-reviews t-desk-2">-->
<!--                           <span class="p-rating">-->
<!--                              <i class="fa fa-star e-star unselected" data-rating="1"></i>-->
<!--                              <i class="fa fa-star e-star unselected" data-rating="2"></i>-->
<!--                              <i class="fa fa-star e-star unselected" data-rating="3"></i>-->
<!--                              <i class="fa fa-star e-star unselected" data-rating="4"></i>-->
<!--                              <i class="fa fa-star e-star unselected" data-rating="5"></i>-->
<!--                           </span>-->
<!--                              <p class="review-desck">Based on 2 reviews</p>
<!--                              <a href="#add-review" data-bs-toggle="collapse">Write a review</a>
<!--                           </div>-->
<!--                           <div class="review-form collapse" id="add-review">-->
<!--                              <h4>Write a review</h4>-->
<!--                              <form>-->
<!--                                 <label>Name</label>-->
<!--                                 <input type="text" name="name" placeholder="Enter your name">-->
<!--                                 <label>Email</label>-->
<!--                                 <input type="text" name="mail" placeholder="Enter your Email">-->
<!--                                 <label>Rating</label>-->
<!--                                 <span>-->
<!--                                 <i class="fa fa-star e-star unselected" data-rating="1"></i>-->
<!--                                 <i class="fa fa-star e-star unselected" data-rating="2"></i>-->
<!--                                 <i class="fa fa-star e-star unselected" data-rating="3"></i>-->
<!--                                 <i class="fa fa-star e-star unselected" data-rating="4"></i>-->
<!--                                 <i class="fa fa-star e-star unselected" data-rating="5"></i>-->
<!--                                 </span>-->
<!--                                 <label>Review title</label>-->
<!--                                 <input type="text" name="mail" placeholder="Review title">-->
<!--                                 <label>Add comments</label>-->
<!--                                 <textarea name="comment" placeholder="Write your comments"></textarea>-->
<!--                              </form>-->
<!--                           </div>-->
<!--                           <div class="customer-reviews">-->
<!--                              <span class="p-rating">-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->

<!--                              </span>-->
<!--                              <h4 class="review-head">He also good and high product see like look</h4>-->
<!--                              <span class="reviews-editor">Sahil Mujawar <span class="review-name">on</span> jan 23, 2025</span>-->
<!--                              <p class="r-description">I noticed some benefits, but I expected more from the product. The taste could also be improved. Overall, it's decent.</p>-->
<!--                           </div>-->
<!--                           <div class="customer-reviews">-->
<!--                              <span class="p-rating">-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                                                            <i class="fa fa-star e-star"></i>-->

<!--                              <i class="fa fa-star-o"></i>-->
<!--                              </span>-->
<!--                              <h4 class="review-head">Decent Product</h4>-->
<!--                              <span class="reviews-editor">Shubham Khenche <span class="review-name"> on</span> fab 5, 2025</span>-->
<!--                              <p class="r-description">The product is good, but I didn't see the results I was hoping for. I might try a different one next time.</p>-->
<!--                           </div>-->
<!--                           <div class="customer-reviews">-->
<!--                              <span class="p-rating">-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              <i class="fa fa-star e-star"></i>-->
<!--                                                            <i class="fa fa-star e-star"></i>-->

<!--                              <i class="fa fa-star e-star"></i>-->
<!--                              </span>-->
<!--                              <h4 class="review-head">Highly Recommend!</h4>-->
<!--                              <span class="reviews-editor">Amar yadav <span class="review-name"> on</span> mar 1, 2025</span>-->
<!--                              <p class="r-description">I've been using My Nutrify's herbal supplements for a month now, and I can already feel a significant boost in my energy levels. Highly recommend!</p>-->
<!--                           </div>-->
<!--                        </div>-->
<!--                      <div class="tab-pane show" id="tab-3">-->
 
<!--</div>-->


<!--                     </div>-->
<!--                  </div>-->
<!--               </div>-->
<!--            </div>-->
<!--         </div>-->
<!--      </section>-->
      
      
    <!-- Display all product sections without categorization -->
      <section class="" style="margin-top: 20px;">
        <div class="container">
            <?php
            $filteredProductTitle = str_replace("My Nutrify Herbal & Ayurveda's", "", $product_data[0]["Title"]);
            ?>
            <div class="product-details-section" id="section1">
                <h1 class="product-details-title">
                    My Nutrify Herbal & Ayurveda's
                </h1>
                <h2 class="product-details-subtitle">
                    <span><?php echo isset($filteredProductTitle) && trim($filteredProductTitle) !== '' ? nl2br(htmlspecialchars(trim($filteredProductTitle))) : 'Product'; ?></span>
                </h2>

            </div>

            <!-- Long Description Section -->
            <?php if (!empty($product_details) && !empty($product_details['Description'])): ?>
            <section id="long-description-section" style="margin-top: 30px;">
                <div class="container">
                    <div class="simple-description-content">
                        <div class="description-text" id="description-text">
                            <?php
                            $description = htmlspecialchars($product_details['Description']);
                            $words = explode(' ', $description);
                            $preview_words = array_slice($words, 0, 30); // Show first 30 words
                            $preview_text = implode(' ', $preview_words);
                            $full_text = $description;
                            ?>
                            <span id="description-preview"><?php echo nl2br($preview_text); ?><?php if (count($words) > 30): ?>...</span>
                            <span id="description-full" style="display: none;"><?php echo nl2br($full_text); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (count($words) > 30): ?>
                        <button id="read-more-btn" class="read-more-button" onclick="toggleLongDescription()">Read More</button>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <section class="section-b-padding pro-releted" id="section2">
                <h1 class="product-details-title">
                    Key <span>INGREDIENTS</span>
                </h1>

                <?php if (!empty($product_details_data)): ?>
                <div class="product-container">
                    <div class="desc-product-images-wrapper">
                        <div class="desc-product-image">
                            <img src="<?php echo ($productImage1); ?>" alt="<?php echo ($productImage1); ?>">
                        </div>
                        <div class="desc-product-image">
                            <img src="<?php echo ($productImage2); ?>" alt="<?php echo ($productImage2); ?>">
                        </div>
                    </div>

                    <div class="product-details-section">
                        <?php if (!empty($product_details) && !empty($product_details['Description'])): ?>
                        
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div style="text-align: center; color: #666; font-size: 16px; padding: 20px;">
                    <p>No detailed ingredient information available for this product.</p>
                </div>
                <?php endif; ?>

            <?php if (!empty($ingredient_data) && is_array($ingredient_data)): ?>
            <div class="ingredients-container" >
                <?php foreach ($ingredient_data as $ingredient): ?>
                <div class="ingredients">
                    <div class="image-container">
                        <img src="<?php echo !empty($ingredient['PhotoPath'])
                        ? "cms/images/ingredient/" . htmlspecialchars($ingredient['PhotoPath'])
                        : "images/default.jpg"; ?>"
                            alt="<?php echo htmlspecialchars($ingredient['IngredientName']); ?>">
                    </div>
                    <p class="ingredients-text"><?php echo htmlspecialchars($ingredient['IngredientName']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="ingredients-container">
                <p style="text-align: center; color: #666; font-size: 16px; padding: 20px;">No ingredients information available for this product.</p>
            </div>
            <?php endif; ?>
            </section>
            <section class="section-b-padding pro-releted" id="section3" style="margin-top: 15px;">
                 <h1 class="product-details-title">
                  Why Drink My Nutrify Herbal & Ayurveda's?
                </h1>
                <h2 class="product-details-subtitle">
                    <span><?php echo nl2br(htmlspecialchars(trim($filteredProductTitle))); ?></span>
                </h2>
                <?php if (!empty($benefit_data)): ?>
                <div class="benifit-container">
                    <?php foreach ($benefit_data as $benefit): ?>
                    <div class="benifit-cart">
                        <img src="<?php echo !empty($benefit['PhotoPath'])
                    ? 'cms/images/ingredient/' . htmlspecialchars($benefit['PhotoPath'])
                    : 'images/default.jpg'; ?>"
                            alt="<?php echo htmlspecialchars(pathinfo($benefit['PhotoPath'], PATHINFO_FILENAME)); ?>">
                        <h4><?php echo htmlspecialchars($benefit['Title']); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($benefit['ShortDescription'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="benifit-container">
                    <p style="text-align: center; color: #666; font-size: 16px; padding: 20px;">No benefits information available for this product.</p>
                </div>
                <?php endif; ?>
                       </section>
            <section class=" pro-releted" id="section4">
                
                <h1 class="product-details-title">
                  Direction To Use
<span>
  
</span>
                </h1>
                <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 16px;">Follow these simple steps for best results</p>

                <div class="how-to-use-wrapper">
                    <div class="how-to-use-container">
                        <div class="use-cart">
                            <img src="cms/images/products/shake.png" alt="">
                            <p>Shake the bottle before use.</p>
                        </div>
                        <div class="use-cart">
                            <img src="cms/images/products/glass.png" alt="">
                            <p>Add 30ml juice in 30ml water.</p>
                        </div>
                        <div class="use-cart">

                            <img src="cms/images/products/stunk.png" alt="">
                            <p>Take on an empty stomach in the' orig and 30 mins after dinner.</p>
                        </div>
                        <div class="use-cart">
                            <img src="cms/images/products/temp.png" alt="">
                            <p>Keep in a cold and dry place.</p>
                        </div>
                        <div class="use-cart">
                            <img src="cms/images/products/bootle.png" alt="">
                            <p>Close the bottle tightly.</p>
                        </div>
                        <div class="use-cart">
                            <img src="cms/images/products/time.png" alt="">
                            <p>Consume within 1 month after opening.</p>
                        </div>
                    </div>
                </div>
                <div class="tab-pane show" id="tab-3">
                    <div class="embed-responsive embed-responsive-16by9"
                        style="max-width: 100%; height: auto; position: relative;">
                        <?php if (!empty($product_data[0]["VideoURL"])) { 
            $videoURL = htmlspecialchars($product_data[0]["VideoURL"], ENT_QUOTES, 'UTF-8');

            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/|live\/))([\w-]+)/', $videoURL, $matches)) {
                $videoID = $matches[1];
                $embedURL = "https://www.youtube.com/embed/$videoID?autoplay=1&loop=1&playlist=$videoID&mute=1"; // Starts muted
        ?>
                        <iframe id="youtubeVideo" width="100%" height="600" src="<?php echo $embedURL; ?>"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
                            style="border-radius: 10px; max-width: 100%;">
                        </iframe>

                        <!-- Mute/Unmute Button -->
                        <button id="muteToggle" style="
                    position: absolute;
                    bottom: 20px;
                    left: 20px;
                    background: rgba(0, 0, 0, 0.7);
                    color: white;
                    border: none;
                    padding: 10px 15px;
                    cursor: pointer;
                    border-radius: 5px;
                ">Unmute</button>


                        <?php 
            } else {
                echo "Invalid YouTube URL.";
            }
        } ?>
                    </div>
                </div>
            </section>
            <section class="section-b-padding pro-releted" id="section5">
                <h1 class="product-details-title">
                    Your Certified
                    <span>Trusted Product
                    </span>
                </h1>
                <div class="use-container">
                    <div class="use-cart">
                        <div>
                            <img src="cms/images/products/Make In India.png" alt="">
                            <p>Make in India </p>
                        </div>
                    </div>
                    <div class="use-cart">
                        <div>
                            <img src="cms/images/products/Bpa free.png" alt="">
                            <p>BPA Free </p>
                        </div>
                    </div>
                    <div class="use-cart">
                        <div>
                            <img src="cms/images/products/Gmp.png" alt="">
                            <p>GMP Certified</p>
                        </div>
                    </div>
                    <div class="use-cart">
                        <div>
                            <img src="cms/images/products/Fssai.png" alt="">
                            <p>Fssai Approved </p>
                        </div>
                    </div>
                    <div class="use-cart">
                        <div>
                            <img src="cms/images/products/How To Use E Commerce.png" alt="">
                            <p>Ayush Approved </p>
                        </div>
                    </div>
                </div>
            </section>


            <section class="section-b-padding pro-releted" id="section7">
                <h1 class="product-details-title">
                    Frequently Asked Questions
                    <span>(FAQ's)
                    </span>
                </h1>
                <?php if (!empty($faq_data) && is_array($faq_data)): ?>
                <?php foreach ($faq_data as $faq): ?>
                <article class="faq-accordion__item js-show-item-default" data-binding="expand-accordion-item">
                    <span id="tab<?php echo $faq['FAQId']; ?>" tabindex="0" class="faq-accordion__title"
                        aria-controls="panel<?php echo $faq['FAQId']; ?>" role="tab" aria-selected="false"
                        aria-expanded="false" data-binding="expand-accordion-trigger">
                        <h5><?php echo htmlspecialchars($faq["Question"]); ?></h5>
                    </span>

                    <div id="panel<?php echo $faq['FAQId']; ?>" class="faq-accordion__content" role="tabpanel"
                        aria-hidden="true" aria-labelledby="tab<?php echo $faq['FAQId']; ?>"
                        data-binding="expand-accordion-container">
                        <div class="faq-accordion__content-inner">
                            <div class="faq-accordion-queans">
                                <h6><?php echo nl2br(htmlspecialchars($faq["Answer"])); ?></h6>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No FAQs available for this product.</p>
                <?php endif; ?>
            </section>

            <section class="section-b-padding pro-releted" id="section6" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 30px 0;">
                <div class="container-fluid" style="max-width: 100%; padding: 0 20px;">
                    <h1 class="product-details-title" style="text-align: center; margin-bottom: 30px; font-size: 3rem; color: #333; font-weight: 700;">
                        Customer <span style="color: #EA652D;">Reviews</span>
                    </h1>
                    <div class="review-header-wrapper" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); margin-bottom: 30px;">
                        <div class="review-left-section">
                            <div class="overall-rating-display" style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                                <div class="rating-number-large" style="font-size: 4rem; font-weight: 700; color: #EA652D;">5.0</div>
                                <div>
                                    <div class="star-rating" style="margin-bottom: 8px;">
                                        <i class="fa fa-star" style="color: #FFD700; font-size: 24px;"></i>
                                        <i class="fa fa-star" style="color: #FFD700; font-size: 24px;"></i>
                                        <i class="fa fa-star" style="color: #FFD700; font-size: 24px;"></i>
                                        <i class="fa fa-star" style="color: #FFD700; font-size: 24px;"></i>
                                        <i class="fa fa-star" style="color: #FFD700; font-size: 24px;"></i>
                                    </div>
                                    <span class="review-count" style="font-size: 1.2rem; color: #666;">
                                        <?php
                                        $reviewCount = !empty($review_data) ? count($review_data) : 0;
                                        echo "Based on " . $reviewCount . " review" . ($reviewCount != 1 ? "s" : "");
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div style="margin-top: 20px;">
                                <button class="review-summary-btn" onclick="toggleReviewGraph()" style="background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%); border: none; color: white; padding: 12px 24px; border-radius: 25px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;">✨ Show reviews summary</button>
                            </div>
                        </div>

                        <div class="review-right-section" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                            <button class="write-review-btn" onclick="toggleReviewForm()" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 12px 24px; border: none; border-radius: 25px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;">✍️ Write A Review</button>
                            <div class="review-dropdown">
                                <select style="padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 25px; font-size: 14px; background: white; cursor: pointer;">
                                    <option>Latest First</option>
                                    <option>Top Reviews</option>
                                    <option>Image First</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Review Graph (Hidden by default) -->
                    <div class="review-graph-container" id="review-graph">
                        <div class="review-graph-header">
                            <h3 class="review-graph-title">Customer Reviews Summary</h3>
                            <button class="close-graph-btn" onclick="toggleReviewGraph()">&times;</button>
                        </div>

                        <div class="review-stats-container">
                            <div class="overall-rating">
                                <span class="rating-number">5.0</span>
                                <div class="rating-stars-large">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                                <div class="total-reviews">
                                    <?php
                                    $reviewCount = !empty($review_data) ? count($review_data) : 0;
                                    echo "Based on " . $reviewCount . " review" . ($reviewCount != 1 ? "s" : "");
                                    ?>
                                </div>
                            </div>

                            <div class="rating-breakdown">
                                <div class="rating-row">
                                    <div class="rating-label">
                                        <span>5</span>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="rating-bar-container">
                                        <div class="rating-bar" data-width="85"></div>
                                    </div>
                                    <div class="rating-count"><?php echo max(1, floor($reviewCount * 0.85)); ?></div>
                                </div>

                                <div class="rating-row">
                                    <div class="rating-label">
                                        <span>4</span>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="rating-bar-container">
                                        <div class="rating-bar" data-width="10"></div>
                                    </div>
                                    <div class="rating-count"><?php echo max(0, floor($reviewCount * 0.10)); ?></div>
                                </div>

                                <div class="rating-row">
                                    <div class="rating-label">
                                        <span>3</span>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="rating-bar-container">
                                        <div class="rating-bar" data-width="3"></div>
                                    </div>
                                    <div class="rating-count"><?php echo max(0, floor($reviewCount * 0.03)); ?></div>
                                </div>

                                <div class="rating-row">
                                    <div class="rating-label">
                                        <span>2</span>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="rating-bar-container">
                                        <div class="rating-bar" data-width="1"></div>
                                    </div>
                                    <div class="rating-count"><?php echo max(0, floor($reviewCount * 0.01)); ?></div>
                                </div>

                                <div class="rating-row">
                                    <div class="rating-label">
                                        <span>1</span>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="rating-bar-container">
                                        <div class="rating-bar" data-width="1"></div>
                                    </div>
                                    <div class="rating-count"><?php echo max(0, floor($reviewCount * 0.01)); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="review-highlights">
                            <h4 class="highlights-title">What customers love most:</h4>
                            <div class="highlight-tags">
                                <span class="highlight-tag">Effective Results</span>
                                <span class="highlight-tag">Natural Ingredients</span>
                                <span class="highlight-tag">Great Taste</span>
                                <span class="highlight-tag">Fast Delivery</span>
                                <span class="highlight-tag">Good Packaging</span>
                                <span class="highlight-tag">Value for Money</span>
                            </div>
                        </div>
                    </div>

                    <!-- Review Form (Hidden by default) -->
                    <div class="review-form collapse" id="add-review" style="display: none; background: white; padding: 20px; border-radius: 15px; margin-top: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                        <h4 style="color: #333; margin-bottom: 20px; font-size: 1.5rem;">Write a Review</h4>
                        <form style="display: grid; gap: 15px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Name</label>
                                    <input type="text" name="name" placeholder="Enter your name" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Email</label>
                                    <input type="email" name="mail" placeholder="Enter your Email" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                                </div>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">Rating</label>
                                <div style="display: flex; gap: 5px; margin-bottom: 10px;">
                                    <i class="fa fa-star e-star unselected" data-rating="1" style="font-size: 20px; color: #ddd; cursor: pointer;"></i>
                                    <i class="fa fa-star e-star unselected" data-rating="2" style="font-size: 20px; color: #ddd; cursor: pointer;"></i>
                                    <i class="fa fa-star e-star unselected" data-rating="3" style="font-size: 20px; color: #ddd; cursor: pointer;"></i>
                                    <i class="fa fa-star e-star unselected" data-rating="4" style="font-size: 20px; color: #ddd; cursor: pointer;"></i>
                                    <i class="fa fa-star e-star unselected" data-rating="5" style="font-size: 20px; color: #ddd; cursor: pointer;"></i>
                                </div>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Review Title</label>
                                <input type="text" name="review_title" placeholder="Review title" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Your Review</label>
                                <textarea name="comment" placeholder="Write your detailed review here..." rows="4" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
                            </div>
                            <button type="submit" style="background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%); color: white; padding: 15px 30px; border: none; border-radius: 25px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; justify-self: start;">Submit Review</button>
                        </form>
                    </div>
                </div>
                <?php if (!empty($review_data)): ?>
                <div class="reviews-grid-container" style="width: 100%; margin: 0 auto;">
                    <div class="reviews-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; padding: 0 20px;">
                        <?php foreach ($review_data as $review): ?>
                        <div class="modern-review-card" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid #f0f0f0; position: relative; overflow: hidden;">
                            <div class="review-card-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                                <div class="reviewer-avatar" style="position: relative; cursor: pointer;" onclick="openImageModal('cms/images/ingredient/<?php echo htmlspecialchars($review['PhotoPath']); ?>', '<?php echo htmlspecialchars($review['Name']); ?>')">
                                    <img src="cms/images/ingredient/<?php echo htmlspecialchars($review['PhotoPath']); ?>"
                                        alt="<?php echo htmlspecialchars($review['Name']); ?>"
                                        style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #EA652D; transition: all 0.3s ease;">
                                    <div class="verified-badge" style="position: absolute; bottom: -2px; right: -2px; background: #28a745; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 10px;">✓</div>
                                    <div class="image-expand-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s ease;">
                                        <i class="fa fa-expand" style="color: white; font-size: 16px;"></i>
                                    </div>
                                </div>
                                <div class="reviewer-info">
                                    <h6 style="font-size: 1.1rem; font-weight: 600; margin: 0 0 5px 0; color: #333;"><?php echo htmlspecialchars($review['Name']); ?></h6>
                                    <div class="rating-group" style="display: flex; gap: 2px; margin-bottom: 5px;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa fa-star" style="color: #FFD700; font-size: 14px;" aria-hidden="true"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="review-date" style="font-size: 0.85rem; color: #888;"><?php echo date("F j, Y", strtotime($review['Date'])); ?></span>
                                </div>
                            </div>
                            <div class="review-content">
                                <?php
                                $reviewText = $review['Review'];
                                $isLongReview = strlen($reviewText) > 200;
                                $displayText = $isLongReview ? substr($reviewText, 0, 200) . '...' : $reviewText;
                                ?>
                                <p style="font-size: 1rem; color: #555; line-height: 1.6; margin: 0; font-style: italic;">"<?php echo nl2br(htmlspecialchars($displayText)); ?>"</p>

                                <!-- View Full Review button - ALWAYS shown for ALL reviews -->
                                <button class="view-full-review"
                                        onclick="openReviewModal('<?php echo htmlspecialchars(addslashes($reviewText), ENT_QUOTES); ?>', '<?php echo htmlspecialchars(addslashes($review['Name']), ENT_QUOTES); ?>', '<?php echo date("F j, Y", strtotime($review['Date'])); ?>')"
                                        style="background: linear-gradient(135deg, #EA652D, #ff7a45) !important;
                                               border: none !important;
                                               padding: 8px 16px !important;
                                               border-radius: 20px !important;
                                               margin-top: 12px !important;
                                               cursor: pointer !important;
                                               font-size: 12px !important;
                                               color: white !important;
                                               transition: all 0.3s ease !important;
                                               box-shadow: 0 2px 8px rgba(234, 101, 45, 0.3) !important;
                                               display: block !important;
                                               visibility: visible !important;
                                               opacity: 1 !important;">
                                    <i class="fa fa-expand" style="margin-right: 6px;"></i> View Full Review
                                </button>
                            </div>
                            <div class="review-card-footer" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                                <div class="helpful-section" style="display: flex; align-items: center; justify-content: space-between;">
                                    <span style="font-size: 0.9rem; color: #666;">Was this helpful?</span>
                                    <div class="helpful-buttons" style="display: flex; gap: 10px;">
                                        <button style="background: none; border: 1px solid #e0e0e0; padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">👍 Yes</button>
                                        <button style="background: none; border: 1px solid #e0e0e0; padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">👎 No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Load More Button -->
                    <div class="load-more-section" style="text-align: center; margin-top: 30px;">
                        <button class="load-more-btn" style="background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%); color: white; padding: 15px 40px; border: none; border-radius: 30px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(234, 101, 45, 0.3);">
                            Load More Reviews
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="no-reviews-section" style="text-align: center; color: #666; font-size: 18px; padding: 40px 20px; background: white; border-radius: 20px; margin-top: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                    <div style="font-size: 64px; color: #EA652D; margin-bottom: 30px;">💬</div>
                    <h3 style="color: #333; margin-bottom: 15px; font-size: 2rem; font-weight: 600;">No Reviews Yet</h3>
                    <p style="margin-bottom: 30px; font-size: 1.1rem; color: #666;">Be the first to share your experience with this amazing product!</p>
                    <button onclick="toggleReviewForm()" style="background: linear-gradient(135deg, #EA652D 0%, #d4541f 100%); color: white; padding: 15px 30px; border: none; border-radius: 30px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(234, 101, 45, 0.3);">✍️ Write First Review</button>
                </div>
                <?php endif; ?>
            </section>

            <!-- Full Screen Image Modal with Zoom -->
            <div id="imageModal" class="image-modal" onclick="closeImageModal()" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(5px);">
                <div class="modal-content" onclick="event.stopPropagation()" style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column; animation: modalFadeIn 0.3s ease-out;">
                    <div class="modal-header" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                        <h3 id="modalTitle" style="margin: 0; color: #333; font-size: 1.3rem; font-weight: 600;">Reviewer Image</h3>
                        <div class="modal-controls" style="display: flex; gap: 15px; align-items: center;">
                            <div class="zoom-controls" style="display: flex; gap: 8px; align-items: center; background: rgba(255,255,255,0.9); padding: 8px 12px; border-radius: 25px; backdrop-filter: blur(10px);">
                                <button id="zoomOut" onclick="zoomImage(-0.2)" style="background: rgba(248,249,250,0.9); border: 1px solid #e0e0e0; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;" title="Zoom Out">
                                    <i class="fa fa-minus" style="font-size: 14px; color: #666;"></i>
                                </button>
                                <span id="zoomLevel" style="font-size: 13px; color: #666; min-width: 45px; text-align: center; font-weight: 500;">100%</span>
                                <button id="zoomIn" onclick="zoomImage(0.2)" style="background: rgba(248,249,250,0.9); border: 1px solid #e0e0e0; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;" title="Zoom In">
                                    <i class="fa fa-plus" style="font-size: 14px; color: #666;"></i>
                                </button>
                                <button id="resetZoom" onclick="resetZoom()" style="background: rgba(248,249,250,0.9); border: 1px solid #e0e0e0; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 12px; color: #666; transition: all 0.3s ease; font-weight: 500;" title="Reset Zoom">Reset</button>
                            </div>
                            <button class="close-modal" onclick="closeImageModal()" style="background: rgba(255,255,255,0.9); border: 1px solid #e0e0e0; font-size: 20px; cursor: pointer; color: #666; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s ease; backdrop-filter: blur(10px);" title="Close">&times;</button>
                        </div>
                    </div>
                    <div class="modal-body" style="background: transparent; padding: 20px; overflow: hidden; position: relative; flex: 1; display: flex; align-items: center; justify-content: center;">
                        <div id="imageContainer" style="width: 100%; height: 100%; overflow: hidden; cursor: grab; position: relative; display: flex; align-items: center; justify-content: center;">
                            <img id="modalImage" src="" alt="" style="max-width: calc(100vw - 40px); max-height: calc(100vh - 120px); object-fit: contain; transition: transform 0.1s ease; user-select: none; pointer-events: none; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
                        </div>
                        <div class="zoom-hint" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.8); color: white; padding: 12px 20px; border-radius: 25px; font-size: 14px; opacity: 0.9; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                            <i class="fa fa-mouse-pointer" style="margin-right: 8px;"></i>
                            Scroll to zoom • Drag to pan • ESC to close
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Review Modal - Increased Size -->
            <div id="reviewModal" class="review-modal" onclick="closeReviewModal()" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.9); backdrop-filter: blur(5px);">
                <div class="review-modal-content" onclick="event.stopPropagation()" style="position: relative; width: 95%; max-width: 900px; max-height: 95vh; margin: 2.5vh auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 80px rgba(0,0,0,0.4); animation: modalFadeIn 0.3s ease-out; display: flex; flex-direction: column;">
                    <div class="review-modal-header" style="background: linear-gradient(135deg, #EA652D, #ff7a45); padding: 25px 35px; display: flex; justify-content: space-between; align-items: center; color: white;">
                        <div>
                            <h3 id="reviewModalTitle" style="margin: 0 0 8px 0; font-size: 1.6rem; font-weight: 600;">Customer Review</h3>
                            <div class="reviewer-info" style="display: flex; align-items: center; gap: 20px;">
                                <div class="reviewer-name" style="font-size: 1.1rem; opacity: 0.9;">by <span id="reviewerName">Customer</span></div>
                                <div class="review-date" id="reviewDate" style="font-size: 1rem; opacity: 0.8;"></div>
                            </div>
                        </div>
                        <button class="close-review-modal" onclick="closeReviewModal()" style="background: rgba(255,255,255,0.2); border: none; font-size: 22px; cursor: pointer; color: white; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s ease;">&times;</button>
                    </div>
                    <div class="review-modal-body" style="padding: 35px; overflow-y: auto; flex: 1; max-height: 70vh;">
                        <div class="review-text" style="font-size: 1.3rem; line-height: 1.8; color: #444;">
                            <p id="fullReviewText" style="white-space: pre-line; margin: 0; font-style: italic;"></p>
                        </div>
                    </div>
                    <div class="review-modal-footer" style="padding: 20px 35px; background: #f9f9f9; border-top: 1px solid #eee; display: flex; justify-content: flex-end;">
                        <button onclick="closeReviewModal()" style="background: #EA652D; color: white; border: none; padding: 12px 24px; border-radius: 25px; cursor: pointer; font-size: 16px; transition: all 0.3s ease; font-weight: 500;">Close Review</button>
                    </div>
                </div>
            </div>


        </div>
        </div>
    </section>

    <script>
    // All sections are now displayed by default - no JavaScript needed for section switching

    // Modern Review Interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Helpful button interactions
        const helpfulButtons = document.querySelectorAll('.helpful-buttons button');
        helpfulButtons.forEach(button => {
            button.addEventListener('click', function() {
                const isYes = this.textContent.includes('👍');
                this.style.background = isYes ? '#28a745' : '#dc3545';
                this.style.color = 'white';
                this.style.borderColor = isYes ? '#28a745' : '#dc3545';

                // Disable both buttons in this group
                const siblingButton = isYes ? this.nextElementSibling : this.previousElementSibling;
                if (siblingButton) {
                    siblingButton.disabled = true;
                    siblingButton.style.opacity = '0.5';
                }
                this.disabled = true;
            });
        });

        // Load more functionality
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';

                // Simulate loading
                setTimeout(() => {
                    this.innerHTML = 'Load More Reviews';
                    // Here you would typically load more reviews via AJAX
                }, 1500);
            });
        }

        // Review card animations
        const reviewCards = document.querySelectorAll('.modern-review-card');
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        reviewCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });

        // Modal close on backdrop click
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageModal();
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    });

    // Image Modal Functions with Zoom
    let currentZoom = 1;
    let isDragging = false;
    let startX, startY, translateX = 0, translateY = 0;

    function openImageModal(imageSrc, reviewerName) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const imageContainer = document.getElementById('imageContainer');

        modalImage.src = imageSrc;
        modalImage.alt = reviewerName + "'s profile image";
        modalTitle.textContent = reviewerName + "'s Profile Image";

        // Reset zoom and position
        currentZoom = 1;
        translateX = 0;
        translateY = 0;
        updateImageTransform();
        updateZoomLevel();

        modal.style.display = 'block';
        modal.classList.add('show');

        // Prevent body scroll when modal is open
        document.body.style.overflow = 'hidden';

        // Add fade-in animation
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);

        // Initialize zoom and pan functionality
        initializeZoomPan();

        // Add keyboard support
        document.addEventListener('keydown', handleKeyPress);

        // Hide zoom hint after 4 seconds
        setTimeout(() => {
            const hint = document.querySelector('.zoom-hint');
            if (hint) hint.classList.add('fade-out');
        }, 4000);
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');

        modal.style.opacity = '0';

        setTimeout(() => {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';

            // Reset zoom and position
            currentZoom = 1;
            translateX = 0;
            translateY = 0;
            updateImageTransform();

            // Remove keyboard listener
            document.removeEventListener('keydown', handleKeyPress);
        }, 300);
    }

    // Keyboard support
    function handleKeyPress(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    }

    // Enhanced Zoom Functions
    function zoomImage(delta) {
        const newZoom = Math.max(0.25, Math.min(5, currentZoom + delta));
        currentZoom = newZoom;
        updateImageTransform();
        updateZoomLevel();

        // Reset position if zoomed out to 100% or less
        if (currentZoom <= 1) {
            translateX = 0;
            translateY = 0;
            updateImageTransform();
        }

        // Update cursor style based on zoom level
        const imageContainer = document.getElementById('imageContainer');
        if (imageContainer) {
            imageContainer.style.cursor = currentZoom > 1 ? 'grab' : 'default';
        }
    }

    function resetZoom() {
        currentZoom = 1;
        translateX = 0;
        translateY = 0;
        updateImageTransform();
        updateZoomLevel();

        // Reset cursor
        const imageContainer = document.getElementById('imageContainer');
        if (imageContainer) {
            imageContainer.style.cursor = 'default';
            imageContainer.classList.remove('grabbing');
        }
    }

    function updateImageTransform() {
        const modalImage = document.getElementById('modalImage');
        if (modalImage) {
            modalImage.style.transform = `scale(${currentZoom}) translate(${translateX / currentZoom}px, ${translateY / currentZoom}px)`;
            modalImage.style.transformOrigin = 'center center';
        }
    }

    function updateZoomLevel() {
        const zoomLevel = document.getElementById('zoomLevel');
        if (zoomLevel) {
            zoomLevel.textContent = Math.round(currentZoom * 100) + '%';
        }

        // Update zoom button states
        const zoomInBtn = document.getElementById('zoomIn');
        const zoomOutBtn = document.getElementById('zoomOut');

        if (zoomInBtn) {
            zoomInBtn.disabled = currentZoom >= 5;
            zoomInBtn.style.opacity = currentZoom >= 5 ? '0.5' : '1';
        }

        if (zoomOutBtn) {
            zoomOutBtn.disabled = currentZoom <= 0.25;
            zoomOutBtn.style.opacity = currentZoom <= 0.25 ? '0.5' : '1';
        }
    }

    // Initialize zoom and pan functionality
    function initializeZoomPan() {
        const imageContainer = document.getElementById('imageContainer');
        const modalImage = document.getElementById('modalImage');

        if (!imageContainer || !modalImage) return;

        // Mouse wheel zoom
        imageContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            zoomImage(delta);
        });

        // Touch zoom (pinch)
        let initialDistance = 0;
        let initialZoom = 1;

        imageContainer.addEventListener('touchstart', function(e) {
            if (e.touches.length === 2) {
                initialDistance = getDistance(e.touches[0], e.touches[1]);
                initialZoom = currentZoom;
            } else if (e.touches.length === 1) {
                startDrag(e.touches[0].clientX, e.touches[0].clientY);
            }
        });

        imageContainer.addEventListener('touchmove', function(e) {
            e.preventDefault();
            if (e.touches.length === 2) {
                const currentDistance = getDistance(e.touches[0], e.touches[1]);
                const scale = currentDistance / initialDistance;
                currentZoom = Math.max(0.5, Math.min(3, initialZoom * scale));
                updateImageTransform();
                updateZoomLevel();
            } else if (e.touches.length === 1 && isDragging) {
                drag(e.touches[0].clientX, e.touches[0].clientY);
            }
        });

        imageContainer.addEventListener('touchend', function(e) {
            if (e.touches.length === 0) {
                endDrag();
            }
        });

        // Mouse drag
        imageContainer.addEventListener('mousedown', function(e) {
            if (currentZoom > 1) {
                startDrag(e.clientX, e.clientY);
            }
        });

        document.addEventListener('mousemove', function(e) {
            if (isDragging) {
                drag(e.clientX, e.clientY);
            }
        });

        document.addEventListener('mouseup', endDrag);
    }

    function getDistance(touch1, touch2) {
        const dx = touch1.clientX - touch2.clientX;
        const dy = touch1.clientY - touch2.clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    function startDrag(x, y) {
        if (currentZoom > 1) {
            isDragging = true;
            startX = x - translateX;
            startY = y - translateY;
            document.getElementById('imageContainer').classList.add('grabbing');
        }
    }

    function drag(x, y) {
        if (isDragging) {
            translateX = x - startX;
            translateY = y - startY;

            // Limit drag to prevent image from going too far
            const maxTranslate = 100 * currentZoom;
            translateX = Math.max(-maxTranslate, Math.min(maxTranslate, translateX));
            translateY = Math.max(-maxTranslate, Math.min(maxTranslate, translateY));

            updateImageTransform();
        }
    }

    function endDrag() {
        isDragging = false;
        const imageContainer = document.getElementById('imageContainer');
        if (imageContainer) {
            imageContainer.style.cursor = currentZoom > 1 ? 'grab' : 'default';
            imageContainer.classList.remove('grabbing');
        }
    }
    </script>
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
                        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                        $ParamArray = array();
                        $Fields = implode(",", $FieldNames);
                        $product_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_master ORDER BY RAND()", $FieldNames, "", $ParamArray);
                        
                        foreach($product_data as $products){
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
                                  echo '        <div class="Discount-Pro-lable">';
                                  echo '            <span class="p-text">Off ₹' . htmlspecialchars($savings) . '</span>';
                                  echo '        </div>';
                              }
                              ?>
                        </div>
                        <div class="caption">
                                <h3><a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>"><?php echo htmlspecialchars($products["ProductName"]); ?></a></h3>
                           <div class="rating">
                              <i class="fa fa-star c-star"></i>
                              <i class="fa fa-star c-star"></i>
                              <i class="fa fa-star c-star"></i>
                              <i class="fa fa-star-o"></i>
                              <i class="fa fa-star-o"></i>
                           </div>
                           <div class="pro-price">
                              <span class="new-price">Starting from ₹<?php echo htmlspecialchars($lowest_price); ?></span>
                              <?php if ($mrp != "N/A" && $mrp != PHP_INT_MAX): ?>
                                  <span class="old-price" style="text-decoration: line-through; color: #999;">₹<?php echo htmlspecialchars($mrp); ?></span>
                              <?php endif; ?>
                           </div>
                           <div class="row">
                               <div class="col-sm-12">
                                   <div class="pro-btn text-center" style="margin:5px;">
                                       <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="<?php echo $products["ProductId"]; ?>">
                                           <i class="fa fa-shopping-bag"></i> Add to cart
                                       </a>
                                   </div>
                               </div>
                           </div>
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
       // Check if element exists before adding event listener
       const pincodeElement = document.getElementById('pincode');
       if (pincodeElement) {
           pincodeElement.addEventListener('input', function () {
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
       } // Close the if statement for pincode element check

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
            <div class="Discount-Pro-lable">
                <span class="Discount-p-discount">₹${discount.toFixed(2)}</span>
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
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            let buyNowButtons = document.querySelectorAll(".buy-now-btn");
        
            buyNowButtons.forEach(function (button) {
                button.addEventListener("click", function (event) {
                    event.preventDefault();
        
                    let productId = this.getAttribute("data-product-id");
                    let sizeElement = document.querySelector(".size-box.selected");
        
                    if (!sizeElement) {
                        alert("Please select a size before proceeding.");
                        return;
                    }
        
                    let rawSize = sizeElement.textContent.trim(); // Original text including price & discount
                    let size = rawSize.replace(/Save ₹\d+/g, '') // Remove "Save ₹xx"
                                      .replace(/₹\d+\.\d{2}/g, '') // Remove prices (₹499.00, ₹549.00)
                                      .trim(); // Trim extra spaces
        
                    let quantity = document.querySelector(".plus-minus input").value;
                    let offerPrice = sizeElement.getAttribute("data-offer-price");
                    let mrp = sizeElement.getAttribute("data-mrp");
        
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "exe_files/buy_now_session.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.send(`product_id=${productId}&quantity=${quantity}&size=${size}&offer_price=${offerPrice}&mrp=${mrp}`);
        
                    xhr.onload = function () {
                        var xhr = new XMLHttpRequest();
                        xhr.open("GET", "check_session.php", true); // Check if the user is logged in
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                // Parse the response
                                var response = JSON.parse(xhr.responseText);

                                // Check if the user is logged in
                                if (response.loggedIn) {
                                    // Redirect to checkout page for logged-in user
                                    window.location.href = "checkout.php";
                                } else {
                                    // Redirect to login page for guests
                                    alert("Please login to proceed with checkout.");
                                    window.location.href = "login.php";
                                }
                            }
                        };
                        xhr.send();

                    };
                });
            });
        
            let sizeBoxes = document.querySelectorAll(".size-box");
            sizeBoxes.forEach(function (sizeBox) {
                sizeBox.addEventListener("click", function () {
                    sizeBoxes.forEach(box => box.classList.remove("selected"));
                    this.classList.add("selected");
        
                    let buyNowButton = document.querySelector(".buy-now-btn");
                    buyNowButton.setAttribute("data-size", this.textContent.trim());
                    buyNowButton.setAttribute("data-offer-price", this.getAttribute("data-offer-price"));
                    buyNowButton.setAttribute("data-mrp", this.getAttribute("data-mrp"));
                });
            });
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
                         document.querySelector('.pro-price .Discount-p-discount').innerText = '₹' + discount+ ' off';
                     } else {
                         document.querySelector('.pro-price .Discount-p-discount').innerText = ''; // Clear discount if invalid
                     }
                 }
             });
         });
         
      </script>
      <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var iframe = document.getElementById("youtubeVideo");
                        var muteToggle = document.getElementById("muteToggle");

                        // Check if elements exist before proceeding
                        if (!iframe || !muteToggle) {
                            return; // Exit if elements don't exist
                        }

                        // Function to send messages to the YouTube iframe
                        function sendMessage(action) {
                            iframe.contentWindow.postMessage(JSON.stringify({
                                event: "command",
                                func: action,
                                args: []
                            }), "*");
                        }

                        // Toggle mute/unmute
                        muteToggle.addEventListener("click", function() {
                            if (muteToggle.textContent === "Unmute") {
                                sendMessage("unMute");
                                muteToggle.textContent = "Mute";
                            } else {
                                sendMessage("mute");
                                muteToggle.textContent = "Unmute";
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

    // Function to toggle review form
    function toggleReviewForm() {
        const reviewForm = document.getElementById('add-review');
        if (reviewForm.style.display === 'none' || reviewForm.style.display === '') {
            reviewForm.style.display = 'block';
        } else {
            reviewForm.style.display = 'none';
        }
    }

    // Function to toggle review graph
    function toggleReviewGraph() {
        const reviewGraph = document.getElementById('review-graph');
        const isVisible = reviewGraph.classList.contains('show');

        if (isVisible) {
            reviewGraph.classList.remove('show');
            setTimeout(() => {
                reviewGraph.style.display = 'none';
            }, 300);
        } else {
            reviewGraph.style.display = 'block';
            setTimeout(() => {
                reviewGraph.classList.add('show');
                animateRatingBars();
            }, 10);
        }
    }

    // Review Modal Functions
    function openReviewModal(reviewText, reviewerName, reviewDate) {
        const modal = document.getElementById('reviewModal');
        const modalTitle = document.getElementById('reviewModalTitle');
        const reviewerNameEl = document.getElementById('reviewerName');
        const reviewDateEl = document.getElementById('reviewDate');
        const fullReviewTextEl = document.getElementById('fullReviewText');

        // Set content
        modalTitle.textContent = 'Full Review';
        reviewerNameEl.textContent = reviewerName;
        reviewDateEl.textContent = reviewDate || '';
        fullReviewTextEl.textContent = reviewText;

        // Show modal
        modal.style.display = 'block';

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Add keyboard support
        document.addEventListener('keydown', handleReviewKeyPress);

        // Fade in animation
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);
    }

    function closeReviewModal() {
        const modal = document.getElementById('reviewModal');

        modal.style.opacity = '0';

        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';

            // Remove keyboard listener
            document.removeEventListener('keydown', handleReviewKeyPress);
        }, 300);
    }

    // Keyboard support for review modal
    function handleReviewKeyPress(e) {
        if (e.key === 'Escape') {
            closeReviewModal();
        }
    }

    // Function to animate rating bars
    function animateRatingBars() {
        const bars = document.querySelectorAll('.rating-bar');
        bars.forEach((bar, index) => {
            const width = bar.getAttribute('data-width');
            setTimeout(() => {
                bar.style.width = width + '%';
            }, index * 100);
        });
    }
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

    if (!selectedSizeBox) {
        alert('Please select a size before proceeding.');
        return;
    }

    const size = selectedSizeBox.querySelector('div:nth-child(2)').textContent.trim();
    const productDetails = { productId, quantity, price, size };

    // Show Customer Details Modal
    $('#customerDetailsModal').modal('show');

    const submitCustomerDetailsBtn = document.getElementById('submitCustomerDetails');
    if (submitCustomerDetailsBtn) {
        submitCustomerDetailsBtn.addEventListener('click', function () {
        const customerName = document.getElementById('customerName').value.trim();
        const customerEmail = document.getElementById('customerEmail').value.trim();
        const customerPhone = document.getElementById('customerPhone').value.trim();

        if (!customerName || !customerEmail || !customerPhone) {
            alert('Please fill out all customer details.');
            return;
        }

        $('#customerDetailsModal').modal('hide');
        $('#shippingAddressModal').modal('show');

        const submitShippingAddressBtn = document.getElementById('submitShippingAddress');
        if (submitShippingAddressBtn) {
            submitShippingAddressBtn.addEventListener('click', function () {
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
            }); // Close submitShippingAddress event listener
        } // Close submitShippingAddress element check
    }); // Close submitCustomerDetails event listener
    } // Close submitCustomerDetails element check
}

// Attach event listener to Buy Now buttons
// document.querySelectorAll('.btn-style1').forEach(button => {
//     if (button.textContent.trim() === 'Buy Now') {
//         button.addEventListener('click', sendProductDetails);
//     }
// });



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
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const accordionItems = document.querySelectorAll('.faq-accordion__item');

        accordionItems.forEach(item => {
            const title = item.querySelector('.faq-accordion__title');
            const content = item.querySelector('.faq-accordion__content');

            title.addEventListener('click', () => toggleAccordion(item));

            // Keyboard navigation
            title.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleAccordion(item);
                }
            });
        });

        function toggleAccordion(item) {
            const isExpanded = item.classList.contains('is-expanded');
            const content = item.querySelector('.faq-accordion__content');

            // Close all items
            accordionItems.forEach(acc => {
                acc.classList.remove('is-expanded');
                acc.querySelector('.faq-accordion__content').style.maxHeight = null;
            });

            // Toggle current item
            if (!isExpanded) {
                item.classList.add('is-expanded');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        }
    });
    document.addEventListener("DOMContentLoaded", function() {
        const questions = document.querySelectorAll(".faq-accordion-queans h6");

        questions.forEach((question) => {
            question.addEventListener("click", function() {
                this.nextElementSibling.classList.toggle("active");
                if (this.nextElementSibling.classList.contains("active")) {
                    this.nextElementSibling.style.display = "block";
                } else {
                    this.nextElementSibling.style.display = "none";
                }
            });
        });
    });



    // Read More/Read Less functionality for short descriptions
    function toggleDescription(type) {
        const shortElement = document.getElementById(type + '-description-short');
        const fullElement = document.getElementById(type + '-description-full');
        const dotsElement = document.getElementById(type + '-description-dots');
        const buttonElement = document.getElementById(type + '-read-more-btn');

        if (fullElement && (fullElement.style.display === 'none' || fullElement.style.display === '')) {
            // Show full description
            shortElement.style.display = 'none';
            fullElement.style.display = 'block';
            buttonElement.textContent = 'Read Less';
            buttonElement.style.background = 'linear-gradient(135deg, #305724, #4a7c59)';
        } else if (fullElement) {
            // Show short description
            shortElement.style.display = 'block';
            fullElement.style.display = 'none';
            buttonElement.textContent = 'Read More';
            buttonElement.style.background = 'linear-gradient(135deg, #ec6504, #ff8533)';
        }
    }

    // Initialize Product Main Slider with Vanilla JavaScript
    function initializeProductSlider() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeProductSlider);
            return;
        }

        // Wait for jQuery and Owl Carousel to be available
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.owlCarousel === 'undefined') {
            setTimeout(initializeProductSlider, 100);
            return;
        }

        const $ = jQuery;

        // Check if there are multiple images to create slider
        const sliderItems = $('.product-main-slider .slider-item');

        if (sliderItems.length > 1) {
            // Initialize Owl Carousel for main product images
            const mainSlider = $('.product-main-slider').owlCarousel({
                items: 1,
                loop: true,
                margin: 0,
                nav: true,
                navText: [
                    '<i class="fa fa-chevron-left"></i>',
                    '<i class="fa fa-chevron-right"></i>'
                ],
                dots: true,
                autoplay: false,
                mouseDrag: true,
                touchDrag: true,
                animateOut: 'fadeOut',
                animateIn: 'fadeIn',
                smartSpeed: 600,
                responsive: {
                    0: {
                        nav: true,
                        dots: true
                    },
                    768: {
                        nav: true,
                        dots: true
                    },
                    1024: {
                        nav: true,
                        dots: true
                    }
                }
            });

            // Connect thumbnail navigation to main slider using vanilla JavaScript
            const thumbnails = document.querySelectorAll('.pro-page-slider .nav-link');
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all thumbnails
                    thumbnails.forEach(thumb => thumb.classList.remove('active'));
                    // Add active class to clicked thumbnail
                    this.classList.add('active');

                    // Navigate to corresponding slide in main slider
                    mainSlider.trigger('to.owl.carousel', [index, 600]);
                });
            });

            // Update thumbnail active state when main slider changes
            mainSlider.on('changed.owl.carousel', function(event) {
                const currentIndex = event.item.index;

                // Remove active class from all thumbnails
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                // Add active class to corresponding thumbnail
                if (thumbnails[currentIndex]) {
                    thumbnails[currentIndex].classList.add('active');
                }
            });

        } else {
            // If only one image, hide navigation
            const mainSlider = document.querySelector('.product-main-slider');
            if (mainSlider) {
                const nav = mainSlider.querySelector('.owl-nav');
                const dots = mainSlider.querySelector('.owl-dots');
                if (nav) nav.style.display = 'none';
                if (dots) dots.style.display = 'none';
            }
        }
    }

    // Start the initialization
    initializeProductSlider();

    // Read More functionality for long product description
    function toggleLongDescription() {
        const preview = document.getElementById('description-preview');
        const full = document.getElementById('description-full');
        const button = document.getElementById('read-more-btn');

        if (full.style.display === 'none') {
            // Show full description
            preview.style.display = 'none';
            full.style.display = 'inline';
            button.textContent = 'Read Less';
        } else {
            // Show preview
            preview.style.display = 'inline';
            full.style.display = 'none';
            button.textContent = 'Read More';
        }
    }
    </script>



    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
   </body>
   </html>
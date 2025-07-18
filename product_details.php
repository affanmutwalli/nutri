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
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
         .specification-tab-2content {
    padding: 15px;
 
    transition: all 0.3s ease-in-out;
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
            
            
         
            
            

      </style>
      <style>
      
      

    /* Product navigation menu removed - all sections now display by default */

    /* Review Section Visibility Fix */
    #section6 {
        display: block !important;
        visibility: visible !important;
    }

    .review-header-wrapper {
        display: flex !important;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
    }

    .slider-container {
        display: block !important;
        margin-top: 20px;
    }

    .product-tab-section {
        padding: 30px 20px;
        margin-bottom: 30px;
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
    gap: 24px;
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.ingredients {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 24px;
    background: white;
    border-radius: 12px;
    transition: transform 0.3s ease;
    /* No shadow on desktop */
    box-shadow: none;
}

.ingredients:hover {
    transform: translateY(-5px);
}

/* Image Wrapper */
.image-container {
    width: 176px;
    height: 176px;
    border: 4px solid #EA652D;
    border-radius: 50%;
    overflow: hidden;
    transition: transform 0.3s;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
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
    transform: scale(1.05);
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
        width: 140px;
        height: 140px;
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
        width: 120px;
        height: 120px;
    }

    .ingredients-text {
        font-size: 14px;
    }
}




    /* Title Styling */
    .product-details-title {
        font-size: 32px;
        font-weight: bold;
        color: #305724;
        /*margin-bottom: 6px;*/
        transition: transform 0.3s ease-in-out;
        text-align: center;
    }

    .product-details-title span {
        color: #EA652D;
    }

    /* Product Description */
    .product-details-description {
        font-size: 16px;
        color: #444;
        line-height: 1.6;
        max-width: 900px;
        margin: auto;
        transition: opacity 0.3s ease-in-out;
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
        flex: 1 1 300px;
        max-width: 400px;
        height: auto;
    }

    .product-details-image img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease-in-out;
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
        padding: 40px 20px;
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
        justify-content: flex-start;
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
    gap: 24px;
    padding: 40px 20px;
    background-color: #f9f9f9;
}

/* Card Styling */
.benifit-cart {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    padding: 24px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
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
    transition: transform 0.3s ease;
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
    gap: 24px;
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Card Styling */
.use-cart {
    background: white;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.use-cart:hover {
    transform: translateY(-5px);
}

.use-cart img {
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin-bottom: 16px;
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
    padding: 20px;
}

.how-to-use-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 30px;
    counter-reset: step-counter;
}

.use-cart {
    background: white;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
    counter-increment: step-counter;
}

/* Step number styling */
.use-cart::before {
    content: counter(step-counter);
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: #ff6b35;
    color: white;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
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
    transition: transform 0.3s ease;
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
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .use-cart {
        padding: 25px 15px;
    }

    .use-cart img {
        width: 60px;
        height: 60px;
    }

    .use-cart p {
        font-size: 14px;
    }

    .use-cart::before {
        width: 30px;
        height: 30px;
        font-size: 14px;
        top: -12px;
    }
}

@media (max-width: 480px) {
    .how-to-use-container {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .how-to-use-wrapper {
        padding: 15px;
    }
}

    .slider-container {
        width: 100%;
        max-width: 1200px;
        margin: 40px auto 0;
        padding: 0 15px;
        overflow: hidden;
    }

    .inner {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
        transition: transform 0.6s ease-in-out;
        align-items: stretch;
    }

    .review-section {
        flex: 0 0 32%;
        max-width: 32%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 100%;
        /* Important for equal height rows */
        background-color: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(85, 110, 77, 0.3);
        border-radius: 20px;
        backdrop-filter: blur(12px);
        padding: 30px 20px;
        font-family: 'Segoe UI', sans-serif;
        transition: all 0.3s ease-in-out;
        margin-bottom: 20px;
        box-sizing: border-box;


    }

    .review-section img {
        width: 100%;
        height: auto;
        /* Keeps image responsive */
        object-fit: cover;
        margin-bottom: 15px;
        border-radius: 12px;
    }

    .review-section h6 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
        text-align: center;

    }

    .rating-group {
        display: flex;
        justify-content: center;
        gap: 4px;
        margin-bottom: 10px;
    }

    .rating-group i {
        color: #EA652D;
    }

    .review-section .desc {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 10px;
    }

    .review-section p {
        font-size: 1rem;
        color: #444;
        margin-bottom: 6px;
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

    /* === TABLET RESPONSIVE (2 per row) === */
    @media (max-width: 992px) {
        .review-section {
            flex: 0 0 48%;
            max-width: 48%;
        }
    }

    /* === MOBILE CAROUSEL === */
    @media (max-width: 600px) {
        .slider-container {
            overflow-x: hidden;
            position: relative;
        }

        .inner {
            flex-wrap: nowrap;
            gap: 0;
            transform: translateX(0%);
        }

        .review-section {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 25px;
            scroll-snap-align: start;
        }

        .review-section h6 {
            font-size: 1rem;
        }

        .review-section p {
            font-size: 0.9rem;
        }

        .map {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            gap: 10px;
        }

        .map button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: none;
            background: #ccc;
            cursor: pointer;
        }

        .map button.active {
            background-color: #305724;
        }
    }


    .review-header-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-top: 40px;
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
        font-family: 'Segoe UI', sans-serif;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));

    }

    .review-left-section {
        flex: 1;
        min-width: 300px;
    }

    .review-star-rating {
        display: inline-block;
        color: #EA652D;
        font-size: 18px;
    }

    .review-count {
        font-weight: 500;
        margin-left: 10px;
        vertical-align: middle;
        font-size: 16px;
    }

    .review-summary-btn {
        background-color: #EA652D;
        border: none;
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 14px;
        margin-top: 10px;
        cursor: pointer;
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
        gap: 10px;
        align-items: center;
    }

    .write-review-btn {
        background-color: #EA652D;
        border: none;
        color: white;
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
    }

    .review-dropdown select {
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background-color: white;
        cursor: pointer;
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
        min-height: 200px;
        padding: 20px 0;
        margin-bottom: 30px;
    }

    /* Make sure sections are visible when selected */
    .product-tab-section {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Ensure proper spacing between sections */
    .section-b-padding {
        padding: 40px 0;
    }

    /* Read More Button Styling */
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
      <!-- product info start -->
      <section class="pro-page desktop-margin">
         <div class="container">
            <div class="row">
                  <!-- Product Images Section -->
                  <div class="col-xl-20 col-lg-16 col-md-12 col-xs-12 pro-image">
                     <div class="row">
                        <!-- Main Image -->
                        <div class="col-lg-6 col-xl-6 col-md-6 col-12 larg-image">
                              <div class="tab-content">
                                 <div class="tab-pane show active" id="image-11">
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
                                 <?php if (!empty($model_image) && is_array($model_image)) {
                                    foreach ($model_image as $index => $model_images) {
                                       // Add null check for PhotoPath
                                       $photoPath = isset($model_images["PhotoPath"]) && !empty($model_images["PhotoPath"])
                                                   ? htmlspecialchars($model_images["PhotoPath"])
                                                   : 'default.jpg';
                                       ?>
                                          <div class="tab-pane" id="image-<?php echo $index + 1; ?>">
                                             <a href="javascript:void(0)" class="long-img" style="border: 1px solid #ccc; border-radius: 5px; margin-top: 15px;">
                                                <figure class="zoom" onmousemove="zoom(event)" style="background-image: url('cms/images/products/<?php echo $photoPath; ?>')">
                                                      <img src="cms/images/products/<?php echo $photoPath; ?>" class="img-fluid" alt="image">
                                                </figure>
                                             </a>
                                          </div>
                                 <?php } } ?>
                              </div>
                              <ul class="nav nav-tabs pro-page-slider owl-carousel owl-theme">
                                 <li class="nav-item items">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#image-11">
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
                                             <a class="nav-link" data-bs-toggle="tab" href="#image-<?php echo $index + 1; ?>">
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
      <section class="" style="margin-top: 50px;">
        <div class="container">
            <?php
            $filteredProductTitle = str_replace("My Nutrify Herbal & Ayurveda’s", "", $product_data[0]["Title"]);
            ?>
            <div class="product-details-section" id="section1">
                <h1 class="product-details-title">
                    My Nutrify Herbal & Ayurveda’s
                    <span><?php echo isset($filteredProductTitle) && trim($filteredProductTitle) !== '' ? nl2br(htmlspecialchars(trim($filteredProductTitle))) : 'Product'; ?></span>
                </h1>
                <div class="product-description-container">
                    <?php
                    $fullDescription = $product_data[0]["Description"];
                    $shortDescription = mb_substr($fullDescription, 0, 100, 'UTF-8');
                    $hasMoreContent = mb_strlen($fullDescription, 'UTF-8') > 100;
                    ?>

                    <p class="ingredients-details-description" id="main-description-short">
                        <?php echo nl2br(htmlspecialchars($shortDescription)); ?>
                        <?php if ($hasMoreContent): ?>
                            <span id="main-description-dots">...</span>
                        <?php endif; ?>
                    </p>

                    <?php if ($hasMoreContent): ?>
                        <p class="ingredients-details-description" id="main-description-full" style="display: none;">
                            <?php echo nl2br(htmlspecialchars($fullDescription)); ?>
                        </p>

                        <button class="read-more-btn" id="main-read-more-btn" onclick="toggleDescription('main')">
                            Read More
                        </button>
                    <?php endif; ?>
                </div>
            </div>



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
                        <div class="product-description-container">
                            <?php
                            $detailsDescription = !empty($product_details['Description']) ? $product_details['Description'] : '';
                            if (!empty($detailsDescription)) {
                            ?>
                                <p class="ingredients-details-description">
                                    <?php echo nl2br(htmlspecialchars($detailsDescription)); ?>
                                </p>
                            <?php } else { ?>
                                <p class="ingredients-details-description">No description available.</p>
                            <?php } ?>
                        </div>
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
            <section class="section-b-padding pro-releted" id="section3" style="margin-top: 20px;">
                 <h1 class="product-details-title">
                  Why Drink My Nutrify Herbal & Ayurveda’s
                    <span><?php echo nl2br(htmlspecialchars(trim($filteredProductTitle))); ?></span>
                </h1>
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
                  Direction To Use My Nutrify Herbal & Ayurveda’s
<span>
  <?php echo isset($filteredProductTitle) && trim($filteredProductTitle) !== ''
    ? nl2br(htmlspecialchars(trim($filteredProductTitle)))
    : 'Product'; ?>
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
                            <p>Take on an empty stomach in the’ orig and 30 mins after dinner.</p>
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
                            <img src="cms/images/products/Halal.png" alt="">
                            <p>Halal Certified</p>
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

            <section class="section-b-padding pro-releted" id="section6">
                <h1 class="product-details-title">
                    Customer <span>Reviews</span>
                </h1>
                <div class="review-header-wrapper">
                    <div class="review-left-section">
                        <div class="star-rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>

                            <!-- <i class="fa fa-star-half-o"></i> -->
                        </div>
                        <span class="review-count">
                            <?php
                            $reviewCount = !empty($review_data) ? count($review_data) : 0;
                            echo $reviewCount . " Review" . ($reviewCount != 1 ? "s" : "");
                            ?>
                        </span>

                        <button class="review-summary-btn">✨ Show reviews summary</button>

                        <!-- <div class="review-tag-pills">
                            <span>she care juice</span>
                            <span>MyNutrify ayurveda</span>
                            <span>women's health</span>
                            <span>hormonal balance</span>
                            <span>periods</span>
                            <span>pcos</span>
                            <span>irregular periods</span>
                            <span>natural remedy</span>
                        </div> -->
                    </div>

                    <div class="review-right-section">
                        <button class="write-review-btn" onclick="toggleReviewForm()">Write A Review</button>
                        <div class="review-form collapse" id="add-review" style="display: none;">
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
                                <input type="text" name="review_title" placeholder="Review title">
                                <label>Add comments</label>
                                <textarea name="comment" placeholder="Write your comments"></textarea>
                                <button type="submit">Submit Review</button>
                            </form>
                        </div>

                        <div class="review-dropdown">
                            <select>
                                <option>Image First</option>
                                <option>Latest First</option>
                                <option>Top Reviews</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if (!empty($review_data)): ?>
                <div class="slider-container">
                    <div class="inner">
                        <?php foreach ($review_data as $review): ?>
                        <section class="review-section">
                            <!-- HTML -->

                            <div class="review-profile">
                                <img src="cms/images/ingredient/<?php echo htmlspecialchars($review['PhotoPath']); ?>"
                                    alt="<?php echo htmlspecialchars($review['Name']); ?>">
                            </div>
                            <h6><?php echo htmlspecialchars($review['Name']); ?></h6>
                            <div class="rating-group">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa fa-star rating__icon" aria-hidden="true"></i>
                                <?php endfor; ?>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($review['Review'])); ?></p>
                            <p><small><?php echo date("F j, Y", strtotime($review['Date'])); ?></small></p>
                        </section>
                        <?php endforeach; ?>
                    </div>

                    <!-- Buttons -->
                    <div class="map">
                        <button class="first active"></button>
                        <button class="second"></button>
                        <button class="third"></button>
                    </div>
                </div>
                <?php else: ?>
                <div style="text-align: center; color: #666; font-size: 16px; padding: 20px;">
                    <p>No customer reviews available for this product yet.</p>
                    <p>Be the first to review this product!</p>
                </div>
                <?php endif; ?>
            </section>


        </div>
        </div>
    </section>

    <script>
    // All sections are now displayed by default - no JavaScript needed for section switching
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

    // Read More/Read Less functionality for product descriptions
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
    </script>



    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
   </body>
   </html>
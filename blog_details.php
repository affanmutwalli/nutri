<!DOCTYPE html>
<html lang="en">
<?php 
    session_start();
    include('includes/urls.php');
    include('database/dbconnection.php');
    $obj = new main();
    $obj->connection();
     if (isset($_GET["BlogId"])) {
    $FieldNames = array("BlogId", "BlogTitle", "BlogDate", "Description", "PhotoPath", "IsActive");
    $ParamArray = array($_GET["BlogId"]);
    $Fields = implode(",", $FieldNames);
    $blog_data = $obj->MysqliSelect1(
        "SELECT " . $Fields . " FROM blogs_master WHERE BlogId = ? AND IsActive = 'Y'",
        $FieldNames,
        "i",
        $ParamArray
    );
    
if (!empty($blog_data)) {
        $description = $blog_data[0]["Description"] ?? '';
        $plain_description = strip_tags($description);
        $short_description = substr($plain_description, 0, 200);
        if (strlen($plain_description) > 200) {
            $short_description .= "...";
        }
     }
    ?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Dynamic Meta Tags -->
    <title><?php echo $blog_data[0]["BlogTitle"]; ?></title>
    <meta name="description" content="<?php echo htmlentities($short_description);  ?>" />
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
    <style>
    .blog-description {
        margin: 10px auto;
        /* Add spacing and center the content */
        padding: 20px;
        /* Add inner spacing */
        max-width: 1200px;
        /* Fixed maximum width for large screens */
        width: 100%;
        /* Ensure it scales for smaller screens */
        box-sizing: border-box;
        /* Include padding in width */
        overflow-x: hidden;
        /* Prevent horizontal scrolling */
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
        /* Fades out after 3 seconds */
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
            <img class="loader-img" src="image/preloader.gif" />
        </div>
    </div>

    <!-- header start -->
    <?php include("components/header.php"); ?>
    <!-- header end -->

    <!-- blog start -->
    <?php if (isset($_GET["BlogId"])) { ?>
    <section class="section-tb-padding blog-page">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="blog-style-1-details">
                        <div class="text-center">
                            <img src="cms/images/blogs/<?php echo htmlspecialchars($blog_data[0]["PhotoPath"] ?? 'default.jpg'); ?>"
                                alt="Centered Image" class="img-fluid">
                        </div>
                        <div class="single-blog-content">
                            <div class="single-b-title">
                                <h2><?php echo htmlspecialchars($blog_data[0]["BlogTitle"] ?? 'Untitled Blog'); ?></h2>
                            </div>
                            <div class="date-edit-comments">
                                <div class="blog-info-wrap">
                                    <span class="blog-data date">
                                        <i class="icon-clock"></i>
                                        <span class="blog-d-n-c">
                                            <?php 
                                                echo date("j M Y", strtotime($blog_data[0]["BlogDate"] ?? 'now')); 
                                                ?>
                                        </span>
                                    </span>
                                    <span class="blog-data blog-edit">
                                        <i class="icon-user"></i>
                                        <span class="blog-d-n-c">By <span class="editor">My Nutrify</span></span>
                                    </span>
                                    <span class="blog-data comments">
                                        <i class="icon-bubble"></i>
                                        <span class="blog-d-n-c">4 <span class="add-comments">comments</span></span>
                                    </span>
                                </div>
                            </div>
                            <div class="blog-description">
                                <p class="blog-description">
                                    <?php
                                        // Allow safe HTML tags including formatting (span, style attributes)
                                        $allowed_tags = '<p><a><strong><em><ul><ol><li><br><img><span><div><h1><h2><h3><h4><h5><h6><blockquote><code><pre>';
                                        $content = strip_tags($blog_data[0]["Description"] ?? '', $allowed_tags);

                                        // Additional security: remove potentially dangerous attributes but keep style
                                        $content = preg_replace('/\s(on\w+)="[^"]*"/i', '', $content); // Remove onclick, onload, etc.
                                        $content = preg_replace('/javascript:/i', '', $content); // Remove javascript: URLs

                                        echo $content;
                                        ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-5 col-12">
                    <div class="left-column style-1">
                        <div class="blog-search">
                            <h4>Search</h4>
                            <form action="blog.php" method="get">
                                <input type="text" name="search" placeholder="Search blog">
                            </form>
                        </div>

                        <div class="blog-title">
                            <h4>Recent Posts</h4>
                        </div>
                        <?php
                            $recent_blogs = [];
                            $FieldNames = array("BlogId", "BlogTitle", "PhotoPath", "BlogDate");
                            $Fields = implode(",", $FieldNames);
                            $ParamArray = [];
                            $recent_blogs = $obj->MysqliSelect1(
                                "SELECT $Fields FROM blogs_master ORDER BY BlogDate DESC LIMIT 3",
                                $FieldNames, 
                                "", 
                                $ParamArray
                            );
                        ?>

                        <div class="left-blog">
                            <?php foreach ($recent_blogs as $recent): ?>
                            <div class="blog-item mb-3">
                                <div class="l-blog-image mb-2">
                                    <a href="blog_details.php?BlogId=<?= $recent['BlogId'] ?>">
                                        <img src="cms/images/blogs/<?= htmlspecialchars($recent["PhotoPath"]) ?>"
                                            alt="<?= htmlspecialchars($recent["BlogTitle"]) ?>"
                                            style="width: 100%; height: auto; object-fit: cover;">
                                    </a>
                                </div>

                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php 
                            $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                            $ParamArray = array();
                            $Fields = implode(",", $FieldNames);

                            // Fetching two random products
                            $product_data = $obj->MysqliSelect1(
                                "SELECT " . $Fields . " FROM product_master ORDER BY RAND() LIMIT 2", 
                                $FieldNames, 
                                "", 
                                $ParamArray
                            );

                            if (!empty($product_data)) {
                                foreach ($product_data as $products) {
                                    $FieldNamesPrice = array("OfferPrice", "MRP");
                                    $ParamArrayPrice = array($products["ProductId"]);
                                    $FieldsPrice = implode(",", $FieldNamesPrice);

                                    // Fetching product prices
                                    $product_prices = $obj->MysqliSelect1(
                                        "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                        $FieldNamesPrice, 
                                        "i", 
                                        $ParamArrayPrice
                                    );

                                    $lowest_price = "N/A";
                                    $mrp = "N/A";
                                    $savings = 0;

                                    if (!empty($product_prices)) {
                                        foreach ($product_prices as $product_price) {
                                            $current_offer_price = floatval($product_price["OfferPrice"]);
                                            $current_mrp = floatval($product_price["MRP"]);

                                            if ($current_offer_price > 0 && ($lowest_price === "N/A" || $current_offer_price < $lowest_price)) {
                                                $lowest_price = $current_offer_price;
                                            }
                                            if ($current_mrp > 0 && ($mrp === "N/A" || $current_mrp < $mrp)) {
                                                $mrp = $current_mrp;
                                            }
                                        }

                                        if ($mrp !== "N/A" && $lowest_price !== "N/A" && $mrp > $lowest_price) {
                                            $savings = $mrp - $lowest_price;
                                        }
                                    }
                            ?>
                        <div class="items"
                            style="border: 1px solid #ccc; padding: 5px; border-radius: 5px; margin-top:15px;">
                            <div class="tred-pro">
                                <div class="tr-pro-img">
                                    <a href="product_details.php?ProductId=<?php echo $products["ProductId"]; ?>">
                                        <img class="img-fluid"
                                            src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>"
                                            alt="<?php echo htmlspecialchars($products["ProductName"]); ?>">
                                        <img class="img-fluid additional-image"
                                            src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>"
                                            alt="<?php echo htmlspecialchars($products["ProductName"]); ?>">
                                    </a>
                                </div>
                                <?php if ($savings > 0): ?>
                                <div class="Pro-lable">
                                    <span class="p-text">Off ₹<?php echo htmlspecialchars($savings); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="caption">
                                <h3><a
                                        href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>"><?php echo htmlspecialchars($products["ProductName"]); ?></a>
                                </h3>
                                <div class="rating">
                                    <i class="fa fa-star c-star"></i>
                                    <i class="fa fa-star c-star"></i>
                                    <i class="fa fa-star c-star"></i>
                                    <i class="fa fa-star-o"></i>
                                    <i class="fa fa-star-o"></i>
                                </div>
                                <div class="pro-price">
                                    <span class="new-price">Starting from
                                        ₹<?php echo htmlspecialchars($lowest_price); ?></span>
                                    <?php if ($mrp !== "N/A"): ?>
                                    <span class="old-price"
                                        style="text-decoration: line-through; color: #999;">₹<?php echo htmlspecialchars($mrp); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pro-btn text-center" style="margin:5px;">
                                            <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session"
                                                data-product-id="<?php echo $products["ProductId"]; ?>">
                                                <i class="fa fa-shopping-bag" style="margin-right:8px;"></i>Add to
                                                Cart
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                        }
                    } else {
                        echo "<p>No trending products available.</p>";
                    }
                    ?>


                    </div>
                </div>
            </div>

        </div>
    </section>
    <?php
            // Get the current page number from the query string, default to 1 if not set
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $blogs_per_page = 3; // Set how many blogs per page
            $offset = ($current_page - 1) * $blogs_per_page; // Calculate the offset for the query

            // Get the total number of active blogs
            $total_blogs = $obj->fSelectRowCountNew("SELECT * FROM blogs_master WHERE IsActive = 'Y';");


            // Get the active blogs for the current page
            $FieldNames = array("BlogId", "BlogTitle", "BlogDate", "Description", "PhotoPath", "IsActive","SubCategoryId");
            $ParamArray = array();
            $Fields = implode(",", $FieldNames);
            $blog_data = $obj->MysqliSelect1(
                "SELECT " . $Fields . " FROM blogs_master WHERE IsActive = 'Y' ORDER BY BlogDate DESC LIMIT $offset, $blogs_per_page",
                $FieldNames,
                "",
                $ParamArray
            );

            // Calculate the total number of pages
            $total_pages = ceil($total_blogs / $blogs_per_page);
            ?>
    <section class="section-tb-padding blog-page">
        <h4 style="text-align:center; margin-bottom:20px;">Related Blogs / Articles</h4>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="blog-style-1-full-grid">
                        <?php
                                // Display the blogs
                                foreach ($blog_data as $blogs) {
                                    // Limit the description to 200 characters
                                    $description = $blogs["Description"];
                                    $short_description = substr($description, 0, 200);
                                    if (strlen($description) > 200) {
                                        $short_description .= "...";
                                    }
                                ?>
                        <div class="blog-start">
                            <div class="blog-post">
                                <div class="blog-image">
                                    <a
                                        href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>">
                                        <img src="cms/images/blogs/<?php echo htmlspecialchars($blogs["PhotoPath"] ?? 'default.jpg'); ?>"
                                            alt="<?php echo htmlspecialchars($blogs["BlogTitle"] ?? 'No Title'); ?>"
                                            class="img-fluid">
                                    </a>
                                </div>
                                <div class="blog-content">
                                    <div class="blog-title">
                                        <h6>
                                            <a
                                                href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>">
                                                <?php echo htmlspecialchars($blogs["BlogTitle"] ?? 'Untitled Blog'); ?>
                                            </a>
                                        </h6>
                                        <span class="blog-admin">By <span class="blog-editor">My Nutrify Herbal &
                                                Ayurveda.</span></span>
                                    </div>

                                    <a href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>"
                                        class="read-link">
                                        <span>Read more</span>
                                        <i class="ti-arrow-right"></i>
                                    </a>
                                    <div class="blog-date-comment">
                                        <span class="blog-date">
                                            <?php 
                                                            $blogDate = isset($blogs["BlogDate"]) ? new DateTime($blogs["BlogDate"]) : null;
                                                            echo $blogDate ? $blogDate->format('j M Y') : 'Unknown Date';
                                                            ?>
                                        </span>
                                        <a href="javascript:void(0)"><?php echo htmlspecialchars($blogs["CommentCount"] ?? '0'); ?>
                                            Comments</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                                }
                                ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php }
            else {
                echo '
                    <section class="section-tb-padding blog-page">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <p>Oops, Invalid Access....</p>
                                </div>
                            </div>
                        </div>
                    </section>';
            }
 ?>
    <!-- blog end -->

    <?php include("components/footer.php"); ?>
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
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
</body>

</html>
<?PHP } ?>
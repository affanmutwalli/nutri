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
    /* Full Screen Responsive Blog Details */
    .blog-page {
        padding: 40px 0;
        min-height: 100vh;
    }

    /* Main Blog Container - Full Width Optimization */
    .blog-style-1-details {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 40px;
    }

    /* Blog Image - Full Width Responsive */
    .blog-style-1-details .text-center {
        padding: 0;
        margin-bottom: 0;
    }

    .blog-style-1-details .text-center img {
        width: 100%;
        height: clamp(300px, 40vh, 500px);
        object-fit: cover;
        border-radius: 0;
        transition: transform 0.3s ease;
    }

    .blog-style-1-details .text-center img:hover {
        transform: scale(1.02);
    }

    /* Blog Content Container */
    .single-blog-content {
        padding: clamp(20px, 4vw, 60px);
    }

    /* Blog Title - Responsive Typography */
    .single-b-title h2 {
        font-size: clamp(24px, 4vw, 42px);
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        line-height: 1.3;
        text-align: center;
    }

    /* Blog Meta Information */
    .date-edit-comments {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f8f9fa;
    }

    .blog-info-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: clamp(15px, 3vw, 30px);
        align-items: center;
    }

    .blog-data {
        display: flex;
        align-items: center;
        font-size: clamp(14px, 1.5vw, 16px);
        color: #6c757d;
        font-weight: 500;
    }

    .blog-data i {
        margin-right: 8px;
        color: #ea652d;
        font-size: clamp(16px, 1.8vw, 18px);
    }

    .editor {
        color: #ea652d;
        font-weight: 600;
    }

    /* Blog Description - Full Width Content */
    .blog-description {
        margin: 0 auto;
        padding: 0;
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
        line-height: 1.8;
    }

    .blog-description p {
        font-size: clamp(16px, 2vw, 18px);
        color: #495057;
        margin-bottom: 20px;
        text-align: justify;
    }

    /* Sidebar - Responsive Design */
    .left-column {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        padding: clamp(20px, 3vw, 30px);
        margin-top: 0;
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    /* Search Section */
    .blog-search h4,
    .blog-title h4 {
        font-size: clamp(18px, 2.5vw, 22px);
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ea652d;
    }

    .blog-search input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: clamp(14px, 1.5vw, 16px);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .blog-search input:focus {
        outline: none;
        border-color: #ea652d;
        box-shadow: 0 0 0 3px rgba(234, 101, 45, 0.1);
    }

    /* Recent Posts */
    .blog-item {
        border-bottom: 1px solid #f8f9fa;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .blog-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .l-blog-image img {
        width: 100%;
        height: clamp(80px, 12vw, 120px);
        object-fit: cover;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    .l-blog-image img:hover {
        transform: scale(1.05);
    }

    /* Product Cards in Sidebar */
    .items {
        border: 2px solid #f8f9fa;
        padding: 15px;
        border-radius: 12px;
        margin-top: 20px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .items:hover {
        border-color: #ea652d;
        box-shadow: 0 8px 25px rgba(234, 101, 45, 0.15);
        transform: translateY(-2px);
    }

    .tr-pro-img img {
        width: 100%;
        height: clamp(120px, 15vw, 180px);
        object-fit: cover;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    .tr-pro-img img:hover {
        transform: scale(1.05);
    }

    .caption h3 {
        font-size: clamp(14px, 1.8vw, 16px);
        font-weight: 600;
        margin: 12px 0 8px 0;
        line-height: 1.4;
    }

    .caption h3 a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .caption h3 a:hover {
        color: #ea652d;
    }

    .pro-price {
        margin: 10px 0;
    }

    .new-price {
        font-size: clamp(14px, 1.6vw, 16px);
        font-weight: 700;
        color: #ea652d;
    }

    .old-price {
        font-size: clamp(12px, 1.4vw, 14px);
        margin-left: 8px;
    }

    .pro-btn .btn {
        width: 100%;
        padding: 10px 15px;
        font-size: clamp(13px, 1.4vw, 15px);
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    /* Related Blogs Section */
    .blog-style-1-full-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .blog-start {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .blog-start:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .blog-image img {
        width: 100%;
        height: clamp(200px, 25vh, 280px);
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .blog-image img:hover {
        transform: scale(1.05);
    }

    .blog-content {
        padding: clamp(15px, 3vw, 25px);
    }

    .blog-title h6 {
        font-size: clamp(16px, 2vw, 20px);
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .blog-title h6 a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .blog-title h6 a:hover {
        color: #ea652d;
    }

    .blog-admin {
        font-size: clamp(12px, 1.3vw, 14px);
        color: #6c757d;
        margin-bottom: 15px;
        display: block;
    }

    .blog-editor {
        color: #ea652d;
        font-weight: 600;
    }

    .read-link {
        display: inline-flex;
        align-items: center;
        color: #ea652d;
        font-weight: 600;
        font-size: clamp(13px, 1.4vw, 15px);
        text-decoration: none;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .read-link:hover {
        color: #d4541f;
        transform: translateX(5px);
    }

    .read-link i {
        margin-left: 8px;
        transition: transform 0.3s ease;
    }

    .read-link:hover i {
        transform: translateX(3px);
    }

    .blog-date-comment {
        font-size: clamp(11px, 1.2vw, 13px);
        color: #6c757d;
        padding-top: 15px;
        border-top: 1px solid #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Loading Screen */
    .loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        animation: fadeOut 1.5s ease-out 3s forwards;
    }

    .loader-img {
        width: clamp(100px, 15vw, 150px);
        height: clamp(100px, 15vw, 150px);
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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

    /* Large Desktop Screens (1920px+) */
    @media (min-width: 1920px) {
        .blog-page {
            padding: 60px 0;
        }

        .single-blog-content {
            padding: 80px;
        }

        .blog-style-1-details .text-center img {
            height: 600px;
        }

        .blog-style-1-full-grid {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
        }

        .left-column {
            padding: 40px;
        }
    }

    /* Standard Desktop (1200px - 1919px) */
    @media (min-width: 1200px) and (max-width: 1919px) {
        .blog-page {
            padding: 50px 0;
        }

        .single-blog-content {
            padding: 60px;
        }

        .blog-style-1-details .text-center img {
            height: 500px;
        }

        .blog-style-1-full-grid {
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 35px;
        }
    }

    /* Large Tablets & Small Desktops (992px - 1199px) */
    @media (min-width: 992px) and (max-width: 1199px) {
        .blog-page {
            padding: 40px 0;
        }

        .single-blog-content {
            padding: 40px;
        }

        .blog-style-1-details .text-center img {
            height: 400px;
        }

        .blog-style-1-full-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .left-column {
            margin-top: 30px;
        }
    }

    /* Tablets (768px - 991px) */
    @media (min-width: 768px) and (max-width: 991px) {
        .blog-page {
            padding: 30px 0;
        }

        .single-blog-content {
            padding: 30px;
        }

        .blog-style-1-details .text-center img {
            height: 350px;
        }

        .blog-info-wrap {
            justify-content: flex-start;
            gap: 20px;
        }

        .blog-style-1-full-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .left-column {
            margin-top: 40px;
            position: static;
        }

        .tr-pro-img img {
            height: 150px;
        }
    }

    /* Mobile Devices (up to 767px) */
    @media (max-width: 767px) {
        .blog-page {
            padding: 20px 0;
        }

        .single-blog-content {
            padding: 20px;
        }

        .blog-style-1-details .text-center img {
            height: 250px;
        }

        .single-b-title h2 {
            text-align: left;
            margin-bottom: 15px;
        }

        .blog-info-wrap {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .blog-description p {
            text-align: left;
            font-size: 16px;
        }

        .blog-style-1-full-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .left-column {
            margin-top: 30px;
            padding: 20px;
            position: static;
        }

        .blog-search input {
            padding: 10px 12px;
        }

        .tr-pro-img img {
            height: 120px;
        }

        .l-blog-image img {
            height: 80px;
        }

        .blog-image img {
            height: 200px;
        }
    }

    /* Extra Small Mobile (up to 480px) */
    @media (max-width: 480px) {
        .blog-page {
            padding: 15px 0;
        }

        .single-blog-content {
            padding: 15px;
        }

        .blog-style-1-details .text-center img {
            height: 200px;
        }

        .blog-description p {
            font-size: 15px;
            line-height: 1.6;
        }

        .left-column {
            padding: 15px;
        }

        .blog-style-1-full-grid {
            gap: 15px;
        }

        .blog-content {
            padding: 15px;
        }

        .tr-pro-img img {
            height: 100px;
        }

        .l-blog-image img {
            height: 60px;
        }

        .blog-image img {
            height: 180px;
        }
    }

    /* Full Screen PC Optimization */
    .container-fluid {
        max-width: 1920px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Enhanced Full Screen Layout */
    @media (min-width: 1400px) {
        .container-fluid {
            max-width: 1800px;
            padding: 0 40px;
        }

        .blog-page {
            padding: 60px 0;
        }

        .single-blog-content {
            padding: 60px 80px;
        }

        .blog-style-1-details .text-center img {
            height: 500px;
            max-height: 50vh;
        }

        .left-column {
            padding: 40px;
        }

        .blog-style-1-full-grid {
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 40px;
        }
    }

    /* Ultra Wide Screens (2560px+) */
    @media (min-width: 2560px) {
        .container-fluid {
            max-width: 2400px;
            padding: 0 60px;
        }

        .blog-page {
            padding: 80px 0;
        }

        .single-blog-content {
            padding: 80px 120px;
        }

        .blog-style-1-details .text-center img {
            height: 600px;
            max-height: 45vh;
        }

        .blog-style-1-full-grid {
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 50px;
        }

        .left-column {
            padding: 50px;
        }
    }

    /* Full Width Content Optimization */
    @media (min-width: 1200px) {
        .blog-description {
            max-width: 100%;
            font-size: 18px;
            line-height: 1.8;
        }

        .blog-description p {
            margin-bottom: 25px;
        }

        .single-b-title h2 {
            font-size: 48px;
            margin-bottom: 30px;
        }

        .blog-info-wrap {
            gap: 40px;
            margin-bottom: 40px;
        }

        .blog-data {
            font-size: 16px;
        }

        .blog-data i {
            font-size: 18px;
        }
    }

    /* Enhanced Spacing for Full Screen */
    @media (min-width: 1600px) {
        .blog-page {
            padding: 70px 0;
        }

        .container-fluid {
            padding: 0 50px;
        }

        .single-blog-content {
            padding: 70px 100px;
        }

        .blog-style-1-details {
            margin-bottom: 60px;
        }

        .left-column {
            padding: 45px;
        }

        .blog-style-1-full-grid {
            margin-top: 50px;
            gap: 45px;
        }

        .blog-start {
            border-radius: 15px;
        }

        .blog-content {
            padding: 30px;
        }
    }

    /* Improved Typography for Large Screens */
    @media (min-width: 1800px) {
        .single-b-title h2 {
            font-size: 52px;
            line-height: 1.2;
        }

        .blog-description p {
            font-size: 20px;
            line-height: 1.9;
            margin-bottom: 30px;
        }

        .blog-data {
            font-size: 17px;
        }

        .blog-search h4,
        .blog-title h4 {
            font-size: 24px;
        }

        .blog-title h6 {
            font-size: 22px;
        }

        .blog-admin {
            font-size: 15px;
        }

        .read-link {
            font-size: 16px;
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-9 col-lg-8 col-md-7 col-12">
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
        <div class="container-fluid">
            <h4 style="text-align:center; margin-bottom:40px; font-size: clamp(24px, 3vw, 36px); font-weight: 700; color: #2c3e50;">Related Blogs / Articles</h4>
            <div class="row">
                <div class="col">
                    <div class="related-blogs-grid">
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
                        <div class="related-blog-card">
                            <div class="blog-card-image">
                                <a href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>">
                                    <img src="cms/images/blogs/<?php echo htmlspecialchars($blogs["PhotoPath"] ?? 'default.jpg'); ?>"
                                        alt="<?php echo htmlspecialchars($blogs["BlogTitle"] ?? 'No Title'); ?>"
                                        class="img-fluid">
                                </a>
                            </div>
                            <div class="blog-card-content">
                                <h5 class="blog-card-title">
                                    <a href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>">
                                        <?php echo htmlspecialchars($blogs["BlogTitle"] ?? 'Untitled Blog'); ?>
                                    </a>
                                </h5>
                                <p class="blog-card-author">By <span>My Nutrify Herbal & Ayurveda.</span></p>
                                <a href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>"
                                    class="blog-read-more">
                                    Read more →
                                </a>
                                <div class="blog-card-meta">
                                    <span class="blog-card-date">
                                        <?php
                                                        $blogDate = isset($blogs["BlogDate"]) ? new DateTime($blogs["BlogDate"]) : null;
                                                        echo $blogDate ? $blogDate->format('j M Y') : 'Unknown Date';
                                                        ?>
                                    </span>
                                    <span class="blog-card-comments"><?php echo htmlspecialchars($blogs["CommentCount"] ?? '0'); ?> Comments</span>
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
<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

?>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- title -->
        <title>Blogs - My Nutrify</title>
        <meta name="description" content="Explore insightful articles, tips, and guides on healthy living, nutrition, and organic wellness from My Nutrify's blog."/>
        <meta name="keywords" content="blog, organic lifestyle, healthy living, wellness, nutrition tips, healthy recipes, sustainable living, natural health"/>
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
        
    </head>
    
<style>
.blog-carousel .carousel-inner {
    overflow: hidden;
    border-radius: 20px;
}

.blog-carousel .carousel-item {
    min-height: 400px;
}

.blog-carousel .blog-img {
    height: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 0;
}

.carousel-caption-custom {
    position: relative;
    color: #305724;
}

.blog-title {
    font-weight: 700;
    color: #305724;
}

.blog-desc {
    font-size: 1rem;
    color: #444;
}

.read-more-btn {
    background-color: #EA652D;
    color: #fff;
    border: none;
    transition: 0.3s ease-in-out;
    margin-top:20px
}

.read-more-btn:hover {
    background-color: #d2561d;
    color: #fff;
}

.carousel-indicators [data-bs-target] {
    background-color: #EA652D;
    width: 10px;
    height: 10px;
    border-radius: 100%;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-size: 100% 100%;
    filter: invert(1);
}

@media (max-width: 768px) {
    .carousel-caption-custom {
        text-align: center;
    }

    .blog-carousel .row {
        flex-direction: column;
    }

    .blog-carousel .blog-img {
        max-height: 300px;
    }
}
</style>
    <body class="home-1">
       
        <!-- header start -->
        <?php include("components/header.php") ?>
        <?php
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $blogs_per_page = 6; // Set how many blogs per page
            $offset = ($current_page - 1) * $blogs_per_page; // Calculate the offset for the query

            $total_blogs = $obj->fSelectRowCountNew("SELECT * FROM blogs_master WHERE IsActive = 'Y';");


            $FieldNames = array("BlogId", "BlogTitle", "BlogDate", "Description", "PhotoPath", "IsActive");
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
        <div class="container">

        <?php
// Fetch latest 6 active blogs for the carousel
$latest_blogs = $obj->MysqliSelect1(
    "SELECT " . $Fields . " FROM blogs_master WHERE IsActive = 'Y' ORDER BY BlogDate DESC LIMIT 6",
    $FieldNames,
    "",
    $ParamArray
);
?>

<section class="section-tb-padding blog-carousel">
    <div class="container">
        <div id="blogCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <!-- Indicators -->
            <div class="carousel-indicators">
                <?php for($i = 0; $i < min(6, count($latest_blogs)); $i++): ?>
                    <button type="button" data-bs-target="#blogCarousel" data-bs-slide-to="<?= $i ?>"
                        <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>
                        aria-label="Slide <?= $i + 1 ?>"></button>
                <?php endfor; ?>
            </div>

            <div class="carousel-inner rounded-4">
                <?php if(!empty($latest_blogs)):
                    $active = true;
                    foreach($latest_blogs as $blog):
                        $description = substr(strip_tags($blog["Description"] ?? ''), 0, 150) . (strlen($blog["Description"] ?? '') > 150 ? '...' : '');
                ?>
                <div class="carousel-item <?= $active ? 'active' : '' ?>">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-6 col-12">
                            <img src="cms/images/blogs/<?= htmlspecialchars($blog["PhotoPath"] ?? 'default.jpg') ?>"
                                 class="d-block w-100 blog-img" alt="<?= htmlspecialchars($blog["BlogTitle"] ?? 'Blog Image') ?>">
                        </div>
                        <div class="col-md-6 col-12  p-4">
                            <div class="carousel-caption-custom">
                                <span class="blog-date text-muted mb-2 d-block">
                                    <?php 
                                        $blogDate = isset($blog["BlogDate"]) ? new DateTime($blog["BlogDate"]) : null;
                                        echo $blogDate ? $blogDate->format('F j, Y') : '';
                                    ?>
                                </span>
                                <h4 class="blog-title"><?= htmlspecialchars($blog["BlogTitle"] ?? 'Untitled Blog') ?></h4>
                                <p class="blog-desc"><?= $description ?></p>
                                <a href="blog_details.php?BlogId=<?= $blog["BlogId"] ?>" class="btn read-more-btn">
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $active = false; endforeach; else: ?>
                <div class="carousel-item active">
                    <div class="text-center p-5">
                        <h3>No recent blogs found</h3>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#blogCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#blogCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon bg-dark rounded-circle p-2"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

            <div class="row">
                <div class="col">
                    <div class="blog-style-6-3-grid">
                        <?php
                    $blog_data = $blog_data ?? [];
                    if (!empty($blog_data)) {
                        foreach ($blog_data as $blogs) {
                            $description = $blogs["Description"] ?? '';
                            $short_description = substr($description, 0, 200);
                            if (strlen($description) > 200) {
                                $short_description .= "...";
                            }
                            ?>
                        <div class="blog-start">
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
                                        <a href="blog_details.php?BlogId=<?php echo ($blogs["BlogId"] ?? ''); ?>">
                                            <?php echo ($blogs["BlogTitle"] ?? 'Untitled Blog'); ?>
                                        </a>
                                    </h6>
                                    <span class="blog-admin">
                                        By <span class="blog-editor">My Nutrify Herbal & Ayurveda.</span>
                                    </span>
                                </div>
                                <p class="blog-description"><?php echo ($short_description); ?></p>
                                <p class="blog-comments">Comments:
                                    <?php echo ($blogs["CommentCount"] ?? '0'); ?></p>
                                <div class="more-blog">
                                    <a href="blog_details.php?BlogId=<?php echo ($blogs["BlogId"] ?? ''); ?>"
                                        class="read-link">
                                        <span>Read more</span>
                                        <i class="ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                    } else {
                        echo "<p>No blog posts found.</p>";
                    }
                    ?>
                    </div>
                    <div class="all-page">
                        <span class="page-title">
                            Showing <?php echo $offset + 1; ?> -
                            <?php echo min($offset + $blogs_per_page, $total_blogs); ?> of <?php echo $total_blogs; ?>
                            results
                        </span>
                        <div class="page-number style-1">
                            <!-- Previous Page Link -->
                            <?php if ($current_page > 1): ?>
                            <a href="?page=<?php echo $current_page - 1; ?>"><i class="fa fa-angle-double-left"></i></a>
                            <?php endif; ?>

                            <!-- Page Number Links -->
                            <?php
                                    for ($page = 1; $page <= $total_pages; $page++) {
                                        echo '<a href="?page=' . $page . '" class="' . ($current_page == $page ? 'active' : '') . '">' . $page . '</a>';
                                    }
                                    ?>

                            <!-- Next Page Link -->
                            <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?php echo $current_page + 1; ?>"><i
                                    class="fa fa-angle-double-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


        <!-- blog end -->
        <!-- footer start -->
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
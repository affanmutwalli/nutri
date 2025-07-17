<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// Ensure the user is logged in
if (!isset($_SESSION["CustomerId"])) {
    header("Location: login.php");
    exit();
}

// Fetch all orders for the logged-in customer
$customerId = $_SESSION["CustomerId"];
$FieldNames = array("OrderId", "OrderDate", "Amount", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType", "TransactionId", "CreatedAt");
$Fields = implode(",", $FieldNames);

$Orders = $obj->MysqliSelect1(
    "SELECT $Fields FROM order_master WHERE CustomerId = ? ORDER BY CreatedAt DESC",
    $FieldNames,
    "i",
    [$customerId]
);

// if (!$Orders) {
//     die("<h4>No orders found.</h4>");
// }


 if($_SESSION["CustomerId"]){
?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Organic and Healthy Products</title>
    <meta name="description"
        content="My Nutrify offers a wide range of organic, healthy, and nutritious products for your wellness and lifestyle." />
    <meta name="keywords"
        content="organic products, healthy food, nutrition, eCommerce, wellness, healthy living, organic supplements, eco-friendly" />
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
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
<!--</head>-->

<body class="home-1">
    <div class="loading">
        <div class="text-align">
            <img class="loader-img" src="image/preloader.gif" />
        </div>
    </div>
    <!-- header start -->
    <?php include("components/header.php") ?>
    <!-- header end -->

    <section id="order-section" class="section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="order-area">
    
                        <div class="table-responsive">
                            <?php if ($Orders) { ?>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="print-hide">Product Photo</th>
                                            <th>Product Name</th>
                                            <th>Order no.</th>
                                            <th>Quantity</th>
                                            <th>Sub Total</th>
                                            <th>Order Date</th>
                                            <th>Track Your Order</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($Orders as $OrderData) {
                                            $orderId = $OrderData["OrderId"];
    
                                            // Fetch order details
                                            $FieldNames = array("ProductId", "ProductCode", "Quantity", "SubTotal");
                                            $Fields = implode(",", $FieldNames);
                                            $OrderDetails = $obj->MysqliSelect1(
                                                "SELECT $Fields FROM order_details WHERE OrderId = ?",
                                                $FieldNames,
                                                "s",
                                                [$orderId]
                                            );
    
                                            if ($OrderDetails) {
                                                foreach ($OrderDetails as $OrderDetail) {
                                                    // Fetch product details
                                                    $prodFieldNames = array("ProductId", "ProductName", "PhotoPath", "SubCategoryId");
                                                    $prodFields = implode(",", $prodFieldNames);
                                                    $product_data = $obj->MysqliSelect1(
                                                        "SELECT $prodFields FROM product_master WHERE ProductId = ?",
                                                        $prodFieldNames,
                                                        "i",
                                                        [$OrderDetail["ProductId"]]
                                                    );
                                                    $product = isset($product_data[0]) ? $product_data[0] : [];
                                                    ?>
                                                    <tr>
                                                        <td class="print-hide">
                                                            <?php if (!empty($product["PhotoPath"])) { ?>
                                                                <img src="cms/images/products/<?php echo htmlspecialchars($product["PhotoPath"]); ?>" alt="Product Image" class="img-fluid" style="max-width:80px;">
                                                            <?php } else { ?>
                                                                N/A
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($product["ProductName"] ?? "N/A"); ?></td>
                                                        <td><?php echo htmlspecialchars($orderId); ?></td>
                                                        <td><?php echo htmlspecialchars($OrderDetail["Quantity"]); ?></td>
                                                        <td>₹<?php echo number_format($OrderDetail['SubTotal'], 2); ?></td>
                                                        <td><?php echo date("d-m-Y h:i A", strtotime($OrderData["CreatedAt"])); ?></td>
                                                        <td>
                                                            <a href="tracking.php?OrderId=<?php echo htmlspecialchars($orderId); ?>" class="btn btn-style1">
                                                                Track Order
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="7">No order details found.</td></tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <div class="text-center py-5">
                                    <img src="image/no-orders.jpg" alt="No Orders Found" style="max-width:200px; margin-bottom:10px;">
                                    <div style="font-size:18px; font-weight:bold; margin-top:10px;">No Orders Found</div>
                                </div>
                            <?php } ?>
                        </div>
    
                    </div><!-- order-area -->
                </div><!-- col -->
            </div><!-- row -->
        </div><!-- container -->
    </section>


   
    <!-- login end -->
    <!-- footer start -->
    <?php include("components/footer.php") ?>
    <!-- footer end -->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

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

</html>
<?php
 } 
 else {
    header("Location: tracking.php");
    exit;
  }?>
<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
// include('includes/ShipRocket.php'); // Removed - using Delhivery only
$obj = new main();
$obj->connection();

$FieldNames = array("OrderId", "OrderDate", "Amount", "PaymentStatus","OrderStatus","ShipAddress","PaymentType","TransactionId","CreatedAt","CustomerId","Name","Email","MobileNo");
$ParamArray = [$_GET["order_id"]];
// Get order data with customer details - fix column names to match actual table structure
$OrderData = $obj->MysqliSelect1("SELECT om.OrderId, om.OrderDate, om.Amount, om.PaymentStatus, om.OrderStatus, om.ShipAddress, om.PaymentType, om.TransactionId, om.CreatedAt, om.CustomerId, c.Name, c.Email, c.MobileNo FROM order_master om JOIN customer_master c ON om.CustomerId = c.CustomerId WHERE om.OrderId = ?", $FieldNames, "s", $ParamArray);

if (!$OrderData) {
    header("HTTP/1.1 404 Not Found");
    die("Order not found");
}

?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify - Order Details</title>
    <meta name="description" content="My Nutrify offers a wide range of organic, healthy, and nutritious products for your wellness and lifestyle."/>
    <meta name="keywords" content="organic products, healthy food, nutrition, eCommerce, wellness, healthy living, organic supplements, eco-friendly"/>
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
   
</head>
<!--</head>-->
<body class="home-1">

    <!-- header start -->
        <?php include("components/header.php") ?>
        <!-- header end -->
                        <div class="container" style="margin-top:20px;">
          <a href="print-order.php?order_id=<?php echo $OrderData[0]['OrderId']; ?>" class="tracking-link btn btn-style1 ">Print Order</a>

        </div>
       <section id="order-section" class="section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="order-area">
                            <div class="order-price">
                                <ul class="total-order">
                                    <li>
                                        <span class="order-no">Order no. <?php echo $OrderData[0]["OrderId"]; ?></span>
                                        <span class="order-date"><?php echo date("d-m-Y h:i A", strtotime($OrderData[0]["CreatedAt"])); ?></span>
                                    </li>
                                    <li>
                                        <span class="total-price">Order total</span>
                                        <span class="amount">₹<?php echo number_format($OrderData[0]['Amount'], 2); ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="order-details">
                                <span class="text-success order-i"><i class="fa fa-check-circle"></i></span>
                                <h4>Thank You for Choosing My Nutrify!</h4>
                                <span class="order-s">Your order has been successfully placed.</span>
                                <a href="tracking.php?OrderId=<?php echo $OrderData[0]['OrderId']; ?>" class="tracking-link btn btn-style1">Track Your Order</a>
                            </div>
                            <div class="order-details">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <!-- Add the print-hide class to hide this column in print -->
                                                <th class="print-hide">Product Photo</th>
                                                <th>Product Name</th>
                                                <th>Product Code</th>
                                                <th>Quantity</th>
                                                <th>Size</th>
                                                <th>Price</th>
                                                <th>Sub Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Fetch order details from the order_details table
                                            $FieldNames = array("ProductId", "ProductCode", "Quantity", "Size", "Price", "SubTotal");
                                            $ParamArray = [$_GET["order_id"]];
                                            $Fields = implode(",", $FieldNames);
                                            $OrderDetails = $obj->MysqliSelect1(
                                                "SELECT $Fields FROM order_details WHERE OrderId = ?",
                                                $FieldNames,
                                                "s",
                                                $ParamArray
                                            );
                                            
                                            if ($OrderDetails) {
                                                // Loop through each order detail record
                                                foreach ($OrderDetails as $OrderDetail) {
                                                    // For each order detail, fetch the product details from product_master table
                                                    $prodFieldNames = array("ProductId", "ProductName", "PhotoPath", "SubCategoryId");
                                                    $prodParamArray = array($OrderDetail["ProductId"]);
                                                    $prodFields = implode(",", $prodFieldNames);
                                                    $product_data = $obj->MysqliSelect1(
                                                        "SELECT $prodFields FROM product_master WHERE ProductId = ?",
                                                        $prodFieldNames,
                                                        "i",
                                                        $prodParamArray
                                                    );
                                                    // Use the first (or only) row of product data
                                                    $product = isset($product_data[0]) ? $product_data[0] : [];
                                                    ?>
                                                    <tr>
                                                        <!-- Product Photo column with print-hide class -->
                                                        <td class="print-hide">
                                                            <?php if (!empty($product["PhotoPath"])) { ?>
                                                                <img src="cms/images/products/<?php echo htmlspecialchars($product["PhotoPath"]); ?>" 
                                                                     alt="Product Image" 
                                                                     class="img-fluid" 
                                                                     style="max-width:80px;">
                                                            <?php } else { ?>
                                                                N/A
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($product["ProductName"] ?? "N/A"); ?></td>
                                                        <td><?php echo htmlspecialchars($OrderDetail["ProductCode"]); ?></td>
                                                        <td><?php echo htmlspecialchars($OrderDetail["Quantity"]); ?></td>
                                                        <td><?php echo htmlspecialchars($OrderDetail["Size"]); ?></td>
                                                        <td><?php echo htmlspecialchars($OrderDetail["Price"]); ?></td>
                                                        <td><?php echo htmlspecialchars($OrderDetail["SubTotal"]); ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                // In case no order details were found
                                                echo '<tr><td colspan="7">No order details found.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
        
                            <div class="order-delivery">
                                <ul class="delivery-payment">
                                    <li class="delivery">
                                        <h5>Delivery address</h5>
                                        <span class="order-span">
                                            <?php 
                                                $shippingAddress = $OrderData[0]['ShipAddress'];
                                                echo str_replace(",", "<br>", $shippingAddress);
                                            ?>
                                        </span>
                                    </li>
                                    <li class="pay">
                                        <h5>Payment summary</h5>
                                            <?php 
                                                // Ensure that OrderData exists and TransactionId is not empty or "NA"
                                                if (isset($OrderData[0]['TransactionId']) && !in_array($OrderData[0]['TransactionId'], ['NA', ''])) {
                                                ?>
                                                    <span class="order-span p-label">
                                                        <span class="n-price">Transaction No : </span>
                                                        <span class="o-price"><?php echo $OrderData[0]["TransactionId"]; ?></span>
                                                    </span>
                                                <?php 
                                                } else {
                                                ?>
                                                    <span class="order-span p-label">
                                                        <span class="n-price">Transaction No : </span>
                                                        <span class="o-price">NA</span>
                                                    </span>
                                                <?php 
                                                }
                                                ?>

                                        <span class="order-span p-label">
                                            <span class="n-price">Order Total</span>
                                            <span class="o-price">₹<?php echo number_format($OrderData[0]['Amount'], 2); ?></span>
                                        </span>
                                        <span class="order-span p-label">
                                            <span class="n-price">Payment Type</span>
                                            <span class="o-price"><?php echo $OrderData[0]["PaymentType"]; ?></span>
                                        </span>
                                        <span class="order-span p-label">
                                            <span class="n-price">Payment Status</span>
                                            <?php 
                                                $paymentStatus = $OrderData[0]["PaymentStatus"];
                                                $color = "text-danger"; // Default red for "Due" or "Failed"
                                                
                                                if ($paymentStatus == "Paid") {
                                                    $color = "text-success"; // Green for Paid
                                                } elseif ($paymentStatus == "Processing") {
                                                    $color = "text-warning"; // Yellow for Processing
                                                }
                                            ?>
                                            <span class="o-price <?php echo $color; ?>"><?php echo $paymentStatus; ?></span>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- order-area -->
                    </div><!-- col -->
                </div><!-- row -->
            </div><!-- container -->
        </section>
        <!-- brand logo end -->
    
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
    <!-- Ensure the script is placed at the end of the body -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
        <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

</body>
</html>
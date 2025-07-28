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

// Log the order lookup attempt
error_log("Order-placed.php: Looking for order ID: " . $_GET["order_id"]);

// Get order data with customer details - fix column names to match actual table structure
$OrderData = $obj->MysqliSelect1("SELECT om.OrderId, om.OrderDate, om.Amount, om.PaymentStatus, om.OrderStatus, om.ShipAddress, om.PaymentType, om.TransactionId, om.CreatedAt, om.CustomerId, c.Name, c.Email, c.MobileNo FROM order_master om JOIN customer_master c ON om.CustomerId = c.CustomerId WHERE om.OrderId = ?", $FieldNames, "s", $ParamArray);

// Log the result
if ($OrderData) {
    error_log("Order-placed.php: Order found successfully");
} else {
    error_log("Order-placed.php: Order not found in database");

    // Try to find recent orders for debugging
    $recentOrders = $obj->MysqliSelect1("SELECT OrderId FROM order_master ORDER BY CreatedAt DESC LIMIT 5", ["OrderId"], "", []);
    if ($recentOrders) {
        error_log("Order-placed.php: Recent orders: " . print_r($recentOrders, true));
    }
}

if (!$OrderData) {
    header("HTTP/1.1 404 Not Found");
    die("Order not found. Order ID searched: " . htmlspecialchars($_GET["order_id"]));
}

// Check if this order just earned points (for showing notification)
$pointsAwarded = 0;
$showPointsNotification = false;

// Check if there's a recent points transaction for this order
try {
    $pointsQuery = "SELECT points FROM points_transactions WHERE order_id = ? AND transaction_type = 'earned' ORDER BY created_at DESC LIMIT 1";
    $pointsResult = $obj->MysqliSelect1($pointsQuery, ["points"], "s", [$_GET["order_id"]]);

    if ($pointsResult && count($pointsResult) > 0) {
        $pointsAwarded = $pointsResult[0]["points"];

        // Show notification if order was placed in the last 10 minutes (likely just placed)
        $orderTime = strtotime($OrderData[0]["CreatedAt"]);
        $currentTime = time();
        $timeDifference = $currentTime - $orderTime;

        if ($timeDifference <= 600) { // 10 minutes
            $showPointsNotification = true;
        }
    }
} catch (Exception $e) {
    error_log("Error checking points for order: " . $e->getMessage());
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

        <!-- Custom CSS for order page -->
        <style>
        .order-header {
            background: #f8f9fa;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .order-status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        .order-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .section-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #fff;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #495057;
        }
        .order-item-row {
            border-bottom: 1px solid #f1f3f4;
            padding: 1rem 0;
        }
        .order-item-row:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .info-label {
            color: #6c757d;
            font-weight: 500;
        }
        .info-value {
            font-weight: 600;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #6c757d;
        }
        </style>

        <div class="container" style="margin-top: 20px;">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="account.php">Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order #<?php echo $OrderData[0]["OrderId"]; ?></li>
                </ol>
            </nav>

            <!-- Order Header -->
            <div class="order-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <h1 class="h3 mb-2">Order #<?php echo $OrderData[0]["OrderId"]; ?></h1>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <?php
                            $statusClass = 'success';
                            $statusText = 'Confirmed';
                            if ($OrderData[0]['OrderStatus'] == 'Process') {
                                $statusClass = 'warning';
                                $statusText = 'Processing';
                            } elseif ($OrderData[0]['OrderStatus'] == 'Shipped') {
                                $statusClass = 'info';
                                $statusText = 'Shipped';
                            } elseif ($OrderData[0]['OrderStatus'] == 'Delivered') {
                                $statusClass = 'success';
                                $statusText = 'Delivered';
                            }
                            ?>
                            <span class="badge badge-<?php echo $statusClass; ?> order-status-badge">
                                <i class="fa fa-check-circle me-1"></i><?php echo $statusText; ?>
                            </span>
                            <span class="order-date"><?php echo date("M d", strtotime($OrderData[0]["CreatedAt"])); ?></span>
                        </div>
                        <p class="text-muted mb-0">Thank you for choosing My Nutrify! Your order has been successfully placed.</p>

                        <?php if ($pointsAwarded > 0): ?>
                        <div class="alert alert-success mt-3" style="border-left: 4px solid #ff8c00; background-color: #fff8f0;">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-star text-warning me-2" style="font-size: 1.2rem;"></i>
                                <div>
                                    <strong style="color: #ff8c00;">🎉 You earned <?php echo $pointsAwarded; ?> reward points!</strong>
                                    <br>
                                    <small class="text-muted">Points have been added to your account and can be used for future purchases.</small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="buy-again.php?order_id=<?php echo $OrderData[0]['OrderId']; ?>" class="btn btn-outline-primary">
                            <i class="fa fa-refresh me-1"></i>Buy again
                        </a>
                        <a href="print-order.php?order_id=<?php echo $OrderData[0]['OrderId']; ?>" class="btn btn-outline-secondary">
                            <i class="fa fa-print me-1"></i>Print Order
                        </a>
                        <a href="tracking.php?OrderId=<?php echo $OrderData[0]['OrderId']; ?>" class="btn btn-primary">
                            <i class="fa fa-truck me-1"></i>Track Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

       <section id="order-section" class="section-tb-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Order Status Section -->
                        <div class="section-card">
                            <h3 class="section-title">Fulfillment status: <?php echo $statusText; ?></h3>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fa fa-check-circle text-success" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1"><?php echo $statusText; ?></h5>
                                    <p class="text-muted mb-0">
                                        <time><?php echo date("M d", strtotime($OrderData[0]["CreatedAt"])); ?></time>
                                        We've received your order.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Order Items Section -->
                        <div class="section-card">
                            <h3 class="section-title">Order items</h3>
                            <?php
                            // Fetch order details from the order_details table
                            $FieldNames = array("ProductId", "ProductCode", "Quantity", "Size", "Price", "SubTotal");
                            $ParamArray = [$_GET["order_id"]];
                            $Fields = implode(",", $FieldNames);

                            // Use GROUP BY to prevent duplicate entries and SUM quantities if needed
                            $OrderDetails = $obj->MysqliSelect1(
                                "SELECT ProductId, ProductCode, SUM(Quantity) as Quantity, Size, Price, SUM(SubTotal) as SubTotal FROM order_details WHERE OrderId = ? GROUP BY ProductId, ProductCode, Size, Price ORDER BY ProductId",
                                $FieldNames,
                                "s",
                                $ParamArray
                            );

                            if ($OrderDetails) {
                                // Create an array to track processed products to avoid duplicates
                                $processedProducts = array();

                                // Loop through each order detail record
                                foreach ($OrderDetails as $OrderDetail) {
                                    // Skip if we've already processed this product
                                    $productKey = $OrderDetail["ProductId"] . "_" . $OrderDetail["Size"];
                                    if (in_array($productKey, $processedProducts)) {
                                        continue;
                                    }
                                    $processedProducts[] = $productKey;
                                    // For each order detail, fetch the product details from product_master table
                                    $prodFieldNames = array("ProductId", "ProductName", "PhotoPath", "SubCategoryId");
                                    $prodParamArray = array($OrderDetail["ProductId"]);
                                    $prodFields = implode(",", $prodFieldNames);
                                    $product_data = $obj->MysqliSelect1(
                                        "SELECT DISTINCT $prodFields FROM product_master WHERE ProductId = ? LIMIT 1",
                                        $prodFieldNames,
                                        "i",
                                        $prodParamArray
                                    );
                                    // Use the first (or only) row of product data
                                    $product = isset($product_data[0]) ? $product_data[0] : [];
                                    ?>
                                    <div class="order-item-row">
                                        <div class="row align-items-center">
                                            <div class="col-md-2 print-hide">
                                                <?php if (!empty($product["PhotoPath"])) { ?>
                                                    <img src="cms/images/products/<?php echo htmlspecialchars($product["PhotoPath"]); ?>"
                                                         alt="Product Image"
                                                         class="product-image">
                                                <?php } else { ?>
                                                    <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fa fa-image text-muted"></i>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($product["ProductName"] ?? "N/A"); ?></h6>
                                                <p class="text-muted mb-0 small">
                                                    <?php echo htmlspecialchars($OrderDetail["Size"]); ?> |
                                                    Code: <?php echo htmlspecialchars($OrderDetail["ProductCode"]); ?>
                                                </p>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <span class="badge badge-light">Qty: <?php echo htmlspecialchars($OrderDetail["Quantity"]); ?></span>
                                            </div>
                                            <div class="col-md-2 text-right">
                                                <strong>₹<?php echo number_format($OrderDetail["SubTotal"], 2); ?></strong>
                                                <br><small class="text-muted">₹<?php echo number_format($OrderDetail["Price"], 2); ?> each</small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<p class="text-muted">No order details found.</p>';
                            }
                            ?>
                        </div>

                        <!-- Order Details Section -->
                        <div class="section-card">
                            <h3 class="section-title">Order details</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Contact information</h6>
                                    <div class="info-row">
                                        <span class="info-label">Name:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($OrderData[0]['Name']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Email:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($OrderData[0]['Email']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phone:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($OrderData[0]['MobileNo']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Shipping address</h6>
                                    <address class="mb-0">
                                        <?php
                                        $shippingAddress = $OrderData[0]['ShipAddress'];
                                        echo nl2br(htmlspecialchars(str_replace(",", "\n", $shippingAddress)));
                                        ?>
                                    </address>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Payment</h6>
                                    <div class="info-row">
                                        <span class="info-label">Payment Method:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($OrderData[0]["PaymentType"]); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Payment Status:</span>
                                        <?php
                                        $paymentStatus = $OrderData[0]["PaymentStatus"];
                                        $statusColor = "text-danger"; // Default red for "Due" or "Failed"

                                        if ($paymentStatus == "Paid") {
                                            $statusColor = "text-success"; // Green for Paid
                                        } elseif ($paymentStatus == "Processing") {
                                            $statusColor = "text-warning"; // Yellow for Processing
                                        }
                                        ?>
                                        <span class="info-value <?php echo $statusColor; ?>"><?php echo $paymentStatus; ?></span>
                                    </div>
                                    <?php if (isset($OrderData[0]['TransactionId']) && !in_array($OrderData[0]['TransactionId'], ['NA', ''])): ?>
                                    <div class="info-row">
                                        <span class="info-label">Transaction ID:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($OrderData[0]["TransactionId"]); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Shipping method</h6>
                                    <p class="mb-0">Standard Delivery</p>
                                    <small class="text-muted">Delivered within 3-7 business days</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Payment Status Section -->
                        <div class="section-card">
                            <h3 class="section-title">Payment status</h3>
                            <div class="text-center mb-3">
                                <h4 class="text-primary">₹<?php echo number_format($OrderData[0]['Amount'], 2); ?> INR</h4>
                                <?php
                                $paymentStatus = $OrderData[0]["PaymentStatus"];
                                $statusBadge = "badge-danger"; // Default red for "Due" or "Failed"

                                if ($paymentStatus == "Paid") {
                                    $statusBadge = "badge-success"; // Green for Paid
                                } elseif ($paymentStatus == "Processing") {
                                    $statusBadge = "badge-warning"; // Yellow for Processing
                                }
                                ?>
                                <span class="badge <?php echo $statusBadge; ?>"><?php echo $paymentStatus; ?></span>
                            </div>
                        </div>

                        <!-- Order Summary Section -->
                        <div class="section-card">
                            <h3 class="section-title">Order summary</h3>

                            <?php
                            // Calculate subtotal from order details
                            $subtotal = 0;
                            if ($OrderDetails) {
                                foreach ($OrderDetails as $OrderDetail) {
                                    $subtotal += $OrderDetail["SubTotal"];
                                }
                            }
                            $shipping = 0; // You can calculate shipping if needed
                            $total = $OrderData[0]['Amount'];
                            ?>

                            <div class="info-row">
                                <span class="info-label">Subtotal:</span>
                                <span class="info-value">₹<?php echo number_format($subtotal, 2); ?></span>
                            </div>

                            <?php if ($shipping > 0): ?>
                            <div class="info-row">
                                <span class="info-label">Shipping:</span>
                                <span class="info-value">₹<?php echo number_format($shipping, 2); ?></span>
                            </div>
                            <?php endif; ?>

                            <hr>

                            <div class="info-row">
                                <span class="info-label"><strong>Total:</strong></span>
                                <span class="info-value"><strong>INR ₹<?php echo number_format($total, 2); ?></strong></span>
                            </div>

                            <?php
                            // Calculate tax if included in total
                            $taxAmount = $total * 0.18; // Assuming 18% tax, adjust as needed
                            if ($taxAmount > 0):
                            ?>
                            <p class="text-muted small mt-2">Including ₹<?php echo number_format($taxAmount, 2); ?> in taxes</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
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

<?php if ($showPointsNotification && $pointsAwarded > 0): ?>
<script>
// Show points notification for recently placed orders
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure page is fully loaded
    setTimeout(function() {
        Swal.fire({
            icon: 'success',
            title: '🎉 Congratulations!',
            html: `<div style="font-size: 18px; color: #ff8c00; font-weight: bold; margin: 10px 0;">
                      You earned +<?php echo $pointsAwarded; ?> Points!
                   </div>
                   <div style="font-size: 14px; color: #666;">
                      Points have been added to your account for this order.
                   </div>`,
            confirmButtonText: 'Awesome!',
            confirmButtonColor: '#ff8c00',
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: true,
            allowOutsideClick: true,
            backdrop: true
        });
    }, 1000); // 1 second delay
});
</script>
<?php endif; ?>

</body>
</html>
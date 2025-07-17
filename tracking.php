<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
include('includes/Delhivery.php');
$obj = new main();
$obj->connection();

// Check if OrderId is set and not empty
if (!isset($_GET['OrderId']) || empty(trim($_GET['OrderId']))) {
    die('Order ID is required.');
}

$OrderId = $_GET['OrderId'];

// Fetch order details - Include Waybill column if it exists
$FieldNames = array("OrderId", "OrderStatus", "CustomerId", "ShipAddress", "Waybill", "delivery_status");
$ParamArray = array($OrderId);
$Fields = implode(",", $FieldNames);
$orderResult = $obj->MysqliSelect1(
    "SELECT " . $Fields . " FROM order_master WHERE OrderId = ?",
    $FieldNames,
    "s",
    $ParamArray
);

// Extract the first row from the result
$order = $orderResult && count($orderResult) > 0 ? $orderResult[0] : null;

// Initialize Delhivery for tracking
$order['tracking_details'] = [];
$order['waybill_number'] = '';

try {
    // Check if order has a waybill number
    $waybill = $order['Waybill'] ?? '';

    if (!empty($waybill) && $waybill !== 'NULL') {
        $delhivery = new Delhivery();
        $trackingInfo = $delhivery->trackShipment($waybill);

        if (!empty($trackingInfo['ShipmentData'])) {
            // Update order status based on Delhivery tracking data
            $shipmentData = $trackingInfo['ShipmentData'][0] ?? [];
            if (!empty($shipmentData['Shipment']['Status']['Status'])) {
                $order['OrderStatus'] = $shipmentData['Shipment']['Status']['Status'];
                $order['tracking_details'] = $shipmentData['Shipment']['Scans'] ?? [];
                $order['waybill_number'] = $waybill;
            }
        }
    }
} catch (Exception $e) {
    // If tracking fails, continue with existing order status
    // Only log if we actually tried to track (had a waybill)
    if (!empty($waybill)) {
        error_log("Delhivery tracking error for waybill $waybill: " . $e->getMessage());
    }
}

// Check if order exists and has the OrderStatus
if (!$order || !isset($order['OrderStatus'])) {
    die('Order not found or missing status.');
}

// Define the steps of the order tracking process
$steps = [
    'Order Placed',
    'Order Confirmed',
    'Picked by courier',
    'On the way',
    'Delivered'
];

// Map actual order statuses to tracking steps
$statusMapping = [
    'Placed' => 'Order Placed',
    'Created' => 'Order Placed',
    'Process' => 'Order Placed',
    'Confirmed' => 'Order Confirmed',
    'Shipped' => 'Picked by courier',
    'In Transit' => 'On the way',
    'Out for Delivery' => 'On the way',
    'Delivered' => 'Delivered'
];

// Get the mapped status or use the original status
$mappedStatus = $statusMapping[$order['OrderStatus']] ?? $order['OrderStatus'];

// Determine the current step based on the mapped status
$current_step = array_search($mappedStatus, $steps);
if ($current_step === false) {
    // If status is not in our predefined steps, default to first step
    $current_step = 0;
    $mappedStatus = 'Order Placed';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>My Nutrify - Your Destination for Health & Wellness</title>
    <meta name="description"
        content="MyNutrify offers a wide range of organic and Ayurveda products for your health and wellness. Explore a variety of natural products to nourish your body and mind." />
    <meta name="keywords"
        content="organic food, health products, Ayurveda, natural supplements, wellness, herbal products, nutrition, healthy living" />
    <meta name="author" content="MyNutrify">

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
    <link rel="stylesheet" type="text/css" href="css/tracking.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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

    
    <!-- header start -->
    <?php include("components/header.php"); ?>



    <section class="about-breadcrumb">
    <div class="about-back section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="about-l">
                        <h1 class="about-p"><span>Track Your Order</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-tb-padding">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="track-area">
                    <div class="track-price">
                        <ul class="track-order">
                            <li>
                                <h4>Order ID: <?= htmlspecialchars($order['OrderId']) ?></h4>
                            </li>
                            <li>
                                <span class="track-status">Status:</span>
                                <?= htmlspecialchars($mappedStatus) ?>
                            </li>
                        </ul>
                    </div>
                    <div class="track-main">
                        <?php if (!empty($order['shipment_id']) && !empty($order['tracking_details'])): ?>
                            <div class="shiprocket-tracking">
                                <?php foreach ($order['tracking_details'] as $track): ?>
                                    <div class="tracking-step <?php echo $track['status'] === $order['OrderStatus'] ? 'current' : ''; ?>">
                                        <div class="tracking-time">
                                            <?php echo date('d M Y h:i A', strtotime($track['date'])); ?>
                                        </div>
                                        <div class="tracking-info">
                                            <span class="status"><?php echo htmlspecialchars($track['status']); ?></span>
                                            <?php if (!empty($track['location'])): ?>
                                                <span class="location"><?php echo htmlspecialchars($track['location']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="track">
                                <?php foreach ($steps as $index => $step): ?>
                                <div class="step <?= $index <= $current_step ? 'active' : '' ?>">
                                    <span class="icon">
                                        <?php switch($index):
                                            case 0: ?>
                                                <i class="fa fa-check"></i>
                                                <?php break;
                                            case 1: ?>
                                                <i class="fa fa-box"></i>
                                                <?php break;
                                            case 2: ?>
                                                <i class="fa fa-truck"></i>
                                                <?php break;
                                            default: ?>
                                                <i class="fa fa-home"></i>
                                        <?php endswitch; ?>
                                    </span>
                                    <span class="text"><?= htmlspecialchars($step) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Shipping information display -->
                    <div class="shipping-info mt-4">
                        <h5>Shipping Details</h5>
                        <?php if (!empty($order['waybill_number'])): ?>
                            <p><strong>AWB/Waybill Number:</strong> <?php echo htmlspecialchars($order['waybill_number']); ?></p>
                        <?php elseif (!empty($order['Waybill'])): ?>
                            <p><strong>AWB/Waybill Number:</strong> <?php echo htmlspecialchars($order['Waybill']); ?></p>
                        <?php else: ?>
                            <p><strong>AWB/Waybill Number:</strong> <em>Not yet assigned</em></p>
                        <?php endif; ?>
                        <p><strong>Delivery Status:</strong> <?php echo htmlspecialchars($order['delivery_status'] ?? 'Pending'); ?></p>
                        <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['ShipAddress']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


    <?php include("components/footer.php"); ?>

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

<!-- Mirrored from spacingtech.com/html/vegist-final/vegist/tracking.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 04 Dec 2024 06:44:49 GMT -->

</html>
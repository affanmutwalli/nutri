<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// Get order ID from URL parameter
$orderId = $_GET['order_id'] ?? '';
$orderData = null;
$orderDetails = [];

if (!empty($orderId)) {
    // Fetch order data from all_orders_unified view (supports both guest and registered orders)
    $FieldNames = array("OrderId", "CustomerName", "CustomerEmail", "CustomerPhone", "CustomerType", "OrderDate", "Amount", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType", "CreatedAt");
    $ParamArray = [$orderId];
    $Fields = implode(",", $FieldNames);
    
    $orderData = $obj->MysqliSelect1("SELECT $Fields FROM all_orders_unified WHERE OrderId = ?", $FieldNames, "s", $ParamArray);
    
    if ($orderData) {
        // Fetch order details (products)
        $FieldNamesDetails = array("ProductId", "ProductCode", "Quantity", "Size", "Price", "SubTotal");
        $ParamArrayDetails = [$orderId];
        $FieldsDetails = implode(",", $FieldNamesDetails);
        
        $orderDetails = $obj->MysqliSelect1("SELECT $FieldsDetails FROM order_details WHERE OrderId = ?", $FieldNamesDetails, "s", $ParamArrayDetails);
    }
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Your Order - My Nutrify</title>
    <meta name="description" content="Track your order status and delivery information on MyNutrify" />
    
    <!-- favicon -->
    <link rel="shortcut icon" type="image/favicon" href="image/fevicon.png">
    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <!-- font-awesome icon -->
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <!-- style -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
    .track-order-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .order-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .order-header h1 {
        color: #ec6504;
        margin-bottom: 10px;
    }
    
    .order-status {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 14px;
    }
    
    .status-placed { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d4edda; color: #155724; }
    .status-shipped { background: #d1ecf1; color: #0c5460; }
    .status-delivered { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    
    .order-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .info-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #ec6504;
    }
    
    .info-card h4 {
        color: #333;
        margin-bottom: 10px;
        font-size: 16px;
    }
    
    .info-card p {
        margin: 5px 0;
        color: #666;
    }
    
    .search-form {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .search-form input {
        padding: 12px 20px;
        border: 2px solid #ddd;
        border-radius: 25px;
        width: 300px;
        max-width: 100%;
        margin-right: 10px;
    }
    
    .search-form button {
        padding: 12px 30px;
        background: #ec6504;
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
    }
    
    .search-form button:hover {
        background: #d55a04;
    }
    
    .no-order {
        text-align: center;
        padding: 50px 20px;
        color: #666;
    }
    
    .no-order i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .track-order-container {
            margin: 20px;
            padding: 20px;
        }
        
        .search-form input {
            width: 100%;
            margin-bottom: 15px;
            margin-right: 0;
        }
    }
    </style>
</head>

<body class="home-1">
    <!-- header start -->
    <?php include("components/header.php"); ?>
    <!-- header end -->
    
    <div class="track-order-container">
        <div class="order-header">
            <h1><i class="fa fa-search"></i> Track Your Order</h1>
            <p>Enter your order ID to track your order status and delivery information</p>
        </div>
        
        <!-- Order Search Form -->
        <div class="search-form">
            <form method="GET" action="">
                <input type="text" name="order_id" placeholder="Enter Order ID (e.g., MN000123)" 
                       value="<?php echo htmlspecialchars($orderId); ?>" required>
                <button type="submit"><i class="fa fa-search"></i> Track Order</button>
            </form>
        </div>
        
        <?php if (!empty($orderId)): ?>
            <?php if ($orderData): ?>
                <!-- Order Found -->
                <div class="order-info">
                    <div class="info-card">
                        <h4><i class="fa fa-receipt"></i> Order Information</h4>
                        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($orderData[0]['OrderId']); ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('d M Y', strtotime($orderData[0]['OrderDate'])); ?></p>
                        <p><strong>Total Amount:</strong> ₹<?php echo number_format($orderData[0]['Amount'], 2); ?></p>
                        <p><strong>Payment:</strong> <?php echo htmlspecialchars($orderData[0]['PaymentType']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="order-status status-<?php echo strtolower($orderData[0]['OrderStatus']); ?>">
                                <?php echo htmlspecialchars($orderData[0]['OrderStatus']); ?>
                            </span>
                        </p>
                    </div>
                    
                    <div class="info-card">
                        <h4><i class="fa fa-user"></i> Customer Information</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($orderData[0]['CustomerName']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($orderData[0]['CustomerEmail']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($orderData[0]['CustomerPhone']); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($orderData[0]['CustomerType']); ?> Customer</p>
                    </div>
                    
                    <div class="info-card">
                        <h4><i class="fa fa-map-marker"></i> Delivery Address</h4>
                        <p><?php echo nl2br(htmlspecialchars($orderData[0]['ShipAddress'])); ?></p>
                    </div>
                </div>
                
                <?php if (!empty($orderDetails)): ?>
                <div class="info-card">
                    <h4><i class="fa fa-box"></i> Order Items</h4>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">Product Code</th>
                                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">Size</th>
                                    <th style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd;">Quantity</th>
                                    <th style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">Price</th>
                                    <th style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderDetails as $item): ?>
                                <tr>
                                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($item['ProductCode']); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($item['Size']); ?></td>
                                    <td style="padding: 10px; text-align: center; border-bottom: 1px solid #eee;"><?php echo $item['Quantity']; ?></td>
                                    <td style="padding: 10px; text-align: right; border-bottom: 1px solid #eee;">₹<?php echo number_format($item['Price'], 2); ?></td>
                                    <td style="padding: 10px; text-align: right; border-bottom: 1px solid #eee;">₹<?php echo number_format($item['SubTotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Order Not Found -->
                <div class="no-order">
                    <i class="fa fa-exclamation-triangle"></i>
                    <h3>Order Not Found</h3>
                    <p>We couldn't find an order with ID: <strong><?php echo htmlspecialchars($orderId); ?></strong></p>
                    <p>Please check your order ID and try again.</p>
                    <p style="margin-top: 20px;">
                        <a href="index.php" style="color: #ec6504; text-decoration: none;">
                            <i class="fa fa-home"></i> Return to Home
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- footer start -->
    <?php include("components/footer.php"); ?>
    
    <!-- back to top start -->
    <a href="javascript:void(0)" class="scroll" id="top">
        <span><i class="fa fa-angle-double-up"></i></span>
    </a>
    <!-- back to top end -->
    
    <!-- jquery -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <!-- custom -->
    <script src="js/custom.js"></script>
</body>
</html>

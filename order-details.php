<!DOCTYPE html>
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');

// Check if user is logged in
if (!isset($_SESSION["CustomerId"]) || empty($_SESSION["CustomerId"])) {
    header("Location: login.php");
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: account.php");
    exit();
}

$obj = new main();
$obj->connection();

$orderId = $_GET['id'];
$customerId = $_SESSION["CustomerId"];

// Get order details - ensure it belongs to the logged-in customer
$orderFieldNames = array("OrderId", "OrderDate", "Amount", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType", "TransactionId", "CreatedAt");
$orderFields = implode(",", $orderFieldNames);
$orderParamArray = array($orderId, $customerId);

$orderData = $obj->MysqliSelect1("SELECT $orderFields FROM order_master WHERE OrderId = ? AND CustomerId = ?", $orderFieldNames, "si", $orderParamArray);

if (empty($orderData)) {
    header("Location: account.php");
    exit();
}

// Get order items
$itemFieldNames = array("ProductId", "Quantity", "Price", "SubTotal", "ProductCode");
$itemFields = implode(",", $itemFieldNames);
$itemParamArray = array($orderId);

$orderItems = $obj->MysqliSelect1("SELECT $itemFields FROM order_details WHERE OrderId = ?", $itemFieldNames, "s", $itemParamArray);

// Get customer data
$customerFieldNames = array("Name", "MobileNo", "Email");
$customerFields = implode(",", $customerFieldNames);
$customerParamArray = array($customerId);

$customerData = $obj->MysqliSelect1("SELECT $customerFields FROM customer_master WHERE CustomerId = ?", $customerFieldNames, "i", $customerParamArray);

// Get product details for each item
$productDetails = array();
if (!empty($orderItems)) {
    foreach ($orderItems as $item) {
        $productFieldNames = array("ProductName", "ProductCode", "PhotoPath");
        $productFields = implode(",", $productFieldNames);
        $productParamArray = array($item['ProductId']);
        
        $product = $obj->MysqliSelect1("SELECT $productFields FROM product_master WHERE ProductId = ?", $productFieldNames, "i", $productParamArray);
        if (!empty($product)) {
            $productDetails[$item['ProductId']] = $product[0];
        }
    }
}

$order = $orderData[0];
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Details - My Nutrify</title>
    <meta name="description" content="View your order details and track your order status." />
    
    <!-- favicon -->
    <link rel="shortcut icon" type="image/favicon" href="image/fevicon.png">
    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <!-- font-awesome icon -->
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <!-- themify icon -->
    <link rel="stylesheet" type="text/css" href="css/themify-icons.css">
    <!-- ion icon -->
    <link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <!-- responsive css -->
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <!-- full width override -->
    <link rel="stylesheet" type="text/css" href="css/full-width-override.css">
    
    <style>
        /* Order Details Styling - Site Consistent */
        .order-details-area {
            background: #fff;
            padding: 80px 0;
        }
        
        .order-title {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .order-title h1 {
            font-size: 30px;
            margin-bottom: 30px;
            text-align: center;
            color: #222;
            font-weight: 600;
        }
        
        .order-info-card {
            background: #fff;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0px 0px 10px 0px rgb(0 0 0 / 10%);
        }
        
        .order-info-card h4 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #222;
            font-weight: 600;
            border-bottom: 2px solid #ec6504;
            padding-bottom: 10px;
        }
        
        .order-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .order-info-row:last-child {
            border-bottom: none;
        }
        
        .order-info-label {
            font-weight: 600;
            color: #555;
        }
        
        .order-info-value {
            color: #222;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: #28a745;
            color: white;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .status-shipped {
            background-color: #17a2b8;
            color: white;
        }
        
        .status-delivered {
            background-color: #28a745;
            color: white;
        }
        
        .payment-paid {
            background-color: #28a745;
            color: white;
        }
        
        .payment-due {
            background-color: #dc3545;
            color: white;
        }
        
        .items-table {
            margin-top: 20px;
        }
        
        .items-table table {
            width: 100%;
            background: #fff;
        }
        
        .items-table table thead th {
            background-color: #ec6504;
            color: #fff;
            font-weight: 600;
            padding: 15px;
            border: none;
            font-size: 14px;
        }
        
        .items-table table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #eee;
            font-size: 14px;
            color: #222;
        }
        
        .items-table table tbody tr:hover {
            background: #f9f9f9;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .back-btn {
            background-color: #ec6504;
            border-color: #ec6504;
            color: #fff;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        
        .back-btn:hover {
            background-color: #d55a04;
            border-color: #d55a04;
            color: #fff;
            text-decoration: none;
        }
        
        .total-amount {
            font-size: 18px;
            font-weight: 700;
            color: #ec6504;
        }
        
        @media (max-width: 768px) {
            .order-info-card {
                padding: 15px;
            }
            
            .order-info-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .items-table table {
                font-size: 12px;
            }
            
            .items-table table thead th,
            .items-table table tbody td {
                padding: 10px 8px;
            }
        }
    </style>

    <!-- Tawk.to Integration -->
    <?php include("components/tawk-to.php"); ?>
</head>

<body>
    <!-- preloader -->
    <?php include('includes/preloader.php'); ?>
    <!-- end preloader -->
    
    <!-- header -->
    <?php include('components/header.php'); ?>
    <!-- end header -->
    
    <!-- order details area -->
    <div class="order-details-area section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <a href="account.php" class="back-btn">
                        <i class="fa fa-arrow-left"></i> Back to My Account
                    </a>
                    
                    <div class="order-title">
                        <h1>Order Details</h1>
                    </div>
                    
                    <!-- Order Information -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="order-info-card">
                                <h4>Order Information</h4>
                                <div class="order-info-row">
                                    <span class="order-info-label">Order ID:</span>
                                    <span class="order-info-value">#<?php echo htmlspecialchars($order['OrderId']); ?></span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-info-label">Order Date:</span>
                                    <span class="order-info-value"><?php echo date('d M Y', strtotime($order['OrderDate'])); ?></span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-info-label">Order Status:</span>
                                    <span class="order-info-value">
                                        <span class="status-badge status-<?php echo strtolower($order['OrderStatus']); ?>">
                                            <?php echo htmlspecialchars($order['OrderStatus']); ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-info-label">Payment Status:</span>
                                    <span class="order-info-value">
                                        <span class="status-badge payment-<?php echo strtolower($order['PaymentStatus']); ?>">
                                            <?php echo htmlspecialchars($order['PaymentStatus']); ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-info-label">Payment Method:</span>
                                    <span class="order-info-value"><?php echo htmlspecialchars($order['PaymentType']); ?></span>
                                </div>
                                <?php if (!empty($order['TransactionId'])): ?>
                                <div class="order-info-row">
                                    <span class="order-info-label">Transaction ID:</span>
                                    <span class="order-info-value"><?php echo htmlspecialchars($order['TransactionId']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="order-info-row">
                                    <span class="order-info-label">Total Amount:</span>
                                    <span class="order-info-value total-amount">₹<?php echo number_format($order['Amount'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="order-info-card">
                                <h4>Shipping Information</h4>
                                <?php if (!empty($customerData)): ?>
                                <div class="order-info-row">
                                    <span class="order-info-label">Customer Name:</span>
                                    <span class="order-info-value"><?php echo htmlspecialchars($customerData[0]['Name']); ?></span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-info-label">Mobile Number:</span>
                                    <span class="order-info-value"><?php echo htmlspecialchars($customerData[0]['MobileNo']); ?></span>
                                </div>
                                <div class="order-info-row">
                                    <span class="order-info-label">Email:</span>
                                    <span class="order-info-value"><?php echo htmlspecialchars($customerData[0]['Email']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="order-info-row">
                                    <span class="order-info-label">Shipping Address:</span>
                                    <span class="order-info-value"><?php echo htmlspecialchars($order['ShipAddress']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="order-info-card">
                        <h4>Order Items</h4>
                        <div class="items-table">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Product Code</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($orderItems)): ?>
                                            <?php foreach ($orderItems as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div style="display: flex; align-items: center;">
                                                            <?php if (isset($productDetails[$item['ProductId']]) && !empty($productDetails[$item['ProductId']]['PhotoPath'])): ?>
                                                                <img src="cms/images/products/<?php echo htmlspecialchars($productDetails[$item['ProductId']]['PhotoPath']); ?>"
                                                                     alt="Product Image" class="product-image" style="margin-right: 10px;">
                                                            <?php endif; ?>
                                                            <span>
                                                                <?php 
                                                                if (isset($productDetails[$item['ProductId']])) {
                                                                    echo htmlspecialchars($productDetails[$item['ProductId']]['ProductName']);
                                                                } else {
                                                                    echo 'Product ID: ' . htmlspecialchars($item['ProductId']);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($item['ProductCode']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                                                    <td>₹<?php echo number_format($item['Price'], 2); ?></td>
                                                    <td>₹<?php echo number_format($item['SubTotal'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" style="text-align: center;">No items found for this order.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end order details area -->
    

    
    <!-- jquery -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <!-- custom js -->
    <script src="js/custom.js"></script>
</body>
</html>

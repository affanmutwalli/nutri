<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
$productId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'Unknown';
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Not Found - My Nutrify Herbal And Ayurveda</title>
    <meta name="description" content="The requested product was not found. Browse our other herbal and ayurvedic products." />
    <meta name="keywords" content="product not found, herbal products, ayurvedic remedies" />
    
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
    <!-- style -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    
    <style>
        .error-container {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 50px 0;
        }
        .error-content {
            max-width: 600px;
            margin: 0 auto;
        }
        .error-icon {
            font-size: 80px;
            color: #ec6504;
            margin-bottom: 30px;
        }
        .error-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .error-buttons {
            margin-top: 30px;
        }
        .error-buttons .btn {
            margin: 0 10px 10px 0;
            padding: 12px 30px;
            font-size: 1rem;
        }
        .btn-primary {
            background-color: #ec6504;
            border-color: #ec6504;
        }
        .btn-primary:hover {
            background-color: #d35400;
            border-color: #d35400;
        }
        .product-id {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            font-family: monospace;
            font-weight: bold;
            color: #495057;
            display: inline-block;
            margin: 10px 0;
        }
    </style>
</head>

<body class="home-1">
    <?php include("components/header.php"); ?>
    
    <div class="error-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="error-content">
                        <div class="error-icon">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <h1 class="error-title">Product Currently Unavailable</h1>
                        <div class="error-message">
                            <p>We're sorry, but this product is currently not available.</p>
                            <p>This item may be temporarily out of stock or has been discontinued.</p>
                            <p>Please check back later or explore our other amazing herbal and ayurvedic products!</p>
                            <p>We're constantly updating our inventory with new and exciting products for your wellness journey.</p>
                        </div>
                        <div class="error-buttons">
                            <a href="index.php" class="btn btn-primary">
                                <i class="fa fa-home"></i> Return to Homepage
                            </a>
                            <a href="shop.php" class="btn btn-secondary">
                                <i class="fa fa-shopping-bag"></i> Browse All Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include("components/footer.php"); ?>
    
    <!-- jquery -->
    <script src="js/jquery.min.js"></script>
    <!-- bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- custom -->
    <script src="js/custom.js"></script>
</body>
</html>

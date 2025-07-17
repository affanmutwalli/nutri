<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

// Get the order ID from the URL
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : '';

// Fetch the order data and details
$FieldNames = array("ProductId", "ProductCode", "Quantity", "Size", "Price", "SubTotal");
$ParamArray = [$orderId];
$Fields = implode(",", $FieldNames);

// Fetch order details
$OrderDetails = $obj->MysqliSelect1("SELECT $Fields FROM order_details WHERE OrderId = ?", $FieldNames, "s", $ParamArray);

$FieldNames = array("OrderId", "OrderDate", "Amount", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType", "TransactionId", "CreatedAt");
$ParamArray = [$_GET["order_id"]];
$Fields = implode(",", $FieldNames);
$OrderData = $obj->MysqliSelect1("SELECT $Fields FROM order_master WHERE OrderId = ?", $FieldNames, "s", $ParamArray);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Shipping Delivery</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; box-sizing: border-box; }
        .logo { max-width: 40%; height: auto; display: block; margin: 0 auto 20px; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-top: 20px; overflow-x: auto; }
        .invoice-table th, .invoice-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .invoice-table th { background-color: #f2f2f2; }
        .invoice-header { margin-top: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .address { font-size: 14px; line-height: 1.6; }
        .print-btn { display: block; margin: 20px auto; padding: 10px 20px; font-size: 16px; cursor: pointer; }
        @media print {
            .print-btn { display: none; }
        }
        @media (max-width: 600px) {
            .container { padding: 10px; }
            .invoice-table th, .invoice-table td { padding: 6px; font-size: 12px; }
            .invoice-header h2 { font-size: 18px; }
            .invoice-header p { font-size: 14px; }
            .address { font-size: 12px; }
            .print-btn { font-size: 14px; padding: 8px 16px; }
        }
 

    </style>
</head>
<body>
    
    <div class="container">
        <!-- Logo -->
        <img src="image/main_logo.png" alt="My Nutrify Logo" class="logo">

        <!-- Order Information -->
        <div class="invoice-header">
            <h2 class="text-center">Order Details - Shipping Delivery</h2>
            <p><strong>Order Number:</strong> <?php echo htmlspecialchars($OrderData[0]['OrderId']); ?></p>
            <p><strong>Order Date:</strong> <?php echo date("d-m-Y h:i A", strtotime($OrderData[0]['CreatedAt'])); ?></p>
            <p><strong>Payment Type:</strong> <?php echo htmlspecialchars($OrderData[0]['PaymentType']); ?></p>
            <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($OrderData[0]['TransactionId']); ?></p>
            <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($OrderData[0]['PaymentStatus']); ?></p>
        </div>

        <!-- Billing and Shipping Address -->
        <div class="invoice-header">
            <h3>Billing & Shipping Address:</h3>
            <div class = "row">
                <div class="col-6">
                <p><?php echo nl2br(htmlspecialchars($OrderData[0]['ShipAddress'])); ?></p>
            </div>
            </div>
        </div>

        <!-- Order Details Table -->
        <div style="overflow-x: auto;">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Product Code</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalAmount = 0;
                    if ($OrderDetails) {
                        foreach ($OrderDetails as $OrderDetail) {
                            // Fetch product name
                            $prodFieldNames = ["ProductName","ProductCode"];
                            $prodParamArray = [$OrderDetail["ProductId"]];
                            $prodFields = implode(",", $prodFieldNames);
                            $product_data = $obj->MysqliSelect1(
                                "SELECT $prodFields FROM product_master WHERE ProductId = ?",
                                $prodFieldNames,
                                "i",
                                $prodParamArray
                            );
                            $product = $product_data[0] ?? [];

                            $totalAmount += $OrderDetail["SubTotal"];

                            echo "<tr>
                                    <td>" . htmlspecialchars($product["ProductName"] ?? "N/A") . "</td>
                                    <td>" . htmlspecialchars($product["ProductCode"] ?? "N/A") . "</td>
                                    <td>" . htmlspecialchars($OrderDetail["Quantity"]) . "</td>
                                    <td>₹" . number_format($OrderDetail["Price"], 2) . "</td>
                                    <td>₹" . number_format($OrderDetail["SubTotal"], 2) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No order details found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="invoice-header">
            <table>
                <tr>
                    <td><strong>Grand Total:</strong></td>
                    <td class="text-right"><strong>₹<?php echo number_format($totalAmount, 2); ?></strong></td>
                </tr>
            </table>
        </div>

        <!-- Print Button -->
        <div class="text-center">
            <button class="print-btn" onclick="window.print()">Print Order Details</button>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();  // Automatically triggers print dialog when page loads
        }
    </script>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
</body>
</html>
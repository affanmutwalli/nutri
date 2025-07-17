<?php
// Beautiful PDF Invoice Generator for My Nutrify Orders
include('includes/urls.php');
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$obj->connection();
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

// Get Order ID from URL
$orderId = $_GET['order_id'] ?? '';
if (empty($orderId)) {
    die("Order ID is required");
}

// Fetch order details
$query = "SELECT om.*, 
                 COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName,
                 COALESCE(cm.MobileNo, dc.MobileNo, '') as CustomerPhone,
                 COALESCE(cm.Email, dc.Email, '') as CustomerEmail,
                 ca.Address as CustomerAddress,
                 ca.Landmark as CustomerLandmark,
                 ca.City as CustomerCity,
                 ca.State as CustomerState,
                 ca.PinCode as CustomerPincode
          FROM order_master om
          LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
          LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
          LEFT JOIN customer_address ca ON om.CustomerId = ca.CustomerId AND om.CustomerType = 'Registered'
          WHERE om.OrderId = ?";

$stmt = mysqli_prepare($mysqli, $query);
mysqli_stmt_bind_param($stmt, "s", $orderId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    die("Order not found");
}

// Fetch order items
$itemsQuery = "SELECT od.*, pm.ProductName, pm.ProductCode
               FROM order_details od
               JOIN product_master pm ON od.ProductId = pm.ProductId
               WHERE od.OrderId = ?";
$itemsStmt = mysqli_prepare($mysqli, $itemsQuery);
mysqli_stmt_bind_param($itemsStmt, "s", $orderId);
mysqli_stmt_execute($itemsStmt);
$itemsResult = mysqli_stmt_get_result($itemsStmt);
$orderItems = [];
while ($item = mysqli_fetch_assoc($itemsResult)) {
    $orderItems[] = $item;
}

// Set content type to HTML
header('Content-Type: text/html; charset=UTF-8');

// Build customer address
$customerAddress = $order['ShipAddress'];
if (empty($customerAddress) && !empty($order['CustomerAddress'])) {
    $customerAddress = $order['CustomerAddress'];
    if (!empty($order['CustomerLandmark'])) $customerAddress .= ", " . $order['CustomerLandmark'];
    if (!empty($order['CustomerCity'])) $customerAddress .= ", " . $order['CustomerCity'];
    if (!empty($order['CustomerState'])) $customerAddress .= ", " . $order['CustomerState'];
    if (!empty($order['CustomerPincode'])) $customerAddress .= " - " . $order['CustomerPincode'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo htmlspecialchars($order['OrderId']); ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .print-container { box-shadow: none; margin: 0; }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .header {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            color: #2c5aa0;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .company-info {
            font-size: 12px;
            text-align: center;
            margin-bottom: 30px;
            color: #666;
            line-height: 1.6;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            background: linear-gradient(135deg, #2c5aa0, #3d6bb3);
            color: white;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 30px;
        }

        .info-box {
            flex: 1;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #2c5aa0;
        }

        .info-box h3 {
            margin: 0 0 15px 0;
            color: #2c5aa0;
            font-size: 16px;
        }

        .info-box p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .items-table th {
            background: linear-gradient(135deg, #2c5aa0, #3d6bb3);
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .items-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .total-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 25px;
            border-radius: 5px;
            border: 2px solid #2c5aa0;
        }

        .total-table {
            width: 100%;
            margin-left: auto;
            max-width: 300px;
        }

        .total-table td {
            padding: 8px 0;
            font-size: 14px;
        }

        .total-row {
            font-weight: bold;
            font-size: 18px;
            color: #2c5aa0;
            border-top: 2px solid #2c5aa0;
            padding-top: 10px;
        }

        .thank-you {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #2c5aa0;
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #666;
            line-height: 1.6;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .print-btn {
            background: #2c5aa0;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 20px auto;
            display: block;
            transition: background 0.3s;
        }

        .print-btn:hover {
            background: #1e3f73;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="header">MY NUTRIFY</div>
        <div class="company-info">
            55 North Shivaji Nagar, Near Apta Police Chowk<br>
            Sangli - 416416, Maharashtra, India<br>
            Phone: +91 9834243754 | Email: support@mynutrify.com<br>
            Website: www.mynutrify.com
        </div>

        <div class="invoice-title">INVOICE</div>

        <div class="info-section">
            <div class="info-box">
                <h3>Bill To:</h3>
                <p><strong><?php echo htmlspecialchars($order['CustomerName']); ?></strong></p>
                <p><?php echo htmlspecialchars($customerAddress); ?></p>
                <p>Phone: <?php echo htmlspecialchars($order['CustomerPhone']); ?></p>
                <p>Email: <?php echo htmlspecialchars($order['CustomerEmail']); ?></p>
            </div>
            <div class="info-box">
                <h3>Invoice Details:</h3>
                <p><strong>Invoice No:</strong> <?php echo htmlspecialchars($order['OrderId']); ?></p>
                <p><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($order['CreatedAt'])); ?></p>
                <p><strong>Payment Type:</strong> <?php echo htmlspecialchars($order['PaymentType']); ?></p>
                <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($order['PaymentStatus']); ?></p>
                <p><strong>AWB/Waybill:</strong> <?php echo !empty($order['Waybill']) ? htmlspecialchars($order['Waybill']) : 'Not Assigned'; ?></p>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="8%">S.No</th>
                    <th width="40%">Product Name</th>
                    <th width="15%">Product Code</th>
                    <th width="10%">Qty</th>
                    <th width="13.5%">Unit Price</th>
                    <th width="13.5%">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalAmount = 0;
                $serialNo = 1;
                foreach ($orderItems as $item):
                    $itemTotal = $item['Quantity'] * $item['Price'];
                    $totalAmount += $itemTotal;
                ?>
                <tr>
                    <td><?php echo $serialNo++; ?></td>
                    <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($item['ProductCode']); ?></td>
                    <td><?php echo $item['Quantity']; ?></td>
                    <td>₹<?php echo number_format($item['Price'], 2); ?></td>
                    <td>₹<?php echo number_format($itemTotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td style="text-align: right;"><strong>₹<?php echo number_format($totalAmount, 2); ?></strong></td>
                </tr>
                <tr>
                    <td><strong>Shipping:</strong></td>
                    <td style="text-align: right;"><strong>Free</strong></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Amount:</strong></td>
                    <td style="text-align: right;"><strong>₹<?php echo number_format($order['Amount'], 2); ?></strong></td>
                </tr>
            </table>
        </div>

        <div class="thank-you">Thank you for choosing My Nutrify!</div>

        <div class="footer">
            This is a computer-generated invoice. No signature required.<br>
            For any queries, please contact us at support@mynutrify.com or call +91 9834243754
        </div>

        <button class="print-btn no-print" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <script>
        // Auto-focus for better printing experience
        window.onload = function() {
            document.title = 'Invoice - <?php echo htmlspecialchars($order['OrderId']); ?>';
        };
    </script>
</body>
</html>



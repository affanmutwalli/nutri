<?php
$selected = "approve_orders.php";

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

// Handle delivery provider selection
if (isset($_POST['select_delivery'])) {
    $orderId = $_POST['order_id'];
    $selectedProvider = $_POST['delivery_provider'];

    // Store selection in session and redirect to show costs
    $_SESSION['selected_order'] = $orderId;
    $_SESSION['selected_provider'] = $selectedProvider;
    header("Location: approve_orders.php?show_costs=" . $orderId);
    exit();
}

// Handle order approval action
if (isset($_POST['approve_order'])) {
    $orderId = $_POST['order_id'];
    $deliveryProvider = $_POST['delivery_provider'] ?? 'delhivery';
    
    try {
        // Get order details
        $query = "SELECT om.OrderId, om.CustomerId, om.CustomerType, om.Amount, om.ShipAddress, om.OrderStatus, om.PaymentType,
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
        
        if ($order = mysqli_fetch_assoc($result)) {
            // Process based on selected delivery provider
            if ($deliveryProvider == 'delhivery') {
                require_once '../includes/DeliveryManager.php';
                $deliveryManager = new DeliveryManager($mysqli);

                if ($deliveryManager->isDelhiveryConfigured()) {
                // Prepare shipping address
                $shippingAddress = $order['ShipAddress'];
                if (empty($shippingAddress) && !empty($order['CustomerAddress'])) {
                    $shippingAddress = $order['CustomerAddress'];
                    if (!empty($order['CustomerLandmark'])) $shippingAddress .= ", " . $order['CustomerLandmark'];
                    if (!empty($order['CustomerCity'])) $shippingAddress .= ", " . $order['CustomerCity'];
                    if (!empty($order['CustomerState'])) $shippingAddress .= ", " . $order['CustomerState'];
                    if (!empty($order['CustomerPincode'])) $shippingAddress .= " - " . $order['CustomerPincode'];
                }
                
                $customerPhone = $order['CustomerPhone'];
                $totalAmount = floatval($order['Amount']);
                
                // Prepare order data for Delhivery
                $orderData = [
                    'order_id' => $order['OrderId'],
                    'customer_name' => $order['CustomerName'] ?? 'Customer',
                    'customer_phone' => $customerPhone,
                    'shipping_address' => $shippingAddress,
                    'total_amount' => $totalAmount,
                    'payment_mode' => ($order['PaymentType'] == 'COD') ? 'COD' : 'Prepaid',
                    'weight' => 0.5,
                    'products' => [['name' => 'Product', 'quantity' => 1]],
                    'order_date' => date('Y-m-d H:i:s')
                ];
                
                // Create shipment with Delhivery
                $shipmentResult = $deliveryManager->createOrder($orderData);
                
                if ($shipmentResult && isset($shipmentResult['waybill'])) {
                    $waybill = $shipmentResult['waybill'];
                    $trackingUrl = "https://www.delhivery.com/track/package/$waybill";
                    
                    // Update order with waybill
                    $shipQuery = "UPDATE order_master SET
                                 Waybill = ?,
                                 OrderStatus = 'Shipped',
                                 delivery_status = 'shipped',
                                 delivery_provider = 'delhivery',
                                 tracking_url = ?
                                 WHERE OrderId = ?";
                    
                    $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                    mysqli_stmt_bind_param($shipStmt, "sss", $waybill, $trackingUrl, $order['OrderId']);

                    if (mysqli_stmt_execute($shipStmt)) {
                        $success_message = "Order {$order['OrderId']} approved and shipped via Delhivery! Waybill: $waybill";

                        // Send SMS notification to admin
                        try {
                            include_once '../sms_api/sms_order_hooks.php';
                            sendAdminOrderShippedSMS($order['OrderId'], $waybill);
                        } catch (Exception $e) {
                            error_log("Admin SMS notification failed: " . $e->getMessage());
                        }
                    } else {
                        $error_message = "Failed to update order status in database";
                    }
                } else {
                    $error_message = "Failed to create shipment with Delhivery: " . ($shipmentResult['message'] ?? 'Unknown error');
                }
                } else {
                    $error_message = "Delhivery not configured";
                }
            } elseif ($deliveryProvider == 'shiprocket') {
                // ShipRocket integration (placeholder)
                $waybill = 'SR' . time() . rand(1000, 9999);
                $trackingUrl = "https://shiprocket.co/tracking/$waybill";

                $shipQuery = "UPDATE order_master SET
                             Waybill = ?,
                             OrderStatus = 'Shipped',
                             delivery_status = 'shipped',
                             delivery_provider = 'shiprocket',
                             tracking_url = ?
                             WHERE OrderId = ?";

                $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                mysqli_stmt_bind_param($shipStmt, "sss", $waybill, $trackingUrl, $order['OrderId']);

                if (mysqli_stmt_execute($shipStmt)) {
                    $success_message = "Order {$order['OrderId']} approved and shipped via ShipRocket! Waybill: $waybill";
                } else {
                    $error_message = "Failed to update order status in database";
                }
            } elseif ($deliveryProvider == 'rapidshyp') {
                // RapidShyp integration (placeholder)
                $waybill = 'RS' . time() . rand(1000, 9999);
                $trackingUrl = "https://rapidshyp.com/track/$waybill";

                $shipQuery = "UPDATE order_master SET
                             Waybill = ?,
                             OrderStatus = 'Shipped',
                             delivery_status = 'shipped',
                             delivery_provider = 'rapidshyp',
                             tracking_url = ?
                             WHERE OrderId = ?";

                $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                mysqli_stmt_bind_param($shipStmt, "sss", $waybill, $trackingUrl, $order['OrderId']);

                if (mysqli_stmt_execute($shipStmt)) {
                    $success_message = "Order {$order['OrderId']} approved and shipped via RapidShyp! Waybill: $waybill";
                } else {
                    $error_message = "Failed to update order status in database";
                }
            } else {
                // Default: just confirm the order
                $confirmQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                $confirmStmt = mysqli_prepare($mysqli, $confirmQuery);
                mysqli_stmt_bind_param($confirmStmt, "s", $orderId);

                if (mysqli_stmt_execute($confirmStmt)) {
                    $success_message = "Order {$orderId} approved and marked as confirmed";
                } else {
                    $error_message = "Failed to confirm order";
                }
            }
        } else {
            $error_message = "Order not found";
        }
    } catch (Exception $e) {
        $error_message = "Error processing order: " . $e->getMessage();
    }
}

// Function to get delivery costs for different providers
function getDeliveryCosts($orderData) {
    $costs = [];

    // Delhivery costs (approximate)
    $weight = 0.5; // kg
    $distance = 'metro'; // assume metro delivery
    $costs['delhivery'] = [
        'base_cost' => 45,
        'weight_cost' => $weight * 10,
        'fuel_surcharge' => 8,
        'total' => 63
    ];

    // ShipRocket costs (approximate)
    $costs['shiprocket'] = [
        'base_cost' => 40,
        'weight_cost' => $weight * 12,
        'fuel_surcharge' => 6,
        'total' => 52
    ];

    // RapidShyp costs (approximate)
    $costs['rapidshyp'] = [
        'base_cost' => 38,
        'weight_cost' => $weight * 11,
        'fuel_surcharge' => 7,
        'total' => 50.5
    ];

    return $costs;
}

// Check if showing delivery costs for a specific order
$showCosts = isset($_GET['show_costs']) ? $_GET['show_costs'] : null;
$costOrderData = null;
if ($showCosts) {
    // Get order details for cost calculation
    $costQuery = "SELECT om.OrderId, om.Amount, om.ShipAddress,
                        COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName,
                        COALESCE(cm.MobileNo, dc.MobileNo, '') as CustomerPhone
                 FROM order_master om
                 LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
                 LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
                 WHERE om.OrderId = ?";

    $costStmt = mysqli_prepare($mysqli, $costQuery);
    mysqli_stmt_bind_param($costStmt, "s", $showCosts);
    mysqli_stmt_execute($costStmt);
    $costResult = mysqli_stmt_get_result($costStmt);
    $costOrderData = mysqli_fetch_assoc($costResult);
}

// Debug: Check what order statuses exist and recent orders
$debugQuery = "SELECT OrderId, OrderStatus, PaymentStatus, Waybill, CreatedAt FROM order_master ORDER BY CreatedAt DESC LIMIT 10";
$debugResult = mysqli_query($mysqli, $debugQuery);
$debug_info = "Recent orders: ";
if ($debugResult) {
    while ($row = mysqli_fetch_assoc($debugResult)) {
        $debug_info .= $row['OrderId'] . "(" . $row['OrderStatus'] . "), ";
    }
}

// Get orders that need approval - same logic as process_single_order.php
$pendingQuery = "SELECT om.OrderId, om.CustomerId, om.CustomerType, om.Amount, om.OrderStatus, om.PaymentType, om.PaymentStatus, om.CreatedAt,
                       COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName,
                       COALESCE(cm.MobileNo, dc.MobileNo, '') as CustomerPhone
                FROM order_master om
                LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
                LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
                WHERE (om.Waybill IS NULL OR om.Waybill = '' OR om.Waybill = 'NULL')
                AND om.OrderStatus NOT IN ('Cancelled', 'Refunded', 'Shipped', 'Delivered')
                ORDER BY om.CreatedAt DESC";

$pendingResult = mysqli_query($mysqli, $pendingQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approve Orders | OMS</title>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include 'components/sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Approve Orders</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Approve Orders</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($showCosts && $costOrderData): ?>
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shipping-fast"></i> Choose Delivery Provider for Order: <?php echo $costOrderData['OrderId']; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <strong>Order Details:</strong> <?php echo $costOrderData['CustomerName']; ?> - ₹<?php echo number_format($costOrderData['Amount'], 2); ?>
                                </div>
                            </div>

                            <?php $costs = getDeliveryCosts($costOrderData); ?>
                            <div class="row">
                                <?php foreach ($costs as $provider => $cost): ?>
                                    <div class="col-md-4">
                                        <div class="card <?php echo $provider == 'rapidshyp' ? 'border-success' : ($provider == 'shiprocket' ? 'border-info' : 'border-warning'); ?>">
                                            <div class="card-header text-center">
                                                <h5 class="mb-0">
                                                    <?php echo ucfirst($provider == 'rapidshyp' ? 'RapidShyp' : ($provider == 'shiprocket' ? 'ShipRocket' : 'Delhivery')); ?>
                                                    <?php if ($provider == 'rapidshyp'): ?>
                                                        <span class="badge badge-success">Cheapest</span>
                                                    <?php endif; ?>
                                                </h5>
                                            </div>
                                            <div class="card-body text-center">
                                                <div class="mb-2">
                                                    <small class="text-muted">Base Cost: ₹<?php echo $cost['base_cost']; ?></small><br>
                                                    <small class="text-muted">Weight: ₹<?php echo $cost['weight_cost']; ?></small><br>
                                                    <small class="text-muted">Fuel: ₹<?php echo $cost['fuel_surcharge']; ?></small>
                                                </div>
                                                <h4 class="text-primary">₹<?php echo number_format($cost['total'], 2); ?></h4>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="order_id" value="<?php echo $costOrderData['OrderId']; ?>">
                                                    <input type="hidden" name="delivery_provider" value="<?php echo $provider; ?>">
                                                    <button type="submit" name="approve_order" class="btn btn-<?php echo $provider == 'rapidshyp' ? 'success' : ($provider == 'shiprocket' ? 'info' : 'warning'); ?> btn-sm">
                                                        <i class="fas fa-check"></i> Select & Ship
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    <a href="approve_orders.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Orders
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-check"></i> Orders Pending Approval
                        </h3>
                        <?php if (!empty($debug_info)): ?>
                            <div class="card-tools">
                                <small class="text-muted">Available statuses: <?php echo rtrim($debug_info, ', '); ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($pendingResult) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Phone</th>
                                            <th>Amount</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = mysqli_fetch_assoc($pendingResult)): ?>
                                            <tr>
                                                <td><?php echo $order['OrderId']; ?></td>
                                                <td><?php echo $order['CustomerName']; ?></td>
                                                <td><?php echo $order['CustomerPhone']; ?></td>
                                                <td>₹<?php echo number_format($order['Amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $order['PaymentType'] == 'COD' ? 'warning' : 'success'; ?>">
                                                        <?php echo $order['PaymentType']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning"><?php echo $order['OrderStatus']; ?></span>
                                                </td>
                                                <td><?php echo date('d-m-Y H:i', strtotime($order['CreatedAt'])); ?></td>
                                                <td>
                                                    <a href="approve_orders.php?show_costs=<?php echo $order['OrderId']; ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-shipping-fast"></i> Choose Delivery
                                                    </a>
                                                    <a href="order_details.php?OrderId=<?php echo $order['OrderId']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No Orders Pending Approval</h4>
                                <p class="text-muted">All orders have been processed or there are no new orders.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include 'components/footer.php'; ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

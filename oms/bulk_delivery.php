<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

// Set $conn for compatibility with DeliveryManager
$conn = $mysqli;

require_once '../includes/DeliveryManager.php';

$selected = "bulk_delivery.php";
$page = "bulk_delivery.php";

$message = '';
$messageType = '';

// Handle bulk operations
if ($_POST && isset($_POST['action'])) {
    try {
        $deliveryManager = new DeliveryManager($conn);
        
        switch ($_POST['action']) {
            case 'bulk_create':
                $orderIds = array_filter(array_map('trim', explode(',', $_POST['order_ids'])));
                $successCount = 0;
                $errors = [];
                
                foreach ($orderIds as $orderId) {
                    try {
                        // Get order details with customer information
                        $orderQuery = "SELECT om.*,
                                              COALESCE(cm.Name, dc.CustomerName, 'Customer') as CustomerName,
                                              COALESCE(cm.MobileNo, dc.MobileNo, '') as MobileNo
                                       FROM order_master om
                                       LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
                                       LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
                                       WHERE om.OrderId = ?";
                        $stmt = $mysqli->prepare($orderQuery);
                        $stmt->bind_param("s", $orderId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $order = $result->fetch_assoc();

                        if ($order) {
                            // Create shipment
                            $orderData = [
                                'order_id' => $orderId,
                                'customer_name' => $order['CustomerName'],
                                'phone' => $order['MobileNo'],
                                'address' => $order['ShipAddress'] ?? '',
                                'amount' => $order['Amount'],
                                'payment_mode' => $order['PaymentType']
                            ];
                            
                            $result = $deliveryManager->createOrder($orderData);
                            $successCount++;
                        }
                    } catch (Exception $e) {
                        $errors[] = "Order $orderId: " . $e->getMessage();
                    }
                }
                
                $message = "Bulk creation completed: $successCount orders processed successfully.";
                if (!empty($errors)) {
                    $message .= " Errors: " . implode(', ', array_slice($errors, 0, 3));
                }
                $messageType = $successCount > 0 ? 'success' : 'danger';
                break;
                
            case 'bulk_track':
                $trackingIds = array_filter(array_map('trim', explode(',', $_POST['tracking_ids'])));
                $trackingResults = [];
                
                foreach ($trackingIds as $trackingId) {
                    try {
                        $result = $deliveryManager->trackShipment($trackingId);
                        $trackingResults[$trackingId] = $result;
                    } catch (Exception $e) {
                        $trackingResults[$trackingId] = ['error' => $e->getMessage()];
                    }
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get pending orders for bulk processing
$pendingOrdersQuery = "SELECT om.OrderId, om.OrderDate, om.Amount, om.OrderStatus,
                              COALESCE(cm.Name, dc.CustomerName, 'Unknown') as CustomerName
                       FROM order_master om
                       LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType = 'Registered'
                       LEFT JOIN direct_customers dc ON om.CustomerId = dc.CustomerId AND om.CustomerType = 'Direct'
                       WHERE om.OrderStatus IN ('Confirmed', 'Pending', 'Placed')
                       ORDER BY om.OrderDate DESC
                       LIMIT 50";
$pendingOrdersResult = mysqli_query($conn, $pendingOrdersQuery);
$pendingOrders = [];
if ($pendingOrdersResult) {
    while ($row = mysqli_fetch_assoc($pendingOrdersResult)) {
        $pendingOrders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS | Bulk Delivery Processing</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div id="loading"></div>
    <div class="wrapper">
        <?php include('components/sidebar.php');?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Bulk Delivery Processing</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Bulk Delivery</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Bulk Order Creation -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Bulk Order Creation</h3>
                                </div>
                                <form method="POST">
                                    <div class="card-body">
                                        <input type="hidden" name="action" value="bulk_create">
                                        <div class="form-group">
                                            <label for="order_ids">Order IDs (comma-separated)</label>
                                            <textarea class="form-control" id="order_ids" name="order_ids" rows="4" 
                                                      placeholder="MN000001, MN000002, MN000003" required></textarea>
                                            <small class="form-text text-muted">Enter order IDs separated by commas</small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-shipping-fast"></i> Create Bulk Shipments
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Bulk Tracking</h3>
                                </div>
                                <form method="POST">
                                    <div class="card-body">
                                        <input type="hidden" name="action" value="bulk_track">
                                        <div class="form-group">
                                            <label for="tracking_ids">Tracking IDs / Waybills (comma-separated)</label>
                                            <textarea class="form-control" id="tracking_ids" name="tracking_ids" rows="4" 
                                                      placeholder="1234567890, 0987654321" required></textarea>
                                            <small class="form-text text-muted">Enter tracking IDs or waybill numbers separated by commas</small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-search"></i> Track Multiple Orders
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Orders -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Pending Orders (Ready for Shipment)</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($pendingOrders)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="select-all"></th>
                                                    <th>Order ID</th>
                                                    <th>Customer</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pendingOrders as $order): ?>
                                                <tr>
                                                    <td><input type="checkbox" class="order-checkbox" value="<?php echo $order['OrderId']; ?>"></td>
                                                    <td><?php echo htmlspecialchars($order['OrderId']); ?></td>
                                                    <td><?php echo htmlspecialchars($order['CustomerName'] ?? 'N/A'); ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($order['OrderDate'])); ?></td>
                                                    <td>₹<?php echo number_format($order['Amount'], 2); ?></td>
                                                    <td><span class="badge badge-warning"><?php echo $order['OrderStatus']; ?></span></td>
                                                    <td>
                                                        <a href="order_details.php?OrderId=<?php echo $order['OrderId']; ?>" 
                                                           class="btn btn-sm btn-info">View</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" onclick="createSelectedOrders()">
                                            <i class="fas fa-shipping-fast"></i> Create Shipments for Selected
                                        </button>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No pending orders found.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Results -->
                    <?php if (isset($trackingResults) && !empty($trackingResults)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Bulk Tracking Results</h3>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($trackingResults as $trackingId => $result): ?>
                                    <div class="mb-3 p-3 border rounded">
                                        <h5>Tracking ID: <?php echo htmlspecialchars($trackingId); ?></h5>
                                        <?php if (isset($result['error'])): ?>
                                            <div class="alert alert-danger">Error: <?php echo htmlspecialchars($result['error']); ?></div>
                                        <?php else: ?>
                                            <p><strong>Status:</strong> <?php echo htmlspecialchars($result['status'] ?? 'Unknown'); ?></p>
                                            <p><strong>Location:</strong> <?php echo htmlspecialchars($result['location'] ?? 'N/A'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </section>
        </div>

        <?php include("components/footer.php"); ?>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <script>
    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Create shipments for selected orders
    function createSelectedOrders() {
        const selectedOrders = [];
        document.querySelectorAll('.order-checkbox:checked').forEach(checkbox => {
            selectedOrders.push(checkbox.value);
        });

        if (selectedOrders.length === 0) {
            alert('Please select at least one order.');
            return;
        }

        document.getElementById('order_ids').value = selectedOrders.join(', ');
        document.querySelector('input[name="action"][value="bulk_create"]').closest('form').submit();
    }
    </script>
</body>
</html>

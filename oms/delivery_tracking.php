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

$selected = "delivery_tracking.php";
$trackingResult = null;
$message = '';
$messageType = '';

// Handle tracking request
if ($_POST && isset($_POST['track'])) {
    $trackingId = trim($_POST['tracking_id']);
    $provider = $_POST['provider'] ?? 'auto';

    if (!empty($trackingId)) {
        try {
            // First check if this tracking ID exists in our database
            $checkQuery = "SELECT OrderId, OrderStatus, Waybill FROM order_master WHERE Waybill = ? OR OrderId = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("ss", $trackingId, $trackingId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $orderData = $checkResult->fetch_assoc();

            if ($orderData) {
                $deliveryManager = new DeliveryManager($conn);

                // Only track if we have a valid waybill
                if (!empty($orderData['Waybill']) && $orderData['Waybill'] !== 'NULL') {
                    $trackingResult = $deliveryManager->trackShipment($orderData['Waybill']);
                    $trackingResult['provider_used'] = 'delhivery';
                    $trackingResult['order_id'] = $orderData['OrderId'];
                } else {
                    // Order exists but no waybill yet
                    $trackingResult = [
                        'status' => 'Order placed, awaiting shipment',
                        'order_id' => $orderData['OrderId'],
                        'order_status' => $orderData['OrderStatus'],
                        'message' => 'Order has been placed but not yet shipped. Waybill number will be assigned once the order is processed.',
                        'provider_used' => 'delhivery'
                    ];
                }
            } else {
                $message = 'Tracking ID not found in our system. Please check the tracking number.';
                $messageType = 'warning';
            }

        } catch (Exception $e) {
            $message = 'Error tracking shipment: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } else {
        $message = 'Please enter a tracking ID.';
        $messageType = 'warning';
    }
}

// Get recent orders for quick tracking
$recentOrdersSql = "SELECT OrderId, OrderDate, OrderStatus, Amount as TotalAmount
                    FROM order_master
                    WHERE OrderStatus IN ('Confirmed', 'Shipped', 'In Transit', 'Out for Delivery')
                    ORDER BY OrderDate DESC
                    LIMIT 10";
$recentOrdersResult = mysqli_query($conn, $recentOrdersSql);
$recentOrders = [];
if ($recentOrdersResult) {
    while ($row = mysqli_fetch_assoc($recentOrdersResult)) {
        $recentOrders[] = $row;
    }
}

// Initialize delivery manager for provider list
try {
    $deliveryManager = new DeliveryManager($conn);
    $availableProviders = $deliveryManager->getAvailableProviders();
} catch (Exception $e) {
    $availableProviders = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Enhanced Tracking | OMS</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    .tracking-timeline {
      position: relative;
      padding-left: 30px;
    }
    .tracking-timeline::before {
      content: '';
      position: absolute;
      left: 15px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: #dee2e6;
    }
    .tracking-step {
      position: relative;
      margin-bottom: 20px;
    }
    .tracking-step::before {
      content: '';
      position: absolute;
      left: -22px;
      top: 5px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #6c757d;
    }
    .tracking-step.completed::before {
      background: #28a745;
    }
    .tracking-step.current::before {
      background: #007bff;
      box-shadow: 0 0 0 3px rgba(0,123,255,0.3);
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include("components/sidebar.php"); ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Enhanced Order Tracking</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Enhanced Tracking</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Tracking Form -->
        <div class="row">
          <div class="col-md-8">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Track Your Shipment</h3>
              </div>
              <form method="POST">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="tracking_id">Tracking ID / AWB Number / Order ID</label>
                        <input type="text" class="form-control" id="tracking_id" name="tracking_id"
                               value="<?php echo htmlspecialchars($_POST['tracking_id'] ?? ''); ?>"
                               placeholder="Enter tracking ID, AWB number, or Order ID" required>
                        <small class="form-text text-muted">Using Delhivery delivery service</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" name="track" class="btn btn-primary">
                    <i class="fas fa-search"></i> Track Shipment
                  </button>
                </div>
              </form>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
              </div>
              <div class="card-body">
                <?php if (!empty($recentOrders)): ?>
                <div class="list-group">
                  <?php foreach ($recentOrders as $order): ?>
                  <a href="#" class="list-group-item list-group-item-action" 
                     onclick="document.getElementById('tracking_id').value='<?php echo $order['OrderId']; ?>'">
                    <div class="d-flex w-100 justify-content-between">
                      <h6 class="mb-1"><?php echo htmlspecialchars($order['OrderId']); ?></h6>
                      <small><?php echo date('M d', strtotime($order['OrderDate'])); ?></small>
                    </div>
                    <p class="mb-1"><?php echo htmlspecialchars($order['CustomerName']); ?></p>
                    <small>₹<?php echo number_format($order['TotalAmount'], 2); ?> - <?php echo ucfirst($order['OrderStatus']); ?></small>
                  </a>
                  <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted">No recent orders found.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Tracking Results -->
        <?php if ($trackingResult): ?>
        <div class="row">
          <div class="col-12">
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-map-marker-alt"></i> Tracking Results 
                  <span class="badge badge-info ml-2"><?php echo ucfirst($trackingResult['provider_used']); ?></span>
                </h3>
              </div>
              <div class="card-body">
                <?php if (isset($trackingResult['ShipmentData']) && !empty($trackingResult['ShipmentData'])): ?>
                <!-- Delhivery Format -->
                <?php 
                $shipment = $trackingResult['ShipmentData'][0]['Shipment'] ?? null;
                if ($shipment):
                ?>
                <div class="row">
                  <div class="col-md-6">
                    <h5>Shipment Information</h5>
                    <table class="table table-sm">
                      <tr><td><strong>AWB:</strong></td><td><?php echo htmlspecialchars($shipment['AWB'] ?? 'N/A'); ?></td></tr>
                      <tr><td><strong>Order ID:</strong></td><td><?php echo htmlspecialchars($shipment['ReferenceNo'] ?? 'N/A'); ?></td></tr>
                      <tr><td><strong>Status:</strong></td><td>
                        <span class="badge badge-<?php echo ($shipment['Status'] ?? '') === 'Delivered' ? 'success' : 'primary'; ?>">
                          <?php echo htmlspecialchars($shipment['Status'] ?? 'Unknown'); ?>
                        </span>
                      </td></tr>
                      <tr><td><strong>Destination:</strong></td><td><?php echo htmlspecialchars($shipment['Destination'] ?? 'N/A'); ?></td></tr>
                      <tr><td><strong>Origin:</strong></td><td><?php echo htmlspecialchars($shipment['Origin'] ?? 'N/A'); ?></td></tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <h5>Tracking Timeline</h5>
                    <div class="tracking-timeline">
                      <?php if (isset($shipment['Scans']) && is_array($shipment['Scans'])): ?>
                      <?php foreach (array_reverse($shipment['Scans']) as $index => $scan): ?>
                      <div class="tracking-step <?php echo $index === 0 ? 'current' : 'completed'; ?>">
                        <div class="d-flex justify-content-between">
                          <strong><?php echo htmlspecialchars($scan['ScanDetail'] ?? 'Status Update'); ?></strong>
                          <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($scan['ScanDateTime'] ?? '')); ?></small>
                        </div>
                        <div class="text-muted"><?php echo htmlspecialchars($scan['ScannedLocation'] ?? ''); ?></div>
                      </div>
                      <?php endforeach; ?>
                      <?php else: ?>
                      <p class="text-muted">No tracking details available.</p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
                
                <?php elseif (isset($trackingResult['tracking_data'])): ?>
                <!-- Shiprocket Format -->
                <?php $tracking = $trackingResult['tracking_data']; ?>
                <div class="row">
                  <div class="col-md-6">
                    <h5>Shipment Information</h5>
                    <table class="table table-sm">
                      <tr><td><strong>AWB:</strong></td><td><?php echo htmlspecialchars($tracking['awb_code'] ?? 'N/A'); ?></td></tr>
                      <tr><td><strong>Order ID:</strong></td><td><?php echo htmlspecialchars($tracking['order_id'] ?? 'N/A'); ?></td></tr>
                      <tr><td><strong>Status:</strong></td><td>
                        <span class="badge badge-<?php echo ($tracking['track_status'] ?? '') === 'Delivered' ? 'success' : 'primary'; ?>">
                          <?php echo htmlspecialchars($tracking['track_status'] ?? 'Unknown'); ?>
                        </span>
                      </td></tr>
                      <tr><td><strong>Courier:</strong></td><td><?php echo htmlspecialchars($tracking['courier_name'] ?? 'N/A'); ?></td></tr>
                      <tr><td><strong>Expected Delivery:</strong></td><td><?php echo htmlspecialchars($tracking['edd'] ?? 'N/A'); ?></td></tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <h5>Tracking Timeline</h5>
                    <div class="tracking-timeline">
                      <?php if (isset($tracking['shipment_track']) && is_array($tracking['shipment_track'])): ?>
                      <?php foreach (array_reverse($tracking['shipment_track']) as $index => $track): ?>
                      <div class="tracking-step <?php echo $index === 0 ? 'current' : 'completed'; ?>">
                        <div class="d-flex justify-content-between">
                          <strong><?php echo htmlspecialchars($track['current_status'] ?? 'Status Update'); ?></strong>
                          <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($track['date'] ?? '')); ?></small>
                        </div>
                        <div class="text-muted"><?php echo htmlspecialchars($track['location'] ?? ''); ?></div>
                      </div>
                      <?php endforeach; ?>
                      <?php else: ?>
                      <p class="text-muted">No tracking details available.</p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                  <h5>Raw Tracking Data</h5>
                  <pre><?php echo htmlspecialchars(json_encode($trackingResult, JSON_PRETTY_PRINT)); ?></pre>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="row">
          <div class="col-12">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <a href="all_orders.php" class="btn btn-block btn-primary">
                      <i class="fas fa-list"></i><br>
                      View All Orders
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="delivery_providers.php" class="btn btn-block btn-info">
                      <i class="fas fa-cog"></i><br>
                      Manage Providers
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="bulk_delivery.php" class="btn btn-block btn-success">
                      <i class="fas fa-shipping-fast"></i><br>
                      Bulk Processing
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="delivery_settings.php" class="btn btn-block btn-warning">
                      <i class="fas fa-wrench"></i><br>
                      Settings
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include("components/footer.php"); ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

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

$selected = "delivery_providers.php";
$message = '';
$messageType = '';

// Handle provider actions
if ($_POST) {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'test_connection':
                    $deliveryManager = new DeliveryManager($conn);
                    $validationResult = $deliveryManager->validateConfiguration();

                    if ($validationResult['delhivery']['status'] === 'success') {
                        $message = "Delhivery connection test successful!";
                        $messageType = 'success';
                    } else {
                        $message = "Delhivery connection test failed: " . $validationResult['delhivery']['message'];
                        $messageType = 'danger';
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Initialize delivery manager
try {
    $deliveryManager = new DeliveryManager($conn);
    $isDelhiveryConfigured = $deliveryManager->isDelhiveryConfigured();
} catch (Exception $e) {
    $isDelhiveryConfigured = false;
    if (empty($message)) {
        $message = 'Error initializing delivery manager: ' . $e->getMessage();
        $messageType = 'warning';
    }
}

// Get current settings
$sql = "SELECT config_key, config_value FROM shipping_config";
$result = mysqli_query($conn, $sql);
$config = [];
while ($row = mysqli_fetch_assoc($result)) {
    $config[$row['config_key']] = $row['config_value'];
}

// Delhivery configuration
$delhiveryConfig = [
    'api_key' => $config['delhivery_api_key'] ?? '',
    'client_name' => $config['delhivery_client_name'] ?? '',
    'return_address' => $config['delhivery_return_address'] ?? '',
    'return_city' => $config['delhivery_return_city'] ?? '',
    'return_state' => $config['delhivery_return_state'] ?? '',
    'return_pincode' => $config['delhivery_return_pincode'] ?? '',
    'seller_name' => $config['delhivery_seller_name'] ?? '',
    'seller_gst' => $config['delhivery_seller_gst'] ?? ''
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Delivery Providers | OMS</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include("components/sidebar.php"); ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Delivery Provider Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Delivery Providers</li>
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

        <!-- Delhivery Status Overview -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Delhivery Provider Status</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8">
                    <div class="card card-primary">
                      <div class="card-header">
                        <h3 class="card-title">
                          <i class="fas fa-truck"></i> Delhivery
                          <span class="badge badge-info ml-2">Primary Provider</span>
                        </h3>
                      </div>
                      <div class="card-body">
                        <p>India's largest logistics company providing express delivery, surface transport, and hyperlocal delivery services.</p>

                        <div class="row">
                          <div class="col-6">
                            <strong>Status:</strong>
                            <?php if ($isDelhiveryConfigured): ?>
                            <span class="badge badge-success">Connected</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Not Configured</span>
                            <?php endif; ?>
                          </div>
                          <div class="col-6">
                            <strong>Configuration:</strong>
                            <?php if (!empty($delhiveryConfig['api_key'])): ?>
                            <span class="badge badge-success">Complete</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Incomplete</span>
                            <?php endif; ?>
                          </div>
                        </div>

                        <hr>
                        <strong>Features:</strong>
                        <ul class="list-unstyled">
                          <li><i class="fas fa-check text-success"></i> Express delivery</li>
                          <li><i class="fas fa-check text-success"></i> Surface transport</li>
                          <li><i class="fas fa-check text-success"></i> Real-time tracking</li>
                          <li><i class="fas fa-check text-success"></i> COD support</li>
                          <li><i class="fas fa-check text-success"></i> Return management</li>
                          <li><i class="fas fa-check text-success"></i> API integration</li>
                        </ul>

                        <div class="btn-group" role="group">
                          <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="test_connection">
                            <button type="submit" class="btn btn-sm btn-info"
                                    <?php echo !$isDelhiveryConfigured ? 'disabled' : ''; ?>>
                              <i class="fas fa-plug"></i> Test Connection
                            </button>
                          </form>

                          <a href="delivery_settings.php" class="btn btn-sm btn-warning">
                            <i class="fas fa-cog"></i> Configure
                          </a>

                          <a href="delivery_tracking.php" class="btn btn-sm btn-success">
                            <i class="fas fa-search"></i> Track Orders
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="card card-info">
                      <div class="card-header">
                        <h3 class="card-title">Quick Stats</h3>
                      </div>
                      <div class="card-body">
                        <div class="info-box">
                          <span class="info-box-icon bg-info"><i class="fas fa-shipping-fast"></i></span>
                          <div class="info-box-content">
                            <span class="info-box-text">Provider</span>
                            <span class="info-box-number">Delhivery</span>
                          </div>
                        </div>

                        <div class="info-box">
                          <span class="info-box-icon bg-<?php echo $isDelhiveryConfigured ? 'success' : 'danger'; ?>">
                            <i class="fas fa-<?php echo $isDelhiveryConfigured ? 'check' : 'times'; ?>"></i>
                          </span>
                          <div class="info-box-content">
                            <span class="info-box-text">Status</span>
                            <span class="info-box-number"><?php echo $isDelhiveryConfigured ? 'Ready' : 'Setup Required'; ?></span>
                          </div>
                        </div>

                        <div class="info-box">
                          <span class="info-box-icon bg-warning"><i class="fas fa-globe"></i></span>
                          <div class="info-box-content">
                            <span class="info-box-text">Coverage</span>
                            <span class="info-box-number">Pan-India</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Configuration Details -->
        <div class="row">
          <div class="col-md-8">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Delhivery Configuration Details</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table table-sm">
                      <tr>
                        <td><strong>API Key:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['api_key']) ? substr($delhiveryConfig['api_key'], 0, 10) . '...' : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Client Name:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['client_name']) ? htmlspecialchars($delhiveryConfig['client_name']) : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Return City:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['return_city']) ? htmlspecialchars($delhiveryConfig['return_city']) : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Return State:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['return_state']) ? htmlspecialchars($delhiveryConfig['return_state']) : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Return Pincode:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['return_pincode']) ? htmlspecialchars($delhiveryConfig['return_pincode']) : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table table-sm">
                      <tr>
                        <td><strong>Return Address:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['return_address']) ? htmlspecialchars(substr($delhiveryConfig['return_address'], 0, 30)) . '...' : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Seller Name:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['seller_name']) ? htmlspecialchars($delhiveryConfig['seller_name']) : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                      <tr>
                        <td><strong>GST Number:</strong></td>
                        <td><?php echo !empty($delhiveryConfig['seller_gst']) ? htmlspecialchars($delhiveryConfig['seller_gst']) : '<span class="text-muted">Not configured</span>'; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                          <?php if ($isDelhiveryConfigured): ?>
                          <span class="badge badge-success">Active</span>
                          <?php else: ?>
                          <span class="badge badge-secondary">Inactive</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td><span class="text-muted">Check settings page</span></td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Configuration Status</h3>
              </div>
              <div class="card-body">
                <div class="progress-group">
                  API Key
                  <span class="float-right"><b><?php echo !empty($delhiveryConfig['api_key']) ? '100' : '0'; ?>%</b></span>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-<?php echo !empty($delhiveryConfig['api_key']) ? 'success' : 'danger'; ?>"
                         style="width: <?php echo !empty($delhiveryConfig['api_key']) ? '100' : '0'; ?>%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  Return Address
                  <span class="float-right"><b><?php echo !empty($delhiveryConfig['return_address']) ? '100' : '0'; ?>%</b></span>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-<?php echo !empty($delhiveryConfig['return_address']) ? 'success' : 'warning'; ?>"
                         style="width: <?php echo !empty($delhiveryConfig['return_address']) ? '100' : '0'; ?>%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  Seller Info
                  <span class="float-right"><b><?php echo !empty($delhiveryConfig['seller_name']) ? '100' : '0'; ?>%</b></span>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-<?php echo !empty($delhiveryConfig['seller_name']) ? 'success' : 'warning'; ?>"
                         style="width: <?php echo !empty($delhiveryConfig['seller_name']) ? '100' : '0'; ?>%"></div>
                  </div>
                </div>

                <hr>
                <p class="text-center">
                  <strong>Overall Configuration</strong><br>
                  <?php
                  $configScore = 0;
                  if (!empty($delhiveryConfig['api_key'])) $configScore += 50;
                  if (!empty($delhiveryConfig['return_address'])) $configScore += 25;
                  if (!empty($delhiveryConfig['seller_name'])) $configScore += 25;
                  ?>
                  <span class="badge badge-<?php echo $configScore >= 75 ? 'success' : ($configScore >= 50 ? 'warning' : 'danger'); ?> badge-lg">
                    <?php echo $configScore; ?>% Complete
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>

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
                    <a href="delivery_settings.php" class="btn btn-block btn-primary">
                      <i class="fas fa-cog"></i><br>
                      Configure Delhivery
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="delivery_tracking.php" class="btn btn-block btn-info">
                      <i class="fas fa-search"></i><br>
                      Track Shipments
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="bulk_delivery.php" class="btn btn-block btn-success">
                      <i class="fas fa-shipping-fast"></i><br>
                      Bulk Processing
                    </a>
                  </div>
                  <div class="col-md-3">
                    <a href="dashboard.php" class="btn btn-block btn-secondary">
                      <i class="fas fa-chart-bar"></i><br>
                      View Dashboard
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

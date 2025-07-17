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

$selected = "delivery_settings.php";
$message = '';
$messageType = '';

// Handle form submission
if ($_POST) {
    try {
        $conn->begin_transaction();

        // Update Delhivery settings
        $delhiverySettings = [
            'delhivery_api_key' => $_POST['delhivery_api_key'] ?? '',
            'delhivery_client_name' => $_POST['delhivery_client_name'] ?? '',
            'delhivery_return_address' => $_POST['delhivery_return_address'] ?? '',
            'delhivery_return_city' => $_POST['delhivery_return_city'] ?? '',
            'delhivery_return_state' => $_POST['delhivery_return_state'] ?? '',
            'delhivery_return_pincode' => $_POST['delhivery_return_pincode'] ?? '',
            'delhivery_return_phone' => $_POST['delhivery_return_phone'] ?? '',
            'delhivery_seller_name' => $_POST['delhivery_seller_name'] ?? '',
            'delhivery_seller_address' => $_POST['delhivery_seller_address'] ?? '',
            'delhivery_seller_gst' => $_POST['delhivery_seller_gst'] ?? ''
        ];

        // General settings
        $generalSettings = [
            'auto_create_shipments' => $_POST['auto_create_shipments'] ?? '0',
            'default_package_weight' => $_POST['default_package_weight'] ?? '0.5',
            'default_dimensions' => json_encode([
                'length' => $_POST['default_length'] ?? 10,
                'width' => $_POST['default_width'] ?? 10,
                'height' => $_POST['default_height'] ?? 10
            ])
        ];

        // Combine all settings
        $allSettings = array_merge($delhiverySettings, $generalSettings);

        // Update or insert settings
        foreach ($allSettings as $key => $value) {
            $stmt = $conn->prepare("INSERT INTO shipping_config (config_key, config_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }

        $conn->commit();

        // Test Delhivery API connection
        $testResults = [];
        if (!empty($_POST['delhivery_api_key'])) {
            try {
                $deliveryManager = new DeliveryManager($conn);
                $validationResult = $deliveryManager->validateConfiguration();
                if ($validationResult['delhivery']['status'] === 'success') {
                    $testResults['delhivery'] = 'Connected successfully';
                } else {
                    $testResults['delhivery'] = 'Connection failed: ' . $validationResult['delhivery']['message'];
                }
            } catch (Exception $e) {
                $testResults['delhivery'] = 'Connection failed: ' . $e->getMessage();
            }
        }

        $message = 'Delhivery settings saved successfully!';
        if (!empty($testResults)) {
            $message .= '<br><strong>API Test Results:</strong><br>';
            foreach ($testResults as $provider => $result) {
                $message .= ucfirst($provider) . ': ' . $result . '<br>';
            }
        }
        $messageType = 'success';

    } catch (Exception $e) {
        $conn->rollback();
        $message = 'Error saving settings: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get current settings
$sql = "SELECT config_key, config_value FROM shipping_config";
$result = mysqli_query($conn, $sql);
$config = [];
while ($row = mysqli_fetch_assoc($result)) {
    $config[$row['config_key']] = $row['config_value'];
}

// Parse dimensions
$dimensions = isset($config['default_dimensions']) ? json_decode($config['default_dimensions'], true) : ['length' => 10, 'width' => 10, 'height' => 10];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Delivery Settings | OMS</title>
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
            <h1 class="m-0">Delivery Settings</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Delivery Settings</li>
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

        <form method="POST" action="">
          <div class="row">
            <!-- Delhivery Settings -->
            <div class="col-md-8">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Delhivery Configuration</h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <label for="delhivery_api_key">API Key *</label>
                    <input type="text" class="form-control" id="delhivery_api_key" name="delhivery_api_key"
                           value="<?php echo htmlspecialchars($config['delhivery_api_key'] ?? ''); ?>" placeholder="Enter Delhivery API Key" required>
                    <small class="form-text text-muted">Get your API key from Delhivery dashboard</small>
                  </div>
                  <div class="form-group">
                    <label for="delhivery_client_name">Client Name</label>
                    <input type="text" class="form-control" id="delhivery_client_name" name="delhivery_client_name"
                           value="<?php echo htmlspecialchars($config['delhivery_client_name'] ?? ''); ?>" placeholder="Your Company Name">
                  </div>

                  <h5>Return Address</h5>
                  <div class="form-group">
                    <label for="delhivery_return_address">Address</label>
                    <textarea class="form-control" id="delhivery_return_address" name="delhivery_return_address" rows="3"
                              placeholder="Return address"><?php echo htmlspecialchars($config['delhivery_return_address'] ?? ''); ?></textarea>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="delhivery_return_city">City</label>
                        <input type="text" class="form-control" id="delhivery_return_city" name="delhivery_return_city"
                               value="<?php echo htmlspecialchars($config['delhivery_return_city'] ?? ''); ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="delhivery_return_state">State</label>
                        <input type="text" class="form-control" id="delhivery_return_state" name="delhivery_return_state"
                               value="<?php echo htmlspecialchars($config['delhivery_return_state'] ?? ''); ?>">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="delhivery_return_pincode">Pincode</label>
                        <input type="text" class="form-control" id="delhivery_return_pincode" name="delhivery_return_pincode"
                               value="<?php echo htmlspecialchars($config['delhivery_return_pincode'] ?? ''); ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="delhivery_return_phone">Phone</label>
                        <input type="text" class="form-control" id="delhivery_return_phone" name="delhivery_return_phone"
                               value="<?php echo htmlspecialchars($config['delhivery_return_phone'] ?? ''); ?>">
                      </div>
                    </div>
                  </div>

                  <h5>Seller Information</h5>
                  <div class="form-group">
                    <label for="delhivery_seller_name">Seller Name</label>
                    <input type="text" class="form-control" id="delhivery_seller_name" name="delhivery_seller_name"
                           value="<?php echo htmlspecialchars($config['delhivery_seller_name'] ?? ''); ?>">
                  </div>
                  <div class="form-group">
                    <label for="delhivery_seller_address">Seller Address</label>
                    <textarea class="form-control" id="delhivery_seller_address" name="delhivery_seller_address" rows="2"
                              placeholder="Seller address"><?php echo htmlspecialchars($config['delhivery_seller_address'] ?? ''); ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="delhivery_seller_gst">GST Number</label>
                    <input type="text" class="form-control" id="delhivery_seller_gst" name="delhivery_seller_gst"
                           value="<?php echo htmlspecialchars($config['delhivery_seller_gst'] ?? ''); ?>">
                  </div>
                </div>
              </div>
            </div>

            <!-- General Settings -->
            <div class="col-md-4">
              <div class="card card-warning">
                <div class="card-header">
                  <h3 class="card-title">General Settings</h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="auto_create_shipments" name="auto_create_shipments" value="1"
                             <?php echo ($config['auto_create_shipments'] ?? '0') === '1' ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="auto_create_shipments">
                        Auto-create shipments for new orders
                      </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="default_package_weight">Default Package Weight (kg)</label>
                    <input type="number" step="0.1" class="form-control" id="default_package_weight" name="default_package_weight"
                           value="<?php echo htmlspecialchars($config['default_package_weight'] ?? '0.5'); ?>">
                  </div>

                  <h6>Default Package Dimensions (cm)</h6>
                  <div class="form-group">
                    <label for="default_length">Length</label>
                    <input type="number" class="form-control" id="default_length" name="default_length"
                           value="<?php echo htmlspecialchars($dimensions['length'] ?? 10); ?>">
                  </div>
                  <div class="form-group">
                    <label for="default_width">Width</label>
                    <input type="number" class="form-control" id="default_width" name="default_width"
                           value="<?php echo htmlspecialchars($dimensions['width'] ?? 10); ?>">
                  </div>
                  <div class="form-group">
                    <label for="default_height">Height</label>
                    <input type="number" class="form-control" id="default_height" name="default_height"
                           value="<?php echo htmlspecialchars($dimensions['height'] ?? 10); ?>">
                  </div>
                </div>
              </div>

              <!-- Quick Info -->
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">Delhivery Info</h3>
                </div>
                <div class="card-body">
                  <p><strong>Provider:</strong> Delhivery</p>
                  <p><strong>Type:</strong> Express & Surface Delivery</p>
                  <p><strong>Coverage:</strong> Pan-India</p>
                  <p><strong>Features:</strong></p>
                  <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Real-time tracking</li>
                    <li><i class="fas fa-check text-success"></i> COD support</li>
                    <li><i class="fas fa-check text-success"></i> Return management</li>
                    <li><i class="fas fa-check text-success"></i> API integration</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Save Delhivery Settings & Test Connection
              </button>
              <a href="delivery_providers.php" class="btn btn-info btn-lg ml-2">
                <i class="fas fa-cog"></i> Manage Providers
              </a>
            </div>
          </div>
        </form>
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

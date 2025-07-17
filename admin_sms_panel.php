<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin SMS Notification Panel - MyNutrify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .notification-card {
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .success-card {
            border-left-color: #28a745;
        }
        .error-card {
            border-left-color: #dc3545;
        }
        .warning-card {
            border-left-color: #ffc107;
        }
        .log-container {
            max-height: 400px;
            overflow-y: auto;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-sms text-primary"></i>
                    Admin SMS Notification Panel
                </h1>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> This panel is for admin SMS notifications only. Customer notifications continue to use WhatsApp via Interakt API.
                </div>
                
                <?php
                require_once 'sms_api/sms_order_hooks.php';
                require_once 'database/dbconnection.php';
                
                $message = '';
                $messageType = '';
                
                // Handle form submissions
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $action = $_POST['action'] ?? '';
                    
                    switch ($action) {
                        case 'test_sms':
                            $orderId = $_POST['order_id'] ?? '';
                            $type = $_POST['notification_type'] ?? 'shipped';
                            
                            if ($orderId) {
                                $result = testSMSWithOrder($orderId, $type);
                                if ($result['success']) {
                                    $message = "✅ Test SMS sent successfully for Order ID: $orderId";
                                    $messageType = 'success';
                                } else {
                                    $message = "❌ SMS failed: " . $result['error'];
                                    $messageType = 'error';
                                }
                            } else {
                                $message = "❌ Please enter an Order ID";
                                $messageType = 'error';
                            }
                            break;
                            
                        case 'custom_sms':
                            $phone = $_POST['phone'] ?? '';
                            $customMessage = $_POST['custom_message'] ?? '';
                            
                            if ($phone && $customMessage) {
                                $result = sendCustomSMS($phone, $customMessage);
                                if ($result) {
                                    $message = "✅ Custom SMS sent successfully to: $phone";
                                    $messageType = 'success';
                                } else {
                                    $message = "❌ Failed to send custom SMS";
                                    $messageType = 'error';
                                }
                            } else {
                                $message = "❌ Please enter phone number and message";
                                $messageType = 'error';
                            }
                            break;
                            
                        case 'bulk_sms':
                            $orderIds = explode(',', $_POST['order_ids'] ?? '');
                            $type = $_POST['bulk_type'] ?? 'shipped';
                            
                            if (!empty($orderIds[0])) {
                                $results = sendBulkSMSNotifications(array_map('trim', $orderIds), $type);
                                $successCount = array_sum($results);
                                $totalCount = count($results);
                                
                                $message = "📊 Bulk SMS completed: $successCount/$totalCount messages sent successfully";
                                $messageType = $successCount == $totalCount ? 'success' : 'warning';
                            } else {
                                $message = "❌ Please enter Order IDs";
                                $messageType = 'error';
                            }
                            break;
                            
                        case 'check_config':
                            $result = checkSMSConfiguration();
                            $message = $result['message'];
                            $messageType = $result['success'] ? 'success' : 'error';
                            break;
                    }
                }
                
                // Display message
                if ($message) {
                    $alertClass = $messageType === 'success' ? 'alert-success' : 
                                 ($messageType === 'warning' ? 'alert-warning' : 'alert-danger');
                    echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>
                            $message
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                          </div>";
                }
                ?>
                
                <!-- Configuration Check -->
                <div class="card notification-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-cog"></i> SMS Configuration</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="check_config">
                            <p>Check if SMS API is properly configured and working.</p>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Test SMS Configuration
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Test SMS -->
                <div class="card notification-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-test-tube"></i> Test SMS Notification</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="test_sms">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="order_id" class="form-label">Order ID</label>
                                    <input type="text" class="form-control" id="order_id" name="order_id" 
                                           placeholder="Enter Order ID" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="notification_type" class="form-label">Notification Type</label>
                                    <select class="form-select" id="notification_type" name="notification_type">
                                        <option value="placed">Order Placed</option>
                                        <option value="shipped" selected>Order Shipped</option>
                                        <option value="delivered">Order Delivered</option>
                                        <option value="payment_reminder">Payment Reminder</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">
                                <i class="fas fa-paper-plane"></i> Send Test SMS
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Custom SMS -->
                <div class="card notification-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-edit"></i> Send Custom SMS</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="custom_sms">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           placeholder="10-digit mobile number" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="custom_message" class="form-label">Message</label>
                                    <textarea class="form-control" id="custom_message" name="custom_message" 
                                              rows="3" placeholder="Enter your message" required></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info mt-3">
                                <i class="fas fa-sms"></i> Send Custom SMS
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Bulk SMS -->
                <div class="card notification-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Bulk SMS Notifications</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="bulk_sms">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="order_ids" class="form-label">Order IDs (comma-separated)</label>
                                    <input type="text" class="form-control" id="order_ids" name="order_ids" 
                                           placeholder="MN001, MN002, MN003" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="bulk_type" class="form-label">Notification Type</label>
                                    <select class="form-select" id="bulk_type" name="bulk_type">
                                        <option value="shipped" selected>Order Shipped</option>
                                        <option value="delivered">Order Delivered</option>
                                        <option value="payment_reminder">Payment Reminder</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning mt-3">
                                <i class="fas fa-broadcast-tower"></i> Send Bulk SMS
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card notification-card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-cart"></i> Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $obj = new main();
                            $obj->connection();
                            
                            $recentOrders = $obj->MysqliSelect(
                                "SELECT o.OrderId, o.OrderStatus, o.Amount, c.Name, c.MobileNo, o.OrderDate 
                                 FROM order_master o 
                                 JOIN customer_master c ON o.CustomerId = c.CustomerId 
                                 ORDER BY o.OrderDate DESC LIMIT 10",
                                ["OrderId", "OrderStatus", "Amount", "Name", "MobileNo", "OrderDate"],
                                "",
                                []
                            );
                            
                            if (!empty($recentOrders)) {
                                echo "<div class='table-responsive'>";
                                echo "<table class='table table-striped'>";
                                echo "<thead><tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Amount</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>";
                                echo "<tbody>";
                                
                                foreach ($recentOrders as $order) {
                                    echo "<tr>";
                                    echo "<td>{$order['OrderId']}</td>";
                                    echo "<td>{$order['Name']}</td>";
                                    echo "<td>{$order['MobileNo']}</td>";
                                    echo "<td>₹{$order['Amount']}</td>";
                                    echo "<td><span class='badge bg-primary'>{$order['OrderStatus']}</span></td>";
                                    echo "<td>" . date('d-M-Y', strtotime($order['OrderDate'])) . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-sm btn-outline-success' onclick='quickSMS(\"{$order['OrderId']}\", \"shipped\")'>Ship SMS</button> ";
                                    echo "<button class='btn btn-sm btn-outline-info' onclick='quickSMS(\"{$order['OrderId']}\", \"delivered\")'>Delivery SMS</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                
                                echo "</tbody></table>";
                                echo "</div>";
                            } else {
                                echo "<p class='text-muted'>No recent orders found.</p>";
                            }
                        } catch (Exception $e) {
                            echo "<p class='text-danger'>Error loading orders: " . $e->getMessage() . "</p>";
                        }
                        ?>
                    </div>
                </div>
                
                <!-- SMS Logs -->
                <div class="card notification-card">
                    <div class="card-header">
                        <h5><i class="fas fa-file-alt"></i> Recent SMS Logs</h5>
                    </div>
                    <div class="card-body">
                        <div class="log-container">
                            <?php
                            $logFile = 'sms_api/logs/sms_' . date('Y-m-d') . '.log';
                            if (file_exists($logFile)) {
                                $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                                $logs = array_reverse(array_slice($logs, -20)); // Last 20 entries
                                
                                foreach ($logs as $log) {
                                    $class = strpos($log, 'SUCCESS') !== false ? 'text-success' : 
                                            (strpos($log, 'ERROR') !== false ? 'text-danger' : 'text-info');
                                    echo "<div class='$class'>" . htmlspecialchars($log) . "</div>";
                                }
                            } else {
                                echo "<p class='text-muted'>No SMS logs found for today.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function quickSMS(orderId, type) {
            if (confirm(`Send ${type} SMS for Order ${orderId}?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="test_sms">
                    <input type="hidden" name="order_id" value="${orderId}">
                    <input type="hidden" name="notification_type" value="${type}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php
/**
 * Simple WhatsApp Admin Panel
 * Manage WhatsApp messages without database changes
 */

session_start();
header('Content-Type: text/html; charset=UTF-8');

// Include required files
require_once __DIR__ . '/whatsapp_api/order_hooks.php';
require_once __DIR__ . '/database/dbconnection.php';

// Handle form submissions
$message = '';
$messageType = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'test_order':
                $orderId = $_POST['order_id'];
                $templateType = $_POST['template_type'];
                $result = testWhatsAppWithOrder($orderId, $templateType);
                
                if ($result['success']) {
                    $message = "✅ WhatsApp sent successfully! Message ID: " . $result['message_id'];
                    $messageType = 'success';
                } else {
                    $message = "❌ Failed to send WhatsApp: " . $result['error'];
                    $messageType = 'error';
                }
                break;
                
            case 'bulk_update':
                $orderIds = explode(',', $_POST['order_ids']);
                $status = $_POST['status'];
                $results = sendBulkOrderUpdates(array_map('trim', $orderIds), $status);
                
                $successCount = array_sum($results);
                $totalCount = count($results);
                
                $message = "📊 Bulk update completed: $successCount/$totalCount messages sent successfully";
                $messageType = $successCount == $totalCount ? 'success' : 'warning';
                break;
                
            case 'birthday_wishes':
                $results = sendDailyBirthdayWishes();

                if (isset($results['success']) && !$results['success']) {
                    $message = "❌ " . $results['error'];
                    $messageType = 'error';
                } else {
                    $count = count($results);
                    $message = "🎂 Birthday wishes sent to $count customers";
                    $messageType = 'success';
                }
                break;
        }
    }
}

// Get recent orders for testing
$obj = new main();
$obj->connection();

$recentOrders = $obj->MysqliSelect(
    "SELECT o.OrderId, o.CustomerId, o.Amount, o.OrderDate, c.Name, c.MobileNo
     FROM order_master o
     JOIN customer_master c ON o.CustomerId = c.CustomerId
     WHERE c.IsActive = 1
     ORDER BY o.OrderDate DESC
     LIMIT 10",
    ["OrderId", "CustomerId", "Amount", "OrderDate", "Name", "MobileNo"]
);

// Get WhatsApp statistics
$stats = getWhatsAppStats(7);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Admin Panel - My Nutrify</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .header {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #25D366;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        button {
            background: #25D366;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        button:hover {
            background: #128C7E;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .message.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .message.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .message.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .logs {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📱 WhatsApp Admin Panel</h1>
        <p>Manage WhatsApp automation for My Nutrify</p>
    </div>

    <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_sent']; ?></div>
            <div>Messages Sent (7 days)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_failed']; ?></div>
            <div>Failed Messages</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo count($stats['by_template']); ?></div>
            <div>Active Templates</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_sent'] > 0 ? round(($stats['total_sent'] / ($stats['total_sent'] + $stats['total_failed'])) * 100) : 0; ?>%</div>
            <div>Success Rate</div>
        </div>
    </div>

    <!-- Test Single Order -->
    <div class="card">
        <h2>🧪 Test WhatsApp with Order</h2>
        <form method="POST">
            <input type="hidden" name="action" value="test_order">
            
            <div class="form-group">
                <label>Order ID:</label>
                <select name="order_id" required>
                    <option value="">Select an order...</option>
                    <?php foreach ($recentOrders as $order): ?>
                        <option value="<?php echo $order['OrderId']; ?>">
                            <?php echo $order['OrderId']; ?> - <?php echo $order['Name']; ?> (<?php echo $order['MobileNo']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Template Type:</label>
                <select name="template_type" required>
                    <option value="shipped">Order Shipped</option>
                    <option value="payment_reminder">Payment Reminder</option>
                    <option value="feedback">Feedback Request</option>
                </select>
            </div>
            
            <button type="submit">Send Test WhatsApp</button>
        </form>
    </div>

    <!-- Bulk Operations -->
    <div class="card">
        <h2>📦 Bulk Order Updates</h2>
        <form method="POST">
            <input type="hidden" name="action" value="bulk_update">
            
            <div class="form-group">
                <label>Order IDs (comma-separated):</label>
                <textarea name="order_ids" rows="3" placeholder="MN000001, MN000002, MN000003" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Status Update:</label>
                <select name="status" required>
                    <option value="shipped">Mark as Shipped</option>
                    <option value="out_for_delivery">Mark as Out for Delivery</option>
                    <option value="delivered">Mark as Delivered</option>
                </select>
            </div>
            
            <button type="submit">Send Bulk Updates</button>
        </form>
    </div>

    <!-- Birthday Wishes - DISABLED -->
    <div class="card" style="opacity: 0.6;">
        <h2>🎂 Birthday Wishes (Disabled)</h2>
        <p style="color: #856404;">This feature requires DateOfBirth column in customer_master table.</p>
        <p>To enable this feature, you would need to:</p>
        <ol>
            <li>Add DateOfBirth column to customer_master table</li>
            <li>Collect customer birth dates during registration</li>
            <li>Create birthday_wishes template in Interakt</li>
        </ol>
        <button type="button" disabled style="background: #ccc; cursor: not-allowed;">Feature Disabled</button>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <h2>📋 Recent Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><?php echo $order['OrderId']; ?></td>
                        <td><?php echo $order['Name']; ?></td>
                        <td><?php echo $order['MobileNo']; ?></td>
                        <td>₹<?php echo number_format($order['Amount'], 2); ?></td>
                        <td><?php echo date('d M Y', strtotime($order['OrderDate'])); ?></td>
                        <td>
                            <button onclick="sendQuickWhatsApp('<?php echo $order['OrderId']; ?>', 'shipped')" style="font-size: 12px; padding: 5px 10px;">
                                📱 Shipped
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Template Statistics -->
    <div class="card">
        <h2>📊 Template Usage (Last 7 Days)</h2>
        <?php if (!empty($stats['by_template'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Template</th>
                        <th>Messages Sent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['by_template'] as $template => $count): ?>
                        <tr>
                            <td><?php echo $template; ?></td>
                            <td><?php echo $count; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No template usage data available.</p>
        <?php endif; ?>
    </div>

    <!-- Today's Logs -->
    <div class="card">
        <h2>📝 Today's WhatsApp Logs</h2>
        <div class="logs">
            <?php echo getTodayWhatsAppLogs(); ?>
        </div>
    </div>

    <script>
        function sendQuickWhatsApp(orderId, status) {
            if (confirm(`Send WhatsApp ${status} notification for order ${orderId}?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="test_order">
                    <input type="hidden" name="order_id" value="${orderId}">
                    <input type="hidden" name="template_type" value="${status}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

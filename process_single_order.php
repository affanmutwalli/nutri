<?php
// Process SINGLE Order for Testing
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🎯 Process Single Order (Safe Testing)</h2>";

// Get order ID from URL parameter
$orderIdToProcess = $_GET['order_id'] ?? '';

if (empty($orderIdToProcess)) {
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>📝 Enter Order ID to Process:</h3>";
    echo "<form method='GET'>";
    echo "<input type='text' name='order_id' placeholder='Enter Order ID (e.g., ON1752061926930)' style='padding: 8px; width: 300px;'>";
    echo "<button type='submit' style='padding: 8px 15px; margin-left: 10px; background: #007bff; color: white; border: none; border-radius: 4px;'>🔍 Check Order</button>";
    echo "</form>";
    echo "</div>";
    
    // Show recent orders for reference
    echo "<h3>📋 Recent Orders (for reference):</h3>";
    $recentQuery = "SELECT OrderId, OrderStatus, Amount, CustomerType, CreatedAt, Waybill 
                    FROM order_master 
                    WHERE (Waybill IS NULL OR Waybill = '' OR Waybill = 'NULL')
                    ORDER BY CreatedAt DESC LIMIT 10";
    
    $recentResult = mysqli_query($mysqli, $recentQuery);
    
    if ($recentResult && mysqli_num_rows($recentResult) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th style='padding: 8px;'>Order ID</th><th style='padding: 8px;'>Status</th><th style='padding: 8px;'>Amount</th><th style='padding: 8px;'>Type</th><th style='padding: 8px;'>Created</th><th style='padding: 8px;'>Action</th></tr>";
        
        while ($row = mysqli_fetch_assoc($recentResult)) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>{$row['OrderId']}</td>";
            echo "<td style='padding: 8px;'>{$row['OrderStatus']}</td>";
            echo "<td style='padding: 8px;'>₹{$row['Amount']}</td>";
            echo "<td style='padding: 8px;'>{$row['CustomerType']}</td>";
            echo "<td style='padding: 8px;'>{$row['CreatedAt']}</td>";
            echo "<td style='padding: 8px;'><a href='?order_id={$row['OrderId']}' style='color: #007bff;'>Process This</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    exit;
}

// Process the specific order
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>⚠️ Processing Order: $orderIdToProcess</h3>";
echo "<p><strong>This will create a REAL shipment and charge your Delhivery account!</strong></p>";
echo "</div>";

try {
    // Get the specific order
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
    mysqli_stmt_bind_param($stmt, "s", $orderIdToProcess);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($order = mysqli_fetch_assoc($result)) {
        echo "<h3>📋 Order Details:</h3>";
        echo "<p><strong>Order ID:</strong> {$order['OrderId']}</p>";
        echo "<p><strong>Customer:</strong> {$order['CustomerName']}</p>";
        echo "<p><strong>Phone:</strong> {$order['CustomerPhone']}</p>";
        echo "<p><strong>Address:</strong> {$order['ShipAddress']}</p>";
        echo "<p><strong>Amount:</strong> ₹{$order['Amount']}</p>";
        echo "<p><strong>Payment:</strong> {$order['PaymentType']}</p>";
        
        // Check if already shipped
        if (!empty($order['Waybill']) && $order['Waybill'] !== 'NULL') {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>✅ Order Already Shipped!</h3>";
            echo "<p>This order already has a waybill and has been shipped.</p>";
            echo "</div>";
            exit;
        }
        
        // Confirm before processing
        if (!isset($_GET['confirm'])) {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>⚠️ Confirm Shipment Creation</h3>";
            echo "<p>This will:</p>";
            echo "<ul>";
            echo "<li>Create a real shipment with Delhivery</li>";
            echo "<li>Charge approximately ₹30-80 from your account</li>";
            echo "<li>Generate a real waybill for delivery</li>";
            echo "<li>Send notifications to the customer</li>";
            echo "</ul>";
            echo "<p><a href='?order_id=$orderIdToProcess&confirm=yes' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>⚠️ YES, CREATE SHIPMENT</a></p>";
            echo "<p><a href='process_single_order.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>❌ Cancel</a></p>";
            echo "</div>";
            exit;
        }
        
        // Process the order (same logic as bulk processor but for single order)
        echo "<p>✅ Processing order $orderIdToProcess...</p>";

        try {
            require_once 'includes/DeliveryManager.php';
            $deliveryManager = new DeliveryManager($mysqli);

            if ($deliveryManager->isDelhiveryConfigured()) {
                // Prepare data same as bulk processor
                $customerPhone = trim($order['CustomerPhone'] ?? '');
                $shippingAddress = trim($order['ShipAddress'] ?? '');
                $totalAmount = $order['Amount'] ?? 0;

                // Skip if missing critical data
                if (empty($customerPhone) || empty($shippingAddress) || $totalAmount <= 0) {
                    echo "<p style='color: red;'>❌ Missing required data - cannot process</p>";
                    exit;
                }

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
                        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                        echo "<h3>✅ SUCCESS!</h3>";
                        echo "<p><strong>Order shipped successfully!</strong></p>";
                        echo "<p><strong>Waybill:</strong> $waybill</p>";
                        echo "<p><strong>Tracking URL:</strong> <a href='$trackingUrl' target='_blank'>$trackingUrl</a></p>";
                        echo "</div>";

                        // Send notification
                        try {
                            include_once 'whatsapp_api/order_hooks.php';
                            sendOrderShippedWhatsApp($order['OrderId']);
                            echo "<p>📱 WhatsApp notification sent to $customerPhone</p>";
                        } catch (Exception $e) {
                            echo "<p>⚠️ Notification failed: " . $e->getMessage() . "</p>";
                        }
                    }
                } else {
                    $errorMsg = isset($shipmentResult['message']) ? $shipmentResult['message'] : 'Unknown error';
                    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                    echo "<h3>❌ FAILED!</h3>";
                    echo "<p><strong>Error:</strong> $errorMsg</p>";
                    echo "</div>";
                }
            } else {
                echo "<p style='color: red;'>❌ Delhivery not configured</p>";
            }
        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>❌ ERROR!</h3>";
            echo "<p><strong>Exception:</strong> " . $e->getMessage() . "</p>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Order $orderIdToProcess not found!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

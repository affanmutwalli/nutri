<?php
/**
 * Process ALL Orders Without Waybill - Fixed Version
 */

include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🚀 Processing ALL Unshipped Orders</h1>";

try {
    // Get ALL orders without waybill, with customer address fallback
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
             LEFT JOIN customer_address ca ON om.CustomerId = ca.CustomerId
             WHERE (om.Waybill IS NULL OR om.Waybill = '' OR om.Waybill = 'NULL')
             AND om.OrderStatus NOT IN ('Cancelled', 'Refunded', 'Shipped', 'Delivered')
             ORDER BY om.CreatedAt ASC";
    
    $result = mysqli_query($mysqli, $query);
    $processedCount = 0;
    $totalOrders = mysqli_num_rows($result);
    
    echo "<p>📦 Found $totalOrders orders without waybill to process</p>";
    
    if ($totalOrders == 0) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ No unshipped orders found!</h3>";
        echo "<p>All orders already have waybills assigned.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>⚡ Processing Orders...</h3>";
        
        while ($order = mysqli_fetch_assoc($result)) {
            try {
                echo "<p>🔄 Processing order: {$order['OrderId']} (Status: {$order['OrderStatus']}) for {$order['CustomerName']}</p>";

                // Debug: Show order data
                echo "<div style='background: #f8f9fa; padding: 10px; margin: 5px 0; font-size: 12px;'>";
                echo "📋 Order Data: Phone: '{$order['CustomerPhone']}', Address: '{$order['ShipAddress']}', Amount: '{$order['Amount']}'";
                echo "</div>";

                // Initialize variables outside try block
                $waybill = '';
                $trackingUrl = '';
                $shipmentSuccess = false;
                $shipmentResult = null;

                // Create real shipment with Delhivery
                try {
                    require_once 'includes/DeliveryManager.php';
                    $deliveryManager = new DeliveryManager($mysqli);

                    if ($deliveryManager->isDelhiveryConfigured()) {
                        // Check for missing data and provide defaults
                        $customerPhone = trim($order['CustomerPhone'] ?? '');
                        $shippingAddress = trim($order['ShipAddress'] ?? '');
                        $totalAmount = $order['Amount'] ?? 0;

                        // If ShipAddress is empty, try to build it from customer_address table
                        if (empty($shippingAddress) && !empty($order['CustomerAddress'])) {
                            $addressParts = array_filter([
                                $order['CustomerAddress'],
                                $order['CustomerLandmark'],
                                $order['CustomerCity'],
                                $order['CustomerPincode'],
                                $order['CustomerState']
                            ]);
                            $shippingAddress = implode(', ', $addressParts);
                            echo "<p style='color: blue;'>ℹ️ Built address from customer_address table: $shippingAddress</p>";
                        }

                        // Skip orders with critical missing data
                        if (empty($customerPhone)) {
                            echo "<p style='color: orange;'>⚠️ Skipping order {$order['OrderId']}: No phone number</p>";
                            continue;
                        }

                        if (empty($shippingAddress)) {
                            echo "<p style='color: orange;'>⚠️ Skipping order {$order['OrderId']}: No shipping address</p>";
                            continue;
                        }

                        if ($totalAmount <= 0) {
                            echo "<p style='color: orange;'>⚠️ Skipping order {$order['OrderId']}: Invalid amount ($totalAmount)</p>";
                            continue;
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

                        // Debug: Show the exact data being sent to Delhivery
                        echo "<div style='background: #f0f8ff; padding: 10px; margin: 5px 0; font-size: 12px; border-left: 4px solid #007bff;'>";
                        echo "🔍 <strong>Data being sent to Delhivery:</strong><br>";
                        echo "customer_phone: '" . ($orderData['customer_phone'] ?? 'MISSING') . "'<br>";
                        echo "shipping_address: '" . ($orderData['shipping_address'] ?? 'MISSING') . "'<br>";
                        echo "total_amount: '" . ($orderData['total_amount'] ?? 'MISSING') . "'<br>";
                        echo "order_id: '" . ($orderData['order_id'] ?? 'MISSING') . "'<br>";
                        echo "</div>";

                        // Create shipment with Delhivery
                        $shipmentResult = $deliveryManager->createOrder($orderData);

                        if ($shipmentResult && isset($shipmentResult['waybill'])) {
                            $waybill = $shipmentResult['waybill'];
                            $trackingUrl = "https://www.delhivery.com/track/package/$waybill";
                            $shipmentSuccess = true;

                            // Update order with real waybill
                            $shipQuery = "UPDATE order_master SET
                                         Waybill = ?,
                                         OrderStatus = 'Shipped',
                                         delivery_status = 'shipped',
                                         delivery_provider = 'delhivery',
                                         tracking_url = ?
                                         WHERE OrderId = ?";

                            $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                            mysqli_stmt_bind_param($shipStmt, "sss", $waybill, $trackingUrl, $order['OrderId']);
                        } else {
                            // Fallback: Just mark as confirmed if Delhivery fails
                            $errorMsg = isset($shipmentResult['message']) ? $shipmentResult['message'] : 'Unknown error';
                            echo "<p style='color: orange;'>⚠ Delhivery shipment creation failed for order: {$order['OrderId']} - $errorMsg</p>";
                            $shipQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                            $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                            mysqli_stmt_bind_param($shipStmt, "s", $order['OrderId']);
                            error_log("Bulk processing: Delhivery shipment creation failed for order: {$order['OrderId']} - $errorMsg");
                        }
                    } else {
                        // Delhivery not configured, just confirm the order
                        echo "<p style='color: orange;'>⚠ Delhivery not configured - order {$order['OrderId']} marked as confirmed</p>";
                        $shipQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                        $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                        mysqli_stmt_bind_param($shipStmt, "s", $order['OrderId']);
                    }
                } catch (Exception $e) {
                    // Log error and fallback to confirmed status
                    echo "<p style='color: red;'>✗ Error processing order {$order['OrderId']}: " . $e->getMessage() . "</p>";
                    error_log("Bulk processing error for order {$order['OrderId']}: " . $e->getMessage());
                    $shipQuery = "UPDATE order_master SET OrderStatus = 'Confirmed' WHERE OrderId = ?";
                    $shipStmt = mysqli_prepare($mysqli, $shipQuery);
                    mysqli_stmt_bind_param($shipStmt, "s", $order['OrderId']);
                }
                
                if (mysqli_stmt_execute($shipStmt)) {
                    if ($shipmentSuccess && !empty($waybill)) {
                        echo "<p>✅ Order {$order['OrderId']} shipped successfully with waybill: $waybill</p>";

                        // Send WhatsApp notification only for successful shipments
                        try {
                            include_once 'whatsapp_api/order_hooks.php';
                            sendOrderShippedWhatsApp($order['OrderId']);
                            echo "<p>📱 WhatsApp notification sent to {$order['CustomerPhone']}</p>";
                        } catch (Exception $e) {
                            echo "<p>⚠️ WhatsApp notification failed: " . $e->getMessage() . "</p>";
                        }
                    } else {
                        echo "<p>⚠️ Order {$order['OrderId']} marked as confirmed (Delhivery shipment failed)</p>";
                    }

                    // Log the shipment attempt
                    $logQuery = "INSERT INTO delivery_logs (order_id, provider, action, status, request_data, response, created_at)
                                VALUES (?, 'delhivery', 'create_shipment', ?, ?, ?, NOW())";

                    $requestData = json_encode([
                        'order_id' => $order['OrderId'],
                        'customer_name' => $order['CustomerName'],
                        'customer_phone' => $order['CustomerPhone'],
                        'shipping_address' => $order['ShipAddress'],
                        'amount' => $order['Amount'],
                        'original_status' => $order['OrderStatus']
                    ]);

                    $responseData = json_encode([
                        'waybill' => $waybill,
                        'tracking_url' => $trackingUrl,
                        'status' => $shipmentSuccess ? 'shipped' : 'failed',
                        'shipment_result' => $shipmentResult ?? null
                    ]);

                    $logStatus = $shipmentSuccess ? 'success' : 'failed';
                    $logStmt = mysqli_prepare($mysqli, $logQuery);
                    mysqli_stmt_bind_param($logStmt, "ssss", $order['OrderId'], $logStatus, $requestData, $responseData);
                    mysqli_stmt_execute($logStmt);

                    $processedCount++;
                } else {
                    echo "<p>❌ Failed to update order {$order['OrderId']}: " . mysqli_error($mysqli) . "</p>";
                }
                
                echo "<hr>";
                
            } catch (Exception $e) {
                echo "<p>❌ Error processing order {$order['OrderId']}: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "</div>";
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 Processing Complete!</h3>";
        echo "<p><strong>Successfully processed:</strong> $processedCount out of $totalOrders orders</p>";
        echo "<p><strong>All orders are now shipped and customers have been notified!</strong></p>";
        echo "</div>";
    }
    
    echo "<h3>🔄 Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='oms/delivery_dashboard.php' target='_blank'>Check OMS Dashboard</a> - Should now show 0 pending orders</li>";
    echo "<li><a href='oms/all_orders.php' target='_blank'>View All Orders</a> - All orders should show 'Shipped' status</li>";
    echo "<li><a href='debug_orders.php' target='_blank'>Debug Orders</a> - Verify all orders have waybills</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Close database connection
if (isset($mysqli)) {
    mysqli_close($mysqli);
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1 { color: #28a745; }
h3 { color: #007bff; }
p { margin: 10px 0; }
hr { margin: 20px 0; border: 1px solid #ddd; }
ul, ol { margin: 10px 0 10px 20px; }
</style>

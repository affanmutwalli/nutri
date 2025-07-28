<?php
/**
 * Fix missing order details for existing orders
 */

header("Content-Type: text/html");
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Fix Missing Order Details</h2>";

session_start();
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

$orderId = $_GET['id'] ?? 'ON1753680466568';

echo "<h3>Fixing Order: " . htmlspecialchars($orderId) . "</h3>";

try {
    // First check if order exists
    $orderQuery = "SELECT OrderId, CustomerId, Amount FROM order_master WHERE OrderId = ?";
    $orderData = $obj->MysqliSelect1($orderQuery, ["OrderId", "CustomerId", "Amount"], "s", [$orderId]);
    
    if (!$orderData || count($orderData) == 0) {
        echo "<p style='color: red;'>❌ Order not found!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Order found: " . $orderData[0]['OrderId'] . "</p>";
    
    // Check if order details already exist
    $existingDetails = $obj->MysqliSelect1("SELECT COUNT(*) as count FROM order_details WHERE OrderId = ?", ["count"], "s", [$orderId]);
    
    if ($existingDetails && $existingDetails[0]['count'] > 0) {
        echo "<p style='color: orange;'>⚠️ Order details already exist (" . $existingDetails[0]['count'] . " items)</p>";
        
        // Show existing details
        $details = $obj->MysqliSelect1("SELECT ProductId, Quantity, Price FROM order_details WHERE OrderId = ?", 
            ["ProductId", "Quantity", "Price"], "s", [$orderId]);
        
        if ($details) {
            echo "<h4>Existing Order Details:</h4>";
            echo "<ul>";
            foreach ($details as $detail) {
                echo "<li>Product ID: " . $detail['ProductId'] . ", Quantity: " . $detail['Quantity'] . ", Price: ₹" . $detail['Price'] . "</li>";
            }
            echo "</ul>";
        }
        
        echo "<p><a href='order-details.php?id=" . urlencode($orderId) . "'>View Order Details Page</a></p>";
        exit;
    }
    
    echo "<p style='color: red;'>❌ No order details found. Let's check pending orders data...</p>";
    
    // Check if there's pending order data
    $pendingQuery = "SELECT order_data FROM pending_orders WHERE order_id = ?";
    $pendingData = $obj->MysqliSelect1($pendingQuery, ["order_data"], "s", [$orderId]);
    
    if ($pendingData && count($pendingData) > 0) {
        echo "<p style='color: green;'>✅ Found pending order data</p>";
        
        $orderDataJson = json_decode($pendingData[0]['order_data'], true);
        
        if ($orderDataJson && isset($orderDataJson['products'])) {
            echo "<h4>Products from pending order data:</h4>";
            echo "<ul>";
            foreach ($orderDataJson['products'] as $product) {
                echo "<li>Product ID: " . ($product['id'] ?? 'N/A') . ", Name: " . ($product['name'] ?? 'N/A') . ", Quantity: " . ($product['quantity'] ?? 'N/A') . ", Price: ₹" . ($product['offer_price'] ?? 'N/A') . "</li>";
            }
            echo "</ul>";
            
            // Insert the missing order details
            if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
                echo "<h4>Inserting Order Details...</h4>";
                
                $insertQuery = "INSERT INTO order_details (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $obj->connection()->prepare($insertQuery);
                
                if ($stmt) {
                    foreach ($orderDataJson['products'] as $product) {
                        $productId = intval($product['id'] ?? 0);
                        $productCode = $product['code'] ?? '';
                        $productSize = $product['size'] ?? '';
                        $quantity = intval($product['quantity'] ?? 1);
                        $price = floatval($product['offer_price'] ?? $product['price'] ?? 0);
                        $subTotal = $quantity * $price;
                        
                        $stmt->bind_param("sissidd", $orderId, $productId, $productCode, $productSize, $quantity, $price, $subTotal);
                        
                        if ($stmt->execute()) {
                            echo "<p style='color: green;'>✅ Inserted product ID: " . $productId . "</p>";
                        } else {
                            echo "<p style='color: red;'>❌ Failed to insert product ID: " . $productId . " - " . $stmt->error . "</p>";
                        }
                    }
                    $stmt->close();
                    
                    echo "<p style='color: green;'>✅ Order details insertion completed!</p>";
                    echo "<p><a href='order-details.php?id=" . urlencode($orderId) . "'>View Order Details Page</a></p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to prepare insert statement</p>";
                }
            } else {
                echo "<p><a href='?id=" . urlencode($orderId) . "&fix=yes' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Fix Order Details</a></p>";
            }
        } else {
            echo "<p style='color: red;'>❌ No products data in pending order</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No pending order data found</p>";
        
        // Manual insertion option
        echo "<h4>Manual Order Details Creation</h4>";
        echo "<p>Since we can't find the original product data, you can manually add order details:</p>";
        
        if (isset($_GET['manual']) && $_GET['manual'] == 'yes') {
            // Add a sample product for testing
            $insertQuery = "INSERT INTO order_details (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $obj->connection()->prepare($insertQuery);
            
            if ($stmt) {
                // Sample data - you can modify this
                $productId = 15; // Apple Cider Vinegar
                $productCode = 'ACV001';
                $productSize = '1000ml';
                $quantity = 1;
                $price = 1.00;
                $subTotal = $quantity * $price;
                
                $stmt->bind_param("sissidd", $orderId, $productId, $productCode, $productSize, $quantity, $price, $subTotal);
                
                if ($stmt->execute()) {
                    echo "<p style='color: green;'>✅ Sample order detail inserted!</p>";
                    echo "<p><a href='order-details.php?id=" . urlencode($orderId) . "'>View Order Details Page</a></p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to insert sample data: " . $stmt->error . "</p>";
                }
                $stmt->close();
            }
        } else {
            echo "<p><a href='?id=" . urlencode($orderId) . "&manual=yes' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Add Sample Order Detail</a></p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Fix Complete</h3>";
echo "<p>Timestamp: " . date('Y-m-d H:i:s') . "</p>";
?>

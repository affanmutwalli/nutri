<?php
// Fix Order - Remove unwanted phantom products
// This script will remove unwanted products from orders and recalculate totals

// Include database configuration and connection
include_once 'cms/includes/psl-config.php';
include_once 'cms/database/dbconnection.php';

// Initialize database object
$obj = new main();

// Order ID to fix - can be changed as needed
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : "MN000098";

echo "<h2>Fixing Order: $orderId</h2>\n";
echo "<p>Removing unwanted Apple Cider Vinegar product...</p>\n";

try {
    // Step 1: Get current order details to see what products are in the order
    echo "<h3>Step 1: Current Order Details</h3>\n";
    
    $FieldNames = array("ProductId", "ProductCode", "Quantity", "Size", "Price", "SubTotal");
    $ParamArray = [$orderId];
    
    $orderDetails = $obj->MysqliSelect1(
        "SELECT ProductId, ProductCode, Quantity, Size, Price, SubTotal FROM order_details WHERE OrderId = ? ORDER BY ProductId",
        $FieldNames,
        "s",
        $ParamArray
    );
    
    if ($orderDetails) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Product ID</th><th>Product Code</th><th>Quantity</th><th>Size</th><th>Price</th><th>SubTotal</th></tr>\n";
        
        $totalAmount = 0;
        $appleVinegarFound = false;
        $appleVinegarProductId = null;
        
        foreach ($orderDetails as $item) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($item['ProductCode']) . "</td>";
            echo "<td>" . htmlspecialchars($item['Quantity']) . "</td>";
            echo "<td>" . htmlspecialchars($item['Size']) . "</td>";
            echo "<td>₹" . number_format($item['Price'], 2) . "</td>";
            echo "<td>₹" . number_format($item['SubTotal'], 2) . "</td>";
            echo "</tr>\n";
            
            $totalAmount += $item['SubTotal'];
            
            // Check for phantom/unwanted products (look for specific codes or empty product codes)
            if (strpos($item['ProductCode'], 'MN-AC') !== false ||
                strpos($item['ProductCode'], 'MN-SC') !== false ||
                strpos($item['ProductCode'], 'AC') !== false ||
                strpos($item['ProductCode'], 'SC') !== false ||
                empty($item['ProductCode']) ||
                $item['ProductCode'] == 'N/A') {
                $appleVinegarFound = true;
                $appleVinegarProductId = $item['ProductId'];
                echo "<tr style='background-color: #ffcccc;'><td colspan='6'><strong>↑ This is the phantom/unwanted product to be removed</strong></td></tr>\n";
            }
        }
        
        echo "</table>\n";
        echo "<p><strong>Current Total Amount: ₹" . number_format($totalAmount, 2) . "</strong></p>\n";
        
        if ($appleVinegarFound) {
            echo "<p style='color: green;'>✓ Phantom/unwanted product found (Product ID: $appleVinegarProductId)</p>\n";

            // Step 2: Remove the phantom/unwanted product
            echo "<h3>Step 2: Removing Phantom/Unwanted Product</h3>\n";
            
            $deleteQuery = "DELETE FROM order_details WHERE OrderId = ? AND ProductId = ?";
            $deleteParams = [$orderId, $appleVinegarProductId];
            
            $deleteResult = $obj->fDeleteNew($deleteQuery, "si", $deleteParams);
            
            if ($deleteResult !== false) {
                echo "<p style='color: green;'>✓ Phantom/unwanted product removed successfully!</p>\n";
                
                // Step 3: Recalculate the order total
                echo "<h3>Step 3: Recalculating Order Total</h3>\n";
                
                // Get remaining products and calculate new total
                $remainingDetails = $obj->MysqliSelect1(
                    "SELECT SUM(SubTotal) as NewTotal FROM order_details WHERE OrderId = ?",
                    array("NewTotal"),
                    "s",
                    [$orderId]
                );
                
                if ($remainingDetails && isset($remainingDetails[0]['NewTotal'])) {
                    $newTotal = $remainingDetails[0]['NewTotal'];
                    echo "<p><strong>New Total Amount: ₹" . number_format($newTotal, 2) . "</strong></p>\n";
                    
                    // Step 4: Update the order_master table with new amount
                    echo "<h3>Step 4: Updating Order Master</h3>\n";
                    
                    $updateQuery = "UPDATE order_master SET Amount = ? WHERE OrderId = ?";
                    $updateParams = [$newTotal, $orderId];
                    
                    $updateResult = $obj->fUpdateNew($updateQuery, "ds", $updateParams);
                    
                    if ($updateResult !== false) {
                        echo "<p style='color: green;'>✓ Order total updated successfully!</p>\n";
                        
                        // Step 5: Show final order details
                        echo "<h3>Step 5: Final Order Details</h3>\n";
                        
                        $finalDetails = $obj->MysqliSelect1(
                            "SELECT ProductId, ProductCode, Quantity, Size, Price, SubTotal FROM order_details WHERE OrderId = ? ORDER BY ProductId",
                            $FieldNames,
                            "s",
                            [$orderId]
                        );
                        
                        if ($finalDetails) {
                            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
                            echo "<tr><th>Product ID</th><th>Product Code</th><th>Quantity</th><th>Size</th><th>Price</th><th>SubTotal</th></tr>\n";
                            
                            foreach ($finalDetails as $item) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($item['ProductId']) . "</td>";
                                echo "<td>" . htmlspecialchars($item['ProductCode']) . "</td>";
                                echo "<td>" . htmlspecialchars($item['Quantity']) . "</td>";
                                echo "<td>" . htmlspecialchars($item['Size']) . "</td>";
                                echo "<td>₹" . number_format($item['Price'], 2) . "</td>";
                                echo "<td>₹" . number_format($item['SubTotal'], 2) . "</td>";
                                echo "</tr>\n";
                            }
                            
                            echo "</table>\n";
                            echo "<p><strong>Final Total: ₹" . number_format($newTotal, 2) . "</strong></p>\n";
                        }
                        
                        echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
                        echo "<h3 style='color: #155724; margin-top: 0;'>✅ Order Fixed Successfully!</h3>\n";
                        echo "<p style='color: #155724; margin-bottom: 0;'>The phantom/unwanted product has been removed from order $orderId and the total amount has been recalculated.</p>\n";
                        echo "</div>\n";
                        
                    } else {
                        echo "<p style='color: red;'>❌ Error updating order total in order_master table</p>\n";
                    }
                } else {
                    echo "<p style='color: red;'>❌ Error calculating new total amount</p>\n";
                }
                
            } else {
                echo "<p style='color: red;'>❌ Error removing phantom/unwanted product</p>\n";
            }
            
        } else {
            echo "<p style='color: orange;'>⚠️ No phantom/unwanted products found in this order</p>\n";
            echo "<p>Please check the product codes manually to identify which product needs to be removed.</p>\n";
        }
        
    } else {
        echo "<p style='color: red;'>❌ No order details found for order ID: $orderId</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<p><a href='order-placed.php?order_id=$orderId'>View Updated Order</a></p>\n";
?>

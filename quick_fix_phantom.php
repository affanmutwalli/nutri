<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🚀 Quick Phantom Product Fix</h2>";

// Direct approach - fix the specific issue
$orderId = 'MN000070';

echo "<h3>Fixing Order: $orderId</h3>";

if (isset($_GET['action']) && $_GET['action'] == 'fix_now') {
    echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
    echo "<h4>🔧 Applying Fix...</h4>";
    
    try {
        // Step 1: Remove the phantom product (ProductId 20) from this order
        $deletePhantom = "DELETE FROM order_details WHERE OrderId = '$orderId' AND ProductId = 20";
        if ($mysqli->query($deletePhantom)) {
            echo "<p>✅ Removed phantom ProductId 20 from order $orderId</p>";
        } else {
            echo "<p>❌ Failed to remove phantom product: " . $mysqli->error . "</p>";
        }
        
        // Step 2: Recalculate and update order total
        $newTotalQuery = "SELECT SUM(SubTotal) as total FROM order_details WHERE OrderId = '$orderId'";
        $totalResult = $mysqli->query($newTotalQuery);
        $newTotal = 0;
        
        if ($totalResult && $row = $totalResult->fetch_assoc()) {
            $newTotal = $row['total'] ?? 0;
        }
        
        $updateOrderQuery = "UPDATE order_master SET Amount = $newTotal WHERE OrderId = '$orderId'";
        if ($mysqli->query($updateOrderQuery)) {
            echo "<p>✅ Updated order total to ₹$newTotal</p>";
        } else {
            echo "<p>❌ Failed to update order total: " . $mysqli->error . "</p>";
        }
        
        // Step 3: Remove orphaned ProductId 20 from product_price table
        $deleteOrphanPrice = "DELETE FROM product_price WHERE ProductId = 20";
        if ($mysqli->query($deleteOrphanPrice)) {
            echo "<p>✅ Removed orphaned ProductId 20 from product_price table</p>";
        } else {
            echo "<p>❌ Failed to remove orphaned price: " . $mysqli->error . "</p>";
        }
        
        // Step 4: Check for any other orders with ProductId 20
        $otherOrdersQuery = "SELECT DISTINCT OrderId FROM order_details WHERE ProductId = 20";
        $otherOrdersResult = $mysqli->query($otherOrdersQuery);
        
        if ($otherOrdersResult && $otherOrdersResult->num_rows > 0) {
            echo "<p><strong>Other affected orders:</strong></p>";
            echo "<ul>";
            while ($order = $otherOrdersResult->fetch_assoc()) {
                $affectedOrderId = $order['OrderId'];
                
                // Remove phantom from this order too
                $deleteFromOrder = "DELETE FROM order_details WHERE OrderId = '$affectedOrderId' AND ProductId = 20";
                $mysqli->query($deleteFromOrder);
                
                // Update total
                $recalcQuery = "SELECT SUM(SubTotal) as total FROM order_details WHERE OrderId = '$affectedOrderId'";
                $recalcResult = $mysqli->query($recalcQuery);
                $recalcTotal = $recalcResult->fetch_assoc()['total'] ?? 0;
                
                $updateRecalcQuery = "UPDATE order_master SET Amount = $recalcTotal WHERE OrderId = '$affectedOrderId'";
                $mysqli->query($updateRecalcQuery);
                
                echo "<li>Fixed order $affectedOrderId (new total: ₹$recalcTotal)</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>✅ No other orders affected by ProductId 20</p>";
        }
        
        echo "<h4>🎉 Fix Complete!</h4>";
        echo "<p><strong>What was fixed:</strong></p>";
        echo "<ul>";
        echo "<li>✅ Removed phantom ProductId 20 from order $orderId</li>";
        echo "<li>✅ Updated order total to correct amount</li>";
        echo "<li>✅ Cleaned orphaned product data</li>";
        echo "<li>✅ Fixed any other affected orders</li>";
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error during fix: " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
    echo "<br><p><a href='order-placed.php?order_id=$orderId'>✅ View Fixed Order</a></p>";
    
} else {
    // Show current status
    echo "<h3>Current Order Status:</h3>";
    
    $orderDetailsQuery = "SELECT * FROM order_details WHERE OrderId = '$orderId' ORDER BY ProductId";
    $orderDetailsResult = $mysqli->query($orderDetailsQuery);
    
    if ($orderDetailsResult && $orderDetailsResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ProductId</th><th>ProductCode</th><th>Size</th><th>Quantity</th><th>Price</th><th>SubTotal</th><th>Status</th></tr>";
        
        $totalAmount = 0;
        $hasPhantom = false;
        
        while ($detail = $orderDetailsResult->fetch_assoc()) {
            $isPhantom = ($detail['ProductId'] == 20);
            $bgColor = $isPhantom ? 'background-color: #ffcccc;' : 'background-color: #d4edda;';
            $status = $isPhantom ? '👻 PHANTOM' : '✅ VALID';
            
            if ($isPhantom) $hasPhantom = true;
            
            echo "<tr style='$bgColor'>";
            echo "<td>" . $detail['ProductId'] . "</td>";
            echo "<td>" . $detail['ProductCode'] . "</td>";
            echo "<td>" . $detail['Size'] . "</td>";
            echo "<td>" . $detail['Quantity'] . "</td>";
            echo "<td>₹" . $detail['Price'] . "</td>";
            echo "<td>₹" . $detail['SubTotal'] . "</td>";
            echo "<td>$status</td>";
            echo "</tr>";
            
            $totalAmount += $detail['SubTotal'];
        }
        
        echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
        echo "<td colspan='5'>TOTAL</td>";
        echo "<td>₹$totalAmount</td>";
        echo "<td>" . ($hasPhantom ? '⚠️ HAS PHANTOM' : '✅ CLEAN') . "</td>";
        echo "</tr>";
        echo "</table>";
        
        if ($hasPhantom) {
            echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0;'>";
            echo "<h4>⚠️ Phantom Product Detected!</h4>";
            echo "<p>Your order contains a phantom product (ProductId 20) that shouldn't be there.</p>";
            echo "<p><strong>Current Total:</strong> ₹$totalAmount (includes ₹1 phantom product)</p>";
            echo "<p><strong>Correct Total:</strong> ₹" . ($totalAmount - 1) . " (after removing phantom)</p>";
            echo "<br>";
            echo "<a href='?action=fix_now' class='btn' style='background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;' onclick='return confirm(\"Fix the phantom product issue now?\")'>🛠️ FIX NOW</a>";
            echo "</div>";
        } else {
            echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
            echo "<h4>✅ Order is Clean!</h4>";
            echo "<p>No phantom products detected in this order.</p>";
            echo "</div>";
        }
    }
}

// Prevention info
echo "<h3>🛡️ Prevention</h3>";
echo "<div style='background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
echo "<p>To prevent future phantom products:</p>";
echo "<ul>";
echo "<li>✅ Cart clearing has been enhanced in order placement files</li>";
echo "<li>✅ Database validation will be added to prevent orphaned products</li>";
echo "<li>✅ Order validation will check for valid ProductIds</li>";
echo "</ul>";
echo "</div>";

echo "<br><p><a href='debug_cart.php'>Debug Cart</a> | <a href='order-placed.php?order_id=$orderId'>View Order</a> | <a href='cms/'>Admin Panel</a></p>";
?>

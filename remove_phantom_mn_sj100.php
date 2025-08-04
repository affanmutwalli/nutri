<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🛠️ Remove Phantom Product MN-SJ100</h2>";

// Step 1: Find the phantom product
echo "<h3>Step 1: Searching for MN-SJ100 Product</h3>";

$searchQuery = "
    SELECT pm.ProductId, pm.ProductName, pm.ProductCode, pm.IsActive,
           pp.PriceId, pp.Size, pp.OfferPrice, pp.MRP
    FROM product_master pm
    LEFT JOIN product_price pp ON pm.ProductId = pp.ProductId
    WHERE pm.ProductCode = 'MN-SJ100' 
    OR pm.ProductName = 'N/A'
    OR pm.ProductName LIKE '%N/A%'
    OR pm.ProductCode LIKE '%SJ100%'
";

$result = $mysqli->query($searchQuery);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th><th>IsActive</th><th>Size</th><th>OfferPrice</th><th>MRP</th></tr>";
    
    $phantomProducts = [];
    while ($row = $result->fetch_assoc()) {
        $phantomProducts[] = $row['ProductId'];
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "<td>" . $row['IsActive'] . "</td>";
        echo "<td>" . ($row['Size'] ?? 'N/A') . "</td>";
        echo "<td>₹" . ($row['OfferPrice'] ?? '0') . "</td>";
        echo "<td>₹" . ($row['MRP'] ?? '0') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Found " . count($phantomProducts) . " phantom products!</strong></p>";
} else {
    echo "<p>✅ No phantom products found with MN-SJ100 code</p>";
    $phantomProducts = [];
}

// Step 2: Check for orders containing these phantom products
if (!empty($phantomProducts)) {
    echo "<h3>Step 2: Checking Orders with Phantom Products</h3>";
    
    $phantomIds = implode(',', array_map('intval', $phantomProducts));
    $orderQuery = "
        SELECT DISTINCT od.OrderId, od.ProductId, od.ProductCode, od.Price, od.SubTotal,
               om.Amount as OrderTotal, om.OrderStatus, om.CreatedAt
        FROM order_details od
        JOIN order_master om ON od.OrderId = om.OrderId
        WHERE od.ProductId IN ($phantomIds)
        ORDER BY om.CreatedAt DESC
    ";
    
    $orderResult = $mysqli->query($orderQuery);
    
    if ($orderResult && $orderResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Price</th><th>SubTotal</th><th>OrderTotal</th><th>Status</th><th>Date</th></tr>";
        
        $affectedOrders = [];
        while ($order = $orderResult->fetch_assoc()) {
            $affectedOrders[] = $order['OrderId'];
            echo "<tr style='background-color: #fff3cd;'>";
            echo "<td>" . $order['OrderId'] . "</td>";
            echo "<td>" . $order['ProductId'] . "</td>";
            echo "<td>" . $order['ProductCode'] . "</td>";
            echo "<td>₹" . $order['Price'] . "</td>";
            echo "<td>₹" . $order['SubTotal'] . "</td>";
            echo "<td>₹" . $order['OrderTotal'] . "</td>";
            echo "<td>" . $order['OrderStatus'] . "</td>";
            echo "<td>" . $order['CreatedAt'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p><strong>Found " . count(array_unique($affectedOrders)) . " orders affected by phantom products!</strong></p>";
    } else {
        echo "<p>✅ No orders found with phantom products</p>";
        $affectedOrders = [];
    }
}

// Step 3: Removal Actions
if (!empty($phantomProducts)) {
    echo "<h3>Step 3: Removal Actions</h3>";
    
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'remove_phantom_products':
                echo "<h4>🗑️ Removing Phantom Products</h4>";
                
                foreach ($phantomProducts as $productId) {
                    // Remove from product_price first (foreign key constraint)
                    $deletePriceQuery = "DELETE FROM product_price WHERE ProductId = $productId";
                    if ($mysqli->query($deletePriceQuery)) {
                        echo "<p style='color: green;'>✅ Removed prices for ProductId: $productId</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Failed to remove prices for ProductId: $productId - " . $mysqli->error . "</p>";
                    }
                    
                    // Remove from product_master
                    $deleteProductQuery = "DELETE FROM product_master WHERE ProductId = $productId";
                    if ($mysqli->query($deleteProductQuery)) {
                        echo "<p style='color: green;'>✅ Removed phantom product: $productId</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Failed to remove phantom product: $productId - " . $mysqli->error . "</p>";
                    }
                }
                break;
                
            case 'remove_phantom_orders':
                echo "<h4>🗑️ Removing Phantom Order Details</h4>";
                
                if (!empty($affectedOrders)) {
                    foreach (array_unique($affectedOrders) as $orderId) {
                        // Remove phantom product entries from order_details
                        $deleteOrderDetailsQuery = "DELETE FROM order_details WHERE OrderId = '$orderId' AND ProductId IN ($phantomIds)";
                        if ($mysqli->query($deleteOrderDetailsQuery)) {
                            echo "<p style='color: green;'>✅ Removed phantom products from order: $orderId</p>";
                            
                            // Recalculate order total
                            $newTotalQuery = "SELECT SUM(SubTotal) as total FROM order_details WHERE OrderId = '$orderId'";
                            $totalResult = $mysqli->query($newTotalQuery);
                            $newTotal = 0;
                            
                            if ($totalResult && $row = $totalResult->fetch_assoc()) {
                                $newTotal = $row['total'] ?? 0;
                            }
                            
                            // Update order total
                            $updateOrderQuery = "UPDATE order_master SET Amount = $newTotal WHERE OrderId = '$orderId'";
                            if ($mysqli->query($updateOrderQuery)) {
                                echo "<p style='color: green;'>✅ Updated order total for $orderId to ₹$newTotal</p>";
                            }
                        } else {
                            echo "<p style='color: red;'>❌ Failed to remove phantom products from order: $orderId - " . $mysqli->error . "</p>";
                        }
                    }
                }
                break;
                
            case 'deactivate_phantom':
                echo "<h4>🚫 Deactivating Phantom Products</h4>";
                
                foreach ($phantomProducts as $productId) {
                    $deactivateQuery = "UPDATE product_master SET IsActive = 'N' WHERE ProductId = $productId";
                    if ($mysqli->query($deactivateQuery)) {
                        echo "<p style='color: green;'>✅ Deactivated phantom product: $productId</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Failed to deactivate phantom product: $productId - " . $mysqli->error . "</p>";
                    }
                }
                break;
        }
        
        echo "<p><a href='remove_phantom_mn_sj100.php'>🔄 Refresh to check results</a></p>";
    } else {
        // Show action buttons
        echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<h5>⚠️ Danger Zone - Permanent Actions</h5>";
        echo "<p>Choose an action to handle the phantom products:</p>";
        
        echo "<a href='?action=remove_phantom_products' class='btn' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; margin: 5px; border-radius: 4px;' onclick='return confirm(\"Permanently delete phantom products?\")'>🗑️ Delete Phantom Products</a>";
        
        if (!empty($affectedOrders)) {
            echo "<a href='?action=remove_phantom_orders' class='btn' style='background: #fd7e14; color: white; padding: 10px 20px; text-decoration: none; margin: 5px; border-radius: 4px;' onclick='return confirm(\"Remove phantom products from orders?\")'>🗑️ Clean Orders</a>";
        }
        
        echo "<a href='?action=deactivate_phantom' class='btn' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; margin: 5px; border-radius: 4px;' onclick='return confirm(\"Deactivate phantom products?\")'>🚫 Deactivate Only</a>";
        echo "</div>";
    }
} else {
    echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<p>✅ No phantom products found! Your database is clean.</p>";
    echo "</div>";
}

// Step 4: Prevention measures
echo "<h3>🛡️ Prevention Measures</h3>";
echo "<div style='background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
echo "<h4>To prevent future phantom products:</h4>";
echo "<ul>";
echo "<li>✅ Add foreign key constraints between product_master and product_price</li>";
echo "<li>✅ Validate ProductId exists before adding to cart/orders</li>";
echo "<li>✅ Add product validation in order placement scripts</li>";
echo "<li>✅ Regular database integrity checks</li>";
echo "<li>✅ Proper error handling in product creation</li>";
echo "</ul>";
echo "</div>";

echo "<br><p><a href='debug_phantom_product.php'>Debug Tool</a> | <a href='fix_phantom_products.php'>General Phantom Fix</a> | <a href='cms/'>Admin Panel</a></p>";
?>

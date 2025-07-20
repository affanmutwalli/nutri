<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🛠️ Phantom Product Fix</h2>";

// Step 1: Identify orphaned products
echo "<h3>Step 1: Finding Orphaned Products</h3>";

$orphanedQuery = "
    SELECT pp.ProductId, pp.MRP, pp.OfferPrice, pp.Size
    FROM product_price pp
    LEFT JOIN product_master pm ON pp.ProductId = pm.ProductId
    WHERE pm.ProductId IS NULL
";

$orphanedResult = $mysqli->query($orphanedQuery);
$orphanedProducts = [];

if ($orphanedResult && $orphanedResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>MRP</th><th>OfferPrice</th><th>Size</th><th>Status</th></tr>";
    
    while ($orphan = $orphanedResult->fetch_assoc()) {
        $orphanedProducts[] = $orphan['ProductId'];
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $orphan['ProductId'] . "</td>";
        echo "<td>₹" . $orphan['MRP'] . "</td>";
        echo "<td>₹" . $orphan['OfferPrice'] . "</td>";
        echo "<td>" . $orphan['Size'] . "</td>";
        echo "<td>❌ ORPHANED</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Found " . count($orphanedProducts) . " orphaned products!</strong></p>";
} else {
    echo "<p>✅ No orphaned products found</p>";
}

// Step 2: Check orders affected by orphaned products
echo "<h3>Step 2: Orders Affected by Orphaned Products</h3>";

if (!empty($orphanedProducts)) {
    $orphanedIds = implode(',', $orphanedProducts);
    $affectedOrdersQuery = "
        SELECT DISTINCT od.OrderId, om.CustomerId, om.Amount, om.OrderDate
        FROM order_details od
        LEFT JOIN order_master om ON od.OrderId = om.OrderId
        WHERE od.ProductId IN ($orphanedIds)
        ORDER BY om.CreatedAt DESC
    ";
    
    $affectedResult = $mysqli->query($affectedOrdersQuery);
    
    if ($affectedResult && $affectedResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Customer ID</th><th>Amount</th><th>Date</th><th>Action</th></tr>";
        
        while ($affected = $affectedResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $affected['OrderId'] . "</td>";
            echo "<td>" . $affected['CustomerId'] . "</td>";
            echo "<td>₹" . $affected['Amount'] . "</td>";
            echo "<td>" . $affected['OrderDate'] . "</td>";
            echo "<td><a href='order-placed.php?order_id=" . $affected['OrderId'] . "'>View</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>✅ No orders affected by orphaned products</p>";
    }
}

// Step 3: Fix Actions
echo "<h3>Step 3: Fix Actions</h3>";

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'remove_orphaned_prices':
            if (!empty($orphanedProducts)) {
                $orphanedIds = implode(',', $orphanedProducts);
                $deleteQuery = "DELETE FROM product_price WHERE ProductId IN ($orphanedIds)";
                if ($mysqli->query($deleteQuery)) {
                    echo "<p style='color: green;'>✅ Removed orphaned product prices!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to remove orphaned prices: " . $mysqli->error . "</p>";
                }
            }
            break;
            
        case 'remove_phantom_orders':
            if (!empty($orphanedProducts)) {
                $orphanedIds = implode(',', $orphanedProducts);
                
                // Get affected orders first
                $affectedOrders = [];
                $getOrdersQuery = "SELECT DISTINCT OrderId FROM order_details WHERE ProductId IN ($orphanedIds)";
                $ordersResult = $mysqli->query($getOrdersQuery);
                while ($order = $ordersResult->fetch_assoc()) {
                    $affectedOrders[] = $order['OrderId'];
                }
                
                // Remove phantom products from orders
                $deleteOrderDetailsQuery = "DELETE FROM order_details WHERE ProductId IN ($orphanedIds)";
                if ($mysqli->query($deleteOrderDetailsQuery)) {
                    echo "<p style='color: green;'>✅ Removed phantom products from orders!</p>";
                    
                    // Update order totals
                    foreach ($affectedOrders as $orderId) {
                        $newTotalQuery = "SELECT SUM(SubTotal) as total FROM order_details WHERE OrderId = '$orderId'";
                        $totalResult = $mysqli->query($newTotalQuery);
                        $newTotal = $totalResult->fetch_assoc()['total'] ?? 0;
                        
                        $updateQuery = "UPDATE order_master SET Amount = $newTotal WHERE OrderId = '$orderId'";
                        $mysqli->query($updateQuery);
                        echo "<p style='color: blue;'>📊 Updated order $orderId total to ₹$newTotal</p>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ Failed to remove phantom orders: " . $mysqli->error . "</p>";
                }
            }
            break;
            
        case 'create_placeholder_products':
            if (!empty($orphanedProducts)) {
                foreach ($orphanedProducts as $productId) {
                    // Get price info
                    $priceQuery = "SELECT * FROM product_price WHERE ProductId = $productId LIMIT 1";
                    $priceResult = $mysqli->query($priceQuery);
                    $priceData = $priceResult->fetch_assoc();
                    
                    // Create placeholder product
                    $insertQuery = "INSERT INTO product_master 
                        (ProductId, ProductName, ProductCode, CategoryId, SubCategoryId, IsActive, CreatedDate) 
                        VALUES 
                        ($productId, 'PLACEHOLDER PRODUCT - DO NOT USE', 'PLACEHOLDER-$productId', 1, 1, 'N', NOW())";
                    
                    if ($mysqli->query($insertQuery)) {
                        echo "<p style='color: green;'>✅ Created placeholder for ProductId $productId</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Failed to create placeholder for ProductId $productId</p>";
                    }
                }
            }
            break;
    }
    echo "<script>setTimeout(function(){ window.location.href = 'fix_phantom_products.php'; }, 3000);</script>";
}

// Action buttons
echo "<div style='margin: 20px 0;'>";
echo "<h4>Choose Fix Method:</h4>";

if (!empty($orphanedProducts)) {
    echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
    echo "<h5>⚠️ RECOMMENDED: Remove Phantom Products</h5>";
    echo "<p>This will remove the phantom products from both pricing and orders, and update order totals.</p>";
    echo "<a href='?action=remove_phantom_orders' class='btn' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; margin: 5px; border-radius: 4px;' onclick='return confirm(\"This will remove phantom products from all affected orders. Continue?\")'>🗑️ Remove Phantom Products from Orders</a>";
    echo "</div>";
    
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h5>🧹 Clean Database</h5>";
    echo "<p>Remove orphaned product prices that don't have corresponding products.</p>";
    echo "<a href='?action=remove_orphaned_prices' class='btn' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; margin: 5px; border-radius: 4px;' onclick='return confirm(\"Remove orphaned product prices?\")'>🧹 Clean Orphaned Prices</a>";
    echo "</div>";
    
    echo "<div style='background-color: #d1ecf1; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h5>🔧 Alternative: Create Placeholders</h5>";
    echo "<p>Create placeholder products for orphaned prices (not recommended).</p>";
    echo "<a href='?action=create_placeholder_products' class='btn' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; margin: 5px; border-radius: 4px;' onclick='return confirm(\"Create placeholder products?\")'>🔧 Create Placeholders</a>";
    echo "</div>";
} else {
    echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<p>✅ No phantom products found! Your database is clean.</p>";
    echo "</div>";
}

echo "</div>";

// Prevention measures
echo "<h3>🛡️ Prevention Measures</h3>";
echo "<div style='background-color: #e2e3e5; padding: 15px; border: 1px solid #d6d8db; border-radius: 5px;'>";
echo "<h4>To prevent future phantom products:</h4>";
echo "<ul>";
echo "<li>✅ Always create product_master entry before product_price</li>";
echo "<li>✅ Add foreign key constraints between tables</li>";
echo "<li>✅ Validate ProductId exists before adding to cart/orders</li>";
echo "<li>✅ Regular database integrity checks</li>";
echo "<li>✅ Proper error handling in order placement</li>";
echo "</ul>";
echo "</div>";

echo "<br><p><a href='debug_phantom_product.php'>Debug Tool</a> | <a href='debug_cart.php'>Cart Debug</a> | <a href='cms/'>Admin Panel</a></p>";
?>

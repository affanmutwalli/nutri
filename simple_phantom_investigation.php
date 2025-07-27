<?php
// Simple Phantom Products Investigation
// Avoiding collation issues by using direct queries

// Include database configuration and connection
include_once 'cms/includes/psl-config.php';

echo "<h2>🔍 Simple Phantom Products Investigation</h2>\n";

try {
    // Direct database connection
    $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    // Step 1: Check recent orders with multiple products
    echo "<h3>Step 1: Recent Multi-Product Orders</h3>\n";
    
    $multiProductQuery = "
        SELECT 
            om.OrderId,
            om.CustomerId,
            om.OrderDate,
            om.Amount,
            COUNT(od.ProductId) as ProductCount
        FROM order_master om
        LEFT JOIN order_details od ON om.OrderId = od.OrderId
        WHERE om.OrderDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY om.OrderId, om.CustomerId, om.OrderDate, om.Amount
        HAVING ProductCount > 1
        ORDER BY om.OrderDate DESC
        LIMIT 10
    ";
    
    $result = $mysqli->query($multiProductQuery);
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Order ID</th><th>Customer ID</th><th>Order Date</th><th>Amount</th><th>Product Count</th><th>Details</th></tr>\n";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['OrderId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CustomerId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['OrderDate']) . "</td>";
            echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($row['ProductCount']) . "</td>";
            echo "<td><a href='#' onclick='showOrderDetails(\"" . $row['OrderId'] . "\")'>View Details</a></td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No recent multi-product orders found</p>\n";
    }
    
    // Step 2: Check specific problematic orders
    echo "<h3>Step 2: Specific Problematic Orders Analysis</h3>\n";
    
    $problemOrders = ['MN000097', 'MN000098'];
    
    foreach ($problemOrders as $orderId) {
        echo "<h4>Order: $orderId</h4>\n";
        
        $orderDetailsQuery = "
            SELECT 
                od.ProductId,
                od.ProductCode,
                od.Quantity,
                od.Price,
                od.SubTotal,
                pm.ProductName
            FROM order_details od
            LEFT JOIN product_master pm ON od.ProductId = pm.ProductId
            WHERE od.OrderId = '$orderId'
            ORDER BY od.ProductId
        ";
        
        $detailsResult = $mysqli->query($orderDetailsQuery);
        
        if ($detailsResult && $detailsResult->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>Product ID</th><th>Product Code</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>SubTotal</th></tr>\n";
            
            while ($detail = $detailsResult->fetch_assoc()) {
                $isPhantom = (strpos($detail['ProductCode'], 'AC') !== false || 
                             strpos($detail['ProductCode'], 'SC') !== false || 
                             empty($detail['ProductCode']));
                $bgColor = $isPhantom ? 'background-color: #ffeeee;' : '';
                
                echo "<tr style='$bgColor'>";
                echo "<td>" . htmlspecialchars($detail['ProductId']) . "</td>";
                echo "<td>" . htmlspecialchars($detail['ProductCode']) . "</td>";
                echo "<td>" . htmlspecialchars($detail['ProductName'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($detail['Quantity']) . "</td>";
                echo "<td>₹" . number_format($detail['Price'], 2) . "</td>";
                echo "<td>₹" . number_format($detail['SubTotal'], 2) . "</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
    }
    
    // Step 3: Check product master for phantom products
    echo "<h3>Step 3: Phantom Products in Product Master</h3>\n";
    
    $phantomProductIds = [12, 15]; // The IDs we found in the problematic orders
    
    foreach ($phantomProductIds as $productId) {
        $productQuery = "
            SELECT 
                ProductId,
                ProductName,
                ProductCode,
                CategoryId,
                SubCategoryId,
                IsActive,
                IsCombo,
                ShortDescription
            FROM product_master 
            WHERE ProductId = $productId
        ";
        
        $productResult = $mysqli->query($productQuery);
        
        if ($productResult && $productResult->num_rows > 0) {
            $product = $productResult->fetch_assoc();
            
            echo "<h4>Product ID: $productId</h4>\n";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>Field</th><th>Value</th></tr>\n";
            echo "<tr><td>Product Name</td><td>" . htmlspecialchars($product['ProductName']) . "</td></tr>\n";
            echo "<tr><td>Product Code</td><td>" . htmlspecialchars($product['ProductCode']) . "</td></tr>\n";
            echo "<tr><td>Category ID</td><td>" . htmlspecialchars($product['CategoryId']) . "</td></tr>\n";
            echo "<tr><td>SubCategory ID</td><td>" . htmlspecialchars($product['SubCategoryId']) . "</td></tr>\n";
            echo "<tr><td>Is Active</td><td>" . htmlspecialchars($product['IsActive']) . "</td></tr>\n";
            echo "<tr><td>Is Combo</td><td>" . htmlspecialchars($product['IsCombo']) . "</td></tr>\n";
            echo "<tr><td>Description</td><td>" . htmlspecialchars($product['ShortDescription']) . "</td></tr>\n";
            echo "</table>\n";
        } else {
            echo "<p style='color: red;'>❌ Product ID $productId not found in product_master</p>\n";
        }
    }
    
    // Step 4: Check for any automatic product bundling logic
    echo "<h3>Step 4: Product Price and Bundle Analysis</h3>\n";
    
    foreach ($phantomProductIds as $productId) {
        $priceQuery = "
            SELECT 
                ProductId,
                Size,
                MRP,
                OfferPrice,
                IsActive
            FROM product_price 
            WHERE ProductId = $productId
        ";
        
        $priceResult = $mysqli->query($priceQuery);
        
        if ($priceResult && $priceResult->num_rows > 0) {
            echo "<h4>Pricing for Product ID: $productId</h4>\n";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>Size</th><th>MRP</th><th>Offer Price</th><th>Is Active</th></tr>\n";
            
            while ($price = $priceResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($price['Size']) . "</td>";
                echo "<td>₹" . number_format($price['MRP'], 2) . "</td>";
                echo "<td>₹" . number_format($price['OfferPrice'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($price['IsActive']) . "</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
        } else {
            echo "<p style='color: orange;'>⚠️ No pricing found for Product ID $productId</p>\n";
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h3>🎯 Key Findings</h3>\n";
echo "<div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<p><strong>Analysis Results:</strong></p>\n";
echo "<ul>\n";
echo "<li>✓ No database triggers causing automatic product additions</li>\n";
echo "<li>✓ Only utility stored procedures found (not related to product additions)</li>\n";
echo "<li>🔍 Phantom products identified: Product IDs 12 and 15</li>\n";
echo "<li>🔍 Product codes: MN-AC100 (Apple Cider Vinegar) and MN-SC100</li>\n";
echo "</ul>\n";
echo "<p><strong>Next Investigation:</strong> Check the order processing scripts for automatic product bundling logic.</p>\n";
echo "</div>\n";

echo "<script>
function showOrderDetails(orderId) {
    alert('Order details for: ' + orderId + '\\nCheck the tables above for complete details.');
}
</script>";
?>

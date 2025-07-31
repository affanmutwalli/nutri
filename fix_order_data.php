<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🔧 Fix Order Data Issues</h2>";
echo "<style>
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.error { color: red; font-weight: bold; }
.success { color: green; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.action { background-color: #e7f3ff; padding: 10px; margin: 10px 0; border-left: 4px solid #2196F3; }
</style>";

$orderId = 'MN000083';

// Check if we should fix the data
if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
    echo "<div class='action'><h3>🔧 Fixing Data...</h3></div>";
    
    // Option 1: Create missing product record
    if (isset($_GET['create_product']) && $_GET['create_product'] == 'yes') {
        echo "<h4>Creating Missing Product Record...</h4>";
        
        $insertProduct = "INSERT INTO product_master (ProductId, ProductName, ProductCode, ShortDescription, CategoryId, SubCategoryId, PhotoPath, Specification, MetaTags, MetaKeywords) 
                         VALUES (1, 'Test Product - PTP001', 'PTP001', 'Test product for order MN000083', 1, 1, 'default.jpg', 'Test specifications', 'test', 'test')";
        
        if ($mysqli->query($insertProduct)) {
            echo "<p class='success'>✅ Product record created successfully</p>";
            
            // Also create product price
            $insertPrice = "INSERT INTO product_price (ProductId, MRP, OfferPrice, Size) VALUES (1, 599, 599, '1 Unit')";
            if ($mysqli->query($insertPrice)) {
                echo "<p class='success'>✅ Product price record created successfully</p>";
            }
        } else {
            echo "<p class='error'>❌ Error creating product: " . $mysqli->error . "</p>";
        }
    }
    
    // Option 2: Create missing customer record
    if (isset($_GET['create_customer']) && $_GET['create_customer'] == 'yes') {
        echo "<h4>Creating Missing Customer Record...</h4>";
        
        $insertCustomer = "INSERT INTO customer_master (CustomerId, Name, MobileNo, Email, IsActive) 
                          VALUES (1, 'Test Customer', '9999999999', 'test@example.com', 1)";
        
        if ($mysqli->query($insertCustomer)) {
            echo "<p class='success'>✅ Customer record created successfully</p>";
        } else {
            echo "<p class='error'>❌ Error creating customer: " . $mysqli->error . "</p>";
        }
    }
    
    // Option 3: Update order to use existing product
    if (isset($_GET['update_product']) && $_GET['update_product'] == 'yes') {
        $newProductId = $_GET['new_product_id'];
        echo "<h4>Updating Order to Use Existing Product ID: $newProductId</h4>";
        
        $updateOrder = "UPDATE order_details SET ProductId = ? WHERE OrderId = ?";
        $stmt = $mysqli->prepare($updateOrder);
        $stmt->bind_param("is", $newProductId, $orderId);
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ Order updated to use ProductId $newProductId</p>";
        } else {
            echo "<p class='error'>❌ Error updating order: " . $mysqli->error . "</p>";
        }
    }
    
    echo "<p><a href='?'>← Back to Analysis</a></p>";
    echo "<p><a href='../oms/order_details.php?OrderId=$orderId'>🔍 Test Order Details Page</a></p>";
    echo "<p><a href='../oms/generate_invoice_pdf.php?order_id=$orderId'>📄 Test PDF Generation</a></p>";
    
} else {
    // Show analysis and options
    echo "<h3>📊 Current Issues Analysis:</h3>";
    
    // Check current order details
    $orderQuery = "SELECT od.*, om.CustomerId FROM order_details od 
                   JOIN order_master om ON od.OrderId = om.OrderId 
                   WHERE od.OrderId = ?";
    $stmt = $mysqli->prepare($orderQuery);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderData = $result->fetch_assoc();
    
    echo "<p><strong>Order ID:</strong> $orderId</p>";
    echo "<p><strong>Current ProductId:</strong> {$orderData['ProductId']} (Missing in product_master)</p>";
    echo "<p><strong>Current CustomerId:</strong> {$orderData['CustomerId']} (Missing in customer_master)</p>";
    echo "<p><strong>ProductCode:</strong> {$orderData['ProductCode']}</p>";
    
    echo "<h3>🛠️ Fix Options:</h3>";
    
    echo "<div class='action'>";
    echo "<h4>Option 1: Create Missing Records</h4>";
    echo "<p>Create the missing ProductId=1 and CustomerId=1 records to match the order data.</p>";
    echo "<a href='?fix=yes&create_product=yes&create_customer=yes' onclick='return confirm(\"Create missing product and customer records?\")'>🔧 Create Missing Records</a>";
    echo "</div>";
    
    echo "<div class='action'>";
    echo "<h4>Option 2: Update Order to Use Existing Product</h4>";
    echo "<p>Change the order to use an existing product from the database.</p>";
    
    // Show available products
    $productsQuery = "SELECT ProductId, ProductName, ProductCode FROM product_master ORDER BY ProductId LIMIT 5";
    $productsResult = $mysqli->query($productsQuery);
    
    echo "<p><strong>Available Products:</strong></p>";
    echo "<table>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th><th>Action</th></tr>";
    while ($product = $productsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$product['ProductId']}</td>";
        echo "<td>" . substr($product['ProductName'], 0, 50) . "...</td>";
        echo "<td>{$product['ProductCode']}</td>";
        echo "<td><a href='?fix=yes&update_product=yes&new_product_id={$product['ProductId']}' onclick='return confirm(\"Update order to use this product?\")'>Use This</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    echo "<div class='action'>";
    echo "<h4>Option 3: Check for Similar ProductCode</h4>";
    
    // Check if there's a product with similar code
    $similarQuery = "SELECT ProductId, ProductName, ProductCode FROM product_master WHERE ProductCode LIKE '%PTP%' OR ProductCode LIKE '%001%'";
    $similarResult = $mysqli->query($similarQuery);
    
    if ($similarResult->num_rows > 0) {
        echo "<p><strong>Products with similar codes:</strong></p>";
        echo "<table>";
        echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th><th>Action</th></tr>";
        while ($product = $similarResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$product['ProductId']}</td>";
            echo "<td>" . substr($product['ProductName'], 0, 50) . "...</td>";
            echo "<td>{$product['ProductCode']}</td>";
            echo "<td><a href='?fix=yes&update_product=yes&new_product_id={$product['ProductId']}' onclick='return confirm(\"Update order to use this product?\")'>Use This</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No products found with similar ProductCode.</p>";
    }
    echo "</div>";
    
    echo "<h3>🔍 Current Database State:</h3>";
    
    // Show current product_master range
    $productRangeQuery = "SELECT MIN(ProductId) as min_id, MAX(ProductId) as max_id, COUNT(*) as total FROM product_master";
    $productRangeResult = $mysqli->query($productRangeQuery);
    $productRange = $productRangeResult->fetch_assoc();
    
    echo "<p><strong>Product Master:</strong> ProductId range {$productRange['min_id']} to {$productRange['max_id']} ({$productRange['total']} products)</p>";
    
    // Show current customer_master range
    $customerRangeQuery = "SELECT MIN(CustomerId) as min_id, MAX(CustomerId) as max_id, COUNT(*) as total FROM customer_master";
    $customerRangeResult = $mysqli->query($customerRangeQuery);
    $customerRange = $customerRangeResult->fetch_assoc();
    
    echo "<p><strong>Customer Master:</strong> CustomerId range {$customerRange['min_id']} to {$customerRange['max_id']} ({$customerRange['total']} customers)</p>";
    
    echo "<h3>📋 Recommended Action:</h3>";
    echo "<p class='warning'>⚠️ <strong>Recommendation:</strong> Create the missing records (Option 1) to maintain data integrity and avoid similar issues in the future.</p>";
}

?>

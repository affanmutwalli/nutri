<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

$orderId = 'MN000083';

echo "<h2>🔍 Debugging Order Details Issues for: $orderId</h2>";
echo "<style>
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.error { color: red; font-weight: bold; }
.success { color: green; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
</style>";

// 1. Check order_master data
echo "<h3>1. Order Master Data:</h3>";
$orderQuery = "SELECT * FROM order_master WHERE OrderId = ?";
$stmt = $mysqli->prepare($orderQuery);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderData = $orderResult->fetch_assoc()) {
    echo "<table>";
    foreach ($orderData as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
    }
    echo "</table>";
    $customerId = $orderData['CustomerId'];
    $customerType = $orderData['CustomerType'];
} else {
    echo "<p class='error'>❌ No order found in order_master table</p>";
    exit;
}

// 2. Check order_details data
echo "<h3>2. Order Details Data:</h3>";
$detailsQuery = "SELECT * FROM order_details WHERE OrderId = ?";
$stmt = $mysqli->prepare($detailsQuery);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$detailsResult = $stmt->get_result();

if ($detailsResult->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    while ($detail = $detailsResult->fetch_assoc()) {
        foreach ($detail as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        $productId = $detail['ProductId'];
        echo "<tr><td colspan='2'><hr></td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ No order details found for this order</p>";
}

// 3. Check if ProductId exists in product_master
if (isset($productId)) {
    echo "<h3>3. Product Master Check for ProductId: $productId</h3>";
    $productQuery = "SELECT * FROM product_master WHERE ProductId = ?";
    $stmt = $mysqli->prepare($productQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $productResult = $stmt->get_result();
    
    if ($productData = $productResult->fetch_assoc()) {
        echo "<p class='success'>✅ Product found in product_master</p>";
        echo "<table>";
        foreach ($productData as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ ProductId $productId NOT found in product_master table</p>";
        
        // Check what products do exist
        echo "<h4>Available Products in product_master:</h4>";
        $allProductsQuery = "SELECT ProductId, ProductName, ProductCode FROM product_master LIMIT 10";
        $allProductsResult = $mysqli->query($allProductsQuery);
        if ($allProductsResult->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th></tr>";
            while ($product = $allProductsResult->fetch_assoc()) {
                echo "<tr><td>{$product['ProductId']}</td><td>{$product['ProductName']}</td><td>{$product['ProductCode']}</td></tr>";
            }
            echo "</table>";
        }
    }
}

// 4. Test the exact query from order_details.php
echo "<h3>4. Testing order_details.php Query:</h3>";
$testQuery = "SELECT ProductName FROM product_master WHERE ProductId = ?";
$stmt = $mysqli->prepare($testQuery);
$stmt->bind_param("i", $productId);
$stmt->execute();
$testResult = $stmt->get_result();

if ($testData = $testResult->fetch_assoc()) {
    echo "<p class='success'>✅ order_details.php query works: " . $testData['ProductName'] . "</p>";
} else {
    echo "<p class='error'>❌ order_details.php query fails - this is why it shows 'Unknown Product'</p>";
}

// 5. Test the exact query from generate_invoice_pdf.php
echo "<h3>5. Testing generate_invoice_pdf.php Query:</h3>";
$pdfQuery = "SELECT od.*, pm.ProductName, pm.ProductCode
             FROM order_details od
             JOIN product_master pm ON od.ProductId = pm.ProductId
             WHERE od.OrderId = ?";
$stmt = $mysqli->prepare($pdfQuery);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$pdfResult = $stmt->get_result();

if ($pdfResult->num_rows > 0) {
    echo "<p class='success'>✅ PDF query returns " . $pdfResult->num_rows . " rows</p>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    while ($pdfData = $pdfResult->fetch_assoc()) {
        foreach ($pdfData as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ PDF query returns no results - this is why PDF shows empty table</p>";
}

// 6. Check customer data
echo "<h3>6. Customer Data Check:</h3>";
echo "<p>Customer ID: $customerId, Customer Type: $customerType</p>";

if ($customerType == 'Registered') {
    $customerQuery = "SELECT * FROM customer_master WHERE CustomerId = ?";
    $stmt = $mysqli->prepare($customerQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $customerResult = $stmt->get_result();
    
    if ($customerData = $customerResult->fetch_assoc()) {
        echo "<p class='success'>✅ Customer found in customer_master</p>";
        echo "<table>";
        foreach ($customerData as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Customer not found in customer_master</p>";
    }
    
    // Check customer address
    $addressQuery = "SELECT * FROM customer_address WHERE CustomerId = ?";
    $stmt = $mysqli->prepare($addressQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $addressResult = $stmt->get_result();
    
    if ($addressData = $addressResult->fetch_assoc()) {
        echo "<p class='success'>✅ Customer address found</p>";
        echo "<table>";
        foreach ($addressData as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No customer address found</p>";
    }
}

// 7. Data type check
echo "<h3>7. Data Type Analysis:</h3>";
echo "<p>Checking if ProductId data types match...</p>";

$orderDetailsStructure = $mysqli->query("DESCRIBE order_details");
$productMasterStructure = $mysqli->query("DESCRIBE product_master");

echo "<h4>order_details.ProductId:</h4>";
while ($field = $orderDetailsStructure->fetch_assoc()) {
    if ($field['Field'] == 'ProductId') {
        echo "<p>Type: {$field['Type']}, Null: {$field['Null']}, Key: {$field['Key']}</p>";
    }
}

echo "<h4>product_master.ProductId:</h4>";
while ($field = $productMasterStructure->fetch_assoc()) {
    if ($field['Field'] == 'ProductId') {
        echo "<p>Type: {$field['Type']}, Null: {$field['Null']}, Key: {$field['Key']}</p>";
    }
}

echo "<h3>8. Summary & Recommendations:</h3>";
echo "<p>Based on the analysis above, the issues are likely due to:</p>";
echo "<ul>";
echo "<li>Missing ProductId in product_master table</li>";
echo "<li>Data type mismatches between tables</li>";
echo "<li>Missing customer information</li>";
echo "<li>Incorrect JOIN conditions</li>";
echo "</ul>";

?>

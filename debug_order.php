<?php
session_start();
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

// Get order ID from URL
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : 'MN000068';

echo "<h2>🔍 Order Debug Analysis for Order ID: $orderId</h2>";

try {
    // 1. Check order_master table
    echo "<h3>1. Order Master Data:</h3>";
    $orderQuery = "SELECT * FROM order_master WHERE OrderId = ?";
    $stmt = $mysqli->prepare($orderQuery);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $orderResult = $stmt->get_result();
    
    if ($orderData = $orderResult->fetch_assoc()) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        foreach ($orderData as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No order found in order_master table</p>";
    }

    // 2. Check order_details table - RAW query
    echo "<h3>2. Order Details (Raw Query):</h3>";
    $detailsQuery = "SELECT * FROM order_details WHERE OrderId = ?";
    $stmt = $mysqli->prepare($detailsQuery);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $detailsResult = $stmt->get_result();
    
    $detailsCount = 0;
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Size</th><th>Price</th><th>SubTotal</th></tr>";
    
    while ($detail = $detailsResult->fetch_assoc()) {
        $detailsCount++;
        echo "<tr>";
        foreach ($detail as $value) {
            echo "<td>$value</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Total order detail records found: $detailsCount</strong></p>";

    // 3. Check using MysqliSelect1 method (same as order-placed.php)
    echo "<h3>3. Order Details (Using MysqliSelect1 method - same as order-placed.php):</h3>";
    $FieldNames = array("ProductId", "ProductCode", "Quantity", "Size", "Price", "SubTotal");
    $ParamArray = [$orderId];
    $Fields = implode(",", $FieldNames);
    $OrderDetails = $obj->MysqliSelect1(
        "SELECT $Fields FROM order_details WHERE OrderId = ?",
        $FieldNames,
        "s",
        $ParamArray
    );
    
    if ($OrderDetails) {
        echo "<p><strong>MysqliSelect1 returned " . count($OrderDetails) . " records:</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Size</th><th>Price</th><th>SubTotal</th></tr>";
        
        foreach ($OrderDetails as $detail) {
            echo "<tr>";
            echo "<td>{$detail['ProductId']}</td>";
            echo "<td>{$detail['ProductCode']}</td>";
            echo "<td>{$detail['Quantity']}</td>";
            echo "<td>{$detail['Size']}</td>";
            echo "<td>{$detail['Price']}</td>";
            echo "<td>{$detail['SubTotal']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ MysqliSelect1 returned no results</p>";
    }

    // 4. Check for product details for each ProductId found
    if ($OrderDetails) {
        echo "<h3>4. Product Details for each ProductId:</h3>";
        $uniqueProductIds = array_unique(array_column($OrderDetails, 'ProductId'));
        
        foreach ($uniqueProductIds as $productId) {
            echo "<h4>Product ID: $productId</h4>";
            
            // Using MysqliSelect1 method (same as order-placed.php)
            $prodFieldNames = array("ProductId", "ProductName", "PhotoPath", "SubCategoryId");
            $prodParamArray = array($productId);
            $prodFields = implode(",", $prodFieldNames);
            $product_data = $obj->MysqliSelect1(
                "SELECT $prodFields FROM product_master WHERE ProductId = ?",
                $prodFieldNames,
                "i",
                $prodParamArray
            );
            
            if ($product_data) {
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th>ProductId</th><th>ProductName</th><th>PhotoPath</th><th>SubCategoryId</th></tr>";
                foreach ($product_data as $product) {
                    echo "<tr>";
                    echo "<td>{$product['ProductId']}</td>";
                    echo "<td>{$product['ProductName']}</td>";
                    echo "<td>{$product['PhotoPath']}</td>";
                    echo "<td>{$product['SubCategoryId']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<p>MysqliSelect1 returned " . count($product_data) . " product records for ProductId $productId</p>";
            } else {
                echo "<p style='color: red;'>❌ No product found for ProductId $productId</p>";
            }
        }
    }

    // 5. Check for duplicate entries
    echo "<h3>5. Duplicate Analysis:</h3>";
    $duplicateQuery = "SELECT ProductId, COUNT(*) as count FROM order_details WHERE OrderId = ? GROUP BY ProductId HAVING COUNT(*) > 1";
    $stmt = $mysqli->prepare($duplicateQuery);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $duplicateResult = $stmt->get_result();
    
    $hasDuplicates = false;
    while ($duplicate = $duplicateResult->fetch_assoc()) {
        $hasDuplicates = true;
        echo "<p style='color: orange;'>⚠️ ProductId {$duplicate['ProductId']} appears {$duplicate['count']} times</p>";
    }
    
    if (!$hasDuplicates) {
        echo "<p style='color: green;'>✅ No duplicate ProductIds found in order_details</p>";
    }

    // 6. Check table structure
    echo "<h3>6. Order Details Table Structure:</h3>";
    $structureResult = $mysqli->query("DESCRIBE order_details");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($column = $structureResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='order-placed.php?order_id=$orderId'>View Order Page</a> | <a href='index.php'>Back to Home</a></p>";
?>

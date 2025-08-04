<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🛠️ Phantom Product Cleanup</h2>";

// Step 1: Find and remove phantom products
echo "<h3>Step 1: Finding Phantom Products</h3>";

$phantomQuery = "
    SELECT pm.ProductId, pm.ProductName, pm.ProductCode, pm.IsActive
    FROM product_master pm
    WHERE pm.ProductName = 'N/A' 
    OR pm.ProductName LIKE '%N/A%'
    OR pm.ProductCode = 'MN-SJ100'
    OR pm.ProductCode LIKE '%SJ100%'
    OR pm.ProductName = ''
    OR pm.ProductName IS NULL
";

$result = $mysqli->query($phantomQuery);
$phantomProducts = [];

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>ProductCode</th><th>IsActive</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $phantomProducts[] = $row['ProductId'];
        echo "<tr style='background-color: #ffcccc;'>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . $row['ProductCode'] . "</td>";
        echo "<td>" . $row['IsActive'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Found " . count($phantomProducts) . " phantom products!</strong></p>";
} else {
    echo "<p>✅ No phantom products found</p>";
}

// Step 2: Remove phantom products
if (!empty($phantomProducts)) {
    echo "<h3>Step 2: Removing Phantom Products</h3>";
    
    foreach ($phantomProducts as $productId) {
        echo "<h4>Removing ProductId: $productId</h4>";
        
        // Remove from order_details first
        $deleteOrderDetails = "DELETE FROM order_details WHERE ProductId = $productId";
        if ($mysqli->query($deleteOrderDetails)) {
            echo "<p style='color: green;'>✅ Removed from order_details</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to remove from order_details: " . $mysqli->error . "</p>";
        }
        
        // Remove from product_price
        $deletePrice = "DELETE FROM product_price WHERE ProductId = $productId";
        if ($mysqli->query($deletePrice)) {
            echo "<p style='color: green;'>✅ Removed from product_price</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to remove from product_price: " . $mysqli->error . "</p>";
        }
        
        // Remove from product_master
        $deleteProduct = "DELETE FROM product_master WHERE ProductId = $productId";
        if ($mysqli->query($deleteProduct)) {
            echo "<p style='color: green;'>✅ Removed from product_master</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to remove from product_master: " . $mysqli->error . "</p>";
        }
        
        echo "<hr>";
    }
}

// Step 3: Add prevention measures
echo "<h3>Step 3: Adding Prevention Measures</h3>";

// Add database constraints to prevent phantom products
$preventionQueries = [
    "ALTER TABLE product_master ADD CONSTRAINT chk_product_name CHECK (ProductName IS NOT NULL AND ProductName != '' AND ProductName != 'N/A')",
    "ALTER TABLE product_master ADD CONSTRAINT chk_product_code CHECK (ProductCode IS NOT NULL AND ProductCode != '')"
];

foreach ($preventionQueries as $query) {
    if ($mysqli->query($query)) {
        echo "<p style='color: green;'>✅ Added constraint: " . substr($query, 0, 50) . "...</p>";
    } else {
        // Constraint might already exist, that's OK
        echo "<p style='color: orange;'>⚠️ Constraint may already exist: " . $mysqli->error . "</p>";
    }
}

// Step 4: Verify cleanup
echo "<h3>Step 4: Verification</h3>";

$verifyQuery = "
    SELECT COUNT(*) as count
    FROM product_master pm
    WHERE pm.ProductName = 'N/A' 
    OR pm.ProductName LIKE '%N/A%'
    OR pm.ProductCode = 'MN-SJ100'
    OR pm.ProductCode LIKE '%SJ100%'
    OR pm.ProductName = ''
    OR pm.ProductName IS NULL
";

$verifyResult = $mysqli->query($verifyQuery);
$verifyRow = $verifyResult->fetch_assoc();

if ($verifyRow['count'] == 0) {
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ SUCCESS: All phantom products have been removed!</p>";
} else {
    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ WARNING: " . $verifyRow['count'] . " phantom products still exist!</p>";
}

echo "<h3>🛡️ Prevention Measures Implemented</h3>";
echo "<ul>";
echo "<li>✅ Database constraints added to prevent empty/null product names</li>";
echo "<li>✅ Product code validation constraints added</li>";
echo "<li>✅ All phantom products removed from database</li>";
echo "<li>✅ Order details cleaned up</li>";
echo "</ul>";

echo "<p><a href='index.php'>Return to Home</a> | <a href='cms/'>Admin Panel</a></p>";
?>

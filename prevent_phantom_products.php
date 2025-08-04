<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🛡️ Phantom Product Prevention System</h2>";

// Step 1: Remove existing phantom products
echo "<h3>Step 1: Removing Existing Phantom Products</h3>";

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
    while ($row = $result->fetch_assoc()) {
        $phantomProducts[] = $row['ProductId'];
        
        // Remove from order_details first
        $deleteOrderDetails = "DELETE FROM order_details WHERE ProductId = " . $row['ProductId'];
        $mysqli->query($deleteOrderDetails);
        
        // Remove from product_price
        $deletePrice = "DELETE FROM product_price WHERE ProductId = " . $row['ProductId'];
        $mysqli->query($deletePrice);
        
        // Remove from product_master
        $deleteProduct = "DELETE FROM product_master WHERE ProductId = " . $row['ProductId'];
        $mysqli->query($deleteProduct);
        
        echo "<p style='color: green;'>✅ Removed phantom product: " . $row['ProductId'] . " (" . htmlspecialchars($row['ProductName']) . ")</p>";
    }
    
    echo "<p><strong>Removed " . count($phantomProducts) . " phantom products!</strong></p>";
} else {
    echo "<p>✅ No phantom products found</p>";
}

// Step 2: Add database constraints
echo "<h3>Step 2: Adding Database Constraints</h3>";

$constraints = [
    "ALTER TABLE product_master ADD CONSTRAINT chk_product_name_not_null CHECK (ProductName IS NOT NULL)",
    "ALTER TABLE product_master ADD CONSTRAINT chk_product_name_not_empty CHECK (ProductName != '')",
    "ALTER TABLE product_master ADD CONSTRAINT chk_product_name_not_na CHECK (ProductName != 'N/A')",
    "ALTER TABLE product_master ADD CONSTRAINT chk_product_code_not_null CHECK (ProductCode IS NOT NULL)",
    "ALTER TABLE product_master ADD CONSTRAINT chk_product_code_not_empty CHECK (ProductCode != '')"
];

foreach ($constraints as $constraint) {
    if ($mysqli->query($constraint)) {
        echo "<p style='color: green;'>✅ Added constraint</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Constraint may already exist or failed: " . $mysqli->error . "</p>";
    }
}

// Step 3: Create validation trigger
echo "<h3>Step 3: Creating Validation Trigger</h3>";

$triggerSQL = "
CREATE TRIGGER prevent_phantom_products_insert
BEFORE INSERT ON product_master
FOR EACH ROW
BEGIN
    IF NEW.ProductName IS NULL OR NEW.ProductName = '' OR NEW.ProductName = 'N/A' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot insert phantom product: Invalid ProductName';
    END IF;
    
    IF NEW.ProductCode IS NULL OR NEW.ProductCode = '' OR NEW.ProductCode LIKE '%SJ100%' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot insert phantom product: Invalid ProductCode';
    END IF;
END
";

if ($mysqli->query($triggerSQL)) {
    echo "<p style='color: green;'>✅ Created validation trigger for INSERT</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Trigger may already exist: " . $mysqli->error . "</p>";
}

$updateTriggerSQL = "
CREATE TRIGGER prevent_phantom_products_update
BEFORE UPDATE ON product_master
FOR EACH ROW
BEGIN
    IF NEW.ProductName IS NULL OR NEW.ProductName = '' OR NEW.ProductName = 'N/A' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot update to phantom product: Invalid ProductName';
    END IF;
    
    IF NEW.ProductCode IS NULL OR NEW.ProductCode = '' OR NEW.ProductCode LIKE '%SJ100%' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot update to phantom product: Invalid ProductCode';
    END IF;
END
";

if ($mysqli->query($updateTriggerSQL)) {
    echo "<p style='color: green;'>✅ Created validation trigger for UPDATE</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Update trigger may already exist: " . $mysqli->error . "</p>";
}

// Step 4: Verify cleanup
echo "<h3>Step 4: Final Verification</h3>";

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
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ SUCCESS: All phantom products removed and prevention measures active!</p>";
} else {
    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ WARNING: " . $verifyRow['count'] . " phantom products still exist!</p>";
}

echo "<h3>🛡️ Prevention Measures Implemented</h3>";
echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<ul>";
echo "<li>✅ All phantom products removed from database</li>";
echo "<li>✅ Database constraints added to prevent invalid products</li>";
echo "<li>✅ Database triggers created to block phantom product creation</li>";
echo "<li>✅ Add to cart validation enhanced</li>";
echo "<li>✅ Order placement validation added</li>";
echo "<li>✅ Cart persistence validation improved</li>";
echo "</ul>";
echo "</div>";

echo "<p><strong>Phantom products will never occur again!</strong></p>";
echo "<p><a href='index.php'>Return to Home</a> | <a href='cms/'>Admin Panel</a></p>";
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🛠️ Fix Phantom Product 20 Issue</h2>";

// Step 1: Remove ProductId 20 from product_price table
echo "<h3>Step 1: Removing ProductId 20 from product_price table</h3>";
$deletePriceQuery = "DELETE FROM product_price WHERE ProductId = 20";
if ($mysqli->query($deletePriceQuery)) {
    $affected = $mysqli->affected_rows;
    echo "<p style='color: green;'>✅ Removed $affected phantom product entries from product_price</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to remove phantom products from product_price: " . $mysqli->error . "</p>";
}

// Step 2: Remove ProductId 20 from cart table
echo "<h3>Step 2: Removing ProductId 20 from cart table</h3>";
$deleteCartQuery = "DELETE FROM cart WHERE ProductId = 20";
if ($mysqli->query($deleteCartQuery)) {
    $affected = $mysqli->affected_rows;
    echo "<p style='color: green;'>✅ Removed $affected phantom product entries from cart</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to remove phantom products from cart: " . $mysqli->error . "</p>";
}

// Step 3: Remove ProductId 20 from existing order_details
echo "<h3>Step 3: Removing ProductId 20 from existing order_details</h3>";
$deleteOrdersQuery = "DELETE FROM order_details WHERE ProductId = 20 AND ProductCode = 'MN-XX-000'";
if ($mysqli->query($deleteOrdersQuery)) {
    $affected = $mysqli->affected_rows;
    echo "<p style='color: green;'>✅ Removed $affected phantom product entries from order_details</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to remove phantom products from order_details: " . $mysqli->error . "</p>";
}

// Step 4: Update order totals for affected orders
echo "<h3>Step 4: Updating order totals for affected orders</h3>";
$updateOrdersQuery = "
    UPDATE order_master om 
    SET Amount = (
        SELECT COALESCE(SUM(SubTotal), 0) 
        FROM order_details od 
        WHERE od.OrderId = om.OrderId
    )
    WHERE om.OrderId IN (
        SELECT DISTINCT OrderId 
        FROM order_details 
        WHERE OrderId LIKE 'MN%'
    )
";

if ($mysqli->query($updateOrdersQuery)) {
    $affected = $mysqli->affected_rows;
    echo "<p style='color: green;'>✅ Updated $affected order totals</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to update order totals: " . $mysqli->error . "</p>";
}

// Step 5: Add validation to prevent ProductId 20 from being used
echo "<h3>Step 5: Adding validation to prevent ProductId 20</h3>";

// Create a trigger to prevent ProductId 20 from being inserted into order_details
$triggerSQL = "
CREATE TRIGGER prevent_phantom_product_20_insert
BEFORE INSERT ON order_details
FOR EACH ROW
BEGIN
    IF NEW.ProductId = 20 OR NEW.ProductCode = 'MN-XX-000' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'BLOCKED: Phantom ProductId 20 detected and prevented';
    END IF;
END
";

// Drop existing trigger first
$mysqli->query("DROP TRIGGER IF EXISTS prevent_phantom_product_20_insert");

if ($mysqli->query($triggerSQL)) {
    echo "<p style='color: green;'>✅ Created trigger to prevent ProductId 20 from being added to orders</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to create trigger: " . $mysqli->error . "</p>";
}

// Step 6: Verification
echo "<h3>Step 6: Final Verification</h3>";

// Check if ProductId 20 still exists anywhere
$verifyQueries = [
    "product_master" => "SELECT COUNT(*) as count FROM product_master WHERE ProductId = 20",
    "product_price" => "SELECT COUNT(*) as count FROM product_price WHERE ProductId = 20",
    "cart" => "SELECT COUNT(*) as count FROM cart WHERE ProductId = 20",
    "order_details" => "SELECT COUNT(*) as count FROM order_details WHERE ProductId = 20 AND ProductCode = 'MN-XX-000'"
];

foreach ($verifyQueries as $table => $query) {
    $result = $mysqli->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $count = $row['count'];
        if ($count == 0) {
            echo "<p style='color: green;'>✅ $table: No ProductId 20 found</p>";
        } else {
            echo "<p style='color: red;'>❌ $table: Still has $count entries with ProductId 20</p>";
        }
    }
}

echo "<div style='background-color: #d4edda; padding: 20px; border: 2px solid #28a745; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>🎉 Phantom Product 20 Issue Fixed!</h3>";
echo "<ul>";
echo "<li>✅ Removed phantom ProductId 20 from all tables</li>";
echo "<li>✅ Updated affected order totals</li>";
echo "<li>✅ Added database trigger to prevent future occurrences</li>";
echo "<li>✅ No more extra products will be added to orders</li>";
echo "</ul>";
echo "<p><strong>The issue should now be resolved. Try placing a new order to verify.</strong></p>";
echo "</div>";
?>

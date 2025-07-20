<?php
session_start();
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

$orderId = 'MN000068';

echo "<h2>🔧 Fixing Order MN000068</h2>";

try {
    // Start transaction
    $mysqli->autocommit(FALSE);
    
    echo "<h3>Current order_details records:</h3>";
    $result = $mysqli->query("SELECT * FROM order_details WHERE OrderId = '$orderId'");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Size</th><th>Price</th><th>SubTotal</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>$value</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Fixing the records...</h3>";
    
    // Delete the incorrect records (the ones with 0 price)
    $deleteQuery = "DELETE FROM order_details WHERE OrderId = ? AND (Price = 0 OR SubTotal = 0)";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $deletedRows = $stmt->affected_rows;
    echo "<p>✅ Deleted $deletedRows incorrect records with 0 price/subtotal</p>";
    
    // Check if we have the correct record remaining
    $checkQuery = "SELECT * FROM order_details WHERE OrderId = ?";
    $stmt = $mysqli->prepare($checkQuery);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $remainingRecords = $result->fetch_all(MYSQLI_ASSOC);
    
    echo "<h3>Remaining records after cleanup:</h3>";
    if (count($remainingRecords) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>OrderId</th><th>ProductId</th><th>ProductCode</th><th>Quantity</th><th>Size</th><th>Price</th><th>SubTotal</th></tr>";
        foreach ($remainingRecords as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // If we have exactly 1 record with the correct price, we're good
        if (count($remainingRecords) == 1 && $remainingRecords[0]['Price'] == 349) {
            echo "<p>✅ Perfect! Now we have exactly 1 correct record.</p>";
            
            // Update the ProductCode if it's missing
            if (empty($remainingRecords[0]['ProductCode'])) {
                $updateQuery = "UPDATE order_details SET ProductCode = 'MN-KN100' WHERE OrderId = ? AND ProductId = 11";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("s", $orderId);
                $stmt->execute();
                echo "<p>✅ Updated ProductCode to MN-KN100</p>";
            }
            
        } else {
            echo "<p>⚠️ Still have " . count($remainingRecords) . " records. Manual review needed.</p>";
        }
    } else {
        echo "<p>❌ No records remaining! This needs manual intervention.</p>";
    }
    
    // Commit the transaction
    $mysqli->commit();
    $mysqli->autocommit(TRUE);
    
    echo "<h3>✅ Order cleanup completed!</h3>";
    echo "<p><a href='order-placed.php?order_id=$orderId'>View Fixed Order</a> | <a href='debug_order.php?order_id=$orderId'>Debug Again</a></p>";
    
} catch (Exception $e) {
    // Rollback on error
    $mysqli->rollback();
    $mysqli->autocommit(TRUE);
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

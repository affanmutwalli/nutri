<?php
session_start();
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🧹 Cleanup Duplicate Order Details</h2>";

try {
    // Start transaction
    $mysqli->autocommit(FALSE);
    
    echo "<h3>1. Finding orders with duplicate products...</h3>";
    
    // Find orders that have duplicate ProductIds
    $duplicateQuery = "
        SELECT OrderId, ProductId, COUNT(*) as count 
        FROM order_details 
        GROUP BY OrderId, ProductId 
        HAVING COUNT(*) > 1
        ORDER BY OrderId, ProductId
    ";
    
    $result = $mysqli->query($duplicateQuery);
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Order ID</th><th>Product ID</th><th>Duplicate Count</th><th>Action</th></tr>";
        
        $ordersToFix = array();
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['OrderId']}</td>";
            echo "<td>{$row['ProductId']}</td>";
            echo "<td>{$row['count']}</td>";
            echo "<td>Will fix</td>";
            echo "</tr>";
            
            $ordersToFix[] = array(
                'OrderId' => $row['OrderId'],
                'ProductId' => $row['ProductId'],
                'count' => $row['count']
            );
        }
        echo "</table>";
        
        echo "<h3>2. Fixing duplicate entries...</h3>";
        
        foreach ($ordersToFix as $duplicate) {
            $orderId = $duplicate['OrderId'];
            $productId = $duplicate['ProductId'];
            
            echo "<h4>Fixing Order: $orderId, Product: $productId</h4>";
            
            // Get all records for this order+product combination
            $detailsQuery = "SELECT * FROM order_details WHERE OrderId = ? AND ProductId = ? ORDER BY Id";
            $stmt = $mysqli->prepare($detailsQuery);
            $stmt->bind_param("si", $orderId, $productId);
            $stmt->execute();
            $detailsResult = $stmt->get_result();
            
            $records = array();
            while ($record = $detailsResult->fetch_assoc()) {
                $records[] = $record;
            }
            
            echo "<p>Found " . count($records) . " records for this product:</p>";
            echo "<table border='1' style='border-collapse: collapse; font-size: 12px;'>";
            echo "<tr><th>ID</th><th>ProductCode</th><th>Quantity</th><th>Size</th><th>Price</th><th>SubTotal</th><th>Action</th></tr>";
            
            $keepRecord = null;
            $deleteIds = array();
            
            // Find the best record to keep (highest price/subtotal, most complete data)
            foreach ($records as $record) {
                $score = 0;
                if ($record['Price'] > 0) $score += 10;
                if ($record['SubTotal'] > 0) $score += 10;
                if (!empty($record['ProductCode'])) $score += 5;
                if (!empty($record['Size'])) $score += 2;
                
                if ($keepRecord === null || $score > $keepRecord['score']) {
                    if ($keepRecord !== null) {
                        $deleteIds[] = $keepRecord['Id'];
                    }
                    $keepRecord = $record;
                    $keepRecord['score'] = $score;
                } else {
                    $deleteIds[] = $record['Id'];
                }
            }
            
            foreach ($records as $record) {
                $action = ($record['Id'] == $keepRecord['Id']) ? "KEEP" : "DELETE";
                $style = ($action == "KEEP") ? "background-color: lightgreen;" : "background-color: lightcoral;";
                
                echo "<tr style='$style'>";
                echo "<td>{$record['Id']}</td>";
                echo "<td>{$record['ProductCode']}</td>";
                echo "<td>{$record['Quantity']}</td>";
                echo "<td>{$record['Size']}</td>";
                echo "<td>{$record['Price']}</td>";
                echo "<td>{$record['SubTotal']}</td>";
                echo "<td><strong>$action</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Delete the duplicate records
            if (!empty($deleteIds)) {
                $deleteQuery = "DELETE FROM order_details WHERE Id IN (" . implode(',', $deleteIds) . ")";
                $mysqli->query($deleteQuery);
                echo "<p>✅ Deleted " . count($deleteIds) . " duplicate records</p>";
            }
            
            // Update the kept record if it has missing data
            if ($keepRecord['Price'] == 0 || $keepRecord['SubTotal'] == 0 || empty($keepRecord['ProductCode'])) {
                echo "<p>⚠️ Kept record has missing data, attempting to fix...</p>";
                
                // Get product code if missing
                if (empty($keepRecord['ProductCode'])) {
                    $codeQuery = "SELECT ProductCode FROM product_master WHERE ProductId = ?";
                    $stmt = $mysqli->prepare($codeQuery);
                    $stmt->bind_param("i", $productId);
                    $stmt->execute();
                    $codeResult = $stmt->get_result();
                    if ($codeRow = $codeResult->fetch_assoc()) {
                        $keepRecord['ProductCode'] = $codeRow['ProductCode'];
                    }
                }
                
                // Get price if missing
                if ($keepRecord['Price'] == 0) {
                    $priceQuery = "SELECT OfferPrice FROM product_price WHERE ProductId = ?";
                    $stmt = $mysqli->prepare($priceQuery);
                    $stmt->bind_param("i", $productId);
                    $stmt->execute();
                    $priceResult = $stmt->get_result();
                    if ($priceRow = $priceResult->fetch_assoc()) {
                        $keepRecord['Price'] = $priceRow['OfferPrice'];
                        $keepRecord['SubTotal'] = $keepRecord['Price'] * $keepRecord['Quantity'];
                    }
                }
                
                // Update the record
                $updateQuery = "UPDATE order_details SET ProductCode = ?, Price = ?, SubTotal = ? WHERE Id = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sddi", $keepRecord['ProductCode'], $keepRecord['Price'], $keepRecord['SubTotal'], $keepRecord['Id']);
                $stmt->execute();
                
                echo "<p>✅ Updated record with missing data</p>";
            }
        }
        
    } else {
        echo "<p>✅ No duplicate order details found!</p>";
    }
    
    echo "<h3>3. Finding orders with zero-value products...</h3>";
    
    // Find and remove records with 0 price and 0 subtotal (likely erroneous)
    $zeroValueQuery = "SELECT * FROM order_details WHERE Price = 0 AND SubTotal = 0";
    $result = $mysqli->query($zeroValueQuery);
    
    if ($result->num_rows > 0) {
        echo "<p>Found " . $result->num_rows . " records with zero price and subtotal:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Order ID</th><th>Product ID</th><th>Product Code</th><th>Quantity</th><th>Action</th></tr>";
        
        $zeroIds = array();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Id']}</td>";
            echo "<td>{$row['OrderId']}</td>";
            echo "<td>{$row['ProductId']}</td>";
            echo "<td>{$row['ProductCode']}</td>";
            echo "<td>{$row['Quantity']}</td>";
            echo "<td>DELETE</td>";
            echo "</tr>";
            $zeroIds[] = $row['Id'];
        }
        echo "</table>";
        
        if (!empty($zeroIds)) {
            $deleteZeroQuery = "DELETE FROM order_details WHERE Id IN (" . implode(',', $zeroIds) . ")";
            $mysqli->query($deleteZeroQuery);
            echo "<p>✅ Deleted " . count($zeroIds) . " zero-value records</p>";
        }
    } else {
        echo "<p>✅ No zero-value records found!</p>";
    }
    
    // Commit the transaction
    $mysqli->commit();
    $mysqli->autocommit(TRUE);
    
    echo "<h3>✅ Cleanup completed successfully!</h3>";
    echo "<p><a href='debug_order.php?order_id=MN000068'>Debug Order MN000068 Again</a> | <a href='order-placed.php?order_id=MN000068'>View Order</a></p>";
    
} catch (Exception $e) {
    // Rollback on error
    $mysqli->rollback();
    $mysqli->autocommit(TRUE);
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

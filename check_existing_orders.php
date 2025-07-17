<!DOCTYPE html>
<html>
<head>
    <title>Check Existing Orders</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .info { background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>📋 Existing Orders Check</h1>
    
    <?php
    try {
        include_once 'database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();
        
        if (!$mysqli) {
            throw new Exception("Database connection failed");
        }
        
        // Get all orders with MN prefix
        $query = "SELECT OrderId, CustomerId, OrderDate, Amount, PaymentStatus, OrderStatus 
                  FROM order_master 
                  WHERE OrderId LIKE 'MN%' 
                  ORDER BY CAST(SUBSTRING(OrderId, 3) AS UNSIGNED) DESC 
                  LIMIT 20";
        
        $result = mysqli_query($mysqli, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<div class='info'>";
            echo "<strong>Found " . mysqli_num_rows($result) . " existing orders with MN prefix:</strong>";
            echo "</div>";
            
            echo "<table>";
            echo "<tr><th>Order ID</th><th>Customer ID</th><th>Date</th><th>Amount</th><th>Payment Status</th><th>Order Status</th></tr>";
            
            $lastOrderNumber = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['OrderId']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CustomerId']) . "</td>";
                echo "<td>" . htmlspecialchars($row['OrderDate']) . "</td>";
                echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['PaymentStatus']) . "</td>";
                echo "<td>" . htmlspecialchars($row['OrderStatus']) . "</td>";
                echo "</tr>";
                
                // Track the highest order number
                $orderNumber = (int) substr($row['OrderId'], 2);
                if ($orderNumber > $lastOrderNumber) {
                    $lastOrderNumber = $orderNumber;
                }
            }
            echo "</table>";
            
            echo "<div class='info'>";
            echo "<strong>Next Order ID should be:</strong> MN" . str_pad($lastOrderNumber + 1, 6, "0", STR_PAD_LEFT);
            echo "</div>";
            
        } else {
            echo "<div class='info'>";
            echo "<strong>No existing orders found with MN prefix.</strong><br>";
            echo "Next Order ID should be: MN000001";
            echo "</div>";
        }
        
        // Check for any duplicate OrderIds
        $duplicateQuery = "SELECT OrderId, COUNT(*) as count 
                          FROM order_master 
                          GROUP BY OrderId 
                          HAVING COUNT(*) > 1";
        
        $duplicateResult = mysqli_query($mysqli, $duplicateQuery);
        
        if ($duplicateResult && mysqli_num_rows($duplicateResult) > 0) {
            echo "<h3>⚠️ Duplicate Order IDs Found:</h3>";
            echo "<table>";
            echo "<tr><th>Order ID</th><th>Count</th></tr>";
            
            while ($row = mysqli_fetch_assoc($duplicateResult)) {
                echo "<tr>";
                echo "<td style='color: red;'>" . htmlspecialchars($row['OrderId']) . "</td>";
                echo "<td style='color: red;'>" . $row['count'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<div class='info' style='background: #f8d7da;'>";
            echo "<strong>⚠️ Warning:</strong> Duplicate Order IDs found! This can cause insertion errors.";
            echo "</div>";
        } else {
            echo "<div class='info'>";
            echo "<strong>✅ No duplicate Order IDs found.</strong>";
            echo "</div>";
        }
        
        mysqli_close($mysqli);
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; color: #721c24;'>";
        echo "<strong>Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
    ?>
    
    <h3>🔧 Order ID Generation Logic</h3>
    <div class='info'>
        <p><strong>How it works:</strong></p>
        <ol>
            <li>Find the highest existing order number (e.g., if MN000005 exists, highest = 5)</li>
            <li>Add 1 to get the next number (5 + 1 = 6)</li>
            <li>Format with padding (6 becomes MN000006)</li>
            <li>Double-check for uniqueness before inserting</li>
        </ol>
    </div>
    
</body>
</html>

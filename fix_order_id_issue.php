<!DOCTYPE html>
<html>
<head>
    <title>Fix Order ID Issue</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .result { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔧 Fix Order ID Issue</h1>
    
    <?php
    try {
        include_once 'database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();
        
        if (!$mysqli) {
            throw new Exception("Database connection failed");
        }
        
        echo "<div class='info'><h3>📋 Current Database Analysis</h3></div>";
        
        // Check all existing orders
        $allOrdersQuery = "SELECT OrderId, CustomerId, OrderDate, Amount, PaymentStatus, OrderStatus FROM order_master ORDER BY OrderId";
        $allResult = mysqli_query($mysqli, $allOrdersQuery);
        
        if ($allResult) {
            $orderCount = mysqli_num_rows($allResult);
            echo "<p><strong>Total orders in database:</strong> $orderCount</p>";
            
            if ($orderCount > 0) {
                echo "<table>";
                echo "<tr><th>Order ID</th><th>Customer ID</th><th>Date</th><th>Amount</th><th>Payment Status</th><th>Order Status</th></tr>";
                
                $mnOrders = [];
                while ($row = mysqli_fetch_assoc($allResult)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['OrderId']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['CustomerId']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['OrderDate']) . "</td>";
                    echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['PaymentStatus']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['OrderStatus']) . "</td>";
                    echo "</tr>";
                    
                    // Track MN orders
                    if (strpos($row['OrderId'], 'MN') === 0) {
                        $mnOrders[] = $row['OrderId'];
                    }
                }
                echo "</table>";
                
                echo "<div class='warning'>";
                echo "<h4>MN Orders Found:</h4>";
                if (!empty($mnOrders)) {
                    echo "<p>" . implode(', ', $mnOrders) . "</p>";
                    
                    // Find the highest number
                    $highestNumber = 0;
                    foreach ($mnOrders as $orderId) {
                        $number = (int) substr($orderId, 2);
                        if ($number > $highestNumber) {
                            $highestNumber = $number;
                        }
                    }
                    echo "<p><strong>Highest MN number:</strong> $highestNumber</p>";
                    echo "<p><strong>Next order should be:</strong> MN" . str_pad($highestNumber + 1, 6, "0", STR_PAD_LEFT) . "</p>";
                } else {
                    echo "<p>No MN orders found. Next order should be: MN000001</p>";
                }
                echo "</div>";
            }
        }
        
        // Test the current order ID generation logic
        echo "<div class='info'><h3>🧪 Test Order ID Generation</h3></div>";
        
        // Simulate the current logic
        $orderPrefix = "MN";
        $lastOrderQuery = "SELECT OrderId FROM order_master WHERE OrderId LIKE 'MN%' ORDER BY CAST(SUBSTRING(OrderId, 3) AS UNSIGNED) DESC LIMIT 1";
        $result = mysqli_query($mysqli, $lastOrderQuery);
        
        echo "<p><strong>Query used:</strong> <code>$lastOrderQuery</code></p>";
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $lastOrderId = (int) substr($row['OrderId'], 2);
            $newOrderNumber = $lastOrderId + 1;
            echo "<p><strong>Last order found:</strong> {$row['OrderId']}</p>";
            echo "<p><strong>Extracted number:</strong> $lastOrderId</p>";
            echo "<p><strong>Next number:</strong> $newOrderNumber</p>";
        } else {
            $newOrderNumber = 1;
            echo "<p><strong>No MN orders found, starting with:</strong> $newOrderNumber</p>";
        }
        
        $testOrderId = $orderPrefix . str_pad($newOrderNumber, 6, "0", STR_PAD_LEFT);
        echo "<p><strong>Generated Order ID:</strong> $testOrderId</p>";
        
        // Check if this ID already exists
        $checkQuery = "SELECT COUNT(*) as count FROM order_master WHERE OrderId = ?";
        $checkStmt = mysqli_prepare($mysqli, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $testOrderId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        $checkRow = mysqli_fetch_assoc($checkResult);
        
        if ($checkRow['count'] > 0) {
            echo "<div class='error'>";
            echo "<p><strong>❌ PROBLEM FOUND!</strong> Order ID $testOrderId already exists!</p>";
            echo "<p>This explains the duplicate entry error.</p>";
            echo "</div>";
        } else {
            echo "<div class='success'>";
            echo "<p><strong>✅ Order ID $testOrderId is available!</strong></p>";
            echo "</div>";
        }
        
        mysqli_close($mysqli);
        
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<strong>Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
    ?>
    
    <div class="info">
        <h3>💡 Solution</h3>
        <p>If the issue persists, I'll create a more robust order ID generation that:</p>
        <ol>
            <li>Uses database transactions to prevent race conditions</li>
            <li>Implements proper locking mechanism</li>
            <li>Has multiple fallback strategies</li>
            <li>Uses timestamp-based IDs as last resort</li>
        </ol>
    </div>
    
</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🎁 Award Missing Points</h2>";

// Get customer ID from session
$customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

if (!$customerId) {
    echo "<p style='color: red;'>❌ Not logged in! Please log in first.</p>";
    echo "<p><a href='login.php'>Login</a></p>";
    exit;
}

echo "<p><strong>Customer ID:</strong> $customerId</p>";

if (isset($_GET['action']) && $_GET['action'] == 'award_all') {
    echo "<h3>🚀 Awarding Points for All Orders...</h3>";
    
    try {
        include_once 'includes/RewardsSystem.php';
        $rewards = new RewardsSystem();
        
        // Get all orders without points
        $ordersQuery = "
            SELECT om.OrderId, om.Amount, om.OrderDate, om.CreatedAt
            FROM order_master om
            LEFT JOIN points_transactions pt ON om.OrderId = pt.order_id AND pt.customer_id = om.CustomerId
            WHERE om.CustomerId = ? AND pt.id IS NULL AND om.Amount > 0
            ORDER BY om.CreatedAt DESC
        ";
        
        $stmt = $mysqli->prepare($ordersQuery);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $totalPointsAwarded = 0;
        $ordersProcessed = 0;
        
        echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        
        while ($order = $result->fetch_assoc()) {
            $orderId = $order['OrderId'];
            $amount = $order['Amount'];
            
            try {
                $pointsAwarded = $rewards->awardOrderPoints($customerId, $orderId, $amount);
                $totalPointsAwarded += $pointsAwarded;
                $ordersProcessed++;
                
                echo "<p>✅ <strong>Order $orderId</strong> (₹$amount) → <span style='color: #ff8c00; font-weight: bold;'>+$pointsAwarded points</span></p>";
                
            } catch (Exception $e) {
                echo "<p>❌ <strong>Order $orderId</strong> → Error: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "</div>";
        
        echo "<div style='background-color: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 Points Award Complete!</h3>";
        echo "<p><strong>Orders Processed:</strong> $ordersProcessed</p>";
        echo "<p><strong>Total Points Awarded:</strong> <span style='color: #ff8c00; font-size: 18px; font-weight: bold;'>$totalPointsAwarded points</span></p>";
        echo "</div>";
        
        echo "<p><a href='debug_rewards.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Check Your Points</a></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    // Show orders without points
    echo "<h3>📋 Orders Missing Points:</h3>";
    
    $ordersQuery = "
        SELECT om.OrderId, om.Amount, om.OrderDate, om.OrderStatus, om.CreatedAt
        FROM order_master om
        LEFT JOIN points_transactions pt ON om.OrderId = pt.order_id AND pt.customer_id = om.CustomerId
        WHERE om.CustomerId = ? AND pt.id IS NULL AND om.Amount > 0
        ORDER BY om.CreatedAt DESC
    ";
    
    $stmt = $mysqli->prepare($ordersQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Date</th><th>Amount</th><th>Status</th><th>Expected Points</th></tr>";
        
        $totalExpectedPoints = 0;
        
        while ($order = $result->fetch_assoc()) {
            $expectedPoints = floor(($order['Amount'] / 100) * 3);
            $totalExpectedPoints += $expectedPoints;
            
            echo "<tr>";
            echo "<td>" . $order['OrderId'] . "</td>";
            echo "<td>" . $order['OrderDate'] . "</td>";
            echo "<td>₹" . $order['Amount'] . "</td>";
            echo "<td>" . $order['OrderStatus'] . "</td>";
            echo "<td style='color: #ff8c00; font-weight: bold;'>$expectedPoints points</td>";
            echo "</tr>";
        }
        
        echo "<tr style='background-color: #fff3cd; font-weight: bold;'>";
        echo "<td colspan='4'>TOTAL MISSING POINTS</td>";
        echo "<td style='color: #ff8c00; font-size: 16px;'>$totalExpectedPoints points</td>";
        echo "</tr>";
        echo "</table>";
        
        echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>🎯 Summary:</h4>";
        echo "<ul>";
        echo "<li><strong>Orders without points:</strong> " . $result->num_rows . "</li>";
        echo "<li><strong>Total missing points:</strong> <span style='color: #ff8c00; font-weight: bold;'>$totalExpectedPoints points</span></li>";
        echo "<li><strong>Reason:</strong> Orders placed before rewards system integration</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div style='text-align: center; margin: 30px 0;'>";
        echo "<a href='?action=award_all' style='background: #ff8c00; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold;' onclick='return confirm(\"Award $totalExpectedPoints points for all missing orders?\")'>🎁 Award All Missing Points</a>";
        echo "</div>";
        
    } else {
        echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<h4>✅ All Orders Have Points!</h4>";
        echo "<p>All your orders already have points awarded. No missing points found.</p>";
        echo "</div>";
    }
    
    // Show orders with points for reference
    echo "<h3>✅ Orders With Points:</h3>";
    $pointsOrdersQuery = "
        SELECT om.OrderId, om.Amount, om.OrderDate, pt.points, pt.created_at
        FROM order_master om
        INNER JOIN points_transactions pt ON om.OrderId = pt.order_id AND pt.customer_id = om.CustomerId
        WHERE om.CustomerId = ? AND pt.transaction_type = 'earned'
        ORDER BY om.CreatedAt DESC
        LIMIT 10
    ";
    
    $pointsStmt = $mysqli->prepare($pointsOrdersQuery);
    $pointsStmt->bind_param("i", $customerId);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    
    if ($pointsResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Order Date</th><th>Amount</th><th>Points Earned</th><th>Points Date</th></tr>";
        
        while ($pointsOrder = $pointsResult->fetch_assoc()) {
            echo "<tr style='background-color: #d4edda;'>";
            echo "<td>" . $pointsOrder['OrderId'] . "</td>";
            echo "<td>" . $pointsOrder['OrderDate'] . "</td>";
            echo "<td>₹" . $pointsOrder['Amount'] . "</td>";
            echo "<td style='color: #28a745; font-weight: bold;'>" . $pointsOrder['points'] . " points</td>";
            echo "<td>" . $pointsOrder['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders with points found yet.</p>";
    }
}

echo "<br><p><a href='debug_rewards.php'>Debug Rewards</a> | <a href='index.php'>Homepage</a> | <a href='test_points_popup.html'>Test Popup</a></p>";
?>

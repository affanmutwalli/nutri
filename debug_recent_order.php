<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🔍 Debug Recent ₹599 Order</h2>";

// Get customer ID from session
$customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

if (!$customerId) {
    echo "<p style='color: red;'>❌ Not logged in! Please log in first.</p>";
    echo "<p><a href='login.php'>Login</a></p>";
    exit;
}

echo "<p><strong>Customer ID:</strong> $customerId</p>";

// Find the most recent ₹599 order
echo "<h3>1. Finding Your ₹599 Order:</h3>";

$recentOrderQuery = "SELECT * FROM order_master WHERE CustomerId = ? AND Amount = 599 ORDER BY CreatedAt DESC LIMIT 1";
$stmt = $mysqli->prepare($recentOrderQuery);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderResult->num_rows > 0) {
    $order = $orderResult->fetch_assoc();
    $orderId = $order['OrderId'];
    
    echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h4>✅ Found Your ₹599 Order:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    foreach ($order as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Check if points were awarded for this order
    echo "<h3>2. Points Status for Order $orderId:</h3>";
    
    $pointsQuery = "SELECT * FROM points_transactions WHERE customer_id = ? AND order_id = ? AND transaction_type = 'earned'";
    $pointsStmt = $mysqli->prepare($pointsQuery);
    $pointsStmt->bind_param("is", $customerId, $orderId);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    
    if ($pointsResult->num_rows > 0) {
        $pointsData = $pointsResult->fetch_assoc();
        echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<h4>✅ Points Were Awarded:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        foreach ($pointsData as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
        echo "</div>";
        
        echo "<p style='color: green;'><strong>Points Awarded:</strong> {$pointsData['points']} points</p>";
        echo "<p style='color: orange;'>⚠️ <strong>Issue:</strong> Points were awarded but popup didn't show. This could be:</p>";
        echo "<ul>";
        echo "<li>Frontend JavaScript issue</li>";
        echo "<li>Order placed before popup integration</li>";
        echo "<li>Browser blocking popups</li>";
        echo "<li>SweetAlert library not loaded</li>";
        echo "</ul>";
        
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<h4>❌ No Points Awarded!</h4>";
        echo "<p>No points transaction found for this order. Possible reasons:</p>";
        echo "<ul>";
        echo "<li>Order placed before rewards integration</li>";
        echo "<li>RewardsSystem class error</li>";
        echo "<li>Database connection issue</li>";
        echo "<li>Order file doesn't have rewards integration</li>";
        echo "</ul>";
        echo "</div>";
        
        // Calculate expected points
        $expectedPoints = floor(($order['Amount'] / 100) * 3);
        echo "<p><strong>Expected Points:</strong> $expectedPoints points (₹{$order['Amount']} ÷ 100 × 3 = $expectedPoints)</p>";
        
        // Manual points award option
        echo "<h4>🛠️ Manual Fix:</h4>";
        if (isset($_GET['award_points']) && $_GET['award_points'] == 'yes') {
            try {
                include_once 'includes/RewardsSystem.php';
                $rewards = new RewardsSystem();
                
                $pointsAwarded = $rewards->awardOrderPoints($customerId, $orderId, $order['Amount']);
                
                echo "<p style='color: green;'>✅ Manually awarded $pointsAwarded points for order $orderId!</p>";
                echo "<script>setTimeout(function(){ window.location.href = 'debug_recent_order.php'; }, 2000);</script>";
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error awarding points: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<a href='?award_points=yes' style='background: #ff8c00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;' onclick='return confirm(\"Award $expectedPoints points for this order?\")'>🎁 Award Points Manually</a>";
        }
    }
    
    // Check order details
    echo "<h3>3. Order Details:</h3>";
    $detailsQuery = "SELECT * FROM order_details WHERE OrderId = ?";
    $detailsStmt = $mysqli->prepare($detailsQuery);
    $detailsStmt->bind_param("s", $orderId);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    
    if ($detailsResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ProductId</th><th>ProductCode</th><th>Size</th><th>Quantity</th><th>Price</th><th>SubTotal</th></tr>";
        while ($detail = $detailsResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $detail['ProductId'] . "</td>";
            echo "<td>" . $detail['ProductCode'] . "</td>";
            echo "<td>" . $detail['Size'] . "</td>";
            echo "<td>" . $detail['Quantity'] . "</td>";
            echo "<td>₹" . $detail['Price'] . "</td>";
            echo "<td>₹" . $detail['SubTotal'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} else {
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h4>❌ No ₹599 Order Found!</h4>";
    echo "<p>No recent order with amount ₹599 found for customer $customerId.</p>";
    echo "</div>";
    
    // Show recent orders
    echo "<h3>Your Recent Orders:</h3>";
    $allOrdersQuery = "SELECT OrderId, OrderDate, Amount, OrderStatus, CreatedAt FROM order_master WHERE CustomerId = ? ORDER BY CreatedAt DESC LIMIT 5";
    $allStmt = $mysqli->prepare($allOrdersQuery);
    $allStmt->bind_param("i", $customerId);
    $allStmt->execute();
    $allResult = $allStmt->get_result();
    
    if ($allResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr>";
        while ($recentOrder = $allResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $recentOrder['OrderId'] . "</td>";
            echo "<td>" . $recentOrder['OrderDate'] . "</td>";
            echo "<td>₹" . $recentOrder['Amount'] . "</td>";
            echo "<td>" . $recentOrder['OrderStatus'] . "</td>";
            echo "<td><a href='?check_order=" . $recentOrder['OrderId'] . "'>Check Points</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Check specific order if requested
if (isset($_GET['check_order'])) {
    $checkOrderId = $_GET['check_order'];
    echo "<h3>Checking Order: $checkOrderId</h3>";
    
    $checkPointsQuery = "SELECT * FROM points_transactions WHERE customer_id = ? AND order_id = ?";
    $checkStmt = $mysqli->prepare($checkPointsQuery);
    $checkStmt->bind_param("is", $customerId, $checkOrderId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo "<p style='color: green;'>✅ Points found for order $checkOrderId</p>";
        while ($point = $checkResult->fetch_assoc()) {
            echo "<p>Points: {$point['points']}, Type: {$point['transaction_type']}, Date: {$point['created_at']}</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No points found for order $checkOrderId</p>";
    }
}

echo "<h3>4. Troubleshooting Steps:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h4>Why No Popup Might Have Appeared:</h4>";
echo "<ol>";
echo "<li><strong>Order placed before integration:</strong> Rewards system was added after your order</li>";
echo "<li><strong>JavaScript error:</strong> SweetAlert popup blocked or failed</li>";
echo "<li><strong>Wrong order file:</strong> Order might have used old order processing file</li>";
echo "<li><strong>Browser issues:</strong> Popup blockers or JavaScript disabled</li>";
echo "</ol>";
echo "</div>";

echo "<br><p><a href='debug_rewards.php'>Full Rewards Debug</a> | <a href='index.php'>Homepage</a> | <a href='test_points_popup.html'>Test Popup</a></p>";
?>

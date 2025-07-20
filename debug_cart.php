<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🛒 Cart Debug Tool</h2>";

// Get customer ID if logged in
$customerId = isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : null;

if ($customerId) {
    echo "<p><strong>Customer ID:</strong> $customerId</p>";
} else {
    echo "<p><strong>Status:</strong> Not logged in</p>";
}

// Check session cart
echo "<h3>Session Cart:</h3>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Product ID</th><th>Quantity</th></tr>";
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        echo "<tr><td>$productId</td><td>$quantity</td></tr>";
    }
    echo "</table>";
    echo "<p><strong>Total Items:</strong> " . array_sum($_SESSION['cart']) . "</p>";
} else {
    echo "<p>✅ Session cart is empty</p>";
}

// Check buy_now session
echo "<h3>Buy Now Session:</h3>";
if (isset($_SESSION['buy_now']) && !empty($_SESSION['buy_now'])) {
    echo "<pre>" . print_r($_SESSION['buy_now'], true) . "</pre>";
} else {
    echo "<p>✅ Buy now session is empty</p>";
}

// Check database cart
if ($customerId) {
    echo "<h3>Database Cart:</h3>";
    $cartQuery = "SELECT * FROM cart WHERE CustomerId = ?";
    $stmt = $mysqli->prepare($cartQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Product ID</th><th>Quantity</th><th>Price</th><th>Created</th><th>Updated</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['ProductId'] . "</td>";
            echo "<td>" . $row['Quantity'] . "</td>";
            echo "<td>₹" . $row['Price'] . "</td>";
            echo "<td>" . $row['CreatedDate'] . "</td>";
            echo "<td>" . $row['UpdatedDate'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>✅ Database cart is empty</p>";
    }
}

// Check recent orders
echo "<h3>Recent Orders (Last 5):</h3>";
if ($customerId) {
    $orderQuery = "SELECT OrderId, OrderDate, Amount, OrderStatus FROM order_master WHERE CustomerId = ? ORDER BY CreatedAt DESC LIMIT 5";
    $stmt = $mysqli->prepare($orderQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Date</th><th>Amount</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><a href='order-placed.php?order_id=" . $row['OrderId'] . "'>" . $row['OrderId'] . "</a></td>";
            echo "<td>" . $row['OrderDate'] . "</td>";
            echo "<td>₹" . $row['Amount'] . "</td>";
            echo "<td>" . $row['OrderStatus'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No recent orders found</p>";
    }
}

// Action buttons
echo "<h3>🔧 Actions:</h3>";
echo "<div style='margin: 10px 0;'>";

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'clear_session':
            if (isset($_SESSION['cart'])) unset($_SESSION['cart']);
            if (isset($_SESSION['buy_now'])) unset($_SESSION['buy_now']);
            echo "<p style='color: green;'>✅ Session cart cleared!</p>";
            break;
            
        case 'clear_database':
            if ($customerId) {
                $clearQuery = "DELETE FROM cart WHERE CustomerId = ?";
                $stmt = $mysqli->prepare($clearQuery);
                $stmt->bind_param("i", $customerId);
                $stmt->execute();
                echo "<p style='color: green;'>✅ Database cart cleared!</p>";
            } else {
                echo "<p style='color: red;'>❌ Not logged in!</p>";
            }
            break;
            
        case 'clear_all':
            if (isset($_SESSION['cart'])) unset($_SESSION['cart']);
            if (isset($_SESSION['buy_now'])) unset($_SESSION['buy_now']);
            if ($customerId) {
                $clearQuery = "DELETE FROM cart WHERE CustomerId = ?";
                $stmt = $mysqli->prepare($clearQuery);
                $stmt->bind_param("i", $customerId);
                $stmt->execute();
            }
            echo "<p style='color: green;'>✅ All cart data cleared!</p>";
            break;
    }
    echo "<script>setTimeout(function(){ window.location.href = 'debug_cart.php'; }, 2000);</script>";
}

echo "<a href='?action=clear_session' class='btn' style='background: #007bff; color: white; padding: 8px 16px; text-decoration: none; margin: 5px; border-radius: 4px;'>Clear Session Cart</a>";

if ($customerId) {
    echo "<a href='?action=clear_database' class='btn' style='background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; margin: 5px; border-radius: 4px;'>Clear Database Cart</a>";
}

echo "<a href='?action=clear_all' class='btn' style='background: #6c757d; color: white; padding: 8px 16px; text-decoration: none; margin: 5px; border-radius: 4px;'>Clear All Cart Data</a>";

echo "</div>";

// Navigation
echo "<h3>🔗 Navigation:</h3>";
echo "<p>";
echo "<a href='index.php'>Homepage</a> | ";
echo "<a href='cart.php'>View Cart</a> | ";
echo "<a href='checkout.php'>Checkout</a> | ";
echo "<a href='cms/'>Admin Panel</a>";
echo "</p>";

// Auto-refresh option
echo "<script>";
echo "if (window.location.search.includes('auto_refresh=1')) {";
echo "  setTimeout(function(){ window.location.reload(); }, 5000);";
echo "}";
echo "</script>";

echo "<p><a href='?auto_refresh=1'>🔄 Enable Auto-Refresh (5s)</a></p>";

// Summary
echo "<h3>📊 Summary:</h3>";
echo "<div style='background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px;'>";

$sessionItems = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$dbItems = 0;

if ($customerId) {
    $countQuery = "SELECT SUM(Quantity) as total FROM cart WHERE CustomerId = ?";
    $stmt = $mysqli->prepare($countQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $dbItems = $row['total'] ?? 0;
    }
}

echo "<p><strong>Session Cart Items:</strong> $sessionItems</p>";
echo "<p><strong>Database Cart Items:</strong> $dbItems</p>";

if ($sessionItems == 0 && $dbItems == 0) {
    echo "<p style='color: green;'>✅ All cart data is clean!</p>";
} elseif ($sessionItems != $dbItems) {
    echo "<p style='color: orange;'>⚠️ Session and database cart are out of sync!</p>";
} else {
    echo "<p style='color: blue;'>ℹ️ Cart data is consistent</p>";
}

echo "</div>";
?>

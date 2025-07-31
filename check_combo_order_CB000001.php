<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

$orderId = 'CB000001';

echo "<h2>🔍 Checking Combo Order CB000001 Data</h2>";
echo "<style>
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.error { color: red; font-weight: bold; }
.success { color: green; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.info { color: blue; font-weight: bold; }
</style>";

// 1. Check order_master
echo "<h3>1. Order Master Data:</h3>";
$orderQuery = "SELECT * FROM order_master WHERE OrderId = ?";
$stmt = $mysqli->prepare($orderQuery);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$orderResult = $stmt->get_result();
$orderData = $orderResult->fetch_assoc();

if ($orderData) {
    echo "<p class='success'>✅ Order found in order_master</p>";
    echo "<p><strong>Amount:</strong> {$orderData['Amount']}</p>";
    echo "<p><strong>Created:</strong> {$orderData['CreatedAt']}</p>";
    echo "<p><strong>Status:</strong> {$orderData['OrderStatus']}</p>";
} else {
    echo "<p class='error'>❌ Order not found</p>";
    exit;
}

// 2. Check order_details
echo "<h3>2. Order Details Check:</h3>";
$detailsQuery = "SELECT * FROM order_details WHERE OrderId = ?";
$stmt = $mysqli->prepare($detailsQuery);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$detailsResult = $stmt->get_result();

if ($detailsResult->num_rows > 0) {
    echo "<p class='success'>✅ Found {$detailsResult->num_rows} records in order_details</p>";
    while ($detail = $detailsResult->fetch_assoc()) {
        echo "<p>ProductId: {$detail['ProductId']}, ProductCode: {$detail['ProductCode']}, Quantity: {$detail['Quantity']}, Price: {$detail['Price']}</p>";
    }
} else {
    echo "<p class='error'>❌ No records in order_details - This is the problem!</p>";
}

// 3. Check combo_order_tracking
echo "<h3>3. Combo Order Tracking Check:</h3>";
$comboQuery = "SELECT * FROM combo_order_tracking WHERE order_id = ?";
$stmt = $mysqli->prepare($comboQuery);
if ($stmt) {
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $comboResult = $stmt->get_result();
    
    if ($comboResult->num_rows > 0) {
        echo "<p class='success'>✅ Found combo tracking data</p>";
        while ($combo = $comboResult->fetch_assoc()) {
            echo "<p>Combo ID: {$combo['combo_id']}, Name: {$combo['combo_name']}, Price: {$combo['combo_price']}</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ No combo tracking data found</p>";
    }
} else {
    echo "<p class='error'>❌ combo_order_tracking table doesn't exist</p>";
}

// 4. Check if this was a failed combo order placement
echo "<h3>4. Diagnosis:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0;'>";
echo "<h4>🔍 What Happened:</h4>";
echo "<p>The combo order CB000001 was created in <code>order_master</code> but the combo placement process failed to:</p>";
echo "<ul>";
echo "<li>❌ Insert individual products into <code>order_details</code> table</li>";
echo "<li>❌ Insert combo tracking data into <code>combo_order_tracking</code> table</li>";
echo "</ul>";
echo "<p><strong>This could happen if:</strong></p>";
echo "<ul>";
echo "<li>The combo placement script encountered an error after creating the order_master record</li>";
echo "<li>The ProductIds in the combo don't exist in product_master</li>";
echo "<li>Database transaction was not properly committed</li>";
echo "<li>The combo_order_tracking table doesn't exist</li>";
echo "</ul>";
echo "</div>";

// 5. Check what combo this should have been
echo "<h3>5. Trying to Reconstruct the Combo:</h3>";

// Look for any clues in the amount
$amount = $orderData['Amount'];
echo "<p><strong>Order Amount:</strong> ₹{$amount}</p>";

// Check dynamic_combos table for combos around this price
$combosQuery = "SELECT * FROM dynamic_combos WHERE combo_price BETWEEN ? AND ? ORDER BY ABS(combo_price - ?) LIMIT 5";
$stmt = $mysqli->prepare($combosQuery);
if ($stmt) {
    $lowerBound = $amount - 50;
    $upperBound = $amount + 50;
    $stmt->bind_param("ddd", $lowerBound, $upperBound, $amount);
    $stmt->execute();
    $combosResult = $stmt->get_result();
    
    if ($combosResult->num_rows > 0) {
        echo "<p class='info'>📋 Possible combos with similar price:</p>";
        echo "<table>";
        echo "<tr><th>Combo ID</th><th>Product1</th><th>Product2</th><th>Combo Price</th><th>Combo Name</th></tr>";
        while ($combo = $combosResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$combo['combo_id']}</td>";
            echo "<td>{$combo['product1_id']}</td>";
            echo "<td>{$combo['product2_id']}</td>";
            echo "<td>₹{$combo['combo_price']}</td>";
            echo "<td>" . substr($combo['combo_name'], 0, 50) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No combos found with similar price</p>";
    }
} else {
    echo "<p class='error'>❌ dynamic_combos table doesn't exist</p>";
}

// 6. Solution options
echo "<h3>6. Solution Options:</h3>";
echo "<div style='background-color: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 10px 0;'>";
echo "<h4>🛠️ How to Fix This:</h4>";
echo "<ol>";
echo "<li><strong>Option 1:</strong> Manually reconstruct the order_details records based on a likely combo</li>";
echo "<li><strong>Option 2:</strong> Modify order_details.php to handle combo orders without order_details records</li>";
echo "<li><strong>Option 3:</strong> Create a fallback display for incomplete combo orders</li>";
echo "</ol>";

echo "<h4>🎯 Recommended Approach:</h4>";
echo "<p>Modify <code>order_details.php</code> and <code>generate_invoice_pdf.php</code> to:</p>";
echo "<ul>";
echo "<li>Detect combo orders (OrderId starts with 'CB')</li>";
echo "<li>Check if order_details exist</li>";
echo "<li>If not, show combo information from combo_order_tracking or display a generic combo item</li>";
echo "<li>Fall back to showing 'Combo Order - ₹{amount}' if no detailed data is available</li>";
echo "</ul>";
echo "</div>";

?>

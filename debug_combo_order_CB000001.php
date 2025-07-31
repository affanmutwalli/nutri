<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

$orderId = 'CB000001';

echo "<h2>🔍 Debugging Combo Order: $orderId</h2>";
echo "<style>
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.error { color: red; font-weight: bold; }
.success { color: green; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.info { color: blue; font-weight: bold; }
</style>";

// 1. Check order_master for combo order
echo "<h3>1. Order Master Data for $orderId:</h3>";
$orderQuery = "SELECT * FROM order_master WHERE OrderId = ?";
$stmt = $mysqli->prepare($orderQuery);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderData = $orderResult->fetch_assoc()) {
    echo "<p class='success'>✅ Combo order found in order_master</p>";
    echo "<table>";
    foreach ($orderData as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ Combo order not found in order_master</p>";
    exit;
}

// 2. Check order_details table
echo "<h3>2. Order Details Check:</h3>";
$detailsQuery = "SELECT * FROM order_details WHERE OrderId = ?";
$stmt = $mysqli->prepare($detailsQuery);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$detailsResult = $stmt->get_result();

if ($detailsResult->num_rows > 0) {
    echo "<p class='success'>✅ Found {$detailsResult->num_rows} records in order_details</p>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    while ($detail = $detailsResult->fetch_assoc()) {
        foreach ($detail as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "<tr><td colspan='2'><hr></td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ No records found in order_details table - This is the problem!</p>";
}

// 3. Check combo_order_tracking table (if it exists)
echo "<h3>3. Combo Order Tracking Check:</h3>";
$comboTrackingQuery = "SELECT * FROM combo_order_tracking WHERE order_id = ?";
$stmt = $mysqli->prepare($comboTrackingQuery);
if ($stmt) {
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $comboResult = $stmt->get_result();
    
    if ($comboResult->num_rows > 0) {
        echo "<p class='success'>✅ Found {$comboResult->num_rows} records in combo_order_tracking</p>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        while ($combo = $comboResult->fetch_assoc()) {
            foreach ($combo as $key => $value) {
                echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
            }
            echo "<tr><td colspan='2'><hr></td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No records found in combo_order_tracking table</p>";
    }
} else {
    echo "<p class='warning'>⚠️ combo_order_tracking table doesn't exist or query failed</p>";
}

// 4. Check what tables might contain combo order data
echo "<h3>4. Searching for Combo Data in Other Tables:</h3>";

// Check if there are any tables with combo-related data
$tablesQuery = "SHOW TABLES LIKE '%combo%'";
$tablesResult = $mysqli->query($tablesQuery);

if ($tablesResult->num_rows > 0) {
    echo "<p class='info'>📋 Found combo-related tables:</p>";
    echo "<ul>";
    while ($table = $tablesResult->fetch_array()) {
        echo "<li><strong>{$table[0]}</strong></li>";
        
        // Check if this table has data for our order
        $checkQuery = "SELECT COUNT(*) as count FROM {$table[0]} WHERE order_id = '$orderId' OR OrderId = '$orderId'";
        $checkResult = $mysqli->query($checkQuery);
        if ($checkResult) {
            $count = $checkResult->fetch_assoc()['count'];
            if ($count > 0) {
                echo " - <span class='success'>Contains $count records for $orderId</span>";
                
                // Show the actual data
                $dataQuery = "SELECT * FROM {$table[0]} WHERE order_id = '$orderId' OR OrderId = '$orderId'";
                $dataResult = $mysqli->query($dataQuery);
                if ($dataResult && $dataResult->num_rows > 0) {
                    echo "<table style='margin-left: 20px; margin-top: 10px;'>";
                    $firstRow = true;
                    while ($row = $dataResult->fetch_assoc()) {
                        if ($firstRow) {
                            echo "<tr>";
                            foreach (array_keys($row) as $header) {
                                echo "<th>$header</th>";
                            }
                            echo "</tr>";
                            $firstRow = false;
                        }
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>$value</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo " - <span class='warning'>No records for $orderId</span>";
            }
        }
    }
    echo "</ul>";
} else {
    echo "<p class='warning'>⚠️ No combo-related tables found</p>";
}

// 5. Check recent combo orders to understand the pattern
echo "<h3>5. Recent Combo Orders Analysis:</h3>";
$recentCombosQuery = "SELECT OrderId, Amount, OrderDate, OrderStatus FROM order_master WHERE OrderId LIKE 'CB%' ORDER BY CreatedAt DESC LIMIT 5";
$recentCombosResult = $mysqli->query($recentCombosQuery);

if ($recentCombosResult->num_rows > 0) {
    echo "<p class='info'>📊 Recent combo orders:</p>";
    echo "<table>";
    echo "<tr><th>OrderId</th><th>Amount</th><th>OrderDate</th><th>OrderStatus</th><th>Has Details?</th></tr>";
    while ($combo = $recentCombosResult->fetch_assoc()) {
        $hasDetails = $mysqli->query("SELECT COUNT(*) as count FROM order_details WHERE OrderId = '{$combo['OrderId']}'")->fetch_assoc()['count'];
        echo "<tr>";
        echo "<td>{$combo['OrderId']}</td>";
        echo "<td>{$combo['Amount']}</td>";
        echo "<td>{$combo['OrderDate']}</td>";
        echo "<td>{$combo['OrderStatus']}</td>";
        echo "<td>" . ($hasDetails > 0 ? "<span class='success'>✅ Yes ($hasDetails)</span>" : "<span class='error'>❌ No</span>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>⚠️ No combo orders found</p>";
}

// 6. Check the order_details.php logic for combo orders
echo "<h3>6. Understanding the Issue:</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0;'>";
echo "<h4>🔍 Root Cause Analysis:</h4>";
echo "<p><strong>The Problem:</strong> Combo orders are stored in <code>order_master</code> but the order details page looks for individual products in <code>order_details</code> table.</p>";
echo "<p><strong>For Combo Orders:</strong></p>";
echo "<ul>";
echo "<li>The combo information might be stored in a separate table (like <code>combo_order_tracking</code>)</li>";
echo "<li>The <code>order_details.php</code> page needs to be modified to handle combo orders differently</li>";
echo "<li>Combo orders might not have individual product breakdowns in <code>order_details</code></li>";
echo "</ul>";
echo "</div>";

echo "<h3>7. Recommended Solutions:</h3>";
echo "<div style='background-color: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 10px 0;'>";
echo "<h4>🛠️ Fix Options:</h4>";
echo "<ol>";
echo "<li><strong>Modify order_details.php:</strong> Add logic to detect combo orders (OrderId starts with 'CB') and fetch data from combo-specific tables</li>";
echo "<li><strong>Create order_details records:</strong> Populate the order_details table with combo product information</li>";
echo "<li><strong>Update PDF generation:</strong> Modify generate_invoice_pdf.php to handle combo orders</li>";
echo "</ol>";
echo "</div>";

?>

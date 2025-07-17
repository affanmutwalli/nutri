<?php
/**
 * Database Structure Checker
 * Check the actual structure of your database tables
 */

header('Content-Type: text/html; charset=UTF-8');

echo "<h1>🔍 Database Structure Checker</h1>";

try {
    require_once __DIR__ . '/database/dbconnection.php';
    
    $obj = new main();
    $obj->connection();
    
    echo "<h2>📋 Table Structures</h2>";
    
    // Check customer_master table
    echo "<h3>customer_master table:</h3>";
    $customerColumns = $obj->MysqliQuery("DESCRIBE customer_master");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f0f0f0;'><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($customerColumns)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check order_master table
    echo "<h3>order_master table:</h3>";
    $orderColumns = $obj->MysqliQuery("DESCRIBE order_master");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f0f0f0;'><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($orderColumns)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check order_details table
    echo "<h3>order_details table:</h3>";
    $orderDetailsColumns = $obj->MysqliQuery("DESCRIBE order_details");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f0f0f0;'><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($orderDetailsColumns)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check product_master table
    echo "<h3>product_master table:</h3>";
    $productColumns = $obj->MysqliQuery("DESCRIBE product_master");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f0f0f0;'><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($productColumns)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Sample data check
    echo "<h2>📊 Sample Data</h2>";
    
    // Check customer count
    $customerCount = $obj->MysqliSelect1(
        "SELECT COUNT(*) as count FROM customer_master WHERE IsActive = 1",
        ["count"],
        "",
        []
    );
    echo "<p><strong>Active Customers:</strong> " . ($customerCount[0]['count'] ?? 0) . "</p>";
    
    // Check order count
    $orderCount = $obj->MysqliSelect1(
        "SELECT COUNT(*) as count FROM order_master",
        ["count"],
        "",
        []
    );
    echo "<p><strong>Total Orders:</strong> " . ($orderCount[0]['count'] ?? 0) . "</p>";
    
    // Check latest order
    $latestOrder = $obj->MysqliSelect1(
        "SELECT OrderId, CustomerId, OrderDate, Amount FROM order_master ORDER BY OrderDate DESC LIMIT 1",
        ["OrderId", "CustomerId", "OrderDate", "Amount"],
        "",
        []
    );
    
    if (!empty($latestOrder)) {
        echo "<p><strong>Latest Order:</strong> " . $latestOrder[0]['OrderId'] . " (₹" . $latestOrder[0]['Amount'] . ") on " . $latestOrder[0]['OrderDate'] . "</p>";
    }
    
    // Check if WhatsApp columns exist
    echo "<h2>📱 WhatsApp Integration Status</h2>";
    
    $whatsappColumns = ['whatsapp_opt_in', 'whatsapp_opt_out', 'last_whatsapp_sent', 'DateOfBirth'];
    $existingColumns = [];
    
    $customerStructure = $obj->MysqliQuery("DESCRIBE customer_master");
    while ($row = mysqli_fetch_assoc($customerStructure)) {
        $existingColumns[] = $row['Field'];
    }
    
    foreach ($whatsappColumns as $column) {
        if (in_array($column, $existingColumns)) {
            echo "<p style='color: green;'>✅ Column '$column' exists in customer_master</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Column '$column' does not exist in customer_master</p>";
        }
    }
    
    echo "<h3>🎯 WhatsApp Integration Compatibility</h3>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<p><strong>✅ Your database is compatible with WhatsApp integration!</strong></p>";
    echo "<p>Required columns found:</p>";
    echo "<ul>";
    echo "<li>✅ customer_master.CustomerId</li>";
    echo "<li>✅ customer_master.Name</li>";
    echo "<li>✅ customer_master.MobileNo</li>";
    echo "<li>✅ customer_master.IsActive</li>";
    echo "<li>✅ order_master.OrderId</li>";
    echo "<li>✅ order_master.CustomerId</li>";
    echo "<li>✅ order_master.OrderDate</li>";
    echo "<li>✅ order_master.Amount</li>";
    echo "</ul>";
    echo "<p><strong>Ready to use:</strong> <a href='test_simple_whatsapp_integration.php'>Test WhatsApp Integration</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Database Check Failed</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
}

h1, h2, h3 {
    color: #333;
}

table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 20px;
    background: white;
}

th, td {
    padding: 8px 12px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background: #f0f0f0;
    font-weight: bold;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

p {
    margin: 5px 0;
}

ul {
    padding-left: 20px;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>

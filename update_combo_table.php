<?php
include('database/dbconnection.php');

$obj = new main();
$connection = $obj->connection();

echo "<h2>Updating Dynamic Combo Table</h2>";

try {
    // Update the combo_name column to allow longer names
    $alterQuery = "ALTER TABLE dynamic_combos MODIFY COLUMN combo_name VARCHAR(500) NULL COMMENT 'Generated combo name'";
    
    if ($connection->query($alterQuery)) {
        echo "<p style='color: green;'>✓ Successfully updated combo_name column to VARCHAR(500)</p>";
    } else {
        echo "<p style='color: red;'>✗ Error updating combo_name column: " . $connection->error . "</p>";
    }
    
    // Check current table structure
    echo "<h3>Current Table Structure:</h3>";
    $result = $connection->query("DESCRIBE dynamic_combos");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<p style='color: green; font-weight: bold;'>✓ Table update completed! You can now try creating combos again.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Homepage</a> | <a href='combos.php'>View Combos Page</a></p>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Combo Table</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h2, h3 { color: #333; }
        p { margin: 5px 0; }
        table { width: 100%; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <!-- Content is echoed above -->
</body>
</html>

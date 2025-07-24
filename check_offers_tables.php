<?php
/**
 * Check what offers-related tables exist and their data
 */

include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>Checking Offers Tables...</h2>";

// Check for tables with 'offer' in the name
echo "<h3>Tables containing 'offer':</h3>";
$result = $mysqli->query("SHOW TABLES LIKE '%offer%'");
if ($result) {
    while ($row = $result->fetch_array()) {
        echo "<p>✓ Found table: " . $row[0] . "</p>";
        
        // Show structure of each table
        $tableName = $row[0];
        echo "<h4>Structure of $tableName:</h4>";
        $desc = $mysqli->query("DESCRIBE $tableName");
        if ($desc) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($field = $desc->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$field['Field']}</td>";
                echo "<td>{$field['Type']}</td>";
                echo "<td>{$field['Null']}</td>";
                echo "<td>{$field['Key']}</td>";
                echo "<td>{$field['Default']}</td>";
                echo "<td>{$field['Extra']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Show data in each table
        echo "<h4>Data in $tableName:</h4>";
        $data = $mysqli->query("SELECT * FROM $tableName LIMIT 10");
        if ($data && $data->num_rows > 0) {
            echo "<p>Found " . $data->num_rows . " rows:</p>";
            echo "<pre>";
            while ($dataRow = $data->fetch_assoc()) {
                print_r($dataRow);
            }
            echo "</pre>";
        } else {
            echo "<p>No data found in $tableName</p>";
        }
        echo "<hr>";
    }
} else {
    echo "<p>No tables found with 'offer' in the name</p>";
}

// Also check if there's a simple 'offers' table
echo "<h3>Checking for 'offers' table specifically:</h3>";
$result = $mysqli->query("SHOW TABLES LIKE 'offers'");
if ($result && $result->num_rows > 0) {
    echo "<p>✓ 'offers' table exists</p>";
    
    // Show data
    $data = $mysqli->query("SELECT * FROM offers");
    if ($data && $data->num_rows > 0) {
        echo "<p>Data in offers table:</p>";
        echo "<pre>";
        while ($row = $data->fetch_assoc()) {
            print_r($row);
        }
        echo "</pre>";
    }
} else {
    echo "<p>No 'offers' table found</p>";
}

$mysqli->close();
?>

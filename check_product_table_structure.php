<?php
// Check Product Master Table Structure
include_once 'cms/includes/psl-config.php';

echo "<h2>🔍 Product Master Table Structure</h2>\n";

try {
    $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    // Get table structure
    $structureQuery = "DESCRIBE product_master";
    $result = $mysqli->query($structureQuery);
    
    if ($result) {
        echo "<h3>Table Structure:</h3>\n";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    // Check phantom products
    echo "<h3>Phantom Products Details:</h3>\n";
    
    $phantomQuery = "SELECT * FROM product_master WHERE ProductId IN (12, 15)";
    $phantomResult = $mysqli->query($phantomQuery);
    
    if ($phantomResult && $phantomResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr>";
        
        // Get column names
        $fields = $phantomResult->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>\n";
        
        // Reset result pointer
        $phantomResult->data_seek(0);
        
        while ($row = $phantomResult->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: orange;'>⚠️ Phantom products (IDs 12, 15) not found in product_master</p>\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>

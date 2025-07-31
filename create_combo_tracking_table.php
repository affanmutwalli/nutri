<?php
/**
 * Script to create the combo_order_tracking table
 */

echo "<h2>🗄️ Creating Combo Order Tracking Table</h2>";

try {
    include('database/dbconnection.php');
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    // Read the SQL file
    $sql = file_get_contents('database/combo_order_tracking_schema.sql');
    
    if (!$sql) {
        throw new Exception("Could not read SQL file");
    }
    
    echo "<h3>📄 SQL Content:</h3>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    // Execute the SQL
    if ($mysqli->multi_query($sql)) {
        echo "<h3>✅ Table Creation Result:</h3>";
        
        do {
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
            
            if ($mysqli->more_results()) {
                echo "<p>Processing next query...</p>";
            }
        } while ($mysqli->next_result());
        
        echo "<p>✅ Combo order tracking table created successfully!</p>";
        
        // Verify table exists
        $check_query = "SHOW TABLES LIKE 'combo_order_tracking'";
        $check_result = $mysqli->query($check_query);
        
        if ($check_result && $check_result->num_rows > 0) {
            echo "<p>✅ Table verification: combo_order_tracking table exists</p>";
            
            // Show table structure
            $structure_query = "DESCRIBE combo_order_tracking";
            $structure_result = $mysqli->query($structure_query);
            
            if ($structure_result) {
                echo "<h4>📋 Table Structure:</h4>";
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                
                while ($row = $structure_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<p>❌ Table verification failed: combo_order_tracking table not found</p>";
        }
        
    } else {
        throw new Exception("SQL execution failed: " . $mysqli->error);
    }
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='test_combo_order.php'>🔄 Test Combo Order Again</a></p>";
?>

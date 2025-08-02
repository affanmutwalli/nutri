<?php
// Check if pending_orders table exists and create if needed
header('Content-Type: text/html');

echo "<h2>Checking pending_orders Table</h2>";

try {
    $mysqli = new mysqli("localhost", "root", "", "my_nutrify_db");
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Database connection successful<br><br>";
    
    // Check if pending_orders table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'pending_orders'");
    
    if ($result && $result->num_rows > 0) {
        echo "✅ pending_orders table exists<br>";
        
        // Show table structure
        $structure = $mysqli->query("DESCRIBE pending_orders");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if there are any records
        $count = $mysqli->query("SELECT COUNT(*) as count FROM pending_orders");
        $countRow = $count->fetch_assoc();
        echo "<p>Records in table: " . $countRow['count'] . "</p>";
        
    } else {
        echo "❌ pending_orders table does NOT exist<br>";
        echo "<h3>Creating pending_orders table...</h3>";
        
        $createTable = "
        CREATE TABLE pending_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id VARCHAR(50) NOT NULL UNIQUE,
            order_data TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_order_id (order_id),
            INDEX idx_created_at (created_at)
        )";
        
        if ($mysqli->query($createTable)) {
            echo "✅ pending_orders table created successfully!<br>";
        } else {
            echo "❌ Error creating table: " . $mysqli->error . "<br>";
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

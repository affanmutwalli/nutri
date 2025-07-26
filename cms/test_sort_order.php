<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

sec_session_start();

if (login_check($mysqli) == true) {
    echo "<h2>Testing sort_order field</h2>";
    
    // Check if sort_order column exists
    $result = $mysqli->query("SHOW COLUMNS FROM model_images LIKE 'sort_order'");
    
    if ($result->num_rows > 0) {
        echo "<div style='color: green;'>✓ sort_order column exists</div>";
        
        // Test query with sort_order
        $testQuery = "SELECT ImageId, PhotoPath, sort_order FROM model_images LIMIT 5";
        $testResult = $mysqli->query($testQuery);
        
        if ($testResult) {
            echo "<div style='color: green;'>✓ Query with sort_order works</div>";
            echo "<h3>Sample data:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>ImageId</th><th>PhotoPath</th><th>sort_order</th></tr>";
            
            while ($row = $testResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['ImageId'] . "</td>";
                echo "<td>" . $row['PhotoPath'] . "</td>";
                echo "<td>" . $row['sort_order'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div style='color: red;'>✗ Query failed: " . $mysqli->error . "</div>";
        }
        
    } else {
        echo "<div style='color: red;'>✗ sort_order column does not exist</div>";
        echo "<p>Please run this SQL query first:</p>";
        echo "<code>ALTER TABLE model_images ADD COLUMN sort_order INT DEFAULT 0 AFTER PhotoPath;</code>";
    }
    
} else {
    echo "Access denied. Please login first.";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
div { margin: 5px 0; padding: 5px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
code { background: #f4f4f4; padding: 2px 4px; }
</style>

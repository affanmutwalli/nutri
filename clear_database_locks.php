<?php
/**
 * Clear Database Locks Script
 * This script forcefully clears all database locks and resets connections
 */

include('database/dbconnection.php');

echo "<h2>Database Lock Clearing Tool</h2>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<h3>1. Current Process List</h3>";
    $result = $mysqli->query("SHOW PROCESSLIST");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>User</th><th>Host</th><th>DB</th><th>Command</th><th>Time</th><th>State</th><th>Info</th></tr>";
        
        $processesToKill = [];
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Id'] . "</td>";
            echo "<td>" . $row['User'] . "</td>";
            echo "<td>" . $row['Host'] . "</td>";
            echo "<td>" . $row['db'] . "</td>";
            echo "<td>" . $row['Command'] . "</td>";
            echo "<td>" . $row['Time'] . "</td>";
            echo "<td>" . $row['State'] . "</td>";
            echo "<td>" . substr($row['Info'] ?? '', 0, 50) . "...</td>";
            echo "</tr>";

            // Mark long-running processes for killing (except current connection and system processes)
            if ($row['Time'] > 30 &&
                $row['Command'] != 'Sleep' &&
                $row['Command'] != 'Daemon' &&
                $row['User'] != 'event_scheduler' &&
                $row['Id'] != $mysqli->thread_id) {
                $processesToKill[] = $row['Id'];
            }
        }
        echo "</table>";
        
        // Kill long-running processes
        if (!empty($processesToKill)) {
            echo "<h3>2. Killing Long-Running Processes</h3>";
            foreach ($processesToKill as $processId) {
                $killResult = $mysqli->query("KILL $processId");
                if ($killResult) {
                    echo "✅ Killed process ID: $processId<br>";
                } else {
                    echo "❌ Failed to kill process ID: $processId<br>";
                }
            }
        } else {
            echo "<h3>2. No Long-Running Processes Found</h3>";
        }
    }
    
    echo "<h3>3. Clearing All Transactions</h3>";
    
    // Force rollback any open transactions
    $mysqli->query("ROLLBACK");
    echo "✅ Rolled back any open transactions<br>";
    
    // Reset autocommit
    $mysqli->autocommit(true);
    echo "✅ Reset autocommit to true<br>";
    
    echo "<h3>4. Checking Lock Status</h3>";
    
    // Check for table locks
    $lockResult = $mysqli->query("SHOW OPEN TABLES WHERE In_use > 0");
    if ($lockResult && $lockResult->num_rows > 0) {
        echo "❌ Found locked tables:<br>";
        while ($row = $lockResult->fetch_assoc()) {
            echo "- " . $row['Database'] . "." . $row['Table'] . " (In_use: " . $row['In_use'] . ")<br>";
        }
    } else {
        echo "✅ No locked tables found<br>";
    }
    
    // Check InnoDB status
    $innodbResult = $mysqli->query("SHOW ENGINE INNODB STATUS");
    if ($innodbResult) {
        $status = $innodbResult->fetch_assoc();
        $statusText = $status['Status'];
        
        // Look for deadlocks
        if (strpos($statusText, 'DEADLOCK') !== false) {
            echo "❌ Deadlock detected in InnoDB status<br>";
        } else {
            echo "✅ No deadlocks detected<br>";
        }
        
        // Look for lock waits
        if (strpos($statusText, 'lock wait') !== false) {
            echo "❌ Lock waits detected<br>";
        } else {
            echo "✅ No lock waits detected<br>";
        }
    }
    
    echo "<h3>5. Optimizing Tables</h3>";
    
    // Optimize key tables
    $tables = ['order_master', 'order_details', 'customer_master'];
    foreach ($tables as $table) {
        $optimizeResult = $mysqli->query("OPTIMIZE TABLE $table");
        if ($optimizeResult) {
            echo "✅ Optimized table: $table<br>";
        } else {
            echo "❌ Failed to optimize table: $table<br>";
        }
    }
    
    echo "<h3>6. Setting Optimal Configuration</h3>";
    
    // Set optimal settings for order processing
    $settings = [
        "SET SESSION innodb_lock_wait_timeout = 5",
        "SET SESSION lock_wait_timeout = 5",
        "SET SESSION autocommit = 1"
    ];
    
    foreach ($settings as $setting) {
        $result = $mysqli->query($setting);
        if ($result) {
            echo "✅ Applied: $setting<br>";
        } else {
            echo "❌ Failed: $setting<br>";
        }
    }
    
    echo "<h3>✅ Database Cleanup Complete!</h3>";
    echo "<p><strong>You can now try placing an order again.</strong></p>";
    
    echo "<h3>7. Quick Test</h3>";
    echo "<a href='checkout.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Checkout</a>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error During Cleanup</h3>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "<p>Try restarting MySQL service in Laragon.</p>";
}

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
h3 { color: #666; margin-top: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>

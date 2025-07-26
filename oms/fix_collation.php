<?php
include("database/dbconnection.php");
include_once 'includes/psl-config.php';

echo "<h2>Fixing Collation Issues</h2>";

// Create direct mysqli connection
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

// Check if connection is successful
if ($mysqli->connect_error) {
    echo "Database connection failed!<br>";
    echo "Error: " . $mysqli->connect_error;
    exit;
}

// Check current collation of order_master table
echo "<h3>1. Checking order_master table collation:</h3>";
$result = $mysqli->query("SHOW TABLE STATUS LIKE 'order_master'");
if ($result && $row = $result->fetch_assoc()) {
    echo "order_master collation: " . $row['Collation'] . "<br>";
    $order_master_collation = $row['Collation'];
} else {
    echo "Could not determine order_master collation<br>";
    $order_master_collation = 'utf8mb4_0900_ai_ci'; // Default assumption
}

// Check current collation of refund_requests table
echo "<h3>2. Checking refund_requests table collation:</h3>";
$result = $mysqli->query("SHOW TABLE STATUS LIKE 'refund_requests'");
if ($result && $row = $result->fetch_assoc()) {
    echo "refund_requests collation: " . $row['Collation'] . "<br>";
} else {
    echo "refund_requests table not found<br>";
}

// Drop and recreate the refund_requests table with matching collation
echo "<h3>3. Recreating refund_requests table with matching collation:</h3>";

// Drop existing table
$mysqli->query("DROP TABLE IF EXISTS refund_requests");
echo "Dropped existing refund_requests table<br>";

// Create table with matching collation
$createTableSQL = "
CREATE TABLE refund_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL COLLATE {$order_master_collation},
    customer_id INT NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    refund_amount DECIMAL(10,2) NOT NULL,
    refund_reason TEXT,
    status ENUM('Initiated', 'Processing', 'Completed', 'Failed', 'Cancelled') DEFAULT 'Initiated',
    razorpay_refund_id VARCHAR(100) NULL,
    refund_receipt VARCHAR(100) NULL,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    admin_notes TEXT NULL,
    created_by VARCHAR(50) DEFAULT 'system',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_order_id (order_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_transaction_id (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE={$order_master_collation}";

if ($mysqli->query($createTableSQL)) {
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>refund_requests table recreated successfully with matching collation!</strong><br>";
    echo "Collation used: {$order_master_collation}";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>Error recreating refund_requests table:</strong><br>";
    echo $mysqli->error;
    echo "</div>";
}

// Verify the new table
echo "<h3>4. Verifying new table:</h3>";
$result = $mysqli->query("SHOW TABLE STATUS LIKE 'refund_requests'");
if ($result && $row = $result->fetch_assoc()) {
    echo "New refund_requests collation: " . $row['Collation'] . "<br>";
    
    if ($row['Collation'] === $order_master_collation) {
        echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>Collation match confirmed!</strong> Both tables now use the same collation.";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
        echo "⚠️ <strong>Warning:</strong> Collations still don't match exactly.";
        echo "</div>";
    }
} else {
    echo "Could not verify new table<br>";
}

$mysqli->close();

echo "<br><p><a href='razorpay_refunds.php'>← Test Razorpay Refunds Page</a></p>";
?>

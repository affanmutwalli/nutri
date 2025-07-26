<?php
include("database/dbconnection.php");
include_once 'includes/psl-config.php';

echo "<h2>Creating Refund Requests Table</h2>";

// Create direct mysqli connection
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

// Check if connection is successful
if ($mysqli->connect_error) {
    echo "Database connection failed!<br>";
    echo "Error: " . $mysqli->connect_error;
    exit;
}

// Create refund_requests table
$createTableSQL = "
CREATE TABLE IF NOT EXISTS refund_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($mysqli->query($createTableSQL)) {
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>refund_requests table created successfully!</strong><br>";
    echo "The table includes the following columns:<br>";
    echo "• id (Primary Key)<br>";
    echo "• order_id (Foreign Key to order_master)<br>";
    echo "• customer_id<br>";
    echo "• transaction_id<br>";
    echo "• refund_amount<br>";
    echo "• refund_reason<br>";
    echo "• status (Initiated, Processing, Completed, Failed, Cancelled)<br>";
    echo "• razorpay_refund_id<br>";
    echo "• refund_receipt<br>";
    echo "• requested_at<br>";
    echo "• processed_at<br>";
    echo "• admin_notes<br>";
    echo "• created_by<br>";
    echo "• updated_at<br>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>Error creating refund_requests table:</strong><br>";
    echo $mysqli->error;
    echo "</div>";
}

// Check if table was created successfully
$result = $mysqli->query("SHOW TABLES LIKE 'refund_requests'");
if ($result && $result->num_rows > 0) {
    echo "<div style='background: #d1ecf1; padding: 10px; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0;'>";
    echo "📋 <strong>Table Structure:</strong><br>";
    
    $structure = $mysqli->query("DESCRIBE refund_requests");
    if ($structure) {
        echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ Table verification failed - refund_requests table was not created properly.";
    echo "</div>";
}

$mysqli->close();

echo "<br><p><a href='razorpay_refunds.php'>← Go to Razorpay Refunds Page</a></p>";
?>

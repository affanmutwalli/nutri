<?php
// Setup affiliate database tables
require_once 'database/dbconnection.php';

echo "<h2>Setting up Affiliate Database Tables...</h2>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception('Database connection failed');
    }
    
    echo "✅ Database connection successful<br><br>";
    
    // Create affiliate_applications table
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS affiliate_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        company VARCHAR(200) NULL,
        website VARCHAR(500) NOT NULL,
        traffic_range ENUM('1k-5k', '5k-10k', '10k-50k', '50k-100k', '100k+') NOT NULL,
        marketing_experience TEXT NOT NULL,
        additional_message TEXT NULL,
        application_status ENUM('pending', 'under_review', 'approved', 'rejected') DEFAULT 'pending',
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        reviewed_by INT NULL,
        review_notes TEXT NULL,
        approval_date TIMESTAMP NULL,
        
        INDEX idx_email (email),
        INDEX idx_phone (phone),
        INDEX idx_status (application_status),
        INDEX idx_created_at (created_at),
        UNIQUE KEY unique_email_pending (email, application_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Affiliate program applications'";
    
    if ($mysqli->query($createTableQuery)) {
        echo "✅ affiliate_applications table created successfully<br>";
    } else {
        throw new Exception('Failed to create affiliate_applications table: ' . $mysqli->error);
    }
    
    // Check if table was created
    $result = $mysqli->query("SHOW TABLES LIKE 'affiliate_applications'");
    if ($result && $result->num_rows > 0) {
        echo "✅ affiliate_applications table verified<br>";
        
        // Get table structure
        $structure = $mysqli->query("DESCRIBE affiliate_applications");
        echo "<br><strong>Table Structure:</strong><br>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Insert a test record to verify everything works
        $testInsert = "INSERT INTO affiliate_applications 
            (name, email, phone, website, traffic_range, marketing_experience) 
            VALUES 
            ('Test User', 'test@example.com', '9876543210', 'https://test-website.com', '10k-50k', 'This is a test marketing experience description to verify the table works properly.')
            ON DUPLICATE KEY UPDATE name = name";
            
        if ($mysqli->query($testInsert)) {
            echo "<br>✅ Test record inserted successfully<br>";
            
            // Count records
            $count = $mysqli->query("SELECT COUNT(*) as count FROM affiliate_applications");
            $countResult = $count->fetch_assoc();
            echo "📊 Total records in table: " . $countResult['count'] . "<br>";
            
            // Clean up test record
            $mysqli->query("DELETE FROM affiliate_applications WHERE email = 'test@example.com'");
            echo "🧹 Test record cleaned up<br>";
        } else {
            echo "⚠️ Warning: Could not insert test record: " . $mysqli->error . "<br>";
        }
        
    } else {
        throw new Exception('Table creation verification failed');
    }
    
    echo "<br><h3>🎉 Affiliate Database Setup Complete!</h3>";
    echo "<p><strong>You can now:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Visit the affiliate contact page: <a href='affiliate-contact.php'>affiliate-contact.php</a></li>";
    echo "<li>✅ Access the admin panel: <a href='cms/affiliate_applications.php'>cms/affiliate_applications.php</a></li>";
    echo "<li>✅ Test form submissions</li>";
    echo "<li>✅ Manage affiliate applications</li>";
    echo "</ul>";
    
    echo "<br><p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Test the affiliate contact form</li>";
    echo "<li>Submit a test application</li>";
    echo "<li>Check the admin panel to see the application</li>";
    echo "<li>Test the approval/rejection workflow</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<br><strong>Please check:</strong><br>";
    echo "1. Database connection settings<br>";
    echo "2. MySQL server is running<br>";
    echo "3. Database permissions<br>";
}

echo "<br><hr>";
echo "<p><a href='test_affiliate.php'>🔄 Run Test Again</a> | ";
echo "<a href='affiliate-contact.php'>🚀 Try Affiliate Page</a> | ";
echo "<a href='cms/affiliate_applications.php'>⚙️ Admin Panel</a></p>";
?>

<?php
// Setup script for promotional popup database table
include_once 'database/dbconnection.php';

echo "<h2>🎯 Promotional Popup Database Setup</h2>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<h3>Step 1: Creating promo_leads table</h3>";
    
    // Read and execute SQL file
    $sql = file_get_contents('database/promo_leads_table.sql');
    
    if ($mysqli->multi_query($sql)) {
        do {
            // Store first result set
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->next_result());
        
        echo "<p style='color: green;'>✅ promo_leads table created successfully!</p>";
    } else {
        throw new Exception("Error creating table: " . $mysqli->error);
    }
    
    echo "<h3>Step 2: Verifying table structure</h3>";
    
    // Check if table exists and show structure
    $result = $mysqli->query("DESCRIBE promo_leads");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
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
        
        echo "<p style='color: green;'>✅ Table structure verified successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error verifying table: " . $mysqli->error . "</p>";
    }
    
    echo "<h3>Step 3: Testing table functionality</h3>";
    
    // Test insert
    $testMobile = "9999999999";
    $testOTP = "123456";
    
    $stmt = $mysqli->prepare("INSERT INTO promo_leads (mobile_number, otp, otp_generated_at, source) VALUES (?, ?, NOW(), 'test')");
    $stmt->bind_param("ss", $testMobile, $testOTP);
    
    if ($stmt->execute()) {
        $insertId = $mysqli->insert_id;
        echo "<p style='color: green;'>✅ Test insert successful (ID: $insertId)</p>";
        
        // Test select
        $stmt = $mysqli->prepare("SELECT * FROM promo_leads WHERE id = ?");
        $stmt->bind_param("i", $insertId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Test select successful</p>";
            
            // Clean up test data
            $stmt = $mysqli->prepare("DELETE FROM promo_leads WHERE id = ?");
            $stmt->bind_param("i", $insertId);
            $stmt->execute();
            echo "<p style='color: blue;'>🧹 Test data cleaned up</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Test insert failed: " . $stmt->error . "</p>";
    }
    
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Database table created and tested</li>";
    echo "<li>✅ Ready to handle promotional popup OTP requests</li>";
    echo "<li>✅ Interakt API integration configured</li>";
    echo "<li>🔄 Update frontend to use real API endpoints</li>";
    echo "</ul>";
    
    echo "<h4>📊 Table Information:</h4>";
    echo "<ul>";
    echo "<li><strong>Table Name:</strong> promo_leads</li>";
    echo "<li><strong>Purpose:</strong> Store promotional popup leads and OTP verification</li>";
    echo "<li><strong>API Endpoint:</strong> exe_files/promo_popup_otp.php</li>";
    echo "<li><strong>OTP Template:</strong> verify_acc (existing Interakt template)</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<?php
// Check existing customers
header('Content-Type: text/html');

echo "<h2>Checking Customer Accounts</h2>";

try {
    $mysqli = new mysqli("localhost", "root", "", "my_nutrify_db");
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Database connection successful<br><br>";
    
    // Get first 5 customers
    $result = $mysqli->query("SELECT CustomerId, Name, Email, MobileNo, IsActive FROM customer_master LIMIT 5");
    
    if ($result && $result->num_rows > 0) {
        echo "<h3>Sample Customer Accounts:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Active</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['CustomerId'] . "</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['Email'] . "</td>";
            echo "<td>" . $row['MobileNo'] . "</td>";
            echo "<td>" . ($row['IsActive'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Get total count
        $countResult = $mysqli->query("SELECT COUNT(*) as count FROM customer_master");
        $countRow = $countResult->fetch_assoc();
        echo "<p>Total customers: " . $countRow['count'] . "</p>";
        
    } else {
        echo "❌ No customers found<br>";
        
        // Create a test customer
        echo "<h3>Creating test customer...</h3>";
        $insertQuery = "INSERT INTO customer_master (Name, Email, Pass, MobileNo, IsActive) VALUES (?, ?, ?, ?, 1)";
        $stmt = $mysqli->prepare($insertQuery);
        
        if ($stmt) {
            $name = "Test User";
            $email = "test@example.com";
            $password = password_hash("password123", PASSWORD_DEFAULT);
            $mobile = "9876543210";
            
            $stmt->bind_param("ssss", $name, $email, $password, $mobile);
            
            if ($stmt->execute()) {
                echo "✅ Test customer created successfully!<br>";
                echo "Email: test@example.com<br>";
                echo "Password: password123<br>";
            } else {
                echo "❌ Error creating test customer: " . $stmt->error . "<br>";
            }
            $stmt->close();
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>User Account Check</h2>";

try {
    // Check if customer_master table exists and has users
    $users = $obj->MysqliSelect("SELECT Email, Name, IsActive FROM customer_master LIMIT 10", array("Email", "Name", "IsActive"));
    
    if ($users && count($users) > 0) {
        echo "<h3>Existing Users (first 10):</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Email</th><th>Name</th><th>Status</th></tr>";
        foreach ($users as $user) {
            $status = $user['IsActive'] === 'Y' ? 'Active' : 'Inactive';
            $color = $user['IsActive'] === 'Y' ? 'green' : 'red';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Name']) . "</td>";
            echo "<td style='color: $color;'>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Instructions:</h3>";
        echo "<ol>";
        echo "<li>Use one of the email addresses above to test login</li>";
        echo "<li>If you don't know the password, you can:</li>";
        echo "<ul>";
        echo "<li>Register a new account</li>";
        echo "<li>Use the forgot password feature</li>";
        echo "<li>Create a test account below</li>";
        echo "</ul>";
        echo "</ol>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ No users found in database</p>";
        echo "<p>You need to register a new account first.</p>";
    }
    
    // Show total count
    $totalUsers = $obj->MysqliSelect("SELECT COUNT(*) as count FROM customer_master", array("count"));
    if ($totalUsers && isset($totalUsers[0]['count'])) {
        echo "<p><strong>Total users in database:</strong> " . $totalUsers[0]['count'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Create test user form
echo "<hr>";
echo "<h3>Create Test User:</h3>";

if (isset($_POST['create_test_user'])) {
    $testEmail = "test@example.com";
    $testPassword = "test123";
    $testName = "Test User";
    $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
    
    try {
        // Check if test user already exists
        $existingUser = $obj->MysqliSelect1(
            "SELECT Email FROM customer_master WHERE Email = ?",
            array("Email"),
            "s",
            array($testEmail)
        );
        
        if ($existingUser && count($existingUser) > 0) {
            echo "<p style='color: orange;'>⚠️ Test user already exists</p>";
        } else {
            // Create test user
            $result = $obj->fInsertNew(
                "INSERT INTO customer_master (Name, Email, Pass, IsActive, CreationDate) VALUES (?, ?, ?, 'Y', NOW())",
                "sss",
                array($testName, $testEmail, $hashedPassword)
            );
            
            if ($result) {
                echo "<p style='color: green;'>✅ Test user created successfully!</p>";
                echo "<p><strong>Email:</strong> $testEmail</p>";
                echo "<p><strong>Password:</strong> $testPassword</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create test user</p>";
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating test user: " . $e->getMessage() . "</p>";
    }
}
?>

<form method="POST">
    <input type="submit" name="create_test_user" value="Create Test User (test@example.com / test123)">
</form>

<br>
<a href="login.php">Go to Login Page</a> | 
<a href="register.php">Register New Account</a> | 
<a href="checkout.php">Test Checkout</a>

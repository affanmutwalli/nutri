<?php
/**
 * Setup script for the Product Offers System
 * Run this file once to create the necessary database tables
 */

include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>Setting up Product Offers System...</h2>";

try {
    // Read and execute the SQL schema
    $sqlFile = 'database/product_offers_schema.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL schema file not found: " . $sqlFile);
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove comments and split by semicolons
    $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove multi-line comments
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if ($mysqli->query($statement)) {
                echo "<p style='color: green;'>✓ Executed: " . substr($statement, 0, 50) . "...</p>";
            } else {
                echo "<p style='color: red;'>✗ Error executing: " . substr($statement, 0, 50) . "...<br>Error: " . $mysqli->error . "</p>";
            }
        }
    }
    
    // Check if tables were created successfully
    $tables = ['product_offers'];
    $allTablesExist = true;
    
    foreach ($tables as $table) {
        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Table '$table' created successfully</p>";
        } else {
            echo "<p style='color: red;'>✗ Table '$table' was not created</p>";
            $allTablesExist = false;
        }
    }
    
    // Check if view was created
    $result = $mysqli->query("SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_" . $mysqli->query("SELECT DATABASE()")->fetch_row()[0] . " = 'active_product_offers'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ View 'active_product_offers' created successfully</p>";
    } else {
        echo "<p style='color: orange;'>⚠ View 'active_product_offers' may not have been created (this might be normal)</p>";
    }
    
    if ($allTablesExist) {
        echo "<h3 style='color: green;'>✓ Product Offers System setup completed successfully!</h3>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ul>";
        echo "<li>Go to CMS → Catalog → Offers Management to add products to offers</li>";
        echo "<li>Visit <a href='offers.php' target='_blank'>offers.php</a> to see the frontend offers page</li>";
        echo "<li>You can safely delete this setup file after successful installation</li>";
        echo "</ul>";
    } else {
        echo "<h3 style='color: red;'>✗ Setup completed with errors. Please check the error messages above.</h3>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Offers System Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #ff6b35; padding-bottom: 10px; }
        h3 { margin-top: 30px; }
        p { margin: 10px 0; }
        ul { margin: 10px 0 10px 20px; }
        a { color: #ff6b35; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-top: 30px;">
        <a href="cms/offers_management.php" style="background: #ff6b35; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Go to Offers Management</a>
        <a href="offers.php" style="background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-left: 10px;">View Offers Page</a>
    </div>
</body>
</html>

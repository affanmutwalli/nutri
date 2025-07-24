<?php
/**
 * Remove Wild Amla Juice from offers
 */

include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>Removing Wild Amla Juice from Offers...</h2>";

// Product ID 6 is the Wild Amla Juice
$productId = 6;

// First check if it exists in offers
$check_query = "SELECT * FROM product_offers WHERE product_id = ?";
$stmt = $mysqli->prepare($check_query);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $offer = $result->fetch_assoc();
    echo "<p>Found offer for Wild Amla Juice:</p>";
    echo "<pre>";
    print_r($offer);
    echo "</pre>";
    
    // Remove it by setting is_active to 0
    $update_query = "UPDATE product_offers SET is_active = 0, updated_date = CURRENT_TIMESTAMP WHERE product_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("i", $productId);
    
    if ($update_stmt->execute()) {
        echo "<p style='color: green;'>✓ Successfully removed Wild Amla Juice from offers</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to remove Wild Amla Juice from offers: " . $mysqli->error . "</p>";
    }
} else {
    echo "<p style='color: orange;'>Wild Amla Juice not found in product_offers table</p>";
}

// Check current active offers
echo "<h3>Current Active Offers:</h3>";
$active_query = "SELECT po.*, pm.ProductName 
                FROM product_offers po 
                INNER JOIN product_master pm ON po.product_id = pm.ProductId 
                WHERE po.is_active = 1";
$active_result = $mysqli->query($active_query);

if ($active_result && $active_result->num_rows > 0) {
    echo "<p>Active offers remaining:</p>";
    echo "<ul>";
    while ($row = $active_result->fetch_assoc()) {
        echo "<li>Product ID: {$row['product_id']} - {$row['ProductName']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No active offers found</p>";
}

echo "<p style='color: blue;'><strong>Done! You can now check the offers page.</strong></p>";

$mysqli->close();
?>

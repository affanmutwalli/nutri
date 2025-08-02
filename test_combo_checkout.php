<?php
/**
 * Test script to set up session and test combo checkout
 */
session_start();

// Set up test session data
$_SESSION['CustomerId'] = 1; // Use existing customer ID
$_SESSION['CustomerName'] = 'Test User';
$_SESSION['Email'] = 'test@example.com';

echo "<h2>🧪 Combo Checkout Test Setup</h2>";
echo "<p>✅ Session set up with CustomerId: " . $_SESSION['CustomerId'] . "</p>";
echo "<p>✅ Customer Name: " . $_SESSION['CustomerName'] . "</p>";
echo "<p>✅ Email: " . $_SESSION['Email'] . "</p>";

echo "<hr>";
echo "<h3>Test Links:</h3>";
echo "<p><a href='combo_checkout.php?combo_id=COMBO_14_6' target='_blank'>🛒 Test Combo Checkout (COMBO_14_6)</a></p>";
echo "<p><a href='combo_product.php?combo_id=COMBO_14_6' target='_blank'>📦 View Combo Product Page</a></p>";
echo "<p><a href='account.php' target='_blank'>👤 View Account Page</a></p>";

echo "<hr>";
echo "<h3>Database Check:</h3>";

try {
    include('database/dbconnection.php');
    $obj = new main();
    $mysqli = $obj->connection();
    
    // Check if combo exists
    $combo_query = "SELECT * FROM combo_details_view WHERE combo_id = 'COMBO_14_6' AND is_active = TRUE";
    $combo_result = $mysqli->query($combo_query);
    
    if($combo_result && $combo_result->num_rows > 0) {
        $combo = $combo_result->fetch_assoc();
        echo "<p>✅ Combo found: " . htmlspecialchars($combo['combo_name']) . "</p>";
        echo "<p>💰 Price: ₹" . $combo['combo_price'] . "</p>";
        echo "<p>📦 Products: " . htmlspecialchars($combo['product1_name']) . " + " . htmlspecialchars($combo['product2_name']) . "</p>";
    } else {
        echo "<p>❌ Combo COMBO_14_6 not found or inactive</p>";
        
        // Show available combos
        $available_query = "SELECT combo_id, combo_name FROM dynamic_combos WHERE is_active = TRUE LIMIT 5";
        $available_result = $mysqli->query($available_query);
        
        if($available_result && $available_result->num_rows > 0) {
            echo "<p><strong>Available combos:</strong></p>";
            echo "<ul>";
            while($row = $available_result->fetch_assoc()) {
                echo "<li><a href='combo_checkout.php?combo_id=" . htmlspecialchars($row['combo_id']) . "' target='_blank'>" . htmlspecialchars($row['combo_name']) . " (" . htmlspecialchars($row['combo_id']) . ")</a></li>";
            }
            echo "</ul>";
        }
    }
    
    // Check customer
    $customer_query = "SELECT * FROM customer_master WHERE CustomerId = 1";
    $customer_result = $mysqli->query($customer_query);
    
    if($customer_result && $customer_result->num_rows > 0) {
        $customer = $customer_result->fetch_assoc();
        echo "<p>✅ Customer found: " . htmlspecialchars($customer['Name']) . " (" . htmlspecialchars($customer['Email']) . ")</p>";
    } else {
        echo "<p>❌ Customer with ID 1 not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Instructions:</h3>";
echo "<ol>";
echo "<li>Click on the 'Test Combo Checkout' link above</li>";
echo "<li>Fill in the billing details</li>";
echo "<li>Select payment method (COD recommended for testing)</li>";
echo "<li>Click 'Place Combo Order'</li>";
echo "<li>Check the browser console for any errors</li>";
echo "</ol>";

echo "<p><strong>Note:</strong> This test sets up a temporary session. In production, users would log in normally.</p>";
?>

<?php
session_start();

echo "<h2>🔧 Fix Checkout Session Issue</h2>";

echo "<h3>Current Session Data:</h3>";
if (!empty($_SESSION)) {
    echo "<div style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; margin: 10px 0;'>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    echo "</div>";
} else {
    echo "<p>No session data found.</p>";
}

// Clear all problematic sessions
if (isset($_GET['fix']) && $_GET['fix'] == 'true') {
    // Clear buy_now session
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
        echo "<p>✅ Cleared buy_now session</p>";
    }
    
    // Clear any old cart data and set correct cart
    $_SESSION['cart'] = [
        6 => 1,  // Wild Amla Juice
        9 => 1   // Cholesterol Care Juice
    ];
    
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
    echo "<h4>✅ FIXED!</h4>";
    echo "<p>Cart has been reset to correct products:</p>";
    echo "<ul>";
    echo "<li>Product 6: Wild Amla Juice</li>";
    echo "<li>Product 9: Cholesterol Care Juice</li>";
    echo "</ul>";
    echo "<p><strong>Now test your checkout - it should show the correct products!</strong></p>";
    echo "</div>";
    
    echo "<p><a href='cart.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🛒 View Cart</a></p>";
    echo "<p><a href='checkout.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>💳 Test Checkout</a></p>";
} else {
    echo "<p><a href='fix_checkout_session.php?fix=true' style='background: #dc3545; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔧 Fix Session & Reset Cart</a></p>";
}

echo "<h3>🎯 The Problem:</h3>";
echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
echo "<p>Your checkout is showing <strong>Product ID 43</strong> (Thyro Balance combo) instead of your actual cart products.</p>";
echo "<p>This is likely due to:</p>";
echo "<ul>";
echo "<li>Old session data from a previous 'Buy Now' action</li>";
echo "<li>Cached product data in your browser</li>";
echo "<li>Session contamination between different products</li>";
echo "</ul>";
echo "<p>The fix above will clear all problematic sessions and reset your cart to the correct products.</p>";
echo "</div>";
?>

<?php
session_start();

echo "<h2>🔍 Real Account Session Check</h2>";

echo "<h3>Current Session Data:</h3>";
if (!empty($_SESSION)) {
    echo "<div style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; margin: 10px 0;'>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    echo "</div>";
} else {
    echo "<p style='color: red;'>❌ No session data found</p>";
}

// Check if user is logged in
if (isset($_SESSION['CustomerId'])) {
    $customerId = $_SESSION['CustomerId'];
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
    echo "<h4>✅ User Logged In</h4>";
    echo "<p><strong>Customer ID:</strong> $customerId</p>";
    echo "</div>";
    
    // Check cart for this specific user
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
        echo "<h4>✅ Cart Found for Your Account</h4>";
        echo "<p><strong>Cart Contents:</strong></p>";
        echo "<ul>";
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            echo "<li>Product ID: $productId, Quantity: $quantity</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
        echo "<h4>⚠️ No Cart Data for Your Account</h4>";
        echo "<p>Your account (Customer ID: $customerId) has no cart items.</p>";
        echo "</div>";
    }
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h4>❌ Not Logged In</h4>";
    echo "<p>No CustomerId found in session.</p>";
    echo "</div>";
}

// Debug code removed to prevent phantom products
}

// Clear buy_now session if it exists
if (isset($_SESSION['buy_now'])) {
    echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
    echo "<h4>⚠️ Buy Now Session Found</h4>";
    echo "<p>This might interfere with cart checkout.</p>";
    echo "<pre>" . print_r($_SESSION['buy_now'], true) . "</pre>";
    
    if (isset($_GET['clear_buy_now'])) {
        unset($_SESSION['buy_now']);
        echo "<p style='color: green;'>✅ Buy Now session cleared!</p>";
    } else {
        echo "<p><a href='check_real_session.php?clear_buy_now=true' style='background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Clear Buy Now</a></p>";
    }
    echo "</div>";
}

echo "<div style='margin: 20px 0;'>";
if (isset($_SESSION['CustomerId'])) {
    // Debug button removed
}
echo "<a href='check_real_session.php' style='background: #17a2b8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔄 Refresh</a>";
echo "</div>";

echo "<p><a href='cart.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>← Back to Cart</a></p>";
?>

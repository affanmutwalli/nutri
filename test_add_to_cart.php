<?php
session_start();
echo "<h2>🛒 Add to Cart Test</h2>";

// Test the add to cart endpoint directly
if (isset($_POST['test_add_to_cart'])) {
    $productId = $_POST['product_id'];
    
    echo "<h3>Testing Add to Cart:</h3>";
    echo "Product ID: " . htmlspecialchars($productId) . "<br><br>";
    
    // Make a POST request to the add to cart endpoint
    $postData = http_build_query(array(
        'action' => 'add_to_cart',
        'productId' => $productId
    ));
    
    $context = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postData
        )
    ));
    
    $response = file_get_contents('http://localhost/nutrify/exe_files/add_to_cart_session.php', false, $context);
    
    echo "<h3>Raw Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    echo "<h3>Response Analysis:</h3>";
    
    // Try to decode JSON
    $jsonData = json_decode($response, true);
    if ($jsonData !== null) {
        echo "<p style='color: green;'>✅ Valid JSON response</p>";
        echo "<pre>" . json_encode($jsonData, JSON_PRETTY_PRINT) . "</pre>";
        
        if (isset($jsonData['status'])) {
            if ($jsonData['status'] === 'success') {
                echo "<p style='color: green;'>✅ Add to cart successful!</p>";
            } else {
                echo "<p style='color: red;'>❌ Add to cart failed: " . ($jsonData['message'] ?? 'Unknown error') . "</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Invalid JSON response</p>";
        echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
        
        // Check for common issues
        if (strpos($response, 'Warning:') !== false || strpos($response, 'Notice:') !== false || strpos($response, 'Fatal error:') !== false) {
            echo "<p style='color: red;'>❌ PHP errors detected in response</p>";
        }
        
        if (strpos($response, '<') !== false) {
            echo "<p style='color: red;'>❌ HTML content detected in response (should be pure JSON)</p>";
        }
    }
    
    // Check current cart contents
    echo "<h3>Current Cart Contents:</h3>";
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo "<table border='1'>";
        echo "<tr><th>Product ID</th><th>Quantity</th></tr>";
        foreach ($_SESSION['cart'] as $pid => $qty) {
            echo "<tr><td>$pid</td><td>$qty</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Cart is empty</p>";
    }
}
?>

<form method="POST">
    <h3>Test Add to Cart:</h3>
    <table>
        <tr>
            <td>Product ID:</td>
            <td>
                <select name="product_id" required>
                    <option value="">Select Product</option>
                    <option value="1">Product 1</option>
                    <option value="2">Product 2</option>
                    <option value="3">Product 3</option>
                    <option value="6">Product 6</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="test_add_to_cart" value="Test Add to Cart">
            </td>
        </tr>
    </table>
</form>

<h3>JavaScript Console Test:</h3>
<p>Open browser console (F12) and test the add to cart functionality:</p>

<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <h4>Test Product 6 Add to Cart:</h4>
    <button class="add-to-cart-session" data-product-id="6" style="background: #ec6504; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
        Add Product 6 to Cart
    </button>
</div>

<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <h4>Test Product 1 Add to Cart:</h4>
    <button class="add-to-cart-session" data-product-id="1" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
        Add Product 1 to Cart
    </button>
</div>

<h3>Expected Results:</h3>
<ul>
    <li>✅ <strong>Backend test:</strong> Should return JSON success response</li>
    <li>✅ <strong>JavaScript test:</strong> Should show popup and add to cart</li>
    <li>✅ <strong>No console errors:</strong> No jQuery or addEventListener errors</li>
    <li>✅ <strong>Cart update:</strong> Product should appear in cart session</li>
</ul>

<br>
<a href="product_details.php?ProductId=6">Test Product Details Page</a> | 
<a href="cart.php">View Cart</a>

<!-- Include jQuery and test scripts -->
<script src="js/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    
    // Handle add-to-cart for session-based cart
    $(document).on('click', '.add-to-cart-session', function () {
        const productId = $(this).data('product-id');
        
        if (!productId) {
            console.error('Product ID is missing.');
            alert('Unable to add to cart. Product ID is missing.');
            return;
        }

        console.log('Adding product to cart:', productId);

        // Perform AJAX request
        $.ajax({
            url: 'exe_files/add_to_cart_session.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'add_to_cart',
                productId: productId
            },
            success: function (response) {
                console.log('Add to cart response:', response);
                if (response.status === 'success') {
                    alert('Product added to cart successfully!');
                    // Reload page to see updated cart
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert(response.message || 'Failed to add product to cart.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response text:', xhr.responseText);
                alert('An error occurred while processing your request. Please try again.');
            }
        });
    });
});
</script>

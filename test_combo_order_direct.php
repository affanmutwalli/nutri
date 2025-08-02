<?php
/**
 * Direct test of combo order functionality
 */
session_start();

// Set up test session data
$_SESSION['CustomerId'] = 1;
$_SESSION['CustomerName'] = 'Test User';
$_SESSION['Email'] = 'test@example.com';

echo "<h2>🧪 Direct Combo Order Test</h2>";

try {
    include('database/dbconnection.php');
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    // Test data
    $combo_id = 'COMBO_14_6';
    $quantity = 1;
    $customer_name = 'Test Customer';
    $customer_email = 'test@example.com';
    $customer_phone = '9876543210';
    $customer_address = 'Guruvar Peth New address, Near Priyadarshini Hotel, Sangli, Maharashtra - 416410';
    $final_total = 628.20;
    $customerId = 1;
    
    echo "<h3>📋 Test Parameters:</h3>";
    echo "<ul>";
    echo "<li><strong>Combo ID:</strong> $combo_id</li>";
    echo "<li><strong>Quantity:</strong> $quantity</li>";
    echo "<li><strong>Customer:</strong> $customer_name ($customer_email)</li>";
    echo "<li><strong>Phone:</strong> $customer_phone</li>";
    echo "<li><strong>Address:</strong> $customer_address</li>";
    echo "<li><strong>Total:</strong> ₹$final_total</li>";
    echo "<li><strong>Customer ID:</strong> $customerId</li>";
    echo "</ul>";
    
    // Step 1: Verify combo exists
    echo "<h3>🔍 Step 1: Verify Combo</h3>";
    $combo_query = "SELECT * FROM combo_details_view WHERE combo_id = ? AND is_active = TRUE";
    $combo_stmt = $mysqli->prepare($combo_query);
    $combo_stmt->bind_param("s", $combo_id);
    $combo_stmt->execute();
    $combo_result = $combo_stmt->get_result();
    
    if ($combo_result->num_rows == 0) {
        throw new Exception("Combo not found or inactive: $combo_id");
    }
    
    $combo = $combo_result->fetch_assoc();
    echo "<p>✅ Combo found: " . htmlspecialchars($combo['combo_name']) . "</p>";
    echo "<p>💰 Price: ₹" . $combo['combo_price'] . "</p>";
    $combo_stmt->close();
    
    // Step 2: Generate Order ID
    echo "<h3>🆔 Step 2: Generate Order ID</h3>";
    $order_query = "SELECT OrderId FROM order_master WHERE OrderId LIKE 'CB%' ORDER BY OrderId DESC LIMIT 1";
    $order_result = $mysqli->query($order_query);
    
    $next_number = 1;
    if ($order_result && $order_result->num_rows > 0) {
        $last_order = $order_result->fetch_assoc();
        $last_number = intval(substr($last_order['OrderId'], 2));
        $next_number = $last_number + 1;
    }
    
    $order_id = 'CB' . str_pad($next_number, 6, '0', STR_PAD_LEFT);
    echo "<p>✅ Generated Order ID: $order_id</p>";
    
    // Step 3: Insert Order
    echo "<h3>💾 Step 3: Insert Order</h3>";
    $order_date = date('Y-m-d H:i:s');
    $amount = $final_total;
    
    $insertOrderQuery = "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt)
                         VALUES (?, ?, 'Registered', ?, ?, 'Due', 'Placed', ?, 'COD', 'NA', ?)";

    $orderStmt = $mysqli->prepare($insertOrderQuery);
    $orderStmt->bind_param("sisdss", $order_id, $customerId, $order_date, $amount, $customer_address, $order_date);
    
    if ($orderStmt->execute()) {
        echo "<p>✅ Order inserted successfully</p>";
        $orderStmt->close();
    } else {
        throw new Exception("Failed to insert order: " . $orderStmt->error);
    }
    
    // Step 4: Insert Order Details (Split combo into individual products)
    echo "<h3>📦 Step 4: Insert Order Details</h3>";
    
    // Get individual products from combo and their prices
    $product1_id = $combo['product1_id'];
    $product2_id = $combo['product2_id'];

    // Get product prices from product_price table (use first available price)
    $price1_query = "SELECT OfferPrice FROM product_price WHERE ProductId = ? AND OfferPrice IS NOT NULL AND OfferPrice != '' LIMIT 1";
    $price1_stmt = $mysqli->prepare($price1_query);
    $price1_stmt->bind_param("i", $product1_id);
    $price1_stmt->execute();
    $price1_result = $price1_stmt->get_result();
    $product1_price = 449; // Default fallback
    if ($price1_result->num_rows > 0) {
        $price1_row = $price1_result->fetch_assoc();
        $product1_price = floatval($price1_row['OfferPrice']);
    }
    $price1_stmt->close();

    $price2_query = "SELECT OfferPrice FROM product_price WHERE ProductId = ? AND OfferPrice IS NOT NULL AND OfferPrice != '' LIMIT 1";
    $price2_stmt = $mysqli->prepare($price2_query);
    $price2_stmt->bind_param("i", $product2_id);
    $price2_stmt->execute();
    $price2_result = $price2_stmt->get_result();
    $product2_price = 249; // Default fallback
    if ($price2_result->num_rows > 0) {
        $price2_row = $price2_result->fetch_assoc();
        $product2_price = floatval($price2_row['OfferPrice']);
    }
    $price2_stmt->close();

    echo "<p>📊 Product prices: Product 1 (ID: $product1_id) = ₹$product1_price, Product 2 (ID: $product2_id) = ₹$product2_price</p>";

    // Insert product 1 (using SubTotal instead of Amount, and converting prices to int)
    $insertDetailQuery = "INSERT INTO order_details (OrderId, ProductId, Quantity, Price, SubTotal) VALUES (?, ?, ?, ?, ?)";
    $detailStmt = $mysqli->prepare($insertDetailQuery);

    $product1_price_int = intval($product1_price);
    $product1_subtotal = $product1_price_int * $quantity;
    $detailStmt->bind_param("siiii", $order_id, $product1_id, $quantity, $product1_price_int, $product1_subtotal);

    if ($detailStmt->execute()) {
        echo "<p>✅ Product 1 detail inserted (ID: $product1_id, SubTotal: ₹$product1_subtotal)</p>";
    } else {
        throw new Exception("Failed to insert product 1 detail: " . $detailStmt->error);
    }

    // Insert product 2
    $product2_price_int = intval($product2_price);
    $product2_subtotal = $product2_price_int * $quantity;
    $detailStmt->bind_param("siiii", $order_id, $product2_id, $quantity, $product2_price_int, $product2_subtotal);

    if ($detailStmt->execute()) {
        echo "<p>✅ Product 2 detail inserted (ID: $product2_id, SubTotal: ₹$product2_subtotal)</p>";
    } else {
        throw new Exception("Failed to insert product 2 detail: " . $detailStmt->error);
    }
    
    $detailStmt->close();
    
    // Step 5: Insert Combo Tracking
    echo "<h3>📊 Step 5: Insert Combo Tracking</h3>";
    $trackingQuery = "INSERT INTO combo_order_tracking (order_id, combo_id, combo_name, combo_price, quantity, total_amount) 
                      VALUES (?, ?, ?, ?, ?, ?)";
    
    $trackingStmt = $mysqli->prepare($trackingQuery);
    $trackingStmt->bind_param("sssdid", $order_id, $combo_id, $combo['combo_name'], $combo['combo_price'], $quantity, $final_total);
    
    if ($trackingStmt->execute()) {
        echo "<p>✅ Combo tracking record inserted</p>";
        $trackingStmt->close();
    } else {
        throw new Exception("Failed to insert combo tracking: " . $trackingStmt->error);
    }
    
    // Success!
    echo "<h3>🎉 Order Placement Successful!</h3>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ Order Summary:</h4>";
    echo "<ul>";
    echo "<li><strong>Order ID:</strong> $order_id</li>";
    echo "<li><strong>Customer:</strong> $customer_name</li>";
    echo "<li><strong>Combo:</strong> " . htmlspecialchars($combo['combo_name']) . "</li>";
    echo "<li><strong>Quantity:</strong> $quantity</li>";
    echo "<li><strong>Total Amount:</strong> ₹$final_total</li>";
    echo "<li><strong>Payment Method:</strong> Cash on Delivery</li>";
    echo "<li><strong>Status:</strong> Placed</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>🔗 Quick Links:</h3>";
echo "<ul>";
echo "<li><a href='combo_checkout.php?combo_id=COMBO_14_6'>🛒 Test Combo Checkout Page</a></li>";
echo "<li><a href='test_combo_checkout.php'>🧪 Test Setup Page</a></li>";
echo "<li><a href='combo_product.php?combo_id=COMBO_14_6'>📦 View Combo Product</a></li>";
echo "</ul>";
?>

<?php
session_start();
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🔍 Cart and Checkout Debug Analysis</h2>";

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "<p style='color: red;'>❌ User not logged in. Please login first.</p>";
    echo "<p><a href='login.php'>Login</a></p>";
    exit;
}

$customerId = $_SESSION['customer_id'];
echo "<p><strong>Customer ID:</strong> $customerId</p>";

echo "<h3>1. Session Cart Contents:</h3>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Product ID</th><th>Quantity</th></tr>";
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        echo "<tr><td>$productId</td><td>$quantity</td></tr>";
    }
    echo "</table>";
    echo "<p><strong>Total unique products in session cart:</strong> " . count($_SESSION['cart']) . "</p>";
} else {
    echo "<p>❌ No items in session cart</p>";
}

echo "<h3>2. Database Cart Contents:</h3>";
try {
    $cartQuery = "SELECT * FROM cart WHERE CustomerId = ?";
    $stmt = $mysqli->prepare($cartQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Customer ID</th><th>Product ID</th><th>Quantity</th><th>Price</th><th>Created Date</th><th>Updated Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No items in database cart</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fetching cart: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Product Details for Cart Items:</h3>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        echo "<h4>Product ID: $productId</h4>";
        
        // Get product details
        $productQuery = "SELECT ProductId, ProductName, ProductCode FROM product_master WHERE ProductId = ?";
        $stmt = $mysqli->prepare($productQuery);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Product ID</th><th>Product Name</th><th>Product Code</th></tr>";
            echo "<tr><td>{$row['ProductId']}</td><td>{$row['ProductName']}</td><td>{$row['ProductCode']}</td></tr>";
            echo "</table>";
            
            // Get product price
            $priceQuery = "SELECT OfferPrice, MRP FROM product_price WHERE ProductId = ?";
            $stmt = $mysqli->prepare($priceQuery);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $priceResult = $stmt->get_result();
            
            if ($priceRow = $priceResult->fetch_assoc()) {
                echo "<p><strong>Offer Price:</strong> ₹{$priceRow['OfferPrice']} | <strong>MRP:</strong> ₹{$priceRow['MRP']}</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Product not found in database</p>";
        }
    }
}

echo "<h3>4. Recent Orders for this Customer:</h3>";
try {
    $recentOrdersQuery = "SELECT OrderId, OrderDate, Amount, PaymentStatus, OrderStatus FROM order_master WHERE CustomerId = ? ORDER BY CreatedAt DESC LIMIT 5";
    $stmt = $mysqli->prepare($recentOrdersQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Order ID</th><th>Order Date</th><th>Amount</th><th>Payment Status</th><th>Order Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No recent orders found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fetching orders: " . $e->getMessage() . "</p>";
}

echo "<h3>5. Test Cart Data Structure:</h3>";
echo "<p>This simulates what would be sent to the order placement script:</p>";

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $products = array();
    $total = 0;
    
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        // Get product price
        $priceQuery = "SELECT OfferPrice FROM product_price WHERE ProductId = ?";
        $stmt = $mysqli->prepare($priceQuery);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($priceRow = $result->fetch_assoc()) {
            $price = $priceRow['OfferPrice'];
            $subtotal = $price * $quantity;
            $total += $subtotal;
            
            // Get product code
            $codeQuery = "SELECT ProductCode FROM product_master WHERE ProductId = ?";
            $stmt = $mysqli->prepare($codeQuery);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $codeResult = $stmt->get_result();
            $codeRow = $codeResult->fetch_assoc();
            
            $products[] = array(
                'id' => $productId,
                'code' => $codeRow['ProductCode'] ?? '',
                'quantity' => $quantity,
                'offer_price' => $price,
                'subtotal' => $subtotal,
                'size' => '' // Default empty size
            );
        }
    }
    
    echo "<pre>";
    echo "Products array that would be sent:\n";
    print_r($products);
    echo "\nTotal amount: ₹$total\n";
    echo "</pre>";
} else {
    echo "<p>❌ No cart items to process</p>";
}

echo "<hr>";
echo "<p><a href='cart.php'>View Cart</a> | <a href='checkout.php'>Checkout</a> | <a href='index.php'>Home</a></p>";
?>

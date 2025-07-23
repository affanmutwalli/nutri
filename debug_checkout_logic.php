<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');

echo "<h2>🔍 Debug Checkout Logic</h2>";

// Initialize database connection (same as checkout.php)
$obj = new main();
$obj->connection();

echo "<h3>1. Session Analysis:</h3>";

// Check buy_now session
if (isset($_SESSION['buy_now']) && !empty($_SESSION['buy_now'])) {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h4>🚨 BUY NOW SESSION FOUND!</h4>";
    echo "<p>This will override cart checkout:</p>";
    echo "<pre>" . print_r($_SESSION['buy_now'], true) . "</pre>";
    echo "</div>";
    $isBuyNow = true;
} else {
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
    echo "<h4>✅ No Buy Now Session</h4>";
    echo "</div>";
    $isBuyNow = false;
}

// Check cart session
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
    echo "<h4>✅ Cart Session Found</h4>";
    echo "<p><strong>Cart Contents:</strong></p>";
    echo "<ul>";
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        echo "<li>Product ID: $productId, Quantity: $quantity</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h4>❌ No Cart Session</h4>";
    echo "</div>";
}

echo "<h3>2. Checkout Logic Simulation:</h3>";

if ($isBuyNow) {
    echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
    echo "<h4>⚠️ CHECKOUT WILL USE BUY NOW MODE</h4>";
    echo "<p>This explains why you see the wrong product!</p>";
    echo "</div>";
} elseif (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
    echo "<h4>✅ CHECKOUT WILL USE CART MODE</h4>";
    echo "<p>Let's see what products will be displayed:</p>";
    
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        echo "<hr><h5>Product ID: $productId</h5>";
        
        // Same query as checkout.php
        $FieldNames = array("ProductId", "ProductName", "ShortDescription", "Specification", "PhotoPath", "SubCategoryId", "MetaTags", "MetaKeywords", "ProductCode", "CategoryId");
        $ParamArray = array($productId);
        $Fields = implode(",", $FieldNames);
        
        $product_data = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
            $FieldNames,
            "i",
            $ParamArray
        );
        
        if ($product_data && count($product_data) > 0) {
            echo "<p><strong>✅ Product Found:</strong></p>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($product_data[0]['ProductName']) . "</p>";
            echo "<p><strong>Code:</strong> " . htmlspecialchars($product_data[0]['ProductCode']) . "</p>";
            
            // Get price data
            $FieldNamesPrice = array("OfferPrice", "MRP", "Size");
            $ParamArrayPrice = array($productId);
            $FieldsPrice = implode(",", $FieldNamesPrice);
            $product_prices = $obj->MysqliSelect1(
                "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                $FieldNamesPrice, 
                "i", 
                $ParamArrayPrice
            );
            
            if ($product_prices && count($product_prices) > 0) {
                echo "<p><strong>✅ Price Found:</strong></p>";
                echo "<p><strong>Offer Price:</strong> ₹" . $product_prices[0]['OfferPrice'] . "</p>";
                echo "<p><strong>MRP:</strong> ₹" . $product_prices[0]['MRP'] . "</p>";
                echo "<p><strong>Size:</strong> " . htmlspecialchars($product_prices[0]['Size']) . "</p>";
            } else {
                echo "<p><strong>❌ No Price Data Found</strong></p>";
            }
        } else {
            echo "<p><strong>❌ Product Not Found in Database!</strong></p>";
        }
    }
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h4>❌ NO CHECKOUT DATA</h4>";
    echo "<p>Neither Buy Now nor Cart data available.</p>";
    echo "</div>";
}

echo "<h3>3. Search for Thyro Balance Product:</h3>";

// Search for the problematic product
$search_query = "SELECT ProductId, ProductName, ProductCode FROM product_master WHERE ProductName LIKE '%Thyro%' OR ProductName LIKE '%Balance%'";
$thyro_products = $obj->MysqliSelect($search_query, array("ProductId", "ProductName", "ProductCode"));

if ($thyro_products && count($thyro_products) > 0) {
    echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
    echo "<h4>🔍 Found Thyro Balance Products:</h4>";
    foreach ($thyro_products as $product) {
        echo "<p><strong>ID:</strong> " . $product['ProductId'] . " | <strong>Name:</strong> " . htmlspecialchars($product['ProductName']) . " | <strong>Code:</strong> " . htmlspecialchars($product['ProductCode']) . "</p>";
    }
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
    echo "<h4>✅ No Thyro Balance Products Found</h4>";
    echo "</div>";
}

echo "<p><a href='checkout.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>💳 Go to Checkout</a></p>";
echo "<p><a href='cart.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🛒 Go to Cart</a></p>";
?>

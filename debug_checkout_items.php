<?php
// Debug Checkout Items - Find why phantom products appear
session_start();

// Include database configuration and connection
include_once 'cms/includes/psl-config.php';
include_once 'cms/database/dbconnection.php';

// Initialize database object
$obj = new main();

echo "<h2>🔍 Debug Checkout Items</h2>\n";
echo "<p>Analyzing what products are being displayed on the checkout page...</p>\n";

try {
    // Check current session cart
    echo "<h3>Step 1: Current Session Cart</h3>\n";
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Product ID</th><th>Quantity</th><th>Product Details</th></tr>\n";
        
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            // Get product details
            $FieldNames = array("ProductId", "ProductName", "ProductCode", "PhotoPath", "ShortDescription");
            $ParamArray = array($productId);
            $Fields = implode(",", $FieldNames);
            
            $product_data = $obj->MysqliSelect1(
                "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
                $FieldNames,
                "i",
                $ParamArray
            );
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($productId) . "</td>";
            echo "<td>" . htmlspecialchars($quantity) . "</td>";
            
            if ($product_data && isset($product_data[0])) {
                $product = $product_data[0];
                echo "<td>";
                echo "<strong>Name:</strong> " . htmlspecialchars($product['ProductName']) . "<br>";
                echo "<strong>Code:</strong> " . htmlspecialchars($product['ProductCode']) . "<br>";
                echo "<strong>Description:</strong> " . htmlspecialchars($product['ShortDescription']);
                echo "</td>";
            } else {
                echo "<td style='color: red;'>❌ Product not found in database!</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: orange;'>⚠️ No items in session cart</p>\n";
    }
    
    // Check if user is logged in and has database cart
    echo "<h3>Step 2: Database Cart (if logged in)</h3>\n";
    
    if (isset($_SESSION['CustomerId'])) {
        $customerId = $_SESSION['CustomerId'];
        echo "<p>Customer ID: " . htmlspecialchars($customerId) . "</p>\n";
        
        $cartQuery = "SELECT ProductId, Quantity, Price FROM cart WHERE CustomerId = ?";
        $cartParams = array($customerId);
        $cartData = $obj->MysqliSelect1($cartQuery, array("ProductId", "Quantity", "Price"), "i", $cartParams);
        
        if ($cartData && !empty($cartData)) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>Product ID</th><th>Quantity</th><th>Price</th><th>Product Details</th></tr>\n";
            
            foreach ($cartData as $cartItem) {
                // Get product details
                $FieldNames = array("ProductId", "ProductName", "ProductCode", "PhotoPath");
                $ParamArray = array($cartItem['ProductId']);
                $Fields = implode(",", $FieldNames);
                
                $product_data = $obj->MysqliSelect1(
                    "SELECT " . $Fields . " FROM product_master WHERE ProductId = ?",
                    $FieldNames,
                    "i",
                    $ParamArray
                );
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($cartItem['ProductId']) . "</td>";
                echo "<td>" . htmlspecialchars($cartItem['Quantity']) . "</td>";
                echo "<td>₹" . number_format($cartItem['Price'], 2) . "</td>";
                
                if ($product_data && isset($product_data[0])) {
                    $product = $product_data[0];
                    echo "<td>";
                    echo "<strong>Name:</strong> " . htmlspecialchars($product['ProductName']) . "<br>";
                    echo "<strong>Code:</strong> " . htmlspecialchars($product['ProductCode']);
                    echo "</td>";
                } else {
                    echo "<td style='color: red;'>❌ Product not found in database!</td>";
                }
                echo "</tr>\n";
            }
            echo "</table>\n";
        } else {
            echo "<p style='color: green;'>✓ No items in database cart</p>\n";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ User not logged in - no database cart to check</p>\n";
    }
    
    // Check for any automatic product additions or recommendations
    echo "<h3>Step 3: Check for Automatic Product Additions</h3>\n";
    
    // Check if there are any products being automatically added based on categories or recommendations
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cartProductIds = array_keys($_SESSION['cart']);
        $productIdsStr = implode(',', $cartProductIds);
        
        // Check categories of products in cart
        $categoryQuery = "
            SELECT DISTINCT CategoryId, SubCategoryId 
            FROM product_master 
            WHERE ProductId IN ($productIdsStr)
        ";
        
        $mysqli = $obj->connection();
        $categoryResult = mysqli_query($mysqli, $categoryQuery);
        
        if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
            echo "<h4>Categories of Products in Cart:</h4>\n";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>Category ID</th><th>SubCategory ID</th></tr>\n";
            
            while ($category = mysqli_fetch_assoc($categoryResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($category['CategoryId']) . "</td>";
                echo "<td>" . htmlspecialchars($category['SubCategoryId']) . "</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
            
            // Check for related products that might be auto-added
            echo "<h4>Potential Related Products (that might be auto-added):</h4>\n";
            
            $relatedQuery = "
                SELECT ProductId, ProductName, ProductCode, CategoryId, SubCategoryId
                FROM product_master 
                WHERE (CategoryId IN (SELECT DISTINCT CategoryId FROM product_master WHERE ProductId IN ($productIdsStr))
                       OR SubCategoryId IN (SELECT DISTINCT SubCategoryId FROM product_master WHERE ProductId IN ($productIdsStr)))
                AND ProductId NOT IN ($productIdsStr)
                AND IsActive = 'Y'
                ORDER BY ProductId
                LIMIT 10
            ";
            
            $relatedResult = mysqli_query($mysqli, $relatedQuery);
            
            if ($relatedResult && mysqli_num_rows($relatedResult) > 0) {
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
                echo "<tr><th>Product ID</th><th>Product Name</th><th>Product Code</th><th>Category ID</th><th>SubCategory ID</th></tr>\n";
                
                while ($related = mysqli_fetch_assoc($relatedResult)) {
                    $isPhantom = (strpos($related['ProductCode'], 'AC') !== false || 
                                 strpos($related['ProductCode'], 'SC') !== false);
                    $bgColor = $isPhantom ? 'background-color: #ffeeee;' : '';
                    
                    echo "<tr style='$bgColor'>";
                    echo "<td>" . htmlspecialchars($related['ProductId']) . "</td>";
                    echo "<td>" . htmlspecialchars($related['ProductName']) . "</td>";
                    echo "<td>" . htmlspecialchars($related['ProductCode']) . "</td>";
                    echo "<td>" . htmlspecialchars($related['CategoryId']) . "</td>";
                    echo "<td>" . htmlspecialchars($related['SubCategoryId']) . "</td>";
                    echo "</tr>\n";
                }
                echo "</table>\n";
            } else {
                echo "<p style='color: green;'>✓ No related products found</p>\n";
            }
        }
        
        mysqli_close($mysqli);
    }
    
    // Check for any combo or bundle logic
    echo "<h3>Step 4: Combo/Bundle Logic Check</h3>\n";
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            // Check if this product is a combo
            $comboQuery = "SELECT IsCombo FROM product_master WHERE ProductId = ?";
            $comboParams = array($productId);
            $comboData = $obj->MysqliSelect1($comboQuery, array("IsCombo"), "i", $comboParams);
            
            if ($comboData && isset($comboData[0]) && $comboData[0]['IsCombo'] == 'Y') {
                echo "<p style='color: orange;'>⚠️ Product ID $productId is marked as a COMBO product</p>\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h3>🎯 Debug Summary</h3>\n";
echo "<div style='background-color: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<p><strong>Key Points to Check:</strong></p>\n";
echo "<ul>\n";
echo "<li>Session cart contents vs what appears on checkout page</li>\n";
echo "<li>Database cart sync issues</li>\n";
echo "<li>Automatic product recommendations or bundles</li>\n";
echo "<li>Hidden checkout items on the page</li>\n";
echo "</ul>\n";
echo "<p><strong>Next Step:</strong> Check the actual checkout page HTML to see if phantom products are being rendered.</p>\n";
echo "</div>\n";
?>

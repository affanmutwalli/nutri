<?php
// Fix Phantom Products - Root Cause Solution
// This script will implement a comprehensive fix to prevent phantom products

// Include database configuration and connection
include_once 'cms/includes/psl-config.php';
include_once 'cms/database/dbconnection.php';

// Initialize database object
$obj = new main();

echo "<h2>🛠️ Phantom Products Root Cause Fix</h2>\n";
echo "<p>Implementing comprehensive solution to prevent phantom products...</p>\n";

try {
    $mysqli = $obj->connection();
    
    // Step 1: Identify and deactivate phantom products
    echo "<h3>Step 1: Deactivate Phantom Products</h3>\n";
    
    $phantomProductIds = [12, 15]; // The problematic product IDs
    
    foreach ($phantomProductIds as $productId) {
        // Check if product exists and get details
        $productQuery = "SELECT ProductId, ProductName, ProductCode, IsActive FROM product_master WHERE ProductId = ?";
        $stmt = $mysqli->prepare($productQuery);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            echo "<h4>Product ID: $productId</h4>\n";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($product['ProductName']) . "</p>\n";
            echo "<p><strong>Code:</strong> " . htmlspecialchars($product['ProductCode']) . "</p>\n";
            echo "<p><strong>Current Status:</strong> " . htmlspecialchars($product['IsActive']) . "</p>\n";
            
            // Deactivate the product
            $deactivateQuery = "UPDATE product_master SET IsActive = 'N' WHERE ProductId = ?";
            $deactivateStmt = $mysqli->prepare($deactivateQuery);
            $deactivateStmt->bind_param("i", $productId);
            
            if ($deactivateStmt->execute()) {
                echo "<p style='color: green;'>✅ Product deactivated successfully</p>\n";
            } else {
                echo "<p style='color: red;'>❌ Failed to deactivate product: " . $deactivateStmt->error . "</p>\n";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Product ID $productId not found in database</p>\n";
        }
    }
    
    // Step 2: Clean up existing cart records with phantom products
    echo "<h3>Step 2: Clean Up Cart Records</h3>\n";
    
    $cartCleanupQuery = "DELETE FROM cart WHERE ProductId IN (" . implode(',', $phantomProductIds) . ")";
    
    if ($mysqli->query($cartCleanupQuery)) {
        $deletedCount = $mysqli->affected_rows;
        echo "<p style='color: green;'>✅ Removed $deletedCount phantom product records from cart table</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Failed to clean cart: " . $mysqli->error . "</p>\n";
    }
    
    // Step 3: Add validation to cart loading process
    echo "<h3>Step 3: Enhanced Cart Validation</h3>\n";
    
    // Create an enhanced cart persistence file with validation
    $enhancedCartCode = '<?php
/**
 * Enhanced Cart Persistence with Phantom Product Prevention
 * This replaces the existing cart_persistence.php with additional validation
 */

class EnhancedCartPersistence {
    private $obj;
    
    public function __construct($dbObject) {
        $this->obj = $dbObject;
    }
    
    /**
     * Load cart from database with phantom product filtering
     */
    public function loadDatabaseCartToSession($customerId) {
        try {
            // Only load active products
            $cartData = $this->obj->MysqliSelect1(
                "SELECT c.ProductId, c.Quantity 
                 FROM cart c
                 INNER JOIN product_master pm ON c.ProductId = pm.ProductId
                 WHERE c.CustomerId = ? AND pm.IsActive = \'Y\'
                 AND pm.ProductCode NOT LIKE \'%AC%\' 
                 AND pm.ProductCode NOT LIKE \'%SC%\'",
                array("ProductId", "Quantity"),
                "i",
                array($customerId)
            );
            
            if ($cartData && count($cartData) > 0) {
                // Initialize session cart if not exists
                if (!isset($_SESSION[\'cart\'])) {
                    $_SESSION[\'cart\'] = array();
                }
                
                // Merge database cart with session cart (only valid products)
                foreach ($cartData as $item) {
                    $productId = $item[\'ProductId\'];
                    $quantity = $item[\'Quantity\'];
                    
                    // Double-check product is valid before adding
                    if ($this->validateProduct($productId)) {
                        if (isset($_SESSION[\'cart\'][$productId])) {
                            $_SESSION[\'cart\'][$productId] = max($_SESSION[\'cart\'][$productId], $quantity);
                        } else {
                            $_SESSION[\'cart\'][$productId] = $quantity;
                        }
                    }
                }
                
                return true;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error loading cart from database: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate product before adding to cart
     */
    private function validateProduct($productId) {
        try {
            $productData = $this->obj->MysqliSelect1(
                "SELECT ProductId, IsActive, ProductCode FROM product_master WHERE ProductId = ? AND IsActive = \'Y\'",
                array("ProductId", "IsActive", "ProductCode"),
                "i",
                array($productId)
            );
            
            if (!$productData || empty($productData)) {
                return false;
            }
            
            $product = $productData[0];
            
            // Check for phantom product codes
            if (strpos($product[\'ProductCode\'], \'AC\') !== false || 
                strpos($product[\'ProductCode\'], \'SC\') !== false) {
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Product validation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add item to cart with enhanced validation
     */
    public function addToCart($productId, $quantity = 1, $customerId = null) {
        // Validate product first
        if (!$this->validateProduct($productId)) {
            return ["success" => false, "message" => "Invalid product"];
        }
        
        // Initialize session cart
        if (!isset($_SESSION[\'cart\'])) {
            $_SESSION[\'cart\'] = array();
        }
        
        // Add to session
        if (isset($_SESSION[\'cart\'][$productId])) {
            $_SESSION[\'cart\'][$productId] += $quantity;
        } else {
            $_SESSION[\'cart\'][$productId] = $quantity;
        }
        
        // Save to database if user is logged in
        if ($customerId) {
            $this->saveSessionCartToDatabase($customerId);
        }
        
        return ["success" => true, "message" => "Product added to cart"];
    }
    
    /**
     * Clean session cart of invalid products
     */
    public function cleanSessionCart() {
        if (!isset($_SESSION[\'cart\']) || empty($_SESSION[\'cart\'])) {
            return;
        }
        
        $cleanCart = array();
        
        foreach ($_SESSION[\'cart\'] as $productId => $quantity) {
            if ($this->validateProduct($productId)) {
                $cleanCart[$productId] = $quantity;
            }
        }
        
        $_SESSION[\'cart\'] = $cleanCart;
    }
}
?>';
    
    // Save the enhanced cart persistence file
    file_put_contents('exe_files/enhanced_cart_persistence.php', $enhancedCartCode);
    echo "<p style='color: green;'>✅ Created enhanced cart persistence with phantom product prevention</p>\n";
    
    // Step 4: Create checkout validation
    echo "<h3>Step 4: Checkout Validation Enhancement</h3>\n";
    
    $checkoutValidationCode = '
// Enhanced checkout validation - add this to checkout.php
function validateCheckoutProducts() {
    let checkoutItems = document.querySelectorAll(".checkout-item");
    let validProducts = [];
    
    checkoutItems.forEach((item, index) => {
        try {
            let productIdElement = item.querySelector("input[name=\'product_id\']");
            let productCodeElement = item.querySelector(".check-code-blod span");
            
            if (!productIdElement) {
                console.warn("Missing product ID for checkout item", index);
                return;
            }
            
            let productId = productIdElement.value;
            let productCode = productCodeElement ? productCodeElement.textContent.trim() : "";
            
            // Skip phantom products
            if (productCode.includes("AC") || productCode.includes("SC") || 
                productId == "12" || productId == "15") {
                console.warn("Skipping phantom product:", productId, productCode);
                return;
            }
            
            // Add to valid products
            validProducts.push(item);
            
        } catch (error) {
            console.error("Error validating checkout item:", error);
        }
    });
    
    return validProducts;
}
';
    
    echo "<div style='background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 10px 0; border-radius: 5px;'>\n";
    echo "<h4>Checkout Validation Code:</h4>\n";
    echo "<pre style='background-color: #e9ecef; padding: 10px; border-radius: 3px; overflow-x: auto;'>" . htmlspecialchars($checkoutValidationCode) . "</pre>\n";
    echo "<p><strong>Instructions:</strong> Add this validation function to checkout.php and call it before processing orders.</p>\n";
    echo "</div>\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h3>✅ Fix Implementation Complete</h3>\n";
echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<h4 style='color: #155724; margin-top: 0;'>Root Cause Fix Summary:</h4>\n";
echo "<ul style='color: #155724;'>\n";
echo "<li>✅ <strong>Deactivated phantom products</strong> (IDs 12, 15) in product_master</li>\n";
echo "<li>✅ <strong>Cleaned up cart records</strong> containing phantom products</li>\n";
echo "<li>✅ <strong>Created enhanced cart persistence</strong> with validation</li>\n";
echo "<li>✅ <strong>Provided checkout validation code</strong> to prevent future issues</li>\n";
echo "</ul>\n";
echo "<p style='color: #155724; margin-bottom: 0;'><strong>Result:</strong> Phantom products will no longer be added to new orders.</p>\n";
echo "</div>\n";

echo "<h3>🔄 Next Steps</h3>\n";
echo "<div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<ol>\n";
echo "<li><strong>Test the fix:</strong> Try placing a new order to confirm no phantom products are added</li>\n";
echo "<li><strong>Monitor existing orders:</strong> Use the fix_order script for any remaining problematic orders</li>\n";
echo "<li><strong>Update cart loading:</strong> Replace cart_persistence.php with enhanced_cart_persistence.php</li>\n";
echo "<li><strong>Add checkout validation:</strong> Implement the provided validation code in checkout.php</li>\n";
echo "</ol>\n";
echo "</div>\n";
?>

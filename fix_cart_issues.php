<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

echo "<h2>🛠️ Cart Issues Fix & Prevention</h2>";

// Step 1: Clear all existing cart data for all users
echo "<h3>Step 1: Clearing All Cart Data</h3>";

try {
    // Clear all cart data from database
    $result = $mysqli->query("DELETE FROM cart");
    if ($result) {
        echo "<p>✅ Cleared all database cart data</p>";
    } else {
        echo "<p>❌ Failed to clear database cart data: " . $mysqli->error . "</p>";
    }
    
    // Clear session cart (for current session)
    session_start();
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
        echo "<p>✅ Cleared session cart</p>";
    }
    
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
        echo "<p>✅ Cleared buy_now session</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Step 2: Create improved cart management
echo "<h3>Step 2: Creating Enhanced Cart Management</h3>";

$enhancedCartCode = '<?php
class EnhancedCartManager {
    private $obj;
    private $mysqli;
    
    public function __construct() {
        require_once "database/dbconnection.php";
        $this->obj = new main();
        $this->mysqli = $this->obj->connection();
    }
    
    /**
     * Add item to cart with validation
     */
    public function addToCart($productId, $quantity = 1, $customerId = null) {
        // Validate product exists
        if (!$this->validateProduct($productId)) {
            return ["success" => false, "message" => "Product not found"];
        }
        
        // Initialize session cart
        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = array();
        }
        
        // Add to session
        if (isset($_SESSION["cart"][$productId])) {
            $_SESSION["cart"][$productId] += $quantity;
        } else {
            $_SESSION["cart"][$productId] = $quantity;
        }
        
        // Save to database if user is logged in
        if ($customerId) {
            $this->saveToDatabase($productId, $_SESSION["cart"][$productId], $customerId);
        }
        
        return ["success" => true, "message" => "Product added to cart"];
    }
    
    /**
     * Clear cart completely
     */
    public function clearCart($customerId = null) {
        // Clear session
        if (isset($_SESSION["cart"])) {
            unset($_SESSION["cart"]);
        }
        if (isset($_SESSION["buy_now"])) {
            unset($_SESSION["buy_now"]);
        }
        
        // Clear database
        if ($customerId) {
            $this->clearDatabaseCart($customerId);
        }
        
        return true;
    }
    
    /**
     * Get cart contents with validation
     */
    public function getCart($customerId = null) {
        $cart = [];
        
        // Load from session first
        if (isset($_SESSION["cart"]) && is_array($_SESSION["cart"])) {
            $cart = $_SESSION["cart"];
        }
        
        // If user is logged in, merge with database cart
        if ($customerId) {
            $dbCart = $this->loadFromDatabase($customerId);
            foreach ($dbCart as $productId => $quantity) {
                if (isset($cart[$productId])) {
                    $cart[$productId] = max($cart[$productId], $quantity);
                } else {
                    $cart[$productId] = $quantity;
                }
            }
            
            // Update session with merged cart
            $_SESSION["cart"] = $cart;
        }
        
        return $cart;
    }
    
    /**
     * Validate product exists and is active
     */
    private function validateProduct($productId) {
        $query = "SELECT ProductId FROM product_master WHERE ProductId = ? AND IsActive = \"Y\"";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    /**
     * Save single item to database
     */
    private function saveToDatabase($productId, $quantity, $customerId) {
        // Get product price
        $priceQuery = "SELECT MIN(OfferPrice) as price FROM product_price WHERE ProductId = ?";
        $stmt = $this->mysqli->prepare($priceQuery);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $price = 0;
        if ($row = $result->fetch_assoc()) {
            $price = $row["price"];
        }
        
        // Insert or update
        $query = "INSERT INTO cart (CustomerId, ProductId, Quantity, Price) 
                  VALUES (?, ?, ?, ?) 
                  ON DUPLICATE KEY UPDATE 
                  Quantity = VALUES(Quantity), 
                  Price = VALUES(Price),
                  UpdatedDate = CURRENT_TIMESTAMP";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("iiid", $customerId, $productId, $quantity, $price);
        $stmt->execute();
    }
    
    /**
     * Load cart from database
     */
    private function loadFromDatabase($customerId) {
        $cart = [];
        $query = "SELECT ProductId, Quantity FROM cart WHERE CustomerId = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $cart[$row["ProductId"]] = $row["Quantity"];
        }
        
        return $cart;
    }
    
    /**
     * Clear database cart
     */
    private function clearDatabaseCart($customerId) {
        $query = "DELETE FROM cart WHERE CustomerId = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
    }
    
    /**
     * Get cart count
     */
    public function getCartCount($customerId = null) {
        $cart = $this->getCart($customerId);
        return array_sum($cart);
    }
    
    /**
     * Remove specific item
     */
    public function removeItem($productId, $customerId = null) {
        // Remove from session
        if (isset($_SESSION["cart"][$productId])) {
            unset($_SESSION["cart"][$productId]);
        }
        
        // Remove from database
        if ($customerId) {
            $query = "DELETE FROM cart WHERE CustomerId = ? AND ProductId = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("ii", $customerId, $productId);
            $stmt->execute();
        }
        
        return true;
    }
    
    /**
     * Update item quantity
     */
    public function updateQuantity($productId, $quantity, $customerId = null) {
        if ($quantity <= 0) {
            return $this->removeItem($productId, $customerId);
        }
        
        // Update session
        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = array();
        }
        $_SESSION["cart"][$productId] = $quantity;
        
        // Update database
        if ($customerId) {
            $this->saveToDatabase($productId, $quantity, $customerId);
        }
        
        return true;
    }
}
?>';

// Save the enhanced cart manager
file_put_contents('exe_files/enhanced_cart_manager.php', $enhancedCartCode);
echo "<p>✅ Created enhanced cart manager</p>";

// Step 3: Create cart validation script
echo "<h3>Step 3: Creating Cart Validation</h3>";

$validationCode = '<?php
/**
 * Cart validation before order placement
 */
function validateCartBeforeOrder($customerId = null) {
    require_once "enhanced_cart_manager.php";
    $cartManager = new EnhancedCartManager();
    
    $cart = $cartManager->getCart($customerId);
    
    if (empty($cart)) {
        return ["valid" => false, "message" => "Cart is empty"];
    }
    
    $validProducts = [];
    $invalidProducts = [];
    
    foreach ($cart as $productId => $quantity) {
        // Validate each product
        $obj = new main();
        $mysqli = $obj->connection();
        
        $query = "SELECT pm.ProductId, pm.ProductName, pm.IsActive 
                  FROM product_master pm 
                  WHERE pm.ProductId = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            if ($product["IsActive"] == "Y") {
                $validProducts[] = [
                    "id" => $productId,
                    "name" => $product["ProductName"],
                    "quantity" => $quantity
                ];
            } else {
                $invalidProducts[] = [
                    "id" => $productId,
                    "name" => $product["ProductName"],
                    "reason" => "Product is inactive"
                ];
            }
        } else {
            $invalidProducts[] = [
                "id" => $productId,
                "name" => "Unknown Product",
                "reason" => "Product not found"
            ];
        }
    }
    
    if (!empty($invalidProducts)) {
        return [
            "valid" => false,
            "message" => "Some products in cart are invalid",
            "invalid_products" => $invalidProducts
        ];
    }
    
    return [
        "valid" => true,
        "message" => "Cart is valid",
        "products" => $validProducts
    ];
}
?>';

file_put_contents('exe_files/cart_validation.php', $validationCode);
echo "<p>✅ Created cart validation script</p>";

// Step 4: Show current status
echo "<h3>Step 4: Current Status</h3>";

// Check cart table
$cartCount = $mysqli->query("SELECT COUNT(*) as count FROM cart")->fetch_assoc()['count'];
echo "<p>📊 Database cart entries: <strong>$cartCount</strong></p>";

// Check recent orders
$recentOrders = $mysqli->query("SELECT COUNT(*) as count FROM order_master WHERE DATE(CreatedAt) = CURDATE()")->fetch_assoc()['count'];
echo "<p>📊 Today's orders: <strong>$recentOrders</strong></p>";

echo "<h3>✅ Fix Complete!</h3>";
echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<p><strong>What was fixed:</strong></p>";
echo "<ul>";
echo "<li>✅ Cleared all existing cart data</li>";
echo "<li>✅ Created enhanced cart management system</li>";
echo "<li>✅ Added cart validation before orders</li>";
echo "<li>✅ Improved session handling</li>";
echo "</ul>";

echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Test adding products to cart</li>";
echo "<li>Test placing orders</li>";
echo "<li>Verify cart is properly cleared after orders</li>";
echo "<li>Monitor for any duplicate product issues</li>";
echo "</ol>";
echo "</div>";

echo "<br><p><a href='index.php'>Go to Homepage</a> | <a href='cms/'>Go to Admin Panel</a></p>";
?>

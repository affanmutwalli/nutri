<?php
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
     * Validate product exists
     */
    private function validateProduct($productId) {
        $query = "SELECT ProductId FROM product_master WHERE ProductId = ?";
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
?>
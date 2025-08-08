<?php
// Enhanced Cart Persistence - Prevents Phantom Products
// This version ensures no old cart data causes phantom products

class EnhancedCartPersistence {
    private $obj;
    
    public function __construct() {
        // Determine the correct path to database connection
        $dbPath = "";
        if (file_exists(__DIR__ . "/../database/dbconnection.php")) {
            $dbPath = __DIR__ . "/../database/dbconnection.php";
        } elseif (file_exists("database/dbconnection.php")) {
            $dbPath = "database/dbconnection.php";
        } elseif (file_exists("../database/dbconnection.php")) {
            $dbPath = "../database/dbconnection.php";
        } else {
            $dbPath = $_SERVER["DOCUMENT_ROOT"] . "/nutrify/database/dbconnection.php";
        }
        
        if (file_exists($dbPath)) {
            include_once $dbPath;
            $this->obj = new main();
            $this->obj->connection();
        } else {
            throw new Exception("Database connection file not found");
        }
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
     * Load cart from database ONLY if session cart is empty
     * This prevents phantom products from old sessions
     */
    public function loadCartFromDatabase($customerId) {
        try {
            // Initialize session cart if not exists
            if (!isset($_SESSION["cart"])) {
                $_SESSION["cart"] = array();
            }
            
            // CRITICAL: Only load database cart if session cart is completely empty
            if (empty($_SESSION["cart"])) {
                $cartData = $this->obj->MysqliSelect1(
                    "SELECT ProductId, Quantity FROM cart WHERE CustomerId = ?",
                    array("ProductId", "Quantity"),
                    "i",
                    array($customerId)
                );
                
                if ($cartData && count($cartData) > 0) {
                    foreach ($cartData as $item) {
                        $productId = $item["ProductId"];
                        $quantity = $item["Quantity"];
                        
                        // Validate product still exists before adding
                        if ($this->validateProduct($productId)) {
                            $_SESSION["cart"][$productId] = $quantity;
                        }
                    }
                }
            } else {
                // If session cart has items, clear the database cart to prevent conflicts
                // This ensures fresh session cart takes precedence over old database data
                $this->clearDatabaseCart($customerId);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error loading cart from database: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Save session cart to database
     */
    public function saveSessionCartToDatabase($customerId) {
        if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
            return true; // Nothing to save
        }
        
        try {
            foreach ($_SESSION["cart"] as $productId => $quantity) {
                // Validate product exists before saving
                if (!$this->validateProduct($productId)) {
                    continue; // Skip invalid products
                }
                
                // Get product price
                $priceData = $this->obj->MysqliSelect1(
                    "SELECT MIN(OfferPrice) as price FROM product_price WHERE ProductId = ?",
                    array("price"),
                    "i",
                    array($productId)
                );
                
                $price = $priceData && isset($priceData[0]["price"]) ? $priceData[0]["price"] : 0;
                
                // Insert or update cart item
                $result = $this->obj->fInsertNew(
                    "INSERT INTO cart (CustomerId, ProductId, Quantity, Price) 
                     VALUES (?, ?, ?, ?) 
                     ON DUPLICATE KEY UPDATE 
                     Quantity = VALUES(Quantity), 
                     Price = VALUES(Price),
                     UpdatedDate = CURRENT_TIMESTAMP",
                    "iiid",
                    array($customerId, $productId, $quantity, $price)
                );
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error saving cart to database: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clear cart from database for a user
     */
    public function clearDatabaseCart($customerId) {
        try {
            $result = $this->obj->fInsertNew(
                "DELETE FROM cart WHERE CustomerId = ?",
                "i",
                array($customerId)
            );
            
            return $result !== false;
        } catch (Exception $e) {
            error_log("Error clearing database cart: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clear all cart data (session and database)
     */
    public function clearAllCart($customerId = null) {
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
     * Validate that a product exists and is active
     */
    private function validateProduct($productId) {
        try {
            $productData = $this->obj->MysqliSelect1(
                "SELECT ProductId FROM product_master WHERE ProductId = ? AND IsActive = 1",
                array("ProductId"),
                "i",
                array($productId)
            );
            
            return $productData && count($productData) > 0;
        } catch (Exception $e) {
            error_log("Error validating product: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove item from cart
     */
    public function removeFromCart($productId, $customerId = null) {
        // Remove from session
        if (isset($_SESSION["cart"][$productId])) {
            unset($_SESSION["cart"][$productId]);
        }
        
        // Remove from database
        if ($customerId) {
            try {
                $this->obj->fInsertNew(
                    "DELETE FROM cart WHERE CustomerId = ? AND ProductId = ?",
                    "ii",
                    array($customerId, $productId)
                );
            } catch (Exception $e) {
                error_log("Error removing from database cart: " . $e->getMessage());
            }
        }
        
        return true;
    }
}
?>
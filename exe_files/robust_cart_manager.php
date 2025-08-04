<?php
// ROBUST CART MANAGER - NO PHANTOM PRODUCTS ALLOWED!
// This is the ONLY cart manager that should be used

class RobustCartManager {
    private $obj;
    private $logFile;
    
    public function __construct() {
        $this->logFile = __DIR__ . "/cart_debug.log";
        
        // Database connection
        if (file_exists(__DIR__ . "/../database/dbconnection.php")) {
            include_once __DIR__ . "/../database/dbconnection.php";
        } elseif (file_exists("database/dbconnection.php")) {
            include_once "database/dbconnection.php";
        } else {
            throw new Exception("Database connection not found");
        }
        
        $this->obj = new main();
        $this->obj->connection();
    }
    
    /**
     * Log cart operations for debugging
     */
    private function log($message) {
        $timestamp = date("Y-m-d H:i:s");
        $logEntry = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Validate that a product exists and is legitimate
     */
    private function validateProduct($productId) {
        try {
            $productData = $this->obj->MysqliSelect1(
                "SELECT ProductId, ProductName, IsActive FROM product_master WHERE ProductId = ?",
                array("ProductId", "ProductName", "IsActive"),
                "i",
                array($productId)
            );
            
            if (!$productData || count($productData) == 0) {
                $this->log("VALIDATION FAILED: ProductId $productId does not exist");
                return false;
            }
            
            if ($productData[0]["IsActive"] != 1) {
                $this->log("VALIDATION FAILED: ProductId $productId is not active");
                return false;
            }
            
            $this->log("VALIDATION PASSED: ProductId $productId - " . $productData[0]["ProductName"]);
            return true;
        } catch (Exception $e) {
            $this->log("VALIDATION ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add item to cart with strict validation
     */
    public function addToCart($productId, $quantity = 1, $customerId = null) {
        $this->log("ADD_TO_CART: ProductId=$productId, Quantity=$quantity, CustomerId=$customerId");
        
        // STRICT VALIDATION - NO PHANTOM PRODUCTS ALLOWED!
        if (!$this->validateProduct($productId)) {
            $this->log("BLOCKED: Invalid product $productId rejected");
            return ["success" => false, "message" => "Invalid product"];
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
        
        $this->log("SUCCESS: Added ProductId $productId to session cart");
        
        // Save to database if user is logged in
        if ($customerId) {
            $this->saveToDatabase($productId, $_SESSION["cart"][$productId], $customerId);
        }
        
        return ["success" => true, "message" => "Product added to cart"];
    }
    
    /**
     * Load cart from database - SAFE VERSION
     */
    public function loadCartFromDatabase($customerId) {
        $this->log("LOAD_CART: Loading for CustomerId=$customerId");
        
        try {
            // Initialize session cart
            if (!isset($_SESSION["cart"])) {
                $_SESSION["cart"] = array();
            }
            
            // CRITICAL: Only load if session cart is COMPLETELY empty
            if (!empty($_SESSION["cart"])) {
                $this->log("SKIP_LOAD: Session cart not empty, clearing database cart to prevent conflicts");
                $this->clearDatabaseCart($customerId);
                return true;
            }
            
            // Load from database
            $cartData = $this->obj->MysqliSelect1(
                "SELECT ProductId, Quantity FROM cart WHERE CustomerId = ?",
                array("ProductId", "Quantity"),
                "i",
                array($customerId)
            );
            
            if ($cartData && count($cartData) > 0) {
                $validItems = 0;
                $invalidItems = 0;
                
                foreach ($cartData as $item) {
                    $productId = $item["ProductId"];
                    $quantity = $item["Quantity"];
                    
                    // VALIDATE EACH ITEM - NO PHANTOM PRODUCTS!
                    if ($this->validateProduct($productId)) {
                        $_SESSION["cart"][$productId] = $quantity;
                        $validItems++;
                        $this->log("LOADED: ProductId $productId (Qty: $quantity)");
                    } else {
                        $invalidItems++;
                        $this->log("REJECTED: Invalid ProductId $productId from database");
                        
                        // Remove invalid item from database
                        $this->obj->fInsertNew(
                            "DELETE FROM cart WHERE CustomerId = ? AND ProductId = ?",
                            "ii",
                            array($customerId, $productId)
                        );
                    }
                }
                
                $this->log("LOAD_COMPLETE: $validItems valid items loaded, $invalidItems invalid items removed");
            }
            
            return true;
        } catch (Exception $e) {
            $this->log("LOAD_ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Save to database with validation
     */
    private function saveToDatabase($productId, $quantity, $customerId) {
        if (!$this->validateProduct($productId)) {
            $this->log("SAVE_BLOCKED: Invalid ProductId $productId not saved to database");
            return false;
        }
        
        try {
            // Get product price
            $priceData = $this->obj->MysqliSelect1(
                "SELECT MIN(OfferPrice) as price FROM product_price WHERE ProductId = ?",
                array("price"),
                "i",
                array($productId)
            );
            
            $price = $priceData && isset($priceData[0]["price"]) ? $priceData[0]["price"] : 0;
            
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
            
            $this->log("SAVE_SUCCESS: ProductId $productId saved to database for CustomerId $customerId");
            return true;
        } catch (Exception $e) {
            $this->log("SAVE_ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clear database cart
     */
    public function clearDatabaseCart($customerId) {
        try {
            $result = $this->obj->fInsertNew(
                "DELETE FROM cart WHERE CustomerId = ?",
                "i",
                array($customerId)
            );
            
            $this->log("CLEAR_DB: Database cart cleared for CustomerId $customerId");
            return true;
        } catch (Exception $e) {
            $this->log("CLEAR_ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clear all cart data
     */
    public function clearAllCart($customerId = null) {
        // Clear session
        if (isset($_SESSION["cart"])) {
            unset($_SESSION["cart"]);
            $this->log("CLEAR_SESSION: Session cart cleared");
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
     * Get cart with validation
     */
    public function getCart() {
        if (!isset($_SESSION["cart"])) {
            return array();
        }
        
        $validCart = array();
        foreach ($_SESSION["cart"] as $productId => $quantity) {
            if ($this->validateProduct($productId)) {
                $validCart[$productId] = $quantity;
            } else {
                $this->log("REMOVED: Invalid ProductId $productId from session cart");
                unset($_SESSION["cart"][$productId]);
            }
        }
        
        return $validCart;
    }
}
?>
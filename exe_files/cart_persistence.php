<?php
// Cart persistence functions for managing cart data between session and database

// Determine the correct path to database connection
$dbPath = '';
if (file_exists(__DIR__ . '/../database/dbconnection.php')) {
    $dbPath = __DIR__ . '/../database/dbconnection.php';
} elseif (file_exists('database/dbconnection.php')) {
    $dbPath = 'database/dbconnection.php';
} elseif (file_exists('../database/dbconnection.php')) {
    $dbPath = '../database/dbconnection.php';
} else {
    // Try absolute path from document root
    $dbPath = $_SERVER['DOCUMENT_ROOT'] . '/nutrify/database/dbconnection.php';
}

// Only include what we need to avoid conflicts
if (file_exists($dbPath)) {
    include_once $dbPath;
} else {
    throw new Exception("Database connection file not found. Tried: " . $dbPath);
}

class CartPersistence {
    private $obj;
    
    public function __construct() {
        $this->obj = new main();
        $this->obj->connection();
    }
    
    /**
     * Save session cart to database for logged-in user
     */
    public function saveSessionCartToDatabase($customerId) {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return true; // Nothing to save
        }
        
        try {
            foreach ($_SESSION['cart'] as $productId => $quantity) {
                // Get product price for storage
                $priceData = $this->obj->MysqliSelect1(
                    "SELECT MIN(OfferPrice) as price FROM product_price WHERE ProductId = ?",
                    array("price"),
                    "i",
                    array($productId)
                );
                
                $price = $priceData && isset($priceData[0]['price']) ? $priceData[0]['price'] : 0;
                
                // Insert or update cart item in database
                $result = $this->obj->fInsertNew(
                    "INSERT INTO cart (CustomerId, ProductId, Quantity, Price) 
                     VALUES (?, ?, ?, ?) 
                     ON DUPLICATE KEY UPDATE 
                     Quantity = Quantity + VALUES(Quantity), 
                     Price = VALUES(Price),
                     UpdatedDate = CURRENT_TIMESTAMP",
                    "iiid",
                    array($customerId, $productId, $quantity, $price)
                );
                
                if (!$result) {
                    error_log("Failed to save cart item: CustomerId=$customerId, ProductId=$productId");
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error saving cart to database: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Load cart from database to session for logged-in user
     */
    public function loadCartFromDatabase($customerId) {
        try {
            $cartData = $this->obj->MysqliSelect1(
                "SELECT ProductId, Quantity FROM cart WHERE CustomerId = ?",
                array("ProductId", "Quantity"),
                "i",
                array($customerId)
            );
            
            if ($cartData && count($cartData) > 0) {
                // Initialize session cart if not exists
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = array();
                }
                
                // Only merge database cart if session cart is empty
                // This prevents phantom products from old sessions
                if (empty($_SESSION['cart'])) {
                    foreach ($cartData as $item) {
                        $productId = $item['ProductId'];
                        $quantity = $item['Quantity'];
                        $_SESSION['cart'][$productId] = $quantity;
                    }
                } else {
                    // If session cart has items, clear the database cart to prevent conflicts
                    // This ensures fresh session cart takes precedence
                    $this->clearDatabaseCart($customerId);
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
     * Sync session cart with database (save session to DB and load any missing items from DB)
     */
    public function syncCart($customerId) {
        // First load any existing cart from database
        $this->loadCartFromDatabase($customerId);
        
        // Then save current session cart to database
        $this->saveSessionCartToDatabase($customerId);
        
        return true;
    }
    
    /**
     * Get cart count from session
     */
    public function getCartCount() {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            return 0;
        }
        
        $totalCount = 0;
        foreach ($_SESSION['cart'] as $quantity) {
            $totalCount += $quantity;
        }
        
        return $totalCount;
    }
    
    /**
     * Add item to cart (both session and database if user is logged in)
     */
    public function addToCart($productId, $quantity = 1, $customerId = null) {
        // Add to session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        // If user is logged in, also save to database
        if ($customerId) {
            $this->saveSessionCartToDatabase($customerId);
        }
        
        return true;
    }
    
    /**
     * Remove item from cart (both session and database if user is logged in)
     */
    public function removeFromCart($productId, $customerId = null) {
        // Remove from session cart
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        
        // If user is logged in, also remove from database
        if ($customerId) {
            try {
                $this->obj->fInsertNew(
                    "DELETE FROM cart WHERE CustomerId = ? AND ProductId = ?",
                    "ii",
                    array($customerId, $productId)
                );
            } catch (Exception $e) {
                error_log("Error removing item from database cart: " . $e->getMessage());
            }
        }
        
        return true;
    }
    
    /**
     * Update cart item quantity (both session and database if user is logged in)
     */
    public function updateCartQuantity($productId, $quantity, $customerId = null) {
        // Update session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
        
        // If user is logged in, also update database
        if ($customerId) {
            if ($quantity > 0) {
                // Get product price
                $priceData = $this->obj->MysqliSelect1(
                    "SELECT MIN(OfferPrice) as price FROM product_price WHERE ProductId = ?",
                    array("price"),
                    "i",
                    array($productId)
                );
                
                $price = $priceData && isset($priceData[0]['price']) ? $priceData[0]['price'] : 0;
                
                $this->obj->fInsertNew(
                    "INSERT INTO cart (CustomerId, ProductId, Quantity, Price) 
                     VALUES (?, ?, ?, ?) 
                     ON DUPLICATE KEY UPDATE 
                     Quantity = VALUES(Quantity), 
                     Price = VALUES(Price),
                     UpdatedDate = CURRENT_TIMESTAMP",
                    "iiid",
                    array($customerId, $productId, $quantity, $price)
                );
            } else {
                $this->removeFromCart($productId, $customerId);
            }
        }
        
        return true;
    }
}
?>

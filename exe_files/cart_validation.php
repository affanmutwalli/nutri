<?php
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
?>
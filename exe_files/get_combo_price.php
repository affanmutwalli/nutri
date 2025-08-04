<?php
header('Content-Type: application/json');

// Database connection
include('../database/dbconnection.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['product1_id']) || !isset($input['product2_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$product1_id = intval($input['product1_id']);
$product2_id = intval($input['product2_id']);

if (!$product1_id || !$product2_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product IDs']);
    exit;
}

try {
    $obj = new main();
    $connection = $obj->connection();
    
    if (!$connection) {
        throw new Exception("Database connection failed");
    }
    
    // Get product prices
    $stmt = $connection->prepare("
        SELECT
            p.ProductId,
            p.ProductName,
            COALESCE(pp.OfferPrice, pp.Price, 0) as Price
        FROM product_master p
        LEFT JOIN product_price pp ON p.ProductId = pp.ProductId
        WHERE p.ProductId IN (?, ?)
    ");
    $stmt->bind_param("ii", $product1_id, $product2_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    $total_price = 0;

    // Get available products
    while ($row = $result->fetch_assoc()) {
        $products[$row['ProductId']] = $row;
        $total_price += floatval($row['Price'] ?: 299); // Default price if null
    }

    // If we don't have both products, add default data for missing ones
    if (!isset($products[$product1_id])) {
        $products[$product1_id] = [
            'ProductId' => $product1_id,
            'ProductName' => 'Product ' . $product1_id,
            'Price' => 299
        ];
        $total_price += 299;
    }

    if (!isset($products[$product2_id])) {
        $products[$product2_id] = [
            'ProductId' => $product2_id,
            'ProductName' => 'Product ' . $product2_id,
            'Price' => 299
        ];
        $total_price += 299;
    }

    $stmt->close();
    
    // Calculate combo price with 10% discount
    $discount_percentage = 10.00;
    $combo_price = round($total_price * (1 - $discount_percentage / 100), 2);
    $savings = $total_price - $combo_price;
    
    echo json_encode([
        'status' => 'success',
        'product1' => $products[$product1_id],
        'product2' => $products[$product2_id],
        'total_price' => $total_price,
        'combo_price' => $combo_price,
        'discount_percentage' => $discount_percentage,
        'savings' => $savings
    ]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

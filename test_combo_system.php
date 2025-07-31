<?php
include('database/dbconnection.php');

header('Content-Type: application/json');

$obj = new main();
$connection = $obj->connection();

try {
    $response = [
        'success' => true,
        'tests' => []
    ];
    
    // Test 1: Check if tables exist
    $tables = ['dynamic_combos', 'combo_analytics'];
    foreach ($tables as $table) {
        $result = $connection->query("SHOW TABLES LIKE '$table'");
        $exists = $result && $result->num_rows > 0;
        $response['tests'][] = [
            'test' => "Table $table exists",
            'result' => $exists,
            'status' => $exists ? 'PASS' : 'FAIL'
        ];
    }
    
    // Test 2: Check if view exists
    $result = $connection->query("SHOW TABLES LIKE 'combo_details_view'");
    $viewExists = $result && $result->num_rows > 0;
    $response['tests'][] = [
        'test' => 'View combo_details_view exists',
        'result' => $viewExists,
        'status' => $viewExists ? 'PASS' : 'FAIL'
    ];
    
    // Test 3: Check if we can get some products
    $stmt = $connection->prepare("SELECT ProductId, ProductName FROM product_master LIMIT 2");
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    
    $response['tests'][] = [
        'test' => 'Can fetch products',
        'result' => count($products) >= 2,
        'status' => count($products) >= 2 ? 'PASS' : 'FAIL',
        'data' => $products
    ];
    
    // Test 4: Check if we can get product prices
    if (count($products) >= 2) {
        $productId = $products[0]['ProductId'];
        $stmt = $connection->prepare("SELECT MIN(OfferPrice) as min_price FROM product_price WHERE ProductId = ? AND OfferPrice > 0");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $priceData = $result->fetch_assoc();
        $stmt->close();
        
        $response['tests'][] = [
            'test' => 'Can fetch product prices',
            'result' => $priceData && $priceData['min_price'] > 0,
            'status' => ($priceData && $priceData['min_price'] > 0) ? 'PASS' : 'FAIL',
            'data' => $priceData
        ];
    }
    
    // Test 5: Try to create a test combo
    if (count($products) >= 2) {
        $product1_id = $products[0]['ProductId'];
        $product2_id = $products[1]['ProductId'];
        $combo_id = "TEST_COMBO_{$product1_id}_{$product2_id}";
        
        // First delete any existing test combo
        $stmt = $connection->prepare("DELETE FROM dynamic_combos WHERE combo_id = ?");
        $stmt->bind_param("s", $combo_id);
        $stmt->execute();
        $stmt->close();
        
        // Try to create test combo
        $combo_name = "Test Combo";
        $combo_description = "Test combo description";
        $total_price = 100.00;
        $discount_percentage = 10.00;
        $combo_price = 90.00;
        $savings = 10.00;
        
        $stmt = $connection->prepare("INSERT INTO dynamic_combos (combo_id, product1_id, product2_id, combo_name, combo_description, total_price, discount_percentage, combo_price, savings) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siissdddd", $combo_id, $product1_id, $product2_id, $combo_name, $combo_description, $total_price, $discount_percentage, $combo_price, $savings);
        $insertResult = $stmt->execute();
        $insertError = $stmt->error;
        $stmt->close();
        
        $response['tests'][] = [
            'test' => 'Can create test combo',
            'result' => $insertResult,
            'status' => $insertResult ? 'PASS' : 'FAIL',
            'error' => $insertError,
            'combo_id' => $combo_id
        ];
        
        // Test 6: Try to fetch the created combo
        if ($insertResult) {
            $stmt = $connection->prepare("SELECT * FROM combo_details_view WHERE combo_id = ?");
            $stmt->bind_param("s", $combo_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $comboData = $result->fetch_assoc();
            $stmt->close();
            
            $response['tests'][] = [
                'test' => 'Can fetch created combo from view',
                'result' => $comboData !== null,
                'status' => $comboData !== null ? 'PASS' : 'FAIL',
                'data' => $comboData
            ];
            
            // Clean up test combo
            $stmt = $connection->prepare("DELETE FROM dynamic_combos WHERE combo_id = ?");
            $stmt->bind_param("s", $combo_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
?>

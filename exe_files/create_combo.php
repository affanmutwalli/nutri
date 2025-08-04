<?php
session_start();
header('Content-Type: application/json');

// Database connection
include('../database/dbconnection.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action']) || $input['action'] !== 'create_combo') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$product1_id = isset($input['product1_id']) ? intval($input['product1_id']) : 0;
$product2_id = isset($input['product2_id']) ? intval($input['product2_id']) : 0;
$product1_name = isset($input['product1_name']) ? trim($input['product1_name']) : '';
$product2_name = isset($input['product2_name']) ? trim($input['product2_name']) : '';

if (!$product1_id || !$product2_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product IDs']);
    exit;
}

if ($product1_id === $product2_id) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot create combo with the same product']);
    exit;
}

try {
    $obj = new main();
    $connection = $obj->connection();

    if (!$connection) {
        throw new Exception("Database connection failed");
    }

    // Check if products exist and get their details
    $stmt = $connection->prepare("SELECT p.ProductId, p.ProductName, COALESCE(pp.OfferPrice, 299) as Price FROM product_master p LEFT JOIN product_price pp ON p.ProductId = pp.ProductId WHERE p.ProductId IN (?, ?)");
    $stmt->bind_param("ii", $product1_id, $product2_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Products not found']);
        exit;
    }

    // If we don't get both products, still proceed with available ones
    if ($result->num_rows < 2) {
        // Get the missing product info from product_master only
        $found_ids = [];
        $temp_products = [];
        while ($row = $result->fetch_assoc()) {
            $temp_products[$row['ProductId']] = $row;
            $found_ids[] = $row['ProductId'];
        }

        // Find missing product
        $missing_ids = array_diff([$product1_id, $product2_id], $found_ids);
        foreach ($missing_ids as $missing_id) {
            $missing_stmt = $connection->prepare("SELECT ProductId, ProductName FROM product_master WHERE ProductId = ?");
            $missing_stmt->bind_param("i", $missing_id);
            $missing_stmt->execute();
            $missing_result = $missing_stmt->get_result();
            if ($missing_result->num_rows > 0) {
                $missing_row = $missing_result->fetch_assoc();
                $missing_row['Price'] = 299; // Default price
                $temp_products[$missing_id] = $missing_row;
            }
            $missing_stmt->close();
        }

        if (count($temp_products) < 2) {
            echo json_encode(['status' => 'error', 'message' => 'One or both products not found in database']);
            exit;
        }

        $products = $temp_products;
    } else {
        // Normal case - both products found
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[$row['ProductId']] = $row;
        }
    }
    $stmt->close();

    // Create combo ID
    $combo_id = 'COMBO_' . $product1_id . '_' . $product2_id;

    // Check if combo already exists
    $stmt = $connection->prepare("SELECT combo_id FROM dynamic_combos WHERE combo_id = ? OR combo_id = ? LIMIT 1");
    $reverse_combo_id = 'COMBO_' . $product2_id . '_' . $product1_id;
    $stmt->bind_param("ss", $combo_id, $reverse_combo_id);
    $stmt->execute();
    $existing = $stmt->get_result();

    if ($existing->num_rows > 0) {
        $combo = $existing->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'message' => 'Combo already exists',
            'combo_id' => $combo['combo_id'],
            'redirect' => 'combo_product.php?ComboId=' . $combo['combo_id']
        ]);
        exit;
    }
    $stmt->close();

    // Calculate combo price (10% discount from total)
    $total_price = ($products[$product1_id]['Price'] ?? 0) + ($products[$product2_id]['Price'] ?? 0);
    $discount_percentage = 10.00;
    $combo_price = round($total_price * (1 - $discount_percentage / 100), 2);
    $savings = $total_price - $combo_price;

    // Create combo name and description
    $combo_name = $products[$product1_id]['ProductName'] . ' + ' . $products[$product2_id]['ProductName'];
    $combo_description = 'Special combo offer: Get both ' . $products[$product1_id]['ProductName'] . ' and ' . $products[$product2_id]['ProductName'] . ' together at a discounted price!';

    // Insert new combo
    $stmt = $connection->prepare("INSERT INTO dynamic_combos (combo_id, product1_id, product2_id, combo_name, combo_description, total_price, discount_percentage, combo_price, savings, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("siissdddd", $combo_id, $product1_id, $product2_id, $combo_name, $combo_description, $total_price, $discount_percentage, $combo_price, $savings);

    if ($stmt->execute()) {
        $stmt->close();

        echo json_encode([
            'status' => 'success',
            'message' => 'Combo created successfully',
            'combo_id' => $combo_id,
            'combo_name' => $combo_name,
            'combo_price' => $combo_price,
            'original_price' => $total_price,
            'discount' => $savings,
            'redirect' => 'combo_product.php?ComboId=' . $combo_id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create combo: ' . $connection->error]);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

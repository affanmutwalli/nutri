<?php
session_start();

// Start output buffering to catch any unwanted output
ob_start();

include('../database/dbconnection.php');

// Clean any output that might have been generated
ob_clean();

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Disable error display to prevent HTML in JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$obj = new main();
$connection = $obj->connection();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }

    $product1_id = isset($input['product1_id']) ? (int)$input['product1_id'] : 0;
    $product2_id = isset($input['product2_id']) ? (int)$input['product2_id'] : 0;
    $action = isset($input['action']) ? $input['action'] : '';

    if ($action === 'create_combo') {
        // Validate inputs
        if ($product1_id <= 0 || $product2_id <= 0) {
            throw new Exception('Invalid product IDs provided');
        }

        if ($product1_id === $product2_id) {
            throw new Exception('Cannot create combo with the same product');
        }

        // Ensure consistent ordering (smaller ID first)
        if ($product1_id > $product2_id) {
            $temp = $product1_id;
            $product1_id = $product2_id;
            $product2_id = $temp;
        }

        $combo_id = "COMBO_{$product1_id}_{$product2_id}";

        // Check if combo already exists
        $stmt = $connection->prepare("SELECT combo_id FROM dynamic_combos WHERE combo_id = ?");
        $stmt->bind_param("s", $combo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingCombo = $result->fetch_assoc();
        $stmt->close();

        if ($existingCombo) {
            // Combo exists, return it
            $stmt = $connection->prepare("SELECT * FROM combo_details_view WHERE combo_id = ?");
            $stmt->bind_param("s", $combo_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $comboDetails = $result->fetch_assoc();
            $stmt->close();

            echo json_encode([
                'success' => true,
                'message' => 'Combo already exists',
                'combo_id' => $combo_id,
                'combo' => $comboDetails,
                'redirect_url' => "combo_product.php?combo_id=" . urlencode($combo_id)
            ]);
            exit;
        }

        // Verify products exist
        $stmt = $connection->prepare("SELECT ProductId, ProductName FROM product_master WHERE ProductId = ?");
        $stmt->bind_param("i", $product1_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product1 = $result->fetch_assoc();
        $stmt->close();

        $stmt = $connection->prepare("SELECT ProductId, ProductName FROM product_master WHERE ProductId = ?");
        $stmt->bind_param("i", $product2_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product2 = $result->fetch_assoc();
        $stmt->close();

        if (!$product1 || !$product2) {
            throw new Exception('One or both products not found');
        }

        // Get product prices first
        $stmt = $connection->prepare("SELECT MIN(OfferPrice) as min_price FROM product_price WHERE ProductId = ? AND OfferPrice > 0");
        $stmt->bind_param("i", $product1_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $price1_data = $result->fetch_assoc();
        $product1_price = $price1_data['min_price'] ?? 0;
        $stmt->close();

        $stmt = $connection->prepare("SELECT MIN(OfferPrice) as min_price FROM product_price WHERE ProductId = ? AND OfferPrice > 0");
        $stmt->bind_param("i", $product2_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $price2_data = $result->fetch_assoc();
        $product2_price = $price2_data['min_price'] ?? 0;
        $stmt->close();

        // Calculate combo details
        $total_price = $product1_price + $product2_price;
        $discount_percentage = 10.00;
        $combo_price = $total_price * (1 - $discount_percentage / 100);
        $savings = $total_price - $combo_price;

        // Create shorter combo name to avoid database length issues
        $product1_short = strlen($product1['ProductName']) > 30 ? substr($product1['ProductName'], 0, 30) . '...' : $product1['ProductName'];
        $product2_short = strlen($product2['ProductName']) > 30 ? substr($product2['ProductName'], 0, 30) . '...' : $product2['ProductName'];
        $combo_name = $product1_short . ' + ' . $product2_short . ' Combo';

        $combo_description = 'Special combo offer: Get ' . $product1['ProductName'] . ' and ' . $product2['ProductName'] . ' together at a discounted price!';

        // Create new combo
        $stmt = $connection->prepare("INSERT INTO dynamic_combos (combo_id, product1_id, product2_id, combo_name, combo_description, total_price, discount_percentage, combo_price, savings) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siissdddd", $combo_id, $product1_id, $product2_id, $combo_name, $combo_description, $total_price, $discount_percentage, $combo_price, $savings);
        $insertResult = $stmt->execute();
        $stmt->close();

        if (!$insertResult) {
            throw new Exception('Failed to create combo: ' . $connection->error);
        }

        // Get the created combo details
        $stmt = $connection->prepare("SELECT * FROM combo_details_view WHERE combo_id = ?");
        $stmt->bind_param("s", $combo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comboDetails = $result->fetch_assoc();
        $stmt->close();

        // Track combo creation
        $session_id = session_id();
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $stmt = $connection->prepare("INSERT INTO combo_analytics (combo_id, action_type, user_session, ip_address, user_agent) VALUES (?, 'view', ?, ?, ?)");
        $stmt->bind_param("sssss", $combo_id, $action_type, $session_id, $ip_address, $user_agent);
        $action_type = 'view';
        $stmt->execute();
        $stmt->close();

        echo json_encode([
            'success' => true,
            'message' => 'Combo created successfully',
            'combo_id' => $combo_id,
            'combo' => $comboDetails,
            'redirect_url' => "combo_product.php?combo_id=" . urlencode($combo_id)
        ]);

    } elseif ($action === 'get_combo') {
        $combo_id = isset($input['combo_id']) ? $input['combo_id'] : '';

        if (empty($combo_id)) {
            throw new Exception('Combo ID required');
        }

        $stmt = $connection->prepare("SELECT * FROM combo_details_view WHERE combo_id = ?");
        $stmt->bind_param("s", $combo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comboDetails = $result->fetch_assoc();
        $stmt->close();

        if (!$comboDetails) {
            throw new Exception('Combo not found');
        }

        echo json_encode([
            'success' => true,
            'combo' => $comboDetails
        ]);

    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    error_log("Combo creation error: " . $e->getMessage());

    // Clean any output buffer
    if (ob_get_level()) {
        ob_clean();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug_info' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ]);
}

// End output buffering
if (ob_get_level()) {
    ob_end_flush();
}
?>

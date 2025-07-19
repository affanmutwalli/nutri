<?php
session_start();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['coupon_code']) && isset($data['discount'])) {
            $_SESSION['applied_coupon'] = [
                'coupon_code' => $data['coupon_code'],
                'discount' => floatval($data['discount']),
                'coupon_id' => $data['coupon_id'] ?? null,
                'applied_at' => time()
            ];
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Coupon stored in session'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid coupon data'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred'
    ]);
}
?>

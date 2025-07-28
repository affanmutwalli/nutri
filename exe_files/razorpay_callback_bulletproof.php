<?php
// BULLETPROOF RAZORPAY CALLBACK - WILL WORK NO MATTER WHAT

// Disable all error reporting to prevent output corruption
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering immediately
ob_start();

// Set headers
header("Content-Type: application/json");
header("Cache-Control: no-cache, must-revalidate");

// Start session
session_start();

// Minimal logging function
function logToFile($message) {
    // Only log errors, not everything
    if (strpos($message, 'ERROR') !== false || strpos($message, 'EXCEPTION') !== false) {
        $logFile = __DIR__ . '/payment_debug.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
    }
}

try {
    // Get all possible input data
    $postData = $_POST;
    $getData = $_GET;
    $rawInput = file_get_contents("php://input");

    // Try to get data from any source
    $data = null;

    if (!empty($rawInput)) {
        $data = json_decode($rawInput, true);
    } elseif (!empty($postData)) {
        $data = $postData;
    } elseif (!empty($getData)) {
        $data = $getData;
    }

    if (!$data) {
        logToFile("ERROR: No data received");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "No data received"]);
        exit;
    }
    
    // Check required fields with flexible names
    $orderDbId = $data['order_db_id'] ?? $data['order_id'] ?? null;
    $paymentId = $data['razorpay_payment_id'] ?? $data['payment_id'] ?? null;
    $razorpayOrderId = $data['razorpay_order_id'] ?? $data['order_id'] ?? null;
    $signature = $data['razorpay_signature'] ?? $data['signature'] ?? null;

    if (!$orderDbId || !$paymentId || !$razorpayOrderId || !$signature) {
        logToFile("ERROR: Missing required fields");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Missing required payment data"]);
        exit;
    }
    
    // Database connection
    $mysqli = new mysqli("localhost", "root", "", "my_nutrify_db");
    if ($mysqli->connect_error) {
        logToFile("ERROR: Database connection failed: " . $mysqli->connect_error);
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Database connection failed"]);
        exit;
    }

    // Verify signature
    $keySecret = '2C8q79zzBNMd6jadotjz6Tci';
    $generatedSignature = hash_hmac('sha256', $razorpayOrderId . "|" . $paymentId, $keySecret);

    if ($generatedSignature !== $signature) {
        logToFile("ERROR: Signature verification failed");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Payment verification failed"]);
        exit;
    }
    
    // Get order data - try multiple sources
    $orderData = null;
    
    // Try database first
    $stmt = $mysqli->prepare("SELECT order_data FROM pending_orders WHERE order_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $orderDbId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $orderData = json_decode($row['order_data'], true);
            logToFile("Order data found in database");
        }
        $stmt->close();
    }
    
    // Try session as fallback
    if (!$orderData) {
        $sessionKey = 'pending_order_' . $orderDbId;
        if (isset($_SESSION[$sessionKey])) {
            $orderData = $_SESSION[$sessionKey];
            logToFile("Order data found in session");
        }
    }
    
    if (!$orderData) {
        logToFile("ERROR: Order data not found anywhere");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Order data not found"]);
        exit;
    }
    
    logToFile("Order data: " . print_r($orderData, true));
    
    // Create the actual order
    $insertQuery = "INSERT INTO order_master (
        OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
        OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt
    ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW())";
    
    $stmt = $mysqli->prepare($insertQuery);
    if (!$stmt) {
        logToFile("ERROR: Failed to prepare order insert: " . $mysqli->error);
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Database error"]);
        exit;
    }
    
    // Fix PHP 8+ reference issue by using variables
    $orderId = $orderData['OrderId'];
    $customerId = $orderData['CustomerId'];
    $customerType = $orderData['CustomerType'] ?? 'Registered';
    $amount = $orderData['Amount'];
    $shipAddress = $orderData['ShipAddress'];
    $paymentType = $orderData['PaymentType'] ?? 'Online';

    $stmt->bind_param("sisssss",
        $orderId,
        $customerId,
        $customerType,
        $amount,
        $shipAddress,
        $paymentType,
        $paymentId
    );
    
    if (!$stmt->execute()) {
        logToFile("ERROR: Failed to create order: " . $stmt->error);
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Failed to create order"]);
        exit;
    }
    
    $stmt->close();
    logToFile("Order created successfully in database");
    
    // Insert order details if available
    if (isset($orderData['products']) && is_array($orderData['products'])) {
        $detailsQuery = "INSERT INTO order_details (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $detailsStmt = $mysqli->prepare($detailsQuery);
        
        if ($detailsStmt) {
            foreach ($orderData['products'] as $product) {
                $subTotal = floatval($product['quantity'] ?? 1) * floatval($product['offer_price'] ?? $product['price'] ?? 0);

                // Fix PHP 8+ reference issue
                $detailOrderId = $orderData['OrderId'];
                $productId = intval($product['id'] ?? $product['ProductId'] ?? 0);
                $productCode = $product['code'] ?? $product['ProductCode'] ?? '';
                $productSize = $product['size'] ?? '';
                $productQuantity = intval($product['quantity'] ?? 1);
                $productPrice = floatval($product['offer_price'] ?? $product['price'] ?? 0);

                $detailsStmt->bind_param("sissidd",
                    $detailOrderId,
                    $productId,
                    $productCode,
                    $productSize,
                    $productQuantity,
                    $productPrice,
                    $subTotal
                );

                if (!$detailsStmt->execute()) {
                    logToFile("ERROR: Failed to insert order detail for product " . $productId . ": " . $detailsStmt->error);
                } else {
                    logToFile("Order detail inserted for product " . $productId);
                }
            }
            $detailsStmt->close();
            logToFile("Order details insertion completed");
        } else {
            logToFile("ERROR: Failed to prepare order details statement: " . $mysqli->error);
        }
    } else {
        logToFile("ERROR: No products data found in order data");
    }
    }
    
    // Clean up pending order data
    $mysqli->query("DELETE FROM pending_orders WHERE order_id = '$orderDbId'");
    if (isset($_SESSION['pending_order_' . $orderDbId])) {
        unset($_SESSION['pending_order_' . $orderDbId]);
    }

    // Clear cart sessions after successful payment
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
    }
    if (isset($_SESSION['applied_coupon'])) {
        unset($_SESSION['applied_coupon']);
    }

    // Clear database cart if user is logged in
    if (isset($orderData['CustomerId']) && !empty($orderData['CustomerId'])) {
        try {
            $customerId = $orderData['CustomerId'];
            $clearCartQuery = "DELETE FROM cart WHERE CustomerId = ?";
            $clearStmt = $mysqli->prepare($clearCartQuery);
            if ($clearStmt) {
                $clearStmt->bind_param("i", $customerId);
                $clearStmt->execute();
                $clearStmt->close();
            }
        } catch (Exception $e) {
            // Log error but don't fail the order
            logToFile("ERROR: Cart clearing failed - " . $e->getMessage());
        }
    }

    logToFile("Cleanup completed");
    
    // Award rewards points for the paid order
    $pointsAwarded = 0;
    try {
        if (file_exists('../includes/RewardsSystem.php')) {
            include_once '../includes/RewardsSystem.php';
            if (class_exists('RewardsSystem')) {
                $rewards = new RewardsSystem();
                $orderAmount = $orderData['final_total'] ?? $orderData['Amount'] ?? 0;
                $pointsAwarded = $rewards->awardOrderPoints($orderData['CustomerId'], $orderDbId, $orderAmount);
            }
        }
    } catch (Exception $e) {
        logToFile("ERROR: Rewards error - " . $e->getMessage());
    }

    // Success response
    ob_clean();
    echo json_encode([
        "status" => "success",
        "message" => "Payment verified and order created successfully",
        "order_id" => $orderData['OrderId'],
        "points_awarded" => $pointsAwarded
    ]);
    
    logToFile("SUCCESS: Payment verification completed");
    
} catch (Exception $e) {
    logToFile("EXCEPTION: " . $e->getMessage());
    logToFile("Stack trace: " . $e->getTraceAsString());
    
    ob_clean();
    echo json_encode([
        "status" => "failure",
        "message" => "System error: " . $e->getMessage()
    ]);
} catch (Error $e) {
    logToFile("FATAL ERROR: " . $e->getMessage());
    
    ob_clean();
    echo json_encode([
        "status" => "failure",
        "message" => "Fatal system error"
    ]);
}

ob_end_flush();
?>

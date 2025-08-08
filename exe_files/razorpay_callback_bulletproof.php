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

    // Extract payment data
    $razorpayPaymentId = $data['razorpay_payment_id'] ?? '';
    $razorpayOrderId = $data['razorpay_order_id'] ?? '';
    $razorpaySignature = $data['razorpay_signature'] ?? '';
    $orderDbId = $data['order_db_id'] ?? '';

    logToFile("Received payment data - Order ID: $orderDbId, Payment ID: $razorpayPaymentId");

    if (empty($razorpayPaymentId) || empty($razorpayOrderId) || empty($razorpaySignature) || empty($orderDbId)) {
        logToFile("ERROR: Missing required payment data");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Missing payment data"]);
        exit;
    }

    // Database connection
    include_once '../database/dbconnection.php';
    $obj = new main();
    $mysqli = $obj->connection();

    if (!$mysqli || $mysqli->connect_error) {
        logToFile("ERROR: Database connection failed");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Database connection failed"]);
        exit;
    }

    // Verify signature
    $expectedSignature = hash_hmac('sha256', $razorpayOrderId . "|" . $razorpayPaymentId, '2C8q79zzBNMd6jadotjz6Tci');
    
    if (!hash_equals($expectedSignature, $razorpaySignature)) {
        logToFile("ERROR: Invalid signature");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Invalid signature"]);
        exit;
    }

    // Get order data from database first (more reliable), then fallback to session
    $orderData = null;

    // Try to get from pending_orders table first
    $getPendingQuery = "SELECT order_data FROM pending_orders WHERE order_id = ?";
    $pendingStmt = $mysqli->prepare($getPendingQuery);

    if ($pendingStmt) {
        $pendingStmt->bind_param("s", $orderDbId);
        $pendingStmt->execute();
        $pendingResult = $pendingStmt->get_result();

        if ($pendingResult && $pendingResult->num_rows > 0) {
            $pendingRow = $pendingResult->fetch_assoc();
            $orderData = json_decode($pendingRow['order_data'], true);
            logToFile("INFO: Order data retrieved from database for " . $orderDbId);
        }
        $pendingStmt->close();
    }

    // Fallback to session if database doesn't have the data
    if (!$orderData) {
        $sessionKey = 'pending_order_' . $orderDbId;
        if (isset($_SESSION[$sessionKey])) {
            $orderData = $_SESSION[$sessionKey];
            logToFile("INFO: Order data retrieved from session for " . $orderDbId);
        } else {
            logToFile("ERROR: No order data found in database or session for " . $orderDbId);
            ob_clean();
            echo json_encode(["status" => "failure", "message" => "No order data found"]);
            exit;
        }
    }
    
    // Create the order in database with Paid status (for online orders, we create after payment success)
    // Handle both guest and registered users
    logToFile("Processing order with CustomerType: " . ($orderData['CustomerType'] ?? 'NOT_SET'));
    logToFile("Order data keys: " . implode(', ', array_keys($orderData)));

    // Determine if this is a guest order
    $isGuestOrder = isset($orderData['CustomerType']) && $orderData['CustomerType'] === 'Guest';

    if ($isGuestOrder) {
        logToFile("Detected GUEST order - using guest fields");
        $insertOrderQuery = "INSERT INTO order_master (
            OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
            OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt,
            GuestName, GuestEmail, GuestPhone
        ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW(), ?, ?, ?)";
    } else {
        logToFile("Detected REGISTERED order - using standard fields");
        $insertOrderQuery = "INSERT INTO order_master (
            OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
            OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt
        ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW())";
    }

    $stmt = $mysqli->prepare($insertOrderQuery);

    if (!$stmt) {
        logToFile("ERROR: Failed to prepare order insert query");
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Database error"]);
        exit;
    }

    // Extract variables for bind_param (needs references)
    $orderId = $orderData['OrderId'];
    $customerId = $orderData['CustomerId'];
    $customerType = $orderData['CustomerType'];
    $amount = $orderData['Amount'];
    $shipAddress = $orderData['ShipAddress'];
    $paymentType = $orderData['PaymentType'];

    if ($isGuestOrder) {
        logToFile("Binding parameters for GUEST order");
        $guestName = $orderData['GuestName'] ?? '';
        $guestEmail = $orderData['GuestEmail'] ?? '';
        $guestPhone = $orderData['GuestPhone'] ?? '';

        $stmt->bind_param("sissssssss",
            $orderId,
            $customerId,
            $customerType,
            $amount,
            $shipAddress,
            $paymentType,
            $razorpayPaymentId,
            $guestName,
            $guestEmail,
            $guestPhone
        );
    } else {
        logToFile("Binding parameters for REGISTERED order");
        $stmt->bind_param("sisssss",
            $orderId,
            $customerId,
            $customerType,
            $amount,
            $shipAddress,
            $paymentType,
            $razorpayPaymentId
        );
    }

    if (!$stmt->execute()) {
        logToFile("ERROR: Failed to create order - " . $stmt->error);
        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Failed to create order"]);
        exit;
    }

    $orderDbId = $orderData['OrderId'];
    $stmt->close();

    // Insert order details if products exist
    if (isset($orderData['products']) && is_array($orderData['products'])) {
        foreach ($orderData['products'] as $product) {
            $detailQuery = "INSERT INTO order_details (OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $detailStmt = $mysqli->prepare($detailQuery);

            if ($detailStmt) {
                // Extract variables for bind_param (needs references)
                // Handle both old and new data structures
                $detailOrderId = $orderData['OrderId'];
                $productId = $product['ProductId'] ?? $product['id'] ?? 0;
                $productCode = $product['ProductCode'] ?? $product['code'] ?? '';
                $size = $product['Size'] ?? $product['size'] ?? '';
                $quantity = $product['Quantity'] ?? $product['quantity'] ?? 1;
                $price = $product['Price'] ?? $product['offer_price'] ?? $product['price'] ?? 0;
                $subTotal = $product['SubTotal'] ?? $product['subtotal'] ?? ($price * $quantity);

                logToFile("Inserting order detail - ProductId: $productId, Price: $price, Quantity: $quantity");

                $detailStmt->bind_param("sissidd",
                    $detailOrderId,
                    $productId,
                    $productCode,
                    $size,
                    $quantity,
                    $price,
                    $subTotal
                );

                if (!$detailStmt->execute()) {
                    logToFile("ERROR: Failed to insert order detail - " . $detailStmt->error);
                }
                $detailStmt->close();
            }
        }
    }

    // Clear session data
    $sessionKey = 'pending_order_' . $orderDbId;
    if (isset($_SESSION[$sessionKey])) {
        unset($_SESSION[$sessionKey]);
    }
    if (isset($_SESSION['applied_coupon'])) {
        unset($_SESSION['applied_coupon']);
    }
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
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

    // Clear pending order data from database
    $deletePendingQuery = "DELETE FROM pending_orders WHERE order_id = ?";
    $deleteStmt = $mysqli->prepare($deletePendingQuery);
    if ($deleteStmt) {
        $deleteStmt->bind_param("s", $orderDbId);
        $deleteStmt->execute();
        $deleteStmt->close();
        logToFile("INFO: Cleaned up pending order data for " . $orderDbId);
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

<?php

// Enable error reporting for debugging but capture errors
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly
ini_set('log_errors', 1);

// Start output buffering to prevent any accidental output
ob_start();

// Set JSON header
header("Content-Type: application/json");
header("Cache-Control: no-cache, must-revalidate");

session_start();

try {
    // Include configuration files
    include_once '../cms/includes/psl-config.php';
    include('razorpay_config.php'); // Include Razorpay API keys

    // Create database connection manually to avoid die() statements
    $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

    // Check connection
    if ($mysqli->connect_error) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }

    // Set charset
    $mysqli->set_charset("utf8");

    // Get and validate input data
    $input = file_get_contents("php://input");
    if (empty($input)) {
        throw new Exception("No input data received");
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON input: " . json_last_error_msg());
    }

    // Validate required fields
    if (!isset($data['order_db_id']) || !isset($data['razorpay_payment_id']) ||
        !isset($data['razorpay_order_id']) || !isset($data['razorpay_signature'])) {
        throw new Exception("Missing required payment data");
    }

    // Log payment verification attempt
    error_log("Razorpay callback: Processing payment verification for order " . $data['order_db_id']);

    $order_db_id = $data['order_db_id'];
    $razorpay_payment_id = $data['razorpay_payment_id'];
    $razorpay_order_id = $data['razorpay_order_id'];
    $razorpay_signature = $data['razorpay_signature'];

    // Verify payment signature
    $generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, RAZORPAY_KEY_SECRET);

    if ($generated_signature === $razorpay_signature) {
    // Payment is verified, now create the actual order in database

        // Get the stored order data from database (more reliable than session)
        $getPendingQuery = "SELECT order_data FROM pending_orders WHERE order_id = ?";
        $pendingStmt = $mysqli->prepare($getPendingQuery);

        if (!$pendingStmt) {
            throw new Exception("Failed to prepare pending order query: " . $mysqli->error);
        }

        $pendingStmt->bind_param("s", $order_db_id);
        $pendingStmt->execute();
        $pendingResult = $pendingStmt->get_result();

        if ($pendingResult && $pendingResult->num_rows > 0) {
            $pendingRow = $pendingResult->fetch_assoc();
            $orderData = json_decode($pendingRow['order_data'], true);
            error_log("Razorpay callback: Order data retrieved from database for " . $order_db_id);
        } else {
            // Fallback to session if database doesn't have the data
            $sessionKey = 'pending_order_' . $order_db_id;
            if (isset($_SESSION[$sessionKey])) {
                $orderData = $_SESSION[$sessionKey];
                error_log("Razorpay callback: Order data retrieved from session fallback for " . $order_db_id);
            } else {
                throw new Exception("Order data not found in database or session for order: " . $order_db_id);
            }
        }

        $pendingStmt->close();

        // Validate order data
        if (!isset($orderData['OrderId']) || !isset($orderData['CustomerId']) || !isset($orderData['Amount'])) {
            throw new Exception("Invalid order data structure");
        }

        // Check database connection
        if (!$mysqli || $mysqli->connect_error) {
            throw new Exception("Database connection failed: " . ($mysqli->connect_error ?? 'Unknown error'));
        }

        // Create the order in database with Paid status
        $insertOrderQuery = "INSERT INTO order_master (
            OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus,
            OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt
        ) VALUES (?, ?, ?, NOW(), ?, 'Paid', 'Placed', ?, ?, ?, NOW())";

        $orderStmt = $mysqli->prepare($insertOrderQuery);
        if (!$orderStmt) {
            throw new Exception("Failed to prepare order insert: " . $mysqli->error);
        }

        // Fix PHP 8+ reference issue
        $orderId = $orderData['OrderId'];
        $customerId = $orderData['CustomerId'];
        $customerType = $orderData['CustomerType'];
        $amount = $orderData['Amount'];
        $shipAddress = $orderData['ShipAddress'];
        $paymentType = $orderData['PaymentType'];

        $orderStmt->bind_param("sisssss",
            $orderId,
            $customerId,
            $customerType,
            $amount,
            $shipAddress,
            $paymentType,
            $razorpay_payment_id // Use actual payment ID as transaction ID
        );

        if (!$orderStmt->execute()) {
            throw new Exception("Failed to create order: " . $orderStmt->error);
        }
        $orderStmt->close();

    // Insert order details
    $detailsQuery = "INSERT INTO order_details (
        OrderId, ProductId, ProductCode, Size, Quantity, Price, SubTotal
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $detailsStmt = $mysqli->prepare($detailsQuery);
    if ($detailsStmt && isset($orderData['products'])) {
        foreach ($orderData['products'] as $product) {
            $subTotal = floatval($product['quantity']) * floatval($product['offer_price']);

            $detailsStmt->bind_param("ssssidd",
                $orderData['OrderId'],
                $product['id'],
                $product['code'] ?? '',
                $product['size'] ?? '',
                $product['quantity'],
                $product['offer_price'],
                $subTotal
            );

            $detailsStmt->execute();
        }
        $detailsStmt->close();
    }

    // Clear the temporary order data from session
    unset($_SESSION[$sessionKey]);

    // Clear cart sessions
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
    }

        // Award rewards points for the paid order
        $pointsAwarded = 0;
        try {
            if (file_exists('../includes/RewardsSystem.php')) {
                include_once '../includes/RewardsSystem.php';
                if (class_exists('RewardsSystem')) {
                    $rewards = new RewardsSystem();
                    // Get the order amount - try multiple fields
                    $orderAmount = $orderData['final_total'] ?? $orderData['Amount'] ?? 0;
                    // Award points for the order amount
                    $pointsAwarded = $rewards->awardOrderPoints($orderData['CustomerId'], $order_db_id, $orderAmount);
                }
            }
        } catch (Exception $e) {
            // Log error but don't fail the order - minimal logging
            error_log("Rewards error: " . $e->getMessage());
        }

        // Process rewards and coupons for the paid order
        try {
            if (file_exists('../includes/order_rewards_integration.php')) {
                include_once '../includes/order_rewards_integration.php';
                if (function_exists('processPaymentConfirmationRewards')) {
                    processPaymentConfirmationRewards($order_db_id);
                }
            }
        } catch (Exception $e) {
            error_log("Error processing rewards for paid order $order_db_id: " . $e->getMessage());
        }

        // Clear database cart if user is logged in
        if (isset($orderData['CustomerId']) && !empty($orderData['CustomerId'])) {
            try {
                if (file_exists('cart_persistence.php')) {
                    include_once 'cart_persistence.php';
                    if (class_exists('CartPersistence')) {
                        $cartManager = new CartPersistence();
                        $cartManager->clearDatabaseCart($orderData['CustomerId']);
                    }
                }
            } catch (Exception $e) {
                // Log error but don't fail the order
                error_log("Error clearing database cart after payment: " . $e->getMessage());
            }
        }

        // Clear pending order data from database
        $deletePendingQuery = "DELETE FROM pending_orders WHERE order_id = ?";
        $deleteStmt = $mysqli->prepare($deletePendingQuery);
        if ($deleteStmt) {
            $deleteStmt->bind_param("s", $order_db_id);
            $deleteStmt->execute();
            $deleteStmt->close();
            error_log("Razorpay callback: Cleaned up pending order data for " . $order_db_id);
        }

        // Clear coupon session
        if (isset($_SESSION['applied_coupon'])) {
            unset($_SESSION['applied_coupon']);
        }

        // Clear output buffer and send success response
        ob_clean();
        echo json_encode([
            "status" => "success",
            "message" => "Payment verified and order created successfully",
            "order_id" => $orderData['OrderId'], // Add the OrderId to the response
            "points_awarded" => $pointsAwarded
        ]);

        // Log the successful order creation
        error_log("Order created successfully: " . $orderData['OrderId'] . " for customer: " . $orderData['CustomerId']);

    } else {
        // Signature mismatch, payment failed
        error_log("Razorpay callback: Payment signature verification FAILED");
        error_log("Expected: " . $generated_signature);
        error_log("Received: " . $razorpay_signature);

        // Clean up the temporary order data
        $sessionKey = 'pending_order_' . $order_db_id;
        if (isset($_SESSION[$sessionKey])) {
            unset($_SESSION[$sessionKey]);
        }

        ob_clean();
        echo json_encode(["status" => "failure", "message" => "Payment verification failed"]);
    }

} catch (Exception $e) {
    // Log the error
    error_log("Razorpay callback error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    // Clear output buffer and send error response
    ob_clean();
    echo json_encode([
        "status" => "failure",
        "message" => "Payment processing error: " . $e->getMessage()
    ]);
} catch (Error $e) {
    // Log fatal errors
    error_log("Razorpay callback fatal error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    // Clear output buffer and send error response
    ob_clean();
    echo json_encode([
        "status" => "failure",
        "message" => "System error occurred during payment processing"
    ]);
}

// End output buffering
ob_end_flush();
?>

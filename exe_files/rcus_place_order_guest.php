<?php
// Set JSON response header
header("Content-Type: application/json");
session_start();
ob_start();

try {
    // Include database connection files (avoid conflicts)
    include_once '../database/dbconnection.php';

    // Create database connection
    $obj = new main();
    $conn = $obj->connection();

    if (!$conn) {
        throw new Exception("Database connection returned null");
    }
} catch (Exception $e) {
    // Clear any output buffer and send proper JSON error response
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Get JSON input data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit();
}

// Debug logging - log received data
error_log("=== GUEST ORDER DEBUG ===");
error_log("Raw input: " . $input);
error_log("Decoded data: " . print_r($data, true));

// Debug: Check each required field individually
$requiredFields = ['name', 'email', 'phone', 'address', 'final_total', 'products'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        $missingFields[] = $field;
        error_log("Missing field: $field = " . (isset($data[$field]) ? var_export($data[$field], true) : 'NOT SET'));
    } else {
        error_log("Field OK: $field = " . var_export($data[$field], true));
    }
}

// Validate required fields for guest checkout
if (!empty($missingFields)) {
    error_log("Missing required fields: " . implode(', ', $missingFields));
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Missing required fields: " . implode(', ', $missingFields)]);
    exit();
}

// Create proper shipping address
$shippingAddress = $data['address'] . ", " . ($data['landmark'] ?? '') . ", " . $data['city'] . ", " . $data['state'] . " - " . $data['pincode'];

// Get current date and time in IST
date_default_timezone_set("Asia/Kolkata");
$orderDate = date("Y-m-d");
$createdAt = date("Y-m-d H:i:s");

// Generate Sequential Order ID (Format: MN000000)
$orderPrefix = "MN";

// Use proper numeric sorting to get the highest order number
$lastOrderQuery = "SELECT COALESCE(MAX(CAST(SUBSTRING(OrderId, 3) AS UNSIGNED)), 0) as max_num
                   FROM order_master
                   WHERE OrderId LIKE 'MN%'";

// Execute query to get max order number
$result = $obj->connection()->query($lastOrderQuery);
if ($result && $row = $result->fetch_assoc()) {
    $lastOrderNumber = (int)$row['max_num'];
    $newOrderNumber = $lastOrderNumber + 1;
    $newOrderId = $orderPrefix . str_pad($newOrderNumber, 6, "0", STR_PAD_LEFT);
} else {
    $newOrderId = $orderPrefix . "000001";
}

// Set payment method and status
$paymentMethod = $data['paymentMethod'] ?? 'COD';
$paymentStatus = ($paymentMethod === 'COD') ? 'Due' : 'Pending';

// For guest orders, we'll use a special CustomerId of 0 or create a temporary guest record
$guestCustomerId = 0; // Special ID for guest orders

// Prepare order data for insertion
$ParamArray = array(
    $newOrderId, 
    $guestCustomerId, 
    $orderDate, 
    $data['final_total'], 
    $paymentStatus, 
    $shippingAddress, 
    $paymentMethod, 
    'NA', // No Razorpay transaction ID for COD
    $createdAt,
    $data['name'],
    $data['email'],
    $data['phone']
);

// Debug logging
error_log("=== GUEST ORDER INSERT DEBUG ===");
error_log("ParamArray count: " . count($ParamArray));
error_log("ParamArray: " . print_r($ParamArray, true));
error_log("Type string length: " . strlen("sisdssssssss"));

// Call fInsertNew to insert the order with guest information
try {
    $InputDocId = $obj->fInsertNew(
        "INSERT INTO order_master (OrderId, CustomerId, CustomerType, OrderDate, Amount, PaymentStatus, OrderStatus, ShipAddress, PaymentType, TransactionId, CreatedAt, GuestName, GuestEmail, GuestPhone)
         VALUES (?, ?, 'Guest', ?, ?, ?, 'Placed', ?, ?, ?, ?, ?, ?, ?)",
        "sisdssssssss", // Data types: s,i,s,d,s,s,s,s,s,s,s,s (12 parameters)
        $ParamArray
    );

    error_log("Order inserted successfully with ID: " . $InputDocId);

} catch (Exception $e) {
    error_log("Database insertion error: " . $e->getMessage());
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Error inserting order: " . $e->getMessage()]);
    exit();
}

if ($InputDocId) {
    // Get a persistent connection for the entire order processing
    $connection = $obj->connection();
    if (!$connection || !mysqli_ping($connection)) {
        echo json_encode(["response" => "E", "message" => "Database connection lost"]);
        exit();
    }

    // Insert order details into `order_details` table
    $processedProducts = array();

    // Log all products being received
    error_log("Guest Order $newOrderId - Received " . count($data['products']) . " products:");
    foreach ($data['products'] as $index => $product) {
        error_log("Product $index: ID=" . $product['id'] . ", Name=" . ($product['name'] ?? 'N/A') .
                 ", Price=" . $product['offer_price'] . ", Qty=" . $product['quantity']);
    }

    foreach ($data['products'] as $product) {
        try {
            // Validate product exists in database before processing
            $productValidationQuery = "SELECT ProductId, ProductName, ProductCode FROM product_master WHERE ProductId = ?";
            $validationStmt = $connection->prepare($productValidationQuery);
            $validationStmt->bind_param("i", $product['id']);
            $validationStmt->execute();
            $validationResult = $validationStmt->get_result();

            if ($validationResult->num_rows === 0) {
                error_log("PHANTOM PRODUCT DETECTED: ProductId=" . $product['id'] . " does not exist in product_master table. Skipping.");
                $validationStmt->close();
                continue; // Skip phantom products
            }
            $validationStmt->close();

            // Create a unique key for this product (ProductId + Size)
            $productKey = $product['id'] . '_' . ($product['size'] ?? '');

            // Skip if we've already processed this exact product
            if (in_array($productKey, $processedProducts)) {
                error_log("Skipping duplicate product: ProductId=" . $product['id'] . ", Size=" . ($product['size'] ?? ''));
                continue;
            }
            $processedProducts[] = $productKey;

            // Prepare the data for insertion into the order_details table
            $sub_total = $product["offer_price"] * $product["quantity"];

            // Convert price values to integers (since DB columns are int type)
            $priceInt = (int) round($product['offer_price']);
            $subTotalInt = (int) round($sub_total);

            // Prepare the parameter array without Id (let auto-increment handle it)
            $ParamArray = array(
                $newOrderId, // OrderId (string)
                $product['id'], // ProductId (integer)
                $product['code'], // ProductCode (string)
                $product['quantity'], // Quantity (integer)
                $product['size'], // Size (string)
                $priceInt, // Price (integer) - converted from decimal
                $subTotalInt // SubTotal (integer) - converted from decimal
            );

            // Call the fInsertNew method to insert the product data
            $productInsertId = $obj->fInsertNew(
                "INSERT INTO order_details (OrderId, ProductId, ProductCode, Quantity, Size, Price, SubTotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                "sissiii", // Data types: s=string, i=integer
                $ParamArray
            );

            if (!$productInsertId) {
                // Get more detailed error information
                $errorInfo = $conn->error;
                throw new Exception("Failed to insert product details for product ID: " . $product['id'] .
                                  ". Database error: " . $errorInfo);
            }
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(["response" => "E", "message" => "Error inserting product details: " . $e->getMessage()]);
            exit();
        }
    }

    // Clear any output buffer before sending JSON response
    ob_clean();

    // Clear sessions after successful order placement (if any exist)
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (isset($_SESSION['buy_now'])) {
        unset($_SESSION['buy_now']);
    }
    if (isset($_SESSION['applied_coupon'])) {
        unset($_SESSION['applied_coupon']);
    }

    // Return success response
    echo json_encode([
        'response' => 'S',
        'message' => 'Order placed successfully',
        'order_id' => $newOrderId,
        'transaction_id' => 'NA', // No Razorpay transaction ID for COD
        'payment_status' => $paymentStatus,
        'amount' => $data['final_total'],
        'mobile' => $data['phone'],
        'name' => $data['name'],
        'customer_type' => 'Guest'
    ]);

} else {
    // Clear any output buffer before sending JSON error response
    ob_clean();
    echo json_encode(["response" => "E", "message" => "Error placing order in database"]);
}
?>

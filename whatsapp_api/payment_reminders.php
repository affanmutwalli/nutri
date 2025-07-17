<?php
/**
 * Payment Reminders & Failed Payment Recovery
 * Sends WhatsApp reminders for pending/failed payments
 */

function sendPaymentReminder($customerName, $mobile, $orderId, $amount, $paymentLink) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "payment_reminder_" . $orderId,
        "type" => "Template",
        "template" => [
            "name" => "payment_reminder",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $orderId,
                "₹" . number_format($amount, 2),
                "24 hours" // Payment deadline
            ],
            "buttonValues" => (object)[
                "0" => [$paymentLink]
            ]
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendFailedPaymentRecovery($customerName, $mobile, $orderId, $amount, $retryLink, $discountCode = null) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $bodyValues = [
        $customerName,
        $orderId,
        "₹" . number_format($amount, 2)
    ];
    
    // Add discount if provided
    if ($discountCode) {
        $bodyValues[] = $discountCode;
        $bodyValues[] = "10%"; // Discount percentage
    }
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "failed_payment_" . $orderId,
        "type" => "Template",
        "template" => [
            "name" => $discountCode ? "failed_payment_with_discount" : "failed_payment_retry",
            "languageCode" => "en",
            "bodyValues" => $bodyValues,
            "buttonValues" => (object)[
                "0" => [$retryLink]
            ]
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendCODConfirmation($customerName, $mobile, $orderId, $amount, $deliveryDate) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "cod_confirmation_" . $orderId,
        "type" => "Template",
        "template" => [
            "name" => "cod_confirmation",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $orderId,
                "₹" . number_format($amount, 2),
                $deliveryDate
            ]
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendWhatsAppMessage($data, $apiUrl, $accessToken) {
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        "success" => ($httpCode == 201),
        "response" => $response,
        "http_code" => $httpCode
    ];
}

// Check for pending payments (run via cron)
function checkPendingPayments() {
    require_once '../exe_files/connection.php';
    $obj = new Connection();
    
    // Get orders with pending payments older than 1 hour
    $pendingOrders = $obj->MysqliSelect(
        "SELECT o.OrderId, o.TotalAmount, c.Name, c.MobileNo, o.CreationDate
         FROM order_master o
         JOIN customer_master c ON o.CustomerId = c.CustomerId
         WHERE o.PaymentStatus = 'Pending' 
         AND o.CreationDate < DATE_SUB(NOW(), INTERVAL 1 HOUR)
         AND o.CreationDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
        ["OrderId", "TotalAmount", "Name", "MobileNo", "CreationDate"],
        "",
        []
    );
    
    $results = [];
    foreach ($pendingOrders as $order) {
        $paymentLink = "https://mynutrify.com/payment.php?order_id=" . $order['OrderId'];
        $result = sendPaymentReminder(
            $order['Name'],
            $order['MobileNo'],
            $order['OrderId'],
            $order['TotalAmount'],
            $paymentLink
        );
        $results[] = [
            'order_id' => $order['OrderId'],
            'customer' => $order['Name'],
            'result' => $result
        ];
    }
    
    return $results;
}

// API endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'payment_reminder':
            $result = sendPaymentReminder(
                $input['customer_name'],
                $input['mobile'],
                $input['order_id'],
                $input['amount'],
                $input['payment_link']
            );
            break;
            
        case 'failed_payment':
            $result = sendFailedPaymentRecovery(
                $input['customer_name'],
                $input['mobile'],
                $input['order_id'],
                $input['amount'],
                $input['retry_link'],
                $input['discount_code'] ?? null
            );
            break;
            
        case 'cod_confirmation':
            $result = sendCODConfirmation(
                $input['customer_name'],
                $input['mobile'],
                $input['order_id'],
                $input['amount'],
                $input['delivery_date']
            );
            break;
            
        case 'check_pending':
            $result = checkPendingPayments();
            break;
            
        default:
            $result = ["error" => "Invalid action"];
    }
    
    echo json_encode($result);
}
?>

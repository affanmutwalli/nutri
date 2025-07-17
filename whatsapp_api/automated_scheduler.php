<?php
/**
 * Automated WhatsApp Message Scheduler
 * Runs various automated WhatsApp campaigns via cron jobs
 */

// Set execution time limit for long-running processes
set_time_limit(300); // 5 minutes

// Log file for tracking automated messages
$logFile = __DIR__ . '/logs/automated_messages.log';

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    
    // Create logs directory if it doesn't exist
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function runBirthdayWishes() {
    logMessage("Starting birthday wishes automation...");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/whatsapp_api/birthday_wishes.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'check_birthdays']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    $count = is_array($result) ? count($result) : 0;
    
    logMessage("Birthday wishes sent to $count customers. HTTP Code: $httpCode");
    return $result;
}

function runPaymentReminders() {
    logMessage("Starting payment reminders automation...");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/whatsapp_api/payment_reminders.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'check_pending']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    $count = is_array($result) ? count($result) : 0;
    
    logMessage("Payment reminders sent to $count customers. HTTP Code: $httpCode");
    return $result;
}

function runInactiveCustomerReminders() {
    logMessage("Starting inactive customer reminders automation...");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/whatsapp_api/product_recommendations.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'check_inactive']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    $count = is_array($result) ? count($result) : 0;
    
    logMessage("Reorder reminders sent to $count inactive customers. HTTP Code: $httpCode");
    return $result;
}

function runFeedbackRequests() {
    logMessage("Starting feedback requests automation...");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/whatsapp_api/feedback_requests.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'check_delivered_orders']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    $count = is_array($result) ? count($result) : 0;
    
    logMessage("Feedback requests sent to $count customers. HTTP Code: $httpCode");
    return $result;
}

function runReviewIncentives() {
    logMessage("Starting review incentives automation...");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/nutrify/whatsapp_api/feedback_requests.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'send_review_incentives']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    $count = is_array($result) ? count($result) : 0;
    
    logMessage("Review incentives sent to $count customers. HTTP Code: $httpCode");
    return $result;
}

function runCartAbandonmentReminders() {
    logMessage("Starting cart abandonment reminders automation...");
    
    require_once '../exe_files/connection.php';
    $obj = new Connection();
    
    // Get abandoned carts (sessions with items but no orders in last 24 hours)
    $abandonedCarts = $obj->MysqliSelect(
        "SELECT DISTINCT s.CustomerId, c.Name, c.MobileNo, 
                GROUP_CONCAT(p.ProductName SEPARATOR ', ') as ProductNames,
                MIN(p.ProductImage) as ProductImage
         FROM cart_session s
         JOIN customer_master c ON s.CustomerId = c.CustomerId
         JOIN cart_items ci ON s.SessionId = ci.SessionId
         JOIN product_master p ON ci.ProductId = p.ProductId
         WHERE s.CreatedAt < DATE_SUB(NOW(), INTERVAL 2 HOUR)
         AND s.CreatedAt > DATE_SUB(NOW(), INTERVAL 24 HOUR)
         AND s.CustomerId NOT IN (
             SELECT CustomerId FROM order_master 
             WHERE CreationDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)
         )
         GROUP BY s.CustomerId
         LIMIT 20",
        ["CustomerId", "Name", "MobileNo", "ProductNames", "ProductImage"],
        "",
        []
    );
    
    $results = [];
    foreach ($abandonedCarts as $cart) {
        $checkoutLink = "https://mynutrify.com/checkout.php?customer_id=" . $cart['CustomerId'];
        
        // Use existing cart abandonment template
        $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
        $apiUrl = "https://api.interakt.ai/v1/public/message/";
        
        $data = [
            "countryCode" => "+91",
            "phoneNumber" => $cart['MobileNo'],
            "callbackData" => "cart_abandonment",
            "type" => "Template",
            "template" => [
                "name" => "your_cart_is_waiting",
                "languageCode" => "en_US",
                "headerValues" => [$cart['ProductImage']],
                "bodyValues" => [
                    $cart['Name'],
                    $cart['ProductNames']
                ],
                "buttonValues" => new stdClass()
            ]
        ];
        
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
        
        $results[] = [
            'customer_id' => $cart['CustomerId'],
            'customer' => $cart['Name'],
            'success' => ($httpCode == 201)
        ];
    }
    
    $count = count($results);
    logMessage("Cart abandonment reminders sent to $count customers.");
    return $results;
}

// Main execution based on command line argument or GET parameter
$action = $argv[1] ?? $_GET['action'] ?? 'all';

logMessage("Starting automated WhatsApp scheduler with action: $action");

$results = [];

switch ($action) {
    case 'birthdays':
        $results['birthdays'] = runBirthdayWishes();
        break;
        
    case 'payments':
        $results['payments'] = runPaymentReminders();
        break;
        
    case 'inactive':
        $results['inactive'] = runInactiveCustomerReminders();
        break;
        
    case 'feedback':
        $results['feedback'] = runFeedbackRequests();
        break;
        
    case 'reviews':
        $results['reviews'] = runReviewIncentives();
        break;
        
    case 'cart':
        $results['cart'] = runCartAbandonmentReminders();
        break;
        
    case 'all':
    default:
        // Run all automations (for daily cron job)
        $results['birthdays'] = runBirthdayWishes();
        sleep(2); // Small delay between API calls
        
        $results['payments'] = runPaymentReminders();
        sleep(2);
        
        $results['feedback'] = runFeedbackRequests();
        sleep(2);
        
        $results['cart'] = runCartAbandonmentReminders();
        sleep(2);
        
        // Run these less frequently (only on specific days)
        $dayOfWeek = date('N'); // 1 = Monday, 7 = Sunday
        
        if ($dayOfWeek == 3) { // Wednesday - inactive customers
            $results['inactive'] = runInactiveCustomerReminders();
            sleep(2);
        }
        
        if ($dayOfWeek == 6) { // Saturday - review incentives
            $results['reviews'] = runReviewIncentives();
        }
        break;
}

logMessage("Automated WhatsApp scheduler completed successfully.");

// Output results (for web access or debugging)
if (isset($_GET['action']) || php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'action' => $action,
        'timestamp' => date('Y-m-d H:i:s'),
        'results' => $results
    ], JSON_PRETTY_PRINT);
}
?>

<?php
/**
 * Birthday & Anniversary WhatsApp Wishes
 * Sends personalized birthday/anniversary messages with special offers
 */

function sendBirthdayWish($customerName, $mobile, $discountCode = null) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "birthday_wish",
        "type" => "Template",
        "template" => [
            "name" => "birthday_wishes",
            "languageCode" => "en",
            "headerValues" => [
                "https://mynutrify.com/cms/images/birthday-banner.jpg" // Add birthday image
            ],
            "bodyValues" => [
                $customerName,
                $discountCode ?: "BIRTHDAY20",
                "20%", // Discount percentage
                "My Nutrify"
            ],
            "buttonValues" => new stdClass()
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendAnniversaryWish($customerName, $mobile, $years, $discountCode = null) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "anniversary_wish",
        "type" => "Template",
        "template" => [
            "name" => "anniversary_wishes",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $years,
                $discountCode ?: "ANNIVERSARY25",
                "25%"
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

// Automated birthday checker (run daily via cron)
function checkTodaysBirthdays() {
    // Include your database connection
    require_once '../exe_files/connection.php';
    $obj = new Connection();
    
    $today = date('m-d'); // MM-DD format
    
    // Query customers with today's birthday
    $birthdayCustomers = $obj->MysqliSelect(
        "SELECT Name, MobileNo, DATE_FORMAT(DateOfBirth, '%m-%d') as birthday_md 
         FROM customer_master 
         WHERE DATE_FORMAT(DateOfBirth, '%m-%d') = ? 
         AND IsActive = 1",
        ["birthday_md", "Name", "MobileNo"],
        "s",
        [$today]
    );
    
    $results = [];
    foreach ($birthdayCustomers as $customer) {
        $result = sendBirthdayWish($customer['Name'], $customer['MobileNo']);
        $results[] = [
            'customer' => $customer['Name'],
            'mobile' => $customer['MobileNo'],
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
        case 'birthday':
            $result = sendBirthdayWish(
                $input['customer_name'],
                $input['mobile'],
                $input['discount_code'] ?? null
            );
            break;
            
        case 'anniversary':
            $result = sendAnniversaryWish(
                $input['customer_name'],
                $input['mobile'],
                $input['years'],
                $input['discount_code'] ?? null
            );
            break;
            
        case 'check_birthdays':
            $result = checkTodaysBirthdays();
            break;
            
        default:
            $result = ["error" => "Invalid action"];
    }
    
    echo json_encode($result);
}
?>

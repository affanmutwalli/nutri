<?php
/**
 * Customer Feedback & Review Requests via WhatsApp
 * Sends feedback requests after delivery and handles responses
 */

function sendFeedbackRequest($customerName, $mobile, $orderId, $productNames, $reviewLink) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "feedback_request_" . $orderId,
        "type" => "Template",
        "template" => [
            "name" => "feedback_request",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $productNames,
                "My Nutrify"
            ],
            "buttonValues" => (object)[
                "0" => [$reviewLink]
            ]
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendReviewIncentive($customerName, $mobile, $discountCode, $discountAmount) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "review_incentive",
        "type" => "Template",
        "template" => [
            "name" => "review_incentive",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $discountCode,
                "₹" . $discountAmount,
                "https://mynutrify.com/review"
            ]
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendThankYouForReview($customerName, $mobile, $rewardCode = null) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $bodyValues = [$customerName, "My Nutrify"];
    
    if ($rewardCode) {
        $bodyValues[] = $rewardCode;
        $bodyValues[] = "₹100"; // Reward amount
    }
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "thank_you_review",
        "type" => "Template",
        "template" => [
            "name" => $rewardCode ? "thank_you_review_with_reward" : "thank_you_review",
            "languageCode" => "en",
            "bodyValues" => $bodyValues
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendNegativeFeedbackFollowup($customerName, $mobile, $orderId, $supportLink) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "negative_feedback_" . $orderId,
        "type" => "Template",
        "template" => [
            "name" => "negative_feedback_followup",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $orderId,
                "My Nutrify Support Team"
            ],
            "buttonValues" => (object)[
                "0" => [$supportLink]
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

// Check for delivered orders that need feedback requests
function checkDeliveredOrdersForFeedback() {
    require_once '../exe_files/connection.php';
    $obj = new Connection();
    
    // Get orders delivered 2-3 days ago that haven't received feedback requests
    $deliveredOrders = $obj->MysqliSelect(
        "SELECT o.OrderId, o.CustomerId, c.Name, c.MobileNo,
                GROUP_CONCAT(p.ProductName SEPARATOR ', ') as ProductNames
         FROM order_master o
         JOIN customer_master c ON o.CustomerId = c.CustomerId
         JOIN order_details od ON o.OrderId = od.OrderId
         JOIN product_master p ON od.ProductId = p.ProductId
         WHERE o.OrderStatus = 'Delivered'
         AND o.DeliveryDate BETWEEN DATE_SUB(NOW(), INTERVAL 3 DAY) AND DATE_SUB(NOW(), INTERVAL 2 DAY)
         AND o.OrderId NOT IN (
             SELECT OrderId FROM feedback_requests WHERE RequestSent = 1
         )
         GROUP BY o.OrderId
         LIMIT 50",
        ["OrderId", "CustomerId", "Name", "MobileNo", "ProductNames"],
        "",
        []
    );
    
    $results = [];
    foreach ($deliveredOrders as $order) {
        $reviewLink = "https://mynutrify.com/review.php?order_id=" . $order['OrderId'];
        $result = sendFeedbackRequest(
            $order['Name'],
            $order['MobileNo'],
            $order['OrderId'],
            $order['ProductNames'],
            $reviewLink
        );
        
        // Mark as feedback request sent
        if ($result['success']) {
            $obj->fInsertNew(
                "INSERT INTO feedback_requests (OrderId, CustomerId, RequestSent, RequestDate) VALUES (?, ?, 1, NOW())",
                "ii",
                [$order['OrderId'], $order['CustomerId']]
            );
        }
        
        $results[] = [
            'order_id' => $order['OrderId'],
            'customer' => $order['Name'],
            'result' => $result
        ];
    }
    
    return $results;
}

// Send review incentives to customers who haven't reviewed yet
function sendReviewIncentives() {
    require_once '../exe_files/connection.php';
    $obj = new Connection();
    
    // Get customers who received feedback requests but haven't reviewed
    $pendingReviews = $obj->MysqliSelect(
        "SELECT fr.OrderId, c.Name, c.MobileNo
         FROM feedback_requests fr
         JOIN order_master o ON fr.OrderId = o.OrderId
         JOIN customer_master c ON fr.CustomerId = c.CustomerId
         WHERE fr.RequestSent = 1
         AND fr.RequestDate < DATE_SUB(NOW(), INTERVAL 7 DAY)
         AND fr.OrderId NOT IN (SELECT OrderId FROM reviews WHERE ReviewDate IS NOT NULL)
         AND fr.IncentiveSent = 0
         LIMIT 20",
        ["OrderId", "Name", "MobileNo"],
        "",
        []
    );
    
    $results = [];
    foreach ($pendingReviews as $review) {
        $discountCode = "REVIEW" . rand(1000, 9999);
        $result = sendReviewIncentive(
            $review['Name'],
            $review['MobileNo'],
            $discountCode,
            50 // ₹50 discount
        );
        
        if ($result['success']) {
            $obj->MysqliUpdate(
                "UPDATE feedback_requests SET IncentiveSent = 1, IncentiveCode = ? WHERE OrderId = ?",
                "si",
                [$discountCode, $review['OrderId']]
            );
        }
        
        $results[] = [
            'order_id' => $review['OrderId'],
            'customer' => $review['Name'],
            'discount_code' => $discountCode,
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
        case 'feedback_request':
            $result = sendFeedbackRequest(
                $input['customer_name'],
                $input['mobile'],
                $input['order_id'],
                $input['product_names'],
                $input['review_link']
            );
            break;
            
        case 'review_incentive':
            $result = sendReviewIncentive(
                $input['customer_name'],
                $input['mobile'],
                $input['discount_code'],
                $input['discount_amount']
            );
            break;
            
        case 'thank_you_review':
            $result = sendThankYouForReview(
                $input['customer_name'],
                $input['mobile'],
                $input['reward_code'] ?? null
            );
            break;
            
        case 'negative_feedback_followup':
            $result = sendNegativeFeedbackFollowup(
                $input['customer_name'],
                $input['mobile'],
                $input['order_id'],
                $input['support_link']
            );
            break;
            
        case 'check_delivered_orders':
            $result = checkDeliveredOrdersForFeedback();
            break;
            
        case 'send_review_incentives':
            $result = sendReviewIncentives();
            break;
            
        default:
            $result = ["error" => "Invalid action"];
    }
    
    echo json_encode($result);
}
?>

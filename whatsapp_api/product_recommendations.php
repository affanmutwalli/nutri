<?php
/**
 * Product Recommendations & Cross-selling via WhatsApp
 * Sends personalized product suggestions based on purchase history
 */

function sendProductRecommendation($customerName, $mobile, $productName, $productImage, $price, $productLink, $discountCode = null) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $bodyValues = [
        $customerName,
        $productName,
        "₹" . number_format($price, 2)
    ];
    
    if ($discountCode) {
        $bodyValues[] = $discountCode;
        $bodyValues[] = "15%"; // Discount percentage
    }
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "product_recommendation",
        "type" => "Template",
        "template" => [
            "name" => $discountCode ? "product_recommendation_with_discount" : "product_recommendation",
            "languageCode" => "en",
            "headerValues" => [$productImage],
            "bodyValues" => $bodyValues,
            "buttonValues" => (object)[
                "0" => [$productLink]
            ]
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendComboOffer($customerName, $mobile, $comboName, $originalPrice, $comboPrice, $savings, $comboLink) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "combo_offer",
        "type" => "Template",
        "template" => [
            "name" => "combo_offer",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $comboName,
                "₹" . number_format($originalPrice, 2),
                "₹" . number_format($comboPrice, 2),
                "₹" . number_format($savings, 2)
            ],
            "buttonValues" => (object)[
                "0" => [$comboLink]
            ]
        ]
    ];
    
    return sendWhatsAppMessage($data, $apiUrl, $accessToken);
}

function sendReorderReminder($customerName, $mobile, $lastOrderProducts, $reorderLink) {
    $accessToken = "SWwxOXhUbTJPYWJRVEJlcWdGekZPTk1oTHBFVUd1OHNNbzdJcENibTdUWTo=";
    $apiUrl = "https://api.interakt.ai/v1/public/message/";
    
    $data = [
        "countryCode" => "+91",
        "phoneNumber" => $mobile,
        "callbackData" => "reorder_reminder",
        "type" => "Template",
        "template" => [
            "name" => "reorder_reminder",
            "languageCode" => "en",
            "bodyValues" => [
                $customerName,
                $lastOrderProducts, // Comma-separated product names
                "30 days ago" // Time since last order
            ],
            "buttonValues" => (object)[
                "0" => [$reorderLink]
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

// Generate personalized recommendations based on purchase history
function generateRecommendations($customerId) {
    require_once '../exe_files/connection.php';
    $obj = new Connection();
    
    // Get customer's purchase history
    $purchaseHistory = $obj->MysqliSelect(
        "SELECT DISTINCT p.CategoryId, p.ProductName, p.Price, p.ProductImage
         FROM order_details od
         JOIN product_master p ON od.ProductId = p.ProductId
         JOIN order_master o ON od.OrderId = o.OrderId
         WHERE o.CustomerId = ? AND o.OrderStatus = 'Delivered'
         ORDER BY o.CreationDate DESC LIMIT 10",
        ["CategoryId", "ProductName", "Price", "ProductImage"],
        "i",
        [$customerId]
    );
    
    if (empty($purchaseHistory)) {
        return [];
    }
    
    // Get categories customer has purchased from
    $categories = array_unique(array_column($purchaseHistory, 'CategoryId'));
    
    // Find related products in same categories
    $recommendations = $obj->MysqliSelect(
        "SELECT ProductId, ProductName, Price, ProductImage, CategoryId
         FROM product_master 
         WHERE CategoryId IN (" . implode(',', $categories) . ")
         AND IsActive = 1
         AND ProductId NOT IN (
             SELECT DISTINCT od.ProductId 
             FROM order_details od 
             JOIN order_master o ON od.OrderId = o.OrderId 
             WHERE o.CustomerId = ?
         )
         ORDER BY RAND() LIMIT 5",
        ["ProductId", "ProductName", "Price", "ProductImage", "CategoryId"],
        "i",
        [$customerId]
    );
    
    return $recommendations;
}

// Check for customers who haven't ordered in 30+ days
function checkInactiveCustomers() {
    require_once '../exe_files/connection.php';
    $obj = new Connection();
    
    $inactiveCustomers = $obj->MysqliSelect(
        "SELECT c.CustomerId, c.Name, c.MobileNo, 
                MAX(o.CreationDate) as LastOrderDate,
                GROUP_CONCAT(DISTINCT p.ProductName SEPARATOR ', ') as LastProducts
         FROM customer_master c
         JOIN order_master o ON c.CustomerId = o.CustomerId
         JOIN order_details od ON o.OrderId = od.OrderId
         JOIN product_master p ON od.ProductId = p.ProductId
         WHERE c.IsActive = 1
         GROUP BY c.CustomerId
         HAVING LastOrderDate < DATE_SUB(NOW(), INTERVAL 30 DAY)
         AND LastOrderDate > DATE_SUB(NOW(), INTERVAL 90 DAY)
         LIMIT 50",
        ["CustomerId", "Name", "MobileNo", "LastOrderDate", "LastProducts"],
        "",
        []
    );
    
    $results = [];
    foreach ($inactiveCustomers as $customer) {
        $reorderLink = "https://mynutrify.com/reorder.php?customer_id=" . $customer['CustomerId'];
        $result = sendReorderReminder(
            $customer['Name'],
            $customer['MobileNo'],
            $customer['LastProducts'],
            $reorderLink
        );
        $results[] = [
            'customer_id' => $customer['CustomerId'],
            'customer' => $customer['Name'],
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
        case 'product_recommendation':
            $result = sendProductRecommendation(
                $input['customer_name'],
                $input['mobile'],
                $input['product_name'],
                $input['product_image'],
                $input['price'],
                $input['product_link'],
                $input['discount_code'] ?? null
            );
            break;
            
        case 'combo_offer':
            $result = sendComboOffer(
                $input['customer_name'],
                $input['mobile'],
                $input['combo_name'],
                $input['original_price'],
                $input['combo_price'],
                $input['savings'],
                $input['combo_link']
            );
            break;
            
        case 'reorder_reminder':
            $result = sendReorderReminder(
                $input['customer_name'],
                $input['mobile'],
                $input['last_products'],
                $input['reorder_link']
            );
            break;
            
        case 'check_inactive':
            $result = checkInactiveCustomers();
            break;
            
        case 'generate_recommendations':
            $result = generateRecommendations($input['customer_id']);
            break;
            
        default:
            $result = ["error" => "Invalid action"];
    }
    
    echo json_encode($result);
}
?>

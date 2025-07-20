<?php
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');


$obj = new main();
$mysqli = $obj->connection();

sec_session_start();

header('Content-Type: application/json'); // Ensure the response is JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Decode JSON data from request
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['code']) && !empty($data['code'])) {
            $code = htmlspecialchars($data['code']);
            $orderAmount = isset($data['order_amount']) ? floatval($data['order_amount']) : 0;

            // First check enhanced_coupons table (new system)
            $enhancedResult = checkEnhancedCoupon($code, $orderAmount, $mysqli);
            if ($enhancedResult['found']) {
                echo json_encode($enhancedResult['response']);
                exit;
            }

            // Fallback to old coupons table
            $FieldNames = array("CodeId", "Code", "Discount", "Above", "Status");
            $ParamArray = array($code);
            $Fields = implode(",", $FieldNames);
            $result = $obj->MysqliSelect1("SELECT " . $Fields . " FROM coupons WHERE Code = ? AND Status = 'Active'", $FieldNames, "s", $ParamArray);

            if (!empty($result)) {
                $discount = floatval($result[0]['Discount']);
                $minAmount = floatval($result[0]['Above']);

                // Check minimum order amount
                if ($orderAmount > 0 && $orderAmount < $minAmount) {
                    echo json_encode([
                        "msg" => "Minimum order amount ₹" . number_format($minAmount, 2) . " required",
                        "response" => "E"
                    ]);
                } else {
                    echo json_encode([
                        "msg" => "Coupon applied successfully! You saved ₹" . number_format($discount, 2),
                        "response" => "S",
                        "discount" => $discount
                    ]);
                }
            } else {
                echo json_encode([
                    "msg" => "The Code Is Invalid or Expired",
                    "response" => "E"
                ]);
            }
        } else {
            echo json_encode([
                "msg" => "Please Enter Code",
                "response" => "E"
            ]);
        }
    } else {
        echo json_encode([
            "msg" => "Invalid request method.",
            "response" => "E"
        ]);
    }
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    echo json_encode([
        "msg" => "An internal error occurred.",
        "response" => "E"
    ]);
}

/**
 * Check enhanced coupons table (new system)
 */
function checkEnhancedCoupon($couponCode, $orderAmount, $mysqli) {
    try {
        // First check rewards-generated coupons table
        $rewardsQuery = "SELECT * FROM coupons
                         WHERE coupon_code = ?
                           AND is_active = 1
                           AND is_used = 0
                           AND (expires_at IS NULL OR expires_at >= NOW())";

        $rewardsStmt = $mysqli->prepare($rewardsQuery);
        if ($rewardsStmt) {
            $rewardsStmt->bind_param("s", $couponCode);
            $rewardsStmt->execute();
            $rewardsResult = $rewardsStmt->get_result();

            if ($rewardsCoupon = $rewardsResult->fetch_assoc()) {
                // Handle rewards coupon
                $discount = 0;
                if ($rewardsCoupon['discount_type'] === 'fixed') {
                    $discount = min($rewardsCoupon['discount_value'], $orderAmount);
                } elseif ($rewardsCoupon['discount_type'] === 'free_shipping') {
                    $discount = 0; // Free shipping handled separately
                }

                // Check minimum order amount
                $minAmount = $rewardsCoupon['min_order_amount'] ?? 0;
                if ($orderAmount < $minAmount) {
                    return [
                        'found' => true,
                        'response' => [
                            "msg" => "Minimum order amount ₹" . number_format($minAmount, 2) . " required",
                            "response" => "E"
                        ]
                    ];
                }

                return [
                    'found' => true,
                    'response' => [
                        "msg" => "Reward coupon applied successfully! You saved ₹" . number_format($discount, 2),
                        "response" => "S",
                        "discount" => $discount,
                        "coupon_id" => $rewardsCoupon['id'],
                        "coupon_code" => $rewardsCoupon['coupon_code'],
                        "is_reward_coupon" => true
                    ]
                ];
            }
        }

        // Then check if enhanced_coupons table exists
        $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
        if (!$tableCheck || $tableCheck->num_rows === 0) {
            return ['found' => false];
        }

        // Direct database query for enhanced coupons
        $query = "SELECT id, coupon_code, coupon_name, discount_type, discount_value,
                         max_discount_amount, minimum_order_amount, is_active,
                         valid_from, valid_until, current_usage_count, usage_limit_total,
                         customer_type, is_reward_coupon
                  FROM enhanced_coupons
                  WHERE coupon_code = ?
                    AND is_active = 1
                    AND valid_from <= NOW()
                    AND valid_until >= NOW()";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $couponCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($coupon = $result->fetch_assoc()) {
            // Check if it's a reward coupon (requires login)
            if ($coupon['is_reward_coupon']) {
                return [
                    'found' => true,
                    'response' => [
                        "msg" => "Please login to use this reward coupon",
                        "response" => "E"
                    ]
                ];
            }

            // Check usage limit
            if ($coupon['usage_limit_total'] && $coupon['current_usage_count'] >= $coupon['usage_limit_total']) {
                return [
                    'found' => true,
                    'response' => [
                        "msg" => "Coupon usage limit exceeded",
                        "response" => "E"
                    ]
                ];
            }

            // Check minimum order amount
            if ($orderAmount > 0 && $orderAmount < $coupon['minimum_order_amount']) {
                return [
                    'found' => true,
                    'response' => [
                        "msg" => "Minimum order amount ₹" . number_format($coupon['minimum_order_amount'], 2) . " required",
                        "response" => "E"
                    ]
                ];
            }

            // Calculate discount
            $discount = 0;
            if ($coupon['discount_type'] === 'fixed') {
                $discount = min($coupon['discount_value'], $orderAmount);
            } else { // percentage
                $discount = ($orderAmount * $coupon['discount_value']) / 100;
                if ($coupon['max_discount_amount']) {
                    $discount = min($discount, $coupon['max_discount_amount']);
                }
            }
            $discount = round($discount, 2);

            return [
                'found' => true,
                'response' => [
                    "msg" => "Coupon applied successfully! You saved ₹" . number_format($discount, 2),
                    "response" => "S",
                    "discount" => $discount,
                    "coupon_id" => $coupon['id'],
                    "coupon_code" => $coupon['coupon_code']
                ]
            ];
        }

        return ['found' => false];

    } catch (Exception $e) {
        error_log("Error checking enhanced coupon: " . $e->getMessage());
        return ['found' => false];
    }
}
?>

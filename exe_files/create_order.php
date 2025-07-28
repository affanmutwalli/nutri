<?php
// Use consistent credentials from config file
include_once 'razorpay_config.php';
$keyId = RAZORPAY_KEY_ID;
$keySecret = RAZORPAY_KEY_SECRET;

$data = json_decode(file_get_contents("php://input"));

$amount = $data->amount;
$receipt = $data->receipt;

$url = "https://api.razorpay.com/v1/orders";

$fields = [
    "amount" => $amount * 100, // Amount in paise
    "currency" => "INR",
    "receipt" => $receipt
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);

$response = curl_exec($ch);
curl_close($ch);

header("Content-Type: application/json");
echo $response;
?>

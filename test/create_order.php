<?php
header('Content-Type: application/json');

// Provided Delhivery API key with Token prefix
$apiKey = '6fed62f581611c5f1778bc2a774dc341bc094e91';

// Use the correct endpoint for creating orders
$url = "https://track.delhivery.com/api/cmu/create.json";

// Build the inner payload with static customer and order details.
// Adjust the pickup_location details to match your registered warehouse if necessary.
$orderPayload = [
    "pickup_location" => [
        // "code"    => "WH001", // Include if your account requires a warehouse code.
        "name"    => "My Nutrify",
        "city"    => "Sangli",
        "pin"     => "416416",
        "phone"   => "9834243754",
        "address" => "Basement gala no B2, Building / flat no S.N.55, shivam iconic, shivaji nager, near corporation bank, Sangli."
    ],
    "shipments" => [
        [
            "name"           => "Muddassar K",
            "order"          => "MN000123",
            "products_desc"  => "Sample Product",
            "amount"         => 10, // Total order value
            "cod_amount"     => 10, // COD amount to be collected
            "quantity"       => 1,
            "payment_mode"   => "COD",
            "phone"          => "8329566751",
            "add"            => "Near Priyadarshini Hotel",
            "city"           => "Miraj",
            "pin"            => "416410",
            "state"          => "Maharashtra"
        ]
    ]
];

// Build the POST fields with two keys: "format" and "data"
// "data" must be a JSON-encoded string of your order payload.
$postData = http_build_query([
    "format" => "json",
    "data"   => json_encode($orderPayload)
]);

$headers = [
    "Authorization: Token $apiKey", // Ensure the "Token" prefix is used.
    "Content-Type: application/x-www-form-urlencoded"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    echo json_encode(["error" => "Error calling Delhivery API: " . $error_msg]);
    exit;
}

curl_close($ch);
echo $response;
?>

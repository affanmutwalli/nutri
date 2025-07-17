<?php
header('Content-Type: application/json');

// Input params
$awb = isset($_GET['awb']) ? trim($_GET['awb']) : '';
$orderId = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';

if (empty($awb) && empty($orderId)) {
    echo json_encode(["error" => "Missing awb or order_id parameter"]);
    exit;
}

// API Setup
$delhiveryApiKey = '6fed62f581611c5f1778bc2a774dc341bc094e91';
$searchParam = !empty($awb) ? "waybill=" . urlencode($awb) : "order=" . urlencode($orderId);
$apiUrl = 'https://track.delhivery.com/api/v1/packages/json/?' . $searchParam . '&token=' . $delhiveryApiKey;

// API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Decode response
$data = json_decode($response, true);

// Extract shipment details
$shipmentInfo = $data['ShipmentData'][0]['Shipment'] ?? null;
$latestStatus = null;
$estimatedDelivery = null;

if ($shipmentInfo && isset($shipmentInfo['Scans'])) {
    $scans = $shipmentInfo['Scans'];
    $latestStatus = end($scans); // latest scan

    // Estimated Delivery Date: try multiple field names
    $estimatedDelivery = $shipmentInfo['ExpectedDeliveryDate'] ??
                         $shipmentInfo['EDD'] ??
                         $shipmentInfo['PromisedDeliveryDate'] ??
                         null;
} elseif (isset($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
    $latestStatus = end($data['data']);
}

// Output
if ($latestStatus) {
    echo json_encode([
        "latest_status" => $latestStatus,
        "estimated_delivery" => $estimatedDelivery ?? "Not available"
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        "error" => "No tracking data found.",
        "raw_response" => $data
    ]);
}
?>

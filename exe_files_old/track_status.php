<?php
require_once '../database/delivery_config.php';

function trackDelhiveryOrder($waybillNumber) {
    $url = 'https://track.delhivery.com/api/packages/json/';
    $apiKey = DELHIVERY_API_KEY;

    // Build URL with parameters
    $params = [
        'waybill' => $waybillNumber,
        'verbose' => 2 // Get detailed tracking history
    ];
    $url .= '?' . http_build_query($params);

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Token ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute and handle response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $trackingData = json_decode($response, true);
        if (!empty($trackingData['ShipmentData'])) {
            return [
                'success' => true,
                'data' => $trackingData['ShipmentData'][0]['Shipment']
            ];
        }
    }

    return [
        'success' => false,
        'error' => 'Tracking failed: ' . $response
    ];
}

// Example usage:
$waybillNumber = '1234567890'; // Retrieve from your database
$result = trackDelhiveryOrder($waybillNumber);
if ($result['success']) {
    echo "Status: " . $result['data']['Status'];
    echo "<br>Scans:";
    foreach ($result['data']['Scans'] as $scan) {
        echo "<li>" . $scan['ScanDetail'] . " (" . $scan['ScanDateTime'] . ")</li>";
    }
} else {
    echo "Error: " . $result['error'];
}
?>
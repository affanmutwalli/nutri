<?php
include('includes/urls.php');
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");

$obj = new main();
$obj->connection();
sec_session_start();

header('Content-Type: application/json');

// --------------------------------------
// 1. Validate and fetch OrderId via GET method
if (!isset($_GET["OrderId"])) {
    echo json_encode(["success" => false, "message" => "OrderId is missing."]);
    exit;
}
$orderId = $_GET["OrderId"];

// --------------------------------------
// 2. Retrieve Order and Customer Details

// Get order_master details
$FieldNames = array("Id", "OrderId", "CustomerId", "OrderDate", "Amount", "TransactionId", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType", "CustomerType");
$ParamArray = array($orderId);
$Fields = implode(",", $FieldNames);
$order_master = $obj->MysqliSelect1("SELECT " . $Fields . " FROM order_master WHERE OrderId= ? ", $FieldNames, "s", $ParamArray);

if(empty($order_master)) {
    echo json_encode(["success" => false, "message" => "Order not found."]);
    exit;
}

$order_master = $order_master[0];


// Initialize customer variables
$Name = "";
$MobileNo = "";
$Email = "";
$IsActive = "";
$Address = "";
$Landmark = "";
$State = "";
$City = "";
$Pincode = "";
$ShipAddress = "";

// Check customer type and fetch details accordingly
if ($order_master["CustomerType"] == "Direct") {
    $FieldNames = array("CustomerName", "MobileNo", "Email", "Address", "City", "Pincode", "State");
    $ParamArray = array($order_master["CustomerId"]);
    $Fields = implode(",", $FieldNames);
    $customer_details = $obj->MysqliSelect1("SELECT " . $Fields . " FROM direct_customers WHERE CustomerId= ? ", $FieldNames, "i", $ParamArray);

    if(!empty($customer_details)) {
        $Name = $customer_details[0]["CustomerName"];
        $MobileNo = $customer_details[0]["MobileNo"];
        $Email = $customer_details[0]["Email"];
        // For direct customers, use the address from the customer table
        $ShipAddress = $customer_details[0]["Address"] . ", " . $customer_details[0]["City"] . ", " . $customer_details[0]["Pincode"] . ", " . $customer_details[0]["State"];
        $State = $customer_details[0]["State"];
        $City = $customer_details[0]["City"];
        $Pincode = $customer_details[0]["Pincode"];
    }
} elseif ($order_master["CustomerType"] == "Guest") {
    // For Guest orders, parse the ShipAddress field which contains all customer info
    // Format: Name, Email, Phone, Address, Landmark, Pincode, City, State
    $shipAddressParts = explode(", ", $order_master["ShipAddress"]);

    if (count($shipAddressParts) >= 8) {
        $Name = trim($shipAddressParts[0]);
        $Email = trim($shipAddressParts[1]);
        $MobileNo = trim($shipAddressParts[2]);
        $Address = trim($shipAddressParts[3]);
        $Landmark = trim($shipAddressParts[4]);
        $Pincode = trim($shipAddressParts[5]);
        $City = trim($shipAddressParts[6]);
        $State = trim($shipAddressParts[7]);

        // Reconstruct shipping address for delivery
        $ShipAddress = "$Address, $Landmark, $City, $Pincode, $State";
    } else {
        // Fallback: try to extract phone number from the address string
        $fullAddress = $order_master["ShipAddress"];
        if (preg_match('/(\d{10})/', $fullAddress, $matches)) {
            $MobileNo = $matches[1];
        }

        // Extract name (usually the first part before comma)
        $addressParts = explode(", ", $fullAddress);
        $Name = !empty($addressParts[0]) ? $addressParts[0] : "Guest Customer";
        $ShipAddress = $fullAddress;

        // Try to extract other details
        if (preg_match('/(\d{6})/', $fullAddress, $pincodeMatch)) {
            $Pincode = $pincodeMatch[1];
        }
    }
} else {
    // For non-direct customers, fetch details from customer_master and customer_address tables
    $FieldNames = array("Name", "MobileNo", "Email", "IsActive");
    $ParamArray = array($order_master["CustomerId"]);
    $Fields = implode(",", $FieldNames);
    $customer_details = $obj->MysqliSelect1("SELECT " . $Fields . " FROM customer_master WHERE CustomerId= ? ", $FieldNames, "i", $ParamArray);
    $FieldNames = array("Address", "Landmark", "State", "City", "PinCode");
    $ParamArray = array($order_master["CustomerId"]);
    $Fields = implode(",", $FieldNames);
    $customer_address = $obj->MysqliSelect1("SELECT " . $Fields . " FROM customer_address WHERE CustomerId= ? ", $FieldNames, "i", $ParamArray);

    
    if(!empty($customer_details) && !empty($customer_address)) {
        $Name = $customer_details[0]["Name"];
        $MobileNo = $customer_details[0]["MobileNo"];
        $Email = $customer_details[0]["Email"];
        $IsActive = $customer_details[0]["IsActive"];
        $Address = $customer_address[0]["Address"];
        $Landmark = $customer_address[0]["Landmark"];
        $State = $customer_address[0]["State"];
        $City = $customer_address[0]["City"];
        $Pincode = $customer_address[0]["PinCode"];
        // Concatenate full shipping address for non-direct customers
        $ShipAddress = $Address . ", " . $Landmark . ", " . $City . ", " . $Pincode . ", " . $State;
    }
}


// --------------------------------------
// 3. Retrieve Order Details and then Product Details
// First, get order_details from order_details table
$FieldNames = array("ProductId", "Quantity", "Price", "SubTotal", "ProductCode", "ProductId");
$ParamArray = array($orderId);
$Fields = implode(",", $FieldNames);
$order_details_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM order_details WHERE OrderId= ? ", $FieldNames, "s", $ParamArray);

if(empty($order_details_data)) {
    echo json_encode(["success" => false, "message" => "No order details found."]);
    exit;
}

// Initialize variables for payload details
$totalQuantity = 0;
$productNames = [];
$photoPaths = [];
$hasShilajit = false;

// Loop through each order detail row and fetch product details for each product id
foreach ($order_details_data as $item) {
    $totalQuantity += $item['Quantity'];
    
    $productId = $item["ProductId"];
    $FieldNames = array("ProductName", "PhotoPath", "ProductCode");
    $ParamArray = array($productId);
    $Fields = implode(",", $FieldNames);
    $product_detail = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_master WHERE ProductId= ? ", $FieldNames, "i", $ParamArray);
    
    if(!empty($product_detail)) {
        $prodName = $product_detail[0]["ProductCode"];
        $productNames[] = $prodName;
        // Collect PhotoPath values
        $photoPaths[] = $product_detail[0]["PhotoPath"];
        
        // Check product code for specific codes
        if (in_array($product_detail[0]["ProductCode"], array("MN-SG020", "MN-SR020", "MN-SG020"))) {
            $hasShilajit = true;
        }
    }
}

if(empty($productNames)){
    $productNames[] = "Sample Product";
}

$photoPathStr = !empty($photoPaths) ? implode(", ", $photoPaths) : "";

// Set dimensions and weight based on product condition
if ($hasShilajit) {
    $length = 10; 
    $breadth = 10; 
    $height = 17; 
    $weight = 150;
} else {
    $length = 8; 
    $breadth = 8; 
    $height = 26; 
    $weight = 1180;
}

// --------------------------------------
// 4. Prepare values for Delhivery API Payload and make API call

$customerName = $Name;                     // Customer name
$orderCode = $order_master["OrderId"];     // Order ID
$phone = $MobileNo;                        // Customer phone
$paymentMode = $order_master["PaymentType"]; // Payment mode (COD/Prepaid)
$orderAmount = $order_master["Amount"];    // Total order amount
$codAmount = ($paymentMode == "COD") ? $orderAmount : 0;
if ($order_master["PaymentType"] == "Online") {
    $paymentMode = "Prepaid";
}

$apiKey = '6fed62f581611c5f1778bc2a774dc341bc094e91';

// IMPORTANT: Use staging environment for testing to avoid real charges
$isTestMode = true; // Set to false only when ready for production
$isMockMode = true; // Set to false to use real Delhivery API (staging has bugs)

if ($isMockMode) {
    // Mock mode - simulate successful response for testing
    $mockWaybill = "MOCK" . time() . rand(100, 999);

    // Update order status and waybill in database for realistic testing
    $stmt = $mysqli->prepare("UPDATE order_master SET OrderStatus = 'Created', Waybill = ? WHERE OrderId = ?");
    $stmt->bind_param("ss", $mockWaybill, $orderId);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        "success" => true,
        "waybill" => $mockWaybill,
        "message" => "Order created successfully! (Mock Mode - No real shipment created)",
        "mock_mode" => true,
        "environment" => "simulation",
        "customer_name" => $customerName,
        "order_id" => $orderCode,
        "amount" => $orderAmount
    ]);
    exit;
}

$url = $isTestMode
    ? "https://staging-express.delhivery.com/api/cmu/create.json"  // TEST - No charges (but has bugs)
    : "https://track.delhivery.com/api/cmu/create.json";          // PRODUCTION - Real charges

$orderPayload = [
    "pickup_location" => [
        "name"    => "My Nutrify",
        "city"    => "Sangli",
        "pin"     => "416416",
        "phone"   => "9834243754",
        "address" => "55 North Shivaji Nagar, Near Apta Police Chowk, Sangli - 416416"
    ],
    "shipments" => [
        [
            "name"           => $customerName,
            "order"          => $orderCode,
            "products_desc"  => implode(", ", $productNames),
            "amount"         => $orderAmount,
            "cod_amount"     => $codAmount,
            "quantity"       => $totalQuantity,
            "payment_mode"   => $paymentMode,
            "phone"          => $phone,
            "add"            => $ShipAddress,
            "city"           => $City,
            "pin"            => $Pincode,
            "state"          => $State,
            "shipment_length"         => $length,
            "shipment_width"        => $breadth,
            "shipment_height"         => $height,
            "weight"         => $weight,
            "photo"          => $photoPathStr,
            "volume"         => $length * $breadth * $height,
            "package_type"   => "BOX",
            "delivery_type"  => "Express"
        ]
    ]
];

$postData = http_build_query([
    "format" => "json",
    "data"   => json_encode($orderPayload)
]);

$headers = [
    "Authorization: Token $apiKey",
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
    echo json_encode(["success" => false, "message" => "Error calling Delhivery API: " . $error_msg]);
    exit;
}

curl_close($ch);

// --------------------------------------
// 5. Process Delhivery's response and output JSON result

$decodedResponse = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON from Delhivery"]);
    exit;
}

if (isset($decodedResponse['packages'][0]['status']) && strtolower($decodedResponse['packages'][0]['status']) === 'success') {
    $waybill = $decodedResponse['packages'][0]['waybill'] ?? null;
    
    
    
    	$stmt = $mysqli->prepare("update order_master set OrderStatus = 'Created', Waybill = ? where OrderId= ? ");
					$stmt->bind_param("ss",$waybill,$orderId);
					$stmt->execute();
					$stmt->close();
    
    
    echo json_encode([
        "success" => true,
        "waybill" => $waybill,
        "message" => "Order created successfully!"
    ]);
} else {
    $error_message = $decodedResponse['packages'][0]['remarks'] ?? 'Unknown error from Delhivery';
    echo json_encode([
        "success" => false,
        "message" => $error_message
    ]);
}
?>

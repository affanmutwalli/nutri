<?php
/**
 * Debug Payment Issue
 * This script helps debug the payment initiation problem
 */

session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Razorpay at top level
if (file_exists('razorpay/Razorpay.php')) {
    require_once('razorpay/Razorpay.php');
}
use Razorpay\Api\Api;

echo "<h2>Payment Debug Information</h2>";

// Check 1: Session Status
echo "<h3>1. Session Status</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Customer ID: " . ($_SESSION['CustomerId'] ?? 'Not set') . "<br>";
echo "Cart Items: " . (isset($_SESSION['cart']) ? count($_SESSION['cart']) : 'No cart') . "<br>";

// Check 2: Database Connection
echo "<h3>2. Database Connection</h3>";
try {
    include('database/dbconnection.php');
    $obj = new main();
    $connection = $obj->connection();
    if ($connection) {
        echo "✅ Database connection successful<br>";
        
        // Check tables
        $tables = ['order_master', 'order_details', 'customer_master'];
        foreach ($tables as $table) {
            $result = $connection->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' missing<br>";
            }
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Check 3: Razorpay SDK
echo "<h3>3. Razorpay SDK</h3>";
try {
    if (file_exists('razorpay/Razorpay.php')) {
        echo "✅ Razorpay SDK file found<br>";

        if (class_exists('Razorpay\Api\Api')) {
            echo "✅ Razorpay API class available<br>";
        } else {
            echo "❌ Razorpay API class not found<br>";
        }
    } else {
        echo "❌ Razorpay SDK file not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Razorpay SDK error: " . $e->getMessage() . "<br>";
}

// Check 4: Razorpay Configuration
echo "<h3>4. Razorpay Configuration</h3>";
if (file_exists('exe_files/razorpay_config.php')) {
    include('exe_files/razorpay_config.php');
    echo "✅ Config file found<br>";
    echo "Key ID defined: " . (defined('RAZORPAY_KEY_ID') ? 'Yes' : 'No') . "<br>";
    echo "Key Secret defined: " . (defined('RAZORPAY_KEY_SECRET') ? 'Yes' : 'No') . "<br>";
    
    if (defined('RAZORPAY_KEY_ID')) {
        echo "Key ID starts with: " . substr(RAZORPAY_KEY_ID, 0, 8) . "...<br>";
        echo "Environment: " . (strpos(RAZORPAY_KEY_ID, 'rzp_test_') === 0 ? 'Test' : 'Live') . "<br>";
    }
} else {
    echo "❌ Razorpay config file not found<br>";
}

// Check 5: Test API Call
echo "<h3>5. Test Razorpay API Call</h3>";
try {
    if (defined('RAZORPAY_KEY_ID') && defined('RAZORPAY_KEY_SECRET') && class_exists('Razorpay\Api\Api')) {
        $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
        
        // Test with minimal order data
        $testOrderData = [
            'receipt' => 'test_' . time(),
            'amount' => 100, // ₹1 in paise
            'currency' => 'INR',
            'payment_capture' => 1
        ];
        
        echo "Attempting to create test order...<br>";
        $razorpayOrder = $api->order->create($testOrderData);
        echo "✅ Razorpay API working! Test order ID: " . $razorpayOrder['id'] . "<br>";
        
    } else {
        echo "❌ Razorpay credentials not properly configured<br>";
    }
} catch (\Razorpay\Api\Errors\BadRequestError $e) {
    echo "❌ Razorpay BadRequest Error: " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "❌ Razorpay API Error: " . $e->getMessage() . "<br>";
}

// Check 6: Sample Order Data
echo "<h3>6. Sample Order Data Test</h3>";
$sampleOrderData = [
    'name' => 'Test Customer',
    'email' => 'test@example.com',
    'phone' => '9876543210',
    'address' => '123 Test Street',
    'landmark' => 'Near Test Mall',
    'pincode' => '400001',
    'state' => 'Maharashtra',
    'city' => 'Mumbai',
    'final_total' => 500,
    'paymentMethod' => 'Online',
    'CustomerId' => $_SESSION['CustomerId'] ?? 1,
    'customerType' => 'Registered',
    'products' => [
        [
            'id' => '1',
            'name' => 'Test Product',
            'code' => 'TP001',
            'size' => 'Medium',
            'quantity' => '1',
            'offer_price' => '500'
        ]
    ]
];

echo "Sample order data prepared:<br>";
echo "<pre>" . json_encode($sampleOrderData, JSON_PRETTY_PRINT) . "</pre>";

echo "<h3>7. Next Steps</h3>";
echo "1. Check browser console for JavaScript errors<br>";
echo "2. Try placing an order and check the console logs<br>";
echo "3. Check PHP error logs in your server<br>";
echo "4. If using live keys, try with test keys first<br>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
h3 { color: #666; margin-top: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>

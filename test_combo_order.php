<?php
/**
 * Test script to directly test combo order placement
 */
session_start();

// Set up test session data
$_SESSION['CustomerId'] = 1;
$_SESSION['CustomerName'] = 'Test User';
$_SESSION['Email'] = 'test@example.com';

echo "<h2>🧪 Combo Order Placement Test</h2>";

// Prepare JSON data for COD order (matching backend expectations)
$test_data = array(
    'name' => 'Test Customer',
    'email' => 'test@example.com',
    'phone' => '9876543210',
    'address' => 'Guruvar Peth New address, Near Priyadarshini Hotel, Sangli, Maharashtra - 416410',
    'final_total' => 628.20,
    'combo' => array(
        'combo_id' => 'COMBO_14_6',
        'quantity' => 1
    ),
    'CustomerId' => 1
);

echo "<h3>Test Data (JSON format for backend):</h3>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

echo "<h3>Testing COD Order Placement:</h3>";

// Simulate the JSON input that the backend expects
$json_input = json_encode($test_data);
echo "<h4>📤 JSON Input:</h4>";
echo "<pre>" . htmlspecialchars($json_input) . "</pre>";

// Create a temporary file to simulate php://input
$temp_file = tempnam(sys_get_temp_dir(), 'combo_test_');
file_put_contents($temp_file, $json_input);

// Mock php://input by temporarily replacing it
$original_input = 'php://input';

ob_start();
try {
    // Temporarily override php://input for testing
    $GLOBALS['test_json_input'] = $json_input;

    // Include a modified version that uses our test data
    $cod_content = file_get_contents('exe_files/combo_place_order_cod.php');
    $cod_content = str_replace('file_get_contents(\'php://input\')', '$GLOBALS[\'test_json_input\']', $cod_content);

    eval('?>' . $cod_content);
    $output = ob_get_clean();
    
    echo "<h4>✅ COD Order Processing Result:</h4>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Try to decode as JSON
    $json_result = json_decode($output, true);
    if ($json_result) {
        echo "<h4>📋 Parsed Response:</h4>";
        echo "<ul>";
        echo "<li><strong>Status:</strong> " . ($json_result['success'] ? '✅ Success' : '❌ Failed') . "</li>";
        echo "<li><strong>Message:</strong> " . htmlspecialchars($json_result['message']) . "</li>";
        if (isset($json_result['order_id'])) {
            echo "<li><strong>Order ID:</strong> " . htmlspecialchars($json_result['order_id']) . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "<h4>❌ Error during processing:</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>Database Check:</h3>";

try {
    include('database/dbconnection.php');
    $obj = new main();
    $mysqli = $obj->connection();
    
    // Check recent combo orders
    $recent_orders_query = "SELECT * FROM order_master WHERE OrderId LIKE 'CB%' ORDER BY CreatedAt DESC LIMIT 5";
    $recent_orders_result = $mysqli->query($recent_orders_query);
    
    if ($recent_orders_result && $recent_orders_result->num_rows > 0) {
        echo "<h4>📦 Recent Combo Orders:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Customer ID</th><th>Amount</th><th>Status</th><th>Payment Status</th><th>Created At</th></tr>";
        
        while ($row = $recent_orders_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['OrderId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CustomerId']) . "</td>";
            echo "<td>₹" . htmlspecialchars($row['Amount']) . "</td>";
            echo "<td>" . htmlspecialchars($row['OrderStatus']) . "</td>";
            echo "<td>" . htmlspecialchars($row['PaymentStatus']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CreatedAt']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No combo orders found in database.</p>";
    }
    
    // Check combo order tracking
    $tracking_query = "SELECT * FROM combo_order_tracking ORDER BY created_at DESC LIMIT 5";
    $tracking_result = $mysqli->query($tracking_query);
    
    if ($tracking_result && $tracking_result->num_rows > 0) {
        echo "<h4>📊 Combo Order Tracking:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Order ID</th><th>Combo ID</th><th>Quantity</th><th>Total Amount</th><th>Created At</th></tr>";
        
        while ($row = $tracking_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['combo_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
            echo "<td>₹" . htmlspecialchars($row['total_amount']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No combo order tracking records found.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If the order was successful, check the order management system</li>";
echo "<li>Test online payment method as well</li>";
echo "<li>Verify that order details are correctly stored</li>";
echo "<li>Test the complete end-to-end flow from combo creation to order completion</li>";
echo "</ol>";
?>

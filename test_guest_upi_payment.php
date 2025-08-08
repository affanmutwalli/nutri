<?php
// Test file to verify guest UPI payment functionality
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Guest UPI Payment Fix Verification</h2>";
echo "<hr>";

// Test 1: Check if required files exist
echo "<h3>📁 File Verification</h3>";
$files_to_check = [
    'exe_files/rcus_place_order_online_guest.php' => 'Guest online payment handler',
    'exe_files/razorpay_callback_bulletproof.php' => 'Payment callback handler',
    'checkout.php' => 'Main checkout page'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description ($file) - Found</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - Missing</p>";
    }
}

echo "<hr>";

// Test 2: Check database structure for guest orders
echo "<h3>🗄️ Database Structure Check</h3>";
try {
    $mysqli = $obj->connection();
    
    // Check order_master table for guest columns
    $result = $mysqli->query("DESCRIBE order_master");
    $guestColumns = ['GuestName', 'GuestEmail', 'GuestPhone'];
    $foundColumns = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (in_array($row['Field'], $guestColumns)) {
                $foundColumns[] = $row['Field'];
            }
        }
        
        foreach ($guestColumns as $column) {
            if (in_array($column, $foundColumns)) {
                echo "<p style='color: green;'>✅ $column column exists in order_master</p>";
            } else {
                echo "<p style='color: red;'>❌ $column column missing in order_master</p>";
            }
        }
    }
    
    // Check pending_orders table
    $pendingTableCheck = $mysqli->query("SHOW TABLES LIKE 'pending_orders'");
    if ($pendingTableCheck && $pendingTableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ pending_orders table exists</p>";
    } else {
        echo "<p style='color: red;'>❌ pending_orders table missing</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 3: Check recent guest orders
echo "<h3>📊 Recent Guest Orders</h3>";
try {
    $FieldNames = array("OrderId","CustomerType","Amount","PaymentStatus","PaymentType","GuestName","GuestEmail","GuestPhone","CreatedAt");
    $ParamArray = array();
    $Fields = implode(",",$FieldNames);
    $guest_orders = $obj->MysqliSelect1("Select ".$Fields." from order_master WHERE CustomerType = 'Guest' ORDER BY CreatedAt DESC LIMIT 10",$FieldNames,"s",$ParamArray);
    
    if ($guest_orders && count($guest_orders) > 0) {
        echo "<p style='color: green;'>✅ Found " . count($guest_orders) . " recent guest order(s)</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>Order ID</th><th>Guest Name</th><th>Amount</th><th>Payment Type</th><th>Payment Status</th><th>Created</th>";
        echo "</tr>";
        
        foreach ($guest_orders as $order) {
            $paymentStatusColor = $order["PaymentStatus"] === 'Paid' ? 'green' : ($order["PaymentStatus"] === 'Pending' ? 'orange' : 'red');
            echo "<tr>";
            echo "<td>" . htmlspecialchars($order["OrderId"]) . "</td>";
            echo "<td>" . htmlspecialchars($order["GuestName"] ?? 'N/A') . "</td>";
            echo "<td>₹" . number_format($order["Amount"], 2) . "</td>";
            echo "<td>" . htmlspecialchars($order["PaymentType"]) . "</td>";
            echo "<td style='color: $paymentStatusColor; font-weight: bold;'>" . htmlspecialchars($order["PaymentStatus"]) . "</td>";
            echo "<td>" . htmlspecialchars($order["CreatedAt"]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No guest orders found yet</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error retrieving guest orders: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 4: Problem Analysis and Solution
echo "<h3>🔧 Problem Analysis & Solution</h3>";
echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545; margin: 10px 0;'>";
echo "<h4>❌ Original Problem:</h4>";
echo "<p><strong>Error:</strong> \"Order processing failed: Missing required field: CustomerId\"</p>";
echo "<p><strong>Cause:</strong> Guest checkout was trying to use registered user payment handler which expects CustomerId</p>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745; margin: 10px 0;'>";
echo "<h4>✅ Solution Applied:</h4>";
echo "<ol>";
echo "<li><strong>Created Guest Payment Handler:</strong> <code>rcus_place_order_online_guest.php</code></li>";
echo "<li><strong>Updated Checkout Logic:</strong> Routes guest users to guest-specific payment handler</li>";
echo "<li><strong>Enhanced Callback Handler:</strong> Updated to handle guest order creation with guest fields</li>";
echo "<li><strong>Database Support:</strong> Uses CustomerId = 0 for guest orders with guest information fields</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";

// Test 5: How the Fix Works
echo "<h3>⚙️ How the Fix Works</h3>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; border-left: 4px solid #2196F3;'>";
echo "<h4>Guest UPI Payment Flow:</h4>";
echo "<ol>";
echo "<li><strong>Guest selects UPI/Online Payment</strong> on checkout page</li>";
echo "<li><strong>System detects guest mode</strong> and routes to <code>rcus_place_order_online_guest.php</code></li>";
echo "<li><strong>Guest handler creates Razorpay order</strong> with guest information (no CustomerId required)</li>";
echo "<li><strong>Order data stored in pending_orders</strong> table with CustomerId = 0</li>";
echo "<li><strong>User completes payment</strong> via Razorpay UPI interface</li>";
echo "<li><strong>Payment callback</strong> creates final order with guest fields populated</li>";
echo "<li><strong>Order confirmation</strong> sent to guest email</li>";
echo "</ol>";

echo "<h4>Key Differences from Registered Users:</h4>";
echo "<ul>";
echo "<li>✅ Uses CustomerId = 0 instead of actual customer ID</li>";
echo "<li>✅ Stores guest info in GuestName, GuestEmail, GuestPhone fields</li>";
echo "<li>✅ CustomerType = 'Guest' for proper identification</li>";
echo "<li>✅ No account creation or login required</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";

// Test 6: Testing Instructions
echo "<h3>🧪 Testing Instructions</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>";
echo "<h4>To Test Guest UPI Payment:</h4>";
echo "<ol>";
echo "<li><strong>Go to Checkout:</strong> <a href='checkout.php' target='_blank'>checkout.php</a></li>";
echo "<li><strong>Select Guest Checkout:</strong> Choose 'Continue as Guest' option</li>";
echo "<li><strong>Fill Guest Details:</strong> Enter name, email, phone, address</li>";
echo "<li><strong>Add Products:</strong> Make sure you have items in cart</li>";
echo "<li><strong>Select Online Payment:</strong> Choose UPI/Online Payment option</li>";
echo "<li><strong>Place Order:</strong> Click 'Place Order' button</li>";
echo "<li><strong>Complete Payment:</strong> Use Razorpay test UPI or card</li>";
echo "<li><strong>Verify Order:</strong> Check if order appears in admin panel</li>";
echo "</ol>";

echo "<h4>Expected Results:</h4>";
echo "<ul>";
echo "<li>✅ No 'Missing CustomerId' error</li>";
echo "<li>✅ Razorpay payment interface opens</li>";
echo "<li>✅ Payment completes successfully</li>";
echo "<li>✅ Order created with guest information</li>";
echo "<li>✅ Confirmation email sent to guest</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";

// Test 7: Quick Actions
echo "<h3>🎯 Quick Actions</h3>";
echo "<p>";
echo "<a href='checkout.php' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Test Guest Checkout</a>";
echo "<a href='cms/orders.php' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>View Orders</a>";
echo "<a href='check_pending_orders_table.php' target='_blank' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Check Pending Orders</a>";
echo "</p>";

echo "<hr>";
echo "<p><em>Guest UPI payment fix verification completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🚨 Emergency Rewards Fix</h2>";

require_once 'database/dbconnection.php';
$obj = new main();
$mysqli = $obj->connection();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'create_customer_record':
            echo "<h3>Creating Customer Points Record...</h3>";
            
            $customerId = 1; // Test customer
            
            // Check if record exists
            $checkQuery = "SELECT * FROM customer_points WHERE customer_id = ?";
            $checkStmt = $mysqli->prepare($checkQuery);
            $checkStmt->bind_param("i", $customerId);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<p style='color: orange;'>⚠️ Customer record already exists</p>";
            } else {
                // Create record
                $insertQuery = "INSERT INTO customer_points (customer_id, total_points, lifetime_points, tier_level, referral_code) VALUES (?, 0, 0, 'Bronze', ?)";
                $referralCode = 'REF' . $customerId . time();
                $insertStmt = $mysqli->prepare($insertQuery);
                $insertStmt->bind_param("is", $customerId, $referralCode);
                
                if ($insertStmt->execute()) {
                    echo "<p style='color: green;'>✅ Customer points record created!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to create record: " . $insertStmt->error . "</p>";
                }
            }
            break;
            
        case 'test_manual_points':
            echo "<h3>Testing Manual Points Award...</h3>";
            
            $customerId = 1;
            $orderId = 'MANUAL_FIX_' . time();
            $points = 8;
            $description = "Manual test points for ₹299 order";
            
            try {
                $mysqli->begin_transaction();
                
                // Ensure customer record exists
                $customerCheck = "SELECT customer_id FROM customer_points WHERE customer_id = ?";
                $checkStmt = $mysqli->prepare($customerCheck);
                $checkStmt->bind_param("i", $customerId);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                
                if ($checkResult->num_rows == 0) {
                    // Create customer record
                    $createQuery = "INSERT INTO customer_points (customer_id, total_points, lifetime_points, tier_level) VALUES (?, 0, 0, 'Bronze')";
                    $createStmt = $mysqli->prepare($createQuery);
                    $createStmt->bind_param("i", $customerId);
                    $createStmt->execute();
                    echo "<p>✅ Created customer record</p>";
                }
                
                // Add transaction
                $transQuery = "INSERT INTO points_transactions (customer_id, transaction_type, points, description, order_id) VALUES (?, 'earned', ?, ?, ?)";
                $transStmt = $mysqli->prepare($transQuery);
                $transStmt->bind_param("iiss", $customerId, $points, $description, $orderId);
                $transStmt->execute();
                echo "<p>✅ Added points transaction</p>";
                
                // Update customer points
                $updateQuery = "UPDATE customer_points SET total_points = total_points + ?, lifetime_points = lifetime_points + ? WHERE customer_id = ?";
                $updateStmt = $mysqli->prepare($updateQuery);
                $updateStmt->bind_param("iii", $points, $points, $customerId);
                $updateStmt->execute();
                echo "<p>✅ Updated customer points</p>";
                
                $mysqli->commit();
                echo "<p style='color: green;'>🎉 Manual points award successful!</p>";
                
            } catch (Exception $e) {
                $mysqli->rollback();
                echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
            }
            break;
            
        case 'fix_rewards_system':
            echo "<h3>Fixing RewardsSystem Class...</h3>";
            
            // Test the RewardsSystem with debugging
            try {
                include_once 'includes/RewardsSystem.php';
                $rewards = new RewardsSystem();
                
                $customerId = 1;
                $orderId = 'FIX_TEST_' . time();
                $orderAmount = 299;
                
                echo "<p>Testing with: Customer $customerId, Order $orderId, Amount ₹$orderAmount</p>";
                
                $result = $rewards->awardOrderPoints($customerId, $orderId, $orderAmount);
                
                echo "<p><strong>Result:</strong> " . var_export($result, true) . "</p>";
                
                if ($result !== false && $result > 0) {
                    echo "<p style='color: green;'>✅ RewardsSystem is working! Awarded $result points</p>";
                } else {
                    echo "<p style='color: red;'>❌ RewardsSystem still has issues</p>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ RewardsSystem error: " . $e->getMessage() . "</p>";
            }
            break;
    }
    
    echo "<br><p><a href='fix_rewards_emergency.php'>Back to Fix Menu</a></p>";
    
} else {
    // Show fix options
    echo "<h3>🛠️ Available Fixes:</h3>";
    
    echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>Step 1: Create Customer Record</h4>";
    echo "<p>Create the missing customer_points record for customer ID 1</p>";
    echo "<a href='?action=create_customer_record' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Create Customer Record</a>";
    echo "</div>";
    
    echo "<div style='background-color: #d1ecf1; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>Step 2: Test Manual Points</h4>";
    echo "<p>Manually award points to test database operations</p>";
    echo "<a href='?action=test_manual_points' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Manual Points</a>";
    echo "</div>";
    
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>Step 3: Test RewardsSystem</h4>";
    echo "<p>Test the RewardsSystem class after fixes</p>";
    echo "<a href='?action=fix_rewards_system' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test RewardsSystem</a>";
    echo "</div>";
    
    // Show current status
    echo "<h3>📊 Current Status:</h3>";
    
    // Check customer record
    $customerCheck = $mysqli->query("SELECT * FROM customer_points WHERE customer_id = 1");
    if ($customerCheck && $customerCheck->num_rows > 0) {
        echo "<p>✅ Customer points record exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Customer points record missing</p>";
    }
    
    // Check points config
    $configCheck = $mysqli->query("SELECT * FROM points_config");
    if ($configCheck && $configCheck->num_rows > 0) {
        echo "<p>✅ Points configuration exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Points configuration missing</p>";
    }
    
    // Check recent transactions
    $transCheck = $mysqli->query("SELECT COUNT(*) as count FROM points_transactions WHERE customer_id = 1");
    if ($transCheck) {
        $count = $transCheck->fetch_assoc()['count'];
        echo "<p>📊 Customer has $count points transactions</p>";
    }
}

echo "<br><p><a href='test_checkout_popup.html'>Test Checkout</a> | <a href='debug_rewards.php'>Debug Rewards</a></p>";
?>

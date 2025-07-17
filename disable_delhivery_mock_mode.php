<?php
// Disable Delhivery Mock Mode for Real Testing
require_once 'includes/db_connect.php';

echo "<h2>🔧 Disabling Delhivery Mock Mode</h2>";

try {
    // Disable mock mode in shipping_config table (used by DeliveryManager)
    $mockModeQuery = "UPDATE shipping_config SET config_value = '0' WHERE config_key = 'delhivery_mock_mode'";
    
    if (mysqli_query($mysqli, $mockModeQuery)) {
        echo "<p style='color: green;'>✅ Mock mode disabled successfully in shipping_config!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to disable mock mode: " . mysqli_error($mysqli) . "</p>";
    }
    
    // Also disable test mode to use production environment
    $testModeQuery = "UPDATE shipping_config SET config_value = '0' WHERE config_key = 'delhivery_test_mode'";
    
    if (mysqli_query($mysqli, $testModeQuery)) {
        echo "<p style='color: green;'>✅ Test mode disabled successfully in shipping_config!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to disable test mode: " . mysqli_error($mysqli) . "</p>";
    }
    
    // Also disable in delivery_config for consistency
    $mockModeQuery2 = "UPDATE delivery_config SET config_value = '0' WHERE config_key = 'delhivery_mock_mode'";
    mysqli_query($mysqli, $mockModeQuery2);
    
    $testModeQuery2 = "UPDATE delivery_config SET config_value = '0' WHERE config_key = 'delhivery_test_mode'";
    mysqli_query($mysqli, $testModeQuery2);
    
    // Show current settings from both tables
    echo "<h3>📋 Current Delhivery Settings:</h3>";
    
    echo "<h4>shipping_config table (used by DeliveryManager):</h4>";
    $settingsQuery1 = "SELECT config_key, config_value FROM shipping_config WHERE config_key LIKE 'delhivery_%'";
    $result1 = mysqli_query($mysqli, $settingsQuery1);
    
    if ($result1) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th style='padding: 8px;'>Setting</th><th style='padding: 8px;'>Value</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result1)) {
            $value = $row['config_value'];
            if ($row['config_key'] === 'delhivery_api_key') {
                $value = substr($value, 0, 10) . '...'; // Hide API key
            }
            echo "<tr><td style='padding: 8px;'>{$row['config_key']}</td><td style='padding: 8px;'>$value</td></tr>";
        }
        
        echo "</table>";
    }
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>⚠️ Mock Mode Disabled - Real API Active!</h3>";
    echo "<p><strong>What this means:</strong></p>";
    echo "<ul>";
    echo "<li>🔄 Orders will now use real Delhivery API calls</li>";
    echo "<li>💰 Real charges will be incurred for each shipment</li>";
    echo "<li>📦 Real waybill numbers will be generated</li>";
    echo "<li>🚚 Actual shipments will be created with Delhivery</li>";
    echo "<li>📱 Customer notifications will contain real tracking info</li>";
    echo "</ul>";
    echo "<p><strong>⚠️ Important:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure your Delhivery account has sufficient balance</li>";
    echo "<li>Each shipment will cost real money (₹30-80 typically)</li>";
    echo "<li>Only process orders you actually want to ship</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p><a href='process_all_unshipped_orders.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>⚠️ Process Orders (REAL API)</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

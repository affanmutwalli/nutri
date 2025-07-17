<?php
// Enable Delhivery Mock Mode to avoid balance issues
require_once 'includes/db_connect.php';

echo "<h2>🔧 Enabling Delhivery Mock Mode</h2>";

try {
    // Enable mock mode in shipping_config table (used by DeliveryManager)
    $mockModeQuery = "INSERT INTO shipping_config (config_key, config_value)
                      VALUES ('delhivery_mock_mode', '1')
                      ON DUPLICATE KEY UPDATE config_value = '1'";

    if (mysqli_query($mysqli, $mockModeQuery)) {
        echo "<p style='color: green;'>✅ Mock mode enabled successfully in shipping_config!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to enable mock mode: " . mysqli_error($mysqli) . "</p>";
    }

    // Also enable test mode to use staging environment
    $testModeQuery = "INSERT INTO shipping_config (config_key, config_value)
                      VALUES ('delhivery_test_mode', '1')
                      ON DUPLICATE KEY UPDATE config_value = '1'";

    if (mysqli_query($mysqli, $testModeQuery)) {
        echo "<p style='color: green;'>✅ Test mode enabled successfully in shipping_config!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to enable test mode: " . mysqli_error($mysqli) . "</p>";
    }

    // Also add to delivery_config for consistency
    $mockModeQuery2 = "INSERT INTO delivery_config (config_key, config_value, created_at, updated_at)
                       VALUES ('delhivery_mock_mode', '1', NOW(), NOW())
                       ON DUPLICATE KEY UPDATE config_value = '1', updated_at = NOW()";
    mysqli_query($mysqli, $mockModeQuery2);

    $testModeQuery2 = "INSERT INTO delivery_config (config_key, config_value, created_at, updated_at)
                       VALUES ('delhivery_test_mode', '1', NOW(), NOW())
                       ON DUPLICATE KEY UPDATE config_value = '1', updated_at = NOW()";
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

    echo "<h4>delivery_config table:</h4>";
    $settingsQuery2 = "SELECT config_key, config_value FROM delivery_config WHERE config_key LIKE 'delhivery_%'";
    $result2 = mysqli_query($mysqli, $settingsQuery2);

    if ($result2) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th style='padding: 8px;'>Setting</th><th style='padding: 8px;'>Value</th></tr>";

        while ($row = mysqli_fetch_assoc($result2)) {
            $value = $row['config_value'];
            if ($row['config_key'] === 'delhivery_api_key') {
                $value = substr($value, 0, 10) . '...'; // Hide API key
            }
            echo "<tr><td style='padding: 8px;'>{$row['config_key']}</td><td style='padding: 8px;'>$value</td></tr>";
        }

        echo "</table>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Mock Mode Enabled!</h3>";
    echo "<p><strong>What this means:</strong></p>";
    echo "<ul>";
    echo "<li>🔄 Orders will be processed without real Delhivery API calls</li>";
    echo "<li>💰 No charges will be incurred</li>";
    echo "<li>📦 Mock waybill numbers will be generated</li>";
    echo "<li>✅ Orders will be marked as shipped successfully</li>";
    echo "<li>📱 Customer notifications will still be sent</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Run the order processing again</li>";
    echo "<li>All orders should process successfully</li>";
    echo "<li>When ready for production, disable mock mode</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<p><a href='process_all_unshipped_orders.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Process Orders Now</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

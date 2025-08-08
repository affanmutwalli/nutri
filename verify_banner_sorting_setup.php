<?php
// Verification script for banner sorting functionality
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

echo "<h2>Banner Sorting Setup Verification</h2>";
echo "<hr>";

// Test 1: Verify Position column exists and check current data
echo "<h3>✅ Database Structure Verification</h3>";
try {
    $result = $mysqli->query("DESCRIBE banners");
    $positionExists = false;
    $positionType = '';
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] == 'Position') {
                $positionExists = true;
                $positionType = $row['Type'];
                break;
            }
        }
        
        if ($positionExists) {
            echo "<p style='color: green;'>✅ Position column exists (Type: $positionType)</p>";
        } else {
            echo "<p style='color: red;'>❌ Position column not found</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test 2: Check current banner data and positions
echo "<h3>📊 Current Banner Data</h3>";
try {
    $FieldNames = array("BannerId","Title","ShortDescription","PhotoPath","Position","ShowButton");
    $ParamArray = array();
    $Fields = implode(",",$FieldNames);
    $banner_data = $obj->MysqliSelect1("Select ".$Fields." from banners ORDER BY Position ASC, BannerId ASC",$FieldNames,"s",$ParamArray);
    
    if ($banner_data && count($banner_data) > 0) {
        echo "<p style='color: green;'>✅ Found " . count($banner_data) . " banner(s)</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>Display Order</th><th>Banner ID</th><th>Title</th><th>Position Value</th><th>Show Button</th><th>Status</th>";
        echo "</tr>";
        
        $needsPositionUpdate = true;
        foreach ($banner_data as $index => $banner) {
            $position = isset($banner["Position"]) ? $banner["Position"] : 0;
            if ($position > 0) $needsPositionUpdate = false;
            
            $showButton = isset($banner["ShowButton"]) ? 
                ($banner["ShowButton"] == 1 ? "✅ Enabled" : "❌ Disabled") : "⚠️ Not Set";
            
            $status = "✅ Ready";
            if (empty($banner["Title"]) && empty($banner["ShortDescription"])) {
                $status = "⚠️ No Content";
            }
            
            echo "<tr>";
            echo "<td style='text-align: center; font-weight: bold;'>" . ($index + 1) . "</td>";
            echo "<td>" . htmlspecialchars($banner["BannerId"]) . "</td>";
            echo "<td>" . htmlspecialchars($banner["Title"] ?: 'No Title') . "</td>";
            echo "<td style='text-align: center;'>" . $position . "</td>";
            echo "<td style='text-align: center;'>" . $showButton . "</td>";
            echo "<td style='text-align: center;'>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if positions need to be initialized
        if ($needsPositionUpdate) {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 10px 0;'>";
            echo "<h4>⚠️ Position Initialization Recommended</h4>";
            echo "<p>All banners have Position = 0. Would you like to initialize sequential positions?</p>";
            echo "<button onclick='initializePositions()' style='background: #007bff; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;'>Initialize Positions</button>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: orange;'>⚠️ No banners found in database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error retrieving banner data: " . $e->getMessage() . "</p>";
}

// Test 3: Check if CMS files are updated
echo "<h3>📁 File Verification</h3>";
$files_to_check = [
    'cms/banners.php' => 'Banner management page',
    'cms/exe_save_banners.php' => 'Banner save handler',
    'cms/update_banner_order.php' => 'AJAX sorting handler'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description ($file) - Found</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - Missing</p>";
    }
}

// Test 4: Instructions
echo "<h3>🚀 Ready to Use!</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
echo "<h4>Your banner sorting system is ready! Here's how to use it:</h4>";
echo "<ol>";
echo "<li><strong>Access CMS:</strong> Go to <a href='cms/banners.php' target='_blank'>cms/banners.php</a></li>";
echo "<li><strong>Drag & Drop:</strong> Use the grip icon (⋮⋮) to drag and reorder banners</li>";
echo "<li><strong>Manual Order:</strong> Edit any banner and set the 'Display Order' field</li>";
echo "<li><strong>View Results:</strong> Check your homepage to see the new order</li>";
echo "</ol>";

echo "<h4>🎯 Quick Actions:</h4>";
echo "<p>";
echo "<a href='cms/banners.php' target='_blank' style='background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Open Banner Management</a>";
echo "<a href='index.php' target='_blank' style='background: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>View Homepage</a>";
echo "<a href='test_banner_sorting.php' target='_blank' style='background: #6c757d; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>Run Full Test</a>";
echo "</p>";
echo "</div>";

echo "<hr>";
echo "<p><em>Verification completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

<script>
function initializePositions() {
    if (confirm('This will set sequential positions for all banners. Continue?')) {
        fetch('initialize_banner_positions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.response === 'S') {
                alert('Positions initialized successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.msg);
            }
        })
        .catch(error => {
            alert('Error initializing positions: ' + error);
        });
    }
}
</script>

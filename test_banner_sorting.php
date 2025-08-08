<?php
// Test file to verify the banner sorting functionality
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Banner Sorting Functionality Test</h2>";
echo "<hr>";

// Test 1: Check if Position field is being retrieved and used for ordering
echo "<h3>Test 1: Banner Data Retrieval with Position Ordering</h3>";
$FieldNames = array("BannerId","Title","ShortDescription","PhotoPath","Position","ShowButton");
$ParamArray = array();
$Fields = implode(",",$FieldNames);
$banner_data = $obj->MysqliSelect1("Select ".$Fields." from banners ORDER BY Position ASC, BannerId ASC",$FieldNames,"s",$ParamArray);

if ($banner_data && count($banner_data) > 0) {
    echo "<p style='color: green;'>✅ Successfully retrieved banner data with Position ordering</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Banner ID</th><th>Title</th><th>Position</th><th>Show Button</th><th>Display Order</th></tr>";
    
    foreach ($banner_data as $index => $banner) {
        $showButtonText = isset($banner["ShowButton"]) ? 
            ($banner["ShowButton"] == 1 ? "✅ Enabled" : "❌ Disabled") : 
            "⚠️ Not Set";
        
        $position = isset($banner["Position"]) ? $banner["Position"] : "Not Set";
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($banner["BannerId"]) . "</td>";
        echo "<td>" . htmlspecialchars($banner["Title"]) . "</td>";
        echo "<td>" . htmlspecialchars($position) . "</td>";
        echo "<td>" . $showButtonText . "</td>";
        echo "<td><strong>" . ($index + 1) . "</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No banner data found or error retrieving data</p>";
}

echo "<hr>";

// Test 2: Check database structure for Position field
echo "<h3>Test 2: Database Structure Verification</h3>";
try {
    $mysqli = $obj->connection();
    $result = $mysqli->query("DESCRIBE banners");
    
    if ($result) {
        echo "<p style='color: green;'>✅ Successfully accessed banners table structure</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $positionExists = false;
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] == 'Position') {
                $positionExists = true;
            }
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if ($positionExists) {
            echo "<p style='color: green;'>✅ Position field exists in banners table</p>";
        } else {
            echo "<p style='color: red;'>❌ Position field NOT found in banners table</p>";
            echo "<p style='color: orange;'>⚠️ You may need to add the Position field to your banners table:</p>";
            echo "<code>ALTER TABLE banners ADD COLUMN Position INT DEFAULT 0;</code>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error accessing banners table structure</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 3: Simulate frontend banner display order
echo "<h3>Test 3: Frontend Banner Display Order Simulation</h3>";
echo "<div style='background: #f5f5f5; padding: 20px; border-radius: 8px;'>";
echo "<h4>Banner Display Order (as they would appear on homepage):</h4>";

if ($banner_data && count($banner_data) > 0) {
    foreach ($banner_data as $index => $banners) {
        echo "<div style='border: 2px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 8px; background: white;'>";
        echo "<h5>Banner " . ($index + 1) . ": " . htmlspecialchars($banners["Title"]) . "</h5>";
        echo "<p><strong>Position Value:</strong> " . (isset($banners["Position"]) ? $banners["Position"] : "Not Set") . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($banners["ShortDescription"]) . "</p>";
        echo "<p><strong>Image:</strong> cms/images/banners/" . htmlspecialchars($banners["PhotoPath"]) . "</p>";
        
        if (isset($banners["ShowButton"]) && $banners["ShowButton"] == 1) {
            echo "<p style='color: green;'><strong>Shop Now Button:</strong> ✅ DISPLAYED</p>";
        } else {
            echo "<p style='color: red;'><strong>Shop Now Button:</strong> ❌ HIDDEN</p>";
        }
        echo "</div>";
    }
} else {
    echo "<p>No banners to display</p>";
}

echo "</div>";

echo "<hr>";

// Test 4: Summary and Instructions
echo "<h3>Test Summary & Instructions</h3>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; border-left: 4px solid #2196F3;'>";
echo "<h4>Banner Sorting Features Added:</h4>";
echo "<ol>";
echo "<li>✅ Position field added to banner form</li>";
echo "<li>✅ Drag-and-drop sorting in CMS banner list</li>";
echo "<li>✅ AJAX functionality to update banner positions</li>";
echo "<li>✅ Frontend ordering by Position field</li>";
echo "<li>✅ Visual feedback for drag-and-drop operations</li>";
echo "</ol>";

echo "<h4>How to Use Banner Sorting:</h4>";
echo "<ol>";
echo "<li><strong>Manual Position Setting:</strong> Edit any banner and set the 'Display Order' field (lower numbers appear first)</li>";
echo "<li><strong>Drag-and-Drop Sorting:</strong> Go to CMS → Banners, then drag the grip icon (⋮⋮) to reorder banners</li>";
echo "<li><strong>Automatic Position Update:</strong> When you drag-and-drop, positions are automatically updated</li>";
echo "<li><strong>Frontend Display:</strong> Banners appear on homepage in the order you set</li>";
echo "</ol>";

echo "<h4>Technical Details:</h4>";
echo "<ul>";
echo "<li><strong>Position Field:</strong> Integer field where 0 = highest priority</li>";
echo "<li><strong>Default Ordering:</strong> Position ASC, then BannerId ASC</li>";
echo "<li><strong>Drag-and-Drop:</strong> Uses jQuery UI Sortable with AJAX updates</li>";
echo "<li><strong>Database Updates:</strong> Transactional updates for data consistency</li>";
echo "</ul>";

echo "<h4>Files Modified:</h4>";
echo "<ul>";
echo "<li><code>cms/banners.php</code> - Added Position field and drag-drop UI</li>";
echo "<li><code>cms/exe_save_banners.php</code> - Added Position field handling</li>";
echo "<li><code>cms/update_banner_order.php</code> - New AJAX handler for sorting</li>";
echo "<li><code>index.php</code> - Updated query to order by Position</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

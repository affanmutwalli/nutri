<?php
// Test file to verify the banner Shop Now button fix
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Banner Shop Now Button Fix Test</h2>";
echo "<hr>";

// Test 1: Check if ShowButton field is being retrieved
echo "<h3>Test 1: Banner Data Retrieval</h3>";
$FieldNames = array("BannerId","Title","ShortDescription","PhotoPath","Position","ShowButton");
$ParamArray = array();
$Fields = implode(",",$FieldNames);
$banner_data = $obj->MysqliSelect1("Select ".$Fields." from banners ",$FieldNames,"s",$ParamArray);

if ($banner_data && count($banner_data) > 0) {
    echo "<p style='color: green;'>✅ Successfully retrieved banner data with ShowButton field</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Banner ID</th><th>Title</th><th>Description</th><th>Photo Path</th><th>Show Button</th></tr>";
    
    foreach ($banner_data as $banner) {
        $showButtonText = isset($banner["ShowButton"]) ? 
            ($banner["ShowButton"] == 1 ? "✅ Enabled" : "❌ Disabled") : 
            "⚠️ Not Set";
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($banner["BannerId"]) . "</td>";
        echo "<td>" . htmlspecialchars($banner["Title"]) . "</td>";
        echo "<td>" . htmlspecialchars($banner["ShortDescription"]) . "</td>";
        echo "<td>" . htmlspecialchars($banner["PhotoPath"]) . "</td>";
        echo "<td>" . $showButtonText . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No banner data found or error retrieving data</p>";
}

echo "<hr>";

// Test 2: Simulate the hero slider logic
echo "<h3>Test 2: Hero Slider Logic Simulation</h3>";
echo "<div style='background: #f5f5f5; padding: 20px; border-radius: 8px;'>";
echo "<h4>Simulated Hero Slider Output:</h4>";

if ($banner_data && count($banner_data) > 0) {
    foreach ($banner_data as $banners) {
        echo "<div style='border: 2px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 8px; background: white;'>";
        echo "<h5>Banner: " . htmlspecialchars($banners["Title"]) . "</h5>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($banners["ShortDescription"]) . "</p>";
        echo "<p><strong>Image:</strong> cms/images/banners/" . htmlspecialchars($banners["PhotoPath"]) . "</p>";
        
        // This is the key logic that was fixed
        if (isset($banners["ShowButton"]) && $banners["ShowButton"] == 1) {
            echo "<p style='color: green;'><strong>Shop Now Button:</strong> ✅ DISPLAYED</p>";
            echo "<button style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Shop now</button>";
        } else {
            echo "<p style='color: red;'><strong>Shop Now Button:</strong> ❌ HIDDEN</p>";
            echo "<em>No button displayed for this banner</em>";
        }
        echo "</div>";
    }
} else {
    echo "<p>No banners to display</p>";
}

echo "</div>";

echo "<hr>";

// Test 3: Check database structure
echo "<h3>Test 3: Database Structure Verification</h3>";
try {
    $mysqli = $obj->connection();
    $result = $mysqli->query("DESCRIBE banners");
    
    if ($result) {
        echo "<p style='color: green;'>✅ Successfully accessed banners table structure</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $showButtonExists = false;
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] == 'ShowButton') {
                $showButtonExists = true;
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
        
        if ($showButtonExists) {
            echo "<p style='color: green;'>✅ ShowButton field exists in banners table</p>";
        } else {
            echo "<p style='color: red;'>❌ ShowButton field NOT found in banners table</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error accessing banners table structure</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 4: Summary
echo "<h3>Test Summary</h3>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; border-left: 4px solid #2196F3;'>";
echo "<h4>Fix Applied:</h4>";
echo "<ol>";
echo "<li>✅ Added 'ShowButton' field to the banner query in index.php</li>";
echo "<li>✅ Added conditional logic to display Shop Now button only when ShowButton = 1</li>";
echo "<li>✅ Maintained backward compatibility with existing banners</li>";
echo "</ol>";

echo "<h4>How to Test:</h4>";
echo "<ol>";
echo "<li>Go to CMS → Banners Management</li>";
echo "<li>Edit any banner and toggle the 'Show Shop Now Button' checkbox</li>";
echo "<li>Save the banner</li>";
echo "<li>Visit the homepage and check if the Shop Now button appears/disappears accordingly</li>";
echo "</ol>";

echo "<h4>Expected Behavior:</h4>";
echo "<ul>";
echo "<li>When ShowButton = 1 (checked): Shop Now button appears on the hero banner</li>";
echo "<li>When ShowButton = 0 (unchecked): Shop Now button is hidden on the hero banner</li>";
echo "<li>Banner image and text still display normally regardless of button setting</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

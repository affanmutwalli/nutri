<?php
// Test 6 Categories Limit
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🧪 Test 6 Sub-Categories Limit</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .info-box { background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .test-form { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
    select { width: 100%; height: 150px; padding: 10px; border: 2px solid #ddd; border-radius: 5px; }
    select:focus { border-color: #007bff; }
    option:checked { background-color: #007bff; color: white; }
    .counter { font-weight: bold; color: #007bff; }
</style>";

try {
    echo "<div class='success-box'>";
    echo "<h3>✅ Enhanced Multi-Select Features</h3>";
    echo "<ul>";
    echo "<li>✅ <strong>Size attribute:</strong> Shows 6 options at once</li>";
    echo "<li>✅ <strong>Height increased:</strong> 150px for better visibility</li>";
    echo "<li>✅ <strong>JavaScript limit:</strong> Prevents selecting more than 6</li>";
    echo "<li>✅ <strong>Visual feedback:</strong> Shows count (X/6 selected)</li>";
    echo "<li>✅ <strong>Enhanced styling:</strong> Better visual appearance</li>";
    echo "</ul>";
    echo "</div>";

    // Get all sub-categories for testing
    $FieldNames = array("SubCategoryId", "SubCategoryName");
    $ParamArray = array();
    $Fields = implode(",", $FieldNames);
    $all_sub_categories = $obj->MysqliSelect1("Select " . $Fields . " from sub_category", $FieldNames, "", $ParamArray);

    echo "<div class='test-form'>";
    echo "<h3>🎯 Test the Multi-Select (Try selecting more than 6)</h3>";
    echo "<form>";
    echo "<label for='testSelect'><span id='selectLabel'>Sub Categories (Select up to 6)</span></label><br><br>";
    echo "<select id='testSelect' name='SubCategoryId[]' multiple size='6'>";
    
    if (!empty($all_sub_categories)) {
        foreach ($all_sub_categories as $sub_category) {
            echo "<option value='" . htmlspecialchars($sub_category['SubCategoryId']) . "'>";
            echo htmlspecialchars($sub_category['SubCategoryName']);
            echo "</option>";
        }
    }
    
    echo "</select>";
    echo "<br><small>Hold Ctrl (Cmd on Mac) to select multiple. Try selecting more than 6!</small>";
    echo "</form>";
    echo "</div>";

    echo "<div class='info-box'>";
    echo "<h3>📋 What's Fixed:</h3>";
    echo "<ol>";
    echo "<li><strong>Size attribute:</strong> <code>size='6'</code> shows 6 options without scrolling</li>";
    echo "<li><strong>Height increased:</strong> <code>height: 150px</code> for better visibility</li>";
    echo "<li><strong>JavaScript validation:</strong> Prevents selecting more than 6 categories</li>";
    echo "<li><strong>User feedback:</strong> Shows alert when limit exceeded</li>";
    echo "<li><strong>Dynamic label:</strong> Updates to show selection count</li>";
    echo "</ol>";
    echo "</div>";

    echo "<div class='success-box'>";
    echo "<h3>🚀 Ready to Use!</h3>";
    echo "<p>Go to <a href='cms/products.php' target='_blank'>CMS Products</a> and try:</p>";
    echo "<ol>";
    echo "<li>Edit any existing product</li>";
    echo "<li>Try selecting multiple sub-categories (up to 6)</li>";
    echo "<li>Try selecting more than 6 - you'll get an alert</li>";
    echo "<li>Save the product</li>";
    echo "<li>Check frontend to see the product in multiple categories</li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<script>
// Test the same JavaScript logic here
document.getElementById('testSelect').addEventListener('change', function() {
    const selectedOptions = Array.from(this.selectedOptions);
    if (selectedOptions.length > 6) {
        // Remove the last selected option if more than 6 are selected
        selectedOptions[selectedOptions.length - 1].selected = false;
        alert('You can select maximum 6 sub-categories. First selected will be the primary category.');
    }
    
    // Update the label to show count
    const label = document.getElementById('selectLabel');
    const count = selectedOptions.length;
    if (count > 0) {
        label.innerHTML = `Sub Categories (<span class="counter">${count}/6 selected</span>)`;
    } else {
        label.textContent = 'Sub Categories (Select up to 6)';
    }
});
</script>

<?php
echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

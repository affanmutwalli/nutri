<?php
// Debug page to check blog status and visibility
include('database/dbconnection.php');
$obj = new main();
$obj->connection();

echo "<h2>Blog Debug Information</h2>";

// Get all blogs with their status
$FieldNames = array("BlogId", "BlogTitle", "BlogDate", "Description", "PhotoPath", "IsActive");
$ParamArray = array();
$Fields = implode(",", $FieldNames);
$all_blogs = $obj->MysqliSelect1(
    "SELECT " . $Fields . " FROM blogs_master ORDER BY BlogDate DESC",
    $FieldNames,
    "",
    $ParamArray
);

echo "<h3>All Blogs in Database:</h3>";
if (!empty($all_blogs)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visible to Users</th><th>Actions</th>";
    echo "</tr>";
    
    foreach ($all_blogs as $blog) {
        $status = $blog['IsActive'] === 'Y' ? 'Active' : 'Inactive';
        $visible = $blog['IsActive'] === 'Y' ? '✅ YES' : '❌ NO';
        $row_color = $blog['IsActive'] === 'Y' ? '#e8f5e8' : '#ffe8e8';
        
        echo "<tr style='background: $row_color;'>";
        echo "<td>" . $blog['BlogId'] . "</td>";
        echo "<td>" . htmlspecialchars($blog['BlogTitle']) . "</td>";
        echo "<td>" . date('d-m-Y', strtotime($blog['BlogDate'])) . "</td>";
        echo "<td><strong>" . $status . "</strong></td>";
        echo "<td><strong>" . $visible . "</strong></td>";
        echo "<td>";
        echo "<a href='blog_details.php?BlogId=" . $blog['BlogId'] . "' target='_blank' style='margin-right: 10px;'>View</a>";
        echo "<a href='cms/blogs.php?BlogId=" . $blog['BlogId'] . "' target='_blank'>Edit</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No blogs found in database.</p>";
}

// Count active vs inactive blogs
$active_count = 0;
$inactive_count = 0;
foreach ($all_blogs as $blog) {
    if ($blog['IsActive'] === 'Y') {
        $active_count++;
    } else {
        $inactive_count++;
    }
}

echo "<h3>Summary:</h3>";
echo "<ul>";
echo "<li><strong>Total Blogs:</strong> " . count($all_blogs) . "</li>";
echo "<li><strong>Active Blogs (Visible to Users):</strong> <span style='color: green;'>" . $active_count . "</span></li>";
echo "<li><strong>Inactive Blogs (Hidden from Users):</strong> <span style='color: red;'>" . $inactive_count . "</span></li>";
echo "</ul>";

echo "<h3>Test User-Facing Queries:</h3>";

// Test the user-facing query
$user_blogs = $obj->MysqliSelect1(
    "SELECT " . $Fields . " FROM blogs_master WHERE IsActive = 'Y' ORDER BY BlogDate DESC",
    $FieldNames,
    "",
    $ParamArray
);

echo "<p><strong>Blogs visible to users:</strong> " . count($user_blogs) . "</p>";
if (!empty($user_blogs)) {
    echo "<ul>";
    foreach ($user_blogs as $blog) {
        echo "<li>" . htmlspecialchars($blog['BlogTitle']) . " (ID: " . $blog['BlogId'] . ")</li>";
    }
    echo "</ul>";
}

echo "<h3>Quick Actions:</h3>";
echo "<p>";
echo "<a href='blogs.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>View User Blog Page</a>";
echo "<a href='cms/blogs.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Manage Blogs (CMS)</a>";
echo "</p>";

echo "<h3>How to Fix Blog Visibility:</h3>";
echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<ol>";
echo "<li>Go to <strong>CMS → Blogs</strong></li>";
echo "<li>Edit the blog you want to make visible</li>";
echo "<li>Check the <strong>'Is Active'</strong> checkbox</li>";
echo "<li>Click <strong>'Submit'</strong></li>";
echo "<li>The blog will now appear on the user-facing blog page</li>";
echo "</ol>";
echo "</div>";

echo "<p style='color: #666; font-size: 12px; margin-top: 30px;'>";
echo "Debug page created at: " . date('Y-m-d H:i:s') . "<br>";
echo "You can delete this file after debugging: blog_debug.php";
echo "</p>";
?>

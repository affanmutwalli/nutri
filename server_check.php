<?php
echo "<h1>🔧 Server Configuration Check</h1>";

echo "<h2>📁 File System Check:</h2>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

echo "<h2>📂 File Existence Check:</h2>";
$files_to_check = [
    'setup_documents_table.php',
    'test_upload.php',
    'cms/products.php',
    'cms/upload_product_document.php',
    'database/dbconnection.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    echo "<p><strong>$file:</strong> " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "</p>";
}

echo "<h2>🌐 URL Testing:</h2>";
$base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
echo "<p><strong>Base URL:</strong> $base_url</p>";

echo "<h3>📋 Working URLs:</h3>";
echo "<ul>";
echo "<li><a href='setup_documents_table.php' target='_blank'>Setup Database Table</a></li>";
echo "<li><a href='test_upload.php' target='_blank'>Test Upload</a></li>";
echo "<li><a href='cms/products.php' target='_blank'>CMS Products</a></li>";
echo "<li><a href='index.php' target='_blank'>Main Website</a></li>";
echo "</ul>";

echo "<h2>🔧 PHP Configuration:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Upload Max Filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Post Max Size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>File Uploads:</strong> " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</p>";

echo "<h2>📊 Database Connection Test:</h2>";
try {
    include('database/dbconnection.php');
    $obj = new main();
    $mysqli = $obj->connection();
    
    if ($mysqli) {
        echo "<p>✅ Database connection successful</p>";
        
        // Check if product_documents table exists
        $result = $mysqli->query("SHOW TABLES LIKE 'product_documents'");
        if ($result->num_rows > 0) {
            echo "<p>✅ product_documents table exists</p>";
        } else {
            echo "<p>❌ product_documents table missing - <a href='setup_documents_table.php'>Create it now</a></p>";
        }
        
        $mysqli->close();
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>✅ Next Steps:</h2>";
echo "<ol>";
echo "<li>If all files show as EXISTS, use the working URLs above</li>";
echo "<li>If database table is missing, click the setup link</li>";
echo "<li>Test the upload functionality using the test upload link</li>";
echo "</ol>";
?>

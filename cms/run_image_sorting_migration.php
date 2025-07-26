<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

sec_session_start();

if (login_check($mysqli) == true) {
    echo "<h2>Image Sorting Migration</h2>";
    echo "<p>Running migration to add sort_order field to model_images table...</p>";
    
    try {
        // Check if sort_order column already exists
        $result = $obj->mysqli->query("SHOW COLUMNS FROM model_images LIKE 'sort_order'");
        
        if ($result->num_rows > 0) {
            echo "<div style='color: orange;'>✓ sort_order column already exists. Skipping column creation.</div>";
        } else {
            // Add sort_order column
            $sql1 = "ALTER TABLE model_images ADD COLUMN sort_order INT DEFAULT 0 AFTER PhotoPath";
            if ($obj->mysqli->query($sql1)) {
                echo "<div style='color: green;'>✓ Added sort_order column successfully.</div>";
            } else {
                throw new Exception("Failed to add sort_order column: " . $obj->mysqli->error);
            }
        }
        
        // Check if index exists
        $result = $obj->mysqli->query("SHOW INDEX FROM model_images WHERE Key_name = 'idx_sort_order'");
        
        if ($result->num_rows > 0) {
            echo "<div style='color: orange;'>✓ Index idx_sort_order already exists. Skipping index creation.</div>";
        } else {
            // Create index
            $sql2 = "CREATE INDEX idx_sort_order ON model_images(ProductId, sort_order)";
            if ($obj->mysqli->query($sql2)) {
                echo "<div style='color: green;'>✓ Created index successfully.</div>";
            } else {
                throw new Exception("Failed to create index: " . $obj->mysqli->error);
            }
        }
        
        // Update existing records with sequential sort_order
        $sql3 = "UPDATE model_images m1
                JOIN (
                    SELECT ImageId, 
                           ROW_NUMBER() OVER (PARTITION BY ProductId ORDER BY ImageId) as new_sort_order
                    FROM model_images
                    WHERE sort_order = 0 OR sort_order IS NULL
                ) m2 ON m1.ImageId = m2.ImageId
                SET m1.sort_order = m2.new_sort_order
                WHERE m1.sort_order = 0 OR m1.sort_order IS NULL";
        
        if ($obj->mysqli->query($sql3)) {
            $affected_rows = $obj->mysqli->affected_rows;
            echo "<div style='color: green;'>✓ Updated $affected_rows existing records with sort_order.</div>";
        } else {
            throw new Exception("Failed to update existing records: " . $obj->mysqli->error);
        }
        
        // Verify the migration
        $result = $obj->mysqli->query("SELECT COUNT(*) as total FROM model_images");
        $row = $result->fetch_assoc();
        $total_images = $row['total'];
        
        $result = $obj->mysqli->query("SELECT COUNT(*) as sorted FROM model_images WHERE sort_order > 0");
        $row = $result->fetch_assoc();
        $sorted_images = $row['sorted'];
        
        echo "<br><h3>Migration Summary:</h3>";
        echo "<div style='color: blue;'>Total images in database: $total_images</div>";
        echo "<div style='color: blue;'>Images with sort_order: $sorted_images</div>";
        
        if ($total_images == $sorted_images) {
            echo "<div style='color: green; font-weight: bold;'>✓ Migration completed successfully!</div>";
            echo "<p><a href='add_images_model.php?ProductId=1' style='color: blue;'>Test the new functionality</a> (replace ProductId with a valid product ID)</p>";
        } else {
            echo "<div style='color: red;'>⚠ Some images may not have been updated properly.</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>✗ Error: " . $e->getMessage() . "</div>";
    }
    
} else {
    echo "Access denied. Please login first.";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
div { margin: 5px 0; padding: 5px; }
</style>

<?php
/**
 * Diagnostic Script - Check Coupon Table Structure
 */

include_once 'database/dbconnection.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Coupon Table Diagnostic</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #ec6504; border-bottom: 3px solid #ec6504; padding-bottom: 10px; }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f8f9fa; }
    .code { background: #f8f9fa; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔍 Coupon Table Diagnostic</h1>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<div class='success'>✅ Database connection successful</div>";
    
    // Check if enhanced_coupons table exists
    echo "<h2>📋 Table Existence Check</h2>";
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'enhanced_coupons'");
    if ($tableCheck->num_rows > 0) {
        echo "<div class='success'>✅ enhanced_coupons table exists</div>";
    } else {
        echo "<div class='error'>❌ enhanced_coupons table not found</div>";
        
        // Check for other coupon tables
        $allTables = $mysqli->query("SHOW TABLES LIKE '%coupon%'");
        if ($allTables->num_rows > 0) {
            echo "<div class='info'>Found these coupon-related tables:</div>";
            echo "<ul>";
            while ($table = $allTables->fetch_array()) {
                echo "<li class='code'>" . $table[0] . "</li>";
            }
            echo "</ul>";
        }
        exit();
    }
    
    // Show table structure
    echo "<h2>🏗️ Table Structure</h2>";
    $structure = $mysqli->query("DESCRIBE enhanced_coupons");
    if ($structure) {
        echo "<table>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $primaryKey = '';
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>" . $row['Field'] . "</strong></td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?: 'NULL') . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
            
            if ($row['Key'] === 'PRI') {
                $primaryKey = $row['Field'];
            }
        }
        echo "</table>";
        
        if ($primaryKey) {
            echo "<div class='success'>✅ Primary key detected: <span class='code'>$primaryKey</span></div>";
        } else {
            echo "<div class='error'>❌ No primary key found</div>";
        }
    }
    
    // Check for IsShining column
    echo "<h2>✨ Shining Feature Check</h2>";
    $columnCheck = $mysqli->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                                  WHERE TABLE_SCHEMA = DATABASE() 
                                  AND TABLE_NAME = 'enhanced_coupons' 
                                  AND COLUMN_NAME = 'IsShining'");
    
    $row = $columnCheck->fetch_assoc();
    if ($row['count'] > 0) {
        echo "<div class='success'>✅ IsShining column already exists</div>";
    } else {
        echo "<div class='info'>ℹ️ IsShining column not found - will be added during setup</div>";
    }
    
    // Show sample data
    echo "<h2>📊 Sample Data (First 5 Records)</h2>";
    $sampleData = $mysqli->query("SELECT * FROM enhanced_coupons LIMIT 5");
    if ($sampleData && $sampleData->num_rows > 0) {
        echo "<table>";
        
        // Get column names
        $fields = $sampleData->fetch_fields();
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>" . $field->name . "</th>";
        }
        echo "</tr>";
        
        // Reset result pointer
        $sampleData->data_seek(0);
        
        // Show data
        while ($row = $sampleData->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?: 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='info'>ℹ️ No data found in the table</div>";
    }
    
    // Show record count
    $countResult = $mysqli->query("SELECT COUNT(*) as total FROM enhanced_coupons");
    if ($countResult) {
        $count = $countResult->fetch_assoc();
        echo "<div class='info'>📊 Total records: <strong>" . $count['total'] . "</strong></div>";
    }
    
    echo "<h2>🎯 Next Steps</h2>";
    echo "<div class='info'>";
    echo "<p>Based on the analysis above:</p>";
    echo "<ol>";
    echo "<li>Your table structure has been detected</li>";
    echo "<li>Primary key: <span class='code'>$primaryKey</span></li>";
    echo "<li>Ready to add shining feature</li>";
    echo "</ol>";
    echo "<p><a href='add_shining_feature.php' style='background: #ec6504; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>✨ Add Shining Feature</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Diagnostic Failed</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div></body></html>";
?>

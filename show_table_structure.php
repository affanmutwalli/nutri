<?php
/**
 * Show Table Structure - Quick diagnostic
 */

include_once 'database/dbconnection.php';

header('Content-Type: application/json');

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    // Get table structure
    $structure = $mysqli->query("DESCRIBE enhanced_coupons");
    $columns = [];
    while ($row = $structure->fetch_assoc()) {
        $columns[] = [
            'name' => $row['Field'],
            'type' => $row['Type'],
            'null' => $row['Null'],
            'key' => $row['Key'],
            'default' => $row['Default'],
            'extra' => $row['Extra']
        ];
    }
    
    // Get sample data
    $sampleData = $mysqli->query("SELECT * FROM enhanced_coupons LIMIT 3");
    $samples = [];
    while ($row = $sampleData->fetch_assoc()) {
        $samples[] = $row;
    }
    
    // Get count
    $countResult = $mysqli->query("SELECT COUNT(*) as total FROM enhanced_coupons");
    $count = $countResult->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'columns' => $columns,
        'sample_data' => $samples,
        'total_records' => $count,
        'table_exists' => true
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'table_exists' => false
    ], JSON_PRETTY_PRINT);
}
?>

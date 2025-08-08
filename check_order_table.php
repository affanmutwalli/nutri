<?php
/**
 * Check Order Master Table Structure
 */

include_once 'database/dbconnection.php';

header('Content-Type: application/json');

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    // Check if order_master table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'order_master'");
    if ($tableCheck->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'error' => 'order_master table not found',
            'table_exists' => false
        ]);
        exit();
    }
    
    // Get table structure
    $structure = $mysqli->query("DESCRIBE order_master");
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
    
    // Check for required guest columns
    $columnNames = array_column($columns, 'name');
    $requiredColumns = ['GuestName', 'GuestEmail', 'GuestPhone'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    // Get sample data
    $sampleData = $mysqli->query("SELECT * FROM order_master LIMIT 3");
    $samples = [];
    while ($row = $sampleData->fetch_assoc()) {
        $samples[] = $row;
    }
    
    // Get count
    $countResult = $mysqli->query("SELECT COUNT(*) as total FROM order_master");
    $count = $countResult->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'table_exists' => true,
        'columns' => $columns,
        'column_names' => $columnNames,
        'required_columns' => $requiredColumns,
        'missing_columns' => $missingColumns,
        'has_guest_columns' => empty($missingColumns),
        'sample_data' => $samples,
        'total_records' => $count
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'table_exists' => false
    ]);
}
?>

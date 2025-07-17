<?php
header("Content-Type: application/json");

try {
    include 'database/dbconnection.php';
    $obj = new main();
    $conn = $obj->connection();
    
    $results = [];
    
    // Check current table structure
    $result = $conn->query("DESCRIBE order_details");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }
    $results['current_structure'] = $columns;
    
    // Check if Id column exists and its properties
    $idColumn = null;
    foreach ($columns as $column) {
        if (strtolower($column['Field']) === 'id') {
            $idColumn = $column;
            break;
        }
    }
    
    $results['id_column'] = $idColumn;
    
    // Check if there's any data in the table
    $result = $conn->query("SELECT COUNT(*) as count FROM order_details");
    $row = $result->fetch_assoc();
    $results['record_count'] = $row['count'];
    
    // Get the CREATE TABLE statement
    $result = $conn->query("SHOW CREATE TABLE order_details");
    $row = $result->fetch_assoc();
    $results['create_statement'] = $row['Create Table'];
    
    // Determine what needs to be fixed
    $fixes_needed = [];
    
    if (!$idColumn) {
        $fixes_needed[] = "Id column is missing";
    } else {
        if (strpos($idColumn['Extra'], 'auto_increment') === false) {
            $fixes_needed[] = "Id column is not auto-increment";
        }
        if ($idColumn['Key'] !== 'PRI') {
            $fixes_needed[] = "Id column is not primary key";
        }
    }
    
    $results['fixes_needed'] = $fixes_needed;
    
    // If fixes are needed and there's no data, apply them
    if (!empty($fixes_needed) && $results['record_count'] == 0) {
        try {
            if (!$idColumn) {
                // Add Id column as auto-increment primary key
                $conn->query("ALTER TABLE order_details ADD COLUMN Id INT AUTO_INCREMENT PRIMARY KEY FIRST");
                $results['fix_applied'] = "Added Id column as auto-increment primary key";
            } else {
                // Modify existing Id column
                $conn->query("ALTER TABLE order_details MODIFY COLUMN Id INT AUTO_INCREMENT PRIMARY KEY FIRST");
                $results['fix_applied'] = "Modified Id column to be auto-increment primary key";
            }
            
            // Verify the fix
            $result = $conn->query("DESCRIBE order_details");
            $newColumns = [];
            while ($row = $result->fetch_assoc()) {
                $newColumns[] = $row;
            }
            $results['new_structure'] = $newColumns;
            
        } catch (Exception $e) {
            $results['fix_error'] = $e->getMessage();
        }
    } else if (!empty($fixes_needed) && $results['record_count'] > 0) {
        $results['warning'] = "Table has data. Manual intervention required.";
    } else {
        $results['status'] = "Table structure is correct";
    }
    
    echo json_encode($results, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>

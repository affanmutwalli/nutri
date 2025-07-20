<?php
// Ultra-simple JSON test
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Test 1: Basic JSON response
if (isset($_GET['test']) && $_GET['test'] == 'basic') {
    echo json_encode(['success' => true, 'message' => 'Basic test works']);
    exit;
}

// Test 2: With session
if (isset($_GET['test']) && $_GET['test'] == 'session') {
    session_start();
    echo json_encode([
        'success' => true, 
        'message' => 'Session test works',
        'customer_id' => $_SESSION['CustomerId'] ?? 'not set'
    ]);
    exit;
}

// Test 3: With database
if (isset($_GET['test']) && $_GET['test'] == 'database') {
    session_start();
    
    try {
        require_once 'database/dbconnection.php';
        $obj = new main();
        $mysqli = $obj->connection();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Database test works',
            'customer_id' => $_SESSION['CustomerId'] ?? 'not set'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Test 4: POST with JSON input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    echo json_encode([
        'success' => true,
        'message' => 'POST test works',
        'received_data' => $input,
        'customer_id' => $_SESSION['CustomerId'] ?? 'not set'
    ]);
    exit;
}

// Default response
echo json_encode([
    'success' => true,
    'message' => 'JSON response test script',
    'tests' => [
        'basic' => '?test=basic',
        'session' => '?test=session', 
        'database' => '?test=database',
        'post' => 'Send POST request'
    ]
]);
?>

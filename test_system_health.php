<?php
/**
 * Online Ordering System Health Check
 * This script tests all components of the online ordering system
 */

session_start();
include('database/dbconnection.php');

// Set content type for JSON response
header('Content-Type: application/json');

$healthCheck = [
    'timestamp' => date('Y-m-d H:i:s'),
    'overall_status' => 'unknown',
    'components' => []
];

try {
    // Test 1: Database Connection
    $obj = new main();
    $connection = $obj->connection();
    
    if ($connection) {
        $healthCheck['components']['database'] = [
            'status' => 'healthy',
            'message' => 'Database connection successful'
        ];
    } else {
        throw new Exception('Database connection failed');
    }

    // Test 2: Check Required Tables
    $requiredTables = ['order_master', 'order_details', 'customer_master'];
    $tablesStatus = [];
    
    foreach ($requiredTables as $table) {
        $result = $connection->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            $tablesStatus[$table] = 'exists';
        } else {
            $tablesStatus[$table] = 'missing';
        }
    }
    
    $healthCheck['components']['database_tables'] = [
        'status' => in_array('missing', $tablesStatus) ? 'warning' : 'healthy',
        'tables' => $tablesStatus
    ];

    // Test 3: Razorpay SDK
    if (file_exists('razorpay/Razorpay.php')) {
        require_once('razorpay/Razorpay.php');
        
        if (class_exists('Razorpay\Api\Api')) {
            $healthCheck['components']['razorpay_sdk'] = [
                'status' => 'healthy',
                'message' => 'Razorpay SDK loaded successfully'
            ];
        } else {
            $healthCheck['components']['razorpay_sdk'] = [
                'status' => 'error',
                'message' => 'Razorpay API class not found'
            ];
        }
    } else {
        $healthCheck['components']['razorpay_sdk'] = [
            'status' => 'error',
            'message' => 'Razorpay SDK file not found'
        ];
    }

    // Test 4: Check Backend Files
    $backendFiles = [
        'exe_files/rcus_place_order_online.php',
        'exe_files/razorpay_callback.php',
        'exe_files/razorpay_config.php'
    ];
    
    $filesStatus = [];
    foreach ($backendFiles as $file) {
        $filesStatus[basename($file)] = file_exists($file) ? 'exists' : 'missing';
    }
    
    $healthCheck['components']['backend_files'] = [
        'status' => in_array('missing', $filesStatus) ? 'error' : 'healthy',
        'files' => $filesStatus
    ];

    // Test 5: Check Razorpay Configuration
    if (file_exists('exe_files/razorpay_config.php')) {
        include('exe_files/razorpay_config.php');
        
        $configStatus = [
            'key_id_defined' => defined('RAZORPAY_KEY_ID'),
            'key_secret_defined' => defined('RAZORPAY_KEY_SECRET'),
            'key_id_value' => defined('RAZORPAY_KEY_ID') ? (strlen(RAZORPAY_KEY_ID) > 0 ? 'set' : 'empty') : 'not_set',
            'key_secret_value' => defined('RAZORPAY_KEY_SECRET') ? (strlen(RAZORPAY_KEY_SECRET) > 0 ? 'set' : 'empty') : 'not_set',
            'environment' => defined('RAZORPAY_KEY_ID') && strpos(RAZORPAY_KEY_ID, 'rzp_test_') === 0 ? 'test' : 'live'
        ];
        
        $configHealthy = $configStatus['key_id_defined'] && 
                        $configStatus['key_secret_defined'] && 
                        $configStatus['key_id_value'] === 'set' && 
                        $configStatus['key_secret_value'] === 'set';
        
        $healthCheck['components']['razorpay_config'] = [
            'status' => $configHealthy ? 'healthy' : 'error',
            'config' => $configStatus
        ];
    } else {
        $healthCheck['components']['razorpay_config'] = [
            'status' => 'error',
            'message' => 'Razorpay config file not found'
        ];
    }

    // Test 6: Session Check
    $healthCheck['components']['session'] = [
        'status' => 'healthy',
        'logged_in' => isset($_SESSION['CustomerId']) && !empty($_SESSION['CustomerId']),
        'customer_id' => $_SESSION['CustomerId'] ?? 'not_set',
        'cart_items' => isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0
    ];

    // Determine Overall Status
    $componentStatuses = array_column($healthCheck['components'], 'status');
    if (in_array('error', $componentStatuses)) {
        $healthCheck['overall_status'] = 'error';
    } elseif (in_array('warning', $componentStatuses)) {
        $healthCheck['overall_status'] = 'warning';
    } else {
        $healthCheck['overall_status'] = 'healthy';
    }

} catch (Exception $e) {
    $healthCheck['overall_status'] = 'error';
    $healthCheck['error'] = $e->getMessage();
}

// Output the health check results
echo json_encode($healthCheck, JSON_PRETTY_PRINT);
?>

<?php
// Sales data API for OMS dashboard charts
header('Content-Type: application/json');

include('includes/urls.php');
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");

$obj = new main();
$obj->connection();

try {
    // Get monthly sales data for the chart
    $salesData = [];
    
    // Get sales data for the last 12 months
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthName = date('M Y', strtotime("-$i months"));
        
        // Query to get total sales for the month
        $query = "SELECT SUM(Amount) as total_sales FROM order_master 
                  WHERE DATE_FORMAT(OrderDate, '%Y-%m') = '$month' 
                  AND OrderStatus IN ('Delivered', 'Completed', 'Shipped')";
        
        $result = $mysqli->query($query);
        $row = $result->fetch_assoc();
        $totalSales = $row['total_sales'] ?? 0;
        
        $salesData[] = [
            'month' => $monthName,
            'total_sales' => (float)$totalSales
        ];
    }
    
    echo json_encode($salesData);
    
} catch (Exception $e) {
    error_log("Sales data API error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Failed to fetch sales data',
        'message' => $e->getMessage()
    ]);
}
?>

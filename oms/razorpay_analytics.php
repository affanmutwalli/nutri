<?php
$selected = "razorpay_analytics.php";

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$obj->connection();
sec_session_start();

if(login_check($mysqli) == false) {
    header('Location: index.php');
    exit();
}

// Get date range for analytics
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // First day of current month
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Today

// Payment method comparison
$paymentComparison = $obj->MysqliSelect1(
    "SELECT 
        PaymentType,
        COUNT(*) as order_count,
        SUM(Amount) as total_amount,
        AVG(Amount) as avg_amount
     FROM order_master 
     WHERE OrderDate BETWEEN ? AND ?
     GROUP BY PaymentType",
    ["PaymentType", "order_count", "total_amount", "avg_amount"],
    "ss",
    [$startDate, $endDate]
);

// Daily payment trends for online payments
$dailyTrends = $obj->MysqliSelect1(
    "SELECT 
        DATE(OrderDate) as payment_date,
        COUNT(*) as transaction_count,
        SUM(Amount) as daily_revenue,
        SUM(CASE WHEN PaymentStatus = 'Paid' THEN 1 ELSE 0 END) as successful_payments,
        SUM(CASE WHEN PaymentStatus = 'Failed' THEN 1 ELSE 0 END) as failed_payments
     FROM order_master 
     WHERE PaymentType = 'Online' AND OrderDate BETWEEN ? AND ?
     GROUP BY DATE(OrderDate)
     ORDER BY payment_date DESC",
    ["payment_date", "transaction_count", "daily_revenue", "successful_payments", "failed_payments"],
    "ss",
    [$startDate, $endDate]
);

// Payment status breakdown
$statusBreakdown = $obj->MysqliSelect1(
    "SELECT 
        PaymentStatus,
        COUNT(*) as count,
        SUM(Amount) as amount,
        ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM order_master WHERE PaymentType = 'Online' AND OrderDate BETWEEN ? AND ?)), 2) as percentage
     FROM order_master 
     WHERE PaymentType = 'Online' AND OrderDate BETWEEN ? AND ?
     GROUP BY PaymentStatus",
    ["PaymentStatus", "count", "amount", "percentage"],
    "ssss",
    [$startDate, $endDate, $startDate, $endDate]
);

// Top performing days
$topDays = $obj->MysqliSelect1(
    "SELECT 
        DAYNAME(OrderDate) as day_name,
        COUNT(*) as transaction_count,
        SUM(Amount) as total_revenue,
        AVG(Amount) as avg_transaction
     FROM order_master 
     WHERE PaymentType = 'Online' AND PaymentStatus = 'Paid' AND OrderDate BETWEEN ? AND ?
     GROUP BY DAYOFWEEK(OrderDate), DAYNAME(OrderDate)
     ORDER BY total_revenue DESC",
    ["day_name", "transaction_count", "total_revenue", "avg_transaction"],
    "ss",
    [$startDate, $endDate]
);

// Monthly comparison (last 6 months)
$monthlyComparison = $obj->MysqliSelect1(
    "SELECT
        DATE_FORMAT(OrderDate, '%Y-%m') as month,
        COUNT(*) as transaction_count,
        SUM(Amount) as monthly_revenue,
        SUM(CASE WHEN PaymentStatus = 'Paid' THEN Amount ELSE 0 END) as successful_revenue
     FROM order_master
     WHERE PaymentType = 'Online' AND OrderDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
     GROUP BY DATE_FORMAT(OrderDate, '%Y-%m')
     ORDER BY month DESC",
    ["month", "transaction_count", "monthly_revenue", "successful_revenue"],
    "",
    []
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment Analytics | MyNutrify OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include('components/sidebar.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Payment Analytics</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="razorpay_dashboard.php">Razorpay</a></li>
                            <li class="breadcrumb-item active">Analytics</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Date Range Filter -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Analytics Date Range</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Update Analytics</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payment Method Comparison -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Payment Method Comparison</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentMethodChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Payment Status Breakdown</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentStatusChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Trends -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Payment Trends</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyTrendsChart" width="400" height="100"></canvas>
                    </div>
                </div>

                <!-- Statistics Tables -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Top Performing Days</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Day</th>
                                                <th>Transactions</th>
                                                <th>Revenue</th>
                                                <th>Avg Transaction</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($topDays)): ?>
                                                <?php foreach ($topDays as $day): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($day['day_name']); ?></td>
                                                        <td><?php echo $day['transaction_count']; ?></td>
                                                        <td>₹<?php echo number_format($day['total_revenue'], 2); ?></td>
                                                        <td>₹<?php echo number_format($day['avg_transaction'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No data available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Monthly Performance (Last 6 Months)</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Transactions</th>
                                                <th>Total Revenue</th>
                                                <th>Success Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($monthlyComparison)): ?>
                                                <?php foreach ($monthlyComparison as $month): ?>
                                                    <tr>
                                                        <td><?php echo date("M Y", strtotime($month['month'] . '-01')); ?></td>
                                                        <td><?php echo $month['transaction_count']; ?></td>
                                                        <td>₹<?php echo number_format($month['monthly_revenue'], 2); ?></td>
                                                        <td>
                                                            <?php 
                                                            $successRate = $month['monthly_revenue'] > 0 ? 
                                                                ($month['successful_revenue'] / $month['monthly_revenue']) * 100 : 0;
                                                            echo number_format($successRate, 1) . '%';
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No data available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include('components/footer.php'); ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
// Payment Method Chart
const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
const paymentMethodChart = new Chart(paymentMethodCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php 
            if (!empty($paymentComparison)) {
                foreach ($paymentComparison as $payment) {
                    echo "'" . htmlspecialchars($payment['PaymentType']) . "',";
                }
            }
            ?>
        ],
        datasets: [{
            data: [
                <?php 
                if (!empty($paymentComparison)) {
                    foreach ($paymentComparison as $payment) {
                        echo $payment['total_amount'] . ",";
                    }
                }
                ?>
            ],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Payment Status Chart
const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
const paymentStatusChart = new Chart(paymentStatusCtx, {
    type: 'pie',
    data: {
        labels: [
            <?php 
            if (!empty($statusBreakdown)) {
                foreach ($statusBreakdown as $status) {
                    echo "'" . htmlspecialchars($status['PaymentStatus']) . "',";
                }
            }
            ?>
        ],
        datasets: [{
            data: [
                <?php 
                if (!empty($statusBreakdown)) {
                    foreach ($statusBreakdown as $status) {
                        echo $status['count'] . ",";
                    }
                }
                ?>
            ],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Daily Trends Chart
const dailyTrendsCtx = document.getElementById('dailyTrendsChart').getContext('2d');
const dailyTrendsChart = new Chart(dailyTrendsCtx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            if (!empty($dailyTrends)) {
                foreach (array_reverse($dailyTrends) as $trend) {
                    echo "'" . date("M d", strtotime($trend['payment_date'])) . "',";
                }
            }
            ?>
        ],
        datasets: [{
            label: 'Daily Revenue',
            data: [
                <?php 
                if (!empty($dailyTrends)) {
                    foreach (array_reverse($dailyTrends) as $trend) {
                        echo $trend['daily_revenue'] . ",";
                    }
                }
                ?>
            ],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
        }, {
            label: 'Transaction Count',
            data: [
                <?php 
                if (!empty($dailyTrends)) {
                    foreach (array_reverse($dailyTrends) as $trend) {
                        echo $trend['transaction_count'] . ",";
                    }
                }
                ?>
            ],
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});
</script>
</body>
</html>

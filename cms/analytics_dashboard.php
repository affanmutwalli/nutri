<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$obj->connection();
sec_session_start();

// Check if user is logged in
if (login_check($mysqli) != true) {
    header("Location: index.php");
    exit();
}

// Include analytics functions
require_once '../includes/analytics_functions.php';

$selected = "analytics_dashboard.php";
$page = "analytics_dashboard.php";

// Setup analytics database if needed
setupAnalyticsDatabase();

// Get analytics data
$days = isset($_GET['days']) ? intval($_GET['days']) : 30;
$summary = getAnalyticsSummary($days);

// Get detailed analytics data
try {
    // Total visitors and page views
    $result = $mysqli->query("
        SELECT 
            COUNT(DISTINCT visitor_id) as total_visitors,
            COUNT(DISTINCT CASE WHEN has_registered = TRUE THEN visitor_id END) as registered_visitors,
            COUNT(DISTINCT CASE WHEN has_purchased = TRUE THEN visitor_id END) as converted_visitors,
            AVG(total_page_views) as avg_pages_per_visitor,
            AVG(total_session_duration) as avg_session_duration,
            SUM(total_order_value) as total_revenue
        FROM visitor_analytics 
        WHERE first_visit >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
    ");
    $overviewStats = $result->fetch_assoc();
    
    // Device breakdown
    $result = $mysqli->query("
        SELECT 
            device_type,
            COUNT(*) as count,
            ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM visitor_analytics WHERE first_visit >= DATE_SUB(NOW(), INTERVAL {$days} DAY))), 2) as percentage
        FROM visitor_analytics 
        WHERE first_visit >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        GROUP BY device_type
        ORDER BY count DESC
    ");
    $deviceStats = $result->fetch_all(MYSQLI_ASSOC);
    
    // Top pages
    $result = $mysqli->query("
        SELECT 
            page_url,
            page_type,
            COUNT(*) as views,
            COUNT(DISTINCT visitor_id) as unique_visitors,
            AVG(time_on_page) as avg_time_on_page
        FROM page_views 
        WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        GROUP BY page_url, page_type
        ORDER BY views DESC
        LIMIT 10
    ");
    $topPages = $result->fetch_all(MYSQLI_ASSOC);
    
    // Recent visitors
    $result = $mysqli->query("
        SELECT 
            visitor_id,
            customer_id,
            device_type,
            browser,
            country,
            city,
            total_page_views,
            has_purchased,
            total_order_value,
            last_visit
        FROM visitor_analytics 
        WHERE last_visit >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY last_visit DESC
        LIMIT 20
    ");
    $recentVisitors = $result->fetch_all(MYSQLI_ASSOC);
    
    // Daily trends for chart
    $result = $mysqli->query("
        SELECT 
            DATE(viewed_at) as date,
            COUNT(DISTINCT visitor_id) as visitors,
            COUNT(*) as page_views
        FROM page_views
        WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
        GROUP BY DATE(viewed_at)
        ORDER BY date ASC
    ");
    $dailyTrends = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    error_log("Analytics dashboard error: " . $e->getMessage());
    $overviewStats = [];
    $deviceStats = [];
    $topPages = [];
    $recentVisitors = [];
    $dailyTrends = [];
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Analytics Dashboard | My Nutrify CMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include('components/header_links.php');?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .analytics-card {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 1.5rem;
    }
    .metric-card {
      text-align: center;
      padding: 1.5rem;
      border-radius: 8px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      margin-bottom: 1rem;
      transition: transform 0.3s ease;
    }
    .metric-card:hover {
      transform: translateY(-5px);
    }
    .metric-number {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }
    .metric-label {
      font-size: 0.9rem;
      opacity: 0.9;
    }
    .chart-container {
      position: relative;
      height: 400px;
      margin: 1rem 0;
    }
    .table-responsive {
      max-height: 400px;
      overflow-y: auto;
    }
    .visitor-status {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    .status-purchased {
      background: #d4edda;
      color: #155724;
    }
    .status-registered {
      background: #d1ecf1;
      color: #0c5460;
    }
    .status-visitor {
      background: #f8d7da;
      color: #721c24;
    }
    .time-filter {
      margin-bottom: 1rem;
    }
    .time-filter .btn {
      margin-right: 0.5rem;
    }
    .device-chart {
      max-width: 300px;
      margin: 0 auto;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include('components/navbar.php');?>
  <?php include('components/sidebar.php');?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Analytics Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Analytics</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <!-- Time Filter -->
        <div class="time-filter">
          <a href="?days=7" class="btn <?php echo $days == 7 ? 'btn-primary' : 'btn-outline-primary'; ?>">Last 7 Days</a>
          <a href="?days=30" class="btn <?php echo $days == 30 ? 'btn-primary' : 'btn-outline-primary'; ?>">Last 30 Days</a>
          <a href="?days=90" class="btn <?php echo $days == 90 ? 'btn-primary' : 'btn-outline-primary'; ?>">Last 90 Days</a>
          <a href="?days=365" class="btn <?php echo $days == 365 ? 'btn-primary' : 'btn-outline-primary'; ?>">Last Year</a>
        </div>

        <!-- Overview Metrics -->
        <div class="row">
          <div class="col-lg-3 col-md-6">
            <div class="metric-card">
              <div class="metric-number"><?php echo number_format($overviewStats['total_visitors'] ?? 0); ?></div>
              <div class="metric-label">Total Visitors</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="metric-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
              <div class="metric-number"><?php echo number_format($summary['total_page_views'] ?? 0); ?></div>
              <div class="metric-label">Page Views</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
              <div class="metric-number"><?php echo $summary['conversion_rate'] ?? 0; ?>%</div>
              <div class="metric-label">Conversion Rate</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="metric-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
              <div class="metric-number">₹<?php echo number_format($overviewStats['total_revenue'] ?? 0, 2); ?></div>
              <div class="metric-label">Total Revenue</div>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
          <div class="col-lg-8">
            <div class="analytics-card">
              <h5>Visitor Trends</h5>
              <div class="chart-container">
                <canvas id="trendsChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="analytics-card">
              <h5>Device Breakdown</h5>
              <div class="device-chart">
                <canvas id="deviceChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Popular Products -->
        <div class="row">
          <div class="col-lg-6">
            <div class="analytics-card">
              <h5>Popular Products</h5>
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Views</th>
                      <th>Cart Adds</th>
                      <th>Purchases</th>
                      <th>Conversion</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($summary['top_products'] ?? [] as $product): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                      <td><?php echo number_format($product['total_views']); ?></td>
                      <td><?php echo number_format($product['total_cart_additions']); ?></td>
                      <td><?php echo number_format($product['total_purchases']); ?></td>
                      <td>
                        <?php 
                        $conversion = $product['total_views'] > 0 ? ($product['total_purchases'] / $product['total_views']) * 100 : 0;
                        echo number_format($conversion, 1) . '%';
                        ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6">
            <div class="analytics-card">
              <h5>Top Pages</h5>
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Page</th>
                      <th>Views</th>
                      <th>Unique Visitors</th>
                      <th>Avg Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($topPages as $page): ?>
                    <tr>
                      <td>
                        <small><?php echo htmlspecialchars($page['page_url']); ?></small>
                        <br><span class="badge badge-secondary"><?php echo $page['page_type']; ?></span>
                      </td>
                      <td><?php echo number_format($page['views']); ?></td>
                      <td><?php echo number_format($page['unique_visitors']); ?></td>
                      <td><?php echo $page['avg_time_on_page'] ? gmdate("i:s", $page['avg_time_on_page']) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Visitors -->
        <div class="row">
          <div class="col-12">
            <div class="analytics-card">
              <h5>Recent Visitors (Last 7 Days)</h5>
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Visitor ID</th>
                      <th>Status</th>
                      <th>Device</th>
                      <th>Browser</th>
                      <th>Location</th>
                      <th>Page Views</th>
                      <th>Order Value</th>
                      <th>Last Visit</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recentVisitors as $visitor): ?>
                    <tr>
                      <td><small><?php echo substr($visitor['visitor_id'], 0, 12) . '...'; ?></small></td>
                      <td>
                        <?php if ($visitor['has_purchased']): ?>
                          <span class="visitor-status status-purchased">Customer</span>
                        <?php elseif ($visitor['customer_id']): ?>
                          <span class="visitor-status status-registered">Registered</span>
                        <?php else: ?>
                          <span class="visitor-status status-visitor">Visitor</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo $visitor['device_type']; ?></td>
                      <td><?php echo $visitor['browser']; ?></td>
                      <td><?php echo $visitor['city'] ? $visitor['city'] . ', ' . $visitor['country'] : $visitor['country']; ?></td>
                      <td><?php echo $visitor['total_page_views']; ?></td>
                      <td>₹<?php echo number_format($visitor['total_order_value'], 2); ?></td>
                      <td><?php echo date('M j, H:i', strtotime($visitor['last_visit'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  <?php include('components/footer.php');?>
</div>

<?php include('components/footer_links.php');?>

<script>
// Trends Chart
const ctx = document.getElementById('trendsChart').getContext('2d');
const trendsData = <?php echo json_encode($dailyTrends); ?>;

const labels = trendsData.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});

const visitorsData = trendsData.map(item => parseInt(item.visitors));
const pageViewsData = trendsData.map(item => parseInt(item.page_views));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Visitors',
            data: visitorsData,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Page Views',
            data: pageViewsData,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Device Chart
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
const deviceData = <?php echo json_encode($deviceStats); ?>;

const deviceLabels = deviceData.map(item => item.device_type.charAt(0).toUpperCase() + item.device_type.slice(1));
const deviceCounts = deviceData.map(item => parseInt(item.count));

new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: deviceLabels,
        datasets: [{
            data: deviceCounts,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Auto-refresh every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>

</body>
</html>

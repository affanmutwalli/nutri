<?php
$selected = "dashboard.php";

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0">
    <script data-cfasync="false" nonce="146b507a-e61c-4134-87d3-d54fb50f20b2">
    try {
        (function(w, d) {
            ! function(a, b, c, d) {
                if (a.zaraz) console.error("zaraz is loaded twice");
                else {
                    a[c] = a[c] || {};
                    a[c].executed = [];
                    a.zaraz = {
                        deferred: [],
                        listeners: []
                    };
                    a.zaraz._v = "5848";
                    a.zaraz._n = "146b507a-e61c-4134-87d3-d54fb50f20b2";
                    a.zaraz.q = [];
                    a.zaraz._f = function(e) {
                        return async function() {
                            var f = Array.prototype.slice.call(arguments);
                            a.zaraz.q.push({
                                m: e,
                                a: f
                            })
                        }
                    };
                    for (const g of ["track", "set", "debug"]) a.zaraz[g] = a.zaraz._f(g);
                    a.zaraz.init = () => {
                        var h = b.getElementsByTagName(d)[0],
                            i = b.createElement(d),
                            j = b.getElementsByTagName("title")[0];
                        j && (a[c].t = b.getElementsByTagName("title")[0].text);
                        a[c].x = Math.random();
                        a[c].w = a.screen.width;
                        a[c].h = a.screen.height;
                        a[c].j = a.innerHeight;
                        a[c].e = a.innerWidth;
                        a[c].l = a.location.href;
                        a[c].r = b.referrer;
                        a[c].k = a.screen.colorDepth;
                        a[c].n = b.characterSet;
                        a[c].o = (new Date).getTimezoneOffset();
                        if (a.dataLayer)
                            for (const k of Object.entries(Object.entries(dataLayer).reduce(((l, m) => ({
                                    ...l[1],
                                    ...m[1]
                                })), {}))) zaraz.set(k[0], k[1], {
                                scope: "page"
                            });
                        a[c].q = [];
                        for (; a.zaraz.q.length;) {
                            const n = a.zaraz.q.shift();
                            a[c].q.push(n)
                        }
                        i.defer = !0;
                        for (const o of [localStorage, sessionStorage]) Object.keys(o || {}).filter((q => q
                            .startsWith("_zaraz_"))).forEach((p => {
                            try {
                                a[c]["z_" + p.slice(7)] = JSON.parse(o.getItem(p))
                            } catch {
                                a[c]["z_" + p.slice(7)] = o.getItem(p)
                            }
                        }));
                        i.referrerPolicy = "origin";
                        i.src = "/cdn-cgi/zaraz/s.js?z=" + btoa(encodeURIComponent(JSON.stringify(a[c])));
                        h.parentNode.insertBefore(i, h)
                    };
                    ["complete", "interactive"].includes(b.readyState) ? zaraz.init() : a.addEventListener(
                        "DOMContentLoaded", zaraz.init)
                }
            }(w, d, "zarazData", "script");
            window.zaraz._p = async bs => new Promise((bt => {
                if (bs) {
                    bs.e && bs.e.forEach((bu => {
                        try {
                            const bv = d.querySelector("script[nonce]"),
                                bw = bv?.nonce || bv?.getAttribute("nonce"),
                                bx = d.createElement("script");
                            bw && (bx.nonce = bw);
                            bx.innerHTML = bu;
                            bx.onload = () => {
                                d.head.removeChild(bx)
                            };
                            d.head.appendChild(bx)
                        } catch (by) {
                            console.error(`Error executing script: ${bu}\n`, by)
                        }
                    }));
                    Promise.allSettled((bs.f || []).map((bz => fetch(bz[0], bz[1]))))
                }
                bt()
            }));
            zaraz._p({
                "e": ["(function(w,d){})(window,document)"]
            });
        })(window, document)
    } catch (e) {
        throw fetch("/cdn-cgi/zaraz/t"), e;
    };
    </script>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include("components/sidebar.php"); ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?php
                        $today = date('Y-m-d');
                        $firstDayOfMonth = date('Y-m-01');
                        $lastDayOfMonth = date('Y-m-t');

                        $todayOrders = $obj->fSelectRowCountNew("SELECT * FROM order_master WHERE DATE(OrderDate) = '$today'");
                        $pendingOrders = $obj->fSelectRowCountNew("SELECT * FROM order_master WHERE OrderStatus = 'Pending'");
                        $deliveredOrders = $obj->fSelectRowCountNew("SELECT * FROM order_master WHERE OrderStatus = 'Delivered' AND OrderDate BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'");
                        $cancelOrders = $obj->fSelectRowCountNew("SELECT * FROM order_master WHERE OrderStatus = 'Cancelled'");
                         // Get the first and last day of the current month in 'Y-m-d' format
                        $firstDay = date('Y-m-01');
                        $lastDay = date('Y-m-t');
                    
                        // Build the query string with the dates embedded
                        $query = "SELECT * FROM order_master WHERE DATE(OrderDate) BETWEEN '$firstDay' AND '$lastDay'";
                    
                        // Execute the query using your method
                        $monthOrders = $obj->fSelectRowCountNew($query);
                        $all_orders = $obj->fSelectRowCountNew("SELECT * FROM order_master");
                        
                        ?>


                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $todayOrders; ?></h3>
                                    <p>Today Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="todays_order.php?OrderDate=<?php echo $today ?>" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>                     
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo $deliveredOrders; ?></h3>
                                    <p>Delivered Orders (This Month)</p>
                                </div>
                                <div class="icon">
                                    <!-- <i class="ion ion-bag"></i> -->
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <a href="delivered_order.php" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo $pendingOrders; ?></h3>
                                    <p>Pending Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-clock"></i>
                                </div>
                                <a href="order_status.php?OrderStatus=Pending" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?php echo $cancelOrders; ?></h3>
                                    <p>Cancel Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-times"></i>
                                </div>
                                <a href="order_status.php?OrderStatus=Cancelled" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                                                <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $monthOrders; ?></h3>
                                    <p>This Month Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="month_order.php" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                         <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $all_orders; ?></h3>
                                    <p>All Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="all_orders.php" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header border-0">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title">Online Total Visitors</h3>
                                        <a href="javascript:void(0);">View Report</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex">
                                        <p class="d-flex flex-column">
                                            <span class="text-bold text-lg">820</span>
                                            <span>Visitors Over Time</span>
                                        </p>
                                        <p class="ml-auto d-flex flex-column text-right">
                                            <span class="text-success">
                                                <i class="fas fa-arrow-up"></i> 12.5%
                                            </span>
                                            <span class="text-muted">Since last week</span>
                                        </p>
                                    </div>
                                    <!-- /.d-flex -->

                                    <div class="position-relative mb-4">
                                        <canvas id="visitors-chart" height="200"></canvas>
                                    </div>

                                    <div class="d-flex flex-row justify-content-end">
                                        <span class="mr-2">
                                            <i class="fas fa-square text-primary"></i> This Week
                                        </span>

                                        <span>
                                            <i class="fas fa-square text-gray"></i> Last Week
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                            <!-- /.card -->
                        </div>
                        <!-- /.col-md-6 -->
                        <div class="col-lg-6">
                            <?php
function getMonthlySalesData() {
    global $mysqli; // Use the correct database connection

    $sql = "SELECT DATE_FORMAT(OrderDate, '%Y-%m') AS month, SUM(Amount) AS total_sales
            FROM order_master
            WHERE OrderStatus IN ('Delivered', 'Completed', 'Shipped')
            GROUP BY month
            ORDER BY month ASC";

    $result = mysqli_query($mysqli, $sql);
    $salesData = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $salesData[] = [
                'month' => $row['month'],
                'total_sales' => (float) $row['total_sales']
            ];
        }
    }

    return json_encode($salesData);
}
?>

                            <div class="card">
                                <div class="card-header border-0">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title">Total Sales</h3>
                                        <a href="javascript:void(0);">View Report</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex">
                                        <p class="d-flex flex-column">
                                            <span class="text-bold text-lg">$18,230.00</span>
                                            <span>Sales Over Time</span>
                                        </p>
                                        <p class="ml-auto d-flex flex-column text-right">
                                            <span class="text-success">
                                                <i class="fas fa-arrow-up"></i> 33.1%
                                            </span>
                                            <span class="text-muted">Since last month</span>
                                        </p>
                                    </div>
                                    <div class="position-relative mb-4">
                                        <canvas id="sales-chart" height="200"></canvas>
                                    </div>
                                    <div class="d-flex flex-row justify-content-end">
                                        <span class="mr-2">
                                            <i class="fas fa-square text-primary"></i> This year
                                        </span>
                                        <span>
                                            <i class="fas fa-square text-gray"></i> Last year
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col-md-6 -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE -->
    <script src="dist/js/adminlte.js?v=3.2.0"></script>

    <!-- OPTIONAL SCRIPTS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard3.js"></script>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('sales_data.php')
        .then(response => response.json())
        .then(data => {
            let labels = data.map(item => item.month);
            let sales = data.map(item => item.total_sales);

            let ctx = document.getElementById('sales-chart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Sales',
                        data: sales,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 0, 255, 0.1)',
                        fill: true
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
        });
});
</script>

</html>
<?php
// }
// else
// {
// 	header('Location: index.php');
// }
?>
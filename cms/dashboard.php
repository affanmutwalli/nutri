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
   ?>
<?php
   $selected = "dashboard.php";
   $page = "dashboard.php";
   
   $total_products=$obj->fSelectRowCountNew("Select * from product_master;");
   // $total_testimonials=$obj->fSelectRowCountNew("Select * from testimonials;");
   // $total_enquiries=$obj->fSelectRowCountNew("Select * from enquiry;");
   // $total_careers =$obj->fSelectRowCountNew("Select * from applied_candidates;");

   ?><!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>CMS | Dashboard</title>
      <!-- Google Font: Source Sans Pro -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
      <!-- Tempusdominus Bootstrap 4 -->
      <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
      <!-- iCheck -->
      <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
      <!-- JQVMap -->
      <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="dist/css/adminlte.min.css">
      <!-- overlayScrollbars -->
      <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
      <!-- Daterange picker -->
      <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
      <!-- summernote -->
      <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
      <style>
          
          .bg-primary-subtle {
                background-color: rgba(0, 123, 255, 0.1); /* Primary color with opacity */
                padding: 10px; /* Adjust padding as needed */
                border-radius: 5px; /* Optional: Add border radius for rounded corners */
                color: #fff; /* Optional: Text color */
            }
            .bg-orders{
               background-color: lightgreen;
            }
            .text-color{
               color: black;
            }

      </style>
   </head>
   <body class="hold-transition sidebar-mini layout-fixed">
      <div class="wrapper">
         <?php include('components/sidebar.php');?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                     </div>
                     <!-- /.col -->
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="#">Home</a></li>
                           <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                     </div>
                     <!-- /.col -->
                  </div>
                  <!-- /.row -->
               </div>
               <!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <!-- Main content -->
            <section class="content">
               <div class="container-fluid">
                  <!-- Small boxes (Stat box) -->
                  <div class="row">
                        <div class="col-lg-3 col-6">
                           <!-- small box -->
                           <div class="small-box bg-primary">
                              <div class="inner">
                                    <h3><?php echo $total_products; ?></h3>
                                    <p>Total Products</p>
                              </div>
                              <div class="icon">
                                    <i class="nav-icon fas fa-box"></i>
                              </div>
                              <a href="product_list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                  </div>
                  <!-- /.row -->
               </div>
               <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <?php include('components/footer.php');?>
         <!-- Control Sidebar -->
         <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
         </aside>
         <!-- /.control-sidebar -->
      </div>
      <!-- ./wrapper -->
      <!-- jQuery -->
      <script src="plugins/jquery/jquery.min.js"></script>
      <!-- jQuery UI 1.11.4 -->
      <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
      <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
      <script>
         $.widget.bridge('uibutton', $.ui.button)
      </script>
      <!-- Bootstrap 4 -->
      <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- ChartJS -->
      <script src="plugins/chart.js/Chart.min.js"></script>
      <!-- Sparkline -->
      <script src="plugins/sparklines/sparkline.js"></script>
      <!-- JQVMap -->
      <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
      <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
      <!-- jQuery Knob Chart -->
      <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
      <!-- daterangepicker -->
      <script src="plugins/moment/moment.min.js"></script>
      <script src="plugins/daterangepicker/daterangepicker.js"></script>
      <!-- Tempusdominus Bootstrap 4 -->
      <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
      <!-- Summernote -->
      <script src="plugins/summernote/summernote-bs4.min.js"></script>
      <!-- overlayScrollbars -->
      <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
      <!-- AdminLTE App -->
      <script src="dist/js/adminlte.js"></script>
      <!-- AdminLTE for demo purposes -->
      <script src="dist/js/demo.js"></script>
      <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
      <script src="dist/js/pages/dashboard.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
    // Chart.js code to display the data
    var ctx = document.getElementById('line-chart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Aug 20", "Aug 21", "Aug 22", "Aug 23", "Aug 24", "Aug 25", "Aug 26", "Aug 27", "Aug 28", "Aug 29"],
            datasets: [{
                label: 'Visits',
                data: [35, 60, 50, 42, 67, 47, 48, 75, 60, 32],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, // Ensures the chart resizes with the window
            maintainAspectRatio: false, // Allows height and width to be set manually
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
   </body>
</html>
<?php
   // }
   // else
   // {
   // 	header('Location: index.php');
   // }
   ?>
<?php
include('includes/urls.php');
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$obj->connection();
sec_session_start();
// if (login_check($mysqli) == true) 
// {
	//getPassword("admin",$mysqli);
?>
<?php
$selected = "home.php";
$page = "track_order.php";
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CMS | Track Order</title>

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
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div id="loading"></div>
<div class="wrapper">
  <?php include('components/sidebar.php');?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Track Order</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Track Order</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
     
    <section class="content">
      <!-- Order Search Card -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">View Order Status</h3>
        </div>
        <form role="form" id="frmData">
          <div class="card-body">
            <?php
              // Display session messages if any (adjust as needed)
              if(isset($_SESSION) && array_key_exists("QueryStatus",$_SESSION)) {
                  if($_SESSION["QueryStatus"]=="SAVED"){
                      echo '<div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Genuine Product!</h5>
                                Its a Genuine Product!
                             </div>';
                      $_SESSION["QueryStatus"]="";
                  }
                  if($_SESSION["QueryStatus"]=="UPDATED"){
                      echo '<div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Error!</h5>
                                Please Enter Product...!
                             </div>';
                      $_SESSION["QueryStatus"]="";
                  }
                  if($_SESSION["QueryStatus"]=="DELETED"){
                      echo '<div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Error!</h5>
                                Product Code Invalid or Not a genuine Product
                             </div>';
                      $_SESSION["QueryStatus"]="";
                  }
                  if($_SESSION["QueryStatus"]=="ERROR"){
                      echo '<div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Failed!</h5>
                                Record Not Updated...!
                             </div>';
                      $_SESSION["QueryStatus"]="";
                  }
              }
            ?>
            <div class="form-group">
              <label for="Code">Waybill (AWB) / Order Id</label>
              <input type="text" class="form-control" id="Code" name="Code" placeholder="Waybill (AWB) / Order Id">
            </div>
            <div class="alert alert-danger" id="ErrorBox" style="display:none;">
              <p id="ErrorMessage"></p>
            </div>
          </div>
          <div class="card-footer">
            <button type="button" class="btn btn-primary" onClick="save_data();">Submit</button>
          </div>
        </form>
      </div>

      <!-- Timeline Card -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Order Timeline</h3>
        </div>
        <div class="card-body">
          <div id="orderDetails" class="mt-3"></div>
        </div>
      </div>
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
<!-- bs-custom-file-input -->
<script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false
      /*"buttons": ["excel", "pdf"]*/
    })/*.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)')*/;
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
<script>
function save_data() {
    var code = $('#Code').val().trim();

    if (!code) {
        $('#ErrorMessage').text('Please enter a Waybill or Order Id.');
        $('#ErrorBox').show();
        return;
    }

    $('#ErrorBox').hide();
    $('#orderDetails').html('<div class="alert alert-info">Fetching tracking info...</div>');

    $.ajax({
        url: 'track_api.php',
        type: 'GET',
        data: { awb: code },
        dataType: 'json',
        success: function (data) {
            const detail = data.latest_status && data.latest_status.ScanDetail;

            if (detail) {
                var edd = data.estimated_delivery && data.estimated_delivery !== "Not available"
                    ? new Date(data.estimated_delivery).toLocaleDateString()
                    : "Not available";

                var statusDate = new Date(detail.ScanDateTime || detail.StatusDateTime).toLocaleString();

                var html = `
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Latest Tracking Status</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> ${detail.Scan}</p>
                        <p><strong>Date:</strong> ${statusDate}</p>
                        <p><strong>Location:</strong> ${detail.ScannedLocation}</p>
                        <p><strong>Remarks:</strong> ${detail.Instructions}</p>
                        <p><strong>Estimated Delivery:</strong> ${edd}</p>
                    </div>
                </div>`;

                $('#orderDetails').html(html);
            } else {
                $('#orderDetails').html('');
                $('#ErrorMessage').text(data.error || 'No tracking info found.');
                $('#ErrorBox').show();
            }
        },
        error: function () {
            $('#orderDetails').html('');
            $('#ErrorMessage').text('Error fetching tracking data.');
            $('#ErrorBox').show();
        }
    });
}
</script>


<script type="text/javascript" src="js/common_functions.js"></script>
<script src="js/jquery.form.js" type="text/javascript"></script>
    <!--Big blue-->
<div id="mdb-preloader" class="flex-center">
  <div class="preloader-wrapper active">
    <div class="spinner-layer spinner-blue-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div>
      <div class="gap-patch">
        <div class="circle"></div>
      </div>
      <div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Confirmation</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        	<p><b>Are you sure you want to delete ?</b></p>
          <form action="delete_category.php" method="post" name="frmDeleteFormData"  id="frmDeleteFormData" enctype="multipart/form-data">
              <input type="hidden" id="DCategoryId" name="DCategoryId" />
        </div>
        <div class="modal-footer">
          <input type="button" class="btn btn-danger"  value="Yes"  onClick="javascript:delete_info();">
          </form>
          <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
</body>
</html>
<?php
// }
// else
// {
// 	header('Location: index.php');
// }
?>
<?php
include('includes/urls.php');
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
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRM | This Month Orders</title>

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
            <h1 class="m-0">This Month Orders</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">This Month Orders</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
	<?php
    $OrderId = "";
    $OrderDate = "";

    $FieldNames = array("Id", "OrderId", "CustomerId", "OrderDate", "Amount", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType");
    $Fields = implode(",", $FieldNames);

    // Calculate the first and last day of the current month
    $firstDay = date('Y-m-01'); // e.g., "2025-02-01"
    $lastDay = date('Y-m-t');   // e.g., "2025-02-28" or "2025-02-29" for leap year

    // Fetch all orders with OrderDate between the first and last day of the current month
    $ParamArray = array($firstDay, $lastDay);
    $all_orders = $obj->MysqliSelect1(
        "SELECT $Fields FROM order_master WHERE OrderDate BETWEEN ? AND ?",
        $FieldNames,
        "ss",
        $ParamArray
    );


?>

    <!-- Main content List-->
    <section class="content">
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">This Month Orders List</h3>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Customer ID</th>
                            <th>Date</th>
                            <th>Amount ($)</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Ship Address</th>
                            <th>Payment Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_orders)) : ?>
                            <?php foreach ($all_orders as $order) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['Id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['OrderId']); ?></td>
                                    <td><?php echo htmlspecialchars($order['CustomerId']); ?></td>
                                    <td><?php echo date("d-m-Y", strtotime($order['OrderDate'])); ?></td>
                                    <td><?php echo number_format($order['Amount'], 2); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo strtolower($order['PaymentStatus']) === 'paid' 
                                                ? 'badge-success' 
                                                : (in_array(strtolower($order['PaymentStatus']), ['pending', 'processing']) 
                                                    ? 'badge-warning' 
                                                    : 'badge-danger'); ?>">
                                            <?php echo ucfirst(htmlspecialchars($order['PaymentStatus'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            <?php echo strtolower($order['OrderStatus']) === 'delivered' 
                                                ? 'badge-success' 
                                                : (strtolower($order['OrderStatus']) === 'pending' 
                                                    ? 'badge-warning' 
                                                    : (strtolower($order['OrderStatus']) === 'shipped' 
                                                        ? 'badge-info' 
                                                        : 'badge-danger')); ?>">
                                            <?php echo ucfirst(htmlspecialchars($order['OrderStatus'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['ShipAddress']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo strtolower($order['PaymentType']) === 'online' 
                                                ? 'badge-success'  // Blue for Online payments
                                                : (strtolower($order['PaymentType']) === 'cod' 
                                                    ? 'badge-primary'  // Grey for COD
                                                    : 'badge-dark'); // Default for other cases
                                            ?>">
                                            <?php echo ucfirst(htmlspecialchars($order['PaymentType'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_details.php?OrderId=<?php echo urlencode($order['OrderId']); ?>" 
                                        class="btn btn-primary btn-sm">
                                            View Order
                                        </a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="10" class="text-center">No orders found for this month.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
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
<script type="text/javascript">
function save_data()
{
	
	var AllRight=1;
	var errorstring="";
	
	
	var CategoryName=document.getElementById("CategoryName").value;
  
	if(CategoryName=="")
	{
		AllRight=0;
		$('#CategoryName').addClass('is-invalid');
		

	}
	else
	{
		$("#CategoryName").removeClass("is-invalid");
	}

	if(AllRight==1)
	{   
		$("#frmData").ajaxForm({
						dataType:'json',
						beforeSend:function(){
										document.getElementById("loading").style.display="block";
											},
						success: function(data){
													if(data.response=="S")
													{	
														window.location="category.php";
													}
													else
													{
														
														$('#ErrorMessage').html(data.msg);
                                                		$('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
                                                		document.getElementById("ErrorMessage").style.display="block";
                                                		$("#ErrorMessage").delay(1000).fadeOut(400);
                                                		document.getElementById("loading").style.display="none";
													}
												}
		}).submit();
	}
	else
	{
		errorstring="Category name is mendetory..!";

		$('#ErrorMessage').html(errorstring);
		$('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
		document.getElementById("ErrorMessage").style.display="block";
		$("#ErrorMessage").delay(1000).fadeOut(400);
	}
}
function fundelete(id1)
{
	 document.getElementById("DCategoryId").value=id1;
	//$("html, body").animate({ scrollTop: 0 }, 600);
}
function delete_info()
{
	$("#frmDeleteFormData").ajaxForm({
						dataType:'json',
						beforeSend:function(){
								document.getElementById("loading").style.display="block";
											},
						success: function(data){
													if(data.response=="D")
													{	
														window.location="category.php";
													}
													else
													{
														document.getElementById("ErrorMessage").innerHTML=data.msg;
														document.getElementById("loading").style.display="none";
	
													}
												}
		}).submit();
}
$( document ).ready(function() {
   bsCustomFileInput.init();
   $(".alert").delay(1000).fadeOut(400);
});
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
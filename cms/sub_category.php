<?php
include('includes/urls.php');
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include("database/dbconnection.php");
$obj = new main();
$obj->connection();
sec_session_start();
if (login_check($mysqli) == true) 
{
	//getPassword("admin",$mysqli);
?>
<?php
$selected = "catlog.php";
$page = "sub_category.php";
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRM | Categories</title>

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
            <h1 class="m-0">Categories</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Categories</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
	<?php
	    $SubCategoryId = "";
		$CategoryId="";
		$PhotoPath = "Choose File";
    $SubCategoryName = "";
			
		if(isset($_GET) && array_key_exists("SubCategoryId",$_GET))
		{
			$FieldNames=array("SubCategoryId","SubCategoryName","PhotoPath");
			$ParamArray=array();
			$ParamArray[0]=$_GET["SubCategoryId"];
			$Fields=implode(",",$FieldNames);
			$single_data=$obj->MysqliSelect1("Select ".$Fields." from sub_category where SubCategoryId= ? ",$FieldNames,"i",$ParamArray);
			
			$SubCategoryId=$single_data[0]["SubCategoryId"];
			$SubCategoryName=$single_data[0]["SubCategoryName"];
			$PhotoPath = $single_data[0]["PhotoPath"];
		}
      
        
	  ?>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add/Edit Sub Category</h3>
        </div>
        <form role="form" action="exe_save_sub_category.php" method="post" enctype="multipart/form-data" id="frmData">
        <div class="card-body">
     	<?php

				if(isset($_SESSION) && array_key_exists("QueryStatus",$_SESSION))
				{
					if($_SESSION["QueryStatus"]=="SAVED")
					{
						echo '<div class="alert alert-success alert-dismissible">
								  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
								  <h5><i class="icon fas fa-check"></i> Successful!</h5>
								  Record Saved Successfully...!
							   </div>';
						$_SESSION["QueryStatus"]="";
					}
					if($_SESSION["QueryStatus"]=="UPDATED")
					{
						echo '<div class="alert alert-success alert-dismissible">
								  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
								  <h5><i class="icon fas fa-check"></i> Successful!</h5>
								  Record Updated Successfully...!
							   </div>';
						$_SESSION["QueryStatus"]="";
					}
					if($_SESSION["QueryStatus"]=="DELETED")
					{
						echo '<div class="alert alert-success alert-dismissible">
								  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
								  <h5><i class="icon fas fa-check"></i> Successful!</h5>
								  Record Deleted Successfully...!
							   </div>';
						$_SESSION["QueryStatus"]="";
					}
					
					if($_SESSION["QueryStatus"]=="ERROR")
					{
						echo '<div class="alert alert-danger alert-dismissible">
								  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
								  <h5><i class="icon fas fa-check"></i> Failed!</h5>
								  Record Not Updated...!
							   </div>';
						$_SESSION["QueryStatus"]="";
					}
				}
		?>
        <input type="hidden" class="form-control" id="SubCategoryId" name="SubCategoryId" value="<?php echo $SubCategoryId;?>">
                                    
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">Sub Category Name</label>
                                          <input type="text" class="form-control" id="SubCategoryName" name="SubCategoryName" placeholder="Enter Sub-Category Name" value="<?php echo $SubCategoryName;?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Product Image (500px X 300px jpg image)</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="PhotoPath" name="PhotoPath">
                                                <label class="custom-file-label" for="PhotoPath"><?php echo htmlspecialchars($PhotoPath); ?></label>
                                            </div>
                                        </div>
              
        <div class="box-footer" id="ErrorBox">
                  <p id="ErrorMessage"></p>
              </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
           <button type="button" class="btn btn-primary" onClick="javascript:save_data();" >Submit</button>
        </div>
        </form>
        <!-- /.card-footer-->
      </div>
      <!-- /.card -->
    </section>
    <!-- Main content Add/Edit-->
    <!-- Main content List-->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Category List</h3>
        </div>
        <div class="card-body">
          <?php
            $FieldNames = array("SubCategoryId", "SubCategoryName", "PhotoPath");
            $ParamArray = array();
            $Fields = implode(",", $FieldNames);
            $all_data = $obj->MysqliSelect1("Select ".$Fields." from sub_category", $FieldNames, "", $ParamArray);
          ?>
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
              <th>Sub Category Id</th>
                <th>Sub Category Name</th>
                <th>Details</th>
                <th style="width:50px;">Edit</th>
                <th style="width:50px;">Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if(!empty($all_data)) {
                  for($i = 0; $i < count($all_data); $i++) {
                    echo '<tr>
                    <td>' . htmlspecialchars($all_data[$i]["SubCategoryId"]) . '</td>
                      <td>' . htmlspecialchars($all_data[$i]["SubCategoryName"]) . '</td>
                      
                      <td>
                        <br>
                        <img src="' . htmlspecialchars("images/products/" . $all_data[$i]["PhotoPath"]) . '" width="100" height="120">
                      </td>
                      <td>
                        <a href="sub_category.php?SubCategoryId=' . htmlspecialchars($all_data[$i]["SubCategoryId"]) . '">
                          <i class="btn btn-info btn-sm fa fa-edit fa-sm"></i>
                        </a>
                      </td>
                      <td>
                        <button onClick="javascript:fundelete(' . htmlspecialchars($all_data[$i]["SubCategoryId"]) . ');" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-default">
                          <i class="fa fa-trash fa-sm"></i>
                        </button>
                      </td>
                    </tr>';
                  }
                }
              ?>
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
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "buttons": ["excel", "pdf", "print"],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

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
	
	
	var SubCategoryName=document.getElementById("SubCategoryName").value;
	var PhotoPath = document.getElementById("PhotoPath").value;
  
	if(SubCategoryName=="")
	{
		AllRight=0;
		$('#SubCategoryName').addClass('is-invalid');
		

	}
	else
	{
		$("#SubCategoryName").removeClass("is-invalid");
	}
	
		if(PhotoPath=="")
	{
		AllRight=0;
		$('#PhotoPath').addClass('is-invalid');
		

	}
	else
	{
		$("#PhotoPath").removeClass("is-invalid");
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
														window.location="sub_category.php";
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
	 document.getElementById("DSubCategoryId").value=id1;
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
														window.location="sub_category.php";
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
          <form action="delete_sub_category.php" method="post" name="frmDeleteFormData"  id="frmDeleteFormData" enctype="multipart/form-data">
              <input type="hidden" id="DSubCategoryId" name="DSubCategoryId" />
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
}
else
{
	header('Location: index.php');
}
?>
<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('../includes/urls.php');
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
$page = "categories.php";
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EKrishiMart | Categories</title>

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
		$CategoryId="";
		$CategoryName="";
		$MetaTag="";
		$MetaKeywords="";
		$IsActive = "selected";
			
		$se = "checked";
		if(isset($_GET) && array_key_exists("CategoryId",$_GET))
		{
			$FieldNames=array("CategoryId","CategoryName","MetaTag","MetaKeywords","IsActive");
			$ParamArray=array();
			$ParamArray[0]=$_GET["CategoryId"];
			$Fields=implode(",",$FieldNames);
			$single_data=$obj->MysqliSelect1("Select ".$Fields." from category_master where CategoryId= ? ",$FieldNames,"i",$ParamArray);
			
			$CategoryId=$single_data[0]["CategoryId"];
			$CategoryName=$single_data[0]["CategoryName"];
			$MetaTag=$single_data[0]["MetaTag"];
			$MetaKeywords=$single_data[0]["MetaKeywords"];
			$IsActive=$single_data[0]["IsActive"];
			if($IsActive == "N")
			{
				$se = "";
			}
		}
	  ?>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add/Edit Category</h3>
        </div>
        <form role="form" action="exe_save_category.php" method="post" enctype="multipart/form-data" id="frmData">
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
				}
		?>
        <input type="hidden" class="form-control" id="CategoryId" name="CategoryId" value="<?php echo $CategoryId;?>">
              <div class="form-group">
                <label for="exampleInputEmail1">Category Name</label>
                <input type="text" class="form-control" id="CategoryName" name="CategoryName" placeholder="Enter Category Name" value="<?php echo $CategoryName;?>">
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <!-- text input -->
                  <div class="form-group">
                    <label>Meta Tags (Saperate With Quama ",")</label>
                    <input type="text" class="form-control" id="MetaTag" name="MetaTag" placeholder="Enter Meta Tag" value="<?php echo $MetaTag;?>">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Meta Keywords (Saperate With Quama ",")</label>
                    <input type="text" class="form-control" id="MetaKeywords" name="MetaKeywords" placeholder="Enter Meta Keywords" value="<?php echo $MetaKeywords;?>">
                  </div>
                </div>
              </div>
              <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input class="custom-control-input" type="checkbox" id="IsActive" name="IsActive" <?php echo $se;?>>
                      <label for="IsActive" class="custom-control-label">Is Active...?</label>
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
			$FieldNames=array("CategoryId","CategoryName","MetaTag","MetaKeywords","IsActive");
			$ParamArray=array();
			$Fields=implode(",",$FieldNames);
			$all_data=$obj->MysqliSelect1("Select ".$Fields." from category_master ",$FieldNames,"",$ParamArray);
			
		  ?>
          <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>CategoryName</th>
                    <th>IsActice</th>
                    <th style="width:100px;">Edit</th>
                    <th style="width:100px;">Delete</th>
                  </tr>
                  </thead>
                  <tbody>
                   	<?php
						for($i=0; $i<count($all_data); $i++)
						{
							$status = "Disabled";
							if($all_data[$i]["IsActive"] == "Y")
							{
								$status = "Enabled";
							}
							echo '<tr>
									<td>'.$all_data[$i]["CategoryName"].'</td>
									<td>'.$status.'</td>
									<td><a href="categories.php?CategoryId='.$all_data[$i]["CategoryId"].'"><i class="btn btn-info fa fa-edit"></i></a></td>
									<td><button onClick="javascript:fundelete('.$all_data[$i]["CategoryId"].');" type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-default"><i class="fa fa-trash"></i></button></td>
								  </tr>';
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
    });/*.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)')*/
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
	}

	if(AllRight==1)
	{   
		$("#frmData").ajaxForm({
						dataType:'json',
						beforeSend:function(){
										
											},
						success: function(data){
													if(data.response=="S")
													{	
														window.location="categories.php";
													}
													else
													{
														document.getElementById("ErrorMessage").innerHTML=data.msg;
	
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
										
											},
						success: function(data){
													if(data.response=="D")
													{	
														window.location="categories.php";
													}
													else
													{
														document.getElementById("ErrorMessage").innerHTML=data.msg;
	
													}
												}
		}).submit();
}
</script>
<script type="text/javascript" src="js/common_functions.js"></script>
<script src="js/jquery.form.js" type="text/javascript"></script>
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
}
else
{
	header('Location: index.php');
}
?>
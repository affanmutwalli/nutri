<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include("database/dbconnection.php");
$obj = new main();
$obj->connection();
sec_session_start();

if (login_check($mysqli) == true)
{
	//getPassword("admin",$mysqli);

	// Handle AJAX banner sorting request
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_banner_order') {
	    header('Content-Type: application/json');

	    if (isset($_POST["bannerOrder"]) && !empty($_POST["bannerOrder"])) {
	        try {
	            $bannerOrder = json_decode($_POST["bannerOrder"], true);

	            if (!$bannerOrder || !is_array($bannerOrder)) {
	                echo json_encode(array("msg" => "Invalid banner order data", "response" => "E"));
	                exit;
	            }

	            $mysqli->autocommit(FALSE);
	            $success = true;

	            foreach ($bannerOrder as $item) {
	                if (isset($item['bannerId']) && isset($item['position'])) {
	                    $bannerId = intval($item['bannerId']);
	                    $position = intval($item['position']);

	                    $stmt = $mysqli->prepare("UPDATE banners SET Position = ? WHERE BannerId = ?");
	                    if ($stmt) {
	                        $stmt->bind_param("ii", $position, $bannerId);
	                        if (!$stmt->execute()) {
	                            $success = false;
	                            break;
	                        }
	                        $stmt->close();
	                    } else {
	                        $success = false;
	                        break;
	                    }
	                }
	            }

	            if ($success) {
	                $mysqli->commit();
	                echo json_encode(array("msg" => "Banner order updated successfully", "response" => "S"));
	            } else {
	                $mysqli->rollback();
	                echo json_encode(array("msg" => "Error updating banner order", "response" => "E"));
	            }

	            $mysqli->autocommit(TRUE);
	            exit;

	        } catch (Exception $e) {
	            $mysqli->rollback();
	            $mysqli->autocommit(TRUE);
	            echo json_encode(array("msg" => "Error: " . $e->getMessage(), "response" => "E"));
	            exit;
	        }
	    } else {
	        echo json_encode(array("msg" => "No banner order data provided", "response" => "E"));
	        exit;
	    }
	}
?>
<?php
$selected = "home.php";
$page="banners.php"
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CMS | Banners</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- jQuery UI for sortable -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <!-- Custom styles for banner sorting -->
  <style>
    .drag-handle {
        color: #6c757d;
        transition: color 0.2s;
    }
    .drag-handle:hover {
        color: #007bff;
        cursor: move;
    }
    .sortable-row {
        transition: all 0.2s ease;
    }
    .ui-state-highlight {
        height: 80px;
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
    }
    .ui-sortable-helper {
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        opacity: 0.9;
    }
    #sortable-banners tr:hover {
        background-color: #f8f9fa;
    }
  </style>
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
            <h1 class="m-0">Banners</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Banners</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
	 <?php
    $BannerId = "";
    $PhotoPath = "Choose File";
    $Title = "";
    $ShortDescription = "";
    $ShowButton = 1; // Default to show button
    $Position = 0; // Default position

    if(isset($_GET["BannerId"])){
    $FieldNames = array("BannerId", "PhotoPath", "Title", "ShortDescription", "ShowButton", "Position");
    $ParamArray = array($_GET["BannerId"]);
    $Fields = implode(",", $FieldNames);

    // Assuming $obj is properly instantiated and MysqliSelect1 is defined
    $single_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM banners WHERE BannerId = ?", $FieldNames, "i", $ParamArray);

    if ($single_data && count($single_data) > 0) {
      $BannerId = $single_data[0]["BannerId"];
      $PhotoPath = $single_data[0]["PhotoPath"];
      $Title = $single_data[0]["Title"];
      $ShortDescription = $single_data[0]["ShortDescription"];
      $ShowButton = $single_data[0]["ShowButton"];
      $Position = $single_data[0]["Position"];
    }
  }
  ?>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add/Edit Banners</h3>
        </div>
        <form role="form" action="exe_save_banners.php" method="post" enctype="multipart/form-data" id="frmData">
          <div class="card-body">
            <?php
    
              // Check if there is a status in the session
              if (isset($_SESSION['QueryStatus'])) {
                // Display appropriate alert based on the session status
                $message = '';
                if ($_SESSION['QueryStatus'] == "SAVED") {
                  $message = "Record Saved Successfully...!";
                } elseif ($_SESSION['QueryStatus'] == "UPDATED") {
                  $message = "Record Updated Successfully...!";
                } elseif ($_SESSION['QueryStatus'] == "DELETED") {
                  $message = "Record Deleted Successfully...!";
                }
    
                if ($message) {
                  echo '<div class="alert alert-success alert-dismissible">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                          <h5><i class="icon fas fa-check"></i> Successful!</h5>
                          ' . $message . '
                        </div>';
                  $_SESSION['QueryStatus'] = ""; // Clear the session status
                }
              }
            ?>
    
            <input type="hidden" class="form-control" id="BannerId" name="BannerId" value="<?php echo htmlspecialchars($BannerId); ?>">
    
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="Title">Banner Title <small class="text-muted">(Optional)</small></label>
                  <input type="text" class="form-control" id="Title" name="Title" placeholder="Enter Title (Optional)" value="<?php echo htmlspecialchars($Title); ?>">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="ShortDescription">Banner Short-Description <small class="text-muted">(Optional)</small></label>
                  <input type="text" class="form-control" id="ShortDescription" name="ShortDescription" placeholder="Enter Short Description (Optional)" value="<?php echo htmlspecialchars($ShortDescription); ?>">
                </div>
              </div>
    
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Product Image (500px X 300px jpg image)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="PhotoPath" name="PhotoPath" onchange="updateFileLabel()">
                        <label class="custom-file-label" for="PhotoPath"><?php echo htmlspecialchars($PhotoPath); ?></label>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="ShowButton">Show "Shop Now" Button</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="ShowButton" name="ShowButton" value="1" <?php echo ($ShowButton == 1) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="ShowButton">Display "Shop Now" button on this banner</label>
                    </div>
                    <small class="form-text text-muted">Toggle this to show or hide the "Shop Now" button for this banner image.</small>
                </div>
            </div>

            </div>

            <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="Position">Display Order</label>
                    <input type="number" class="form-control" id="Position" name="Position" value="<?php echo $Position; ?>" min="0" step="1">
                    <small class="form-text text-muted">Lower numbers appear first. Use 0 for highest priority.</small>
                </div>
            </div>
            </div>
    
            <div class="box-footer" id="ErrorBox">
              <p id="ErrorMessage"></p>
            </div>
    
            <div class="row">
              <div class="col-sm-12">
                <?php
                  if ($PhotoPath != "Choose File" && !empty($PhotoPath)) {
                    echo '<img alt="Banner Image" src="images/banners/' . $PhotoPath . '" width="100%" height="100%" style="margin-bottom: 15px;">';
                  } else {
                    echo '<img alt="Placeholder Image" src="images/placeholder.png" width="100%" height="100%" style="margin-bottom: 15px;">';
                  }
                ?>
              </div>
            </div>
            <!-- /.card-body -->
            
            <div class="card-footer">
              <button type="button" class="btn btn-primary" onClick="javascript:save_data();">Submit</button>
            </div>
          </form>
          <!-- /.card-footer-->
        </div>
        <!-- /.card -->
      </section>
    <!-- Main content List-->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Image List</h3>
          <div class="card-tools">
            <small class="text-muted">Drag and drop rows to reorder banners</small>
          </div>
        </div>
        <div class="card-body">
          <?php
			$FieldNames=array("BannerId","PhotoPath","Title","ShortDescription","ShowButton","Position");
			$ParamArray=array();
			$Fields=implode(",",$FieldNames);
			$all_data=$obj->MysqliSelect1("Select ".$Fields." from banners ORDER BY Position ASC, BannerId ASC",$FieldNames,"",$ParamArray);

		  ?>
          <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                  	<th style="width:40px;">Order</th>
                  	<th style="width:120px;">Image</th>
                  	<th style="width:120px;">Details</th>
                  	<th style="width:80px;">Shop Now Button</th>
                  	<th style="width:10px;">Edit</th>
                    <th style="width:10px;">Delete</th>
                  </tr>
                  </thead>
                  <tbody id="sortable-banners">
                   	<?php
                   	    if($all_data != NULL)
                   	    {
    						for($i=0; $i<count($all_data); $i++)
    						{

    							$showButtonStatus = ($all_data[$i]["ShowButton"] == 1) ?
    								'<span class="badge badge-success"><i class="fas fa-check"></i> Enabled</span>' :
    								'<span class="badge badge-secondary"><i class="fas fa-times"></i> Disabled</span>';

    							echo '<tr data-banner-id="'.$all_data[$i]["BannerId"].'" class="sortable-row">
    									<td class="text-center drag-handle" style="cursor: move;"><i class="fas fa-grip-vertical"></i><br><small>'.($all_data[$i]["Position"] ?? 0).'</small></td>
    									<td><img src="images/banners/'.$all_data[$i]["PhotoPath"].'" width="100%"></td>
    									<td><p> Title : '.$all_data[$i]["Title"].'<br> Description : '.$all_data[$i]["ShortDescription"].'</p></td>
    									<td class="text-center">'.$showButtonStatus.'</td>
    									<td><a href="banners.php?BannerId='.$all_data[$i]["BannerId"].'"><i class="btn btn-info btn-sm fa fa-edit fa-sm"></i></a></td>
    									<td><button onClick="javascript:fundelete('.$all_data[$i]["BannerId"].');" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-default"><i class="fa fa-trash fa-sm"></i></button></td>
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
    <!-- Main content Add/Edit-->
    
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
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>

<!-- bs-custom-file-input -->
<script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
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

<!-- jQuery UI for sortable -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<script>
  $(function () {
  	//Initialize Select2 Elements
    $('.select2').select2()
	// Summernote
	
    
    $('#Description').on('summernote.keydown', function(we, e) {
        var t = e.currentTarget.innerText;
        if (t.length >= 100) {
            //delete key
            if (e.keyCode != 8)
              e.preventDefault();
            // add other keys ...
          }
    });
    
    $('#Description').on('summernote.paste', function(we, e) {
        var t = e.currentTarget.innerText;
      var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
      e.preventDefault();
      var all = t + bufferText;
      document.execCommand('insertText', false, all.trim().substring(0, 100));
      if (typeof callbackMax == 'function') {
        callbackMax(max - t.length);
      }
    });
    
    $("#example1").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "ordering": false, // Disable default ordering for drag-drop
      "paging": false // Disable paging for better drag-drop experience
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

    // Initialize sortable functionality
    initializeBannerSorting();
  });
</script>
<script type="text/javascript">
function save_data()
{
	
	var AllRight=1;
	var errorstring="";
	
	var Title = document.getElementById("Title").value;
    var ShortDescription = document.getElementById("ShortDescription").value;
    var PhotoPath = document.getElementById("PhotoPath").value;
    var BannerId = document.getElementById("BannerId").value;

    	// Remove validation for Title and Description - they are now optional
    	$("#Title").removeClass("is-invalid");
    	$("#ShortDescription").removeClass("is-invalid");

    	// Only check if image is required (for new banners)
    	if(BannerId == "" && PhotoPath == "")
    	{
    		AllRight=0;
    		$('#PhotoPath').addClass('is-invalid');
    		errorstring="Banner Image is mandatory for new banners..!";
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
														window.location="banners.php";
													}
													else
													{
														$('#ErrorMessage').html(data.msg);
														$('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
														document.getElementById("ErrorMessage").style.display="block";
														$("#ErrorMessage").delay(1000).fadeOut(400);
														document.getElementById("loading").style.display="none";
													}
												},
						error: function(xhr, status, error) {
													console.log("AJAX Error: " + status + " - " + error);
													console.log("Response: " + xhr.responseText);
													$('#ErrorMessage').html("Error uploading banner. Please try again.");
													$('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
													document.getElementById("ErrorMessage").style.display="block";
													$("#ErrorMessage").delay(3000).fadeOut(400);
													document.getElementById("loading").style.display="none";
												}
		}).submit();
	}
	else
	{
		

		$('#ErrorMessage').html(errorstring);
		$('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
		document.getElementById("ErrorMessage").style.display="block";
		$("#ErrorMessage").delay(1000).fadeOut(400);
		
	}
}
function updateFileLabel() {
    var input = document.getElementById("PhotoPath");
    var label = document.querySelector("label[for='PhotoPath']");
    var fileName = input.files.length > 0 ? input.files[0].name : "Choose File";

    label.textContent = fileName; // Update label with the selected file name

    // Validate the input
    if (input.value === "") {
        $('#PhotoPath').addClass('is-invalid');
    } else {
        $('#PhotoPath').removeClass('is-invalid');
    }
}

function fundelete(id1)
{
	 document.getElementById("DBannerId").value=id1;
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
														window.location="banners.php";
													}
													else
													{
														document.getElementById("ErrorMessage").innerHTML=data.msg;
														document.getElementById("loading").style.display="none";
													}
												},
						error: function(xhr, status, error) {
													console.log("Delete AJAX Error: " + status + " - " + error);
													console.log("Response: " + xhr.responseText);
													document.getElementById("ErrorMessage").innerHTML="Error deleting banner. Please try again.";
													document.getElementById("loading").style.display="none";
												}
		}).submit();
}
$( document ).ready(function() {
    bsCustomFileInput.init();
   $(".alert").delay(1000).fadeOut(400);

});

// Banner Sorting Functions
function initializeBannerSorting() {
    // Make the table body sortable
    $("#sortable-banners").sortable({
        handle: ".drag-handle",
        cursor: "move",
        placeholder: "ui-state-highlight",
        helper: function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width());
            });
            return $helper;
        },
        update: function(event, ui) {
            updateBannerOrder();
        }
    });

    // Add visual feedback
    $("#sortable-banners").disableSelection();
}

function updateBannerOrder() {
    var bannerOrder = [];
    $("#sortable-banners tr").each(function(index) {
        var bannerId = $(this).data('banner-id');
        if (bannerId) {
            bannerOrder.push({
                bannerId: bannerId,
                position: index
            });
        }
    });

    // Send AJAX request to update order
    $.ajax({
        url: 'banners.php',
        type: 'POST',
        data: {
            action: 'update_banner_order',
            bannerOrder: JSON.stringify(bannerOrder)
        },
        dataType: 'json',
        success: function(response) {
            if (response.response === 'S') {
                // Update position numbers in the UI
                $("#sortable-banners tr").each(function(index) {
                    $(this).find('.drag-handle small').text(index);
                });

                // Show success message briefly
                showSortMessage('Banner order updated successfully!', 'success');
            } else {
                showSortMessage('Error updating banner order: ' + response.msg, 'error');
            }
        },
        error: function(xhr, status, error) {
            showSortMessage('Error updating banner order. Please try again.', 'error');
            console.log("Sort AJAX Error: " + status + " - " + error);
        }
    });
}

function showSortMessage(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>';

    $('.card-header').after(alertHtml);

    // Auto-hide after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 3000);
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
          <form action="delete_banners.php" method="post" name="frmDeleteFormData"  id="frmDeleteFormData" enctype="multipart/form-data">
              <input type="hidden" id="DBannerId" name="DBannerId" />
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
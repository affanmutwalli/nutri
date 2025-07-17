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
$selected = "home.php";
$page = "blogs.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM | Blogs</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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
                            <h1 class="m-0">Blogs</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Blogs</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <?php
              $BlogId="";
              $BlogTitle="";
              $BlogDate="";
              $Description = "";
              $IsActive = "Y";
              $ck = "checked";
              $SubCategoryId = "";
              
              $FieldNames = array("SubCategoryId", "SubCategoryName");
              $ParamArray = array();
              $Fields = implode(",", $FieldNames);
              $all_sub_categories = $obj->MysqliSelect1("Select " . $Fields . " from sub_category", $FieldNames, "", $ParamArray);
              
              if(isset($_GET) && array_key_exists("BlogId",$_GET))
              {
                $FieldNames=array("BlogId","BlogTitle","BlogDate","Description","IsActive","SubCategoryId");
                $ParamArray=array();
                $ParamArray[0]=$_GET["BlogId"];
                $Fields=implode(",",$FieldNames);
                $single_data=$obj->MysqliSelect1("Select ".$Fields." from blogs_master where BlogId= ? ",$FieldNames,"i",$ParamArray);
                
                $BlogId=$single_data[0]["BlogId"];
                $BlogTitle=$single_data[0]["BlogTitle"];
                $BlogDate=date("d-m-Y",strtotime($single_data[0]["BlogDate"]));
                $Description=$single_data[0]["Description"];
                $IsActive=$single_data[0]["IsActive"];
                $SubCategoryId = $single_data[0]["SubCategoryId"];
                if($IsActive == "N")
                {
                  $ck = "";
                }
                  // If SubCategoryId is empty, fetch all subcategories
                  $FieldNames = array("SubCategoryId", "SubCategoryName");
                  $ParamArray = array();  // No parameters for fetching all subcategories
                  $Fields = implode(",", $FieldNames);
                  $all_sub_categories = $obj->MysqliSelect1("SELECT " . $Fields . " FROM sub_category", $FieldNames, "", $ParamArray);

                
              }
              ?>
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add/Edit Blogs</h3>
                    </div>
                    <form role="form" action="exe_save_blogs.php" method="post" enctype="multipart/form-data"
                        id="frmData">
                        <div class="card-body">
                            <?php
                                    if (isset($_SESSION) && array_key_exists("QueryStatus", $_SESSION)) {
                                        if ($_SESSION["QueryStatus"] == "SAVED") {
                                            echo '<div class="alert alert-success alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h5><i class="icon fas fa-check"></i> Successful!</h5>
                                                Record Saved Successfully...!
                                            </div>';
                                            $_SESSION["QueryStatus"] = "";
                                        }
                                        if ($_SESSION["QueryStatus"] == "UPDATED") {
                                            echo '<div class="alert alert-success alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h5><i class="icon fas fa-check"></i> Successful!</h5>
                                                Record Updated Successfully...!
                                            </div>';
                                            $_SESSION["QueryStatus"] = "";
                                        }
                                        if ($_SESSION["QueryStatus"] == "DELETED") {
                                            echo '<div class="alert alert-success alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h5><i class="icon fas fa-check"></i> Successful!</h5>
                                                Record Deleted Successfully...!
                                            </div>';
                                            $_SESSION["QueryStatus"] = "";
                                        }
                                    }
                                    ?>
                            <input type="hidden" class="form-control" id="BlogId" name="BlogId"
                                value="<?php echo $BlogId; ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="BlogTitle">Blog Title</label>
                                        <input type="text" class="form-control" id="BlogTitle" name="BlogTitle"
                                            placeholder="Enter Blog Title" value="<?php echo $BlogTitle; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="BlogDate" name="BlogDate"
                                                data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy"
                                                value="<?php echo $BlogDate; ?>" data-mask>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="SubCategoryId">Sub Category</label>
                                        <select class="form-control" id="SubCategoryId" name="SubCategoryId">
                                            <option value="">Select Sub Category</option>
                                            <?php if (!empty($all_sub_categories)) : ?>
                                            <?php foreach ($all_sub_categories as $sub_category) : ?>
                                            <option
                                                value="<?php echo htmlspecialchars($sub_category['SubCategoryId']); ?>"
                                                <?php echo ($sub_category['SubCategoryId'] == $SubCategoryId) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sub_category['SubCategoryName']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                            <?php else : ?>
                                            <option value="">No Subcategories Available</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="Description">Blog Content (Supports headings, lists, code
                                            blocks)</label>
                                        <textarea id="Description" name="Description" class="form-control"
                                            rows="10"><?php echo $Description; ?></textarea>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Blog Image (1080px X 620px jpg image)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="PhotoPath"
                                                name="PhotoPath">
                                            <label class="custom-file-label" for="PhotoPath">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="IsActive"
                                                name="IsActive" <?php echo $ck; ?>>
                                            <label for="IsActive" class="custom-control-label">Is Active...?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer" id="ErrorBox">
                                <p id="ErrorMessage"></p>
                            </div>
                        </div>

                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary"
                                onClick="javascript:save_data();">Submit</button>
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
                        <h3 class="card-title">Blogs List</h3>
                    </div>
                    <div class="card-body">
                        <?php
			$FieldNames=array("BlogId","BlogTitle","BlogDate","Description","PhotoPath","IsActive");
			$ParamArray=array();
			$Fields=implode(",",$FieldNames);
			$all_data=$obj->MysqliSelect1("Select ".$Fields." from blogs_master ",$FieldNames,"",$ParamArray);
			
		  ?>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>

                                    <th style="width:50px;">Blog Image</th>
                                    <th>Blog Title</th>
                                    <th>Blog Date</th>
                                    <th style="width:50px;">IsActice</th>
                                    <th style="width:50px;">Edit</th>
                                    <th style="width:50px;">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    if($all_data){
						for($i=0; $i<count($all_data); $i++)
						{
							$status = "Disabled";
							if($all_data[$i]["IsActive"] == "Y")
							{
								$status = "Enabled";
							}
							echo '<tr>
									<td><img alt="'.$all_data[$i]["BlogTitle"].'" src="images/blogs/'.$all_data[$i]["PhotoPath"].'" width="100"></td>
									<td>'.$all_data[$i]["BlogTitle"].'</td>
									<td>'.date("d-m-Y",strtotime($all_data[$i]["BlogDate"])).'</td>
									<td>'.$status.'</td>
									<td><a href="blogs.php?BlogId='.$all_data[$i]["BlogId"].'"><i class="btn btn-sm btn-info fa fa-edit fa-sm"></i></a></td>
									<td><button onClick="javascript:fundelete('.$all_data[$i]["BlogId"].');" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-default"><i class="fa fa-trash fa-sm"></i></button></td>
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
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bs-custom-file-input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
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
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js"></script>
    <script src="https://cdn.ckeditor.com/4.20.2/full-all/ckeditor.js"></script>
<script>
    CKEDITOR.replace('Description', {
        height: 400,
        extraPlugins: 'codesnippet',
        codeSnippet_theme: 'monokai_sublime',
        // Allow all content - don't filter out formatting
        allowedContent: true,
        // Preserve formatting when pasting
        pasteFromWordRemoveFontStyles: false,
        pasteFromWordRemoveStyles: false,
        // Enhanced toolbar with more formatting options
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Preview', 'Print'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
            '/',
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
            { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'CodeSnippet'] },
            '/',
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
        ],
        // Configure font sizes
        fontSize_sizes: '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px',
        // Configure fonts
        font_names: 'Arial/Arial, Helvetica, sans-serif;Times New Roman/Times New Roman, Times, serif;Courier New/Courier New, Courier, monospace;Georgia/Georgia, serif;Verdana/Verdana, Geneva, sans-serif;Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;Tahoma/Tahoma, Geneva, sans-serif;Impact/Impact, Charcoal, sans-serif;Comic Sans MS/Comic Sans MS, cursive'
    });
</script>
    <script>
    $(function() {
        //Initialize Select2 Elements
        $('.select2').select2()
        //Datemask dd/mm/yyyy
        $('#BlogDate').inputmask('dd-mm-yyyy', {
            'placeholder': 'dd-mm-yyyy'
        })
        // Summernote
        $('.textarea').summernote()
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false
            /*"buttons": ["excel", "pdf"]*/
        }); /*.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)')*/
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
    function save_data() {

        var AllRight = 1;
        var errorstring = "";

        var BlogId = document.getElementById("BlogId").value;
        var BlogTitle = document.getElementById("BlogTitle").value;
        var PhotoPath = document.getElementById("PhotoPath").value;
        if (BlogId == "") {
            if (BlogTitle == "") {
                AllRight = 0;
                $('#BlogTitle').addClass('is-invalid');
            } else {
                $("#BlogTitle").removeClass("is-invalid");
            }

            if (PhotoPath == "") {
                AllRight = 0;
                $('#PhotoPath').addClass('is-invalid');


            } else {
                $("#PhotoPath").removeClass("is-invalid");
            }
        } else {
            if (BlogTitle == "") {
                AllRight = 0;
                $('#BlogTitle').addClass('is-invalid');
            } else {
                $("#BlogTitle").removeClass("is-invalid");
            }
        }


        if (AllRight == 1) {
            console.log("Form validation passed, submitting...");
            $("#frmData").ajaxForm({
                dataType: 'json',
                beforeSend: function() {
                    console.log("AJAX request starting...");
                    document.getElementById("loading").style.display = "block";
                },
                success: function(data) {
                    console.log("AJAX response received:", data);
                    if (data.response == "S") {
                        console.log("Success response, redirecting...");
                        window.location = "blogs.php?updated=" + Date.now();
                    } else {
                        console.log("Error response:", data.msg);
                        $('#ErrorMessage').html(data.msg);
                        $('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
                        document.getElementById("ErrorMessage").style.display = "block";
                        $("#ErrorMessage").delay(1000).fadeOut(400);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX error:", error);
                    console.log("Response text:", xhr.responseText);
                    $('#ErrorMessage').html("Error submitting form: " + error);
                    $('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
                    document.getElementById("ErrorMessage").style.display = "block";
                }
            }).submit();
        } else {
            errorstring = "Blog Title & Image is mendetory..!";

            $('#ErrorMessage').html(errorstring);
            $('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
            document.getElementById("ErrorMessage").style.display = "block";
            $("#ErrorMessage").delay(1000).fadeOut(400);
        }
    }

    function fundelete(id1) {
        document.getElementById("DBlogId").value = id1;
        //$("html, body").animate({ scrollTop: 0 }, 600);
    }

    function delete_info() {
        $("#frmDeleteFormData").ajaxForm({
            dataType: 'json',
            beforeSend: function() {
                document.getElementById("loading").style.display = "block";
            },
            success: function(data) {
                if (data.response == "D") {
                    window.location = "blogs.php";
                } else {
                    document.getElementById("ErrorMessage").innerHTML = data.msg;

                }
            }
        }).submit();
    }
    $(document).ready(function() {
        bsCustomFileInput.init();
        $(".alert").delay(1000).fadeOut(400);

    });
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
                    <form action="delete_blogs.php" method="post" name="frmDeleteFormData" id="frmDeleteFormData"
                        enctype="multipart/form-data">
                        <input type="hidden" id="DBlogId" name="DBlogId" />
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-danger" value="Yes" onClick="javascript:delete_info();">
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
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
    
    $selected = "product_details.php";
    $page = "Ingredients.php";
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM | Ingredient</title>

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
                            <h1 class="m-0">Ingredients</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Ingredients</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <?php
                $IngredientId = "";
                $ProductId = "";
                $PhotoPath = "Choose File";
                $IngredientName = "";

                // Fetch all products for dropdown
                $FieldNames = array("ProductId", "ProductName");
                $all_products = $obj->MysqliSelect1("SELECT ProductId, ProductName FROM product_master", $FieldNames, "", []);

                if (isset($_GET["IngredientId"])) {
                    $FieldNames = array("IngredientId", "ProductId", "IngredientName", "PhotoPath");
                    $ParamArray = array($_GET["IngredientId"]);
                    $Fields = implode(",", $FieldNames);
                    $single_data = $obj->MysqliSelect1("SELECT $Fields FROM product_ingredients WHERE IngredientId = ?", $FieldNames, "i", $ParamArray);

                    $IngredientId = $single_data[0]["IngredientId"];
                    $ProductId = $single_data[0]["ProductId"];
                    $IngredientName = $single_data[0]["IngredientName"];
                    $PhotoPath = !empty($single_data[0]["PhotoPath"]) ? $single_data[0]["PhotoPath"] : "Choose File";

                }
            ?>
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add/Edit Ingredient</h3>
                    </div>
                    <form role="form" action="exe_save_sub_ingredient.php" method="post" enctype="multipart/form-data"
                        id="frmData">
                        <div class="card-body">
                            <?php
                                if (!empty($_SESSION["QueryStatus"])) {
                                    $status = $_SESSION["QueryStatus"];
                                    $alertType = ($status == "SAVED" || $status == "UPDATED" || $status == "DELETED") ? "success" : "danger";
                                    $statusMessage = [
                                        "SAVED" => "Record Saved Successfully...!",
                                        "UPDATED" => "Record Updated Successfully...!",
                                        "DELETED" => "Record Deleted Successfully...!",
                                        "ERROR" => "Record Not Updated...!"
                                    ];
                                    echo '<div class="alert alert-' . $alertType . ' alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                            <h5><i class="icon fas fa-check"></i> ' . ucfirst(strtolower($status)) . '!</h5>
                                            ' . $statusMessage[$status] . '
                                        </div>';
                                    $_SESSION["QueryStatus"] = "";
                                }
                                ?>

                            <input type="hidden" name="IngredientId"
                                value="<?php echo htmlspecialchars($IngredientId); ?>">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="ProductId">Select Product</label>
                                        <select class="form-control" id="ProductId" name="ProductId" required>
                                            <option value="">Select Product</option>
                                            <?php
                        if (!empty($all_products)) {
                            foreach ($all_products as $product) {
                                $selected = ($product['ProductId'] == $ProductId) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($product['ProductId']) . '" ' . $selected . '>' . htmlspecialchars($product['ProductName']) . '</option>';
                            }
                        } else {
                            echo '<option value="">No Product Available</option>';
                        }
                        ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="IngredientName">Ingredient Name</label>
                                <input type="text" class="form-control" id="IngredientName" name="IngredientName"
                                    placeholder="Enter Ingredient Name"
                                    value="<?php echo htmlspecialchars($IngredientName); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Ingredient Image (300x300px)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="PhotoPath" name="PhotoPath">
                                    <label class="custom-file-label"
                                        for="PhotoPath"><?php echo htmlspecialchars($PhotoPath); ?></label>
                                </div>
                            </div>

                            <div class="box-footer" id="ErrorBox">
                                <p id="ErrorMessage"></p>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
                        <h3 class="card-title">Ingredient List</h3>
                    </div>
                    <div class="card-body">
                        <?php
                            $FieldNames = array("IngredientId", "ProductId", "PhotoPath", "IngredientName");
                            $ParamArray = array();
                            $Fields = implode(",", $FieldNames);         
                            $ingredient_data = $obj->MysqliSelect1(
                            "SELECT " . $Fields . " FROM product_ingredients",
                                $FieldNames,
                                "",
                                $ParamArray
                            );
                        ?>

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Ingredient ID</th>
                                    <th>Product Name</th>
                                    <th>Ingredient Name</th>
                                    <th>Image</th>
                                    <th style="width:50px;">Edit</th>
                                    <th style="width:50px;">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
            if (!empty($ingredient_data)) {
                foreach ($ingredient_data as $ingredient) {
                    echo '<tr>
                        <td>' . htmlspecialchars($ingredient["IngredientId"]) . '</td>
                        <td>' . htmlspecialchars($ingredient["ProductId"]) . '</td>
                        <td>' . htmlspecialchars($ingredient["IngredientName"]) . '</td>
                        <td>
                            <img src="images/ingredient/' . htmlspecialchars($ingredient["PhotoPath"]) . '" width="100" height="100" alt="Ingredient Image">
                        </td>
                        <td>
                            <a href="ingredient.php?IngredientId=' . htmlspecialchars($ingredient["IngredientId"]) . '" class="btn btn-info btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <button onclick="fundelete(' . htmlspecialchars($ingredient["IngredientId"]) . ');" type="button" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>';
                }
            } else {
                echo '<tr><td colspan="6">No ingredients found for this product.</td></tr>';
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
    $(function() {
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
    function save_data() {

        var AllRight = 1;
        var errorstring = "";


        var SubCategoryName = document.getElementById("SubCategoryName").value;
        var PhotoPath = document.getElementById("PhotoPath").value;

        if (SubCategoryName == "") {
            AllRight = 0;
            $('#SubCategoryName').addClass('is-invalid');


        } else {
            $("#SubCategoryName").removeClass("is-invalid");
        }

        if (PhotoPath == "") {
            AllRight = 0;
            $('#PhotoPath').addClass('is-invalid');


        } else {
            $("#PhotoPath").removeClass("is-invalid");
        }

        if (AllRight == 1) {
            $("#frmData").ajaxForm({
                dataType: 'json',
                beforeSend: function() {
                    document.getElementById("loading").style.display = "block";
                },
                success: function(data) {
                    if (data.response == "S") {
                        window.location = "sub_category.php";
                    } else {

                        $('#ErrorMessage').html(data.msg);
                        $('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
                        document.getElementById("ErrorMessage").style.display = "block";
                        $("#ErrorMessage").delay(1000).fadeOut(400);
                        document.getElementById("loading").style.display = "none";
                    }
                }
            }).submit();
        } else {
            errorstring = "Category name is mendetory..!";

            $('#ErrorMessage').html(errorstring);
            $('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
            document.getElementById("ErrorMessage").style.display = "block";
            $("#ErrorMessage").delay(1000).fadeOut(400);
        }
    }
    </script>

    <script>
    function fundelete(ingredientId) {
        if (confirm("Are you sure you want to delete this ingredient?")) {
            window.location.href = "delete_ingredient.php?IngredientId=" + ingredientId;
        }
    }

    $(document).ready(function() {
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
                    <form action="delete_sub_category.php" method="post" name="frmDeleteFormData" id="frmDeleteFormData"
                        enctype="multipart/form-data">
                        <input type="hidden" id="DSubCategoryId" name="DSubCategoryId" />
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
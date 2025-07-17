<?php
    include('includes/urls.php');
    include_once 'includes/db_connect.php';
    include_once 'includes/functions.php';
    include("database/dbconnection.php");
    $obj = new main();
    $obj->connection();
    sec_session_start();
    if (login_check($mysqli) == true) {
      //getPassword("admin",$mysqli);
    ?>
<?php
    
    $selected = "product_details.php";
    $page = "product_review.php";
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM | Products</title>
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
        <?php include('components/sidebar.php'); ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Products Review</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Product Review</li>
                            </ol>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <?php
$Product_ReviewId = "";
$ProductId = "";
$Name = "";                 
$Review = "";
$Date = "";
$PhotoPath = "Choose File";

// Get all products
$FieldNames = array("ProductId", "ProductName");
$ParamArray = array();
$Fields = implode(",", $FieldNames);
$all_products = $obj->MysqliSelect1("SELECT $Fields FROM product_master", $FieldNames, "", $ParamArray);

// If editing existing review
if (isset($_GET["Product_ReviewId"])) {
    $FieldNames = array("Product_ReviewId", "ProductId", "Name", "Date", "PhotoPath", "Review");
    $ParamArray = array($_GET["Product_ReviewId"]);
    $Fields = implode(",", $FieldNames);
    $single_data = $obj->MysqliSelect1("SELECT $Fields FROM product_review WHERE Product_ReviewId = ?", $FieldNames, "i", $ParamArray);

    $Product_ReviewId = $single_data[0]["Product_ReviewId"];
    $ProductId = $single_data[0]["ProductId"];
    $Name = $single_data[0]["Name"];
    $Review = $single_data[0]["Review"];
    $PhotoPath = !empty($single_data[0]["PhotoPath"]) ? $single_data[0]["PhotoPath"] : "Choose File";
    $Date = $single_data[0]["Date"];
}
?>

            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add/Edit Product Review</h3>
                    </div>

                    <form role="form" action="exe_save_review.php" method="post" enctype="multipart/form-data"
                        id="frmData">
                        <div class="card-body">

                            <input type="hidden" class="form-control" id="Product_ReviewId" name="Product_ReviewId"
                                value="<?php echo htmlspecialchars($Product_ReviewId); ?>">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="ProductId">Select Product</label>
                                    <select class="form-control" id="ProductId" name="ProductId">
                                    <option value="">Select Product</option>
                                        <?php if (!empty($all_products)): ?>
                                        <?php foreach ($all_products as $product): ?>
                                        <option value="<?php echo htmlspecialchars($product['ProductId']); ?>"
                                            <?php echo ($product['ProductId'] == $ProductId) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($product['ProductName']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <option value="">No Product Available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="Name">Reviewer Name</label>
                                        <input type="text" class="form-control" id="Name" name="Name"
                                            placeholder="Enter Reviewer Name"
                                            value="<?php echo htmlspecialchars($Name); ?>" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Review Product Image (300px X 300px jpg/png/webp)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="PhotoPath" name="PhotoPath"
                                                onchange="updateFileName(this)">
                                            <label class="custom-file-label" for="PhotoPath" id="fileLabel">
                                                <?php echo htmlspecialchars($PhotoPath); ?>
                                            </label>
                                        </div>
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
                                            <input type="text" class="form-control" id="Date" name="Date"
                                                placeholder="DD-MM-YYYY"
                                                value="<?php echo !empty($Date) ? htmlspecialchars(date('d-m-Y', strtotime($Date))) : ''; ?>"
                                                data-inputmask="'alias': 'datetime', 'inputFormat': 'dd-mm-yyyy'"
                                                data-mask required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Review</label>
                                    <textarea class="form-control" id="Review" name="Review" placeholder="Enter Review"
                                        rows="4" required><?php echo htmlspecialchars($Review); ?></textarea>
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
                </div>
            </section>

            <!-- Main content Add/Edit-->
            <!-- Main content List-->
            <section class="content">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Product List</h3>
                    </div>
                    <div class="card-body">
                        <?php
    // Define the fields to fetch from the database
    $FieldNames = array("Product_ReviewId", "ProductId", "PhotoPath", "Name", "Review", "Date");
    $Fields = implode(",", $FieldNames);
    $ParamArray = array();
    $all_data = $obj->MysqliSelect1("SELECT $Fields FROM product_review", $FieldNames, "", $ParamArray);

    if ($all_data && count($all_data) > 0) {
    ?>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:150px;">Product Name</th>
                                    <th>Review Details</th>
                                    <th style="width:50px;">Add Images</th>
                                    <th style="width:50px;">Edit</th>
                                    <th style="width:50px;">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                foreach ($all_data as $review) {

                    // Get Product Name by ProductId
                    $productName = "Unknown Product";
                    if (!empty($review["ProductId"])) {
                        $prodFieldNames = array("ProductName");
                        $prodParamArray = array($review["ProductId"]);
                        $prodData = $obj->MysqliSelect1("SELECT ProductName FROM product_master WHERE ProductId = ?", $prodFieldNames, "i", $prodParamArray);
                        $productName = $prodData[0]["ProductName"] ?? 'Unknown Product';
                    }

                    
                    echo '<tr>
                        <td>' . htmlspecialchars($productName) . '<br>
                            <img src="images/ingredient/' . htmlspecialchars($review["PhotoPath"]) . '" width="100" height="120">
                        </td>
                        <td>
                            <strong>Name:</strong> ' . htmlspecialchars($review["Name"]) . '<br>
                            <strong>Review:</strong> ' . htmlspecialchars($review["Review"]) . '<br>
                            <strong>Date:</strong> ' . htmlspecialchars($review["Date"]) . '
                        </td>
                        <td>
                            <a href="add_images_model.php?ProductReviewId=' . htmlspecialchars($review["Product_ReviewId"]) . '">
                                <i class="btn btn-sm btn-primary fa fa-image fa-sm"></i>
                            </a>
                        </td>
                        <td>
                            <a href="product_review_edit.php?ProductReviewId=' . htmlspecialchars($review["Product_ReviewId"]) . '">
                                <i class="btn btn-sm btn-info fa fa-edit fa-sm"></i>
                            </a>
                        </td>
                        <td>
                            <button onClick="fundeleteReview(' . htmlspecialchars($review["Product_ReviewId"]) . ');" type="button" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash fa-sm"></i>
                            </button>
                        </td>
                    </tr>';
                }
                ?>
                            </tbody>
                        </table>
                        <?php
    } else {
        echo "<p>No reviews found.</p>";
    }
    ?>
                    </div>


                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include('components/footer.php'); ?>
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
    <script>
    $(function() {
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

        var ProductName = document.getElementById("Name").value.trim();
        var ProductId = document.getElementById("ProductId").value.trim();
        var Review = document.getElementById("Review").value.trim();
        var DateValue = document.getElementById("Date").value.trim();

        // ✅ Validate Date (Format: DD-MM-YYYY)
        var datePattern = /^(\d{2})-(\d{2})-(\d{4})$/;
        if (!datePattern.test(DateValue)) {
            AllRight = 0;
            $('#Date').addClass('is-invalid');
        } else {
            $('#Date').removeClass('is-invalid');
        }

        // ✅ Validate Reviewer Name
        if (ProductName === "") {
            AllRight = 0;
            $('#Name').addClass('is-invalid');
        } else {
            $('#Name').removeClass('is-invalid');
        }

        // ✅ Validate Product Selection
        if (ProductId === "") {
            AllRight = 0;
            $('#ProductId').addClass('is-invalid');
        } else {
            $('#ProductId').removeClass('is-invalid');
        }

        // ✅ Validate Review
        if (Review === "") {
            AllRight = 0;
            $('#Review').addClass('is-invalid');
        } else {
            $('#Review').removeClass('is-invalid');
        }

        // ✅ Submit Form with AJAX
        if (AllRight === 1) {
            $("#frmData").ajaxForm({
                dataType: 'json',
                beforeSend: function () {
                    $("#loading").show(); // Show loader (optional div)
                    $("#ErrorMessage").hide().removeClass().html('');
                },
                success: function (data) {
                    $("#loading").hide();
                    if (data.response === "S") {
                        // Redirect on success
                        window.location.href = "product_review.php";
                    } else {
                        $("#ErrorMessage").html(data.msg).addClass("alert alert-danger alert-dismissible").show();
                    }
                },
                error: function () {
                    $("#loading").hide();
                    $("#ErrorMessage").html("An unexpected error occurred. Please try again.")
                        .addClass("alert alert-danger alert-dismissible").show();
                }
            }).submit();
        } else {
            errorstring = "Please fill all required fields and enter a valid date!";
            $("#ErrorMessage").html(errorstring).addClass("alert alert-danger alert-dismissible").show();
            setTimeout(function () {
                $("#ErrorMessage").fadeOut("slow");
            }, 3000);
        }
    }

    // ✅ Optional - Show selected file name
    function updateFileName(input) {
        var fileName = input.files[0]?.name || "Choose File";
        document.getElementById("fileLabel").innerText = fileName;
    }

    function fundelete(id1) {
        document.getElementById("DProductId").value = id1;
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
                    window.location = "product_review.php";
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
                    <form action="delete_products.php" method="post" name="frmDeleteFormData" id="frmDeleteFormData"
                        enctype="multipart/form-data">
                        <input type="hidden" id="DProductId" name="DProductId" />
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
    } else {
      header('Location: index.php');
    }
    ?>
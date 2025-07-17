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
    $page = "product_details.php";
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
                            <h1 class="m-0">Products Details</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Products Details</li>
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
                                      
                    $Product_DetailsId = "";
                    $ProductId = "";
                    $Description = "";                   
                    $PhotoPath = "Choose File";
                    $ImagePath = "Choose File";

                    
                    // Fetch all products
                    $FieldNames = array("ProductId", "ProductName");
                    $all_products_details = $obj->MysqliSelect1("SELECT ProductId, ProductName FROM product_master", $FieldNames, "", []);
                    
                    // Check if editing an existing Product Benefit
                    if (isset($_GET["Product_DetailsId"])) {
                        $FieldNames = array("Product_DetailsId", "ProductId","Description", "PhotoPath","ImagePath");
                        $ParamArray = array($_GET["Product_DetailsId"]);
                        $single_data = $obj->MysqliSelect1("SELECT " . implode(",", $FieldNames) . " FROM product_detailss WHERE Product_DetailsId= ?", $FieldNames, "i", $ParamArray);
                    
                        if (!empty($single_data)) {
                            $Product_BenefitId = $single_data[0]["Product_DetailsId"];
                            $ProductId = $single_data[0]["ProductId"];
                            $Title = $single_data[0]["Title"];
                            $Description = $single_data[0]["Description"];
                            $PhotoPath = !empty($single_data[0]["PhotoPath"]) ? $single_data[0]["PhotoPath"] : "Choose File";
                            $ImagePath = !empty($single_data[0]["ImagePath"]) ? $single_data[0]["ImagePath"] : "Choose File";

                        }
                    }
                        
                   
                    ?>
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add/Edit Product Details</h3>
                    </div>

                    <form role="form" action="exe_save_product_details.php" method="post" enctype="multipart/form-data"
                        id="frmData">
                        <div class="card-body">
                            <?php
            if (isset($_SESSION["QueryStatus"])) {
                if ($_SESSION["QueryStatus"] == "SAVED") {
                    echo '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Successful!</h5>
                            Record Saved Successfully...!
                          </div>';
                } elseif ($_SESSION["QueryStatus"] == "UPDATED") {
                    echo '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Successful!</h5>
                            Record Updated Successfully...!
                          </div>';
                } elseif ($_SESSION["QueryStatus"] == "DELETED") {
                    echo '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Successful!</h5>
                            Record Deleted Successfully...!
                          </div>';
                }
                $_SESSION["QueryStatus"] = "";
            }
            ?>

                            <input type="hidden" name="Product_DetailsId"
                                value="<?php echo htmlspecialchars($Product_DetailsId); ?>">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="ProductId">Select Product</label>
                                        <select class="form-control" id="ProductId" name="ProductId" required>
                                            <option value="">Select Product</option>
                                            <?php if (!empty($all_products_details)): ?>
                                            <?php foreach ($all_products_details as $product): ?>
                                            <option value="<?php echo htmlspecialchars($product['ProductId']); ?>"
                                                <?php echo ($product['ProductId'] == $ProductId) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($product['ProductName']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <option value="">No Products Available</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                               <div class="col-sm-4">
    <div class="form-group">
        <label>Upload Product Image (500x300 JPG)</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="PhotoPath" name="PhotoPath" 
                onchange="updateFileName(this, 'photoLabel')">
            <label id="photoLabel" class="custom-file-label" for="PhotoPath">
                <?php echo !empty($PhotoPath) && $PhotoPath !== "Choose File" 
                    ? htmlspecialchars($PhotoPath) 
                    : "Choose File"; ?>
            </label>
        </div>
        
        <!-- Image Preview -->
        <?php if (!empty($PhotoPath) && $PhotoPath !== "Choose File"): ?>
            <img src="cms/images/products/<?php echo htmlspecialchars($PhotoPath); ?>" 
                alt="Current Image" style="margin-top: 10px; max-width: 100%; height: auto;" height="100">
        <?php endif; ?>
    </div>
</div>

<div class="col-sm-4">
    <div class="form-group">
        <label>Upload Image (500x300 JPG)</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="ImagePath" name="ImagePath" 
                onchange="updateFileName(this, 'imageLabel')">
            <label id="imageLabel" class="custom-file-label" for="ImagePath">
                <?php echo !empty($ImagePath) && $ImagePath !== "Choose File" 
                    ? htmlspecialchars($ImagePath) 
                    : "Choose File"; ?>
            </label>
        </div>

        <!-- Image Preview -->
        <?php if (!empty($ImagePath) && $ImagePath !== "Choose File"): ?>
            <img src="cms/images/products/<?php echo htmlspecialchars($ImagePath); ?>" 
                alt="Current Image" style="margin-top: 10px; max-width: 100%; height: auto;" height="100">
        <?php endif; ?>
    </div>
</div>


                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="Description">Product Description</label>
                                        <textarea class="form-control" id="Description" name="Description" rows="4"
                                            placeholder="Enter product description"><?php echo htmlspecialchars($Description); ?></textarea>
                                    </div>
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
                <!-- /.card -->
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
        $FieldNames = array("Product_DetailsId", "ProductId", "Description", "PhotoPath", "ImagePath");
        $Fields = implode(",", $FieldNames);

        // Fetch all product details
        $all_products_details = $obj->MysqliSelect1(
            "SELECT " . $Fields . " FROM product_details",
            $FieldNames,
            "",
            []
        );

        if (!empty($all_products_details)) {
    ?>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:120px;">Photo Image</th>
                                    <th style="width:120px;">Image Image</th>
                                    <th>Product Description</th>
                                    <th style="width:80px;">Add Images</th>
                                    <th style="width:60px;">Edit</th>
                                    <th style="width:60px;">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_products_details as $product): ?>
                                <tr>
                                    <td>
                                        <img src="images/products/<?php echo !empty($product["PhotoPath"]) ? htmlspecialchars($product["PhotoPath"]) : 'default.jpg'; ?>"
                                            width="100" height="100" alt="Product Image">
                                    </td>
                                    <td>
                                        <img src="images/products/<?php echo !empty($product["ImagePath"]) ? htmlspecialchars($product["ImagePath"]) : 'default.jpg'; ?>"
                                            width="100" height="100" alt="Additional Image">
                                    </td>

                                    <td>
                                        <?php echo nl2br(htmlspecialchars($product["Description"])); ?>
                                    </td>
                                    <td>
                                        <a href="add_images_model.php?ProductId=<?php echo htmlspecialchars($product["ProductId"]); ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="fa fa-image"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="products.php?Product_DetailsId=<?php echo htmlspecialchars($product["Product_DetailsId"]); ?>"
                                            class="btn btn-sm btn-info">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <button
                                            onClick="fundelete(<?php echo htmlspecialchars($product['Product_DetailsId']); ?>);"
                                            type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#modal-default">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php
        } else {
            echo "<p>No product details found.</p>";
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

    // ✅ Get Form Values
    var Product_DetailsId = document.getElementById("Product_DetailsId").value.trim();
    var ProductId = document.getElementById("ProductId").value.trim();
    var Description = document.getElementById("Description").value.trim();
    var PhotoPath = document.getElementById("PhotoPath").value.trim();
    var ImagePath = document.getElementById("ImagePath").value.trim();

    // ✅ Validate Product Selection
    if (ProductId === "") {
        AllRight = 0;
        $('#ProductId').addClass('is-invalid');
    } else {
        $('#ProductId').removeClass('is-invalid');
    }

    // ✅ Validate Description
    if (Description === "") {
        AllRight = 0;
        $('#Description').addClass('is-invalid');
    } else {
        $('#Description').removeClass('is-invalid');
    }

    // ✅ Validate PhotoPath Extension
    if (PhotoPath !== "" && !/\.(jpg|jpeg|png|webp)$/i.test(PhotoPath)) {
        AllRight = 0;
        $('#PhotoPath').addClass('is-invalid');
        errorstring += "Invalid Photo format (jpg, jpeg, png, webp only)<br>";
    } else {
        $('#PhotoPath').removeClass('is-invalid');
    }

    // ✅ Validate ImagePath Extension
    if (ImagePath !== "" && !/\.(jpg|jpeg|png|webp)$/i.test(ImagePath)) {
        AllRight = 0;
        $('#ImagePath').addClass('is-invalid');
        errorstring += "Invalid Image format (jpg, jpeg, png, webp only)<br>";
    } else {
        $('#ImagePath').removeClass('is-invalid');
    }

    // ✅ Submit Form with AJAX if all fields are valid
    if (AllRight === 1) {
        $("#frmData").ajaxForm({
            dataType: 'json',
            beforeSend: function () {
                $("#loading").show();         // Show loader
                $("#ErrorMessage").hide().removeClass().html('');
            },
            success: function (data) {
                $("#loading").hide();

                if (data.response === "S") {
                    // Redirect on success
                    window.location.href = "product_details.php";
                } else {
                    $("#ErrorMessage").html(data.msg)
                        .addClass("alert alert-danger alert-dismissible")
                        .show();
                }
            },
            error: function () {
                $("#loading").hide();
                $("#ErrorMessage").html("An unexpected error occurred. Please try again.")
                    .addClass("alert alert-danger alert-dismissible")
                    .show();
            }
        }).submit();
    } else {
        // Display errors
        $("#ErrorMessage").html(errorstring || "Please fill all required fields correctly!")
            .addClass("alert alert-danger alert-dismissible")
            .show();
        
        setTimeout(function () {
            $("#ErrorMessage").fadeOut("slow");
        }, 3000);
    }
}

// ✅ File Name Display Function
function updateFileName(input, labelId) {
    var fileName = input.files[0]?.name || "Choose File";
    document.getElementById(labelId).innerText = fileName;
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
                    window.location = "product_details.php";
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

    <
    script type = "text/javascript"
    src = "js/common_functions.js" >
    </script>
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
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
    $page = "product_benifit.php";
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
                            <h1 class="m-0">Product Benifits</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Product Benifits</li>
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
                   $Product_BenefitId = "";
                   $ProductId = "";
                   $Title = "";
                   $ShortDescription = "";
                   $PhotoPath = "Choose File";
                   
                   // Fetch all products
                   $FieldNames = array("ProductId", "ProductName");
                   $all_products = $obj->MysqliSelect1("SELECT ProductId, ProductName FROM product_master", $FieldNames, "", []);
                   
                   // Check if editing an existing Product Benefit
                   if (isset($_GET["Product_BenefitId"])) {
                       $FieldNames = array("Product_BenefitId", "ProductId", "Title", "ShortDescription", "PhotoPath");
                       $ParamArray = array($_GET["Product_BenefitId"]);
                       $single_data = $obj->MysqliSelect1("SELECT " . implode(",", $FieldNames) . " FROM product_benefits WHERE Product_BenefitId= ?", $FieldNames, "i", $ParamArray);
                   
                       if (!empty($single_data)) {
                           $Product_BenefitId = $single_data[0]["Product_BenefitId"];
                           $ProductId = $single_data[0]["ProductId"];
                           $Title = $single_data[0]["Title"];
                           $ShortDescription = $single_data[0]["ShortDescription"];
                           $PhotoPath = !empty($single_data[0]["PhotoPath"]) ? $single_data[0]["PhotoPath"] : "Choose File";
                       }
                   }
                
                    ?>
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add/Edit Products</h3>
                    </div>
                    <form role="form" action="exe_save_products.php" method="post" enctype="multipart/form-data"
                        id="frmData">
                        <div class="card-body">
                            <?php
        if (isset($_SESSION["QueryStatus"])) {
            $statusMessage = "";
            switch ($_SESSION["QueryStatus"]) {
                case "SAVED":
                    $statusMessage = "Record Saved Successfully!";
                    break;
                case "UPDATED":
                    $statusMessage = "Record Updated Successfully!";
                    break;
                case "DELETED":
                    $statusMessage = "Record Deleted Successfully!";
                    break;
            }
            if (!empty($statusMessage)) {
                echo '<div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> Successful!</h5>
                        ' . $statusMessage . '
                      </div>';
                $_SESSION["QueryStatus"] = "";
            }
        }
        ?>

                            <input type="hidden" class="form-control" id="Product_BenefitId" name="Product_BenefitId"
                                value="<?php echo htmlspecialchars($Product_BenefitId); ?>">

                            <div class="row">
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

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Product Image (500px X 300px jpg image)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="PhotoPath"
                                                name="PhotoPath">
                                            <label class="custom-file-label"
                                                for="PhotoPath"><?php echo htmlspecialchars($PhotoPath); ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="Title">Benefits Head</label>
                                        <input type="text" class="form-control" id="Title" name="Title"
                                            placeholder="Enter Benefits Head"
                                            value="<?php echo htmlspecialchars($Title); ?>">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Short Description</label>
                                        <textarea class="form-control" id="ShortDescription" name="ShortDescription"
                                            placeholder="Enter Short Description"
                                            rows="4"><?php echo htmlspecialchars($ShortDescription); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer" id="ErrorBox">
                                <p id="ErrorMessage"></p>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="button" class="btn btn-primary"
                                onClick="javascript:save_data();">Submit</button>
                        </div>
                    </form>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </section>
            <!-- Main content Add/Edit-->
            <!-- Main content List-->
            <section class="content"> 
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Product Benefits</h3>
        </div>
        <div class="card-body">
            <?php
            // Fetch all Product Benefits
            $FieldNames = array("Product_BenefitId", "ProductId", "PhotoPath", "Title", "ShortDescription");
            $Fields = implode(",", $FieldNames);
            $ParamArray = array();
            $all_data = $obj->MysqliSelect1("SELECT $Fields FROM product_benefits", $FieldNames, "", $ParamArray);

            if ($all_data && count($all_data) > 0) {
            ?>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:200px;">Product</th>
                            <th>Benefit Details</th>
                            <th style="width:60px;">Images</th>
                            <th style="width:60px;">Edit</th>
                            <th style="width:60px;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($all_data as $benefit) {
                            // Get Product Name by ProductId
                            $productName = "Unknown Product";
                            if (!empty($benefit["ProductId"])) {
                                $prodFieldNames = array("ProductName");
                                $prodParamArray = array($benefit["ProductId"]);
                                $prodData = $obj->MysqliSelect1("SELECT ProductName FROM product_master WHERE ProductId = ?", $prodFieldNames, "i", $prodParamArray);
                                $productName = $prodData[0]["ProductName"] ?? 'Unknown Product';
                            }

                            echo '<tr>
                                <td>
                                    <strong>' . htmlspecialchars($productName) . '</strong><br>';
                            if (!empty($benefit["PhotoPath"])) {
                                echo '<img src="images/products/' . htmlspecialchars($benefit["PhotoPath"]) . '" width="100" height="120">';
                            }
                            echo '</td>
                                <td>
                                    <strong>Title:</strong> ' . htmlspecialchars($benefit["Title"]) . '<br>
                                    <strong>Description:</strong> ' . nl2br(htmlspecialchars($benefit["ShortDescription"])) . '
                                </td>
                                <td class="text-center">
                                    <a href="add_images_model.php?ProductBenefitId=' . htmlspecialchars($benefit["Product_BenefitId"]) . '" class="btn btn-sm btn-primary" title="Add Images">
                                        <i class="fa fa-image"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="product_benefit_edit.php?Product_BenefitId=' . htmlspecialchars($benefit["Product_BenefitId"]) . '" class="btn btn-sm btn-info" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button onClick="deleteProductBenefit(' . htmlspecialchars($benefit["Product_BenefitId"]) . ');" type="button" class="btn btn-danger btn-sm" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } else {
                echo "<p>No product benefits found.</p>";
            }
            ?>
        </div>
    </div>
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
    <!-- Summernote --> Cat
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

        var ProductId = document.getElementById("ProductId").value;
        var Title = document.getElementById("Title").value;

        if (ProductId == "") {
            AllRight = 0;
            $('#ProductId').addClass('is-invalid');
        } else {
            $("#ProductId").removeClass("is-invalid");
        }

        if (Title == "") {
            AllRight = 0;
            $('#Title').addClass('is-invalid');
        } else {
            $("#Title").removeClass("is-invalid");
        }

        if (AllRight == 1) {
            $("#frmData").ajaxForm({
                url: "exe_save_benefit.php",
                dataType: 'json',
                beforeSend: function() {
                    // Show loader if you have one
                    $("#loading").show();
                },
                success: function(data) {
                    $("#loading").hide();
                    if (data.response == "S") {
                        window.location.href = "product_benifit.php";
                    } else {
                        s
                        $('#ErrorMessage').html(data.msg);
                        $('#ErrorMessage').addClass('alert alert-danger alert-dismissible').show();
                        $("#ErrorMessage").delay(3000).fadeOut(500);
                    }
                },
                error: function() {
                    $("#loading").hide();
                    $('#ErrorMessage').html("Something went wrong!");
                    $('#ErrorMessage').addClass('alert alert-danger alert-dismissible').show();
                    $("#ErrorMessage").delay(3000).fadeOut(500);
                }
            }).submit();
        } else {
            errorstring = "Please fill required fields.";
            $('#ErrorMessage').html(errorstring);
            $('#ErrorMessage').addClass('alert alert-danger alert-dismissible').show();
            $("#ErrorMessage").delay(3000).fadeOut(500);
        }
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
                    window.location = "products.php";
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
    <script>
    document.getElementById('addRowBtn').addEventListener('click', function() {
        var table = document.getElementById('product_sizes_table').getElementsByTagName('tbody')[0];
        var row = table.insertRow(table.rows.length);
        row.innerHTML = `
                <td><input type="text" name="size[]" class="form-control" placeholder="Enter Size" required></td>
                <td><input type="number" name="offer_price[]" class="form-control" placeholder="Enter Offer Price" step="0.01" required></td>
                <td><input type="number" name="mrp[]" class="form-control" placeholder="Enter MRP" step="0.01" required></td>
                <td><button type="button" class="btn btn-danger removeRowBtn">Remove</button></td>
            `;
    });

    document.querySelector('#product_sizes_table').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('removeRowBtn')) {
            var row = e.target.closest('tr');
            row.remove();
        }
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
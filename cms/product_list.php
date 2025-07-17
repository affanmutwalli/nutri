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
  $selected = "catlog.php";
  $page = "products.php";
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM | Products List</title>

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
      <?php include('components/sidebar.php'); ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">Products List</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item active">Products List</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <?php
        $ProductId = "";
        $ProductName = "";
        $MetaTags = "";
        $MetaKeywords = "";
        $ShortDescription = "";
        $Specifications = "";
        $PhotoPath = "Choose File";
        $MRP = "";
        $OfferPrice = "";
        $CategoryId = "";
        if (isset($_GET) && array_key_exists("ProductId", $_GET)) {
          $FieldNames = array("ProductId", "ProductName","CategoryId","MetaTags", "MetaKeywords","ShortDescription", "PhotoPath", "Specification","MRP","OfferPrice");
          $ParamArray = array();
          $ParamArray[0] = $_GET["ProductId"];
          $Fields = implode(",", $FieldNames);
          $single_data = $obj->MysqliSelect1("Select " . $Fields . " from product_master where ProductId= ? ", $FieldNames, "i", $ParamArray);

          $ProductId = $single_data[0]["ProductId"];
          $ProductName = $single_data[0]["ProductName"];
          $MetaTags = $single_data[0]["MetaTags"];
          $MetaKeywords = $single_data[0]["MetaKeywords"];
          $ShortDescription = $single_data[0]["ShortDescription"];
          $Specifications = $single_data[0]["Specification"];
          $PhotoPath = $single_data[0]["PhotoPath"];
          $MRP = $single_data[0]["MRP"];
          $OfferPrice = $single_data[0]["OfferPrice"];
          $CategoryId = $single_data[0]["CategoryId"];
        }
        ?>
        <!-- Main content -->
        <!-- <section class="content"> -->
          <!-- Default box -->
          
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
            
            $FieldNames = array("ProductId", "ProductName","CategoryId","MetaTags", "MetaKeywords","ShortDescription", "PhotoPath", "Specification","MRP","OfferPrice");
            $ParamArray = array();
            $Fields = implode(",", $FieldNames);
            $all_data = $obj->MysqliSelect1("Select " . $Fields . " from product_master ", $FieldNames, "", $ParamArray);
            if ($all_data && count($all_data) > 0) {
            ?>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:100px;">Product Name</th>
                            <th>Product Details</th>
                            <th style="width:50px;">Add Images</th>
                            <th style="width:50px;">Edit</th>
                            <th style="width:50px;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through each product and display in the table
                        foreach ($all_data as $product) {
                          
                          $FieldNames = array("CategoryId", "CategoryName");
                          $Fields = implode(",", $FieldNames);
                          $ParamArray = array();
                          $ParamArray[0] = $product["CategoryId"];
                          $categories = $obj->MysqliSelect1("SELECT " . $Fields . " FROM category_master where CategoryId = ?", $FieldNames,"i",$ParamArray);
                          $category_name = $categories[0]["CategoryName"] ?? ''; 
                            echo '<tr>
                                    <td>' . htmlspecialchars($product["ProductName"]) . '</td>
                                    <td>
                                    <div class="row">
                                  <div class="col-md-6">
                                      <p>Category: <b>' . htmlspecialchars($category_name) . '</b></p>
                                      </div>
                                      <div class="col-md-6">
                                      <img src="' . htmlspecialchars($ProductImagesURL . $product["PhotoPath"]) . '" width="100" height="120">
                                      </div>
                                      </div>
                                      <b>Short Description: </b>' .$product["Specification"] . '<br></td>
                                    <td><a href="add_images_model.php?ProductId=' . $product["ProductId"] . '"><i class="btn btn-sm btn-primary fa fa-edit fa-sm"></i></a></td>
                                    <td><a href="products.php?ProductId=' . $product["ProductId"] . '"><i class="btn btn-sm btn-info fa fa-edit fa-sm"></i></a></td>
                                    <td><button onClick="javascript:fundelete(' . $product["ProductId"] . ');" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-default"><i class="fa fa-trash fa-sm"></i></button></td>
                                  </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } else {
                // Display a message if no data is available
                echo "<p>No products found.</p>";
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
      function getProducts(id) {
        $.post("get_parent_products.php", {
            CategoryId: id
          },
          function(data, status) {
            document.getElementById("Products").innerHTML = data;
          });
      }

      function save_data() {

        var AllRight = 1;
        var errorstring = "";


        var ProductName = document.getElementById("ProductName").value;
        var CategoryId = document.getElementById("CategoryId").value;

        if (ProductName == "") {
          AllRight = 0;
          $('#ProductName').addClass('is-invalid');


        } else {
          $("#ProductName").removeClass("is-invalid");
        }

        if (CategoryId == "0") {
          AllRight = 0;
          $('#CategoryId').addClass('is-invalid');


        } else {
          $("#CategoryId").removeClass("is-invalid");
        }

        if (AllRight == 1) {
          $("#frmData").ajaxForm({
            dataType: 'json',
            beforeSend: function() {
              document.getElementById("loading").style.display = "block";
            },
            success: function(data) {
              if (data.response == "S") {
                window.location = "products.php";
              } else {
                document.getElementById("ErrorMessage").innerHTML = data.msg;

              }
            }
          }).submit();
        } else {
          errorstring = "Product name & category is mendetory..!";

          $('#ErrorMessage').html(errorstring);
          $('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
          document.getElementById("ErrorMessage").style.display = "block";
          $("#ErrorMessage").delay(1000).fadeOut(400);
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
    <!-- <script>
  // JavaScript function to reload the page with the selected CategoryId
  function reloadPage(categoryId) {
    // Get the current URL
    var currentUrl = window.location.href;

    // Check if the URL already contains a query string
    var separator = currentUrl.indexOf('?') !== -1 ? '&' : '?';

    // Construct the new URL with the selected CategoryId
    var newUrl = currentUrl + separator + 'CategoryId=' + categoryId;

    // Redirect to the new URL
    window.location.href = newUrl;
  }
</script> -->
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
            <form action="delete_products.php" method="post" name="frmDeleteFormData" id="frmDeleteFormData" enctype="multipart/form-data">
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
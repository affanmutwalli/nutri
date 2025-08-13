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
    $selected = "catlog.php";
    $page = "products.php";
    ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CRM | Products</title>
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
        <style>
            /* Enhanced multi-select styling */
            #SubCategoryId {
                border: 2px solid #ddd;
                border-radius: 5px;
                padding: 5px;
            }
            #SubCategoryId:focus {
                border-color: #007bff;
                box-shadow: 0 0 5px rgba(0,123,255,0.3);
            }
            #SubCategoryId option:checked {
                background-color: #007bff;
                color: white;
            }
            #SubCategoryId option {
                padding: 5px;
                margin: 2px 0;
            }
        </style>
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
                                <h1 class="m-0">Products</h1>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item active">Products</li>
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
                    $ProductId = "";
                    $ProductName = "";
                    $ProductCode = "";
                    $MetaTags = "";
                    $MetaKeywords = "";
                    $ShortDescription = "";
                    $LongDescription = "";
                    $Specifications = "";
                    $PhotoPath = "Choose File";
                    $CategoryId = "";
                    $SubCategoryId = "";
                    $Size = "";
                    $OfferPrice = "";
                    $MRP = "";
                    $IsCombo = "";
                    
                    $FieldNames = array("SubCategoryId", "SubCategoryName");
                    $ParamArray = array();
                      $Fields = implode(",", $FieldNames);
                      $all_sub_categories = $obj->MysqliSelect1("Select " . $Fields . " from sub_category", $FieldNames, "", $ParamArray);

                    // For editing: get currently assigned sub-categories
                    $assigned_subcategories = array();

                    if (isset($_GET) && array_key_exists("ProductId", $_GET)) {
                      $FieldNames = array("ProductId", "ProductName","CategoryId","MetaTags", "MetaKeywords","ShortDescription", "PhotoPath", "Specification","SubCategoryId","ProductCode","IsCombo");
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
                      $CategoryId = $single_data[0]["CategoryId"];
                      $SubCategoryId = $single_data[0]["SubCategoryId"];
                      $ProductCode = $single_data[0]["ProductCode"];
                      $IsCombo = $single_data[0]["IsCombo"];
                      
                      // Get all sub-categories for the dropdown
                      $FieldNames = array("SubCategoryId", "SubCategoryName");
                      $ParamArray = array();
                      $Fields = implode(",", $FieldNames);
                      $all_sub_categories = $obj->MysqliSelect1("Select " . $Fields . " from sub_category", $FieldNames, "", $ParamArray);

                      // Get currently assigned sub-categories for this product
                      $FieldNames = array("SubCategoryId");
                      $ParamArray = array($_GET["ProductId"]);
                      $Fields = implode(",", $FieldNames);
                      $assigned_data = $obj->MysqliSelect1("Select " . $Fields . " from product_subcategories where ProductId = ? ", $FieldNames, "i", $ParamArray);

                      if (!empty($assigned_data)) {
                          foreach ($assigned_data as $assigned) {
                              $assigned_subcategories[] = $assigned['SubCategoryId'];
                          }
                      }

                      $FieldNames = array("ProductId", "Size","OfferPrice","MRP","Coins");
                      $ParamArray = array();
                      $ParamArray[0] = $_GET["ProductId"];
                      $Fields = implode(",", $FieldNames);
                      $product_price = $obj->MysqliSelect1("Select " . $Fields . " from product_price where ProductId= ? ", $FieldNames, "i", $ParamArray);

                      // Fetch Long Description from product_details table
                      $FieldNames = array("Description");
                      $ParamArray = array($_GET["ProductId"]);
                      $Fields = implode(",", $FieldNames);
                      $product_details_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_details WHERE ProductId = ? LIMIT 1", $FieldNames, "i", $ParamArray);

                      if (!empty($product_details_data) && isset($product_details_data[0]['Description'])) {
                          $LongDescription = $product_details_data[0]['Description'];
                      }

                    }
                    
                    // Define the field names for the query
                    $FieldNames = array("CategoryId", "CategoryName");
                    
                    // Create a comma-separated string of field names
                    $Fields = implode(",", $FieldNames);
                    
                    // Fetch categories from the database
                    $all_categories = $obj->MysqliSelect("SELECT " . $Fields . " FROM category_master", $FieldNames);

                    
                    
                    
                    
                    ?>
                <!-- Main content -->
                <section class="content">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add/Edit Products</h3>
                        </div>
                        <form role="form" action="exe_save_products.php" method="post" enctype="multipart/form-data" id="frmData">
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
                                <input type="hidden" class="form-control" id="ProductId" name="ProductId" value="<?php echo htmlspecialchars($ProductId); ?>">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="ProductName">Product Name</label>
                                            <input type="text" class="form-control" id="ProductName" name="ProductName" placeholder="Enter Product Name" value="<?php echo htmlspecialchars($ProductName); ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Product Image (500px X 300px jpg image)</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="PhotoPath" name="PhotoPath">
                                                <label class="custom-file-label" for="PhotoPath"><?php echo htmlspecialchars($PhotoPath); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Meta Tags (Separate With Comma ",")</label>
                                            <input type="text" class="form-control" id="MetaTags" name="MetaTags" placeholder="Enter Meta Tags" value="<?php echo htmlspecialchars($MetaTags); ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Meta Keywords (Separate With Comma ",")</label>
                                            <input type="text" class="form-control" id="MetaKeywords" name="MetaKeywords" placeholder="Enter Meta Keywords" value="<?php echo htmlspecialchars($MetaKeywords); ?>">
                                        </div>
                                    </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="CategoryId">Category</label>
                                                <select class="form-control" id="CategoryId" name="CategoryId" onchange="fetchSubCategories(this.value)">
                                                    <option value="">Select Category</option>
                                                    <?php if (!empty($all_categories)): ?>
                                                        <?php foreach ($all_categories as $category): ?>
                                                            <option value="<?php echo htmlspecialchars($category['CategoryId']); ?>" 
                                                                <?php echo ($category['CategoryId'] == $CategoryId) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($category['CategoryName']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="">No Categories Available</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="SubCategoryId">Sub Categories (Select up to 6)</label>
                                                <select class="form-control" id="SubCategoryId" name="SubCategoryId[]" multiple size="6" style="height: 150px;">
                                                    <?php if (!empty($all_sub_categories)): ?>
                                                        <?php foreach ($all_sub_categories as $sub_category): ?>
                                                            <option value="<?php echo htmlspecialchars($sub_category['SubCategoryId']); ?>"
                                                                <?php echo in_array($sub_category['SubCategoryId'], $assigned_subcategories) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($sub_category['SubCategoryName']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="">No Subcategories Available</option>
                                                    <?php endif; ?>
                                                </select>
                                                <small class="form-text text-muted">Hold Ctrl (Cmd on Mac) to select up to 6 categories. First selected will be primary.</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="ProductCode">Product Code</label>
                                            <input type="text" class="form-control" id="ProductCode" name="ProductCode" placeholder="Enter Product Code" value="<?php echo htmlspecialchars($ProductCode); ?>">
                                        </div>

                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="IsCombo">Product Type</label>
                                            <select class="form-control" id="IsCombo" name="IsCombo">
                                                <option value="">Single Product</option>
                                                <option value="Y" <?php echo (isset($IsCombo) && $IsCombo == 'Y') ? 'selected' : ''; ?>>Combo Product</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                <div class="form-group">
                                <label>Select Unit Size</label>
                                    <select id="unit_select" class="form-control" onchange="updateSizes()">
                                        <option value="ml">Milliliters (ml)</option>
                                        <option value="gm">Grams (gm)</option>
                                        <option value="capsules">Capsules</option>
                                    </select>
                                    </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control" id="ShortDescription" name="ShortDescription" placeholder="Enter Short Description" rows="4"><?php echo htmlspecialchars($ShortDescription); ?></textarea>
                                        </div>
                                    </div>
                                <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Long Description</label>
                                            <textarea class="form-control" id="LongDescription" name="LongDescription" placeholder="Enter Long Description for product details page" rows="6"><?php echo htmlspecialchars($LongDescription); ?></textarea>
                                        </div>
                                    </div>

                                <!-- Product Documents Section -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Product Documents (PDF Files)</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Document Title</label>
                                                        <input type="text" class="form-control" id="DocumentTitle" placeholder="Enter document title (e.g., Lab Report, Certificate)">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Document Type</label>
                                                        <select class="form-control" id="DocumentType">
                                                            <option value="lab_report">Lab Report</option>
                                                            <option value="certificate">Certificate</option>
                                                            <option value="test_report">Test Report</option>
                                                            <option value="specification">Specification</option>
                                                            <option value="other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>PDF File</label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="DocumentFile" accept=".pdf">
                                                            <label class="custom-file-label" for="DocumentFile">Choose PDF file</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success" onclick="addDocument()">Add Document</button>

                                            <!-- Documents List -->
                                            <div id="documents-list" style="margin-top: 20px;">
                                                <h5>Uploaded Documents:</h5>
                                                <div id="documents-container">
                                                    <!-- Documents will be loaded here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row" style="margin: bottom 10px;">
                                    <div class="col-sm-12">
                                    <label>Sizes, Offer Price, and MRP</label>
                                        <table id="product_sizes_table" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Offer Price</th>
                                                    <th>MRP</th>
                                                    <th>Coins</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sizes_table_body">
                                                <!-- Sizes will be dynamically inserted here -->
                                            </tbody>
                                        </table>
                                        <button type="button" id="addRowBtn" class="btn btn-primary">Add Size/Price Row</button>
                                    </div>
                                </div>

                                        
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label>Specifications.</label>
                                                <textarea id="Specification" name="Specification" class="textarea" placeholder="Place some text here" style="width: 100%; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo htmlspecialchars($Specifications); ?></textarea>
                                            </div>
                                        </div>
                                <div class="box-footer" id="ErrorBox">
                                    <p id="ErrorMessage"></p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary" onClick="javascript:save_data();">Submit</button>
                            </div>
                        </form>
                        <!-- /.card-footer -->
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
                                // Define the fields to fetch from the database
                                
                                $FieldNames = array("ProductId", "ProductName","CategoryId","MetaTags", "MetaKeywords","ShortDescription", "PhotoPath", "Specification");
                                $ParamArray = array();
                                $Fields = implode(",", $FieldNames);
                                $all_data = $obj->MysqliSelect1("Select " . $Fields . " from product_master ", $FieldNames, "", $ParamArray);
                                // Check if the data fetch was successful
                                if ($all_data && count($all_data) > 0) {
                                ?>
                            <?php
                                // Assume $all_data contains the product data
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
                                            // Define fields and parameters for fetching category
                                            $FieldNames = array("CategoryName", "CategoryId");
                                            $Fields = implode(",", $FieldNames);
                                            $ParamArray = array($product["CategoryId"]);
                                        
                                            // Fetch the category details
                                            $all_categories = $obj->MysqliSelect1("SELECT " . $Fields . " FROM category_master WHERE CategoryId = ?", $FieldNames, "i", $ParamArray);
                                            
                                            // Set category values if available
                                            $category_name = $all_categories[0]["CategoryName"] ?? 'Unknown Category'; 
                                            $CategoryID = $all_categories[0]["CategoryId"] ?? '';
                                        
                                            echo '<tr>
                                                    <td>' . htmlspecialchars($product["ProductName"]) . '<br>
                                                        <img src="' . htmlspecialchars('images/products/'.$product["PhotoPath"]) . '" width="100" height="120">
                                                    </td>
                                                    <td>
                                                        Specifications : '.$product["Specification"]. '<br>
                                                        Category: ' . htmlspecialchars($category_name) . '
                                                    </td>
                                                    <td>
                                                        <a href="add_images_model.php?ProductId=' . htmlspecialchars($product["ProductId"]) . '">
                                                            <i class="btn btn-sm btn-primary fa fa-edit fa-sm"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="products.php?ProductId=' . htmlspecialchars($product["ProductId"]) . '">
                                                            <i class="btn btn-sm btn-info fa fa-edit fa-sm"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <button onClick="javascript:fundelete(' . htmlspecialchars($product["ProductId"]) . ');" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-default">
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
            function save_data() {

              var AllRight = 1;
              var errorstring = "";


              var ProductName = document.getElementById("ProductName").value;
              var CategoryId = document.getElementById("CategoryId").value;
              var ProductCode = document.getElementById("ProductCode").value;
            
              if (ProductName == "") {
                AllRight = 0;
                $('#ProductName').addClass('is-invalid');
            
            
              } else {
                $("#ProductName").removeClass("is-invalid");
              }
            
              if (CategoryId == "" || CategoryId == "0") {
                AllRight = 0;
                $('#CategoryId').addClass('is-invalid');


              } else {
                $("#CategoryId").removeClass("is-invalid");
              }

              if (ProductCode == "") {
                AllRight = 0;
                $('#ProductCode').addClass('is-invalid');


              } else {
                $("#ProductCode").removeClass("is-invalid");
              }

              // Validate that at least one size/price row is filled
              var sizeInputs = document.querySelectorAll('input[name="size[]"]');
              var hasValidSize = false;
              for (var i = 0; i < sizeInputs.length; i++) {
                if (sizeInputs[i].value.trim() !== '') {
                  hasValidSize = true;
                  break;
                }
              }

              if (!hasValidSize) {
                AllRight = 0;
                errorstring = "Please add at least one size and price for the product.";
                $('#ErrorMessage').html(errorstring);
                $('#ErrorMessage').addClass('alert alert-danger alert-dismissible');
                document.getElementById("ErrorMessage").style.display = "block";
                $("#ErrorMessage").delay(3000).fadeOut(400);
              }

              if (AllRight == 1) {
                $("#frmData").ajaxForm({
                  dataType: 'json',
                  beforeSend: function() {
                    document.getElementById("loading").style.display = "block";
                  },
                  success: function(data) {
                    document.getElementById("loading").style.display = "none";
                    if (data.response == "S") {
                      window.location = "products.php";
                    } else {
                      document.getElementById("ErrorMessage").innerHTML = data.msg;
                      document.getElementById("ErrorMessage").style.display = "block";
                    }
                  },
                  error: function(xhr, status, error) {
                    document.getElementById("loading").style.display = "none";
                    document.getElementById("ErrorMessage").innerHTML = "An error occurred while saving the product. Please try again.";
                    document.getElementById("ErrorMessage").style.display = "block";
                    console.error("AJAX Error:", status, error);
                    console.error("Response:", xhr.responseText);
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
                  document.getElementById("loading").style.display = "none";
                  if (data.response == "D") {
                    window.location = "products.php";
                  } else {
                    document.getElementById("ErrorMessage").innerHTML = data.msg;
                    document.getElementById("ErrorMessage").style.display = "block";
                  }
                },
                error: function(xhr, status, error) {
                  document.getElementById("loading").style.display = "none";
                  document.getElementById("ErrorMessage").innerHTML = "An error occurred while deleting the product. Please try again.";
                  document.getElementById("ErrorMessage").style.display = "block";
                  console.error("AJAX Error:", status, error);
                  console.error("Response:", xhr.responseText);
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
                <td><input type="number" name="coins[]" class="form-control" placeholder="Enter Coins" step="0.01" required></td>
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
        <script>
            // Define sizes for each unit type
            const sizes = {
                ml: [
                    '500 ml | Pack of 1',
                    '1000 ml | Pack of 1',
                    '1000 ml | Pack of 2',
                    '500 ml | Pack of 2'
                ],
                gm: [
                    '20 gm | Pack of 1',
                    '20 gm | Pack of 2',
                    '20 gm | Pack of 3',

                ],
                capsules: [
                    '30 caps | Pack of 1',
                    '60 caps | Pack of 1',
                    '60 caps | Pack of 2',
                    '90 caps | Pack of 1',
                    '90 caps | Pack of 2'
                ]
            };

            // Function to update the table based on the selected unit
            function updateSizes() {
                const unit = document.getElementById('unit_select').value; // Get selected unit
                const tableBody = document.getElementById('sizes_table_body'); // Table body

                // Clear the existing table rows
                tableBody.innerHTML = '';

                // Get the sizes for the selected unit
                const unitSizes = sizes[unit];

                // Loop through the sizes and add rows to the table
                unitSizes.forEach(size => {
                    const row = document.createElement('tr');

                    // Create table cells
                    const sizeCell = document.createElement('td');
                    sizeCell.innerHTML = `<input type="text" name="size[]" class="form-control" value="${size}" readonly>`;

                    const offerPriceCell = document.createElement('td');
                    offerPriceCell.innerHTML = `<input type="number" name="offer_price[]" class="form-control" placeholder="Enter Offer Price" step="0.01" required>`;

                    const mrpCell = document.createElement('td');
                    mrpCell.innerHTML = `<input type="number" name="mrp[]" class="form-control" placeholder="Enter MRP" step="0.01" required>`;

                    const coinsCell = document.createElement('td');
                    coinsCell.innerHTML = `<input type="number" name="coins[]" class="form-control" placeholder="Enter coins" step="0.01" required>`;

                    // Append cells to the row
                    row.appendChild(sizeCell);
                    row.appendChild(offerPriceCell);
                    row.appendChild(mrpCell);
                    row.appendChild(coinsCell);

                    // Append the row to the table
                    tableBody.appendChild(row);
                });
            }

            // Initial call to populate sizes table based on default unit (ml)
            updateSizes();

            // Handle multiple sub-category selection with limit
            document.getElementById('SubCategoryId').addEventListener('change', function() {
                const selectedOptions = Array.from(this.selectedOptions);
                if (selectedOptions.length > 6) {
                    // Remove the last selected option if more than 6 are selected
                    selectedOptions[selectedOptions.length - 1].selected = false;
                    alert('You can select maximum 6 sub-categories. First selected will be the primary category.');
                }

                // Update the label to show count
                const label = document.querySelector('label[for="SubCategoryId"]');
                const count = selectedOptions.length;
                if (count > 0) {
                    label.textContent = `Sub Categories (${count}/6 selected)`;
                } else {
                    label.textContent = 'Sub Categories (Select up to 6)';
                }
            });

        </script>

        <!-- About Product JavaScript -->
        <script>
            // Initialize Summernote for about sections
            $(document).ready(function() {
                // Initialize summernote
                $('.summernote').summernote({
                    height: 200,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });


            });


        </script>

        <!-- Product Documents JavaScript -->
        <script>
            // Load existing documents when editing a product
            function loadProductDocuments(productId) {
                if (productId) {
                    fetch('get_product_documents.php?ProductId=' + productId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                displayDocuments(data.documents);
                            }
                        })
                        .catch(error => console.error('Error loading documents:', error));
                }
            }

            // Display documents in the list
            function displayDocuments(documents) {
                const container = document.getElementById('documents-container');
                container.innerHTML = '';

                if (documents.length === 0) {
                    container.innerHTML = '<p class="text-muted">No documents uploaded yet.</p>';
                    return;
                }

                documents.forEach(doc => {
                    const docElement = document.createElement('div');
                    docElement.className = 'document-item border p-3 mb-2';
                    docElement.innerHTML = `
                        <div class="row">
                            <div class="col-md-4">
                                <strong>${doc.document_title}</strong><br>
                                <small class="text-muted">${doc.document_type.replace('_', ' ').toUpperCase()}</small>
                            </div>
                            <div class="col-md-4">
                                <small>${doc.file_name}</small><br>
                                <small class="text-muted">${formatFileSize(doc.file_size)}</small>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="${doc.file_path}" target="_blank" class="btn btn-sm btn-info">View PDF</a>
                                <button onclick="deleteDocument(${doc.document_id})" class="btn btn-sm btn-danger">Delete</button>
                            </div>
                        </div>
                    `;
                    container.appendChild(docElement);
                });
            }

            // Add new document
            function addDocument() {
                const productId = document.getElementById('ProductId').value;
                const title = document.getElementById('DocumentTitle').value;
                const type = document.getElementById('DocumentType').value;
                const fileInput = document.getElementById('DocumentFile');

                if (!productId) {
                    alert('Please save the product first before adding documents.');
                    return;
                }

                if (!title || !fileInput.files[0]) {
                    alert('Please enter document title and select a PDF file.');
                    return;
                }

                const formData = new FormData();
                formData.append('ProductId', productId);
                formData.append('DocumentTitle', title);
                formData.append('DocumentType', type);
                formData.append('DocumentFile', fileInput.files[0]);

                fetch('upload_product_document.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('Response text:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            alert('Document uploaded successfully!');
                            document.getElementById('DocumentTitle').value = '';
                            fileInput.value = '';
                            loadProductDocuments(productId);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        alert('Server error: ' + text);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Network error uploading document: ' + error.message);
                });
            }

            // Delete document
            function deleteDocument(documentId) {
                if (confirm('Are you sure you want to delete this document?')) {
                    fetch('delete_product_document.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({document_id: documentId})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Document deleted successfully!');
                            const productId = document.getElementById('ProductId').value;
                            loadProductDocuments(productId);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting document.');
                    });
                }
            }

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Load documents when page loads if editing
            document.addEventListener('DOMContentLoaded', function() {
                const productId = document.getElementById('ProductId').value;
                if (productId) {
                    loadProductDocuments(productId);
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
<?php
    } else {
      header('Location: index.php');
    }
    ?>
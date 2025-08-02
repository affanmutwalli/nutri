<?php
include('includes/urls.php');
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

// Initialize database connection - avoid duplicate includes
if (!class_exists('main')) {
    include("database/dbconnection.php");
}
$obj = new main();
$obj->connection();

sec_session_start();
if (login_check($mysqli) == true) 
{
// 	getPassword("admin",$mysqli);

  if(isset($_GET) && array_key_exists("OrderId",$_GET))
  {
    $FieldNames=array("Id", "OrderId", "CustomerId", "OrderDate", "Amount","TransactionId", "PaymentStatus", "OrderStatus", "ShipAddress", "PaymentType","CustomerType");
    $ParamArray=array();
    $ParamArray[0]=$_GET["OrderId"];
    $Fields=implode(",",$FieldNames);
    $order_master=$obj->MysqliSelect1("Select ".$Fields." from order_master where OrderId= ? ",$FieldNames,"s",$ParamArray);

    // Check if order exists
    if($order_master == null || !isset($order_master[0])) {
        echo "Order not found.";
        exit();
    }

    $FieldNames=array("ProductId", "Quantity", "Price", "SubTotal","ProductCode");
    $ParamArray=array();
    $ParamArray[0]=$_GET["OrderId"];
    $Fields=implode(",",$FieldNames);
    $order_details=$obj->MysqliSelect1("Select ".$Fields." from order_details where OrderId= ? ",$FieldNames,"s",$ParamArray);

    // Check if this is a combo order and handle missing order_details
    $isComboOrder = strpos($_GET["OrderId"], 'CB') === 0;
    $combo_data = null;

    if ($isComboOrder && empty($order_details)) {
        // Try to get combo tracking data
        $comboFieldNames = array("combo_id", "combo_name", "combo_price", "quantity", "total_amount");
        $comboParamArray = array($_GET["OrderId"]);
        $comboFields = implode(",", $comboFieldNames);
        $combo_tracking = $obj->MysqliSelect1("SELECT ".$comboFields." FROM combo_order_tracking WHERE order_id = ?", $comboFieldNames, "s", $comboParamArray);

        if (!empty($combo_tracking)) {
            $combo_data = $combo_tracking[0];
        } else {
            // Fallback: Try to find the combo based on order amount
            $orderAmount = $order_master[0]["Amount"];
            $comboFallbackFields = array("combo_id", "combo_name", "combo_price", "product1_id", "product2_id");
            $comboFallbackParams = array($orderAmount);
            $combo_fallback = $obj->MysqliSelect1("SELECT ".implode(",", $comboFallbackFields)." FROM dynamic_combos WHERE combo_price = ? LIMIT 1", $comboFallbackFields, "d", $comboFallbackParams);

            if (!empty($combo_fallback)) {
                $combo_data = array(
                    'combo_id' => $combo_fallback[0]['combo_id'],
                    'combo_name' => $combo_fallback[0]['combo_name'],
                    'combo_price' => $combo_fallback[0]['combo_price'],
                    'quantity' => 1,
                    'total_amount' => $orderAmount,
                    'product1_id' => $combo_fallback[0]['product1_id'],
                    'product2_id' => $combo_fallback[0]['product2_id']
                );
            }
        }
    }

      $Name = "";
      $MobileNo = "";
      $Email = "";
      $IsActive = "";
      $Address = "";
      $Landmark = "";
      $State = "";
      $City = "";
      $Pincode = "";

    if($order_master[0]["CustomerType"] == "Direct"){

      $FieldNames=array("CustomerName", "MobileNo", "Email","Address","City","Pincode","State");
      $ParamArray=array();
      $ParamArray[0]=$order_master[0]["CustomerId"];
      $Fields=implode(",",$FieldNames);
      $customer_details=$obj->MysqliSelect1("Select ".$Fields." from direct_customers where CustomerId= ? ",$FieldNames,"i",$ParamArray);

      // Check if customer details exist and set values safely
      $Name = ($customer_details && isset($customer_details[0]["CustomerName"])) ? $customer_details[0]["CustomerName"] : "";
      $MobileNo = ($customer_details && isset($customer_details[0]["MobileNo"])) ? $customer_details[0]["MobileNo"] : "";
      $Email = ($customer_details && isset($customer_details[0]["Email"])) ? $customer_details[0]["Email"] : "";

      $Address = ($customer_details && isset($customer_details[0]["Address"])) ? $customer_details[0]["Address"] : "";
      $State = ($customer_details && isset($customer_details[0]["State"])) ? $customer_details[0]["State"] : "";
      $City = ($customer_details && isset($customer_details[0]["City"])) ? $customer_details[0]["City"] : "";
      $Pincode = ($customer_details && isset($customer_details[0]["Pincode"])) ? $customer_details[0]["Pincode"] : "";

      // Set ShipAddress for direct customers too
      $ShipAddress = $Address . ", " . $City . ", " . $Pincode . ", " . $State;

    }
    else {
      $FieldNames=array("Name", "MobileNo", "Email", "IsActive");
      $ParamArray=array();
      $ParamArray[0]=$order_master[0]["CustomerId"];
      $Fields=implode(",",$FieldNames);
      $customer_details=$obj->MysqliSelect1("Select ".$Fields." from customer_master where CustomerId= ? ",$FieldNames,"i",$ParamArray);

      $FieldNames=array("Address", "Landmark", "State", "City","PinCode");
      $ParamArray=array();
      $ParamArray[0]=$order_master[0]["CustomerId"];
      $Fields=implode(",",$FieldNames);
      $customer_address=$obj->MysqliSelect1("Select ".$Fields." from customer_address where CustomerId= ? ",$FieldNames,"i",$ParamArray);

      // Check if customer details exist and set values safely
      $Name = ($customer_details && isset($customer_details[0]["Name"])) ? $customer_details[0]["Name"] : "";
      $MobileNo = ($customer_details && isset($customer_details[0]["MobileNo"])) ? $customer_details[0]["MobileNo"] : "";
      $Email = ($customer_details && isset($customer_details[0]["Email"])) ? $customer_details[0]["Email"] : "";
      $IsActive = ($customer_details && isset($customer_details[0]["IsActive"])) ? $customer_details[0]["IsActive"] : "";

      // Try to get address from customer_address table first
      if ($customer_address && !empty($customer_address[0]["Address"])) {
          $Address = $customer_address[0]["Address"];
          $Landmark = $customer_address[0]["Landmark"];
          $State = $customer_address[0]["State"];
          $City = $customer_address[0]["City"];
          $Pincode = $customer_address[0]["PinCode"];
          $ShipAddress = $Address . ", " . $Landmark . ", " . $City . ", " . $Pincode . ", " . $State;
      } else {
          // Fallback: Use the ShipAddress from order_master if customer_address is empty
          $ShipAddress = $order_master[0]["ShipAddress"];
          // Try to parse the address components from ShipAddress
          $addressParts = explode(", ", $ShipAddress);
          $Address = isset($addressParts[0]) ? $addressParts[0] : "";
          $Landmark = isset($addressParts[1]) ? $addressParts[1] : "";
          $City = isset($addressParts[2]) ? $addressParts[2] : "";
          $State = isset($addressParts[3]) ? $addressParts[3] : "";
          $Pincode = isset($addressParts[4]) ? $addressParts[4] : "";
      }

    }
    
  }
?>
<?php
$selected = "dashboard.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM | Today's Order</title>

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
<style>
  .logo_shipping {
    width: 300px; /* Adjust the width as needed */
    height: auto; /* Maintain aspect ratio */
    display: inline-block; /* Ensures it aligns properly */
    vertical-align: middle; /* Aligns with text */
}



</style>
<body class="hold-transition sidebar-mini layout-fixed">
    <div id="loading"></div>
    <div class="wrapper">
        <?php include('components/sidebar.php');?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <!-- /.content-header -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Invoice</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Invoice</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">


            <!-- Main content -->
             

<div class="invoice p-3 mb-3">
    <!-- Title Row -->
    <div class="row">
        <div class="col-12">
            <h4>
                <img src="../image/main_logo.png" class="logo_shipping"/> 
                <small class="float-right">Date: <?php echo date("Y-m-d"); ?></small>
            </h4>
        </div>
    </div>
    
    <!-- Info Row -->
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            From
            <address>
                <strong>My Nutrify</strong><br>
                55 North Shivaji Nagar,<br>
                Near Apta Police Chowk,<br>
                Sangli - 416416.<br>
                Phone: +91 9834243754<br>
                Email: support@mynutrify.com
            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            To
            <address>
                <strong><?php echo $Name; ?></strong><br>
                <?php echo $ShipAddress; ?><br>
                <?php echo $City; ?><br>
                <?php echo $State; ?><br>
                <?php echo $Pincode; ?><br>
                <br>
                Phone: <?php echo $MobileNo; ?><br>
                Email: <?php echo $Email; ?>
            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            <h5>Order ID : <?php echo $order_master[0]["OrderId"]; ?></h5><br>
            <b>Transaction : <?php echo empty($order_master[0]["TransactionId"]) ? 'NA' : $order_master[0]["TransactionId"]; ?></b><br>
            <b>Payment Type:</b> <?php echo $order_master[0]["PaymentType"]; ?><br>
            <b>Payment Status:</b> <?php echo $order_master[0]["PaymentStatus"]; ?><br>
            <b>AWB / Way Bill:</b> <?php echo !empty($order_master[0]["Waybill"]) ? $order_master[0]["Waybill"] : 'Not Assigned'; ?><br>
        </div>
    </div>

    <!-- Table Row -->
    <div class="row">
        <div class="col-12 table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    
                    <th>Product</th>
                    <th>Product Code</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
              <?php
              $totalAmount = 0;

              // Check if we have regular order details
              if (!empty($order_details) && is_array($order_details)) {
                  foreach ($order_details as $item) {
                      // Fetch product name using ProductId
                      $FieldNames = array("ProductName");
                      $ParamArray = array($item["ProductId"]);  // Fixed ProductId reference
                      $Fields = implode(",", $FieldNames);
                      $product_data = $obj->MysqliSelect1("SELECT ".$Fields." FROM product_master WHERE ProductId = ?", $FieldNames, "i", $ParamArray);

                      $ProductName = $product_data[0]["ProductName"] ?? "Unknown Product"; // Handle missing product name
                      $subtotal = $item["Quantity"] * $item["Price"]; // Fixed subtotal calculation
                      $totalAmount += $subtotal; // Accumulate total amount

                      echo "<tr>
                              <td>{$ProductName}</td>
                              <td>{$item['ProductCode']}</td>
                              <td>{$item['Quantity']}</td>
                              <td>{$item['Price']}</td>
                              <td>{$subtotal}</td>
                            </tr>";
                  }
              }
              // Handle combo orders without order_details
              elseif ($isComboOrder && !empty($combo_data)) {
                  $comboName = $combo_data['combo_name'] ?? 'Combo Product';
                  $comboCode = $combo_data['combo_id'] ?? 'COMBO';
                  $comboQuantity = $combo_data['quantity'] ?? 1;
                  $comboPrice = $combo_data['combo_price'] ?? $order_master[0]["Amount"];
                  $comboSubtotal = $comboQuantity * $comboPrice;
                  $totalAmount = $comboSubtotal;

                  echo "<tr>
                          <td>{$comboName}</td>
                          <td>{$comboCode}</td>
                          <td>{$comboQuantity}</td>
                          <td>{$comboPrice}</td>
                          <td>{$comboSubtotal}</td>
                        </tr>";

                  // If we have individual product info, show them as sub-items
                  if (isset($combo_data['product1_id']) && isset($combo_data['product2_id'])) {
                      // Get product names for the combo items
                      $product1_data = $obj->MysqliSelect1("SELECT ProductName FROM product_master WHERE ProductId = ?", array("ProductName"), "i", array($combo_data['product1_id']));
                      $product2_data = $obj->MysqliSelect1("SELECT ProductName FROM product_master WHERE ProductId = ?", array("ProductName"), "i", array($combo_data['product2_id']));

                      $product1Name = $product1_data[0]["ProductName"] ?? "Product 1";
                      $product2Name = $product2_data[0]["ProductName"] ?? "Product 2";

                      echo "<tr style='background-color: #f8f9fa; font-size: 0.9em;'>
                              <td style='padding-left: 20px;'>├─ {$product1Name}</td>
                              <td>COMBO-P1-{$combo_data['product1_id']}</td>
                              <td>{$comboQuantity}</td>
                              <td>-</td>
                              <td>-</td>
                            </tr>";
                      echo "<tr style='background-color: #f8f9fa; font-size: 0.9em;'>
                              <td style='padding-left: 20px;'>└─ {$product2Name}</td>
                              <td>COMBO-P2-{$combo_data['product2_id']}</td>
                              <td>{$comboQuantity}</td>
                              <td>-</td>
                              <td>-</td>
                            </tr>";
                  }
              }
              // Fallback for combo orders without any data
              elseif ($isComboOrder) {
                  $orderAmount = $order_master[0]["Amount"];
                  $totalAmount = $orderAmount;

                  echo "<tr>
                          <td>Combo Order (Details Unavailable)</td>
                          <td>{$_GET['OrderId']}</td>
                          <td>1</td>
                          <td>{$orderAmount}</td>
                          <td>{$orderAmount}</td>
                        </tr>";
              }
              // Regular orders with no details
              else {
                  echo "<tr><td colspan='5'>No order details found.</td></tr>";
              }
              ?>
          </tbody>

        </table>

        </div>
    </div>

    <div class="row">
        <!-- Payment Methods -->
        <div class="col-6">
        </div>

        <div class="col-6">
            <p class="lead">Amount Due</p>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th style="width:50%">Subtotal:</th>
                        <td><?php echo number_format($totalAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td><?php echo $order_master[0]["Amount"]; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="row no-print">
        <div class="col-12">
            <a href="invoice-print.html" rel="noopener" target="_blank" class="btn btn-default">
                <i class="fas fa-print"></i> Print
            </a>
            <?php if(!empty($order_master[0]["Waybill"])) { ?>
                <button type="button" class="btn btn-info float-right" onclick="window.open('https://www.delhivery.com/track/package/<?php echo $order_master[0]["Waybill"]; ?>', '_blank')">
                    <i class="fas fa-truck"></i> Track Package
                </button>
            <?php } else { ?>
                <span class="btn btn-warning float-right">
                    <i class="fas fa-clock"></i> Processing Order
                </span>
            <?php } ?>
            <a href="generate_invoice_pdf.php?order_id=<?php echo urlencode($order_master[0]['OrderId']); ?>"
               class="btn btn-primary float-right" style="margin-right: 5px;" target="_blank">
                <i class="fas fa-download"></i> Generate PDF
            </a>
        </div>
    </div>
</div>

            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
            <!-- Main content List-->

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
            "lengthChange": false,
            "autoWidth": false
            /*"buttons": ["excel", "pdf"]*/
        }) /*.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)')*/ ;
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
    <script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".btn-primary").addEventListener("click", function () {
        var docDefinition = {
            content: [
                { text: 'Order Invoice', style: 'header' },
                { text: '----------------------------------------', style: 'subheader' },

                { text: 'Order Details', style: 'section' },
                { text: 'Order ID: MN000131', style: 'details' },
                { text: 'AWB No: 30983010000173', style: 'details' },

                { text: '----------------------------------------', style: 'subheader' },

                { text: 'Pickup Address:', style: 'section' },
                { text: 'My Nutrify, Sangli, Maharashtra, 416416', style: 'details' },

                { text: 'Delivery Address:', style: 'section' },
                { text: 'Shivam Kamboj (Radaur - 135133)', style: 'details' },

                { text: '----------------------------------------', style: 'subheader' },

                { text: 'Package Details:', style: 'section' },
                { text: 'Weight: 1180 gm', style: 'details' },
                { text: 'Dimensions: 8 x 8 x 26 cm', style: 'details' },

                { text: '----------------------------------------', style: 'subheader' },

                { text: 'Payment Mode: Cash on Delivery', style: 'section' },
                { text: 'Total Amount: ₹499.00', style: 'details' },

                { text: '----------------------------------------', style: 'subheader' },

                { text: 'Thank you for your order!', style: 'footer' }
            ],
            styles: {
                header: { fontSize: 18, bold: true, alignment: 'center', margin: [0, 0, 0, 10] },
                subheader: { fontSize: 12, bold: true, alignment: 'center', margin: [0, 5, 0, 5] },
                section: { fontSize: 12, bold: true, margin: [0, 10, 0, 5] },
                details: { fontSize: 10, margin: [0, 2, 0, 2] },
                footer: { fontSize: 12, bold: true, alignment: 'center', margin: [0, 10, 0, 0] }
            }
        };

        pdfMake.createPdf(docDefinition).download("Order_Invoice.pdf");
    });
});
</script>

    <script type="text/javascript">
    function save_data() {

        var AllRight = 1;
        var errorstring = "";


        var CategoryName = document.getElementById("CategoryName").value;

        if (CategoryName == "") {
            AllRight = 0;
            $('#CategoryName').addClass('is-invalid');


        } else {
            $("#CategoryName").removeClass("is-invalid");
        }

        if (AllRight == 1) {
            $("#frmData").ajaxForm({
                dataType: 'json',
                beforeSend: function() {
                    document.getElementById("loading").style.display = "block";
                },
                success: function(data) {
                    if (data.response == "S") {
                        window.location = "category.php";
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

    function fundelete(id1) {
        document.getElementById("DCategoryId").value = id1;
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
                    window.location = "category.php";
                } else {
                    document.getElementById("ErrorMessage").innerHTML = data.msg;
                    document.getElementById("loading").style.display = "none";

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
    <!-- Order details page scripts -->
    <script>
    // Order details page loaded
    console.log('Order details page loaded');
    </script>


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
                    <form action="delete_category.php" method="post" name="frmDeleteFormData" id="frmDeleteFormData"
                        enctype="multipart/form-data">
                        <input type="hidden" id="DCategoryId" name="DCategoryId" />
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
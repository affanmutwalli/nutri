<?php
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

$selected = "offers_management.php";
$page = "offers_management.php";

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'toggle_offer') {
        $productId = intval($_POST['product_id']);
        $isActive = intval($_POST['is_active']);
        
        if ($isActive) {
            // Add to offers
            $stmt = $mysqli->prepare("INSERT INTO product_offers (product_id, is_active) VALUES (?, 1) ON DUPLICATE KEY UPDATE is_active = 1, updated_date = CURRENT_TIMESTAMP");
            $stmt->bind_param("i", $productId);
        } else {
            // Remove from offers
            $stmt = $mysqli->prepare("UPDATE product_offers SET is_active = 0, updated_date = CURRENT_TIMESTAMP WHERE product_id = ?");
            $stmt->bind_param("i", $productId);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Offer status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating offer status']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'update_offer_details') {
        $productId = intval($_POST['product_id']);
        $offerTitle = trim($_POST['offer_title']);
        $offerDescription = trim($_POST['offer_description']);
        
        $stmt = $mysqli->prepare("UPDATE product_offers SET offer_title = ?, offer_description = ?, updated_date = CURRENT_TIMESTAMP WHERE product_id = ?");
        $stmt->bind_param("ssi", $offerTitle, $offerDescription, $productId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Offer details updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating offer details']);
        }
        exit();
    }
}

// Fetch all products with their offer status
$query = "
    SELECT 
        pm.ProductId,
        pm.ProductName,
        pm.PhotoPath,
        pm.ShortDescription,
        po.offer_id,
        po.offer_title,
        po.offer_description,
        po.is_active as offer_active,
        po.created_date as offer_created,
        MIN(pp.OfferPrice) as min_offer_price,
        MIN(pp.MRP) as min_mrp,
        (MIN(pp.MRP) - MIN(pp.OfferPrice)) as savings_amount
    FROM product_master pm
    LEFT JOIN product_offers po ON pm.ProductId = po.product_id
    LEFT JOIN product_price pp ON pm.ProductId = pp.ProductId
    GROUP BY pm.ProductId, pm.ProductName, pm.PhotoPath, pm.ShortDescription, 
             po.offer_id, po.offer_title, po.offer_description, po.is_active, po.created_date
    ORDER BY pm.ProductName ASC
";

$result = $mysqli->query($query);
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Offers Management | MyNutrify CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <style>
        .offer-status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .offer-active {
            background-color: #28a745;
            color: white;
        }
        .offer-inactive {
            background-color: #6c757d;
            color: white;
        }
        .savings-badge {
            background-color: #ff6b35;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .offer-details-form {
            display: none;
            margin-top: 10px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include('components/sidebar.php'); ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Offers Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Offers Management</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Product Offers</h3>
                        <div class="card-tools">
                            <span class="badge badge-info">Total Products: <?php echo count($products); ?></span>
                            <span class="badge badge-success">Active Offers: <?php echo count(array_filter($products, function($p) { return $p['offer_active'] == 1; })); ?></span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div id="alert-container"></div>
                        
                        <table id="offersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Price Info</th>
                                    <th>Offer Status</th>
                                    <th>Offer Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr data-product-id="<?php echo $product['ProductId']; ?>">
                                    <td>
                                        <img src="images/products/<?php echo htmlspecialchars($product['PhotoPath']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['ProductName']); ?>" 
                                             class="product-image">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['ProductName']); ?></strong>
                                        <?php if ($product['ShortDescription']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($product['ShortDescription'], 0, 100)); ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['min_offer_price'] && $product['min_mrp']): ?>
                                        <div>₹<?php echo number_format($product['min_offer_price'], 2); ?> <del class="text-muted">₹<?php echo number_format($product['min_mrp'], 2); ?></del></div>
                                        <?php if ($product['savings_amount'] > 0): ?>
                                        <span class="savings-badge">Save ₹<?php echo number_format($product['savings_amount'], 0); ?></span>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <span class="text-muted">No pricing data</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="offer-status-badge <?php echo ($product['offer_active'] == 1) ? 'offer-active' : 'offer-inactive'; ?>">
                                            <?php echo ($product['offer_active'] == 1) ? 'Active Offer' : 'No Offer'; ?>
                                        </span>
                                        <?php if ($product['offer_created']): ?>
                                        <br><small class="text-muted">Created: <?php echo date('M j, Y', strtotime($product['offer_created'])); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['offer_active'] == 1): ?>
                                        <div>
                                            <strong>Title:</strong> <?php echo htmlspecialchars($product['offer_title'] ?: 'No title'); ?><br>
                                            <strong>Description:</strong> <?php echo htmlspecialchars($product['offer_description'] ?: 'No description'); ?>
                                        </div>
                                        <?php else: ?>
                                        <span class="text-muted">Not applicable</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm <?php echo ($product['offer_active'] == 1) ? 'btn-danger' : 'btn-success'; ?> toggle-offer-btn" 
                                                data-product-id="<?php echo $product['ProductId']; ?>"
                                                data-current-status="<?php echo $product['offer_active'] ?: 0; ?>">
                                            <i class="fas <?php echo ($product['offer_active'] == 1) ? 'fa-times' : 'fa-plus'; ?>"></i>
                                            <?php echo ($product['offer_active'] == 1) ? 'Remove Offer' : 'Add to Offers'; ?>
                                        </button>
                                        
                                        <?php if ($product['offer_active'] == 1): ?>
                                        <button class="btn btn-sm btn-info edit-details-btn" 
                                                data-product-id="<?php echo $product['ProductId']; ?>">
                                            <i class="fas fa-edit"></i> Edit Details
                                        </button>
                                        <?php endif; ?>
                                        
                                        <!-- Offer Details Form (hidden by default) -->
                                        <div class="offer-details-form" id="form-<?php echo $product['ProductId']; ?>">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Offer Title:</label>
                                                    <input type="text" class="form-control offer-title" 
                                                           value="<?php echo htmlspecialchars($product['offer_title'] ?: ''); ?>" 
                                                           placeholder="e.g., Special Launch Offer">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Offer Description:</label>
                                                    <textarea class="form-control offer-description" rows="2" 
                                                              placeholder="Brief description of the offer"><?php echo htmlspecialchars($product['offer_description'] ?: ''); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-primary save-details-btn" 
                                                        data-product-id="<?php echo $product['ProductId']; ?>">
                                                    <i class="fas fa-save"></i> Save Details
                                                </button>
                                                <button class="btn btn-sm btn-secondary cancel-edit-btn">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        
        <?php include('components/footer.php'); ?>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#offersTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [[1, "asc"]]
            });

            // Toggle offer status
            $('.toggle-offer-btn').click(function() {
                const productId = $(this).data('product-id');
                const currentStatus = $(this).data('current-status');
                const newStatus = currentStatus == 1 ? 0 : 1;
                const button = $(this);
                
                $.post('offers_management.php', {
                    action: 'toggle_offer',
                    product_id: productId,
                    is_active: newStatus
                }, function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('danger', response.message);
                    }
                }, 'json').fail(function() {
                    showAlert('danger', 'Error processing request');
                });
            });

            // Show edit details form
            $('.edit-details-btn').click(function() {
                const productId = $(this).data('product-id');
                $('#form-' + productId).slideDown();
                $(this).hide();
            });

            // Cancel edit
            $('.cancel-edit-btn').click(function() {
                $(this).closest('.offer-details-form').slideUp();
                $(this).closest('td').find('.edit-details-btn').show();
            });

            // Save offer details
            $('.save-details-btn').click(function() {
                const productId = $(this).data('product-id');
                const form = $('#form-' + productId);
                const title = form.find('.offer-title').val();
                const description = form.find('.offer-description').val();
                
                $.post('offers_management.php', {
                    action: 'update_offer_details',
                    product_id: productId,
                    offer_title: title,
                    offer_description: description
                }, function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('danger', response.message);
                    }
                }, 'json').fail(function() {
                    showAlert('danger', 'Error processing request');
                });
            });

            function showAlert(type, message) {
                const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;
                $('#alert-container').html(alert);
                
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 3000);
            }
        });
    </script>
</body>
</html>

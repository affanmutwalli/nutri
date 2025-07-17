
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      
    </ul>
    <!-- Right navbar links -->
    	<ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="includes/logout.php" role="button">
              <i class="fas fa-lock"></i>
              Log Out
            </a>
          </li>
         </ul>	
  </nav>
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="dashboard.php" class="brand-link">
  <img alt="MyNutrify Logo" src="images/logo.jpg" width="100%" style="padding:5x;" /> 
  </a>
       <!-- Sidebar -->
    <div class="sidebar">
      
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php if($selected == "dashboard.php") echo "active"; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview <?php if(in_array($selected, ["home.php", "all_orders.php", "delivered_order.php", "todays_order.php", "month_order.php", "approve_orders.php"])) echo "menu-open"; ?>">
            <a href="#" class="nav-link <?php if(in_array($selected, ["home.php", "all_orders.php", "delivered_order.php", "todays_order.php", "month_order.php", "approve_orders.php"])) echo "active"; ?>">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Orders
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="approve_orders.php" class="nav-link <?php if($selected == "approve_orders.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Approve Orders</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="all_orders.php" class="nav-link <?php if($selected == "all_orders.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Orders</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="todays_order.php" class="nav-link <?php if($selected == "todays_order.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Today's Orders</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="delivered_order.php" class="nav-link <?php if($selected == "delivered_order.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Delivered Orders</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="month_order.php" class="nav-link <?php if($selected == "month_order.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Monthly Orders</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="track_order.php" class="nav-link <?php if($selected == "track_order.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Track Order</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- Delivery Management Section -->
          <li class="nav-item has-treeview <?php if(in_array($selected, ["delivery_dashboard.php", "delivery_settings.php", "delivery_providers.php", "delivery_tracking.php", "bulk_delivery.php", "delivery_logs.php", "test_mode_config.php", "test_delhivery.php"])) echo "menu-open"; ?>">
            <a href="#" class="nav-link <?php if(in_array($selected, ["delivery_dashboard.php", "delivery_settings.php", "delivery_providers.php", "delivery_tracking.php", "bulk_delivery.php", "delivery_logs.php", "test_mode_config.php", "test_delhivery.php"])) echo "active"; ?>">
              <i class="nav-icon fas fa-shipping-fast"></i>
              <p>
                Delivery Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="delivery_dashboard.php" class="nav-link <?php if($selected == "delivery_dashboard.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Delivery Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="delivery_settings.php" class="nav-link <?php if($selected == "delivery_settings.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Delivery Settings</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="delivery_providers.php" class="nav-link <?php if($selected == "delivery_providers.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Provider Management</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="delivery_tracking.php" class="nav-link <?php if($selected == "delivery_tracking.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Enhanced Tracking</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="bulk_delivery.php" class="nav-link <?php if($selected == "bulk_delivery.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Bulk Processing</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="delivery_logs.php" class="nav-link <?php if($selected == "delivery_logs.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Delivery Logs</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="test_mode_config.php" class="nav-link <?php if($selected == "test_mode_config.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Test Mode Config</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="test_delhivery.php" class="nav-link <?php if($selected == "test_delhivery.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Test Integration</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- Razorpay Management Section -->
          <li class="nav-item has-treeview <?php if(in_array($selected, ["razorpay_dashboard.php", "razorpay_transactions.php", "razorpay_payments.php", "razorpay_refunds.php", "razorpay_analytics.php", "razorpay_settings.php"])) echo "menu-open"; ?>">
            <a href="#" class="nav-link <?php if(in_array($selected, ["razorpay_dashboard.php", "razorpay_transactions.php", "razorpay_payments.php", "razorpay_refunds.php", "razorpay_analytics.php", "razorpay_settings.php"])) echo "active"; ?>">
              <i class="nav-icon fas fa-credit-card"></i>
              <p>
                Razorpay Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="razorpay_dashboard.php" class="nav-link <?php if($selected == "razorpay_dashboard.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Payment Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="razorpay_transactions.php" class="nav-link <?php if($selected == "razorpay_transactions.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Transactions</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="razorpay_payments.php" class="nav-link <?php if($selected == "razorpay_payments.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Payment Status</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="razorpay_refunds.php" class="nav-link <?php if($selected == "razorpay_refunds.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Refund Management</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="razorpay_analytics.php" class="nav-link <?php if($selected == "razorpay_analytics.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Payment Analytics</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="razorpay_settings.php" class="nav-link <?php if($selected == "razorpay_settings.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>API Settings</p>
                </a>
              </li>
            </ul>
          </li>

          <?php
           if($_SESSION["email"] == "admin")
           {
            ?>
            <li class="nav-item">
                <a href="create_user.php" class="nav-link <?php if($selected == "user.php") echo "active"; ?>">
                  <i class="nav-icon fas fa-lock"></i>
                  <p>
                    Create User
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="change_password.php" class="nav-link <?php if($selected == "change_password.php") echo "active"; ?>">
                  <i class="nav-icon fas fa-key"></i>
                  <p>
                    Change Password
                  </p>
                </a>
              </li>
            <?php
           }
          ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

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
          <li class="nav-item has-treeview <?php if($selected == "home.php") echo "menu-open"; ?>">
            <a href="#" class="nav-link <?php if($selected == "home.php") echo "active"; ?>">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Website Content
                <i class="fas fa-angle-left right"></i>
               
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="banners.php" class="nav-link <?php if($page == "banners.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Banners</p>
                </a>
              </li> 
               <li class="nav-item">
                <a href="blogs.php" class="nav-link <?php if($page == "blogs.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Blogs</p>
                </a>
              </li>   
            </ul>
          </li>
          <li class="nav-item has-treeview <?php if($selected == "catlog.php") echo "menu-open"; ?>">
            <a href="#" class="nav-link <?php if($selected == "catlog.php") echo "active"; ?>">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Catlog
                <i class="fas fa-angle-left right"></i>
               
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="category.php" class="nav-link <?php if($page == "category.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Category</p>
                </a>
              </li> 
               <li class="nav-item">
                <a href="sub_category.php" class="nav-link <?php if($page == "sub_category.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sub Category</p>
                </a>
              </li>   
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="products.php" class="nav-link <?php if($page == "products.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Products</p>
                </a>
              </li>     
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="create_product_code.php" class="nav-link <?php if($page == "ingredients .php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Product Ingredients </p>
                </a>
              </li>     
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="create_product_code.php" class="nav-link <?php if($page == "product_details.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Product Details</p>
                </a>
              </li>     
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="create_product_code.php" class="nav-link <?php if($page == "create_product_code.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Product Authenticity</p>
                </a>
              </li>     
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="verify_product.php" class="nav-link <?php if($page == "verify_product.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Check Product Authenticity</p>
                </a>
              </li>     
            </ul>
          </li>
          <li class="nav-item has-treeview <?php if($selected == "product_details.php") echo "menu-open"; ?>">
                    <a href="#" class="nav-link <?php if($selected == "product_details.php") echo "active"; ?>">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Product Details
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="product_details.php"
                                class="nav-link <?php if($page == "product_details.php") echo "active"; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Details</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Ingredients.php"
                                class="nav-link <?php if($page == "Ingredients.php") echo "active"; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Ingredients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="product_review.php"
                                class="nav-link <?php if($page == "product_review.php") echo "active"; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Review</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="product_benifit.php"
                                class="nav-link <?php if($page == "product_benifit.php") echo "active"; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Benefits</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="product_faq.php"
                                class="nav-link <?php if($page == "product_faq.php") echo "active"; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>FAQ's</p>
                            </a>
                        </li>
                    </ul>
                </li>

          <!-- Customer Rewards & Coupons Section -->
          <li class="nav-item has-treeview <?php if(in_array($selected, ["enhanced_rewards_dashboard.php", "rewards_dashboard.php", "coupon_management.php", "rewards_management.php", "customer_points.php", "rewards_settings.php", "rewards_reports.php", "rewards_analytics.php"])) echo "menu-open"; ?>">
            <a href="#" class="nav-link <?php if(in_array($selected, ["enhanced_rewards_dashboard.php", "rewards_dashboard.php", "coupon_management.php", "rewards_management.php", "customer_points.php", "rewards_settings.php", "rewards_reports.php", "rewards_analytics.php"])) echo "active"; ?>">
              <i class="nav-icon fas fa-gift"></i>
              <p>
                Customer Rewards
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="enhanced_rewards_dashboard.php" class="nav-link <?php if($page == "enhanced_rewards_dashboard.php") echo "active"; ?>">
                  <i class="fas fa-tachometer-alt nav-icon"></i>
                  <p>Enhanced Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="rewards_dashboard.php" class="nav-link <?php if($page == "rewards_dashboard.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Basic Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="coupon_management.php" class="nav-link <?php if($page == "coupon_management.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Coupon Management</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="rewards_management.php" class="nav-link <?php if($page == "rewards_management.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Rewards Catalog</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="customer_points.php" class="nav-link <?php if($page == "customer_points.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Customer Points</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="rewards_settings.php" class="nav-link <?php if($page == "rewards_settings.php") echo "active"; ?>">
                  <i class="fas fa-cog nav-icon"></i>
                  <p>Rewards Settings</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="rewards_reports.php" class="nav-link <?php if($page == "rewards_reports.php") echo "active"; ?>">
                  <i class="fas fa-chart-bar nav-icon"></i>
                  <p>Reports & Analytics</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="rewards_analytics.php" class="nav-link <?php if($page == "rewards_analytics.php") echo "active"; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Legacy Analytics</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- Website Analytics Section -->
          <li class="nav-item">
            <a href="analytics_dashboard.php" class="nav-link <?php if($selected == "analytics_dashboard.php") echo "active"; ?>">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Website Analytics
                <span class="badge badge-info right">NEW</span>
              </p>
            </a>
          </li>

          <!-- Contact Messages Section -->
          <li class="nav-item">
            <a href="contact_messages.php" class="nav-link <?php if($page == "contact_messages.php") echo "active"; ?>">
              <i class="nav-icon fas fa-envelope"></i>
              <p>
                Contact Messages
                <?php
                // Show unread count badge
                try {
                  $obj = new main();
                  $mysqli = $obj->connection();
                  if ($mysqli) {
                    $result = $mysqli->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'");
                    if ($result) {
                      $unread_count = $result->fetch_assoc()['count'];
                      if ($unread_count > 0) {
                        echo '<span class="badge badge-warning right">' . $unread_count . '</span>';
                      }
                    }
                  }
                } catch (Exception $e) {
                  // Ignore errors for badge display
                }
                ?>
              </p>
            </a>
          </li>

          <!-- Affiliate Applications Section -->
          <li class="nav-item">
            <a href="affiliate_applications.php" class="nav-link <?php if($page == "affiliate_applications.php") echo "active"; ?>">
              <i class="nav-icon fas fa-handshake"></i>
              <p>
                Affiliate Applications
                <?php
                // Show pending applications count badge
                try {
                  $obj = new main();
                  $mysqli = $obj->connection();
                  if ($mysqli) {
                    $result = $mysqli->query("SELECT COUNT(*) as count FROM affiliate_applications WHERE application_status = 'pending'");
                    if ($result) {
                      $pending_count = $result->fetch_assoc()['count'];
                      if ($pending_count > 0) {
                        echo '<span class="badge badge-danger right">' . $pending_count . '</span>';
                      }
                    }
                  }
                } catch (Exception $e) {
                  // Ignore errors for badge display
                }
                ?>
              </p>
            </a>
          </li>

          <?php
          // if($_SESSION["email"] == "admin")
          // {
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
          // }
          ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
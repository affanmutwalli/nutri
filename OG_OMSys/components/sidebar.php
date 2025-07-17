
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
                <a href="admin_master_password.php" class="nav-link <?php if($selected == "admin_master_password.php") echo "active"; ?>">
                  <i class="nav-icon fas fa-lock"></i>
                  <p>
                    Change Master Password
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
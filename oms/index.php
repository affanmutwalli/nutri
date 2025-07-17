<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
 
if (login_check($mysqli) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MyNutrify  | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <script type="text/JavaScript" src="js/sha512.js"></script> 
  <script type="text/JavaScript" src="js/forms.js"></script> 
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>My Nutrify </b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Login To Order Management System</p>
		<?php
            if(isset($_GET) && array_key_exists("error",$_GET))
            {
                echo '<p><b class="text text-danger">Invalid credentials</b></p>';
            }
        ?>
      <form action="includes/exe_login.php" method="post" name="login_form" enctype="multipart/form-data">
        <div class="input-group mb-3">
          <input type="UserId" class="form-control" placeholder="User Name" name="email" id="username" >
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" id="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            
          </div>
          <!-- /.col -->
          <div class="col-4">
             <button type="submit" onClick="formhash(this.form, this.form.password);" class="btn btn-primary btn-block">Sign In</button>
          </div>
          
          <!-- /.col -->
        </div>
      </form>

      
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
  <script>
    var OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
      OneSignal.init({
        appId: "YOUR_APP_ID",
        notifyButton: {
          enable: true,
        },
      });
      OneSignal.showNativePrompt();
    });
  </script>
</body>
</html>

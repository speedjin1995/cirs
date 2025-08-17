<?php
    $license = include(dirname(__DIR__, 2) . '/license.php');
    require_once 'php/db_connect.php';
    $company_name = '';
    $result = $db->query("SELECT name FROM companies LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $company = $result->fetch_assoc();
        $company_name = htmlspecialchars($company['name']); // Store name in variable
    }

    $db->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $company_name; ?> | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><img src="assets/logo.png" height="auto" width="100%"/></a>
  </div>
  <div class="text-center font-weight-bold">
    <p>Licensed By :</p>
    <p><?php echo $company_name; ?></p>
    <p style="margin-top:-5%">Valid: <?=$license['from'] ?> - <?=$license['to'] ?></p>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Please enter your registered email address</p>

      <form action="php/resetPassword.php" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" id="userEmail" name="userEmail" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-5">
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
          </div>
          <div class="col-2"></div>
          <div class="col-5">
            <a href="login.php"><button type="button" class="btn btn-primary btn-block">Back to Login</button></a>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>

  <div class="text-center font-weight-bold" style="margin-top:20%">
    <p>***** Ideal of Synctronix *****<br>Ver: 1.1.0</p>
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

</body>
</html>

<?php
$license = include(dirname(__DIR__, 2) . '/license.php');
require_once 'php/db_connect.php';
$company_name = '';
$result = $db->query("SELECT name FROM companies LIMIT 1");

if ($result && $result->num_rows > 0) {
    $company = $result->fetch_assoc();
    $company_name = htmlspecialchars($company['name']); // Store name in variable
}

session_start();

if (!isset($_GET['token']) && !isset($_POST['token'])) {
    die("Invalid or missing token.");
}

$token = isset($_GET['token']) ? $_GET['token'] : $_POST['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // Verify token
    $stmt = $db->prepare("SELECT id, email, salt FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if (($row = $result->fetch_assoc()) !== null) {
        $id = $row['id'];
        $salt = $row['salt'];

        // Hash new password with salt (same as your change-password.php)
        $hashedPassword = hash('sha512', $newPassword . $salt);

        // Update password and clear token
        $update = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $update->bind_param("ss", $hashedPassword, $id);

        if ($update->execute()) {
            echo "<script>alert('Password reset successful. Please login.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Database update failed.'); window.location.href='forgot-password.php';</script>";
        }

    } else {
        echo "<script>alert('Invalid or expired token.'); window.location.href='forgot-password.php';</script>";
    }

    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Password Reset</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font -->
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

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Enter your new password</p>

      <form action="reset-password.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="New Password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
          </div>
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="login.php">Back to Login</a>
      </p>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

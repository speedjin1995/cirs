<?php
require_once 'db_connect.php';
require '../vendor/autoload.php'; // make sure PHPMailer is installed via composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$username=$_POST['userEmail'];

$stmt = $db->prepare("SELECT * from users where email= ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if(($row = $result->fetch_assoc()) !== null){
	// Generate a secure token
    $token = bin2hex(random_bytes(50));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Save token and expiry in DB (make sure you have fields reset_token, reset_expires)
    $update = $db->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE email=?");
    $update->bind_param("sss", $token, $expires, $username);
    $update->execute();

    // Send email
    $resetLink = "https://syncweb.com.my/scm/reset-password.php?token=" . $token;

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; // your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'help@syncweb.com.my'; 
        $mail->Password   = '@Sync5500'; // use app password if Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('help@syncweb.com.my', 'Stamping Calibration Management System');
        $mail->addAddress($username);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Hello,<br><br>Click the link below to reset your password:<br>
                          <a href='$resetLink'>$resetLink</a><br><br>
                          This link will expire in 1 hour.";

        $mail->send();
        echo '<script>alert("Reset link has been sent to your email."); window.location.href="../login.php";</script>';
    } catch (Exception $e) {
        echo '<script>alert("Message could not be sent. Mailer Error: '. $mail->ErrorInfo .'"); window.location.href="../forgot-password.php";</script>';
    }
} 
else{
	echo '<script type="text/javascript">alert("Email Address is not matched");';
	echo 'window.location.href = "../forgot-password.php";</script>';
}

$stmt->close();
$db->close();
?>

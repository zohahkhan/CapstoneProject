<?php
require_once __DIR__ . '/../include/db_connect.php';

date_default_timezone_set('America/New_York');
$db->exec("SET time_zone = '-04:00'");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) 
{
    header('Location: ../index.php'); 
    exit();
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
{
    header('Location: forgot_password.html?error=invalid_email');
    exit();
}

$queryCheckEmail = 'SELECT user_id, first_name FROM `User` WHERE user_email = :email';
$statement = $db->prepare($queryCheckEmail);
$statement->bindParam(':email', $email);
$statement->execute();
$user = $statement->fetch();

if ($user) 
{
    $token = bin2hex(random_bytes(6));
    date_default_timezone_set('America/New_York');
    $expires_at = date('Y-m-d H:i:s', strtotime('+20 minutes'));
    
    $queryInsertToken = 'INSERT INTO PasswordResetToken (user_id, token, reset_success, expires_at) 
                         VALUES (:user_id, :token, 0, :expires_at)';
    $statement = $db->prepare($queryInsertToken);
    $statement->bindParam(':user_id', $user['user_id']);
    $statement->bindParam(':token', $token);
    $statement->bindParam(':expires_at', $expires_at);
    $statement->execute();

        $resetLink = 'https://capstone.ongkg.com/PswdRecovery/reset_password.php?token='
        . urlencode($token)
        . '&email=' . urlencode($email);

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.brevo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'a851db001@smtp-brevo.com';
        $mail->Password = 'YOUR_SMTP_PASSWORD_HERE';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply_capstone@ongkg.com', 'Capstone Support');
        $mail->addAddress($email, $user['first_name']);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = '
            <p>Hello ' . htmlspecialchars($user['first_name']) . ',</p>
            <p>We received a request to reset your password.</p>
            <p><a href="' . $resetLink . '">Click here to reset your password</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>If you did not request this, you can ignore this email.</p>
        ';
        $mail->AltBody = "Hello {$user['first_name']},\n\n"
            . "We received a request to reset your password.\n\n"
            . "Reset your password here: {$resetLink}\n\n"
            . "This link will expire in 1 hour.\n\n"
            . "If you did not request this, you can ignore this email.";

		$mail->send();

			header('Location: forgot_password_success.html?token=' . urlencode($token) . '&email=' . urlencode($email));

		exit();

} catch (Exception $e) {
    header('Location: forgot_password_success.html?token=' . urlencode($token) . '&email=' . urlencode($email));
    exit();
}

} else {
    header('Location: forgot_password_success.html');
    exit();
}
?>

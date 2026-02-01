<?php
require_once './include/db_connect.php';

session_start();

if (isset($_SESSION['user'])) 
{
    header('Location: index.php'); 
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
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $queryInsertToken = 'INSERT INTO PasswordResetToken (user_id, token, reset_success, expires_at) 
                         VALUES (:user_id, :token, 0, :expires_at)';
    $statement = $db->prepare($queryInsertToken);
    $statement->bindParam(':user_id', $user['user_id']);
    $statement->bindParam(':token', $token);
    $statement->bindParam(':expires_at', $expires_at);
    $statement->execute();
    
    header('Location: forgot_password_success.html?token=' . $token);
    exit();
} 
else 
{
    header('Location: forgot_password_success.html');
    exit();
}
?>

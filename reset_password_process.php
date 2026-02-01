<?php
require_once './include/db_connect.php';

session_start();

if (isset($_SESSION['user'])) 
{
    header('Location: hompage.html'); 
    exit();
}

$token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password');
$confirm_password = filter_input(INPUT_POST, 'confirm_password');

$queryCheckToken = 'SELECT prt.reset_id, prt.user_id, prt.expires_at, u.user_email 
                    FROM PasswordResetToken prt
                    JOIN `User` u ON prt.user_id = u.user_id
                    WHERE prt.token = :token 
                    AND prt.reset_success = 0
                    AND prt.expires_at > NOW()';
$statement = $db->prepare($queryCheckToken);
$statement->bindParam(':token', $token);
$statement->execute();
$reset_data = $statement->fetch();

if (!$reset_data) 
{
    header('Location: reset_password.html?error=invalid_token');
    exit();
}

if (strlen($password) < 8) 
{
    header('Location: reset_password.html?token=' . $token . '&error=short_password');
    exit();
}

if ($password !== $confirm_password) 
{
    header('Location: reset_password.html?token=' . $token . '&error=no_match');
    exit();
}

$password_hashed = password_hash($password, PASSWORD_DEFAULT);

$queryUpdatePassword = 'UPDATE `User` SET password_hashed = :password_hashed WHERE user_id = :user_id';
$statement = $db->prepare($queryUpdatePassword);
$statement->bindParam(':password_hashed', $password_hashed);
$statement->bindParam(':user_id', $reset_data['user_id']);
$statement->execute();

$queryMarkTokenUsed = 'UPDATE PasswordResetToken 
                       SET reset_success = 1, used_at = NOW() 
                       WHERE reset_id = :reset_id';
$statement = $db->prepare($queryMarkTokenUsed);
$statement->bindParam(':reset_id', $reset_data['reset_id']);
$statement->execute();

header('Location: reset_password.html?success=1');
exit();
?>
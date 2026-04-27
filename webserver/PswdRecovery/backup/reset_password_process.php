<?php
require_once __DIR__ . '/../include/db_connect.php';

session_start();

if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

$token = isset($_POST['token']) ? trim($_POST['token']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if ($token === '') {
    header('Location: reset_password.html?error=invalid_token');
    exit();
}

if ($email !== '') {
    $queryCheckToken = 'SELECT prt.reset_id, prt.user_id, u.user_email
                        FROM PasswordResetToken prt
                        JOIN `User` u ON prt.user_id = u.user_id
                        WHERE prt.token = :token
                          AND u.user_email = :email
                          AND prt.reset_success = 0
                          AND prt.expires_at > NOW()
                        LIMIT 1';
    $statement = $db->prepare($queryCheckToken);
    $statement->bindValue(':token', $token);
    $statement->bindValue(':email', $email);
} else {
    $queryCheckToken = 'SELECT prt.reset_id, prt.user_id, u.user_email
                        FROM PasswordResetToken prt
                        JOIN `User` u ON prt.user_id = u.user_id
                        WHERE prt.token = :token
                          AND prt.reset_success = 0
                          AND prt.expires_at > NOW()
                        LIMIT 1';
    $statement = $db->prepare($queryCheckToken);
    $statement->bindValue(':token', $token);
}

$statement->execute();
$reset_data = $statement->fetch(PDO::FETCH_ASSOC);

if (!$reset_data || empty($reset_data['user_id'])) {
    header('Location: reset_password.html?error=invalid_token');
    exit();
}

if (strlen($password) < 8) {
    header('Location: reset_password.html?token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=short_password');
    exit();
}

if ($password !== $confirm_password) {
    header('Location: reset_password.html?token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=no_match');
    exit();
}

$token = isset($_POST['token']) ? trim($_POST['token']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if ($token === '') {
    header('Location: reset_password.html?error=invalid_token');
    exit();
}

if ($email !== '') {
    $queryCheckToken = 'SELECT prt.reset_id, prt.user_id, u.user_email
                        FROM PasswordResetToken prt
                        JOIN `User` u ON prt.user_id = u.user_id
                        WHERE prt.token = :token
                          AND u.user_email = :email
                          AND prt.reset_success = 0
                          AND prt.expires_at > NOW()
                        LIMIT 1';
    $statement = $db->prepare($queryCheckToken);
    $statement->bindValue(':token', $token);
    $statement->bindValue(':email', $email);
} else {
    $queryCheckToken = 'SELECT prt.reset_id, prt.user_id, u.user_email
                        FROM PasswordResetToken prt
                        JOIN `User` u ON prt.user_id = u.user_id
                        WHERE prt.token = :token
                          AND prt.reset_success = 0
                          AND prt.expires_at > NOW()
                        LIMIT 1';
    $statement = $db->prepare($queryCheckToken);
    $statement->bindValue(':token', $token);
}

$statement->execute();
$reset_data = $statement->fetch(PDO::FETCH_ASSOC);

if (!$reset_data || empty($reset_data['user_id'])) {
    die('<pre>DEBUG reset_data: ' . print_r($reset_data, true) . '</pre>');
}

$password_hashed = password_hash($password, PASSWORD_DEFAULT);

$queryUpdatePassword = 'UPDATE `User`
                        SET password_hashed = :password_hashed
                        WHERE user_id = :user_id';
$statement = $db->prepare($queryUpdatePassword);
$statement->bindValue(':password_hashed', $password_hashed);
$statement->bindValue(':user_id', (int)$reset_data['user_id'], PDO::PARAM_INT);
$statement->execute();

$queryMarkTokenUsed = 'UPDATE PasswordResetToken
                       SET reset_success = 1, used_at = NOW()
                       WHERE reset_id = :reset_id';
$statement = $db->prepare($queryMarkTokenUsed);
$statement->bindValue(':reset_id', (int)$reset_data['reset_id'], PDO::PARAM_INT);
$statement->execute();

header('Location: reset_password.html?success=1');
exit();
?>
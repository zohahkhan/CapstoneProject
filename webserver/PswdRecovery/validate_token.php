<?php
require_once __DIR__ . '/../include/db_connect.php';

date_default_timezone_set('America/New_York');
$db->exec("SET time_zone = '-04:00'");

$token = $_GET['token'] ?? '';

$stmt = $db->prepare("
    SELECT 1
    FROM PasswordResetToken
    WHERE token = :token
      AND reset_success = 0
      AND expires_at > NOW()
");
$stmt->execute([':token' => $token]);

if (!$stmt->fetch()) {
    header("Location: reset_password.html?error=invalid_token");
    exit();
}
?>
<?php
$isHttps =
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

$protocol = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

define('BASE_URL', $protocol . '://' . $host . '/');
?>

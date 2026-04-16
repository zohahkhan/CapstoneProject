<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

$basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
$basePath = '/' . trim($basePath, '/\\');

define('BASE_URL', $protocol . '://' . $host . $basePath);


?>
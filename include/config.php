<?php
/* Appears in:
   newEvent, president_requests, record_attendance, headdepartnementSummary, 
   memberSummary, manage_roles, viewUser, newUser, and userInfo
*/

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

$basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
$basePath = '/' . trim($basePath, '/\\');

define('BASE_URL', $protocol . '://' . $host . $basePath);


?>

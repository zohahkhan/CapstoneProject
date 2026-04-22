<?php
/* Appears in:
   newEvent, president_requests, record_attendance, headdepartnementSummary, 
   memberSummary, manage_roles, viewUser, newUser, and userInfo
*/

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Get the directory of the current script
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// If it's root, avoid double slashes
$basePath = $basePath === '/' ? '' : $basePath;

define('BASE_URL', $protocol . '://' . $host . $basePath . '/');


?>

<?php
/* Appears in:
   newEvent, president_requests, record_attendance, headdepartnementSummary, 
   memberSummary, manage_roles, viewUser, newUser, and userInfo

   also used in surveyHub
*/

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Get first directory (project root)
$pathParts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectFolder = $pathParts[0] ?? '';

$basePath = $projectFolder ? '/' . $projectFolder . '/' : '/';

define('BASE_URL', $protocol . '://' . $host . $basePath);


?>

<?php
$dsn = 'mysql:host=localhost;dbname=lanja_db';
$username = 'mgs_user';
$password = 'pa55word';

try 
{
    $db = new PDO($dsn, $username, $password);
} 
catch (PDOException $e) 
{
    $error_message = $e->getMessage();
    die('Connection error: ' . $error_message);
}
?>
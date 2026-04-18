<?php
	$dsn = 'mysql:host=localhost;dbname=lanja_db';
    //$username = 'mgs_user';
    //$password = 'pa55word';
   	$username = 'root';
    $password = '';
   	$error_message = 'Your connection failed.';

    try 
	{
        $db = new PDO($dsn, $username, $password);
    } 
	catch (PDOException $e) 
	{
        $error_message = $e->getMessage();
        echo 'Connection error: ' . $error_message;
    }	
?>

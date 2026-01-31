<?php
	$dsn = 'mysql:host=localhost;dbname=lanja_db';
    $username = 'mgs_user';
    $password = 'pa55word';
   	
    try 
	{
        $db = new PDO($dsn, $username, $password);
		echo '<p> Your connection is secure.</p>';
    } 
	catch (PDOException $e) 
	{
        $error_message = $e->getMessage();
        echo  'Connection error.:$error_message';
    }	
?>
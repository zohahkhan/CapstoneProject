<?php
session_start();

if (!isset($_SESSION['submitted'])) {
    header("Location: contact.php");
    exit;
}

unset($_SESSION['submitted']);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Submission Successful</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body style="text-align:center; margin-top:100px;">
    	<img src="images/topLeft.png" alt="" class="corner-img top-left">
	<img src="images/bottomRight.png" alt="" class="corner-img bottom-right">

  <h1>Submission Successful 🎉</h1>
  <p>Thank you for reaching out! Your request has been received.</p>

  <br>
  <a href="index.php">Back to Home</a>

</body>
</html>

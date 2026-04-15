<?php
session_start();
if (!isset($_SESSION['submitted'])) {
    header("Location: loginpages/login.php");
    exit;
}
unset($_SESSION['submitted']);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Submission Successful</title>
  <link rel="stylesheet" type="text/css" href="loginpages/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="box" style="max-width: 600px;">
        <h1>Submission Successful 🎉</h1>
        <p>Thank you for reaching out! Your request has been received.</p>
        <br>
        <a href="loginpages/index.php" style="color: #8b6f47; text-decoration: none; font-weight: 600;">Back to Home</a>
    </div>
</body>
</html>

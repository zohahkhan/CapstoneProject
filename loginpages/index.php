<?php
require_once './include/db_connect.php';
$status = session_status();
if ($status == PHP_SESSION_NONE) 
{
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Homepage</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
	<style>
		.admin-link {
			font-size: 1.1em;
			color: #8b6f47;
			text-decoration: none;
			font-weight: 500;
		}
		.admin-link:hover {
			color: #6b5437;
			text-decoration: underline;
		}
	</style>
</head>
<body>
    <?php
        if (isset($_SESSION['user'])) 
		{
			echo "<h1>Hello, ";
			echo $_SESSION['user']['first_name']." ";
			echo $_SESSION['user']['last_name']."!</h1>";
			
			$queryCheckAdmin = 'SELECT COUNT(*) FROM UserRole ur
								JOIN Role r ON ur.role_id = r.role_id
								WHERE ur.user_id = :user_id AND r.role_name = "Admin"';
			$stmtCheck = $db->prepare($queryCheckAdmin);
			$stmtCheck->bindParam(':user_id', $_SESSION['user']['user_id']);
			$stmtCheck->execute();
			$isAdmin = $stmtCheck->fetchColumn() > 0;
			
			if ($isAdmin) {
				echo '<p><a href="../manage_roles.php" class="admin-link">⚙ Manage User Roles & Permissions</a></p>';
			}
	?>		
	<br><br>
	<div class="boxes">
		<div class="box left-box">
			<h2>Monthly Report</h2>
			<p>Description</p>
		</div>
		<div class="right-box">
			<div class="right-sub-box">
				<h2>Important Reminders</h2>
				<p>Description</p>
			</div>
			<div class="right-sub-box">
				<h2>Calendar</h2>
				<p>Description</p>
			</div>
			<div class="right-sub-box">
				<h2>Meeting Attendance</h2>
				<p>Description</p>
			</div>
			<div class="right-sub-box">
				<h2>Suggestions</h2>
				<p>Description</p>
			</div>
		</div>
	</div>
	</br></br>
    <p><a href="logout.php">Logout</a></p>
    <?php 
		} else {
			echo "<h1>Welcome to Lajna Pittsburgh</h1>";
	?>
	<p><a href="login.php">Login</a></p> 
    <?php } ?>
</body>
</html>
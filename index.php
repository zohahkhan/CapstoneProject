<!--homepage.php the homepage / user landing page-->
<?php
// connects to database script
require_once './include/db_connect.php';

// check for an existing session
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
</head>

<body>
    <!--display user session information-->
    <?php
        if (isset($_SESSION['user'])) 
		{
			echo "<h1>Hello, ";
			echo $_SESSION['user']['first_name']." ";
			echo $_SESSION['user']['last_name']."!</h1>";
	?>		
	<br><br>
	<div class="boxes">
		<!--the left side big box-->
		<div class="box left-box">
			<h2>Monthly Report</h2>
			<p>Description</p>
		</div>

		<!--the right box with four separate boxes inside-->
		<div class="right-box">
<div class="right-sub-box">
  <h2>Important Reminders</h2>

  <?php if ($isPresident): ?>
    <?php
      $stmt = $db->prepare("SELECT COUNT(*) FROM `suggestion` WHERE msg_status = :status");
      $stmt->execute([':status' => 'Pending']);
      $pendingCount = (int)$stmt->fetchColumn();
    ?>
    <p>
      You have <b><?= $pendingCount ?></b> pending request(s).<br>
      <a href="president_requests.php">View Visitor Requests</a>
    </p>
  <?php else: ?>
    <p>Description</p>
  <?php endif; ?>
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
	<!--if the user is logged in, display a logout link-->
    <p><a href="logout.php">Logout</a></p>
		
	<!--this section is the default home screen when logged out-->
    <?php 
		} else {
			echo "<h1>Welcome to Lajna Pittsburgh</h1>";
	?>
	<!--if the user is not logged in, display a login link-->
	<p><a href="login.php">Login</a>
	<a href="contact.php">Join Us</a>
	</p> 
    <?php } ?>
</body>
</html>
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

if (!isset($user_id)) 
{
	$user_id = $_SESSION['user']['user_id'];	
}

if (isset($_POST['role'])) 
{
    $_SESSION['user']['role_id'] = $_POST['role'];  
}

$queryAllUserRoles = 'SELECT Role.role_id, Role.role_name
						FROM Role					
						JOIN UserRole ON Role.role_id = UserRole.role_id
						WHERE UserRole.user_id = :user_id';
	$statement = $db->prepare($queryAllUserRoles);
	$statement->bindParam(':user_id', $user_id);
	$statement->execute();
	$role = $statement->fetchAll();

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
			echo "\n<h2>";
	?>
	<label>Current Role: </label>
	<form method="POST">
		<select name="role" onchange="this.form.submit()">
			<?php foreach ($role as $r): ?>
				<option value="<?= $r['role_id']; ?>"
					<?= (isset($_SESSION['user']['role_id']) && 
						$_SESSION['user']['role_id'] == $r['role_id']) 
						? 'selected' : ''; ?>>
					<?php echo $r['role_name']; ?>
				</option>
			<?php endforeach; ?>
		</select> 
	</form>
	<?php echo "</h2>";	?>		
	
	
	<!---- PRES HOMEPAGE ---->
	<?php if ($_SESSION['user']['role_name'] == "President" || $_SESSION['user']['role_id'] == 1) { ?>
	<div class="boxes">
		<!-- left box split horizontally into 2 -->
		<div class="left-box">
			<div class="left-sub-box top-box">
				<h2>Compiled Monthly Report</h2>
				<p>Description</p>
			</div>
			<div class="left-sub-box bottom-box">
				<h2>Monthly Report</h2>
				<p>Description</p>
			</div>
		</div>

		<!--the right box with four separate boxes inside-->
		<div class="right-box">
			<div class="right-sub-box">
				<h2>Create a new Reminder</h2>
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
				<h2>Review Suggestions</h2>
				<p>Description</p>
			</div>
		</div>
	</div>
	</br></br>
	<!--if the user is logged in, display a logout link-->
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF PRES HOMEPAGE --->


	<!---- DEPT HOMEPAGE ----->
	<?php } else if ($_SESSION['user']['role_name'] == "Department Head" || $_SESSION['user']['role_id'] == 2) { ?>
	<br><br>
	<div class="boxes">
		<!-- left box, split horizontally into 2 -->
		<div class="left-box">
			<div class="left-sub-box top-box">
				<h2>Department Report</h2>
				<p>Description</p>
			</div>
			<div class="left-sub-box bottom-box">
				<h2>Monthly Report</h2>
				<p>Description</p>
			</div>
		</div>

		<!--the right box with four separate boxes inside-->
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
	<!--if the user is logged in, display a logout link-->
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF DEPT HOMEPAGE --->
	
	
	
	<!--- MEMBER HOMEPAGE --->
	<?php } else if ($_SESSION['user']['role_name'] == "Member" || $_SESSION['user']['role_id'] == 3) { ?>
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
	<!--if the user is logged in, display a logout link-->
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF MEMBER HOMEPAGE --->
		
	
	<!--- ADMIN HOMEPAGE ---->
	<?php } else if ($_SESSION['user']['role_name'] == "Admin" || $_SESSION['user']['role_id'] == 4) { ?>	
	<br><br>
	 <div class="homepage-boxes">
        <!-- the top row with two boxes -->
        <div class="homepage-top">
            <div class="homepage-top-box">
                <h2>View Logs</h2>
                <p>Description</p>
            </div>
            <div class="homepage-top-box">
                <h2>View Compiled Monthly Report</h2>
                <p>Description</p>
            </div>
        </div>
        <!--bottom box -->
        <div class="homepage-bottom-box">
            <h2>Members</h2>
            <p>Description</p>
        </div>
    </div>
	<br><br>
	<!--if the user is logged in, display a logout link-->
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF ADMIN HOMEPAGE --->
		
	
	<!--this section is the default home screen when logged out-->
    <?php 
	}} else {
			echo "<h1>Welcome to Lajna Pittsburgh</h1>";
	?>
	<!--if the user is not logged in, display a login link-->
	<p><a href="login.php">Login</a></p> 
    <?php } ?>
</body>
</html>

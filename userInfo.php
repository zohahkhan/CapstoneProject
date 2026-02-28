<!--userinfo.php only the president can view all member info -->
<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once('include/db_connect.php');

if (!isset($user_id)) 
{
	$user_id = $_GET['user_id'];
}
// Get user Information
$queryProfile = 'SELECT * FROM `User`
				 WHERE user_id = :user_id';
$statement = $db->prepare($queryProfile);
$statement->bindValue(':user_id', $user_id);
$statement->execute();
$users = $statement->fetchAll();
$statement->closeCursor();


$queryRefresh = "SELECT * 
				FROM `User` 
				WHERE user_id = :user_id";
$stmt2 = $db->prepare($queryRefresh);
$stmt2->bindValue(':user_id', $user_id);
$stmt2->execute();
$user = $stmt2->fetch();


// if deactivate button clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
	
	$queryDeactivate = 'UPDATE `User` 
						SET is_active = NOT is_active 
						WHERE user_id = :user_id';
    $stmt = $db->prepare($queryDeactivate);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->execute();
	
	header("Location: userInfo.php?user_id=" . $user_id);
    exit;
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Final Project	</title>
	<link rel = "stylesheet" href = "style.css">
</head>
<body>
	<header>
		<h1>Member Info </h1>
	</header>
	<br><br>
<main>
<!--- view details about the selected user  --->
	<table>
		<form action = "" method = "post">
			
		<?php foreach($users as $user): ?>

		<tr>
			<label><th>First Name</th></label>
			<td><?php  echo $user['first_name']; ?></td>
		</tr>
		<tr>
			<label><th>Last Name</th></label>
			<td><?php  echo $user['last_name']; ?></td>
		</tr>
		<tr>
			<label><th>User Email</th></label>
			<td><?php  echo $user['user_email']; ?></td>
		</tr>
		<tr>
			<label><th>Phone Number</th></label>
			<td><?php  echo $user['user_phone']; ?></td>
		</tr>
		<tr>
			<label><th>Street Address</th></label>
			<td><?php  echo $user['user_address']; ?></td>
		</tr>
		<tr>
			<label><th>Joined On</th></label>
			<td><?php  echo $user['joined_on']; ?></td>
		</tr>
		<tr>
			<label><th>Last Login</th></label>
			<td><?php  echo $user['last_login']; ?></td>
		</tr>
		<tr>
			<label><th>Last Updated</th></label>
			<td><?php  echo $user['last_updated']; ?></td>
		</tr>	
		<tr>
			<label><th>Active User</th></label>
			<td><?php  echo $user['is_active'] ? 'true' : 'false'; ?></td>
			<td>
				<form method="post" style="margin:0;">
					<input type="hidden" name="user_id" value="<?= $user_id ?>">

					<button type="submit" >
						<?= $user['is_active'] ? 'Deactivate this user' : 'Activate as Member' ?>
					</button>
				</form>			
			</td>
		</tr>		
		</form>
		<?php  endforeach; ?>
	</table>		
<br><br>	
		<p><a href="viewUser.php">Back to all Members</a></p>
		<p><a href="index.php">Back to dashboard</a></p>
	</main>
</body>
</html>
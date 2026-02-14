<!--viewUser.php only the president can view all member info -->
<?php
require_once('include/db_connect.php');

// Get all users
$queryAllUsers = 'SELECT * FROM `User`
				   ORDER BY user_id';
$statement = $db->prepare($queryAllUsers);
$statement->execute();
$users= $statement->fetchAll();
$statement->closeCursor();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Final project</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
	<header>
		<h1>User Profiles</h1>
	</header>
<main>
        <!-- display a table of users -->
    <h2>All Users</h2>
	<p><a href="newUser.php">Add new member</a></p>
	<br>
	<table>
		<tr>
			<th>ID | </th>
			<th>First & </th>
			<th>Last Name</th>
			<th>Email</th>	
			<th>Joined On</th>
			<th>Activated</th>
		</tr>
		<?php foreach($users as $user): ?>
		<tr>
			<td><?php echo $user['user_id']; ?></td>
			<td><?php echo $user['first_name']; ?></td>
			<td><?php echo $user['last_name']; ?></td>
			<td><?php echo $user['user_email']; ?></td>
			<td><?php echo $user['joined_on']; ?></td>
			<td style="text-align: center;"><?php echo $user['is_active']; ?></td>
			<td>
				<form action = "userInfo.php" method = "get">
					<input type="hidden" name = "user_id" 
					value= "<?php echo $user['user_id']; ?>">
					<br>
					<input type="submit" name="action" value="More">
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p><a href="index.php">Back to dashboard</a></p>
</main>
	
</body>
</html>
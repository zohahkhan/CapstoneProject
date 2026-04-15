<!--viewUser.php only the president can view all member info -->
<?php
require_once __DIR__ . '/../include/db_connect.php';

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
    <link rel="stylesheet" type="text/css" href="../style.css" />
	<style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
            background-image: url('../images/background.png');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
        }
        h1 {
            text-align: center;
            width: 100%;
        }
        .content-box {
            width: 80%;
            max-width: 1400px;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            padding: 40px;
            text-align: center;
        }
        .event-select-form {
            margin-bottom: 30px;
        }
        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #c4a484;
            color: white;
        }
        .action-btn {
            padding: 8px 16px;
            margin: 5px;
            border-radius: 8px;
            border: none;
            background-color: #c4a484;
            color: white;
            cursor: pointer;
            font-size: 0.9em;
        }
        .action-btn:hover {
            background-color: #b39578;
        }
     
    </style>
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
	<p><a href="../index.php">Back to dashboard</a></p>
</main>
	
</body>
</html>

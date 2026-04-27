<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once __DIR__ . '/../include/db_connect.php';

// only president can view all member info
if (!in_array($_SESSION['user']['role_id'], [1]))
{
	require_once __DIR__ . '/../include/config.php';
	$error_page = BASE_URL.'/include/error.php';
    header("Location: $error_page");
    exit;
}

if (!isset($user_id)) 
{
	$user_id = $_GET['user_id'];
}

$current_user = $_SESSION['user']['user_id'];

// for database script to 'see' session variable
$db->exec("SET @current_role_id = " . (int)$_SESSION['user']['role_id']);

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
						SET is_active = NOT is_active,
							updated_by = :updated_by
						WHERE user_id = :user_id';
    $stmt = $db->prepare($queryDeactivate);
    $stmt->bindValue(':user_id', $user_id);
	$stmt->bindValue(':updated_by', $current_user);
    $stmt->execute();
	
	header("Location: userInfo.php?user_id=" . $user_id);
    exit;
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Final Project	</title>
	<link rel = "stylesheet" href = "../style.css">
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
		.back-link
        {
            display: inline-block;
            margin-bottom: 20px;
            color: #c4a484;
            text-decoration: none;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
			text-shadow: 0 1px 1px rgba(0,0,0,0.40);
        }
        .back-link:hover
        {
            color: #b39578;
        }
		.links 
		{
    		display: flex;
    		justify-content: space-between;
		}
     
    </style>
</head>
<body>
	<header>
		<h1>Member Info </h1>
	</header>
	<br><br>
<main>
	<div class="links">
    	<a href="viewUser.php" class="back-link">&larr; Back to all Members</a>
		<a href="../index.php" class="back-link">Jump to dashboard</a>		
	</div>
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
				<form method="post" style="margin:0;">
					<input type="hidden" name="user_id" value="<?= $user_id ?>">

					<button type="submit" >
						<?= $user['is_active'] ? 'Deactivate this user' : 'Activate as Member' ?>
					</button>
				</form>			
		</tr>		
		</form>
		<?php  endforeach; ?>
	</table>		
<br><br>	
		<p><a href="viewUser.php">Back to all Members</a></p>
		<p><a href="../index.php">Back to dashboard</a></p>
	</main>
</body>
</html>

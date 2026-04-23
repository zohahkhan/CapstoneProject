<!--viewUser.php  view all member info -->
<?php
require_once __DIR__ . '/../include/db_connect.php';
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
// only president or admin can view all members
if (!in_array($_SESSION['user']['role_id'], [1, 4]))
{
	require_once __DIR__ . '/../include/config.php';
	$error_page = BASE_URL.'/include/error.php';
    header("Location: $error_page");
    exit;
}

$search = isset($_POST['search']) ? $_POST['search'] : '';
if (!preg_match("/^[a-zA-Z\s]*$/", $search)) 
{
	echo '<p style="color:red;">No match</p>';
    $search = '';
}
if (!empty($search)) {
	
	$searchByName = "%" . $search . "%";
    $queryName = "SELECT * FROM `User` 
				  WHERE CONCAT(first_name, ' ', last_name) 
				  LIKE :search";
    $stmt1 = $db->prepare($queryName);
    $stmt1->bindValue(':search', $searchByName);
    $stmt1->execute();
    $users = $stmt1->fetchAll();
	
	if (count($users) == 0) {
    echo '<p style="color:red;">Name not found</p>';
		
	$queryAllUsers = 'SELECT * FROM `User`
					  ORDER BY user_id';
	$stmt2 = $db->prepare($queryAllUsers);
	$stmt2->execute();
	$users = $stmt2->fetchAll();
	$stmt2->closeCursor();
    }
	
} else {
	
	$queryAllUsers = 'SELECT * FROM `User`
					  ORDER BY user_id';
	$stmt2 = $db->prepare($queryAllUsers);
	$stmt2->execute();
	$users = $stmt2->fetchAll();
	$stmt2->closeCursor();
}

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
     	.search{
			font-size: 0.9em; 
			height: 50px;
			padding: 10px;
			margin-top: 16px; 
			margin-right: 10px;
			box-sizing: border-box;
		}
    </style>
</head>

<body>
	<header>
		<h1>User Profiles</h1>
	</header>
<main>
    <h2>Review All Users</h2>
	<div class="links">
		<?php if ($_SESSION['user']['role_id'] == 1) { ?>
		<a href="newUser.php" class="back-link"> Add New member</a>
		<?php } ?>
	</div>
	
	<div class="links" style="justify-content: center; margin-top: 15px; ">
		<form method="POST" action="" class="form-group" style="display: flex; ">
			<?php
				$search = isset($_POST['search']) ? $_POST['search'] : '';
			?>
			<input type="text" name="search" class="search" placeholder="Search by name">
			<button type="submit">Search</button>
		</form>
	</div>
	 <!-- display a table of users -->
	<table>
		<tr>
			<th>ID  </th>
			<th>First & </th>
			<th>Last Name</th>
			<th>Email</th>	
			<th>Joined On</th>
			<th>Activated</th>
			<th>More Info</th>
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
					<input type="submit" name="action" value="View">
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</main>
</body>
</html>

<!--login.php the login entry page-->
<?php
// connects to database script
require_once './include/db_connect.php';

// check if the user is already logged in
$status = session_status();
if ($status == PHP_SESSION_NONE) 
{
    session_start();
}

if (isset($_SESSION['user'])) 
{
	// redirect to homepage if the login is successful
	header('Location: index.php'); 
	exit();
}
	// check if the login form was submitted
	$login = filter_input(INPUT_POST, 'submit');

if (isset($login)) 
{
	// retrieve login credentials from input
	$email = filter_input(INPUT_POST, 'email');
	$password = filter_input(INPUT_POST,'password');

	// check if the provided email is found in the database
	// retrieve needed variables for session
	$queryVerifyUser = 'SELECT user_id, first_name, last_name, user_email, password_hashed
						FROM `User`
						WHERE user_email = :email';
	$statement = $db->prepare($queryVerifyUser);
	$statement->bindParam(':email', $email);
	$statement->execute();
	$user = $statement->fetch();
	
	if (!is_null($user)) 
	{
		// verify user password against stored hashed value
		if (password_verify($password, $user['password_hashed'])) 
		{
			$_SESSION['user'] = array(
				'user_id' => $user['user_id'],
				'user_email' => $user['user_email'],
				'first_name' => $user['first_name'],
				'last_name' => $user['last_name']
			);
			header('Location: index.php'); // successful login
			exit();
		} else 
		{
			$error_message = 'Login failed. Please try again 1.';	
		}
	} else 
	{
		$error_message = 'Login failed. Please try again 2.'; 
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Login</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<!--images-->
	<img src="images/topLeft.png" alt="" class="corner-img top-left">
	<img src="images/bottomRight.png" alt="" class="corner-img bottom-right">
	
	<div class="box">
	<h1>Lajna Pittsburgh</h1>
	<h3>Please login:</h3>
		<?php 
			if (isset($error_message)) 
			{ 
				echo $error_message;
			}
		?>
		<!--username and password -->
		<form class="login-form" action="" method="POST" >
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" id="username" name="email"placeholder="Enter your username" required/>
		</div>

		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" id="password" name="password" placeholder="Enter your password" required/>
		</div>
		<p><a href="reset.php"></a>Password Reset</p>
		<button type="submit" name="submit">Submit</button>
		</form>
	</div>
</body>

</html>

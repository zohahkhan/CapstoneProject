<!--login.php the login entry page-->
<?php
// connects to database script
require_once './include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

if (isset($_SESSION['user'])) 
{
	// redirect to homepage if the login is successful
	header('Location: index.php'); 
	exit();
}

// Create a secure-ish session cookie that still works on localhost (HTTP)
// CHANGE: on localhost (HTTP), cookie("secure"=>true) will NOT be set by the browser.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

$error_message = null;

	// check if the login form was submitted
	$login = filter_input(INPUT_POST, 'submit');

if (isset($login)) 
{
	// retrieve login credentials from input
	$email = filter_input(INPUT_POST, 'email');
	$password = filter_input(INPUT_POST,'password');

	// check if the provided email is found in the database
	// retrieve needed variables for session
	
	// ***********added join for session switch
	$queryVerifyUser = 'SELECT User.user_id, User.first_name, User.last_name, User.user_email, User.password_hashed, Role.role_id, Role.role_name
						FROM `User`						
						JOIN UserRole ON User.user_id = UserRole.user_id
						JOIN Role ON UserRole.role_id = Role.role_id
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
				'last_name' => $user['last_name'],
				'role_id' => $user['role_id'],
				'role_name' => $user['role_name']
			);
		                    // CHANGE: DB-backed session token stored in Session table + HttpOnly cookie
                    // Added a table to db.sql: Session(session_id, user_id, expires_at [, created_at, last_seen_at])
                    $token = bin2hex(random_bytes(32)); // 64 hex chars
                    $expiresAt = (new DateTime('+7 days'))->format('Y-m-d H:i:s');

                    try {
                        $ins = $db->prepare('INSERT INTO `Session` (session_id, user_id, expires_at)
                                             VALUES (:session_id, :user_id, :expires_at)');
                        $ins->execute([
                            ':session_id' => $token,
                            ':user_id' => $user['user_id'],
                            ':expires_at' => $expiresAt
                        ]);
                    } catch (PDOException $e) {
                        // CHANGE: fail open for class project demo if Session table isn't deployed yet
                    }

                    // CHANGE: Update last_login 
                    try {
                        $upd = $db->prepare('UPDATE `User` SET last_login = NOW() WHERE user_id = :user_id');
                        $upd->execute([':user_id' => $user['user_id']]);
                    } catch (PDOException $e) {
                        // Same note as above: ignore if column doesn't exist in a dev schema.
                    }

                    // CHANGE: Set cookie for DB-backed session token
                    // - Secure is enabled only when HTTPS is detected
                    setcookie('session', $token, [
                        'expires' => time() + 7 * 24 * 60 * 60,
                        'path' => '/',
                        'secure' => $isHttps,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);	
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
		<p><a href="forgot_password.html" style="color: #c4a484; text-decoration: none;">Forgot Password?</a></p>
		<button type="submit" name="submit">Submit</button>
		</form>
	</div>
</body>
</html>

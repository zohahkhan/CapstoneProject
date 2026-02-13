<!--newUser.php new member registration page / for president's use only -->
<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once 'include/db_connect.php';

// initialize variables
$first_name = $last_name = $email = $phone = $address = $temp_password = '';

$errors = array();
	
$register = filter_input(INPUT_POST, 'register');

if (isset($register)) 
{
    // validate and sanitize input
    $first_name = filter_input(INPUT_POST,'first_name');
    $last_name = filter_input(INPUT_POST,'last_name');
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone');
    $address = filter_input(INPUT_POST, 'address');
    $temp_password = filter_input(INPUT_POST, 'temp_password');
	
	// set default values
	$is_active = true;
	$joined_on = date("Y-m-d H:i:s", time());
	$last_login = date("Y-m-d H:i:s", time());

	// make sure email hasn't been used already
	$queryCheckEmail = 'SELECT COUNT(*) FROM `User` 
	                    WHERE user_email =:email';
	$stmt = $db->prepare( $queryCheckEmail);
	$stmt->bindValue(':email', $email);
    $stmt->execute();
    $countEmail = $stmt->fetchColumn();
	
    // error handling: check for empty fields
    if (empty($email)) 
	{
        $errors['email'] = 'Email is required';
    }
    if (empty($temp_password)) 
	{
        $errors['temp_password'] = 'Password is required';
    }
    if (empty($first_name)) 
	{
        $errors['first_name'] = 'First name is required';
    }
	if (empty($last_name)) 
	{
        $errors['last_name'] = 'Last name is required';
    }
	if (empty($phone)) 
	{
        $errors['phone'] = 'Phone number is required';
    }
	if (empty($address)) 
	{
        $errors['address'] = 'Street address is required';
    }
	if ($countEmail>0) 
	{
		$errors['email'] = "An account with this email address already exists";
	}

    // If there are no errors, proceed with registration
    else if (empty($errors)) 
	{
        // Hash the password before storing it
        $hashedPassword = password_hash($temp_password, PASSWORD_DEFAULT);

        // query the new member into the database
        $queryInsertUser = 'INSERT INTO `User` 
		
		(first_name, last_name, user_email, user_phone, user_address, password_hashed, is_active, joined_on, last_login) 
				  
		VALUES (:first_name, :last_name, :user_email, :user_phone, :user_address, :password_hashed, :is_active, :joined_on, :last_login)';
        $statement = $db->prepare($queryInsertUser);
		$statement->bindParam(':first_name', $first_name);
		$statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':user_email', $email);
        $statement->bindParam(':user_phone', $phone);
        $statement->bindParam(':user_address', $address);
		$statement->bindParam(':password_hashed', $hashedPassword);
        $statement->bindParam(':is_active', $is_active);
        $statement->bindParam(':joined_on', $joined_on);
        $statement->bindParam(':last_login', $last_login);


        if ($statement->execute()) 
		{
			$userId = $db->lastInsertId();
			
			$stmtGetRole = $db->prepare("SELECT role_id FROM Role WHERE role_name = 'Member'");
			$stmtGetRole->execute();
			$roleId = $stmtGetRole->fetchColumn();
			 
			$stmtRoleAssign = $db->prepare("INSERT INTO UserRole (user_id, role_id) VALUES (:user_id, :role_id)");
			
			$stmtRoleAssign->execute([
					':user_id' => $userId,
					':role_id' => $roleId
				]);
			
            // if successful, redirect to login page		
			header('Location: login.php?success=1');
            exit();
        } else {
            // if failed, display error message
            echo 'Registration failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Final Project</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
<body>
	<header>
		<h1>Member Registration</h1>
	</header>

    <main>
		<p><?php echo $msg; ?></p>
        <?php if (isset($errors['registration'])) : ?>
            <p class="error"><?php echo $errors['registration']; ?></p>
        <?php endif; ?>
        <form method="POST" action="newUser.php">
		<table>
			<tr>
				<td><label for="email">Email:</label></td>
				<td><input type="email" name="email" id="email" value="<?php echo $email; ?>"></td>
					<?php if (isset($errors['email'])) : ?>
					<p class="error"><?php echo $errors['email']; ?></p>
					<?php endif; ?>
			</tr>
			<tr>
				<td><label for="password">Password:</label></td>
				<td><input type="password" name="temp_password" id="temp_password"></td>
					<?php if (isset($errors['temp_password'])) : ?>
					<p class="error"><?php echo $errors['temp_password']; ?></p>
					<?php endif; ?>
			</tr>
            <tr>
				<td><label for="first_name">first_name:</label></td>
				<td><input type="text" name="first_name" id="first_name" value="<?php echo $first_name; ?>"></td>
					<?php if (isset($errors['first_name'])) : ?>
					<p class="error"><?php echo $errors['first_name']; ?></p>
					<?php endif; ?>	
			</tr>
            <tr>
				<td><label for="last_name">last_name:</label></td>
				<td><input type="text" name="last_name" id="last_name" value="<?php echo $last_name; ?>"></td>
					<?php if (isset($errors['last_name'])) : ?>
					<p class="error"><?php echo $errors['last_name']; ?></p>
					<?php endif; ?>
            </tr>
			<tr>
				<td><label for="phone">phone:</label></td>
				<td><input type="text" name="phone" id="phone" value="<?php echo $phone; ?>"></td>
				<?php if (isset($errors['phone'])) : ?>
					<p class="error"><?php echo $errors['phone']; ?></p>
					<?php endif; ?>
			</tr>
            <tr>
				<td><label for="address">address:</label></td>
				<td><input type="text" name="address" id="address" value="<?php echo $address; ?>"></td>
				<?php if (isset($errors['address'])) : ?>
					<p class="error"><?php echo $errors['address']; ?></p>
					<?php endif; ?>
            </tr>
		</table>
        <br>
		<div style="text-align: center;">
            <button type="submit" name="register">Register</button>
			<p><a href="index.php">Back to home</a></p>
        </div>
		</form>
    </main>
</body>
</html>

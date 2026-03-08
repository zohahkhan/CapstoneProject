<?php
//connecting to the database
require_once './include/db_connect.php';

//get the user_id
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];

if($user_id == null || $user_id === false){
	$error = "Error";
	echo $error;
	exit();}

//query for user
$queryUser = 'SELECT * FROM User
              WHERE user_id = :user_id';
                   
$statement1 = $db->prepare($queryUser);
$statement1->bindValue(':user_id', $user_id);
$statement1->execute();
$User = $statement1->fetch();
$statement1->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Profile</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<!--images-->
	<img src="images/topLeft.png" alt="" class="corner-img top-left">
	<img src="images/bottomRight.png" alt="" class="corner-img bottom-right">

	<div class="box">
		<form action="updateProfile.php" method="post" id="update_profile_form" class="form-group">

            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo $User["first_name"];?>" ><br>

            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo $User["last_name"];?>"><br>

            <label>Email</label>
            <input type="text" name="user_email" value="<?php echo $User["user_email"];?>"><br>

			<label>Phone Number:</label>
            <input type="text" name="user_phone" value="<?php echo $User["user_phone"];?>"><br>
			
			<label>Street Address:</label>
            <input type="text" name="user_address" value="<?php echo $User["user_address"];?>"><br>
			
			<input type="hidden" name="user_id" value="<?php echo $User["user_id"];?>" ><br>
			
            <label>&nbsp;</label>
            <input type="submit" value="Update Profile"><br>
           </form>
		   
		   <!-- link to return to homepage page -->
		<a href="index.php">Back to Homepage</a>
	</div>
</body>
</html>

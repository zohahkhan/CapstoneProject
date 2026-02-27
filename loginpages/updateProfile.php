<?php
//connects to the database
require_once './include/db_connect.php';
session_start();
if (!isset($_SESSION['user'])) {
    echo "Session expired. Please log in again.";
    exit();
}

//fetch inputs
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$first_name  = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
$last_name   = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
$user_email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_STRING);
$user_phone  = filter_input(INPUT_POST, 'user_phone', FILTER_VALIDATE_INT);
$user_address = filter_input(INPUT_POST, 'user_address', FILTER_SANITIZE_STRING);
$editor_id = $_SESSION['user']['user_id'];

//validate inputs
$errors = [];
if (empty($first_name))  $errors[] = "First name required.";
if (empty($last_name))   $errors[] = "Last name required.";
if (empty($user_phone))   $errors[] = "Phone number required.";
if (empty($user_address))   $errors[] = "Street address required.";
if (!filter_var($user_email, FILTER_VALIDATE_EMAIL))
    $errors[] = "Invalid email.";

if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<p style='color:red'>$e</p>";
    }
	include("updateProfileForm.php");
    exit();
}

//update query
$query = 'UPDATE User
		 SET first_name = :first_name, 
		 last_name = :last_name, 
		 user_email =:user_email, 
		 user_phone =:user_phone, 
		 user_address =:user_address,
		 last_updated = NOW(),
         updated_by = :updated_by
		 WHERE user_id =:user_id';
$statement = $db->prepare($query);
$statement->bindValue(':user_id', $user_id);
$statement->bindValue(':first_name', $first_name);
$statement->bindValue(':last_name', $last_name);
$statement->bindValue(':user_email', $user_email);
$statement->bindValue(':user_phone', $user_phone);
$statement->bindValue(':user_address', $user_address);
$statement->bindValue(':updated_by', $editor_id);
$statement->execute();
$statement->closeCursor();
 
// Display the activities List page
$success = $statement->execute();

if ($success) {
    echo "<p style='color:green'>Profile updated successfully!</p>";

    // 2 SECOND REFRESH REQUIREMENT
    echo "<script>setTimeout(function() {window.location='index.php';}, 2000);</script>";
} else {
    echo "Update failed.";
}
?>

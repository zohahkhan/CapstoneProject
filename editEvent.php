<?php
require_once './include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user_id = $_SESSION['user']['user_id'];

// for database script to 'see' session variable
$db->exec("SET @current_role_id = " . (int)$_SESSION['user']['role_id']);

$eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
$editTitle  = filter_input(INPUT_POST, 'title');
$editDate   = filter_input(INPUT_POST, 'edit_date');
$editLocation = filter_input(INPUT_POST, 'location');
$editDescription  = filter_input(INPUT_POST, 'description');
$saveChanges = filter_input(INPUT_POST, 'saveChanges');

if 

($editTitle == null || $editDate == null || 
		$editLocation == null || $editDescription == null) 
{
	header('Location: calendar.php?error=1');
}
else 
{ 

//update query
$updateEvent = 'UPDATE calendarEvent
		 SET event_title = :editTitle, 
		 event_desc =:editDescription, 	 
		 event_location =:editLocation, 
		 event_date = :editDate, 
		 updated_at = NOW(),
		 updated_by = :updated_by
		 WHERE event_id =:eventId';
$statement = $db->prepare($updateEvent);
$statement->bindValue(':eventId', $eventId);
$statement->bindValue(':editTitle', $editTitle);
$statement->bindValue(':editDescription', $editDescription);
$statement->bindValue(':editLocation', $editLocation);
$statement->bindValue(':editDate', $editDate);
$statement->bindValue(':updated_by', $user_id);
$statement->execute();
$statement->closeCursor();
 
	header('Location: calendar.php?success=1');	
}
?>

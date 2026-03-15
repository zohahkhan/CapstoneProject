<?php
require_once 'include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_id']))
{
    header("Location: login.php");
    exit;
}

// post date from calendar click
if(!isset($_POST['event_id']))
{ 
	exit("No event selected"); 
} 

$event_id = intval($_POST['event_id']);

// query events for selected date
$stmt = $db->prepare("SELECT event_id, event_title, event_desc, event_location,  event_date, created_at
						FROM CalendarEvent 
						WHERE event_id = :event_id
						ORDER BY event_date");
$stmt->bindParam(":event_id", $event_id);
$stmt->execute();
$result = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<body>
	<div class="container" id="eventPopup">
	<div id="eventDetails">

	<?php foreach ($result as $r): ?>
	<h1>Today's Event is</h1><br>
	
	<h2 id="eventTitle">
		<?php echo htmlspecialchars($r['event_title']); ?>
	</h2>
<br>
	<div id="eventDate" class="event-detail">
		<strong>Date:</strong> 
		<?php echo date("F j, Y g:i A", strtotime($r['event_date'])); ?>
	</div>

	<div id="eventLocation" class="event-detail">
		<strong>Location:</strong> 
		<?php echo htmlspecialchars($r['event_location']); ?>
	</div>

	<div id="eventDescription" class="event-detail">
		<strong>Description:</strong><br>
		<?php echo nl2br(htmlspecialchars($r['event_desc'])); ?>
	</div>
<br>
	<!-- only president and dept head can edit events -->
	<?php if ($_SESSION['user']['role_id'] === 1 || $_SESSION['user']['role_id'] === 2) { ?>
	<button onclick="showEditForm()">Edit Event</button>

	<?php  } endforeach; ?>
</div>
</div>

</body>
</html>



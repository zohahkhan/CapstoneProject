<?php
require_once __DIR__ . '/../include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_id']))
{
    header("Location: ../login.php");
    exit;
}

// post date from calendar click
if (!isset($_POST['event_id']))
{ 
	exit("No event selected"); 
} 

$event_id = intval($_POST['event_id']);

// CHANGED: get clicked occurrence date from calendar.php
$occurrence_date = $_POST['occurrence_date'] ?? null;

// CHANGED: load recurrence fields too
$stmt = $db->prepare("
    SELECT 
        event_id,
        event_title,
        event_desc,
        event_location,
        event_date,
        created_at,
        is_recurring,
        recurrence_type,
        recurrence_count,
        recurrence_end_date,
        recurrence_days_of_week
    FROM CalendarEvent 
    WHERE event_id = :event_id
");
$stmt->bindParam(":event_id", $event_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
		<?php
            // CHANGED: prefer clicked occurrence date if available
            $displayDate = !empty($occurrence_date) ? $occurrence_date : $r['event_date'];
            echo date("F j, Y g:i A", strtotime($displayDate));
        ?>
	</div>

	<div id="eventLocation" class="event-detail">
		<strong>Location:</strong> 
		<?php echo htmlspecialchars($r['event_location']); ?>
	</div>

	<div id="eventDescription" class="event-detail">
		<strong>Description:</strong><br>
		<?php echo nl2br(htmlspecialchars($r['event_desc'])); ?>
	</div>

    <?php if (!empty($r['is_recurring'])): ?>
    <div class="event-detail">
        <strong>Recurring:</strong>
        <?php echo htmlspecialchars(ucfirst($r['recurrence_type'])); ?>
    </div>

    <?php if (!empty($r['recurrence_count'])): ?>
    <div class="event-detail">
        <strong>Iterations:</strong>
        <?php echo (int)$r['recurrence_count']; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($r['recurrence_end_date'])): ?>
    <div class="event-detail">
        <strong>Ends:</strong>
        <?php echo date("F j, Y g:i A", strtotime($r['recurrence_end_date'])); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($r['recurrence_days_of_week'])): ?>
    <div class="event-detail">
        <strong>Days of week:</strong>
        <?php echo htmlspecialchars($r['recurrence_days_of_week']); ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

<br>

	<!-- only president and dept head can edit events -->
	<?php if ($_SESSION['user']['role_id'] == 1 || $_SESSION['user']['role_id'] == 2) { ?>
        <button onclick="showEditForm()">Edit Event</button>

        <?php if (!empty($r['is_recurring'])) { ?>
            <br><br>

            <!-- CHANGED: cancel only clicked occurrence -->
            <form method="post" action="cancelEvent.php" style="display:inline-block; margin-right:10px;">
                <input type="hidden" name="event_id" value="<?php echo (int)$r['event_id']; ?>">
                <input type="hidden" name="occurrence_date" value="<?php echo htmlspecialchars($occurrence_date ?? $r['event_date']); ?>">
                <input type="hidden" name="type" value="single">
                <button type="submit">Cancel this occurrence</button>
            </form>

            <!-- CHANGED: cancel whole recurring series -->
            <form method="post" action="cancelEvent.php" style="display:inline-block;">
                <input type="hidden" name="event_id" value="<?php echo (int)$r['event_id']; ?>">
                <input type="hidden" name="type" value="series">
                <button type="submit">Cancel entire series</button>
            </form>
        <?php } ?>
	<?php } ?>

	<?php endforeach; ?>
</div>
</div>

</body>
</html>

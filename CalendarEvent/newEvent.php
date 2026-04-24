<?php
require_once __DIR__ . '/../include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

// for database script to 'see' session variable for trigger log activity
$db->exec("SET @current_role_id = " . (int)$_SESSION['user']['role_id']);
$db->exec("SET @current_user_id = " . (int)$_SESSION['user']['user_id']);


// alert for if a new event is added
if (isset($_GET['success']) && $_GET['success'] == 1) 
{
    echo "<script>alert('Your event has been created!');</script>";

    // redirect to same page without get query string 
    $url = strtok($_SERVER["REQUEST_URI"], '?'); 
    echo "<script>window.location.href='$url';</script>";
    exit();
}

// CHANGED: kept recurring and iterations, added recurrence_end_date
$event_title = $event_desc = $event_location = $event_date = $recurring = $created_at = $created_by = '';
$iterations = null;
$recurrence_end_date = null;
$days_of_week = [];

	
$create = filter_input(INPUT_POST, 'create');


if (isset($create)) 
{
    $event_title = filter_input(INPUT_POST,'event_title');
    $event_desc = filter_input(INPUT_POST,'event_desc');
	$event_location = filter_input(INPUT_POST, 'event_location');
    $event_date = filter_input(INPUT_POST, 'event_date');
    // $recurring = filter_input(INPUT_POST, 'recurring');
    //$iterations = filter_input(INPUT_POST, 'iterations', FILTER_VALIDATE_INT);
    //$days_of_week = filter_input(INPUT_POST, 'days_of_week');
  	// CHANGED: keep old names recurring + iterations
    $recurring = filter_input(INPUT_POST, 'recurring') ?? 'NULL';
    $iterations = filter_input(INPUT_POST, 'iterations', FILTER_VALIDATE_INT);
    $recurrence_end_date = filter_input(INPUT_POST, 'recurrence_end_date');
    $days_of_week = $_POST['days_of_week'] ?? [];
	
	// set default values
	$created_at = date("Y-m-d H:i:s");
	$created_by = $_SESSION['user']['user_id'];
	
	if ($event_date) {
    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $event_date);
    $event_date = $dt ? $dt->format('Y-m-d H:i:s') : null;
    
    }
  	    // CHANGED: format optional recurrence end date
    if (!empty($recurrence_end_date)) {
        $endDt = DateTime::createFromFormat('Y-m-d\TH:i', $recurrence_end_date);
        $recurrence_end_date = $endDt ? $endDt->format('Y-m-d H:i:s') : null;
    }
	$errors = [];
	
	
	if (empty($event_title) || $event_title == false) 
	{
        $errors['event_title'] = 'event_title is required';
    }
	else if (empty($event_desc) || $event_desc == false) 
	{
        $errors['event_desc'] = 'event_desc is required';
    }
	else if (empty($event_location) || $event_location == false) 
	{
        $errors['event_location'] = 'event_location is required';
    }
	else if (empty($event_date) || $event_date == false) 
	{
        $errors['event_date'] = 'event_date is required';
    }
 	else if ($recurring !== 'NULL' && !empty($iterations) && !empty($recurrence_end_date)) {
    $errors['error'] = 'Choose either number of iterations OR end date, not both.';
	}
	else if ($recurring !== 'NULL' && empty($iterations) && empty($recurrence_end_date)) {
    $errors['error'] = 'Recurring events need either iterations or an end date.';
}
	/*
	else if ($recurring == false) 
	{
        $errors['error'] = 'Error-recurring: incorrect value type. Please try again.';
    }
	else if ($iterations == false) {
		$errors['error'] = 'Error-iterations: incorrect value type. Please try again.';
	}
	else if ($days_of_week == false) {
	$errors['error'] = 'Error-days_of_week: incorrect value type. Please try again.';
	}
	*/
	else if ($created_at == false) {
		$errors['error'] = 'Error-created_at: incorrect value type. Please try again.';
		
	} else if ($created_by == false) {
		$errors['error'] = 'Error-created_by: incorrect value type. Please try again.';
	}
	
	
    else
//		if (empty($errors)) 
	{  
// CHANGED: map existing recurring dropdown values
$is_recurring = ($recurring !== 'NULL') ? 1 : 0;
$recurrence_type = 'none';

if ($recurring === 'Daily') {
    $recurrence_type = 'daily';
} elseif ($recurring === 'Weekly') {
    $recurrence_type = 'weekly';
} elseif ($recurring === 'Monthly') {
    $recurrence_type = 'monthly';
} elseif ($recurring === 'Annually') {
    $recurrence_type = 'yearly';
}

$daysString = !empty($days_of_week) ? implode(',', $days_of_week) : null;
        // query the new member into the database
		/* i removed $days_of_week , recurring , iterations 
		from query insert and values temporarily to test required fields 
		*/
// CHANGED: save recurring fields too
$queryInsertEvent = 'INSERT INTO CalendarEvent 
    (
        event_title,
        event_desc,
        event_location,
        event_date,
        is_recurring,
        recurrence_type,
        recurrence_count,
        recurrence_end_date,
        recurrence_days_of_week,
        created_at,
        created_by
    ) 
    VALUES (
        :event_title,
        :event_desc,
        :event_location,
        :event_date,
        :is_recurring,
        :recurrence_type,
        :recurrence_count,
        :recurrence_end_date,
        :recurrence_days_of_week,
        :created_at,
        :created_by
    )';
				  
        $statement = $db->prepare($queryInsertEvent);
		$statement->bindParam(':event_title', $event_title);
		$statement->bindParam(':event_desc', $event_desc);
        $statement->bindParam(':event_location', $event_location);
        $statement->bindParam(':event_date', $event_date);
       // $statement->bindParam(':recurring', $recurring);
       // $statement->bindParam(':iterations', $iterations);
       // $statement->bindParam(':days_of_week', $daysString);
      	// CHANGED: bind recurrence values
        $statement->bindValue(':is_recurring', $is_recurring, PDO::PARAM_INT);
        $statement->bindValue(':recurrence_type', $recurrence_type);
        $statement->bindValue(':recurrence_count', !empty($iterations) ? $iterations : null, PDO::PARAM_INT);
        $statement->bindValue(':recurrence_end_date', !empty($recurrence_end_date) ? $recurrence_end_date : null);
        $statement->bindValue(':recurrence_days_of_week', $daysString);
		$statement->bindParam(':created_at', $created_at);
		$statement->bindParam(':created_by', $created_by);
		$statement->execute();
      
	  header('Location: calendar.php?success=1');
            exit();

	}}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="icon" href= ../images/logo.png>
		<title>Final Project</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="../style.css">
	</head>
<body>
	<header>
		<h1>Event Creation</h1>
	</header>
<br><br>
	<style>
		.error {
			color:red;
		}
	</style>
    <main>
        <?php if (isset($errors['error'])) : ?>
            <p class="error">
			<?php echo $errors['error']; ?></p>
        <?php endif; ?>
        <form method="POST" action="newEvent.php">
         
		<div class="form-group" style="width: 320px;">
		
			<div>
				<label for="event_title"  class="labels">Event Title:</label>
				<input type="text" name="event_title" id="event_title" value="<?php echo $event_title; ?>" required placeholder = "Give the event a name">
					<?php if (isset($errors['event_title'])) : ?>
						<p class="error"><?php echo $errors['event_title']; ?></p>
					<?php endif; ?>	
			</div>	
				<br>
			<div >
				<label for="event_desc"  class="labels">Description: </label>
				<input type="text" name="event_desc" id="event_desc" value="<?php echo $event_desc; ?>" required placeholder = "What details will people need to know?">
					<?php if (isset($errors['event_desc'])) : ?>
						<p class="error"><?php echo $errors['event_desc']; ?></p>
					<?php endif; ?>		
			</div>
				<br>
			<div>
				<label for="event_location"  class="labels">Location: </label>
				<input type="text" name="event_location" id="event_location" value="<?php echo $event_location; ?>" required placeholder ="Where will it take place?">
					<?php if (isset($errors['event_location'])) : ?>
						<p class="error"><?php echo $errors['event_location']; ?></p>
					<?php endif; ?>
			</div>
				<br>
				
			<div>
				<label for="event_date"  class="labels">What date and time will it occur? </label><br>
				<input type="datetime-local" name="event_date" id="event_date"
       				value="<?php echo !empty($event_date) ? date('Y-m-d\TH:i', strtotime($event_date)) : ''; ?>" required>
					<?php if (isset($errors['event_date'])) : ?>
						<p class="error"><?php echo $errors['event_date']; ?></p>
					<?php endif; ?>			
			</div>			 
				<br>
		<!---- non-required fields for automation: recurring, iterations, days_of_week  ----->
				<br>
				<?php
					$recurringOptions = [
						'NULL' => 'None',
						'Daily' => 'Daily',
						'Weekly' => 'Weekly',
						'Monthly' => 'Monthly',
						'Annually' => 'Annually'
					];
				?>			
			<div>
				<label for="recurring"  class="labels">Is this event recurring? (optional)</label>
				<select name="recurring" id="recurring">
					<?php foreach ($recurringOptions as $value => $label): ?>
						<option value="<?= $value ?>" <?= ($recurring === $value ? 'selected' : '') ?>>
    						<?= $label ?>
							</option>
					<?php endforeach; ?>
				</select>
			</div>	
				<br>
				<!--- will need to be made nullable if field is empty --->
			<div>
				<label for="iterations">Number of iterations:</label>
				<input type="number" name="iterations" id="iterations" placeholder = 0 min="0" max="1000" step="1">
			</div>
				<br>
            <div>
                    <!-- CHANGED: added end date input -->
                    <label for="recurrence_end_date" class="labels">Or end date:</label>
                    <input type="datetime-local" name="recurrence_end_date" id="recurrence_end_date"
                        value="<?php echo !empty($recurrence_end_date) ? date('Y-m-d\TH:i', strtotime($recurrence_end_date)) : ''; ?>">
                </div>
                <br>
				
			<style>	
				.labels {
					font-weight: bold;
				}
				.days {
					display: flex;
					gap: 9px;
				}
				.day {
					display: flex;
					flex-direction: column;
					align-items: center;
					font-size: 14px;
				}
			</style>
				
				<?php
					$weekDays = [
						'SU' => 'Sun',
						'MO' => 'Mon',
						'TU' => 'Tues',
						'WE' => 'Wed',
						'TH' => 'Thur',
						'FR' => 'Fri',
						'SA' => 'Sat'
					];
					$myDays = $days_of_week ?? ($_POST['days_of_week'] ?? []);
				?>
				<label for="days_of_week" class="labels"> For which days of the week? (optional) </label>
			<div class="days">	
				<?php foreach ($weekDays as $value => $label): ?>
				<label class="day">				
					<input type="checkbox" name="days_of_week[]" value="<?= $value ?>"
					<?= in_array($value, $myDays) ? 'checked' : '' ?>>
					<span><?= $label ?></span>
				</label>
					<br>
				<?php endforeach; ?>

			</div>
				<br>
			<div style="text-align: center;">
				<button type="submit" name="create">Create</button>
				<p><a href="calendar.php" style="color: #4b3d29;">Back to Calendar</a></p>
			</div>
		</div>
		</form>
    </main>
</body>
</html>

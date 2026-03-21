<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once 'include/db_connect.php';

// alert for if a new event is added
if (isset($_GET['success']) && $_GET['success'] == 1) 
{
    echo "<script>alert('Your event has been created!');</script>";

    // redirect to same page without get query string 
    $url = strtok($_SERVER["REQUEST_URI"], '?'); 
    echo "<script>window.location.href='$url';</script>";
    exit();
}

$event_title = $event_desc = $event_location = $event_date = $recurring = $iterations = $days_of_week = $created_at = $created_by = '';

	
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
	
	// set default values
	$created_at = date("Y-m-d H:i:s");
	$created_by = $_SESSION['user']['user_id'];
	
	if ($event_date) {
    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $event_date);
    $event_date = $dt ? $dt->format('Y-m-d H:i:s') : null;
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

        // query the new member into the database
		/* i removed $days_of_week , recurring , iterations 
		from query insert and values temporarily to test required fields 
		*/
        $queryInsertEvent = 'INSERT INTO CalendarEvent 
		
			    (event_title, event_desc, event_location, event_date, created_at, created_by) 
				  
		VALUES (:event_title, :event_desc, :event_location, :event_date, :created_at, :created_by)';
        $statement = $db->prepare($queryInsertEvent);
		$statement->bindParam(':event_title', $event_title);
		$statement->bindParam(':event_desc', $event_desc);
        $statement->bindParam(':event_location', $event_location);
        $statement->bindParam(':event_date', $event_date);
       // $statement->bindParam(':recurring', $recurring);
       // $statement->bindParam(':iterations', $iterations);
       // $statement->bindParam(':days_of_week', $daysString);
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
		<title>Final Project</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="style.css">
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
				<input type="datetime-local" name="event_date" id = "event_date" value="<?php echo $event_date; ?>" required >
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
						<option value="<?= $value ?>"><?= $label ?></option>
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
					$myDays = $_POST['days_of_week'] ?? [];
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

				<?php $daysString = implode(',', $myDays); ?>
			</div>
				<br>
			<div style="text-align: center;">
				<button type="submit" name="create">Create</button>
				<p><a href="calendar.php">Back to Calendar</a></p>
			</div>
		</div>
		</form>
    </main>
</body>
</html>

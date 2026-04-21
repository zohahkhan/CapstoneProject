<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_id']))
{
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../include/db_connect.php';

// success message for edit event
if (isset($_GET['success'])) {
	$success = "Event successfully updated!";
    echo "<p style='color:green;'>$success</p>";
}
// error message if event doesn't updated
if (isset($_GET['error'])) {
	$error = "Incomplete data. Check all fields and try again.";
    echo "<p style='color:red;'>$error</p>";
}

$user_id = $_SESSION['user']['user_id'];
$active_role = $_SESSION['user']['role_id'] ?? null;

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1;  $year++; }

$today_day   = (int)date('j');
$today_month = (int)date('n');
$today_year  = (int)date('Y');

$first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
$days_in_month      = (int)date('t', $first_day_of_month);
$start_weekday      = (int)date('w', $first_day_of_month);

$month_name = date('F', $first_day_of_month);

$prev_month = $month - 1;
$prev_year  = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }

$next_month = $month + 1;
$next_year  = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

$stmt = $db->prepare("
    SELECT event_id, event_title, event_date
    FROM calendarevent
    WHERE YEAR(event_date) = :year AND MONTH(event_date) = :month
    ORDER BY event_date ASC
");
$stmt->bindParam(':year',  $year,  PDO::PARAM_INT);
$stmt->bindParam(':month', $month, PDO::PARAM_INT);
$stmt->execute();
$events_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events_by_day = [];
foreach ($events_raw as $event)
{
    $day = (int)date('j', strtotime($event['event_date']));
    $events_by_day[$day][] = $event;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body
        {
            font-family: 'Poppins', sans-serif;
            background-image: url("../images/background.png");
        }

        .calendar-wrapper
        {
            max-width: 900px;
            margin: 40px auto;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            padding: 40px;
            font-family: 'Poppins', sans-serif;
        }

        .calendar-header
        {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .calendar-header h2
        {
            font-size: 1.6rem;
            font-weight: 600;
            color: #3b2f2f;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .nav-btn
        {
            background-color: #c4a484;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .nav-btn:hover
        {
            background-color: #b39578;
        }

        .jump-form
        {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .jump-form select
        {
            background-color: #fff8f3;
            border: 1px solid #e8d9c8;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.9rem;
            color: #3b2f2f;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        .jump-form select:focus
        {
            outline: none;
            border-color: #c4a484;
        }

        .jump-btn
        {
            background-color: #c4a484;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .jump-btn:hover
        {
            background-color: #b39578;
        }

        .calendar-grid
        {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .day-label
        {
            text-align: center;
            font-weight: 600;
            color: #3b2f2f;
            padding: 8px 0;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
        }

        .day-cell
        {
            min-height: 90px;
            background-color: #fff8f3;
            border-radius: 8px;
            padding: 6px;
            border: 1px solid #e8d9c8;
            vertical-align: top;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }

        .day-cell.empty
        {
            background-color: transparent;
            border: none;
        }

        .day-cell.today
        {
            border: 2px solid #c4a484;
            background-color: #f5e9d9;
        }

        .day-number
        {
            font-weight: 600;
            font-size: 0.95rem;
            color: #3b2f2f;
            margin-bottom: 4px;
        }

        .day-cell.today .day-number
        {
            color: #8a5e3c;
        }

        .event-chip
        {
            background-color: #c4a484;
            color: white;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 0.72rem;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            font-family: 'Poppins', sans-serif;
        }

        .back-link
        {
            display: inline-block;
            margin-bottom: 20px;
            color: #c4a484;
            text-decoration: none;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }

        .back-link:hover
        {
            color: #b39578;
        }

        .legend
        {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #7a6655;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .legend-dot
        {
            width: 12px;
            height: 12px;
            background-color: #c4a484;
            border-radius: 3px;
            display: inline-block;
        }

        .legend-today
        {
            width: 12px;
            height: 12px;
            background-color: #f5e9d9;
            border: 2px solid #c4a484;
            border-radius: 3px;
            display: inline-block;
        }
        
            /* css to view each event */
        .modal 
        {
            display:none;
            position:fixed;
            z-index:1000;
            left:0;
            top:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.6);
        }
        
        .modal-content 
        {
            background:white;
            width:500px;
            margin:10% auto;
            padding:20px;
            border-radius:8px;
        }

        .close 
        {
            float:right;
            font-size:26px;
            cursor:pointer;
        }

        .event-detail 
        {
            margin-bottom:10px;
        }
		.links 
		{
    		display: flex;
    		justify-content: space-between;
		}
    </style>
</head>
<body>
<div class="calendar-wrapper">

	<div class="links">
    	<a href="../index.php" class="back-link">&larr; Back to Home</a>
		<?php if ($_SESSION['user']['role_id'] == 1 || $_SESSION['user']['role_id'] == 2) { ?>
		<a href="newEvent.php" class="back-link"> Add New Event</a>
		<?php } ?>
	</div>

    <div class="calendar-header">
        <a href="calendar.php?month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="nav-btn">&lsaquo;</a>
        <h2><?= htmlspecialchars($month_name) ?> <?= $year ?></h2>
        <a href="calendar.php?month=<?= $next_month ?>&year=<?= $next_year ?>" class="nav-btn">&rsaquo;</a>
    </div>

    <form method="GET" action="calendar.php" class="jump-form">
        <select name="month">
            <?php
            $month_names = ['January','February','March','April','May','June',
                            'July','August','September','October','November','December'];
            foreach ($month_names as $i => $mn)
            {
                $val = $i + 1;
                $selected = ($val === $month) ? 'selected' : '';
                echo '<option value="' . $val . '" ' . $selected . '>' . $mn . '</option>';
            }
            ?>
        </select>
        <select name="year">
            <?php
            $start_year = 1900;
            $end_year   = 2100;
            for ($y = $start_year; $y <= $end_year; $y++)
            {
                $selected = ($y === $year) ? 'selected' : '';
                echo '<option value="' . $y . '" ' . $selected . '>' . $y . '</option>';
            }
            ?>
        </select>
        <button type="submit" class="jump-btn">Go</button>
    </form>

    <div class="calendar-grid">
        <?php
        $day_labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        foreach ($day_labels as $label)
        {
            echo '<div class="day-label">' . $label . '</div>';
        }

        for ($i = 0; $i < $start_weekday; $i++)
        {
            echo '<div class="day-cell empty"></div>';
        }

        for ($day = 1; $day <= $days_in_month; $day++)
        {
            $is_today = ($day === $today_day && $month === $today_month && $year === $today_year);
            $class = 'day-cell' . ($is_today ? ' today' : '');
            echo '<div class="' . $class . '">';
            echo '<div class="day-number">' . $day . '</div>';

            if (isset($events_by_day[$day]))
            {
                foreach ($events_by_day[$day] as $ev)
                {
                    (int)$event_id = $ev['event_id'];
                    echo '<span class="event-chip" onclick="openEvent(' . $event_id . ')" title="' 
                        . htmlspecialchars($ev['event_title']) . '">'
                        . htmlspecialchars($ev['event_title'])
                        . '</span>';
                }
            }

            echo '</div>';
        }
        ?>
    </div>

    <div class="legend">
        <span class="legend-today"></span> Today &nbsp;
        <span class="legend-dot"></span> Event
    </div>

</div>

<!-----  form to edit calendar events !! --->

<div id="eventModal" class="modal"> 
<div class="modal-content"> 
<span class="close" onclick="closeModal()">&times;</span> 
<div id="eventDetails"> Loading event... </div>

<div id="editView" style="display:none;">
	<table>
    <form id="editForm" method="post" action="editEvent.php">
			
		<input type="hidden" name="event_id" id="eventId">
		
		<tr>
			<td><label>Title</label></td>
            <td><input type="text" name="title" id="editTitle"></td>
		</tr>
		
			<input type="hidden" name="edit_date" id="editDate">
		<tr>
			<td><label>Month</label></td>	
			<td><select id="edit_month" onchange="updateDateTime()">
				<option value="01">January</option>
				<option value="02">February</option>
				<option value="03">March</option>
				<option value="04">April</option>
				<option value="05">May</option>
				<option value="06">June</option>
				<option value="07">July</option>
				<option value="08">August</option>
				<option value="09">September</option>
				<option value="10">October</option>
				<option value="11">November</option>
				<option value="12">December</option>
				</select></td>
		
			<td><label>Day</label></td>		
			<td><select id="edit_day" onchange="updateDateTime()">
				<?php
					for ($d=1; $d<=31; $d++) 
					{
						printf("<option value='%02d'>%d</option>", $d, $d);
					}
				?>
				</select></td>
			
			<td><label>Year</label></td>
			<td><select id="edit_year" onchange="updateDateTime()">
				<?php
					$currentYear = date("Y");
					for ($y=$currentYear-2; $y<=$currentYear+5; $y++) 
					{
						echo "<option value='$y'>$y</option>";
					}
				?>
				</select></td>
		</tr>
		
		<tr>
			<td><label>Time</label></td>
				<?php 
					$stmt5 = $db->prepare("
									SELECT event_date 
									FROM CalendarEvent 
									WHERE event_id = :event_id");
					$stmt5->bindParam(":event_id", $event_id);
					$stmt5->execute();
					$result = $stmt5->fetch(PDO::FETCH_ASSOC);

					if ($result) 
					{
						$time = $result['event_date'];

						$hr12 = date("g", strtotime($time));   // 1-12
						$min = date("i", strtotime($time));    // 00-59
						$ampm   = date("A", strtotime($time)); // AM or PM
				?>
<?php var_dump($event_id); ?>
<?php var_dump($time); ?>
<?php var_dump($hr12); ?>
<?php var_dump($min); ?>
<?php var_dump($ampm); ?>

				<td><select id="edit_hour" onchange="updateDateTime()">
				<?php
					for ($h=1; $h<13; $h++) 
					{
						echo "<option value='$h'>$h</option>";
					}
				?>
				</select>
		
				<?php } ?>
				<select id="edit_minute" onchange="updateDateTime()">
				<?php
					for ($m=0; $m<60; $m+=5) 
					{
						printf("<option value='%02d'>: %02d</option>", $m, $m);
					}
				?>
				</select>

				<select id="edit_time" onchange="updateDateTime()">
					<option value="AM" <?php echo ($ampm=="AM") ? "selected" : ""; ?>>AM</option>
					<option value="PM" <?php echo ($ampm=="PM") ? "selected" : ""; ?>>PM</option>
					</select></td>	
		</tr>
		
		<tr>
            <td><label>Location</label></td>
            <td><input type="text" name="location" id="editLocation"></td>
		</tr>
            
		<tr>
			<td><label>Description</label></td>
            <td><textarea name="description" id="editDescription"></textarea></td>
		</tr>
		
		<tr>
            <td><button type="submit" name="saveChanges" >Save</button></td>
            <td><button type="button" onclick="cancelEdit()">Cancel</button></td>
		</tr>
  </form>
  </table>
    </div>

<script>
function openEvent(eventId)
{
    document.getElementById("eventModal").style.display = "block";
	document.getElementById("eventId").value = eventId;	
	
	fetch("viewEvent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "event_id=" + encodeURIComponent(eventId)
    })

        .then(response => response.text())
        .then(data => {
            document.getElementById("eventDetails").innerHTML = data;
        });
}

function closeModal()
{
    document.getElementById("eventModal").style.display = "none";
}

window.onclick = function(event)
{
    let modal = document.getElementById("eventModal");
    if (event.target == modal)
    {
        modal.style.display = "none";
    }
}

function convertTo24Hour(hour12Str) 
{
    let [h, ampm] = hour12Str.split(" ");
    h = parseInt(h, 10);
    ampm = ampm.toUpperCase();

    if (ampm === "AM") {
        return (h === 12) ? 0 : h; 
    } else { // PM
        return (h === 12) ? 12 : h + 12;
    }
}
function updateDateTime()
{
    let year   = document.getElementById("edit_year").value;
    let month  = document.getElementById("edit_month").value;
    let day    = document.getElementById("edit_day").value;
    let hour   = document.getElementById("edit_hour").value;
    let minute = document.getElementById("edit_minute").value;

	// convert hour to 24-hour format
	let hour12 = document.getElementById("edit_hour").value + " " +
             document.getElementById("edit_time").value;
			 
    let hour24 = convertTo24Hour(hour12);

    // keep all other values the same
    let hourStr = (hour24 === 0) ? "00" : hour24.toString().padStart(2,'0'); 

    let dateTime = `${year}-${month}-${day} ${hourStr}:${minute}:00`;

    document.getElementById("editDate").value = dateTime;
}

function showEditForm()
{
	// displays the edit event form
    document.getElementById("eventDetails").style.display = "none";
    document.getElementById("editView").style.display = "block";

    document.getElementById("editTitle").value =
        document.getElementById("eventTitle").innerText;

    document.getElementById("editLocation").value =
        document.getElementById("eventLocation").innerText.replace("Location:", "").trim();

    document.getElementById("editDescription").value =
        document.getElementById("eventDescription").innerText.replace("Description:", "").trim();
		

	// parse existing date string
    let dtStr = document.getElementById("eventDate").innerText.replace("Date:", "").trim();
    let dt = new Date(dtStr); // assumes format parseable by JS

    // prefill dropdowns
    document.getElementById("edit_year").value = dt.getFullYear();
    document.getElementById("edit_month").value = (dt.getMonth() + 1).toString().padStart(2,'0');
    document.getElementById("edit_day").value = dt.getDate().toString().padStart(2,'0');

    let hours = dt.getHours(); // 24-hour
    let ampm = (hours >= 12) ? "PM" : "AM";
    hours = hours % 12;
	
	// convert 0 to 12 for dropdown
    if (hours === 0) hours = 12; 
   
	document.getElementById("edit_hour").value = "<?= (int)$hr12 ?>";
	document.getElementById("edit_minute").value = "<?= $min ?>";
	document.getElementById("edit_time").value = "<?= $ampm ?>";

    // fill the hidden event_date field
    updateDateTime();
}

function cancelEdit()
{
    document.getElementById("editView").style.display = "none";
    document.getElementById("eventDetails").style.display = "block";
}
document.getElementById("editForm").addEventListener("submit", function(e)
{
    //e.preventDefault(); 
	
    let formData = new FormData(this);

    fetch("editEvent.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("formErrors").innerHTML = data;
    });
});
</script>
    
</body>
</html>

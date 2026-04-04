<!--homepage.php the homepage / user landing page-->
<?php
require_once './include/db_connect.php';

// alert for if a new member is added
if (isset($_GET['success']) && $_GET['success'] == 1) 
{
    echo "<script>alert('Account successfully created!');</script>";

    // redirect to same page without get query string 
    $url = strtok($_SERVER["REQUEST_URI"], '?'); 
    echo "<script>window.location.href='$url';</script>";
    exit();
}
// for database script to 'see' session variable
$db->exec("SET @current_role_id = " . (int)$_SESSION['user']['role_id']);


// check for an existing session
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

if (isset($_SESSION['user']['user_id'])) 
{
	if (!isset($user_id)) 
	{
		$user_id = $_SESSION['user']['user_id'];	
	}
}

if (isset($_POST['role'])) 
{
    $_SESSION['user']['role_id'] = $_POST['role'];
}

$queryAllUserRoles = 'SELECT Role.role_id, Role.role_name
						FROM Role					
						JOIN UserRole ON Role.role_id = UserRole.role_id
						WHERE UserRole.user_id = :user_id';
	$statement = $db->prepare($queryAllUserRoles);
	$statement->bindParam(':user_id', $user_id);
	$statement->execute();
	$role = $statement->fetchAll();


// fetches the template for compled Report
$template_id = 1;
$stmt = $db->prepare("
	SELECT COUNT(*) 
	FROM FormResponse 
	WHERE template_id = :template_id
");
$stmt->execute(['template_id' => $template_id]);
$totalReports = $stmt->fetchColumn();


// Mini calendar data
$mini_month = (int)date('n');
$mini_year  = (int)date('Y');
$mini_first_day = mktime(0, 0, 0, $mini_month, 1, $mini_year);
$mini_days_in_month = (int)date('t', $mini_first_day);
$mini_start_weekday = (int)date('w', $mini_first_day);
$mini_month_name = date('F', $mini_first_day);
$today_day = (int)date('j');

$stmtEvents = $db->prepare("
    SELECT event_date
    FROM calendarevent
    WHERE YEAR(event_date) = :year AND MONTH(event_date) = :month
");
$stmtEvents->bindParam(':year',  $mini_year,  PDO::PARAM_INT);
$stmtEvents->bindParam(':month', $mini_month, PDO::PARAM_INT);
$stmtEvents->execute();
$event_rows = $stmtEvents->fetchAll(PDO::FETCH_ASSOC);

$event_days = [];
foreach ($event_rows as $row)
{
    $event_days[] = (int)date('j', strtotime($row['event_date']));
}

//query for the upcoming events for popup announcement, only events from that month 
$stmtUpcoming = $db->prepare("
    SELECT event_title, event_date
    FROM calendarevent
    WHERE YEAR(event_date) = :year AND MONTH(event_date) = :month
    ORDER BY event_date ASC
");
$stmtUpcoming->bindParam(':year', $mini_year, PDO::PARAM_INT);
$stmtUpcoming->bindParam(':month', $mini_month, PDO::PARAM_INT);
$stmtUpcoming->execute();
$upcoming_events = $stmtUpcoming->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Homepage</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
	<style>
		.admin-link {
			font-size: 1.1em;
			color: #8b6f47;
			text-decoration: none;
			font-weight: 500;
		}
		.admin-link:hover {
			color: #6b5437;
			text-decoration: underline;
		}
		.mini-calendar-link {
			display: block;
			text-decoration: none;
			color: inherit;
		}

		.mini-calendar-link:hover .mini-calendar {
			opacity: 0.85;
		}

		.mini-calendar {
			width: 100%;
			border-collapse: collapse;
			font-size: 0.75rem;
			margin-top: 8px;
			transition: opacity 0.2s;
		}

		.mini-calendar th {
			text-align: center;
			color: #8b6f47;
			font-weight: 600;
			padding: 2px 0;
		}

		.mini-calendar td {
			text-align: center;
			padding: 3px 2px;
			color: #3b2f2f;
			position: relative;
		}

		.mini-calendar td.today {
			background-color: #c4a484;
			color: white;
			border-radius: 50%;
			font-weight: bold;
		}

		.mini-calendar td.has-event::after {
			content: '';
			display: block;
			width: 4px;
			height: 4px;
			background-color: #8b6f47;
			border-radius: 50%;
			margin: 1px auto 0;
		}

		.mini-calendar td.today.has-event::after {
			background-color: white;
		}

		.mini-cal-header {
			text-align: center;
			font-size: 0.8rem;
			font-weight: bold;
			color: #3b2f2f;
			margin-bottom: 4px;
		}

		.mini-cal-hint {
			text-align: center;
			font-size: 0.7rem;
			color: #c4a484;
			margin-top: 6px;
		}
	</style>
</head>

<body>
    <!--display user session information-->
    <?php		
        if (isset($_SESSION['user'])) 
		{
			echo "<h1>Hello, ";
			echo $_SESSION['user']['first_name']." ";
			echo $_SESSION['user']['last_name']."!</h1>";
			
			
			
			$queryCheckAdmin = 'SELECT COUNT(*) FROM UserRole ur
								JOIN Role r ON ur.role_id = r.role_id
								WHERE ur.user_id = :user_id AND r.role_name = "Admin"';
			$stmtCheck = $db->prepare($queryCheckAdmin);
			$stmtCheck->bindParam(':user_id', $_SESSION['user']['user_id']);
			$stmtCheck->execute();
			$isAdmin = $stmtCheck->fetchColumn() > 0;
		
			if ($isAdmin) {
				echo '<p><a href="manage_roles.php" class="admin-link">⚙ Manage User Roles & Permissions</a></p>';
			}
			echo "\n<h2>";
		
	?>
	<label>Current Role: </label>
	<form method="POST">
		<select name="role" onchange="this.form.submit()">
			<?php foreach ($role as $r): ?>
				<option value="<?= $r['role_id']; ?>"
					<?= (isset($_SESSION['user']['role_id']) && 
						$_SESSION['user']['role_id'] == $r['role_id']) 
						? 'selected' : ''; ?>>
					<?= $r['role_name']; ?>
				</option>
			<?php endforeach; ?>
		</select> 
	</form>
	<?php echo "</h2>";	?>		
	
	<!---- Calendar code ---->
	<?php
	// Mini calendar HTML block — reused across roles
	ob_start();
	?>
	<a href="../calendar.php" class="mini-calendar-link">
		<div class="mini-cal-header"><?= $mini_month_name ?> <?= $mini_year ?></div>
		<table class="mini-calendar">
			<thead>
				<tr>
					<th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$cell = 0;
				echo '<tr>';
				for ($i = 0; $i < $mini_start_weekday; $i++)
				{
					echo '<td></td>';
					$cell++;
				}
				for ($d = 1; $d <= $mini_days_in_month; $d++)
				{
					$classes = [];
					if ($d === $today_day) $classes[] = 'today';
					if (in_array($d, $event_days)) $classes[] = 'has-event';
					$class_str = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
					echo '<td' . $class_str . '>' . $d . '</td>';
					$cell++;
					if ($cell % 7 === 0 && $d < $mini_days_in_month) echo '</tr><tr>';
				}
				while ($cell % 7 !== 0)
				{
					echo '<td></td>';
					$cell++;
				}
				echo '</tr>';
				?>
			</tbody>
		</table>
		<div class="mini-cal-hint">Click to open full calendar</div>
	</a>
	<?php
	$mini_calendar_html = ob_get_clean();
	?>
	
	
	<!---- PRES HOMEPAGE ---->
	<?php if ($_SESSION['user']['role_id'] == 1) { ?>
	<div class="boxes">
		<!-- left box split horizontally into 2 -->
		<div class="left-box left-split">
			<div class="left-sub-box top-box">
				<h2>Compiled Monthly Report</h2>
				<p>Description</p>
				<a href="headDepartmentSummary.php">Compiled Monthly Report Summary</a>
			</div>
			<div class="left-sub-box bottom-box">
				<h2>Monthly Report</h2>
				<p>Description</p>
				<p><a href="viewUser.php" style="color: #c4a484; text-decoration: none;">View all members</a></p>
			</div>
		</div>

		<!--the right box with four separate boxes inside-->
		<div class="right-box">
			<div class="right-sub-box">
				<h2>Create a new Reminder</h2>
				<div class="scrollable-report-box">
				<div class="report-summary-box">
				<strong>Upcoming Events:</strong>
				<?php if (!empty($upcoming_events)) : ?>
					<ul class = "reminder-list">
						<?php foreach ($upcoming_events as $event): ?>
							<li>
							<strong><?= htmlspecialchars($event['event_title']) ?></strong><br>
							<?= date("F j", strtotime($event['event_date'])) ?>
							</li>
						<?php endforeach; ?>
       		 		</ul>
   					 <?php else: ?>
        			<p>No upcoming events this month.</p>
  				   <?php endif; ?>
				   </div>
				   </div>
			</div>

			<div class="right-sub-box">
				<h2>Calendar</h2>
				<?= $mini_calendar_html ?>
			</div>

			<div class="right-sub-box">
				<h2>Meeting Attendance</h2>
				<p>Description</p>
				<p><a href="record_attendance.php" style="color: #c4a484; text-decoration: none;">Record Attendance</a></p>
			</div>

			<div class="right-sub-box">
				<h2>Review Suggestions</h2>
				<p>Description</p>
			</div>
		</div>
	</div>
	</br>
	<p><a href="updateProfileForm.php">Update Profile</a></p>
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF PRES HOMEPAGE --->


	<!---- DEPT HOMEPAGE ----->
	<?php } else if ($_SESSION['user']['role_id'] == 2) { ?>
	<div class="boxes">
		<!-- left box, split horizontally into 2 -->
		<!-- left side-->
		<div class="dept-left-box">

			<div class="left-sub-box">				
				<h2>Monthly Report Responses</h2>
				<!-- scroll container -->
    			<div class="scrollable-report-box">
				<!-- stats summary box -->
				<div class="report-summary-box">
					<h3>Monthly Summary</h3>

					<div class="report-summary-content">
						<p><strong>Total Reports Submitted:</strong> <?= $totalReports ?></p>
					</div>
					
				</div>
				</div> 
				<p><a href="viewSummary.php">View summary</a></p>
			</div>


			<div class="left-sub-box">
				<h2>Monthly Report</h2>
				<p>Description</p>
			</div>

			<div class="left-sub-box dept-full-width">
				<h2>Compiled Monthly Report</h2>
				<p>Description</p>
				<p><?php include("include/surveyHub.php"); ?></p>
			</div>
	     </div>


		<!--the right box with four separate boxes inside-->
		<div class="right-box">
			<div class="right-sub-box">
				<h2>Create a new Reminder</h2>
				<div class="scrollable-report-box">
				<div class="report-summary-box">
				<strong>Upcoming Events:</strong>
				<?php if (!empty($upcoming_events)) : ?>
					<ul class = "reminder-list">
						<?php foreach ($upcoming_events as $event): ?>
							<li>
							<strong><?= htmlspecialchars($event['event_title']) ?></strong><br>
							<?= date("F j", strtotime($event['event_date'])) ?>
							</li>
						<?php endforeach; ?>
       		 		</ul>
   					 <?php else: ?>
        			<p>No upcoming events this month.</p>
  				   <?php endif; ?>
				   </div>
				   </div>
			</div>

			<div class="right-sub-box">
				<h2>Calendar</h2>
				<?= $mini_calendar_html ?>
			</div>

			<div class="right-sub-box">
				<h2>Meeting Attendance</h2>
				<p>Description</p>
				<p><a href="record_attendance.php" style="color: #c4a484; text-decoration: none;">Record Attendance</a></p>
			</div>

			<div class="right-sub-box">
				<h2>Review Suggestions</h2>
				<p>Description</p>
			</div>
		</div>
	</div>
	</br>
	<p><a href="updateProfileForm.php">Update Profile</a></p>
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF DEPT HOMEPAGE --->
	
	
	<!--- MEMBER HOMEPAGE --->
	<?php } else if ($_SESSION['user']['role_id'] == 3) { ?>
	<div class="boxes">
		<!--the left side big box-->
		<div class="box left-box">
			<h2>Monthly Report</h2>
		<?php include("include/surveyHub.php"); ?>

		</div>

		<!--the right box with four separate boxes inside-->
		<div class="right-box">
			<div class="right-sub-box">
				<h2>Important Reminders</h2>
				<div class="scrollable-report-box">
				<div class="report-summary-box">
				<strong>Upcoming Events:</strong>
					<?php if (!empty($upcoming_events)) : ?>
					<ul class = "reminder-list">
						<?php foreach ($upcoming_events as $event): ?>
							<li>
							<strong><?= htmlspecialchars($event['event_title']) ?></strong><br>
							<?= date("F j", strtotime($event['event_date'])) ?>
							</li>
						<?php endforeach; ?>
       		 		</ul>
   					 <?php else: ?>
        			<p>No upcoming events this month.</p>
  				   <?php endif; ?>
				   </div>
				   </div>
			</div>
			<div class="right-sub-box">
				<h2>Calendar</h2>
				<?= $mini_calendar_html ?>
			</div>

			<div class="right-sub-box">
				<h2>Meeting Attendance</h2>
				<p><a href="view_attendance.php" style="color: #c4a484; text-decoration: none;">View My Attendance</a></p>
			</div>

			<div class="right-sub-box">
				<h2>Suggestions</h2>
				<p><a href="memberSuggestion.php" style="color: #c4a484; text-decoration: none;">Suggestion</a></p>
				<p>Description</p>
			</div>
		</div>
	</div>
	</br>
	<p><a href="updateProfileForm.php">Update Profile</a></p>
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF MEMBER HOMEPAGE --->
		
	
	<!--- ADMIN HOMEPAGE ---->
	<?php } else if ($_SESSION['user']['role_id'] == 4) { ?>	
	
	 <div class="homepage-boxes">
        <!-- the top row with two boxes -->
        <div class="homepage-top">
            <div class="homepage-top-box">
                <h2>View Logs</h2>
                <p>Description</p>
				<p><a href="viewLog.php">Logs</a></p>
            </div>
            <div class="homepage-top-box">
                <h2>View Compiled Monthly Report</h2>
                <p>Description</p>
            </div>
        </div>
        <!--bottom box -->
        <div class="homepage-bottom-box">
            <h2>Members</h2>
            <p>Description</p>
			<p><a href="viewUser.php" style="color: #c4a484; text-decoration: none;">View all members</a></p>
        </div>
    </div>
	<br>
    <p><a href="logout.php">Logout</a></p>
	<!----- END OF ADMIN HOMEPAGE --->
		
	
	<!--this section is the default home screen when logged out-->
    <?php 
	}} else {
			echo "<h1>Welcome to Lajna Pittsburgh</h1>";
	?>
	<!--if the user is not logged in, display a login link-->
	<p><a href="login.php" style="text-decoration: none;">Login Here</a></p>
	<a href="contact.php" style="text-decoration: none;">Join Us</a>
	<?php } ?>
	
	<!--- display the events upcoming this month popup ---->
	<?php if (!empty($upcoming_events)) : ?>
	<div class="event-popup" id="eventPopup">
		<span class="close-popup" onclick="document.getElementById('eventPopup').style.display='none'">×</span>

		<h4>Upcoming This Month</h4>

		<ul>
			<?php foreach ($upcoming_events as $event): ?>
				<li>
					<strong><?= htmlspecialchars($event['event_title']) ?></strong><br>
					<?= date("F j", strtotime($event['event_date'])) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<script>
	setTimeout(function(){
		var popup = document.getElementById("eventPopup");
		if(popup){
			popup.style.display = "none";
		}
	}, 5000); // auto close after 5 seconds
	</script>
	<?php endif; ?>

</body>
</html>

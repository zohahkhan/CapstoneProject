<!--homepage.php the homepage / user landing page-->
<?php
require_once './include/db_connect.php';


if (isset($_GET['success']) && $_GET['success'] == 1) 
{
    echo "<script>alert('Account successfully created!');</script>";
    $url = strtok($_SERVER["REQUEST_URI"], '?'); 
    echo "<script>window.location.href='$url';</script>";
    exit();
}

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

$template_id = 1;
$stmt = $db->prepare("
    SELECT COUNT(*) 
    FROM FormResponse 
    WHERE template_id = :template_id
");
$stmt->execute(['template_id' => $template_id]);
$totalReports = $stmt->fetchColumn();

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

$stmtPending = $db->prepare("SELECT COUNT(*) FROM MemberSuggestion WHERE status = 'Pending'");
$stmtPending->execute();
$pendingSuggestions = $stmtPending->fetchColumn();

$stmtCompleted = $db->prepare("SELECT COUNT(*) FROM MemberSuggestion WHERE status IN ('Reviewed', 'Resolved')");
$stmtCompleted->execute();
$completedSuggestions = $stmtCompleted->fetchColumn();

$stmtMySuggestions = $db->prepare("SELECT COUNT(*) FROM MemberSuggestion WHERE user_id = :user_id");
$stmtMySuggestions->bindParam(':user_id', $user_id);
$stmtMySuggestions->execute();
$myTotalSuggestions = $stmtMySuggestions->fetchColumn();

$stmtMyPending = $db->prepare("SELECT COUNT(*) FROM MemberSuggestion WHERE user_id = :user_id AND status = 'Pending'");
$stmtMyPending->bindParam(':user_id', $user_id);
$stmtMyPending->execute();
$myPendingSuggestions = $stmtMyPending->fetchColumn();

$stmtActiveAnn = $db->prepare("SELECT COUNT(*) FROM Announcement WHERE announce_expiry > NOW() AND archived = 0");
$stmtActiveAnn->execute();
$activeAnnouncements = $stmtActiveAnn->fetchColumn();

$stmtAnnPreview = $db->prepare("
    SELECT announce_title, announce_expiry
    FROM Announcement
    WHERE announce_expiry > NOW() AND archived = 0
    ORDER BY announce_expiry ASC
    LIMIT 3
");
$stmtAnnPreview->execute();
$annPreview = $stmtAnnPreview->fetchAll(PDO::FETCH_ASSOC);
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
        .suggestion-preview {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            background-color: #fdfaf7;
            border-radius: 8px;
            border: 1px solid #e6d5c3;
            font-size: 0.85em;
            text-align: left;
            flex: 1;
        }
        .suggestion-preview p {
            margin: 4px 0;
            color: #3b2f2f;
        }
        .suggestion-preview .pending-count {
            font-weight: 600;
            color: #856404;
        }
        .suggestion-preview .completed-count {
            font-weight: 600;
            color: #155724;
        }
        .announcement-preview {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            background-color: #fdfaf7;
            border-radius: 8px;
            border: 1px solid #e6d5c3;
            font-size: 0.85em;
            text-align: left;
            flex: 1;
            box-sizing: border-box;
        }
        .announcement-preview p {
            margin: 4px 0;
            color: #3b2f2f;
        }
        .announcement-preview .active-count {
            font-weight: 600;
            color: #155724;
        }
        .ann-preview-item {
            padding: 5px 0;
            border-bottom: 1px solid #e6d5c3;
            font-size: 0.82em;
            color: #3b2f2f;
        }
        .ann-preview-item:last-child {
            border-bottom: none;
        }
        .ann-preview-item .ann-preview-title {
            font-weight: 600;
        }
        .ann-preview-item .ann-preview-expiry {
            font-size: 0.78em;
            color: #8b6f47;
        }
        .ann-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
        }
        .ann-links a {
            color: #c4a484;
            text-decoration: none;
            font-size: 0.9em;
        }
        .ann-links a:hover {
            text-decoration: underline;
        }
        .dept-full-width {
            grid-column: 1 / span 2;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 20px;
            overflow: hidden;
        }
        .dept-ann-full {
            grid-column: 1 / span 2;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
		.select-current-role {
			width: 200px !important;
			height: 38px !important;
			box-sizing: border-box;
			display: inline-block;
			font-size: 14px !important;
			border-radius: 5px;
			text-align: center;
			background-color: #faf5f0;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			border-color: white;
		}
		.profile-links {
			color: #4b3d29;
			text-align: center;
			display:block;
		}
    </style>
</head>

<body>
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
                echo '<p><a href="RoleAssign/manage_roles.php" class="admin-link">⚙ Manage User Roles & Permissions</a></p>';
            }
            echo "\n<h2>";
        
    ?>
    <label>Current Role: </label>
    <form method="POST">
        <select name="role" onchange="this.form.submit()" class="select-current-role">
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
	}, 5000);
	</script>
	<?php endif; ?>
    
    <?php
    ob_start();
    ?>
    <a href="CalendarEvent/calendar.php" class="mini-calendar-link">
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

    <!---- President ---->
    <?php if ($_SESSION['user']['role_id'] == 1) { ?>
	<style> body { background: url('images/background.png') !important; } </style>
    <div class="boxes">
        <div class="left-box left-split">
            <div class="left-sub-box top-box">
                <h2>Compiled Monthly Report</h2>
                <div class="scrollable-report-box"  style="height: 300px;"> 
					<?php include("SurveyPages/headDepartmentSummary.php"); ?>
				</div>
                <br><br>
                <a href="SurveyPages/headDepartmentSummary.php" style="color: #4b3d29;">Compiled Monthly Report Summary</a>
            </div>

            <div class="left-sub-box bottom-box">
                <h2>Monthly Report</h2>
                <div class="scrollable-report-box"  style="height: 300px;" > 
					<?php include("include/surveyHub.php"); ?>
				</div>
                <br>
                <p><a href="SurveyPages/memberSurvey.php" style="color: #4b3d29; ">Complete the Report</a></p>
            </div>
        </div>

        <div class="right-box">
            <div class="right-sub-box">
                <h2>Reminders</h2>
                <div class="scrollable-report-box" style="height: 250px;">
                <div class="report-summary-box">
                <strong>Upcoming Events:</strong>
                <?php if (!empty($upcoming_events)) : ?>
                    <ul class="reminder-list">
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
                <br><p style="color: #8b6f47; font-size: 1.6rem;">Attendance should be held for all events</p><br>
                <p><a href="AttendanceRecords/record_attendance.php" style="color: #4b3d29;">Record Attendance</a></p>
            </div>
    
            <div class="right-sub-box">
                <h2>Review Suggestions & Requests </h2>
                <div class="suggestion-preview">
                    <p>Pending: <span class="pending-count"><?= $pendingSuggestions ?></span></p>
                    <p>Reviewed / Resolved: <span class="completed-count"><?= $completedSuggestions ?></span></p>
                </div>
                <p><a href="Communications/reviewSuggestions.php" style="color: #4b3d29;">Review Suggestions</a></p>

				<?php $stmt = $db->prepare("SELECT COUNT(*) FROM `VisitorRequest` WHERE msg_status = :status");
      					$stmt->execute([':status' => 'Pending']);
      					$pendingCount = (int)$stmt->fetchColumn();
	  					if ($pendingCount): ?>
    				<p> You have <b><?= $pendingCount ?></b> pending request(s).<br>
      					<a href="Communications/president_requests.php" style="color: #4b3d29;">View Visitor Requests</a>
    				</p>
  				<?php else: ?>
    				<p style="color: #4b3d29;"> Visitor requests will show here.</p>
  				<?php endif; ?>
            </div>

            <div class="right-sub-box" style="grid-column: 1 / span 2;">
                <h2>Announcements</h2>
                <div class="announcement-preview">
                    <p>Active: <span class="active-count"><?= $activeAnnouncements ?></span></p>
                    <?php if (!empty($annPreview)): ?>
                        <?php foreach ($annPreview as $ap): ?>
                            <div class="ann-preview-item">
                                <div class="ann-preview-title"><?= htmlspecialchars($ap['announce_title']) ?></div>
                                <div class="ann-preview-expiry">Expires: <?= date("M j, Y g:i A", strtotime($ap['announce_expiry'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="ann-links">
                    <a href="Announcements/createAnnouncement.php" style="color: #4b3d29; text-decoration: underline;">Create Announcement</a>
                    <a href="Announcements/manageAnnouncements.php" style="color: #4b3d29; text-decoration: underline;">Manage Announcements</a>
                </div>
            </div>
        </div>
    </div>
    </br>
    <a href="UserActivity/updateProfileForm.php" class="profile-links">Update Profile</a>
    <p><a href="UserActivity/viewUser.php" class="profile-links">View all members</a></p>
    <a href="logout.php" class="profile-links">Logout</a>

    <!---- Department Head ---->
    <?php } else if ($_SESSION['user']['role_id'] == 2) { ?>
	<style> body { background: url('images/background.png') !important; } </style>
    <div class="boxes">
        <div class="dept-left-box">

            <div class="left-sub-box">				
                <h2>Monthly Report Responses</h2>
                <div class="scrollable-report-box">
                <div class="report-summary-box">
                    <h3>Monthly Summary</h3>
                    <div class="report-summary-content">
                        <p ><strong >Total Reports Submitted:</strong> <?= $totalReports ?></p>
                    </div>
                </div>
                </div> 
                <p><a href="SurveyPages/viewSummary.php" style="color: #4b3d29;">View summary</a></p>
            </div>

            <div class="left-sub-box">
                <h2>Monthly Report</h2>
                <div class="scrollable-report-box">
                <div class="report-summary-box">
                    <div class="report-summary-content">
                        <p><strong>Reports must be submitted by the 5th of every month.</strong></p>
                    </div>	
                </div>
                </div> 
                <p><a href="SurveyPages/memberSurvey.php" style="color: #4b3d29;">Complete the Report</a></p>
            </div>

            <div class="dept-full-width">
                <h2>Compiled Monthly Report</h2>
                <div class="scrollable-report-box-dept" style="height: 450px;" >
                    <?php include("include/surveyHub.php"); ?>
                </div>
                <p><a href="include/surveyHub.php" style="color: #4b3d29; ">Complete the Compiled Monthly Report</a></p>

            </div>
        </div>

        <div class="right-box">
            <div class="right-sub-box">
                <h2>Reminders</h2>
                <div class="scrollable-report-box" style="height: 230px;">
                <div class="report-summary-box">
                <strong>Upcoming Events:</strong>
                <?php if (!empty($upcoming_events)) : ?>
                    <ul class="reminder-list">
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
                <p style="color: #8b6f47; font-size: 1.4rem;">Attendance should be held for all events</p><br>
                <p><a href="AttendanceRecords/record_attendance.php" style="color: #4b3d29;">Record Attendance</a></p>
            </div>

            <div class="right-sub-box">
                <h2>Review Suggestions</h2>
                <div class="suggestion-preview">
                    <p>Pending: <span class="pending-count"><?= $pendingSuggestions ?></span></p>
                    <p>Reviewed / Resolved: <span class="completed-count"><?= $completedSuggestions ?></span></p>
                </div>
                <p><a href="Communications/reviewSuggestions.php" style="color: #4b3d29;">Review Suggestions</a></p>
            </div>

			 <div class="right-sub-box" style="grid-column: 1 / span 2;">
                <h2>Announcements</h2>
                <div class="announcement-preview">
                    <p>Active: <span class="active-count"><?= $activeAnnouncements ?></span></p>
                    <?php if (!empty($annPreview)): ?>
                        <?php foreach ($annPreview as $ap): ?>
                            <div class="ann-preview-item">
                                <div class="ann-preview-title"><?= htmlspecialchars($ap['announce_title']) ?></div>
                                <div class="ann-preview-expiry">Expires: <?= date("M j, Y g:i A", strtotime($ap['announce_expiry'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="ann-links">
                    <a href="Announcements/createAnnouncement.php" style="color: #4b3d29; text-decoration: underline;">Create Announcement</a>
                    <a href="Announcements/manageAnnouncements.php" style="color: #4b3d29; text-decoration: underline;">Manage Announcements</a>
                </div>
            </div>
        </div>
    </div>
    </br>
    <a href="UserActivity/updateProfileForm.php"  class="profile-links">Update Profile</a>
    <p><a href="logout.php"  class="profile-links">Logout</a></p> 

    <!---- Member ---->
    <?php } else if ($_SESSION['user']['role_id'] == 3) { ?>
	<style> body { background: url('images/background.png') !important; } </style>
    <div class="boxes">
        <div class="box left-box">
            <h2>Monthly Report</h2>
        <?php include("include/surveyHub.php"); ?>
        </div>

        <div class="right-box">
            <div class="right-sub-box" >
                <h2>Reminders</h2>
                <div class="scrollable-report-box" style="height: 230px;">
                <div class="report-summary-box">
                <strong>Upcoming Events:</strong>
                    <?php if (!empty($upcoming_events)) : ?>
                    <ul class="reminder-list">
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
                <p style="color: #8b6f47; font-size: 1.5rem;">Review your attendance held for all events </p>
                <p><a href="AttendanceRecords/view_attendance.php" style="color: #4b3d29;">View My Attendance</a></p>
            </div>

            <div class="right-sub-box">
                <h2>Suggestions</h2>
                <div class="suggestion-preview">
                    <p>My Submissions: <strong><?= $myTotalSuggestions ?></strong></p>
                    <p>Pending: <span class="pending-count"><?= $myPendingSuggestions ?></span></p>
                </div>
                <p><a href="Communications/memberSuggestion.php" style="color: #4b3d29;">Submit a Suggestion</a></p>
            </div>

            <div class="right-sub-box" style="grid-column: 1 / span 2;">
                <h2>Announcements</h2>
                <div class="announcement-preview">
                    <p>Active: <span class="active-count"><?= $activeAnnouncements ?></span></p>
                    <?php if (!empty($annPreview)): ?>
                        <?php foreach ($annPreview as $ap): ?>
                            <div class="ann-preview-item">
                                <div class="ann-preview-title"><?= htmlspecialchars($ap['announce_title']) ?></div>
                                <div class="ann-preview-expiry">Expires: <?= date("M j, Y g:i A", strtotime($ap['announce_expiry'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="ann-links">
                    <a href="Announcements/viewAnnouncements.php" style="color: #4b3d29; text-decoration: underline;">View Announcements</a>
                </div>
            </div>
        </div>
    </div>
    </br>
    <a href="UserActivity/updateProfileForm.php" class="profile-links">Update Profile</a>
    <p><a href="logout.php" class="profile-links">Logout</a></p>

    <!---- Admin ---->
    <?php } else if ($_SESSION['user']['role_id'] == 4) { ?>	
    <style> body { background: url('images/background.png') !important; } </style>
    <div class="homepage-boxes">
        <div class="homepage-top">
            <div class="homepage-top-box">
                <h2>View Logs</h2>
                <div class="scrollable-report-box" style="background-image: url('images/background.png');">
					<?php include("AuditLog/viewLog.php"); ?>
				</div>
				<p><a href="AuditLog/viewLog.php" style="color: #4b3d29;">View Logs</a></p>
            </div>
            <div class="homepage-top-box">
                <h2>View Compiled Monthly Report</h2>
               <div class="scrollable-report-box">
					<?php include("SurveyPages/headDepartmentSummary.php"); ?>
				</div>
				<p><a href="SurveyPages/headDepartmentSummary.php" style="color: #4b3d29;" >View Compiled Monthly Report Summary</a></p>
            </div>
        </div>
        <div class="homepage-bottom-box">
		<h2>Members</h2>
			<div class="scrollable-report-box"  style="background-image: url('images/background.png'); height: 300px;" >
				<?php include("UserActivity/viewUser.php"); ?>
			</div><br>
		<a href="UserActivity/viewUser.php" style="color: #4b3d29;">View all members</a>
		</div>
    </div>
    <br>
	<a href="UserActivity/updateProfileForm.php" class="profile-links">Update Profile</a>
    <p><a href="logout.php" class="profile-links">Logout</a></p>
	
    <?php 
	}} else {
			echo "<h1>Welcome to Lajna Pittsburgh</h1>";
	?>
	<p><a href="login.php" class="profile-links">Login Here</a></p>
	<a href="Communications/contact.php" class="profile-links">Join Us</a>
	<?php } ?>

</body>
</html>

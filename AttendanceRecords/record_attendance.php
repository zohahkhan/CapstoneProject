<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once './include/db_connect.php';

if (!isset($_SESSION['user'])) 
{
    echo '<p>You must be logged in to access this page.</p>';
    echo '<p><a href="./loginpages/login.php">Login here</a></p>';
    exit();
}

$role_id = $_SESSION['user']['role_id'];
$current_user_id = $_SESSION['user']['user_id'];

if (!in_array($role_id, [1, 2, 4])) 
{
    echo '<p>Access denied.</p>';
    echo '<p><a href="./loginpages/index.php">Back to Home</a></p>';
    exit();
}

$queryEvents = 'SELECT event_id, event_title, event_date FROM calendarevent ORDER BY event_date DESC';
$stmtEvents = $db->prepare($queryEvents);
$stmtEvents->execute();
$events = $stmtEvents->fetchAll();

$queryMembers = 'SELECT u.user_id, u.first_name, u.last_name
                 FROM User u
                 JOIN UserRole ur ON u.user_id = ur.user_id
                 WHERE ur.role_id = 3 AND u.is_active = 1
                 ORDER BY u.last_name, u.first_name';
$stmtMembers = $db->prepare($queryMembers);
$stmtMembers->execute();
$members = $stmtMembers->fetchAll();

$selected_event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;

$existingAttendance = [];
if ($selected_event_id) 
{
    $queryExisting = 'SELECT user_id, attend_status, check_in_time, notes FROM attendance WHERE event_id = :event_id';
    $stmtExisting = $db->prepare($queryExisting);
    $stmtExisting->bindParam(':event_id', $selected_event_id);
    $stmtExisting->execute();
    foreach ($stmtExisting->fetchAll() as $row) 
    {
        $existingAttendance[$row['user_id']] = [
            'status'        => $row['attend_status'],
            'check_in_time' => $row['check_in_time'],
            'notes'         => $row['notes']
        ];
    }
}

$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) 
{
    $success_message = 'Attendance saved successfully!';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Record Attendance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
            background-image: url('loginpages/images/background.png');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
        }
        h1 {
            text-align: center;
            width: 100%;
        }
        .content-box {
            width: 80%;
            max-width: 1400px;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            padding: 40px;
            text-align: center;
        }
        .event-select-form {
            margin-bottom: 30px;
        }
        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #c4a484;
            color: white;
        }
        .action-btn {
            padding: 8px 16px;
            margin: 5px;
            border-radius: 8px;
            border: none;
            background-color: #c4a484;
            color: white;
            cursor: pointer;
            font-size: 0.9em;
        }
        .action-btn:hover {
            background-color: #b39578;
        }
        .bulk-actions {
            margin: 20px 0;
            text-align: center;
        }
        .success {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }
        .export-btn {
            background-color: #6b8c6b;
        }
        .export-btn:hover {
            background-color: #5a7a5a;
        }
        .month-export-box {
            background-color: #f0e8df;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .month-export-box input[type="month"] {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
            margin: 0 10px;
        }
        input[type="time"]:disabled {
            background-color: #e0e0e0;
            color: #999;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Record Attendance</h1>
    <div class="content-box">

        <?php if ($success_message): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Month CSV Export -->
        <div class="month-export-box">
            <strong>Export Attendance by Month:</strong><br><br>
            <form method="GET" action="attendance_process.php" style="display:inline;">
                <input type="hidden" name="action" value="export_csv_month">
                <input type="month" name="month" value="<?php echo date('Y-m'); ?>" required>
                <button type="submit" class="action-btn export-btn">Export Month CSV</button>
            </form>
        </div>

        <!-- Event Selection -->
        <div class="event-select-form">
            <form method="GET" action="record_attendance.php">
                <label for="event_id"><strong>Select Event:</strong></label>
                <select name="event_id" id="event_id" onchange="this.form.submit()">
                    <option value="">-- Select an event --</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?php echo $event['event_id']; ?>"
                            <?php echo ($selected_event_id == $event['event_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($event['event_title']) . ' (' . date('M j, Y g:i A', strtotime($event['event_date'])) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($selected_event_id): ?>

        <!-- Per-Event CSV Export -->
        <div style="text-align:center; margin-bottom: 10px;">
            <form method="GET" action="attendance_process.php" style="display:inline;">
                <input type="hidden" name="action" value="export_csv">
                <input type="hidden" name="event_id" value="<?php echo $selected_event_id; ?>">
                <button type="submit" class="action-btn export-btn">Export This Event CSV</button>
            </form>
        </div>

        <!-- Attendance Form -->
        <form method="POST" action="attendance_process.php">
            <input type="hidden" name="event_id" value="<?php echo $selected_event_id; ?>">
            <input type="hidden" name="action" value="save_attendance">

            <div class="bulk-actions">
                <strong>Bulk Actions:</strong>
                <button type="button" class="action-btn" onclick="markAll('Present')">Mark All Present</button>
                <button type="button" class="action-btn" onclick="markAll('Absent')">Mark All Absent</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Check-In Time</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                    <?php
                        $currentStatus  = $existingAttendance[$member['user_id']]['status'] ?? 'Absent';
                        $currentCheckIn = $existingAttendance[$member['user_id']]['check_in_time'] ?? '';
                        $currentNotes   = $existingAttendance[$member['user_id']]['notes'] ?? '';
                        if ($currentCheckIn) {
                            $timeValue = date('H:i', strtotime($currentCheckIn));
                        } else {
                            $timeValue = '00:00';
                        }
                        $isDisabled = in_array($currentStatus, ['Absent', 'Excused']) ? 'disabled' : '';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                        <td>
                            <select name="attendance[<?php echo $member['user_id']; ?>][status]"
                                    class="status-select"
                                    onchange="handleStatusChange(this)">
                                <?php foreach (['Present', 'Absent', 'Late', 'Excused'] as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo $currentStatus === $status ? 'selected' : ''; ?>>
                                    <?php echo $status; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="time"
                                   name="attendance[<?php echo $member['user_id']; ?>][check_in_time]"
                                   value="<?php echo htmlspecialchars($timeValue); ?>"
                                   class="checkin-time"
                                   <?php echo $isDisabled; ?>
                                   style="padding:6px; border-radius:6px; border:1px solid #ccc;">
                        </td>
                        <td>
                            <input type="text"
                                   name="attendance[<?php echo $member['user_id']; ?>][notes]"
                                   value="<?php echo htmlspecialchars($currentNotes); ?>"
                                   placeholder="Optional notes"
                                   class="notes-input"
                                   style="padding:6px; border-radius:6px; border:1px solid #ccc; width:100%;">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="action-btn" style="margin-top: 20px;">Save Attendance</button>
        </form>

        <?php endif; ?>

        <p style="margin-top: 30px;"><a href="./loginpages/index.php">Back to Home</a></p>
    </div>

    <script>
        function handleStatusChange(selectEl) {
            const row = selectEl.closest('tr');
            const timeInput = row.querySelector('.checkin-time');
            const notesInput = row.querySelector('.notes-input');
            const status = selectEl.value;

            if (status === 'Absent' || status === 'Excused') {
                timeInput.disabled = true;
                timeInput.value = '00:00';
            } else {
                timeInput.disabled = false;
            }
        }

        function markAll(status) {
            document.querySelectorAll('.status-select').forEach(function(select) {
                select.value = status;
                const row = select.closest('tr');
                const timeInput = row.querySelector('.checkin-time');
                const notesInput = row.querySelector('.notes-input');

                // Reset notes and time
                notesInput.value = '';
                timeInput.value = '00:00';

                // Gray out time if Absent
                if (status === 'Absent' || status === 'Excused') {
                    timeInput.disabled = true;
                } else {
                    timeInput.disabled = false;
                }
            });
        }
    </script>
</body>
</html>

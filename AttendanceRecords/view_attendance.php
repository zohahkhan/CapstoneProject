<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once __DIR__ . '/../include/db_connect.php';

if (!isset($_SESSION['user'])) 
{
    echo '<p>You must be logged in to access this page.</p>';
    echo '<p><a href="../login.php">Login here</a></p>';
    exit();
}

$current_user_id = $_SESSION['user']['user_id'];

$query = 'SELECT ce.event_title, ce.event_date, a.attend_status, a.notes, a.check_in_time
          FROM attendance a
          JOIN calendarevent ce ON a.event_id = ce.event_id
          WHERE a.user_id = :user_id
          ORDER BY ce.event_date DESC';
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $current_user_id);
$stmt->execute();
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Attendance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
            background-image: url('../images/background.png');
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
        .status-present { color: green; font-weight: bold; }
        .status-absent { color: red; font-weight: bold; }
        .status-late { color: orange; font-weight: bold; }
        .status-excused { color: #8b6f47; font-weight: bold; }
    </style>
</head>
<body>
    <h1>My Attendance History</h1>
    <div class="content-box">
        <?php if (empty($records)): ?>
            <p>No attendance records found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['event_title']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($record['event_date'])); ?></td>
                    <td class="status-<?php echo strtolower($record['attend_status']); ?>">
                        <?php echo htmlspecialchars($record['attend_status']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($record['notes']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <p style="margin-top: 30px;"><a href="../index.php">Back to Home</a></p>
    </div>
</body>
</html>

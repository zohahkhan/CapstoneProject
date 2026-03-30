<?php
session_start();

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../include/db_connect.php';

$event_id = $_POST['event_id'] ?? null;
$title = trim($_POST['event_title'] ?? '');
$description = trim($_POST['event_desc'] ?? '');
$start = $_POST['start_datetime'] ?? '';
$end = $_POST['end_datetime'] ?? '';
$location = trim($_POST['event_location'] ?? '');
$recurring = trim($_POST['recurring'] ?? '');
$recurrence_type = trim($_POST['recurrence_type'] ?? '');
$recurrence_interval = (int)($_POST['recurrence_interval'] ?? 1);
$recurrence_end_date = !empty($_POST['recurrence_end_date']) ? $_POST['recurrence_end_date'] : null;

if (!$event_id || $title === '' || $start === '' || $end === '') {
    die('Missing required fields.');
}

if (strtotime($end) <= strtotime($start)) {
    echo "<div style='font-family: Arial; padding:20px;'>
            <h3 style='color:red;'>End date/time must be after start date/time.</h3>
            <a href='edit_event.php?id=" . urlencode($event_id) . "' style='
                display:inline-block;
                padding:10px 15px;
                background:#7d5a50;
                color:white;
                text-decoration:none;
                border-radius:5px;
            '>Back to Edit</a>
          </div>";
    exit;
}

if ($recurring === '') {
    $recurrence_type = null;
    $recurrence_interval = 1;
    $recurrence_end_date = null;
} else {
    if ($recurrence_type === '') {
        switch ($recurring) {
            case 'Daily':
                $recurrence_type = 'daily';
                break;
            case 'Weekly':
                $recurrence_type = 'weekly';
                break;
            case 'Monthly':
                $recurrence_type = 'monthly';
                break;
            case 'Annually':
                $recurrence_type = 'yearly';
                break;
        }
    }
}

$stmt = $db->prepare("
    UPDATE CalendarEvent
    SET event_title = :event_title,
        event_desc = :event_desc,
        event_location = :event_location,
        event_date = :event_date,
        start_datetime = :start_datetime,
        end_datetime = :end_datetime,
        recurring = :recurring,
        recurrence_type = :recurrence_type,
        recurrence_interval = :recurrence_interval,
        recurrence_end_date = :recurrence_end_date,
        updated_at = NOW()
    WHERE event_id = :event_id
");

$stmt->execute([
    ':event_title' => $title,
    ':event_desc' => $description,
    ':event_location' => $location,
    ':event_date' => $start,
    ':start_datetime' => $start,
    ':end_datetime' => $end,
    ':recurring' => ($recurring !== '' ? $recurring : null),
    ':recurrence_type' => $recurrence_type,
    ':recurrence_interval' => $recurrence_interval,
    ':recurrence_end_date' => $recurrence_end_date,
    ':event_id' => $event_id
]);

header('Location: calendar.php');
exit;
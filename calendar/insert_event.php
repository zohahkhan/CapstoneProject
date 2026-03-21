<?php
session_start();

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../include/db_connect.php';

$title = trim($_POST['event_title'] ?? '');
$description = trim($_POST['event_desc'] ?? '');
$start = $_POST['start_datetime'] ?? '';
$end = $_POST['end_datetime'] ?? '';
$location = trim($_POST['event_location'] ?? '');
$recurring = trim($_POST['recurring'] ?? '');
$recurrence_type = trim($_POST['recurrence_type'] ?? '');
$recurrence_interval = (int)($_POST['recurrence_interval'] ?? 1);
$recurrence_end_date = !empty($_POST['recurrence_end_date']) ? $_POST['recurrence_end_date'] : null;

if ($title === '' || $start === '' || $end === '') {
    die('Missing required fields.');
}

if (strtotime($end) <= strtotime($start)) {
    die('End date/time must be after start date/time.');
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
    INSERT INTO CalendarEvent (
        event_title,
        event_desc,
        event_location,
        event_date,
        start_datetime,
        end_datetime,
        recurring,
        recurrence_type,
        recurrence_interval,
        recurrence_end_date,
        status,
        created_at,
        updated_at
    ) VALUES (
        :event_title,
        :event_desc,
        :event_location,
        :event_date,
        :start_datetime,
        :end_datetime,
        :recurring,
        :recurrence_type,
        :recurrence_interval,
        :recurrence_end_date,
        'active',
        NOW(),
        NOW()
    )
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
    ':recurrence_end_date' => $recurrence_end_date
]);

header('Location: calendar.php');
exit;
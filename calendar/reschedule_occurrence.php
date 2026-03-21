<?php
session_start();

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../include/db_connect.php';

$event_id = $_POST['event_id'] ?? null;
$occurrence_date = $_POST['occurrence_date'] ?? null;
$new_start = $_POST['new_start_datetime'] ?? null;
$new_end = $_POST['new_end_datetime'] ?? null;
$reason = trim($_POST['reason'] ?? '');

if (!$event_id || !$occurrence_date || !$new_start || !$new_end) {
    die('Missing required fields.');
}

if (strtotime($new_end) <= strtotime($new_start)) {
    die('End date/time must be after start date/time.');
}

$stmt = $db->prepare("
    INSERT INTO CalendarEvent_Exception (
        event_id,
        original_occurrence_date,
        action_type,
        new_start_datetime,
        new_end_datetime,
        reason,
        created_at
    ) VALUES (
        :event_id,
        :original_occurrence_date,
        'rescheduled',
        :new_start_datetime,
        :new_end_datetime,
        :reason,
        NOW()
    )
");

$stmt->execute([
    ':event_id' => $event_id,
    ':original_occurrence_date' => $occurrence_date,
    ':new_start_datetime' => $new_start,
    ':new_end_datetime' => $new_end,
    ':reason' => $reason
]);

header('Location: calendar.php');
exit;
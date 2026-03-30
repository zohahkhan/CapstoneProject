<?php
session_start();

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../include/db_connect.php';

$event_id = $_POST['event_id'] ?? null;
$occurrence_date = $_POST['occurrence_date'] ?? null;
$reason = trim($_POST['reason'] ?? '');

if (!$event_id || !$occurrence_date) {
    die('Missing required fields.');
}

$stmt = $db->prepare("
    INSERT INTO CalendarEvent_Exception (
        event_id,
        original_occurrence_date,
        action_type,
        reason,
        created_at
    ) VALUES (
        :event_id,
        :original_occurrence_date,
        'cancelled',
        :reason,
        NOW()
    )
");

$stmt->execute([
    ':event_id' => $event_id,
    ':original_occurrence_date' => $occurrence_date,
    ':reason' => $reason
]);

header('Location: calendar.php');
exit;
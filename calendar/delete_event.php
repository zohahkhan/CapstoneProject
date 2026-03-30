<?php
session_start();

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../include/db_connect.php';

$event_id = $_POST['event_id'] ?? null;

if (!$event_id) {
    die('Missing event ID.');
}

$stmt = $db->prepare("
    UPDATE CalendarEvent
    SET status = 'cancelled',
        updated_at = NOW()
    WHERE event_id = :event_id
");

$stmt->execute([
    ':event_id' => $event_id
]);

header('Location: calendar.php');
exit;
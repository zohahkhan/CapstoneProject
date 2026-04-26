<?php
require_once __DIR__ . '/../include/db_connect.php';
session_start();

$event_id = $_POST['event_id'];
$type = $_POST['type'];

// for database script to 'see' session variable
$db->exec("SET @current_role_id = " . (int)$_SESSION['user']['role_id']);
$db->exec("SET @current_user_id = " . (int)$_SESSION['user']['user_id']);


if ($type === 'single') {

    // CHANGED: cancel ONE occurrence
    $occurrence_date = $_POST['occurrence_date'];

    $stmt = $db->prepare("
        INSERT INTO CalendarEvent_Exception
        (event_id, occurrence_date, action_type)
        VALUES (:event_id, :occurrence_date, 'cancelled')
    ");
    $stmt->execute([
        ':event_id' => $event_id,
        ':occurrence_date' => $occurrence_date
    ]);

} else {

    // CHANGED: cancel entire series
    $stmt = $db->prepare("
        UPDATE CalendarEvent
        SET is_cancelled = 1
        WHERE event_id = :event_id
    ");
    $stmt->execute([':event_id' => $event_id]);
}

header("Location: calendar.php");

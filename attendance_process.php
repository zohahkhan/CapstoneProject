<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once './include/db_connect.php';

if (!isset($_SESSION['user'])) 
{
    header('Location: ./loginpages/login.php');
    exit();
}

$current_user_id = $_SESSION['user']['user_id'];
$role_id = $_SESSION['user']['role_id'];

if (!in_array($role_id, [1, 2, 4])) 
{
    header('Location: ./loginpages/index.php');
    exit();
}

$action = $_REQUEST['action'] ?? '';

// SAVE ATTENDANCE
if ($action === 'save_attendance' && $_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $event_id = (int)$_POST['event_id'];
    $attendance_data = $_POST['attendance'] ?? [];

    $eventQuery = 'SELECT event_date FROM calendarevent WHERE event_id = :event_id';
    $eventStmt = $db->prepare($eventQuery);
    $eventStmt->bindParam(':event_id', $event_id);
    $eventStmt->execute();
    $eventRow = $eventStmt->fetch();
    $eventDate = $eventRow ? date('Y-m-d', strtotime($eventRow['event_date'])) : date('Y-m-d');

    foreach ($attendance_data as $user_id => $data) 
    {
        $user_id = (int)$user_id;
        $status = $data['status'];
        $notes = $data['notes'] ?? '';
        $taken_at = date('Y-m-d H:i:s');

        $timeInput = $data['check_in_time'] ?? '';
        if ($timeInput && !in_array($status, ['Absent', 'Excused'])) {
            $check_in_time = $eventDate . ' ' . $timeInput . ':00';
        } else {
            $check_in_time = $taken_at;
        }

        $checkQuery = 'SELECT attendance_id, attend_status, check_in_time, notes FROM attendance WHERE user_id = :user_id AND event_id = :event_id';
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':user_id', $user_id);
        $checkStmt->bindParam(':event_id', $event_id);
        $checkStmt->execute();
        $existing = $checkStmt->fetch();

        if ($existing) 
        {
            $updateQuery = 'UPDATE attendance SET attend_status = :status, notes = :notes, taken_by = :taken_by, taken_at = :taken_at, check_in_time = :check_in_time
                            WHERE user_id = :user_id AND event_id = :event_id';
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':status', $status);
            $updateStmt->bindParam(':notes', $notes);
            $updateStmt->bindParam(':taken_by', $current_user_id);
            $updateStmt->bindParam(':taken_at', $taken_at);
            $updateStmt->bindParam(':check_in_time', $check_in_time);
            $updateStmt->bindParam(':user_id', $user_id);
            $updateStmt->bindParam(':event_id', $event_id);
            $updateStmt->execute();

            if ($existing['attend_status'] !== $status || $existing['notes'] !== $notes) 
            {
                $beforeJson = json_encode(['attend_status' => $existing['attend_status'], 'check_in_time' => $existing['check_in_time'], 'notes' => $existing['notes']]);
                $afterJson  = json_encode(['attend_status' => $status, 'check_in_time' => $check_in_time, 'notes' => $notes]);
                $auditQuery = 'INSERT INTO auditlog (user_id, action, entity_type, entity_id, before_json, after_json)
                               VALUES (:user_id, :action, :entity_type, :entity_id, :before_json, :after_json)';
                $auditStmt = $db->prepare($auditQuery);
                $auditStmt->execute([
                    ':user_id'     => $current_user_id,
                    ':action'      => 'Update',
                    ':entity_type' => 'attendance',
                    ':entity_id'   => $existing['attendance_id'],
                    ':before_json' => $beforeJson,
                    ':after_json'  => $afterJson
                ]);
            }
        } 
        else 
        {
            $insertQuery = 'INSERT INTO attendance (user_id, event_id, attend_status, check_in_time, taken_by, taken_at, notes)
                            VALUES (:user_id, :event_id, :status, :check_in_time, :taken_by, :taken_at, :notes)';
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bindParam(':user_id', $user_id);
            $insertStmt->bindParam(':event_id', $event_id);
            $insertStmt->bindParam(':status', $status);
            $insertStmt->bindParam(':check_in_time', $check_in_time);
            $insertStmt->bindParam(':taken_by', $current_user_id);
            $insertStmt->bindParam(':taken_at', $taken_at);
            $insertStmt->bindParam(':notes', $notes);
            $insertStmt->execute();

            $beforeJson = json_encode([]);
            $afterJson  = json_encode(['attend_status' => $status, 'check_in_time' => $check_in_time, 'notes' => $notes]);
            $auditQuery = 'INSERT INTO auditlog (user_id, action, entity_type, entity_id, before_json, after_json)
                           VALUES (:user_id, :action, :entity_type, :entity_id, :before_json, :after_json)';
            $auditStmt = $db->prepare($auditQuery);
            $auditStmt->execute([
                ':user_id'     => $current_user_id,
                ':action'      => 'Create',
                ':entity_type' => 'attendance',
                ':entity_id'   => $event_id,
                ':before_json' => $beforeJson,
                ':after_json'  => $afterJson
            ]);
        }
    }

    header('Location: record_attendance.php?event_id=' . $event_id . '&success=1');
    exit();
}

// Helper function for check-in time formatting in CSV
function formatCheckIn($check_in_time, $status) {
    if (in_array($status, ['Absent', 'Excused'])) {
        return 'N/A';
    }
    if ($check_in_time) {
        $dt = new DateTime($check_in_time, new DateTimeZone('America/New_York'));
        return $dt->format('g:i A') . ' EST';
    }
    return '';
}

// CSV EXPORT BY EVENT
if ($action === 'export_csv') 
{
    $event_id = (int)$_GET['event_id'];

    $query = 'SELECT u.first_name, u.last_name, u.user_email, ce.event_title, ce.event_date,
                     a.attend_status, a.check_in_time, a.notes
              FROM attendance a
              JOIN User u ON a.user_id = u.user_id
              JOIN calendarevent ce ON a.event_id = ce.event_id
              WHERE a.event_id = :event_id
              ORDER BY u.last_name, u.first_name';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':event_id', $event_id);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_event_' . $event_id . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['First Name', 'Last Name', 'Email', 'Event', 'Event Date/Time', 'Status', 'Check-In Time', 'Notes']);

    foreach ($rows as $row) 
    {
        fputcsv($output, [
            $row['first_name'],
            $row['last_name'],
            $row['user_email'],
            $row['event_title'],
            date('M j, Y g:i A', strtotime($row['event_date'])),
            $row['attend_status'],
            formatCheckIn($row['check_in_time'], $row['attend_status']),
            $row['notes']
        ]);
    }

    fclose($output);
    exit();
}

// CSV EXPORT BY MONTH
if ($action === 'export_csv_month') 
{
    $month = $_GET['month'] ?? date('Y-m');
    $year  = substr($month, 0, 4);
    $mon   = substr($month, 5, 2);

    $query = 'SELECT u.first_name, u.last_name, u.user_email, ce.event_title, ce.event_date,
                     a.attend_status, a.check_in_time, a.notes
              FROM attendance a
              JOIN User u ON a.user_id = u.user_id
              JOIN calendarevent ce ON a.event_id = ce.event_id
              WHERE YEAR(ce.event_date) = :year AND MONTH(ce.event_date) = :month
              ORDER BY ce.event_date, u.last_name, u.first_name';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':month', $mon);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $monthLabel = date('F_Y', mktime(0, 0, 0, $mon, 1, $year));

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_' . $monthLabel . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['First Name', 'Last Name', 'Email', 'Event', 'Event Date/Time', 'Status', 'Check-In Time', 'Notes']);

    foreach ($rows as $row) 
    {
        fputcsv($output, [
            $row['first_name'],
            $row['last_name'],
            $row['user_email'],
            $row['event_title'],
            date('M j, Y g:i A', strtotime($row['event_date'])),
            $row['attend_status'],
            formatCheckIn($row['check_in_time'], $row['attend_status']),
            $row['notes']
        ]);
    }

    fclose($output);
    exit();
}

header('Location: record_attendance.php');
exit();
?>
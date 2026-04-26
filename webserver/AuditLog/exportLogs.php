<?php
require_once __DIR__ . '/../include/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!in_array($_SESSION['user']['role_id'], [4])) {
    require_once __DIR__ . '/../include/config.php';
    $error_page = BASE_URL . '/include/error.php';
    header("Location: $error_page");
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'export_csv') {

    $log_id      = $_POST['log_id'] ?? '';
    $user_id     = $_POST['user_id'] ?? '';
    $role_name   = $_POST['role_name'] ?? '';
    $action_type = $_POST['action_type'] ?? '';
    $entity_type = $_POST['entity_type'] ?? '';
    $occurred_at = $_POST['occurred_at'] ?? '';
    $entity_id   = $_POST['entity_id'] ?? '';
    $json_1      = $_POST['json_1'] ?? '';
    $json_2      = $_POST['json_2'] ?? '';
    $json_3      = $_POST['json_3'] ?? '';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $counter = $_SESSION['export_counter'] ?? 0;
    $counter++;
    $_SESSION['export_counter'] = $counter;

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="audit_log_' . $log_id . '_' . $counter . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Log ID','Change(s) Made by','User Role','Action Type','Entity Affected','Row Affected','Date/Time Occured','Before','After','Diff']);

    fputcsv($output, [
        $log_id,
        $user_id,
        $role_name,
        $action_type,
        $entity_type,
        $entity_id,
        $occurred_at !== '' ? date('M j, Y g:i A', strtotime($occurred_at)) : '',
        $json_1,
        $json_2,
        $json_3
    ]);

    fclose($output);
    exit();
}

if ($action === 'export_all_csv') {

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $counter = $_SESSION['export_counter'] ?? 0;
    $counter++;
    $_SESSION['export_counter'] = $counter;

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="audit_log_all_' . date('Ymd_His') . '_' . $counter . '.csv"');

    $queryAllLogs = 'SELECT a.*, r.role_name
                     FROM AuditLog a
                     LEFT JOIN Role r ON a.role_id = r.role_id';
    $stmt = $db->prepare($queryAllLogs);
    $stmt->execute();
    $allLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Log ID','User ID','User Role','Action Type','Entity Affected','Row Affected','Date/Time Occured','Key','Before','After','Changes']);

    foreach ($allLogs as $row) {
        $before = !empty($row['before_json']) ? json_decode($row['before_json'], true) : [];
        $after  = !empty($row['after_json'])  ? json_decode($row['after_json'], true)  : [];
        $diff   = !empty($row['diff_json'])   ? json_decode($row['diff_json'], true)   : [];

        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after), array_keys($diff)));

        foreach ($allKeys as $field) {
            fputcsv($output, [
                $row['log_id'] ?? '',
                $row['user_id'] ?? '',
                $row['role_name'] ?? '',
                $row['action'] ?? '',
                $row['entity_type'] ?? '',
                $row['entity_id'] ?? '',
                $row['occurred_at'] ?? '',
                $field,
                $before[$field] ?? '',
                $after[$field] ?? '',
                $diff[$field] ?? ''
            ]);
        }
    }

    fclose($output);
    exit();
}
?>
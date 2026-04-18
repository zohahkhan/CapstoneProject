<?php
require_once __DIR__ . '/../include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

$action = $_POST['action'] ?? '';

if (!empty($action)) 
{

// EXPORT ONE SELECTED LOG 
if ($action === 'export_csv')	{
	
	if (!isset($log_id)) 
{
	$log_id = $_POST['log_id'];
}
if (!isset($user_id)) 
{
	$user_id = $_POST['user_id'];
}
if (!isset($role_name)) 
{
	$role_name = $_POST['role_name'];
}
if (!isset($action_type)) 
{
	$action_type = $_POST['action_type'];
}
if (!isset($entity_type)) 
{
	$entity_type = $_POST['entity_type'];
}
if (!isset($occurred_at)) 
{
	$occurred_at = $_POST['occurred_at'];
}
if (!isset($entity_id)) 
{
	$entity_id = $_POST['entity_id'];
}

if (!isset($json_1)) 
{
	$json_1 = $_POST['json_1'];
}
if (!isset($json_2)) 
{
	$json_2 = $_POST['json_2'];
}
if (!isset($json_3)) 
{
	$json_3 = $_POST['json_3'];
}

if (!isset($action)) 
{
	$action = $_POST['action'];
}
ob_clean();

$counter = $_SESSION['export_counter'] ?? 0;
$counter++;
$_SESSION['export_counter'] = $counter;
	
    header('Content-Type: text/csv');
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
            date('M j, Y g:i A', strtotime($_POST['occurred_at'])),
			$json_1,
			$json_2,
			$json_3
           
        ]);

    fclose($output);
    exit();
}


// EXPORT ALL THE LOGS TO AUDIT 
if ($action === 'export_all_csv')	{
	
	
	if (!isset($role_name)) 
{
	$role_name = $_POST['role_name'];
}
ob_clean();

$counter = $_SESSION['export_counter'] ?? 0;
$counter++;
$_SESSION['export_counter'] = $counter;

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="audit_log_all_' . date('Ymd_His') . '_' . $counter . '.csv"');


	$queryAllLogs = 'SELECT a.* , r.role_name
					FROM AuditLog a
					JOIN Role r ON a.role_id = r.role_id';
	$stmt = $db->prepare($queryAllLogs);
	$stmt->execute();
	$allLogs=$stmt->fetchAll();

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Log ID','User ID','User Role','Action Type','Entity Affected','Row Affected','Date/Time Occured','Key','Before','After','Changes']);

   
    foreach ($allLogs as $row) {
		$before = json_decode($row['before_json'], true) ?? [];
		$after = json_decode($row['after_json'], true) ?? [];
		$diff = json_decode($row['diff_json'], true) ?? [];

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

}
?>

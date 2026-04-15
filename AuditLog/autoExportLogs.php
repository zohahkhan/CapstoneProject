<?php
require_once __DIR__ . '/../include/db_connect.php';

// CONFIG
$exportFolder = 'C:\\xampp\\htdocs\\exports\\'; // folder to store CSVs
$lastExportFile = $exportFolder . 'last_export.txt';
$maxFileAgeDays = 365; // delete CSVs older than 1 year
$exportIntervalDays = 89; // days between exports

if (!file_exists($exportFolder)) {
    mkdir($exportFolder, 0755, true);
}

// CHECK 89-DAY INTERVAL
$lastExport = file_exists($lastExportFile) ? (int)file_get_contents($lastExportFile) : 0;
$intervalSeconds = $exportIntervalDays * 24 * 60 * 60;

if (time() - $lastExport < $intervalSeconds) {
    exit("Export skipped. Not yet $exportIntervalDays days since last export.\n");
}

// PREPARE CSV
$filename = 'audit_log_auto_' . date('Ymd_His') . '.csv';
$fullPath = $exportFolder . $filename;


$query = 'SELECT a.* , r.role_name
		FROM AuditLog a
		JOIN Role r ON a.role_id = r.role_id';
$stmt = $db->prepare($query);
$stmt->execute();
$allLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = fopen($filename, 'w');

fputcsv($output, ['Log ID', 'User ID', 'Role ID', 'Role Name', 'Action', 'Entity Type', 'Entity ID', 'Occurred At', 'Field', 'Before', 'After', 'Change']);

foreach ($allLogs as $row) {
    $before = json_decode($row['before_json'], true) ?? [];
    $after = json_decode($row['after_json'], true) ?? [];
    $diff = json_decode($row['diff_json'], true) ?? [];
    $allKeys = array_unique(array_merge(array_keys($before), array_keys($after), array_keys($diff)));

    foreach ($allKeys as $field) {
        fputcsv($output, [
            $row['log_id'] ?? '',
            $row['user_id'] ?? '',
			$row['role_id'] ?? '',
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



// UPDATE LAST EXPORT TIMESTAMP
file_put_contents($lastExportFile, time());

// CLEAN UP OLD CSV FILES
$files = glob($exportFolder . '*.csv');
$maxAgeSeconds = $maxFileAgeDays * 24 * 60 * 60;
foreach ($files as $file) {
    if (filemtime($file) < time() - $maxAgeSeconds) {
        unlink($file);
    }
}

echo "CSV export completed: $fullPath\n";


?>

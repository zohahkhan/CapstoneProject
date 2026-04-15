<?php
require_once('include/db_connect.php');

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}


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



$current_user = $_SESSION['user']['user_id'];


$queryProfile = 'SELECT before_json, after_json, diff_json  
				 FROM `AuditLog`
				 WHERE log_id = :log_id';
$statement = $db->prepare($queryProfile);
$statement->bindValue(':log_id', $log_id);
$statement->execute();
$data_json = $statement->fetch(PDO::FETCH_ASSOC);
$statement->closeCursor();

$json1 = is_array($data_json['before_json']) ? $data_json['before_json'] : ($data_json['before_json'] ? json_decode($data_json['before_json'], true) : []);

$json2 = is_array($data_json['after_json']) ? $data_json['after_json'] : ($data_json['after_json'] ? json_decode($data_json['after_json'], true) : []);

$json3 = is_array($data_json['diff_json']) ? $data_json['diff_json'] : ($data_json['diff_json'] ? json_decode($data_json['diff_json'], true) : []);

$keys = array_unique(array_merge(array_keys($json1 ?? []), array_keys($json2 ?? []), array_keys($json3 ?? [])));

/*
var_dump("variables other ");
var_dump($log_id);
var_dump($user_id);
var_dump($role_name);
var_dump($action_type);
var_dump($entity_type);
var_dump($occurred_at);
var_dump($entity_id);
*/

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Final Project	</title>
	<link rel = "stylesheet" href = "style.css">
	<style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
            background-image: url('images/background.png');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
        }
        h1 {
            text-align: center;
            width: 100%;
        }
        .content-box {
            width: 80%;
            max-width: 1400px;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            padding: 40px;
            text-align: center;
        }
        .event-select-form {
            margin-bottom: 30px;
        }
        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #c4a484;
            color: white;
        }
        .action-btn {
            padding: 8px 16px;
            margin: 5px;
            border-radius: 8px;
            border: none;
            background-color: #c4a484;
            color: white;
            cursor: pointer;
            font-size: 0.9em;
        }
        .action-btn:hover {
            background-color: #b39578;
        }
     
    </style>
</head>
<body>
	<header>
		<h1>Audit Log Info </h1>
	</header>
	<br><br>
<main>
	<table>	
	<form action = "exportLogs.php" method = "post" style="display:inline;">

		<tr>
			<th>Key</th>
			<th>Before</th>
			<th>After</th>
			<th>Difference</th>
		</tr>
			 <?php foreach ($keys as $key): ?>

		<tr>
            <td><?php echo $key; ?></td>
            <td><?php echo $json1[$key] ?? ''; ?></td>
            <td><?php echo $json2[$key] ?? ''; ?></td>
            <td><?php echo $json3[$key] ?? ''; ?></td>

				<input type="hidden" name = "log_id" value= "<?php echo $log_id; ?>">
				<input type="hidden" name = "user_id" value= "<?php echo $user_id; ?>">
				<input type="hidden" name = "role_name" value= "<?php echo $role_name; ?>">
				<input type="hidden" name = "action_type" value= "<?php echo $action_type; ?>">
				<input type="hidden" name = "entity_type" value= "<?php echo $entity_type; ?>">
				<input type="hidden" name = "occurred_at" value= "<?php echo $occurred_at; ?>">
				<input type="hidden" name = "entity_id" value= "<?php echo $entity_id; ?>">
        </tr>
		<?php endforeach; ?>

    <!---<button type="submit"  name="action" class="action-btn export-btn" value="export_csv" >Export CSV</button>-->
 	</form>
	</table>	
<br><br>	
		<p><a href="viewLog.php">Back to all Logs</a></p>
		<p><a href="index.php">Back to dashboard</a></p>
	</main>
</body>
</html>




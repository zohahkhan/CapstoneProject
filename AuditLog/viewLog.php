<?php
require_once __DIR__ . '/../include/db_connect.php';
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
// Admins only
if (!in_array($_SESSION['user']['role_id'], [4])) 
{
	require_once __DIR__ . '/../include/config.php';
	$error_page = BASE_URL.'/include/error.php';
    header("Location: $error_page");
    exit;
}

$user_id = isset($_POST['user_id']) && $_POST['user_id'] !== '' ? $_POST['user_id'] : null;
$action  = isset($_POST['action'])  && $_POST['action'] !== '' ? $_POST['action'] : null;
$role    = isset($_POST['role'])    && $_POST['role'] !== '' ? $_POST['role'] : null;

$stmt2 = $db->prepare('SELECT COUNT(user_id) FROM User');
$stmt2->execute();
$number_of_users = $stmt2->fetchColumn();
$stmt2->closeCursor();

$queryAuditLog = "
	SELECT a.* , r.role_name
	FROM AuditLog a
	LEFT JOIN Role r ON a.role_id = r.role_id
	WHERE 1=1 ";

$params = [];

// all the filters
if (!empty($_POST['user_id'])) {
    $queryAuditLog .= " AND a.user_id = ?";
    $params[] = $_POST['user_id'];
}
if (!empty($_POST['action'])) {
    $queryAuditLog .= " AND a.action = ?";
    $params[] = $_POST['action'];
}
if (!empty($_POST['role'])) {
    $queryAuditLog .= " AND r.role_name = ?";
    $params[] = $_POST['role'];
}
$sort = $_POST['date_sort'] ?? 'ASC';
if (!in_array($sort, ['ASC', 'DESC'])) {
    $sort = 'DESC';
}

$queryAuditLog .= " ORDER BY occurred_at $sort";

$stmt1 = $db->prepare($queryAuditLog);
$stmt1->execute($params);
$results = $stmt1->fetchAll(PDO::FETCH_ASSOC);
?>
	
<!DOCTYPE html>
<html>
<head>
    <title>Final project</title>
    <link rel="stylesheet" type="text/css" href="../style.css" />
	<style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
            background-image: url('../images/background.png');
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
		.back-link
        {
            display: inline-block;
            margin-bottom: 20px;
            color: #c4a484;
            text-decoration: none;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
			text-shadow: 0 1px 1px rgba(0,0,0,0.40);
        }
        .back-link:hover
        {
            color: #b39578;
        }
		.links 
		{
    		display: flex;
    		justify-content: space-between;
		}
    </style>
</head>

<body>
	<header>
		<h1>Audit Log</h1>
	</header>
<main>
    <h2>Review all Logs</h2>
	<br>

	<form method="post" action="exportLogs.php">
		<div style="display: flex; justify-content: center; margin-top: -35px;">
		<button type="submit" name="action" value="export_all_csv" >Export All Logs</button>
		</div>
	</form>
	<br><br>
	<a href="../index.php" class="back-link">&larr; Back to dashboard</a>

	<form method="POST" action="">
    <label>User ID:</label>
    <select name="user_id">
        <option value="">All</option>
        <?php for ($i = 1; $i <= $number_of_users ; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if(isset($_POST['user_id']) && $_POST['user_id']==$i) echo 'selected'; ?>>
                <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>

   <label>Action:</label>
    <select name="action">
        <option value="">All</option>
        <?php foreach(['create','read','update','delete'] as $act): ?>
            <option value="<?php echo $act; ?>" <?php if(isset($_POST['action']) && $_POST['action']==$act) echo 'selected'; ?>>
                <?php echo ucfirst($act); ?>
            </option>
        <?php endforeach; ?>
    </select>

   <label>Role:</label>
    <select name="role">
        <option value="">All</option>
        <?php foreach(['President','Department Head','Member','Admin'] as $r): ?>
            <option value="<?php echo $r; ?>" <?php if(isset($_POST['role']) && $_POST['role']==$r) echo 'selected'; ?>>
                <?php echo strtoupper($r); ?>
            </option>
        <?php endforeach; ?>
    </select>

   <label>Date:</label>
    <select name="date_sort">
        <?php foreach(['DESC'=>'Newest First','ASC'=>'Oldest First'] as $val=>$label): ?>
            <option value="<?php echo $val; ?>" <?php if($sort == $val) echo 'selected'; ?>>
                <?php echo $label; ?>
            </option>
        <?php endforeach; ?>
    </select>

  <button type="submit">Filter</button>
</form>
	
<?php if($results): ?>
<table border="1">
	<tr>
		<th>Log ID</th>
		<th>User ID</th>
		<th>Role</th>
		<th>Action</th>
		<th>Table</th>
		<th>Date</th>
		<th>Details</th>
	</tr>
	<?php foreach($results as $row): ?>
	<tr>
		<td><?php echo $row['log_id']; ?></td>
		<td><?php echo $row['user_id']; ?></td>
		<td><?php echo $row['role_name']; ?></td>
		<td><?php echo $row['action']; ?></td>
		<td><?php echo $row['entity_type']; ?></td>
		<td><?php echo date('M d, Y', strtotime($row['occurred_at'])); ?></td>
		<td>
			<form action = "auditLogInfo.php" method = "post">
				<input type="hidden" name = "log_id" value= "<?php echo $row['log_id']; ?>">
				
				<input type="hidden" name = "user_id" value= "<?php echo $row['user_id']; ?>">
				<input type="hidden" name = "role_name" value= "<?php echo $row['role_name']; ?>">
				<input type="hidden" name = "action_type" value= "<?php echo $row['action']; ?>">
				<input type="hidden" name = "entity_type" value= "<?php echo $row['entity_type']; ?>">
				<input type="hidden" name = "occurred_at" value= "<?php echo $row['occurred_at']; ?>">
				<input type="hidden" name = "entity_id" value= "<?php echo $row['entity_id']; ?>">
				<input type="submit" name="action" value="View">
			</form>		
		</td>
	</tr>
</form>
	
	<?php endforeach; ?>
</table>
	<?php else: ?>
		<p>No results found.</p>
	<?php endif; ?>

	<br><br>
	<a href="../index.php" class="back-link">&larr; Back to dashboard</a>
</main>
	
</body>
</html>

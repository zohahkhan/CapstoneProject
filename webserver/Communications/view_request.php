<?php
declare(strict_types=1);
require_once __DIR__ . "/../include/db_connect.php";
require_once __DIR__ . "/../include/auth.php";

//require_role(["President"]);
global $db;

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) exit("Missing id");

// Update status
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $newStatus = $_POST["msg_status"] ?? "Pending";
  $allowed = ["Pending","Reviewed","Finalized"];
  if (!in_array($newStatus, $allowed, true)) exit("Invalid status");

  $db->prepare("UPDATE VisitorRequest SET msg_status=:s WHERE request_id=:id")
     ->execute([":s"=>$newStatus, ":id"=>$id]);
}

// Load request
$stmt = $db->prepare("
  SELECT request_id, full_name, contact_email, visitor_msg, msg_status, created_at
  FROM VisitorRequest
  WHERE request_id=:id
");
$stmt->execute([":id"=>$id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) exit("Not found");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>View Request</title>
  <link rel="stylesheet" type="text/css" href="../style.css" />
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<p><a href="president_requests.php">← Back</a></p>

<h2>Request #<?= (int)$r["request_id"] ?></h2>
<p><b>Name:</b> <?= htmlspecialchars($r["full_name"]) ?></p>
<p><b>Email:</b> <?= htmlspecialchars($r["contact_email"]) ?></p>
<p><b>Submitted:</b> <?= htmlspecialchars($r["created_at"]) ?></p>
<p><b>Status:</b> <?= htmlspecialchars($r["msg_status"]) ?></p>

<h3>Message</h3>
<pre style="white-space:pre-wrap; border:1px solid #ccc; padding:10px;">
<?= htmlspecialchars($r["visitor_msg"]) ?>
</pre>

<form method="post">
  <label>Update Status:</label>
  <select name="msg_status">
    <?php foreach (["Pending","Reviewed","Finalized"] as $s): ?>
      <option value="<?= $s ?>" <?= $r["msg_status"]===$s ? "selected" : "" ?>><?= $s ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit">Save</button>
</form>
</body>
</html>

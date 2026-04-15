<?php
declare(strict_types=1);

require_once __DIR__ . "/include/db_connect.php";
require_once __DIR__ . "/include/auth.php";

require_role(["President"]);

// Notification count
$pendingCount = (int)$db->query("
  SELECT COUNT(*) FROM `suggestion` WHERE msg_status='Pending'
")->fetchColumn();

$rows = $db->query("
  SELECT suggestion_id, full_name, contact_email, created_at
  FROM `suggestion`
  WHERE msg_status='Pending'
  ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Join Requests</title>
</head>
<body>
  <p>
  <a href="index.php">← Back to Dashboard</a>
</p>

<h1>Join Requests</h1>
<p>🔔 Pending: <?= $pendingCount ?></p>

<?php if (!$rows): ?>
  <p>No pending requests.</p>
<?php else: ?>
  <ul>
    <?php foreach ($rows as $r): ?>
      <li>
        <?= htmlspecialchars($r["created_at"]) ?> —
        <?= htmlspecialchars($r["full_name"]) ?> —
        <?= htmlspecialchars($r["contact_email"]) ?>
        [<a href="view_request.php?id=<?= (int)$r["suggestion_id"] ?>">View</a>]
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
</body>
</html>

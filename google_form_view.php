<?php
declare(strict_types=1);

require_once __DIR__ . "/../include/db_connect.php";
require_once __DIR__ . "/../include/auth.php";

// Only internal users
require_role(["President", "Department Head"]);

global $db;

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit("Missing or invalid id.");
}

$stmt = $db->prepare("
  SELECT
    fr.response_id,
    fr.created_at,
    fr.form_status,
    fr.form_response,
    ft.temp_title,
    u.first_name,
    u.last_name,
    u.user_email
  FROM FormResponse fr
  JOIN FormTemplate ft ON ft.template_id = fr.template_id
  JOIN User u ON u.user_id = fr.user_id
  WHERE fr.response_id = :id
  LIMIT 1
");
$stmt->execute([":id" => $id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$r) {
    http_response_code(404);
    exit("Submission not found.");
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

$decoded = json_decode($r["form_response"] ?? "{}", true) ?: [];
$named = $decoded["namedValues"] ?? [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Submission #<?= (int)$r["response_id"] ?></title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .wrap{max-width:900px;margin:28px auto;padding:0 14px;font-family:Arial,sans-serif;}
    .card{border:1px solid #ddd;border-radius:10px;padding:14px;margin-top:12px;background:#fafafa;}
    .q{font-weight:700;margin-top:10px;}
    .a{margin-left:10px;}
  </style>
</head>
<body>
<div class="wrap">
  <h1><?= h($r["temp_title"]) ?></h1>
  <p>
    <b>ID:</b> <?= (int)$r["response_id"] ?> |
    <b>Submitted:</b> <?= h((string)$r["created_at"]) ?> |
    <b>Status:</b> <?= h((string)$r["form_status"]) ?><br>
    <b>User:</b> <?= h($r["first_name"]." ".$r["last_name"]) ?> (<?= h($r["user_email"]) ?>)
  </p>

  <div class="card">
    <?php if (empty($named)): ?>
      <em>No namedValues found in stored JSON.</em>
      <pre style="white-space:pre-wrap;"><?= h(json_encode($decoded, JSON_PRETTY_PRINT) ?: "") ?></pre>
    <?php else: ?>
      <?php foreach ($named as $question => $answers): ?>
        <div class="q"><?= h((string)$question) ?></div>
        <div class="a">
          <?= h(is_array($answers) ? implode(", ", array_map("strval", $answers)) : (string)$answers) ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <p style="margin-top:14px;">
    <a href="google_form_report.php">← Back to report</a>
  </p>
</div>
</body>
</html>
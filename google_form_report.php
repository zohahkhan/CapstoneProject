<?php
declare(strict_types=1);

require_once __DIR__ . "/../include/db_connect.php";
require_once __DIR__ . "/../include/auth.php";

// Only internal users (example: President + Dept Head)
require_role(["President", "Department Head"]);

global $db;

$stmt = $db->query("
  SELECT
    submission_id,
    form_title,
    form_id,
    submitted_at,
    response_json,
    created_at
  FROM GoogleFormSubmission
  ORDER BY submission_id DESC
  LIMIT 200
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Google Form Report</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 28px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border-bottom: 1px solid #ddd; padding: 10px; vertical-align: top; text-align: left; }
    .card { border: 1px solid #ddd; padding: 12px; border-radius: 10px; background:#fafafa; }
    .q { font-weight: 700; margin-top: 8px; }
    .a { margin-left: 10px; }
    details pre { white-space: pre-wrap; margin: 8px 0 0; }
    .muted { color: #666; font-size: 12px; }
  </style>
</head>
<body>

<h1>Google Form Submissions</h1>

<?php if (!$rows): ?>
  <p>No submissions found.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Form</th>
        <th>Submitted</th>
        <th>Answers</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <?php
        $raw = $r["response_json"] ?? "{}";
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) $decoded = [];

        // Apps Script sends namedValues
        $named = $decoded["namedValues"] ?? [];

        // submitted_at is best (comes from payload), fallback to created_at
        $submitted = $r["submitted_at"] ?? $r["created_at"] ?? "";
      ?>
      <tr>
        <td><?= (int)$r["submission_id"] ?></td>

        <td>
          <?php

            $formTitle = (string)($r["form_title"] ?? "Google Form");
            $formId = (string)($r["form_id"] ?? "");
          ?>
          <?= h($formTitle) ?>
          <?php if ($formId !== ""): ?>
            <div class="muted">Form ID: <?= h($formId) ?></div>
          <?php endif; ?>
        </td>

        <td><?= h((string)$submitted) ?></td>

        <td>
          <div class="card">
            <?php if (!is_array($named) || empty($named)): ?>
              <em>No namedValues found.</em>
              <details>
                <summary>Show raw JSON</summary>
                <pre><?= h($raw) ?></pre>
              </details>
            <?php else: ?>
              <?php foreach ($named as $question => $answers): ?>
                <div class="q"><?= h((string)$question) ?></div>
                <div class="a">
                  <?php
                    if (is_array($answers)) {
                      echo h(implode(", ", array_map("strval", $answers)));
                    } else {
                      echo h((string)$answers);
                    }
                  ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

</body>
</html>
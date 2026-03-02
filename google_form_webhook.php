<?php
declare(strict_types=1);

require_once __DIR__ . "/../include/db_connect.php";


global $db;

$SHARED_SECRET = "lajnaabc2026";

// Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit("Method Not Allowed");
}

// Verify secret
$secret = $_SERVER["HTTP_X_WEBHOOK_SECRET"] ?? "";
if (!hash_equals($SHARED_SECRET, $secret)) {
  http_response_code(401);
  exit("Unauthorized");
}

// Read raw JSON body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
  http_response_code(400);
  exit("Invalid JSON");
}

$form_title = trim((string)($data["form_title"] ?? $data["source_form"] ?? "Google Form"));
$response_json = json_encode($data, JSON_UNESCAPED_UNICODE);

if ($response_json === false) {
  http_response_code(400);
  exit("JSON encode failed");
}

// Insert into DB
$form_title = trim((string)($data["form_title"] ?? $data["source_form"] ?? "Google Form"));
$form_id = trim((string)($data["form_id"] ?? ""));
$submitted_iso = (string)($data["submitted_at"] ?? "");
$submitted_at = date("Y-m-d H:i:s", strtotime($submitted_iso ?: "now"));

$stmt = $db->prepare("
  INSERT INTO GoogleFormSubmission (form_title, form_id, submitted_at, response_json)
  VALUES (:form_title, :form_id, :submitted_at, :response_json)
");

$stmt->execute([
  ":form_title"    => $form_title,
  ":form_id"       => ($form_id !== "" ? $form_id : null),
  ":submitted_at"  => $submitted_at,
  ":response_json" => $response_json
]);

http_response_code(200);
echo "OK";
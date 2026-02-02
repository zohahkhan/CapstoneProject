<?php
// auth.php
require_once "db_connect.php";

$token = $_COOKIE["session"] ?? "";
if ($token === "") {
  header("Location: login.html");
  exit;
}

$stmt = $pdo->prepare("
  SELECT s.user_id
  FROM Session s
  WHERE s.session_id = ?
    AND s.revoked_at IS NULL
    AND s.expires_at > NOW()
");
$stmt->execute([$token]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  setcookie("session", "", time() - 3600, "/");
  header("Location: login.html");
  exit;
}

// Optionally refresh session last_seen
$pdo->prepare("UPDATE Session SET last_seen_at = NOW() WHERE session_id = ?")->execute([$token]);

$user_id = (int)$row["user_id"];

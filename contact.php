<?php
declare(strict_types=1);
require_once './include/db_connect.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

$first_name = "";
$last_name = "";
$email = "";
$statement = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST["first_name"] ?? "");
    $last_name  = trim($_POST["last_name"] ?? "");
    $email      = trim($_POST["email"] ?? "");
    $statement  = trim($_POST["statement"] ?? "");

    if ($first_name === "" || mb_strlen($first_name) > 60) {
        $errors[] = "First name is required (max 60 characters).";
    }
    if ($last_name === "" || mb_strlen($last_name) > 60) {
        $errors[] = "Last name is required (max 60 characters).";
    }
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if ($statement === "" || mb_strlen($statement) > 2000) {
        $errors[] = "Personal statement is required (max 2000 characters).";
    }

    if (empty($errors)) {
        $full_name = trim($first_name . " " . $last_name);
        $visitor_session_id = session_id();

        $stmt = $db->prepare("
            INSERT INTO Suggestion
              (full_name, contact_email, visitor_msg, msg_status, session_id, created_at)
            VALUES
              (:full_name, :email, :msg, 'Pending', :session_id, NOW())
        ");

        $stmt->execute([
            ":full_name"  => $full_name,
            ":email"      => $email,
            ":msg"        => $statement,
            ":session_id" => $visitor_session_id
        ]);

$_SESSION['submitted'] = true;
header("Location: thank_you.php");
exit;

    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Join Us</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Join Us</h1>

<form class="login-form" method="post" action="contact.php">
  <div class="form-group">
    <label>First Name</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
  </div>

  <div class="form-group">
    <label>Last Name</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
  </div>

  <div class="form-group">
    <label>Why do you want to join us?</label>
    <textarea name="statement" required><?= htmlspecialchars($statement) ?></textarea>
  </div>

  <button type="submit">Submit</button>
</form>

<br><br>
<a href="index.php">Back to home</a>
</body>
</html>


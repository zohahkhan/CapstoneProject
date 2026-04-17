<?php
require_once __DIR__ . '/loginpages/include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: loginpages/login.php");
    exit;
}

$roleId = (int)$_SESSION['user']['role_id'];
if ($roleId !== 1 && $roleId !== 2) {
    header("Location: loginpages/index.php");
    exit;
}

$userId = (int)$_SESSION['user']['user_id'];
$message = '';
$messageType = '';

$prefillTitle = $_GET['copy_title'] ?? '';
$prefillBody  = $_GET['copy_body']  ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title  = trim($_POST['title']  ?? '');
    $body   = trim($_POST['body']   ?? '');
    $expiry = trim($_POST['expiry'] ?? '');

    if ($title === '' || $body === '' || $expiry === '') {
        $message = 'All fields are required.';
        $messageType = 'error';
    } elseif (mb_strlen($title) > 50) {
        $message = 'Title must be 50 characters or fewer.';
        $messageType = 'error';
    } elseif (mb_strlen($body) > 2000) {
        $message = 'Body must be 2,000 characters or fewer.';
        $messageType = 'error';
    } else {
        $stmt = $db->prepare("
            INSERT INTO Announcement (user_id, announce_title, announce_body, announce_expiry, visibility_scope, allow_opt_out, archived, created_at, updated_at)
            VALUES (:user_id, :title, :body, :expiry, 'Everyone', 0, 0, NOW(), NOW())
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':title'   => $title,
            ':body'    => $body,
            ':expiry'  => $expiry,
        ]);
        header("Location: manageAnnouncements.php?success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Announcement</title>
    <link rel="stylesheet" type="text/css" href="loginpages/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .announce-wrapper {
            width: 95%;
            max-width: 700px;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .announce-card {
            background-color: #faf5f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
        }

        .announce-card h2 {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.95em;
            font-weight: 600;
            margin-bottom: 6px;
            color: #3b2f2f;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e6d5c3;
            background-color: #fdfaf7;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9em;
            color: #3b2f2f;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #c4a484;
        }

        .form-group textarea {
            height: 160px;
            resize: vertical;
        }

        .char-count {
            font-size: 0.75em;
            color: #8b6f47;
            text-align: right;
            margin-top: 4px;
        }

        .char-count.over {
            color: #b02a37;
            font-weight: 600;
        }

        .submit-btn {
            width: 100%;
            margin-top: 8px;
            padding: 14px;
            border-radius: 10px;
            border: none;
            background-color: #c4a484;
            color: white;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        .submit-btn:hover {
            background-color: #8b6f47;
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #8b6f47;
            text-decoration: none;
            font-size: 0.9em;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="announce-wrapper">
        <div class="announce-card">
            <h2>Create Announcement</h2>

            <?php if ($message !== ''): ?>
                <div class="message <?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="title">Title <span style="color:#8b6f47; font-weight:400;">(max 50 characters)</span></label>
                    <input type="text" id="title" name="title" maxlength="50" value="<?= htmlspecialchars($_POST['title'] ?? $prefillTitle) ?>" oninput="updateCount('title', 'titleCount', 50)">
                    <div class="char-count" id="titleCount">0 / 50</div>
                </div>

                <div class="form-group">
                    <label for="body">Body <span style="color:#8b6f47; font-weight:400;">(max 2,000 characters)</span></label>
                    <textarea id="body" name="body" maxlength="2000" oninput="updateCount('body', 'bodyCount', 2000)"><?= htmlspecialchars($_POST['body'] ?? $prefillBody) ?></textarea>
                    <div class="char-count" id="bodyCount">0 / 2,000</div>
                </div>

                <div class="form-group">
                    <label for="expiry">Expiry Date &amp; Time</label>
                    <input type="datetime-local" id="expiry" name="expiry" value="<?= htmlspecialchars($_POST['expiry'] ?? '') ?>">
                </div>

                <button type="submit" class="submit-btn">Publish Announcement</button>
            </form>

            <a href="manageAnnouncements.php" class="back-link">← Back to Manage Announcements</a>
        </div>
    </div>

    <script>
        function updateCount(fieldId, countId, max) {
            var len = document.getElementById(fieldId).value.length;
            var el = document.getElementById(countId);
            var formatted = max === 2000 ? '2,000' : max.toString();
            el.textContent = len + ' / ' + formatted;
            if (len >= max) {
                el.classList.add('over');
            } else {
                el.classList.remove('over');
            }
        }
        updateCount('title', 'titleCount', 50);
        updateCount('body', 'bodyCount', 2000);
    </script>
</body>
</html>
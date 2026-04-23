<?php
require_once __DIR__ . '/../include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$roleId = (int)$_SESSION['user']['role_id'];
if ($roleId !== 1 && $roleId !== 2) {
    header("Location: ../index.php");
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['announcement_id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        $stmt = $db->prepare("DELETE FROM Announcement WHERE announcement_id = :id");
        $stmt->execute([':id' => $id]);
        $message = 'Announcement deleted.';
        $messageType = 'success';

    } elseif ($action === 'close' && $id > 0) {
        $stmt = $db->prepare("UPDATE Announcement SET announce_expiry = NOW(), updated_at = NOW() WHERE announcement_id = :id");
        $stmt->execute([':id' => $id]);
        $message = 'Announcement closed.';
        $messageType = 'success';

    } elseif ($action === 'reopen' && $id > 0) {
        $expiry = trim($_POST['new_expiry'] ?? '');
        if ($expiry === '') {
            $message = 'Please provide a new expiry date to reopen.';
            $messageType = 'error';
        } else {
            $stmt = $db->prepare("UPDATE Announcement SET announce_expiry = :expiry, archived = 0, updated_at = NOW() WHERE announcement_id = :id");
            $stmt->execute([':expiry' => $expiry, ':id' => $id]);
            $message = 'Announcement reopened.';
            $messageType = 'success';
        }

    } elseif ($action === 'edit' && $id > 0) {
        $title  = trim($_POST['edit_title']  ?? '');
        $body   = trim($_POST['edit_body']   ?? '');
        $expiry = trim($_POST['edit_expiry'] ?? '');
        if ($title === '' || $body === '' || $expiry === '') {
            $message = 'All fields are required to save.';
            $messageType = 'error';
        } else {
            $stmt = $db->prepare("UPDATE Announcement SET announce_title = :title, announce_body = :body, announce_expiry = :expiry, updated_at = NOW() WHERE announcement_id = :id");
            $stmt->execute([':title' => $title, ':body' => $body, ':expiry' => $expiry, ':id' => $id]);
            $message = 'Announcement updated.';
            $messageType = 'success';
        }
    }
}

$stmtActive = $db->prepare("
    SELECT a.announcement_id, a.announce_title, a.announce_body, a.announce_expiry, a.created_at,
           u.first_name, u.last_name
    FROM Announcement a
    JOIN User u ON a.created_by = u.user_id
    WHERE a.announce_expiry > NOW() AND a.archived = 0
    ORDER BY a.created_at DESC
");
$stmtActive->execute();
$activeList = $stmtActive->fetchAll(PDO::FETCH_ASSOC);

$stmtExpired = $db->prepare("
    SELECT a.announcement_id, a.announce_title, a.announce_body, a.announce_expiry, a.created_at,
           u.first_name, u.last_name
    FROM Announcement a
    JOIN User u ON a.created_by = u.user_id
    WHERE a.announce_expiry <= NOW() OR a.archived = 1
    ORDER BY a.announce_expiry DESC
");
$stmtExpired->execute();
$expiredList = $stmtExpired->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href= ../images/logo.png>
    <meta charset="UTF-8">
    <title>Manage Announcements</title>
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .manage-wrapper {
            width: 95%;
            max-width: 1100px;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .manage-card {
            background-color: #faf5f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
        }

        .manage-card h2 {
            margin-top: 0;
            text-align: center;
        }

        .section-title {
            font-size: 1em;
            font-weight: 600;
            color: #3b2f2f;
            margin: 24px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e6d5c3;
        }

        .ann-item {
            background-color: #fdfaf7;
            border: 1px solid #e6d5c3;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 14px;
            text-align: left;
        }

        .ann-item .ann-title {
            font-size: 1em;
            font-weight: 600;
            color: #3b2f2f;
            margin-bottom: 6px;
        }

        .ann-item .ann-body {
            font-size: 0.88em;
            color: #3b2f2f;
            line-height: 1.5;
            margin-bottom: 10px;
            white-space: pre-wrap;
        }

        .ann-item .ann-meta {
            font-size: 0.75em;
            color: #8b6f47;
            margin-bottom: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72em;
            font-weight: 600;
            margin-left: 8px;
        }

        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.expired {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .action-row form {
            margin: 0;
            padding: 0;
            display: inline-flex;
        }

        .action-btn {
            padding: 6px 14px;
            border-radius: 8px;
            border: none;
            font-family: 'Poppins', sans-serif;
            font-size: 0.8em;
            font-weight: 600;
            cursor: pointer;
            color: white;
            background-color: #c4a484;
            line-height: 1.4;
            display: inline-block;
        }

        .action-btn:hover {
            background-color: #8b6f47;
        }

        .btn-delete {
            background-color: #b02a37;
        }

        .btn-delete:hover {
            background-color: #7d1e27;
        }

        .edit-form {
            display: none;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #e6d5c3;
        }

        .edit-form.open {
            display: block;
        }

        .edit-form .form-group {
            margin-bottom: 14px;
            text-align: left;
        }

        .edit-form label {
            display: block;
            font-size: 0.85em;
            font-weight: 600;
            margin-bottom: 4px;
            color: #3b2f2f;
        }

        .edit-form input,
        .edit-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e6d5c3;
            background-color: #faf5f0;
            font-family: 'Poppins', sans-serif;
            font-size: 0.85em;
            color: #3b2f2f;
            box-sizing: border-box;
        }

        .edit-form textarea {
            height: 100px;
            resize: vertical;
        }

        .reopen-form {
            display: none;
            margin-top: 10px;
        }

        .reopen-form.open {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .reopen-form input {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #e6d5c3;
            font-family: 'Poppins', sans-serif;
            font-size: 0.82em;
            color: #3b2f2f;
            background-color: #fdfaf7;
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .empty-msg {
            text-align: center;
            color: #888;
            padding: 20px 0;
            font-size: 0.9em;
        }

        .create-btn-wrap {
            text-align: center;
            margin-bottom: 24px;
        }

        .create-btn {
            display: inline-block;
            padding: 12px 28px;
            background-color: #c4a484;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95em;
        }

        .create-btn:hover {
            background-color: #8b6f47;
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
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
    <div class="manage-wrapper">
        <div class="manage-card">
            <h2>Manage Announcements</h2>

            <?php if ($message !== ''): ?>
                <div class="message <?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="create-btn-wrap">
                <a href="createAnnouncement.php" class="create-btn">+ Create New Announcement</a>
            </div>

            <div class="section-title">Active Announcements</div>

            <?php if (empty($activeList)): ?>
                <p class="empty-msg">No active announcements.</p>
            <?php else: ?>
                <?php foreach ($activeList as $a): ?>
                <div class="ann-item">
                    <div class="ann-title">
                        <?= htmlspecialchars($a['announce_title']) ?>
                        <span class="status-badge active">Active</span>
                    </div>
                    <div class="ann-body"><?= htmlspecialchars($a['announce_body']) ?></div>
                    <div class="ann-meta">
                        Posted by <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                        &nbsp;|&nbsp; <?= date("M j, Y g:i A", strtotime($a['announce_deliverytime'] ?? $a['created_at'])) ?>
                        &nbsp;|&nbsp; Expires: <?= date("M j, Y g:i A", strtotime($a['announce_expiry'])) ?>
                    </div>
                    <div class="action-row">
                        <form method="POST">
                            <input type="hidden" name="action" value="toggle_edit">
                            <button type="button" class="action-btn" onclick="toggleEdit(<?= $a['announcement_id'] ?>)">Edit</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="action" value="close">
                            <input type="hidden" name="announcement_id" value="<?= $a['announcement_id'] ?>">
                            <button type="submit" class="action-btn" onclick="return confirm('Close this announcement now?')">Close</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="announcement_id" value="<?= $a['announcement_id'] ?>">
                            <button type="submit" class="action-btn btn-delete" onclick="return confirm('Delete this announcement permanently?')">Delete</button>
                        </form>
                    </div>

                    <div class="edit-form" id="edit-<?= $a['announcement_id'] ?>">
                        <form method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="announcement_id" value="<?= $a['announcement_id'] ?>">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="edit_title" maxlength="50" value="<?= htmlspecialchars($a['announce_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Body</label>
                                <textarea name="edit_body" maxlength="2000"><?= htmlspecialchars($a['announce_body']) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Expiry</label>
                                <input type="datetime-local" name="edit_expiry" value="<?= date('Y-m-d\TH:i', strtotime($a['announce_expiry'])) ?>">
                            </div>
                            <button type="submit" class="action-btn">Save Changes</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="section-title">Expired Announcements</div>

            <?php if (empty($expiredList)): ?>
                <p class="empty-msg">No expired announcements.</p>
            <?php else: ?>
                <?php foreach ($expiredList as $a): ?>
                <div class="ann-item">
                    <div class="ann-title">
                        <?= htmlspecialchars($a['announce_title']) ?>
                        <span class="status-badge expired">Expired</span>
                    </div>
                    <div class="ann-body"><?= htmlspecialchars($a['announce_body']) ?></div>
                    <div class="ann-meta">
                        Posted by <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                        &nbsp;|&nbsp; <?= date("M j, Y g:i A", strtotime($a['announce_deliverytime'] ?? $a['created_at'])) ?>
                        &nbsp;|&nbsp; Expired: <?= date("M j, Y g:i A", strtotime($a['announce_expiry'])) ?>
                    </div>
                    <div class="action-row">
                        <form method="POST">
                            <input type="hidden" name="action" value="reopen">
                            <button type="button" class="action-btn" onclick="toggleReopen(<?= $a['announcement_id'] ?>)">Reopen</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="announcement_id" value="<?= $a['announcement_id'] ?>">
                            <button type="submit" class="action-btn btn-delete" onclick="return confirm('Delete this announcement permanently?')">Delete</button>
                        </form>
                    </div>

                    <div class="reopen-form" id="reopen-<?= $a['announcement_id'] ?>">
                        <form method="POST" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                            <input type="hidden" name="action" value="reopen">
                            <input type="hidden" name="announcement_id" value="<?= $a['announcement_id'] ?>">
                            <input type="datetime-local" name="new_expiry">
                            <button type="submit" class="action-btn">Confirm Reopen</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <a href="../index.php" class="back-link">← Back to Home</a>
        </div>
    </div>

    <script>
        function toggleEdit(id) {
            var el = document.getElementById('edit-' + id);
            el.classList.toggle('open');
        }
        function toggleReopen(id) {
            var el = document.getElementById('reopen-' + id);
            el.classList.toggle('open');
        }
    </script>
</body>
</html>

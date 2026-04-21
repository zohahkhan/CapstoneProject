<?php
require_once __DIR__ . '/../include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$stmt = $db->prepare("
    SELECT a.announcement_id, a.announce_title, a.announce_body, a.announce_expiry, a.created_at,
           u.first_name, u.last_name
    FROM Announcement a
    JOIN User u ON a.user_id = u.user_id
    WHERE a.announce_expiry > NOW() AND a.archived = 0
    ORDER BY a.created_at DESC
");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Announcements</title>
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .announce-wrapper {
            width: 95%;
            max-width: 900px;
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

        .announcement-item {
            background-color: #fdfaf7;
            border: 1px solid #e6d5c3;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 16px;
            text-align: left;
        }

        .announcement-item .ann-title {
            font-size: 1.05em;
            font-weight: 600;
            color: #3b2f2f;
            margin-bottom: 8px;
        }

        .announcement-item .ann-body {
            font-size: 0.9em;
            color: #3b2f2f;
            line-height: 1.6;
            margin-bottom: 12px;
            white-space: pre-wrap;
        }

        .announcement-item .ann-meta {
            font-size: 0.78em;
            color: #8b6f47;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            margin-left: 8px;
        }

        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }

        .empty-msg {
            text-align: center;
            color: #888;
            padding: 40px 0;
            font-size: 0.95em;
        }

        .back-link {
            display: inline-block;
            margin-top: 24px;
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
            <h2>Announcements</h2>

            <?php if (empty($announcements)): ?>
                <p class="empty-msg">No active announcements at this time.</p>
            <?php else: ?>
                <?php foreach ($announcements as $a): ?>
                <div class="announcement-item">
                    <div class="ann-title">
                        <?= htmlspecialchars($a['announce_title']) ?>
                        <span class="status-badge active">Active</span>
                    </div>
                    <div class="ann-body"><?= htmlspecialchars($a['announce_body']) ?></div>
                    <div class="ann-meta">
                        Posted by <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                        &nbsp;|&nbsp; <?= date("M j, Y g:i A", strtotime($a['created_at'])) ?>
                        &nbsp;|&nbsp; Expires: <?= date("M j, Y g:i A", strtotime($a['announce_expiry'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <a href="../index.php" class="back-link">← Back to Home</a>
        </div>
    </div>
</body>
</html>

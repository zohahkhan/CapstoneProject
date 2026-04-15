<?php
session_start();
require_once __DIR__ . '/loginpages/include/db_connect.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: loginpages/login.php");
    exit;
}

$userId = (int)$_SESSION['user']['user_id'];
$roleId = (int)$_SESSION['user']['role_id'];

if ($roleId !== 1 && $roleId !== 2) {
    header("Location: loginpages/index.php");
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'], $_POST['suggestion_id'])) {
        $suggestionId = (int)$_POST['suggestion_id'];
        $action = $_POST['action'];

        if ($action === 'resolve') {
            $stmt = $db->prepare("UPDATE MemberSuggestion SET status = 'Resolved', resolved_by = :resolved_by WHERE suggestion_id = :id");
            $stmt->execute([':resolved_by' => $userId, ':id' => $suggestionId]);
            $message = "Suggestion marked as resolved.";
            $messageType = 'success';
        } elseif ($action === 'reopen') {
            $stmt = $db->prepare("UPDATE MemberSuggestion SET status = 'Pending', resolved_by = NULL WHERE suggestion_id = :id");
            $stmt->execute([':id' => $suggestionId]);
            $message = "Suggestion reopened.";
            $messageType = 'success';
        } elseif ($action === 'approve' && $roleId === 1) {
            $stmt = $db->prepare("UPDATE MemberSuggestion SET status = 'Reviewed', resolved_by = :resolved_by WHERE suggestion_id = :id");
            $stmt->execute([':resolved_by' => $userId, ':id' => $suggestionId]);
            $message = "Suggestion approved.";
            $messageType = 'success';
        }
    }

    if (isset($_POST['delete_selected']) && !empty($_POST['selected_ids'])) {
        $selectedIds = array_map('intval', $_POST['selected_ids']);
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $stmt = $db->prepare("DELETE FROM MemberSuggestion WHERE suggestion_id IN ($placeholders)");
        $stmt->execute($selectedIds);
        $message = count($selectedIds) . " suggestion(s) deleted.";
        $messageType = 'success';
    }
}

$stmt = $db->prepare("
    SELECT 
        ms.suggestion_id,
        ms.suggestion_text,
        ms.attachment_path,
        ms.status,
        ms.created_at,
        ms.resolved_by,
        u.first_name,
        u.last_name,
        u.user_id AS submitter_id,
        GROUP_CONCAT(DISTINCT r.role_name ORDER BY r.role_id SEPARATOR ', ') AS roles,
        ru.first_name AS resolver_first,
        ru.last_name AS resolver_last,
        GROUP_CONCAT(DISTINCT rr.role_name ORDER BY rr.role_id SEPARATOR ', ') AS resolver_roles
    FROM MemberSuggestion ms
    JOIN User u ON ms.user_id = u.user_id
    JOIN UserRole ur ON u.user_id = ur.user_id
    JOIN Role r ON ur.role_id = r.role_id
    LEFT JOIN User ru ON ms.resolved_by = ru.user_id
    LEFT JOIN UserRole rur ON ru.user_id = rur.user_id
    LEFT JOIN Role rr ON rur.role_id = rr.role_id
    GROUP BY ms.suggestion_id
    ORDER BY ms.created_at DESC
");
$stmt->execute();
$suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Suggestions</title>
    <link rel="stylesheet" type="text/css" href="loginpages/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .review-wrapper {
            width: 95%;
            max-width: 1400px;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .review-card {
            background-color: #faf5f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .review-card h2 {
            color: #3b2f2f;
            margin-bottom: 24px;
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95em;
        }

        .success {
            background: #e6ffed;
            color: #1e7e34;
            border: 1px solid #b7ebc6;
        }

        .error {
            background: #ffe6e6;
            color: #b02a37;
            border: 1px solid #f5c2c7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }

        th {
            background-color: #c4a484;
            color: white;
            padding: 12px 14px;
            text-align: left;
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid #e6d5c3;
            vertical-align: top;
            color: #3b2f2f;
        }

        tr:hover td {
            background-color: #fdfaf7;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-Pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-Reviewed {
            background: #cce5ff;
            color: #004085;
        }

        .status-Resolved {
            background: #d4edda;
            color: #155724;
        }

        .resolver-info {
            font-size: 0.78rem;
            color: #6b5437;
            margin-top: 4px;
            font-style: italic;
        }

        .action-btn {
            padding: 6px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.82rem;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            font-weight: 600;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .btn-resolve {
            background-color: #8b6f47;
            color: white;
        }

        .btn-resolve:hover {
            background-color: #6b5437;
        }

        .btn-approve {
            background-color: #c4a484;
            color: white;
        }

        .btn-approve:hover {
            background-color: #b39578;
        }

        .btn-reopen {
            background-color: #6c757d;
            color: white;
        }

        .btn-reopen:hover {
            background-color: #545b62;
        }

        .btn-delete {
            background-color: #b02a37;
            color: white;
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .btn-delete:hover {
            background-color: #8c1f2a;
        }

        .btn-delete:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .attachment-link {
            color: #8b6f47;
            font-size: 0.85rem;
            text-decoration: none;
        }

        .attachment-link:hover {
            text-decoration: underline;
        }

        .no-attach {
            color: #aaa;
            font-size: 0.85rem;
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
            font-size: 0.95em;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="review-wrapper">
        <div class="review-card">
            <h2>Review Suggestions &amp; Feedback</h2>

            <?php if ($message !== ''): ?>
                <div class="message <?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($suggestions)): ?>
                <p class="empty-msg">No suggestions have been submitted yet.</p>
            <?php else: ?>

            <form method="POST">
                <button type="submit" name="delete_selected" class="btn-delete" id="deleteBtn" disabled
                    onclick="return confirm('Are you sure you want to delete the selected suggestions?');">
                    Delete Selected
                </button>

                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"> Select All</th>
                            <th>#</th>
                            <th>Submitted By</th>
                            <th>Role(s)</th>
                            <th>Suggestion</th>
                            <th>Attachment</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($suggestions as $s): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_ids[]" value="<?= $s['suggestion_id'] ?>" class="row-checkbox">
                            </td>
                            <td><?= $s['suggestion_id'] ?></td>
                            <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td><?= htmlspecialchars($s['roles']) ?></td>
                            <td><?= htmlspecialchars($s['suggestion_text']) ?></td>
                            <td>
                                <?php if ($s['attachment_path']): ?>
                                    <a class="attachment-link" href="<?= htmlspecialchars($s['attachment_path']) ?>" target="_blank">View File</a>
                                <?php else: ?>
                                    <span class="no-attach">None</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y g:i A', strtotime($s['created_at'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($s['status']) ?>">
                                    <?= htmlspecialchars($s['status']) ?>
                                </span>
                                <?php if ($s['resolved_by'] && $s['resolver_first']): ?>
                                    <?php
                                        $resolverRoles = $s['resolver_roles'] ?? '';
                                        if (strpos($resolverRoles, 'President') !== false) {
                                            $resolverTitle = 'President';
                                        } elseif (strpos($resolverRoles, 'Department Head') !== false) {
                                            $resolverTitle = 'Department Head';
                                        } else {
                                            $resolverTitle = 'Staff';
                                        }
                                    ?>
                                    <div class="resolver-info">
                                        by <?= htmlspecialchars($resolverTitle) ?>: <?= htmlspecialchars($s['resolver_first'] . ' ' . $s['resolver_last']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($s['status'] !== 'Resolved'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="suggestion_id" value="<?= $s['suggestion_id'] ?>">
                                        <input type="hidden" name="action" value="resolve">
                                        <button type="submit" class="action-btn btn-resolve">Mark Resolved</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($s['status'] === 'Resolved' || $s['status'] === 'Reviewed'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="suggestion_id" value="<?= $s['suggestion_id'] ?>">
                                        <input type="hidden" name="action" value="reopen">
                                        <button type="submit" class="action-btn btn-reopen">Reopen</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($roleId === 1 && $s['status'] === 'Pending'): ?>
                                    <?php $isDeptHead = strpos($s['roles'], 'Department Head') !== false; ?>
                                    <?php if ($isDeptHead): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="suggestion_id" value="<?= $s['suggestion_id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="action-btn btn-approve">Approve</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </form>

            <?php endif; ?>

            <a class="back-link" href="loginpages/index.php">← Back to Home</a>
        </div>
    </div>

    <script>
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const deleteBtn = document.getElementById('deleteBtn');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            deleteBtn.disabled = !this.checked;
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const anyChecked = Array.from(checkboxes).some(c => c.checked);
                deleteBtn.disabled = !anyChecked;
                selectAll.checked = Array.from(checkboxes).every(c => c.checked);
            });
        });
    </script>
</body>
</html>

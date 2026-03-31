<?php
session_start();
require_once __DIR__ . '/include/db_connect.php';

// Check login
if (!isset($_SESSION['user']['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)($_SESSION['user']['user_id'] ?? 0);
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suggestionText = trim($_POST['suggestion_text'] ?? '');
    $attachmentPath = null;

    if ($userId <= 0) {
        $errorMessage = "User session is missing.";
    } elseif ($suggestionText === '') {
        $errorMessage = "Please enter your suggestion or feedback.";
    } else {
        // Handle optional file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadDir = __DIR__ . '/uploads/suggestions/';

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    $errorMessage = "Failed to create upload folder.";
                }
            }

            if ($errorMessage === '' && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];

                $originalName = $_FILES['attachment']['name'];
                $tmpName = $_FILES['attachment']['tmp_name'];
                $fileSize = (int)$_FILES['attachment']['size'];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($extension, $allowedExtensions, true)) {
                    $errorMessage = "Invalid file type. Allowed: jpg, jpeg, png, pdf, doc, docx.";
                } elseif ($fileSize > 5 * 1024 * 1024) {
                    $errorMessage = "File is too large. Maximum size is 5MB.";
                } else {
                    $newFileName = 'suggestion_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                    $destination = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $attachmentPath = 'uploads/suggestions/' . $newFileName;
                    } else {
                        $errorMessage = "Failed to upload the file.";
                    }
                }
            } elseif ($errorMessage === '') {
                $errorMessage = "There was an upload error.";
            }
        }

        if ($errorMessage === '') {
            $sql = "INSERT INTO MemberSuggestion (user_id, suggestion_text, attachment_path, status)
                    VALUES (:user_id, :suggestion_text, :attachment_path, :status)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':suggestion_text' => $suggestionText,
                ':attachment_path' => $attachmentPath,
                ':status' => 'Pending'
            ]);

	$_SESSION['submitted'] = true;
	$_SESSION['submitted_from'] = 'memberSuggestion';
	header("Location: thank_you.php");
exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Suggestion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f7f7f7;
        }

        .container {
            max-width: 700px;
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin: 0 auto;
        }

        h2 {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        textarea,
        input[type="file"] {
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            min-height: 150px;
            padding: 10px;
            resize: vertical;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 18px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn:hover {
            background: #0056b3;
        }

        .message {
            margin-top: 15px;
            padding: 12px;
            border-radius: 6px;
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

        .note {
            font-size: 0.9rem;
            color: #555;
            margin-top: 6px;
        }

        .back-link {
            display: inline-block;
            margin-top: 18px;
            text-decoration: none;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit Suggestion or Feedback</h2>

        <?php if ($successMessage !== ''): ?>
            <div class="message success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
            <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="suggestion_text">Suggestion / Feedback</label>
            <textarea
                name="suggestion_text"
                id="suggestion_text"
                placeholder="Enter your suggestion or feedback here..."
                required
            ><?= htmlspecialchars($_POST['suggestion_text'] ?? '') ?></textarea>

            <label for="attachment">Attach Screenshot or Document</label>
            <input
                type="file"
                name="attachment"
                id="attachment"
                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
            >
            <div class="note">Allowed files: JPG, JPEG, PNG, PDF, DOC, DOCX. Max size: 5MB.</div>

            <button type="submit" class="btn">Submit</button>
        </form>

        <a class="back-link" href="index.php">← Back</a>
    </div>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../include/db_connect.php';

// Check login
if (!isset($_SESSION['user']['user_id'])) {
    header("Location: ../login.php");
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
    <link rel="icon" href= ../images/logo.png>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Suggestion</title>
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .suggestion-wrapper {
            width: 95%;
            max-width: 700px;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .suggestion-card {
            background-color: #faf5f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .suggestion-card h2 {
            color: #3b2f2f;
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: 600;
            color: #3b2f2f;
        }

        textarea {
            width: 100%;
            box-sizing: border-box;
            min-height: 150px;
            padding: 10px;
            resize: vertical;
            border-radius: 8px;
            border: 1px solid #e6d5c3;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            background-color: #fdfaf7;
        }

        textarea:focus {
            outline: none;
            border-color: #c4a484;
        }

        input[type="file"] {
            width: 100%;
            box-sizing: border-box;
            margin-top: 4px;
        }

        .note {
            font-size: 0.85rem;
            color: #8b6f47;
            margin-top: 6px;
        }

        .btn {
            margin-top: 24px;
            padding: 12px 24px;
            border: none;
            background-color: #c4a484;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            font-size: 1em;
            font-weight: 600;
        }

        .btn:hover {
            background-color: #b39578;
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

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #8b6f47;
            font-size: 0.95em;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="suggestion-wrapper">
        <div class="suggestion-card">
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

            <a class="back-link" href="../index.php">← Back</a>
        </div>
    </div>
</body>
</html>

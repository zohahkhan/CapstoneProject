<?php
require_once('include/db_connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user']['user_id'];
$form_status = "Pending";
$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($form_id <= 0) {
    die("Invalid form ID.");
}

/* Fetch template */
$queryForm = "
    SELECT template_id, temp_title, temp_desc, temp_status, form_questions, form_deadline
    FROM FormTemplate
    WHERE template_id = :template_id
";
$stmt2 = $db->prepare($queryForm);
$stmt2->bindValue(':template_id', $form_id, PDO::PARAM_INT);
$stmt2->execute();
$row = $stmt2->fetch(PDO::FETCH_ASSOC);
$stmt2->closeCursor();

if (!$row) {
    die("Form template not found.");
}

$allQuestions = json_decode($row['form_questions'], true);

if (!is_array($allQuestions) || empty($allQuestions)) {
    die("Invalid form questions.");
}

$pageRanges = [
    1 => ['start' => 0,  'length' => 5],
    2 => ['start' => 5,  'length' => 5],
    3 => ['start' => 10, 'length' => 9],
    4 => ['start' => 19, 'length' => 4],
    5 => ['start' => 23, 'length' => 4],
    6 => ['start' => 27, 'length' => 6]
];

$totalSteps = count($pageRanges);
$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$answers = $_POST['answers'] ?? [];

/* Handle submit BEFORE any HTML output */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $formatted = [];

    foreach ($answers as $qid => $resp) {
        $formatted[] = [
            'id' => (int)$qid,
            'response' => (string)$resp
        ];
    }

    usort($formatted, function($a, $b) {
        return $a['id'] <=> $b['id'];
    });

    $json = json_encode($formatted);

    $stmt5 = $db->prepare("
        INSERT INTO FormResponse (template_id, user_id, form_response, form_status)
        VALUES (:template_id, :user_id, :form_response, :form_status)
    ");
    $stmt5->bindParam(':template_id', $form_id, PDO::PARAM_INT);
    $stmt5->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt5->bindParam(':form_response', $json, PDO::PARAM_STR);
    $stmt5->bindParam(':form_status', $form_status, PDO::PARAM_STR);
    $stmt5->execute();

    if (isset($_POST['submit_quiz'])) 
    {	
        echo "<h3 style='text-align:center;'>Survey Complete!</h3><br>"; 
    }
    //header("Location: include/surveyHub.php?id=$form_id");
    exit;
}

/* Navigation */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['next']) && $step < $totalSteps) {
        $step++;
    }

    if (isset($_POST['back']) && $step > 1) {
        $step--;
    }
}

$range = $pageRanges[$step];
$displayQuestions = array_slice($allQuestions, $range['start'], $range['length']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($row['temp_title']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<form method="post" action="headdepartmentSurvey.php?id=<?= $form_id ?>">
    <input type="hidden" name="step" value="<?= $step ?>">

    <h2><?= htmlspecialchars($row['temp_title']) ?></h2>
    <p><?= htmlspecialchars($row['temp_desc']) ?></p>
    <p><strong>Page <?= $step ?> of <?= $totalSteps ?></strong></p>

    <?php foreach ($displayQuestions as $q): ?>
        <div style="margin-bottom: 25px;">
            <p><strong><?= htmlspecialchars($q['question'] ?? 'Unknown question') ?></strong></p>

            <?php if (!empty($q['options']) && is_array($q['options'])): ?>
                <?php foreach ($q['options'] as $option): ?>
                    <label>
                        <input
                            type="radio"
                            name="answers[<?= (int)$q['id'] ?>]"
                            value="<?= htmlspecialchars($option) ?>"
                            <?= (isset($answers[$q['id']]) && $answers[$q['id']] == $option) ? 'checked' : '' ?>
                            required
                        >
                        <?= htmlspecialchars($option) ?>
                    </label><br>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php
    $currentIds = [];
    foreach ($displayQuestions as $q) {
        if (isset($q['id'])) {
            $currentIds[] = (int)$q['id'];
        }
    }

    foreach ($answers as $id => $val) {
        if (!in_array((int)$id, $currentIds)) {
            echo '<input type="hidden" name="answers[' . (int)$id . ']" value="' . htmlspecialchars($val, ENT_QUOTES) . '">';
        }
    }
    ?>

    <?php if ($step > 1): ?>
        <button type="submit" name="back" formnovalidate>Back</button>
    <?php endif; ?>

    <?php if ($step < $totalSteps): ?>
        <button type="submit" name="next">Next</button>
    <?php else: ?>
        <button type="submit" name="submit_quiz">Submit Report</button>
    <?php endif; ?>
</form>

</body>
</html>

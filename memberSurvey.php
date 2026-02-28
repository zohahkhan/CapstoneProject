<?php
require_once('include/db_connect.php');
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

// Titles for Lajna quiz pages
$group1Title = 'Office Bearer';
$group2Title = 'Taleem (Education)';
$group3Title = 'Lajna/Nasirat books:';
$group4Title = 'Tarbiyyat (Moral Training)';
$group5Title = 'Tabligh (Preaching)';

$form_id = 1;

// Fetch questions from database
$queryForm = 'SELECT template_id, temp_title, temp_desc, temp_status, form_questions, form_deadline FROM FormTemplate WHERE template_id = :template_id';
$stmt2 = $db->prepare($queryForm);
$stmt2->bindValue(':template_id', $form_id);
$stmt2->execute();
$row = $stmt2->fetch();
$stmt2->closeCursor();

// Decode JSON questions
$allQuestions = json_decode($row['form_questions'], true);

// Get previous answers and step info
$answers = $_POST['answers'] ?? [];
$step = $_POST['step'] ?? 1;
$currentGroup = $_POST['currentGroup'] ?? 1;

// Determine branch answer
$branchAnswer = isset($answers[5]) ? (int)$answers[5] : null;

// Makes sure the correct questions are displayed on page 2
if ($branchAnswer === 0) {
    $branchAnswer = 1; // Lajna maps to branch 1
} elseif ($branchAnswer === 1) {
    $branchAnswer = 0; // Nasirat maps to branch 0
}

// Handle form navigation
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // NEXT button
    if (isset($_POST['next'])) 
	{
        if ($step == 1) 
		{
            $step = 2;
            $currentGroup = ($branchAnswer === 1) ? 1 : 0;
        } elseif ($branchAnswer === 1 && $currentGroup < 5) 
		{
            $currentGroup++;
        } elseif ($branchAnswer === 0 || ($branchAnswer === 1 && $currentGroup == 5)) 
		{
            $step = 3;
        }
    }

    // BACK button
    if (isset($_POST['back'])) 
	{
        if ($step == 2) 
		{
            if ($branchAnswer === 1 && $currentGroup > 1) 
			{
                $currentGroup--;
            } else {
                $step = 1;
            }
        } elseif ($step == 3) {
            if ($branchAnswer === 1) 
			{
                $step = 2;
                $currentGroup = 5;
            } else {
                $step = 2;
                $currentGroup = 0;
            }
        }
    }
}

// recompute current group based on latest branch answer
if ($step == 2) 
{
    if ($branchAnswer === 1) 
	{
        $currentGroup = max($currentGroup, 1);
    } elseif ($branchAnswer === 0) {
        $currentGroup = 0;
    }
}

// display the questions on each page
$displayQuestions = [];
if ($step == 1) 
{
    foreach ($allQuestions as $q) if ($q['id'] <= 5) $displayQuestions[] = $q;
} elseif ($step == 2) 
{
    foreach ($allQuestions as $q) 
	{
        if ($branchAnswer === 0 && isset($q['branch']) && $q['branch'] == 0) 
		{
            $displayQuestions[] = $q;
        }
        if ($branchAnswer === 1 && isset($q['branch'], $q['group']) &&
            $q['branch'] == 1 && $q['group'] == $currentGroup) 
			{
            $displayQuestions[] = $q;
        }
    }
}

if ($step == 2 && $branchAnswer === 1) 
{
    // only branch 1 has groups
    switch ($currentGroup) 
	{
        case 1:
            echo "<h3>{$group1Title}</h3>";
            break;
        case 2:
            echo "<h3>{$group2Title}</h3>";
            break;
        case 3:
            echo "<h3>{$group3Title}</h3>";
            break;
        case 4:
            echo "<h3>{$group4Title}</h3>";
            break;
        case 5:
            echo "<h3>{$group5Title}</h3>";
            break;
    }
}

if (isset($_POST['submit_quiz'])) {
    echo "<pre>" . json_encode($answers, JSON_PRETTY_PRINT) . "</pre>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Monthly Members Survey</title>
	<link rel="stylesheet" href="style.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
	<form method="post">
	<input type="hidden" name="step" value="<?= $step ?>">
	<input type="hidden" name="currentGroup" value="<?= $currentGroup ?>">

<?php if ($step == 1): ?>
<h2><?= ($row['temp_title']) ?></h2>
<p><?= ($row['temp_desc']) ?></p>
<?php endif; ?>


	<?php foreach ($displayQuestions as $q): ?>
	<div>
		<p><strong><?= ($q['question']) ?></strong></p>
	<?php foreach ($q['options'] as $i => $option): ?>
	<label>
		<input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $i ?>"
			<?= (isset($answers[$q['id']]) && $answers[$q['id']] == $i) ? 'checked' : '' ?>
			<?php if (!isset($_POST['back'])) echo ' required'; ?>>
			<?= ($option) ?>
	</label><br>
<?php endforeach; ?>
</div>
<?php endforeach; ?>

<!-- Preserve answers for questions not on the current page -->
<?php
$currentIds = array_column($displayQuestions, 'id');
foreach ($answers as $id => $val) 
{
    if (!in_array($id, $currentIds)) 
	{
        echo '<input type="hidden" name="answers['.$id.']" value="'.($val).'">';
    }
}
?>

<br><br>
<?php
if ($step == 1 || ($step == 2 && ($branchAnswer === 1 && $currentGroup < 5 || $branchAnswer === 0))) 
{
    echo '<button type="submit" name="back" formnovalidate>Back</button> ';
    echo '<button type="submit" name="next">Next</button>';
} else 
{
    echo '<button type="submit" name="back" formnovalidate>Back</button> ';
    echo '<button type="submit" name="submit_quiz">Submit Quiz</button>';
}
?>
</form>
</body>
</html>













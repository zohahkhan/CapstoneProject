<!--- preview the quiz and results -->
<style>

#quizBox {
    width: 250px;
    border: 2px solid black;
    display: block;
    height: auto !important;
    min-height: 0 !important;
}

#quizList {
    background: #f4f4f4;
    background-color: #fdfaf7;
    padding: 10px;
    border-bottom: 1px solid #ccc;
    overflow-y: auto;
    display: flex;          
    flex-direction: column; 
    width: 100%;            
}

.monthly-report-box {
    width: 100%;
    height: 100%;
    margin-top: 5px;
    padding: 5px;
    background-color: #fdfaf7;
    border-radius: 5px;
    border: 1px solid #e6d5c3;
    box-shadow: inset 0 2px 6px rgba(0,0,0,0.05);
    text-align: left;

    display: flex;
    flex-direction: column;
}

.scrollable-monthly-report-box {
    width: 100%;
    max-height: 80px;
    overflow-y: auto;
}

.scrollable-monthly-report-box::-webkit-scrollbar {
    width: 8px;
}

.scrollable-monthly-report-box::-webkit-scrollbar-thumb {
    background-color: #c4a484;
    border-radius: 6px;
}

.quiz-item {
    background-color: #e8d9c8;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 5px;
    cursor: pointer;

    width: 100%;
    box-sizing: border-box;

    display: flex;
    justify-content: center; /* horizontal center */
    align-items: center;     /* vertical center */
}

.quiz-item.completed {
    background-color: #d4edda;
    cursor: default;
    opacity: 0.7;
}

#quizFrame {
    flex: 1;
    width: 100%;
    height: 600px;
    border: 1px solid #ccc;
    margin-top: 15px;
}
</style>

<?php
require_once 'db_connect.php';

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

// Check if user already submitted
$stmt = $db->prepare("
    SELECT 1
    FROM FormResponse 
    WHERE template_id = :template_id
      AND user_id = :user_id
    LIMIT 1
");
$stmt->bindParam(':template_id', $form_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$alreadyCompleted = $stmt->fetch(PDO::FETCH_ASSOC);

if ($alreadyCompleted) {
    // redirect back to hub
    header("Location: surveyHub.php");
    exit;
}

if (isset($_SESSION['user']['user_id'])) 
{
	if (!isset($user_id)) 
	{
		$user_id = $_SESSION['user']['user_id'];	
	}
}

//added code
if (!isset($_SESSION['user']['user_id'])) 
{
    die("User not logged in.");
} 

if (!isset($_SESSION['user']['role_id'])) 
{
    die("User role not found.");
}

$user_id = $_SESSION['user']['user_id']; 
$role_id = $_SESSION['user']['role_id'];

/*
    role_id:
    2 = Department Head
    3 = Member
*/

$formTitle = '';
$formPage = '';

if ($role_id == 2) 
{
    $formTitle = '%Compiled Monthly Report%';
    $formPage = 'headdepartmentSurvey.php';
}
elseif ($role_id == 3 || 1) 
{
    $formTitle = '%Monthly Members Survey%';
    $formPage = 'memberSurvey.php';
}
else 
{
    die("No valid role found for surveys.");
}
//end of code I added

/* Fetch JSON results for all quizzes */
$sql = 'SELECT q.template_id, q.temp_title, q.form_questions, r.form_response
        FROM FormTemplate q
        LEFT JOIN FormResponse r 
        ON q.template_id = r.template_id AND r.user_id = :user_id
        WHERE q.temp_title LIKE :temp_title
        ORDER BY q.template_id;';

$stmt3 = $db->prepare($sql);
$stmt3->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt3->bindParam(':temp_title', $formTitle, PDO::PARAM_STR);
$stmt3->execute();
$quizzes = $stmt3->fetchAll(PDO::FETCH_ASSOC);
$stmt3->closeCursor();
?>

<div class="monthly-report-box">
<div class="scrollable-monthly-report-box">
<?php foreach ($quizzes as $quiz): ?>
    <?php if ($quiz['form_response']): ?>
        <?php
        $questionsData = json_decode($quiz['form_questions'], true); // <-- FIXED (proper PHP placement)
        $responsesData = json_decode($quiz['form_response'], true); // <-- FIXED
        ?>

    <?php
        $responseMap = [];
    foreach ($responsesData as $resp) 
    {
        $responseMap[$resp['id']] = $resp['response'];
    }
?>
	
    <!---- this part displays after the form is complete
				and is responsible for the box -->
       <div class="">
      <ol>  <strong><?php echo htmlspecialchars($quiz['temp_title']); ?> (Submitted) </strong?id=<?= $quiz['template_id'] ?>'></ol>
	

<ol>

<?php 
foreach ($questionsData as $q): ?>
    <?php
    if (!isset($responseMap[$q['id']])) {
        continue;
    }

    $questionText = $q['question'] ?? 'Unknown question';
    $questionId = $q['id'];
    $userResponse = $responseMap[$q['id']] ?? 'No response';
    ?>

    <li>
	Question: <?php echo htmlspecialchars($questionText); ?><br> Your Response: <?php echo htmlspecialchars($userResponse); ?>
    </li>

<?php endforeach; ?>
</ol>

    </div>

    <?php endif; ?>

    <?php endforeach; ?>

</div>
<?php
$firstQuiz = $quizzes[0]['template_id'] ?? null;
?>

<iframe 
id="quizFrame"
src="<?php echo $formPage; ?>?id=<?php echo $firstQuiz; ?>">
</iframe>

</div>

<script>
function loadQuiz(page, event) {
    // Remove active class from all items
    const items = document.querySelectorAll('.quiz-item');
    items.forEach(item => item.classList.remove('active'));

    // Add active class to the clicked item
    event.currentTarget.classList.add('active');

    // Load the selected quiz in the iframe
    document.getElementById("quizFrame").src = page;
}
</script>

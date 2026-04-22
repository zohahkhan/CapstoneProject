<!--- preview the quiz and results -->
<style>
#quizBox {
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
	align-items: flex-start;
}
.monthly-report-box {
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
    max-height: 500px;
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
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;  
}
#quizFrame {
    flex: 1;
    border: 1px solid #ccc;
    margin-top: 15px;
	height: 100% !important;
	width: 100% !important; 
	border-radius: 10px; 
}
.showFrame {
    display: block;
}
.showFrame.is-hidden {
    display: none; 
}
</style>

<?php
require_once 'db_connect.php';

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
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

/* NOTE 2 SELF: UPDATE THIS TO YOUR CURRENT HTDOCS DIRECTORY */
define('BASE_URL', '/CapstoneProject-final-develop-integration/');

if ($role_id == 2) 
{
    $formTitle = '%Compiled Monthly Report%';
    $formPage = BASE_URL . 'SurveyPages/headdepartmentSurvey.php';
}
elseif ($role_id == 3 || 2) 
{
    $formTitle = '%Monthly Members Survey%';
    $formPage = BASE_URL . 'SurveyPages/memberSurvey.php';
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


<?php
$form_id = $quizzes[0]['template_id'] ?? null;

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
?>

<iframe 
	id="quizFrame" class="<?= $alreadyCompleted ? 'showFrame is-hidden' : 'showFrame' ?>"
	src="<?php echo $formPage; ?>?id=<?php echo $form_id; ?>">
</iframe>

<div class="monthly-report-box scrollable-monthly-report-box">

<?php 
	foreach ($quizzes as $quiz): ?>

    <?php if ($quiz['form_response']): ?>
	
    <?php
        $questionsData = json_decode($quiz['form_questions'], true);
        $responsesData = json_decode($quiz['form_response'], true);	
    ?>
		
	<?php 
		$responseMap = [];
    foreach ($responsesData as $resp) 
    {
        $responseMap[$resp['id']] = $resp['response'];
    }
?>
	
	<!---- this part displays after the form is complete -->
	<div class="">
    <ol>  
		<strong><?php echo htmlspecialchars($quiz['temp_title']); 
		?> (Submitted) </strong?id=<?= $quiz['template_id'] ?>'>	
	</ol>
	
<ol>
<?php foreach ($questionsData as $q): ?>

    <?php // question and answers
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

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
    background-color: #fdfaf7; /*makes it lighter than main box for contrast*/
    padding: 10px;
    border-bottom: 1px solid #ccc;
    overflow-y: auto;
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

.scrollable-report-box::-webkit-scrollbar {
    width: 8px;
}

.scrollable-report-box::-webkit-scrollbar-thumb {
    background-color: #c4a484;
    border-radius: 10px;
}

.scrollable-monthly-report-box::-webkit-scrollbar {
    width: 8px;
}

.scrollable-monthly-report-box::-webkit-scrollbar-track {
    background: #fdfaf7;
}

.scrollable-monthly-report-box::-webkit-scrollbar-thumb {
    background-color: #c4a484;
    border-radius: 6px;
}

.scrollable-monthly-report-box::-webkit-scrollbar-thumb:hover {
    background-color: #8b6f47;
}

.quiz-item {
    background-color: #e8d9c8;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 5px;
    cursor: pointer;
}

.quiz-item.active {
    background-color: #e8d9c8;
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

if (!isset($_SESSION['user']['user_id'])) 
{
    die("User not logged in.");
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
elseif ($role_id == 3) 
{
    $formTitle = '%Monthly Members Survey%';
    $formPage = 'memberSurvey.php';
}
else 
{
    die("No valid role found for surveys.");
}

/* Fetch JSON results only for the correct survey type */
	if (!isset($user_id)) 
	{
		$user_id = $_SESSION['user']['user_id'];	
	}
}?>

<div class="monthly-report-box">

<div class="scrollable-monthly-report-box">

<?php
// Fetch JSON results for all quizzes
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

<div id="quizBox">
    <div id="quizList">

        <?php if (empty($quizzes)): ?>
            <div class="quiz-item completed">No forms found.</div>
        <?php else: ?>

            <?php foreach ($quizzes as $quiz): ?>
                <?php if ($quiz['form_response']): ?>
                    <div class="quiz-item completed">
                        <?php echo htmlspecialchars($quiz['temp_title']); ?> (Submitted)
                    </div>
                <?php else: ?>
                    <div class="quiz-item" 
                        onclick="loadQuiz('<?php echo $formPage; ?>?id=<?= $quiz['template_id'] ?>', event)">
                        <?php echo htmlspecialchars($quiz['temp_title']); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</div>

<?php foreach ($quizzes as $quiz): ?>
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
                <strong><?php echo htmlspecialchars($quiz['temp_title']); ?> (Submitted)</strong>
            </ol>

            <ol>
            <?php foreach ($questionsData as $q): ?>
                <?php 
                if (!isset($responseMap[$q['id']])) {
                    continue;
                }

                $questionText = $q['question'] ?? 'Unknown question';
                $questionId = $q['id'];
                $userResponse = $responseMap[$questionId] ?? 'No response';
                ?>
                <li> 
                    Question: <?php echo htmlspecialchars($questionText); ?><br>
                    Your Response: <?php echo htmlspecialchars($userResponse); ?>
                </li>
            <?php endforeach; ?>
            </ol>
        </div>

    <?php endif; ?>
<?php endforeach; ?>

<iframe id="quizFrame"></iframe>
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

<!---- this part displays after the form is complete and is responsible for the box -->
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
                $userResponse = $responseMap[$questionId] ?? 'No response'; ?>
            <li> 
                Question: <?php echo htmlspecialchars($questionText); ?><br> Your Response: <?php echo htmlspecialchars($userResponse); ?>
            </li>
        <?php endforeach; ?>
        </ol>
</div>
    <?php else: ?>
        <div class="quiz-item" 
		onclick="loadQuiz('memberSurvey.php?id=<?= $quiz['template_id'] ?>', event)">
            <?php echo htmlspecialchars($quiz['temp_title']); ?>
        </div>
        <?php endif; ?>
<?php endforeach; ?>

</div>

 <iframe id="quizFrame" ></iframe>
</div>

<script>
function loadQuiz(page, event) {
    const items = document.querySelectorAll('.quiz-item');
    items.forEach(item => item.classList.remove('active'));

    event.currentTarget.classList.add('active');
    document.getElementById("quizFrame").src = page;
}
</script>

<!--- preview the quiz and results -->
<style>

#quizBox {
    width: 250px;
    border: 2px solid black;
    display: flex;
    flex-direction: column;
    height: 74px;
}

#quizList {
    background: #f4f4f4;
    padding: 10px;
    border-bottom: 1px solid #ccc;
    overflow-y: auto;
}

.quiz-item {
    background: white;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 5px;
    cursor: pointer;
}

.quiz-item.active {
    background-color: #d0e6ff;
}

.quiz-item.completed {
    background-color: #d4edda;
    cursor: default;
    opacity: 0.7;
    border: 1px solid #999;
    padding: 10px;
    max-height: 200px; 
	max-width: 500px;
	overflow-y: scroll;
	word-wrap: break-word;
	overflow-wrap: break-word;
}

#quizFrame {
    flex: 1;
    width: 100%;
    height: 600px;
    border: 1px solid #ccc;
    margin-top: 15px;
}
.hidden {
    display: none;
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
elseif ($role_id == 2 || $role_id == 3) 
{
    $formTitle = '%Monthly Members Survey%';
    $formPage = 'memberSurvey.php';
}
else 
{
    die("No valid role found for surveys.");
}

/* Fetch JSON results only for the correct survey type */
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
        <div class="quiz-item completed">
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

<iframe id="quizFrame" class="hidden"></iframe>

<script>
function loadQuiz(page, event) {
    const items = document.querySelectorAll('.quiz-item');
    items.forEach(item => item.classList.remove('active'));

    event.currentTarget.classList.add('active');
    document.getElementById("quizFrame").src = page;

    const frame = document.getElementById('quizFrame');
	frame.classList.remove('hidden');
    frame.src = page;
}
</script>

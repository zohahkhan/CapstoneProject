<?php
include __DIR__ . '/../include/db_connect.php';

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

// restrict access to the summary report 
if (!in_array($_SESSION['user']['role_id'], [1, 2, 4])) 
{
	require_once __DIR__ . '/../include/config.php';
	$error_page = BASE_URL.'/include/error.php';
    header("Location: $error_page");
    exit;
}

/*
    If template_id is passed from hyperlink, use it.
    Otherwise, fall back to finding the Head Department form by title.
*/
$template_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($template_id > 0) 
{
    $stmt = $db->prepare("
        SELECT template_id, temp_title, form_questions
        FROM FormTemplate
        WHERE template_id = :template_id
        LIMIT 1
    ");
    $stmt->execute(['template_id' => $template_id]);
}
else 
{
    $formTitle = '%Compiled Monthly Report%';

    $stmt = $db->prepare("
        SELECT template_id, temp_title, form_questions
        FROM FormTemplate
        WHERE temp_title LIKE :temp_title
        ORDER BY template_id
        LIMIT 1
    ");
    $stmt->execute(['temp_title' => $formTitle]);
}

$template = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$template) 
{
    die("Head Department form template not found.");
}

$template_id = $template['template_id'];
$temp_title = $template['temp_title'];
$form_questions = json_decode($template['form_questions'], true);

if (!is_array($form_questions)) 
{
    die("Invalid form questions data.");
}

/*
    Get all responses submitted for this Head Department form
*/
$stmt2 = $db->prepare("
    SELECT form_response
    FROM FormResponse
    WHERE template_id = :template_id
");
$stmt2->execute(['template_id' => $template_id]);
$responses = $stmt2->fetchAll(PDO::FETCH_ASSOC);

/*
    Page grouping based on the form layout:
*/
$page_groups = [
    1 => [0, 4],
    2 => [5, 9],
    3 => [10, 18],
    4 => [19, 22],
    5 => [23, 26],
    6 => [27, 32]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="icon" href= ../images/logo.png>
    <meta charset="UTF-8">
    <title>Head Department Responses Summary</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            background-image: url("../images/background.png");
        }

        .box {
            width: 95%;
            max-width: 1400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 10px;
        }

        .summary-box {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .page-section {
            margin-top: 35px;
            margin-bottom: 35px;
        }

        .page-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #ccc;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .chart-wrapper {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            min-height: 380px;
        }

        .chart-wrapper h3 {
            font-size: 16px;
            line-height: 1.4;
            margin-top: 0;
            margin-bottom: 15px;
        }

        canvas {
            max-width: 100%;
            height: 250px !important;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #0056b3;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="box">
    <h2><?php echo htmlspecialchars($temp_title); ?> Responses Summary</h2>

    <div class="summary-box">
        <h3>Total Reports Submitted</h3>
        <p><strong><?php echo count($responses); ?></strong></p>
    </div>

    <?php foreach ($page_groups as $page_number => $range): ?>
        <?php
            $start = $range[0];
            $end = $range[1];
        ?>

        <div class="page-section">
            <div class="page-title">Page <?php echo $page_number; ?></div>

            <div class="charts-grid">
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <?php
                    if (!isset($form_questions[$i])) 
                    {
                        continue;
                    }

                    $question = $form_questions[$i];
                    $question_id = $question['id'] ?? '';
                    $question_text = $question['question'] ?? 'Unknown question';

                    $counts = [];

                    foreach ($responses as $row) 
                    {
                        $json = json_decode($row['form_response'], true);

                        if (!is_array($json)) 
                        {
                            continue;
                        }

                        foreach ($json as $item) 
                        {
                            if (($item['id'] ?? '') == $question_id) 
                            {
                                $answer = trim($item['response'] ?? '');

                                if ($answer === '') 
                                {
                                    $answer = 'No response';
                                }

                                if (!isset($counts[$answer])) 
                                {
                                    $counts[$answer] = 0;
                                }

                                $counts[$answer]++;
                            }
                        }
                    }

                    if (empty($counts)) 
                    {
                        $counts['No responses yet'] = 1;
                    }

                    $labels = json_encode(array_keys($counts));
                    $data = json_encode(array_values($counts));
                    $canvas_id = "chart_" . $question_id . "_" . $i;
                    ?>

                    <div class="chart-wrapper">
                        <h3><?php echo htmlspecialchars($question_text); ?></h3>
                        <canvas id="<?php echo $canvas_id; ?>"></canvas>
                    </div>

                    <script>
                        new Chart(document.getElementById('<?php echo $canvas_id; ?>'), {
                            type: 'pie',
                            data: {
                                labels: <?php echo $labels; ?>,
                                datasets: [{
                                    data: <?php echo $data; ?>,
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.5)',
                                        'rgba(255, 99, 132, 0.5)',
                                        'rgba(255, 206, 86, 0.5)',
                                        'rgba(153, 102, 255, 0.5)',
                                        'rgba(54, 162, 235, 0.5)',
                                        'rgba(255, 159, 64, 0.5)',
                                        'rgba(201, 203, 207, 0.5)',
                                        'rgba(100, 181, 246, 0.5)'
                                    ],
                                    borderColor: [
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(201, 203, 207, 1)',
                                        'rgba(100, 181, 246, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    title: {
                                        display: false
                                    }
                                }
                            }
                        });
                    </script>
                <?php endfor; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <a class="back-link" href="../index.php">Back to Homepage</a>
</div>
</body>
</html>

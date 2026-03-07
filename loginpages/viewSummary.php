<?php
//database connection
require_once './include/db_connect.php';

$template_id = 1; //selects the form
//gets the questions from db, using JSON
$stmt = $db->prepare("SELECT form_questions FROM FormTemplate WHERE template_id = :template_id");
$stmt->execute(['template_id' => $template_id]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);
//this converts the JSON into a php array, so we can loop through questions
$form_questions = json_decode($template['form_questions'], true);

//getting the responses for the questions
$stmt2 = $db->prepare("SELECT form_response FROM FormResponse WHERE template_id = :template_id");
$stmt2->execute(['template_id' => $template_id]);
$responses = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Responses Summary</title>
	<link rel="stylesheet" href="style.css">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="box">
    <h2>Responses Summary</h2>

    <div class="chart-wrapper">
        <h3>Total Reports Submitted</h3>
        <p><strong><?= count($responses); ?></strong></p>
    </div>
	
    <div class="charts-grid">
    <?php
    //looping through each question for charts
    foreach ($form_questions as $question) {
		//getting question ID and text
        $question_id = $question['id'];
        $question_text = $question['question'];

        //counts array stores the number of answers
        $counts = [];
		//so this loop goes through all of the responses 
        foreach ($responses as $row) {
			//decodes so that php can read
            $json = json_decode($row['form_response'], true);
			//goes through the question reponses for current id
            foreach ($json as $item) {
                if ($item['id'] == $question_id) {
                    $answer = $item['response'];
                    if (!isset($counts[$answer])) {
                        $counts[$answer] = 0;
                    }
					//this counts the answers 
                    $counts[$answer]++;
                }
            }
        }
		//now converting it to a format for the charts 
        $labels = json_encode(array_keys($counts));
        $data = json_encode(array_values($counts));

        //creating the chart canvas
        $canvas_id = "chart_" . $question_id;

        echo "<div class='chart-wrapper'>";
        echo "<h3>$question_text</h3>";
		//this is the chart container
        echo "<canvas id='$canvas_id'></canvas>";
        echo "</div>";
		//this creates a new pie chart that has a legend at the bottom
        echo "<script>
            new Chart(document.getElementById('$canvas_id'), {
                type: 'pie',
                data: {
                    labels: $labels,
                    datasets: [{
                        data: $data,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(153, 102, 255, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 159, 64, 0.5)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
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
        </script>";
    }
    ?>
    </div>
	<br><br>	
    <a href="index.php">Back to Homepage</a>
</div>
</body>
</html>
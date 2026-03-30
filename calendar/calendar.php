<?php
session_start();

if (!isset($_SESSION['user']['user_id']))
{
    header("Location: login.php");
    exit;
}

require_once '../include/db_connect.php';
require_once '../include/calendar_helpers.php';

$user_id = $_SESSION['user']['user_id'];
$active_role = $_SESSION['user']['role_id'] ?? null;

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1;  $year++; }

$today_day   = (int)date('j');
$today_month = (int)date('n');
$today_year  = (int)date('Y');

$first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
$days_in_month      = (int)date('t', $first_day_of_month);
$start_weekday      = (int)date('w', $first_day_of_month);

$month_name = date('F', $first_day_of_month);

$prev_month = $month - 1;
$prev_year  = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }

$next_month = $month + 1;
$next_year  = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

$monthStart = sprintf('%04d-%02d-01 00:00:00', $year, $month);
$monthEnd   = date('Y-m-t 23:59:59', strtotime($monthStart));
$monthStartDate = date('Y-m-01', strtotime($monthStart));
$monthEndDate   = date('Y-m-t', strtotime($monthStart));

$stmt = $db->prepare("
    SELECT *
    FROM CalendarEvent
    WHERE status = 'active'
      AND (
            (
                (recurring IS NULL OR recurring = '')
                AND start_datetime <= :monthEnd
                AND end_datetime >= :monthStart
            )
         OR (
                recurring IS NOT NULL
                AND recurring <> ''
                AND start_datetime <= :monthEnd
            )
      )
    ORDER BY start_datetime ASC
");
$stmt->execute([
    ':monthStart' => $monthStart,
    ':monthEnd'   => $monthEnd
]);
$all_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$event_title_map = [];
foreach ($all_events as $event)
{
    $event_title_map[$event['event_id']] = $event['event_title'];
}

$events_raw = [];

foreach ($all_events as $event)
{
    $isRecurring = !empty($event['recurring']);

    if (!$isRecurring)
    {
        $events_raw[] = [
            'event_id' => $event['event_id'],
            'event_title' => $event['event_title'],
            'occurrence_date' => date('Y-m-d', strtotime($event['start_datetime'])),
            'occurrence_start' => $event['start_datetime'],
            'occurrence_end' => $event['end_datetime'],
            'is_recurring' => 0,
            'recurrence_type' => null
        ];
    }
    else
    {
        $occurrences = generateOccurrences($event, $month, $year);

        foreach ($occurrences as $occurrence)
        {
            $events_raw[] = $occurrence;
        }
    }
}

$stmtEx = $db->prepare("
    SELECT *
    FROM CalendarEvent_Exception
    WHERE (
        original_occurrence_date BETWEEN :dateStart AND :dateEnd
        OR DATE(new_start_datetime) BETWEEN :dateStart AND :dateEnd
    )
");
$stmtEx->execute([
    ':dateStart' => $monthStartDate,
    ':dateEnd'   => $monthEndDate
]);
$exceptions = $stmtEx->fetchAll(PDO::FETCH_ASSOC);

foreach ($exceptions as $ex)
{
    foreach ($events_raw as $k => $event)
    {
        if (
            $event['event_id'] == $ex['event_id'] &&
            $event['occurrence_date'] == $ex['original_occurrence_date']
        )
        {
            unset($events_raw[$k]);
        }
    }

    if (
        isset($ex['action_type']) &&
        $ex['action_type'] === 'rescheduled' &&
        !empty($ex['new_start_datetime']) &&
        !empty($ex['new_end_datetime'])
    )
    {
        $rescheduledDate = date('Y-m-d', strtotime($ex['new_start_datetime']));

        if ($rescheduledDate >= $monthStartDate && $rescheduledDate <= $monthEndDate)
        {
            $originalTitle = $event_title_map[$ex['event_id']] ?? 'Event';

            $events_raw[] = [
                'event_id' => $ex['event_id'],
                'event_title' => '[Rescheduled] ' . $originalTitle,
                'occurrence_date' => $rescheduledDate,
                'occurrence_start' => $ex['new_start_datetime'],
                'occurrence_end' => $ex['new_end_datetime'],
                'is_recurring' => 0,
                'recurrence_type' => null,
                'reason' => $ex['reason'] ?? ''
            ];
        }
    }
}

$events_raw = array_values($events_raw);

$events_by_day = [];

foreach ($events_raw as $event)
{
    $startDate = date('Y-m-d', strtotime($event['occurrence_start']));
    $endDate   = date('Y-m-d', strtotime($event['occurrence_end']));

    if ((int)$event['is_recurring'] === 1 || $startDate === $endDate)
    {
        $displayDate = $event['occurrence_date'];

        if ($displayDate >= $monthStartDate && $displayDate <= $monthEndDate)
        {
            $day_num = (int)date('j', strtotime($displayDate));
            $events_by_day[$day_num][] = $event;
        }
    }
    else
    {
        $loopDate = max($startDate, $monthStartDate);
        $loopEnd  = min($endDate, $monthEndDate);

        while ($loopDate <= $loopEnd)
        {
            $day_num = (int)date('j', strtotime($loopDate));
            $events_by_day[$day_num][] = $event;
            $loopDate = date('Y-m-d', strtotime($loopDate . ' +1 day'));
        }
    }
}

foreach ($events_by_day as $day => $dayEvents)
{
    usort($dayEvents, function ($a, $b) {
        return strtotime($a['occurrence_start']) <=> strtotime($b['occurrence_start']);
    });

    $events_by_day[$day] = $dayEvents;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body
        {
            font-family: 'Poppins', sans-serif;
            background-image: url("loginpages/images/background.png");
        }

        .calendar-wrapper
        {
            max-width: 900px;
            margin: 40px auto;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            padding: 40px;
            font-family: 'Poppins', sans-serif;
        }

        .calendar-header
        {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .calendar-header h2
        {
            font-size: 1.6rem;
            font-weight: 600;
            color: #3b2f2f;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .nav-btn
        {
            background-color: #c4a484;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .nav-btn:hover
        {
            background-color: #b39578;
        }

        .jump-form
        {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .jump-form select
        {
            background-color: #fff8f3;
            border: 1px solid #e8d9c8;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.9rem;
            color: #3b2f2f;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        .jump-form select:focus
        {
            outline: none;
            border-color: #c4a484;
        }

        .jump-btn
        {
            background-color: #c4a484;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .jump-btn:hover
        {
            background-color: #b39578;
        }

        .calendar-grid
        {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .day-label
        {
            text-align: center;
            font-weight: 600;
            color: #3b2f2f;
            padding: 8px 0;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
        }

        .day-cell
        {
            min-height: 90px;
            background-color: #fff8f3;
            border-radius: 8px;
            padding: 6px;
            border: 1px solid #e8d9c8;
            vertical-align: top;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }

        .day-cell.empty
        {
            background-color: transparent;
            border: none;
        }

        .day-cell.today
        {
            border: 2px solid #c4a484;
            background-color: #f5e9d9;
        }

        .day-number
        {
            font-weight: 600;
            font-size: 0.95rem;
            color: #3b2f2f;
            margin-bottom: 4px;
        }

        .day-cell.today .day-number
        {
            color: #8a5e3c;
        }

        .event-chip
        {
            background-color: #c4a484;
            color: white;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 0.72rem;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            font-family: 'Poppins', sans-serif;
        }

        .back-link
        {
            display: inline-block;
            margin-bottom: 20px;
            color: #c4a484;
            text-decoration: none;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }

        .back-link:hover
        {
            color: #b39578;
        }

        .legend
        {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #7a6655;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .legend-dot
        {
            width: 12px;
            height: 12px;
            background-color: #c4a484;
            border-radius: 3px;
            display: inline-block;
        }

        .legend-today
        {
            width: 12px;
            height: 12px;
            background-color: #f5e9d9;
            border: 2px solid #c4a484;
            border-radius: 3px;
            display: inline-block;
        }
      
      .day-top-row
{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.add-day-btn
{
    text-decoration: none;
    background-color: #c4a484;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
}
    
    

.add-day-btn:hover
{
    background-color: #b39578;
}
    </style>
</head>
<body>
<div class="calendar-wrapper">

    <a href="../index.php" class="back-link">&larr; Back to Home</a>

    <div class="calendar-header">
        <a href="calendar.php?month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="nav-btn">&lsaquo;</a>
        <h2><?= htmlspecialchars($month_name) ?> <?= $year ?></h2>
        <a href="calendar.php?month=<?= $next_month ?>&year=<?= $next_year ?>" class="nav-btn">&rsaquo;</a>
    </div>

    <form method="GET" action="calendar.php" class="jump-form">
        <select name="month">
            <?php
            $month_names = ['January','February','March','April','May','June',
                            'July','August','September','October','November','December'];
            foreach ($month_names as $i => $mn)
            {
                $val = $i + 1;
                $selected = ($val === $month) ? 'selected' : '';
                echo '<option value="' . $val . '" ' . $selected . '>' . $mn . '</option>';
            }
            ?>
        </select>
        <select name="year">
            <?php
            $start_year = 1900;
            $end_year   = 2100;
            for ($y = $start_year; $y <= $end_year; $y++)
            {
                $selected = ($y === $year) ? 'selected' : '';
                echo '<option value="' . $y . '" ' . $selected . '>' . $y . '</option>';
            }
            ?>
        </select>
        <button type="submit" class="jump-btn">Go</button>
    </form>

    <div class="calendar-grid">
        <?php
        $day_labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        foreach ($day_labels as $label)
        {
            echo '<div class="day-label">' . $label . '</div>';
        }

        for ($i = 0; $i < $start_weekday; $i++)
        {
            echo '<div class="day-cell empty"></div>';
        }

for ($day = 1; $day <= $days_in_month; $day++)
{
    $is_today = ($day === $today_day && $month === $today_month && $year === $today_year);
    $class = 'day-cell' . ($is_today ? ' today' : '');
    $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

    echo '<div class="' . $class . '">';

    // top row: day number + add button
    echo '<div class="day-top-row">';
    echo '<div class="day-number">' . $day . '</div>';
    echo '<a class="add-day-btn" href="create_event.php?date=' . urlencode($fullDate) . '" title="Add event">+</a>';
    echo '</div>';

    // events
    if (isset($events_by_day[$day]))
    {
        foreach ($events_by_day[$day] as $ev)
        {
            echo '<a class="event-chip" href="edit_event.php?id=' . urlencode($ev['event_id']) . '" title="' . htmlspecialchars($ev['event_title']) . '">'
                . htmlspecialchars($ev['event_title'])
                . '</a>';
        }
    }

    echo '</div>';
}
        ?>
    </div>

    <div class="legend">
        <span class="legend-today"></span> Today &nbsp;
        <span class="legend-dot"></span> Event
    </div>

</div>
</body>
</html>

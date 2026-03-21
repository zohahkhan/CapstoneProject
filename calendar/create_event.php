<?php
session_start();

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: ../login.php");
    exit;
}

date_default_timezone_set('America/New_York');

$selected_date = $_GET['date'] ?? '';
$default_start = '';
$default_end = '';

if (!empty($selected_date)) {
    $currentHour = (int)date('H');

    $startTimestamp = strtotime($selected_date . ' ' . str_pad($currentHour, 2, '0', STR_PAD_LEFT) . ':00:00');
    $startTimestamp = strtotime('+1 hour', $startTimestamp);

    $endTimestamp = strtotime('+60 minutes', $startTimestamp);

    $default_start = date('Y-m-d\TH:i', $startTimestamp);
    $default_end = date('Y-m-d\TH:i', $endTimestamp);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .box { max-width: 700px; margin: 30px auto; background: white; padding: 25px; border-radius: 10px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 6px; box-sizing: border-box; }
        .btn { margin-top: 16px; padding: 10px 16px; background: #7d5a50; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; }
        .cancel-btn { background: #888; margin-left: 10px; }
        .cancel-btn:hover { background: #666; }
    </style>
    <script>
        function toggleRecurringFields() {
            const recurringValue = document.getElementById('recurring').value;
            const recurringSection = document.getElementById('recurring_section');
            recurringSection.style.display = recurringValue !== '' ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="box">
    <h2>Create Event</h2>

    <form method="POST" action="insert_event.php">
        <label>Event Title</label>
        <input type="text" name="event_title" required>

        <label>Description</label>
        <textarea name="event_desc" rows="4"></textarea>

        <label>Start Date &amp; Time</label>
        <input type="datetime-local" name="start_datetime" value="<?= htmlspecialchars($default_start) ?>" required>

        <label>End Date &amp; Time</label>
        <input type="datetime-local" name="end_datetime" value="<?= htmlspecialchars($default_end) ?>" required>

        <label>Location</label>
        <input type="text" name="event_location">

        <label>Recurring?</label>
        <select name="recurring" id="recurring" onchange="toggleRecurringFields()">
            <option value="">No</option>
            <option value="Daily">Yes - Daily</option>
            <option value="Weekly">Yes - Weekly</option>
            <option value="Monthly">Yes - Monthly</option>
            <option value="Annually">Yes - Annually</option>
        </select>

        <div id="recurring_section" style="display:none;">
            <label>Recurrence Type</label>
            <select name="recurrence_type">
                <option value="">Select recurrence</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>

            <label>Repeat Every</label>
            <input type="number" name="recurrence_interval" min="1" value="1">

            <label>Recurring End Date</label>
            <input type="date" name="recurrence_end_date">
        </div>

        <button type="submit" class="btn">Create Event</button>
        <a href="calendar.php" class="btn cancel-btn">Back</a>
    </form>
</div>
</body>
</html>
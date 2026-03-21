<?php
session_start();

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../include/db_connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die('Missing event ID.');
}

$stmt = $db->prepare("SELECT * FROM CalendarEvent WHERE event_id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die('Event not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .box { max-width: 700px; margin: 30px auto; background: white; padding: 25px; border-radius: 10px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 6px; box-sizing: border-box; }
        .btn { margin-top: 16px; padding: 10px 16px; background: #7d5a50; color: white; border: none; border-radius: 6px; cursor: pointer; }
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
<body onload="toggleRecurringFields()">
<div class="box">
    <h2>Edit Event</h2>

    <form method="POST" action="update_event.php">
        <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id']) ?>">

        <label>Event Title</label>
        <input type="text" name="event_title" value="<?= htmlspecialchars($event['event_title']) ?>" required>

        <label>Description</label>
        <textarea name="event_desc" rows="4"><?= htmlspecialchars($event['event_desc'] ?? '') ?></textarea>

        <label>Start Date & Time</label>
        <input type="datetime-local" name="start_datetime" value="<?= !empty($event['start_datetime']) ? date('Y-m-d\TH:i', strtotime($event['start_datetime'])) : '' ?>" required>

        <label>End Date & Time</label>
        <input type="datetime-local" name="end_datetime" value="<?= !empty($event['end_datetime']) ? date('Y-m-d\TH:i', strtotime($event['end_datetime'])) : '' ?>" required>

        <label>Location</label>
        <input type="text" name="event_location" value="<?= htmlspecialchars($event['event_location'] ?? '') ?>">

        <label>Recurring?</label>
        <select name="recurring" id="recurring" onchange="toggleRecurringFields()">
            <option value="" <?= empty($event['recurring']) ? 'selected' : '' ?>>No</option>
            <option value="Daily" <?= ($event['recurring'] ?? '') === 'Daily' ? 'selected' : '' ?>>Yes - Daily</option>
            <option value="Weekly" <?= ($event['recurring'] ?? '') === 'Weekly' ? 'selected' : '' ?>>Yes - Weekly</option>
            <option value="Monthly" <?= ($event['recurring'] ?? '') === 'Monthly' ? 'selected' : '' ?>>Yes - Monthly</option>
            <option value="Annually" <?= ($event['recurring'] ?? '') === 'Annually' ? 'selected' : '' ?>>Yes - Annually</option>
        </select>

        <div id="recurring_section" style="display:none;">
            <label>Recurrence Type</label>
            <select name="recurrence_type">
                <option value="">Select recurrence</option>
                <option value="daily" <?= ($event['recurrence_type'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
                <option value="weekly" <?= ($event['recurrence_type'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                <option value="monthly" <?= ($event['recurrence_type'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="yearly" <?= ($event['recurrence_type'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
            </select>

            <label>Repeat Every</label>
            <input type="number" name="recurrence_interval" min="1" value="<?= (int)($event['recurrence_interval'] ?? 1) ?>">

            <label>Recurring End Date</label>
            <input type="date" name="recurrence_end_date" value="<?= !empty($event['recurrence_end_date']) ? htmlspecialchars(date('Y-m-d', strtotime($event['recurrence_end_date']))) : '' ?>">
        </div>

        <button type="submit" class="btn">Update Event</button>
    </form>

    <form method="POST" action="delete_event.php" style="margin-top:15px;">
        <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id']) ?>">
        <button type="submit" class="btn danger">Cancel Event</button>
      	<a href="calendar.php" class="btn cancel-btn">Back</a>
    </form>
</div>
</body>
</html>
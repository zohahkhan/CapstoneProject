<?php

function generateOccurrences(array $event, int $month, int $year): array
{
    $occurrences = [];

    $monthStart = new DateTime(sprintf('%04d-%02d-01 00:00:00', $year, $month));
    $monthEnd   = clone $monthStart;
    $monthEnd->modify('last day of this month')->setTime(23, 59, 59);

    $current = new DateTime($event['start_datetime']);
    $eventEnd = new DateTime($event['end_datetime']);

    $maxCount = !empty($event['recurrence_count']) ? (int)$event['recurrence_count'] : PHP_INT_MAX;
    $endDate  = !empty($event['recurrence_end_date'])
        ? new DateTime($event['recurrence_end_date'] . ' 23:59:59')
        : null;

    $interval = max(1, (int)($event['recurrence_interval'] ?? 1));
    $count = 0;

    while ($current <= $monthEnd && $count < $maxCount)
    {
        if ($endDate && $current > $endDate) {
            break;
        }

        if ($current >= $monthStart && $current <= $monthEnd) {
            $durationSeconds = $eventEnd->getTimestamp() - (new DateTime($event['start_datetime']))->getTimestamp();

            $occurrences[] = [
                'event_id' => $event['event_id'],
                'event_title' => $event['event_title'],
                'occurrence_date' => $current->format('Y-m-d'),
                'occurrence_start' => $current->format('Y-m-d H:i:s'),
                'occurrence_end' => date('Y-m-d H:i:s', $current->getTimestamp() + $durationSeconds),
                'is_recurring' => 1,
                'recurrence_type' => $event['recurrence_type']
            ];
        }

        switch ($event['recurrence_type'])
        {
            case 'daily':
                $current->modify("+{$interval} day");
                break;
            case 'weekly':
                $current->modify("+{$interval} week");
                break;
                case 'monthly':
                $current->modify("+{$interval} month");
                break;
            case 'yearly':
                $current->modify("+{$interval} year");
                break;
            default:
                return $occurrences;
        }

        $count++;
    }

    return $occurrences;
}
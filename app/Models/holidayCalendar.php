<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class holidayCalendar extends Model
{
    protected $fillable = [
        'name',
        'date',
        'type',
        'is_recurring',
        'recurring_pattern',
        'recurring_rules',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'recurring_rules' => 'array',
    ];

    // public static function getForDateRange($startDate, $endDate)
    // {
    //     $start = Carbon::parse($startDate);
    //     $end = Carbon::parse($endDate);

    //     $holidays = self::all();
    //     $instances = [];

    //     foreach ($holidays as $holiday) {
    //         if ($holiday->is_recurring && $holiday->recurring_pattern !== 'none') {
    //             // Generate recurring instances
    //             $instances = array_merge(
    //                 $instances,
    //                 $holiday->generateRecurringInstances($start, $end)
    //             );
    //         } else {
    //             // One-time holiday - check if in range
    //             $holidayDate = Carbon::parse($holiday->date);
    //             if ($holidayDate->between($start, $end)) {
    //                 $instances[] = array_merge($holiday->toArray(), [
    //                     'is_recurring_instance' => false,
    //                 ]);
    //             }
    //         }
    //     }

    //     return collect($instances);
    // }
    /**
     * Generate recurring holiday instances
     */
    public function generateRecurringInstances($rangeStart, $rangeEnd)
    {
        $instances = [];
        $startYear = $rangeStart->year;
        $endYear = $rangeEnd->year;

        for ($year = $startYear; $year <= $endYear; $year++) {
            $instanceDate = $this->calculateDateForYear($year);

            if ($instanceDate && $instanceDate->between($rangeStart, $rangeEnd)) {
                $instances[] = [
                    'id' => $this->id . '_' . $year,
                    'parent_id' => $this->id,
                    'name' => $this->name,
                    'date' => $instanceDate->toDateString(),
                    'type' => $this->type,
                    'description' => $this->description,
                    'is_recurring_instance' => true,
                    'original_date' => $this->date->toDateString(),
                ];
            }
        }

        return $instances;
    }

    /**
     * Calculate the specific date for a given year based on recurrence pattern
     */
    public function calculateDateForYear($year)
    {
        // Decode recurring_rules if it's a string
        $rules = is_string($this->recurring_rules)
            ? json_decode($this->recurring_rules, true)
            : $this->recurring_rules;

        switch ($this->recurring_pattern) {
            case 'yearly':
                // Same month and day every year
                if (isset($rules['month']) && isset($rules['day'])) {
                    return Carbon::createFromDate($year, $rules['month'], $rules['day']);
                }
                // Fallback: use original date's month/day
                $originalDate = Carbon::parse($this->date);
                return Carbon::createFromDate($year, $originalDate->month, $originalDate->day);

            case 'easter_based':
                $easter = $this->getEasterDate($year);
                $offset = $rules['days_offset'] ?? 0;
                return $easter->copy()->addDays($offset);

            case 'floating':
                return $this->getNthWeekdayOfMonth(
                    $year,
                    $rules['month'],
                    $rules['weekday'],
                    $rules['week']
                );

            default:
                // Fallback to original date
                $originalDate = Carbon::parse($this->date);
                return Carbon::createFromDate($year, $originalDate->month, $originalDate->day);
        }
    }

    /**
     * Calculate Easter Sunday date (Gregorian)
     */
    private function getEasterDate($year)
    {
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::createFromDate($year, $month, $day);
    }

    /**
     * Get Nth weekday of a month (e.g., 4th Thursday)
     */
    private function getNthWeekdayOfMonth($year, $month, $weekday, $n)
    {
        $weekdays = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6
        ];

        $firstDay = Carbon::createFromDate($year, $month, 1);
        $targetDayNum = $weekdays[$weekday] ?? 0;

        // Find first occurrence
        $daysUntilFirst = ($targetDayNum - $firstDay->dayOfWeek + 7) % 7;
        $firstOccurrence = $firstDay->copy()->addDays($daysUntilFirst);

        // Add weeks to get Nth occurrence
        return $firstOccurrence->addWeeks($n - 1);
    }
}

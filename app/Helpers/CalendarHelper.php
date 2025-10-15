<?php

namespace App\Helpers;

use Carbon\Carbon;

class CalendarHelper
{
    /**
     * Generate simple HTML calendar for a given month
     */
    public static function generateMonthCalendar($year, $month, $events = [])
    {
        $date = Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $startOfMonth = $date->copy()->startOfMonth();
        $dayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        
        // Adjust to start week on Monday (ISO 8601)
        $startDay = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;
        
        $calendar = [];
        $week = [];
        
        // Fill initial empty days
        for ($i = 0; $i < $startDay; $i++) {
            $week[] = ['day' => null, 'date' => null, 'events' => []];
        }
        
        // Fill days of month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($year, $month, $day);
            $dateStr = $currentDate->format('Y-m-d');
            
            // Get events for this day
            $dayEvents = array_filter($events, function($event) use ($dateStr) {
                try {
                    // Parse event start date - handle various formats
                    $eventStart = $event['start'];
                    
                    // If start contains multiple spaces, take only first part (date + time)
                    if (substr_count($eventStart, ' ') > 1) {
                        $parts = explode(' ', $eventStart);
                        // Take date and first time only (YYYY-MM-DD HH:MM:SS)
                        $eventStart = $parts[0] . ' ' . $parts[1];
                    }
                    
                    $eventDate = Carbon::parse($eventStart)->format('Y-m-d');
                    
                    // Check if event has end date (timeline event)
                    if (isset($event['end'])) {
                        $eventEndDate = Carbon::parse($event['end'])->format('Y-m-d');
                        // Event spans multiple days - check if current date is within range
                        return $dateStr >= $eventDate && $dateStr <= $eventEndDate;
                    }
                    
                    // Single day event
                    return $eventDate === $dateStr;
                } catch (\Exception $e) {
                    // If parsing fails, skip this event
                    return false;
                }
            });
            
            $week[] = [
                'day' => $day,
                'date' => $currentDate,
                'events' => array_values($dayEvents),
                'isToday' => $currentDate->isToday(),
            ];
            
            // Start new week on Sunday
            if (count($week) == 7) {
                $calendar[] = $week;
                $week = [];
            }
        }
        
        // Fill remaining empty days
        while (count($week) < 7 && count($week) > 0) {
            $week[] = ['day' => null, 'date' => null, 'events' => []];
        }
        
        if (count($week) > 0) {
            $calendar[] = $week;
        }
        
        return [
            'weeks' => $calendar,
            'month' => $date->format('F'),
            'year' => $year,
            'monthNum' => $month,
        ];
    }
    
    /**
     * Get color class for event/ticket type
     */
    public static function getEventColorClass($type, $status = null)
    {
        if ($type === 'Tiket') {
            return match($status) {
                'todo' => 'bg-gray-500',
                'doing' => 'bg-blue-500',
                'done' => 'bg-green-500',
                default => 'bg-gray-500',
            };
        }
        
        if ($type === 'Project') {
            return match($status) {
                'planning' => 'bg-gray-600',
                'active' => 'bg-indigo-600',
                'on_hold' => 'bg-yellow-600',
                'completed' => 'bg-green-600',
                default => 'bg-indigo-600',
            };
        }
        
        return 'bg-purple-500'; // Event
    }
}

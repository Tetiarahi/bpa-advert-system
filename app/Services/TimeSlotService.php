<?php

namespace App\Services;

use Carbon\Carbon;

class TimeSlotService
{
    /**
     * Get the current time slot based on the current time
     * 
     * @return string morning|lunch|evening
     */
    public static function getCurrentTimeSlot(): string
    {
        $now = Carbon::now();
        $hour = $now->hour;

        // 5AM - 9AM: Morning
        if ($hour >= 5 && $hour < 9) {
            return 'morning';
        }
        
        // 11AM - 3PM: Lunch
        if ($hour >= 11 && $hour < 15) {
            return 'lunch';
        }
        
        // 4PM - 11PM: Evening
        if ($hour >= 16 && $hour < 23) {
            return 'evening';
        }

        // Outside active hours - return the next upcoming slot
        if ($hour >= 23 || $hour < 5) {
            return 'morning'; // Next slot will be morning
        }
        
        if ($hour >= 9 && $hour < 11) {
            return 'lunch'; // Next slot will be lunch
        }
        
        if ($hour >= 15 && $hour < 16) {
            return 'evening'; // Next slot will be evening
        }

        return 'morning'; // Default fallback
    }

    /**
     * Check if the current time is within active broadcasting hours
     * 
     * @return bool
     */
    public static function isActiveHours(): bool
    {
        $hour = Carbon::now()->hour;
        
        return ($hour >= 5 && $hour < 9) ||    // Morning: 5AM-9AM
               ($hour >= 11 && $hour < 15) ||  // Lunch: 11AM-3PM
               ($hour >= 16 && $hour < 23);    // Evening: 4PM-11PM
    }

    /**
     * Get the time range for a specific time slot
     * 
     * @param string $timeSlot
     * @return array
     */
    public static function getTimeSlotRange(string $timeSlot): array
    {
        switch ($timeSlot) {
            case 'morning':
                return ['start' => 5, 'end' => 9];
            case 'lunch':
                return ['start' => 11, 'end' => 15];
            case 'evening':
                return ['start' => 16, 'end' => 23];
            default:
                return ['start' => 0, 'end' => 24];
        }
    }

    /**
     * Get all time slots
     * 
     * @return array
     */
    public static function getAllTimeSlots(): array
    {
        return ['morning', 'lunch', 'evening'];
    }

    /**
     * Get human-readable time slot name
     * 
     * @param string $timeSlot
     * @return string
     */
    public static function getTimeSlotDisplayName(string $timeSlot): string
    {
        switch ($timeSlot) {
            case 'morning':
                return 'Morning (5AM - 9AM)';
            case 'lunch':
                return 'Lunch (11AM - 3PM)';
            case 'evening':
                return 'Evening (4PM - 11PM)';
            default:
                return ucfirst($timeSlot);
        }
    }

    /**
     * Check if a specific time slot is currently active
     * 
     * @param string $timeSlot
     * @return bool
     */
    public static function isTimeSlotActive(string $timeSlot): bool
    {
        return self::getCurrentTimeSlot() === $timeSlot && self::isActiveHours();
    }
}

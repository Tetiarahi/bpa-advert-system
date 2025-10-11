<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Presenter extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'shift',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function readStatuses()
    {
        return $this->hasMany(PresenterReadStatus::class);
    }

    public function hasRead($contentType, $contentId, $timeSlot = null, $readingNumber = null)
    {
        $query = $this->readStatuses()
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('is_read', true);

        if ($timeSlot) {
            $query->where('time_slot', $timeSlot);
        }

        if ($readingNumber) {
            $query->where('reading_number', $readingNumber);
        }

        return $query->exists();
    }

    public function getReadCount($contentType, $contentId, $timeSlot)
    {
        $readStatus = $this->readStatuses()
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('time_slot', $timeSlot)
            ->first();

        if (!$readStatus || !$readStatus->readings_data) {
            return 0;
        }

        // Count how many readings are marked as read
        $readingsData = $readStatus->readings_data;
        $readCount = 0;

        for ($i = 1; $i <= 9; $i++) {
            if (isset($readingsData[$i]) && $readingsData[$i]['is_read']) {
                $readCount++;
            }
        }

        return $readCount;
    }

    public function getNextReadingNumber($contentType, $contentId, $timeSlot)
    {
        $readCount = $this->getReadCount($contentType, $contentId, $timeSlot);
        return min($readCount + 1, 9); // Maximum 9 readings
    }

    public function markAsRead($contentType, $contentId, $timeSlot, $readingNumber = null)
    {
        if ($readingNumber === null) {
            $readingNumber = $this->getNextReadingNumber($contentType, $contentId, $timeSlot);
        }

        // Don't allow more than 9 readings
        if ($readingNumber > 9) {
            return null;
        }

        // Get or create the read status record
        $readStatus = $this->readStatuses()
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('time_slot', $timeSlot)
            ->first();

        if (!$readStatus) {
            $readStatus = $this->readStatuses()->create([
                'content_type' => $contentType,
                'content_id' => $contentId,
                'time_slot' => $timeSlot,
                'reading_number' => 1,
                'is_read' => false,
                'readings_data' => [],
            ]);
        }

        // Get current readings data and update it
        $readingsData = $readStatus->readings_data ?: [];
        $readingsData[$readingNumber] = [
            'is_read' => true,
            'read_at' => now()->toISOString(),
        ];

        // Calculate total read count
        $totalReadCount = 0;
        for ($i = 1; $i <= 9; $i++) {
            if (isset($readingsData[$i]) && $readingsData[$i]['is_read']) {
                $totalReadCount++;
            }
        }

        // Update the record
        $readStatus->update([
            'readings_data' => $readingsData,
            'is_read' => $totalReadCount > 0,
            'read_at' => $totalReadCount > 0 ? now() : null,
        ]);

        return $readStatus;
    }

    public function markAsUnread($contentType, $contentId, $timeSlot, $readingNumber)
    {
        // Find the read status record
        $readStatus = $this->readStatuses()
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('time_slot', $timeSlot)
            ->first();

        if (!$readStatus) {
            return null;
        }

        // Update the readings data
        $readingsData = $readStatus->readings_data ?: [];
        $readingsData[$readingNumber] = [
            'is_read' => false,
            'read_at' => null,
        ];

        // Update the main fields based on the readings data
        $totalReadCount = 0;
        for ($i = 1; $i <= 9; $i++) {
            if (isset($readingsData[$i]) && $readingsData[$i]['is_read']) {
                $totalReadCount++;
            }
        }

        $readStatus->update([
            'readings_data' => $readingsData,
            'is_read' => $totalReadCount > 0,
            'read_at' => $totalReadCount > 0 ? now() : null,
        ]);

        return $readStatus;
    }

    public function getUnreadCountByTimeSlot($timeSlot, $advertisements, $gongs)
    {
        $unreadCount = 0;

        // Count unread advertisements for this time slot
        foreach ($advertisements as $ad) {
            $freqField = $timeSlot . '_frequency';
            if (isset($ad->$freqField) && $ad->$freqField > 0) {
                $readCount = $this->getReadCount('advertisement', $ad->id, $timeSlot);
                $requiredReadings = min($ad->$freqField, 9); // Max 9 readings
                if ($readCount < $requiredReadings) {
                    $unreadCount += ($requiredReadings - $readCount);
                }
            }
        }

        // Count unread gongs for this time slot
        foreach ($gongs as $gong) {
            $freqField = $timeSlot . '_frequency';
            if (isset($gong->$freqField) && $gong->$freqField > 0) {
                $readCount = $this->getReadCount('gong', $gong->id, $timeSlot);
                $requiredReadings = min($gong->$freqField, 9); // Max 9 readings
                if ($readCount < $requiredReadings) {
                    $unreadCount += ($requiredReadings - $readCount);
                }
            }
        }

        return $unreadCount;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'shift', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Presenter {$eventName}")
            ->useLogName('presenter');
    }
}

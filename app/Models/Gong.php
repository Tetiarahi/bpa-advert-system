<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Gong extends Model
{
    use LogsActivity;
    protected $fillable = [
        'customer_id',
        'departed_name',
        'death_date',
        'published_date',
        'broadcast_start_date',
        'broadcast_end_date',
        'band',
        'contents',
        'song_title',
        'morning_frequency',
        'morning_times',
        'lunch_frequency',
        'lunch_times',
        'evening_frequency',
        'evening_times',
        'broadcast_days',
        'broadcast_notes',
        'amount',
        'is_paid',
        'attachment',
        'is_read',
    ];

    protected $casts = [
        'death_date' => 'date',
        'published_date' => 'date',
        'broadcast_start_date' => 'date',
        'broadcast_end_date' => 'date',
        'broadcast_days' => 'array',
        'band' => 'array',
        'morning_times' => 'array',
        'lunch_times' => 'array',
        'evening_times' => 'array',
        'is_paid' => 'boolean',
        'is_read' => 'boolean',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get cleaned content without empty HTML tags
     */
    public function getCleanContentsAttribute()
    {
        return $this->cleanHtmlContent($this->contents);
    }

    /**
     * Get plain text content for display
     */
    public function getPlainContentsAttribute()
    {
        return strip_tags($this->cleanHtmlContent($this->contents));
    }

    /**
     * Clean HTML content by removing empty tags and formatting
     */
    private function cleanHtmlContent($content)
    {
        if (empty($content)) {
            return '';
        }

        // Remove empty paragraph tags
        $content = preg_replace('/<p[^>]*>(\s|&nbsp;)*<\/p>/i', '', $content);

        // Remove empty div tags
        $content = preg_replace('/<div[^>]*>(\s|&nbsp;)*<\/div>/i', '', $content);

        // Remove empty span tags
        $content = preg_replace('/<span[^>]*>(\s|&nbsp;)*<\/span>/i', '', $content);

        // Remove multiple consecutive line breaks
        $content = preg_replace('/(<br\s*\/?>\s*){3,}/i', '<br><br>', $content);

        // Clean up whitespace
        $content = trim($content);

        return $content;
    }

    // Accessors for frequency shorthand (morning_freq, lunch_freq, evening_freq)
    public function getMorningFreqAttribute()
    {
        return $this->morning_frequency ?? 0;
    }

    public function getLunchFreqAttribute()
    {
        return $this->lunch_frequency ?? 0;
    }

    public function getEveningFreqAttribute()
    {
        return $this->evening_frequency ?? 0;
    }

    // Accessor for band field (handles JSON string from database)
    public function getBandAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [$value];
        }
        return is_array($value) ? $value : [$value];
    }

    // Accessor for export-friendly band display
    public function getBandDisplayAttribute()
    {
        $band = $this->band;
        if (is_array($band)) {
            return implode(', ', $band);
        }
        return (string) $band;
    }

    // Accessor for broadcast_days field (handles JSON string from database)
    public function getBroadcastDaysAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [$value];
        }
        return is_array($value) ? $value : [$value];
    }

    // Accessor for export-friendly broadcast days display
    public function getBroadcastDaysDisplayAttribute()
    {
        $broadcastDays = $this->broadcast_days;
        if (is_array($broadcastDays)) {
            return implode(', ', $broadcastDays);
        }
        return (string) $broadcastDays;
    }

    // Override toArray to ensure array fields are converted to strings for export
    public function toArray()
    {
        $array = parent::toArray();

        // Convert array fields to strings for export compatibility
        if (isset($array['band']) && is_array($array['band'])) {
            $array['band'] = implode(', ', $array['band']);
        }

        if (isset($array['broadcast_days']) && is_array($array['broadcast_days'])) {
            $array['broadcast_days'] = implode(', ', $array['broadcast_days']);
        }

        if (isset($array['attachment']) && is_array($array['attachment'])) {
            $array['attachment'] = implode(', ', $array['attachment']);
        }

        return $array;
    }

    // Override attributesToArray to handle export at a deeper level
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        // Convert array fields to strings for export compatibility
        if (isset($attributes['band']) && is_array($attributes['band'])) {
            $attributes['band'] = implode(', ', $attributes['band']);
        }

        if (isset($attributes['broadcast_days']) && is_array($attributes['broadcast_days'])) {
            $attributes['broadcast_days'] = implode(', ', $attributes['broadcast_days']);
        }

        if (isset($attributes['attachment']) && is_array($attributes['attachment'])) {
            $attributes['attachment'] = implode(', ', $attributes['attachment']);
        }

        return $attributes;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_id', 'departed_name', 'death_date', 'published_date', 'contents', 'song_title', 'amount', 'is_paid'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Gong {$eventName}")
            ->useLogName('gong');
    }

}

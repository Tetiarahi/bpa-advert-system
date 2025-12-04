<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Advertisement extends Model
{
    use LogsActivity;
    protected $fillable = [
        'customer_id',
        'customer_type',
        'ads_category_id',
        'band',
        'title',
        'content',
        'issued_date',
        'broadcast_start_date',
        'broadcast_end_date',
        'broadcast_times',
        'daily_frequency',
        'morning_frequency',
        'morning_times',
        'lunch_frequency',
        'lunch_times',
        'evening_frequency',
        'evening_times',
        'broadcast_days',
        'broadcast_notes',
        'is_paid',
        'amount',
        'attachment',
        'is_read',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'broadcast_start_date' => 'date',
        'broadcast_end_date' => 'date',
        'broadcast_times' => 'array',
        'broadcast_days' => 'array',
        'morning_times' => 'array',
        'lunch_times' => 'array',
        'evening_times' => 'array',
        'is_paid' => 'boolean',
        'is_read' => 'boolean',
        'band' => 'array',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function adsCategory()
    {
        return $this->belongsTo(AdsCategory::class);
    }

    /**
     * Get cleaned content without empty HTML tags
     */
    public function getCleanContentAttribute()
    {
        return $this->cleanHtmlContent($this->content);
    }

    /**
     * Get plain text content for display
     */
    public function getPlainContentAttribute()
    {
        return strip_tags($this->cleanHtmlContent($this->content));
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

    // Accessor for export-friendly band display
    public function getBandDisplayAttribute()
    {
        if (is_array($this->band)) {
            return implode(', ', $this->band);
        }
        return (string) $this->band;
    }

    // Accessor for export-friendly broadcast days display
    public function getBroadcastDaysDisplayAttribute()
    {
        if (is_array($this->broadcast_days)) {
            return implode(', ', $this->broadcast_days);
        }
        return (string) $this->broadcast_days;
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

        return $array;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_id', 'customer_type', 'ads_category_id', 'title', 'content', 'issued_date', 'is_paid', 'amount'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Advertisement {$eventName}")
            ->useLogName('advertisement');
    }
}

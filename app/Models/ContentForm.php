<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContentForm extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'content_type',
        'content_id',
        'customer_id',
        'title',
        'content_summary',
        'word_count',
        'source',
        'received_date',
        'amount',
        'is_paid',
        'band',
        'broadcast_start_date',
        'broadcast_end_date',
        'broadcast_days',
        'morning_frequency',
        'lunch_frequency',
        'evening_frequency',
        'morning_ticked_at',
        'lunch_ticked_at',
        'evening_ticked_at',
        'morning_tick_count',
        'lunch_tick_count',
        'evening_tick_count',
        'morning_tick_times',
        'lunch_tick_times',
        'evening_tick_times',
        'morning_readings',
        'lunch_readings',
        'evening_readings',
        'presenter_id',
        'presenter_shift',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'band' => 'array',
        'broadcast_days' => 'array',
        'broadcast_start_date' => 'date',
        'broadcast_end_date' => 'date',
        'received_date' => 'datetime',
        'morning_ticked_at' => 'datetime',
        'lunch_ticked_at' => 'datetime',
        'evening_ticked_at' => 'datetime',
        'morning_tick_times' => 'array',
        'lunch_tick_times' => 'array',
        'evening_tick_times' => 'array',
        'morning_readings' => 'array',
        'lunch_readings' => 'array',
        'evening_readings' => 'array',
        'completed_at' => 'datetime',
        'is_paid' => 'boolean',
        'is_completed' => 'boolean',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function presenter()
    {
        return $this->belongsTo(Presenter::class);
    }

    public function logs()
    {
        return $this->hasMany(ContentFormLog::class);
    }

    public function content()
    {
        if ($this->content_type === 'advertisement') {
            return $this->belongsTo(Advertisement::class, 'content_id');
        } elseif ($this->content_type === 'gong') {
            return $this->belongsTo(Gong::class, 'content_id');
        }
        return null;
    }

    // Accessors
    public function getReadingProgressAttribute()
    {
        $total = $this->morning_frequency + $this->lunch_frequency + $this->evening_frequency;
        $completed = $this->morning_tick_count + $this->lunch_tick_count + $this->evening_tick_count;

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    public function getStatusAttribute()
    {
        if ($this->is_completed) {
            return 'Completed';
        }

        $total = $this->morning_frequency + $this->lunch_frequency + $this->evening_frequency;
        $completed = $this->morning_tick_count + $this->lunch_tick_count + $this->evening_tick_count;

        if ($completed === 0) {
            return 'Not Started';
        } elseif ($completed < $total) {
            return 'In Progress';
        }

        return 'Pending';
    }

    // Activity Logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'content_type',
                'content_id',
                'customer_id',
                'title',
                'word_count',
                'source',
                'amount',
                'morning_tick_count',
                'lunch_tick_count',
                'evening_tick_count',
                'is_completed'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Content Form {$eventName}")
            ->useLogName('content_form');
    }
}

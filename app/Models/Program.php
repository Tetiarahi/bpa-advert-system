<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Program extends Model
{
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'customer_type',
        'radio_program',
        'band',
        'publish_start_date',
        'publish_end_date',
        'payment_status',
        'amount',
        'attachment',
        'staff_id',
    ];

    protected $casts = [
        'publish_start_date' => 'date',
        'publish_end_date' => 'date',
        'payment_status' => 'boolean',
        'band' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_id', 'customer_type', 'radio_program', 'band', 'publish_start_date', 'publish_end_date', 'payment_status', 'amount', 'staff_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Program {$eventName}")
            ->useLogName('program');
    }
}

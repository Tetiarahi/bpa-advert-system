<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    use LogsActivity;
    protected $fillable = [
        'fullname',
        'Organization',
        'email',
        'phone',
        'address',
    ];
    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }
    public function gongs()
    {
        return $this->hasMany(Gong::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fullname', 'email', 'phone', 'address'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Customer {$eventName}")
            ->useLogName('customer');
    }
}

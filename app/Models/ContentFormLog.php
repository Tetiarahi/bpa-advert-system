<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentFormLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_form_id',
        'presenter_id',
        'action',
        'time_slot',
        'action_at',
        'ip_address',
        'user_agent',
        'reading_number',
        'notes',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    // Relationships
    public function contentForm()
    {
        return $this->belongsTo(ContentForm::class);
    }

    public function presenter()
    {
        return $this->belongsTo(Presenter::class);
    }

    // Scopes
    public function scopeByTimeSlot($query, $timeSlot)
    {
        return $query->where('time_slot', $timeSlot);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByPresenter($query, $presenterId)
    {
        return $query->where('presenter_id', $presenterId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('action_at', '>=', now()->subDays($days));
    }
}

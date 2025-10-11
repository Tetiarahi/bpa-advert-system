<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PresenterReadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'presenter_id',
        'content_type',
        'content_id',
        'time_slot',
        'reading_number',
        'is_read',
        'read_at',
        'readings_data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'readings_data' => 'array',
    ];

    public function presenter()
    {
        return $this->belongsTo(Presenter::class);
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
}

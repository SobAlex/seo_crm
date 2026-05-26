<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningFact extends Model
{
    use HasFactory;

    protected $fillable = [
        'planning_id', 'week_number', 'week_start', 'week_end', 'days_in_week',
        'actual_value', 'manual_value', 'source', 'synced_at', 'manual_override_at',
        'manual_override_by_id'
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'actual_value' => 'decimal:2',
        'manual_value' => 'decimal:2',
        'synced_at' => 'datetime',
        'manual_override_at' => 'datetime',
    ];

    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }

    public function manualOverrideBy()
    {
        return $this->belongsTo(User::class, 'manual_override_by_id');
    }
}

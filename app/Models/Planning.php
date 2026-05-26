<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id', 'track_id', 'title', 'period_start', 'period_end',
        'metric_type', 'metric_label', 'target_value', 'alert_threshold',
        'completed_notification_sent'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'target_value' => 'decimal:2',
        'alert_threshold' => 'decimal:2',
        'completed_notification_sent' => 'boolean',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function facts()
    {
        return $this->hasMany(PlanningFact::class);
    }
}

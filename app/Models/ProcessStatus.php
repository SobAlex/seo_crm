<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'business_process_id', 'color', 'sort_order', 'is_start_status', 'is_end_status', 'team_id'
    ];

    protected $casts = [
        'is_start_status' => 'boolean',
        'is_end_status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function businessProcess()
    {
        return $this->belongsTo(BusinessProcess::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('team', function (Builder $builder) {
            if (auth()->check() && auth()->user()->team_id) {
                $builder->where('team_id', auth()->user()->team_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->team_id) {
                $model->team_id = auth()->user()->team_id;
            }
        });
    }
}

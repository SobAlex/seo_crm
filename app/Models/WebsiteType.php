<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'default_metrics', 'icon', 'sort_order', 'team_id'
    ];

    protected $casts = [
        'default_metrics' => 'array',
    ];

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id', 'keyword', 'frequency', 'difficulty', 'current_position', 'target_position', 'team_id'
    ];

    protected $casts = [
        'frequency' => 'integer',
        'difficulty' => 'integer',
        'current_position' => 'integer',
        'target_position' => 'integer',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
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

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

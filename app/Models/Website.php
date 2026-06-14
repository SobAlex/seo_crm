<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'url', 'project_id', 'website_type_id', 'cms', 'region', 'team_id', 'topvisor_project_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function websiteType()
    {
        return $this->belongsTo(WebsiteType::class);
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

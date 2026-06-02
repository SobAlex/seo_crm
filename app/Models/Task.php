<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'track_id', 'status_id', 'priority',
        'deadline', 'assignee_user_id', 'assignee_contractor_id', 'created_by_id',
        'checklist', 'structure', 'files', 'completed_at', 'team_id'
    ];

    protected $casts = [
        'deadline' => 'date',
        'completed_at' => 'datetime',
        'checklist' => 'array',
        'structure' => 'array',
        'files' => 'array',
        'priority' => 'string',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function status()
    {
        return $this->belongsTo(ProcessStatus::class, 'status_id');
    }

    public function assigneeUser()
    {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    public function assigneeContractor()
    {
        return $this->belongsTo(User::class, 'assignee_contractor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'task_keyword');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
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

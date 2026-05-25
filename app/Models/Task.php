<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'track_id', 'status_id', 'priority',
        'deadline', 'assignee_user_id', 'assignee_contractor_id', 'created_by_id',
        'checklist', 'structure', 'files', 'completed_at'
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
        return $this->belongsTo(Contractor::class, 'assignee_contractor_id');
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
}

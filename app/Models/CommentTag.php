<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'color', 'is_system', 'team_id'
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'comment_comment_tag');
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

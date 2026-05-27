<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'color', 'is_system'
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'comment_comment_tag');
    }
}

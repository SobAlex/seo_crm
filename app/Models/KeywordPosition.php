<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id', 'search_engine', 'region', 'position', 'url', 'checked_at'
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'position' => 'integer',
    ];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id', 'keyword', 'frequency', 'difficulty', 'current_position', 'target_position'
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
}

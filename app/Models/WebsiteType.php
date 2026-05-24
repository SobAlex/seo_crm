<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'default_metrics', 'icon', 'sort_order'
    ];

    protected $casts = [
        'default_metrics' => 'array',
    ];
}

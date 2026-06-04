<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetrikaCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id', 'counter_id', 'token', 'token_expires_at', 'goals', 'last_sync_at', 'sync_status'
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'goals' => 'array',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}

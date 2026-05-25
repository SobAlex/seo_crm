<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'business_process_id', 'color', 'sort_order', 'is_start_status', 'is_end_status'
    ];

    protected $casts = [
        'is_start_status' => 'boolean',
        'is_end_status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function businessProcess()
    {
        return $this->belongsTo(BusinessProcess::class);
    }
}

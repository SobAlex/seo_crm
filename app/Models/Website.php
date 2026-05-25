<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'url', 'project_id', 'website_type_id', 'cms', 'region'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function websiteType()
    {
        return $this->belongsTo(WebsiteType::class);
    }
}

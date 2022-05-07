<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
        'thumbnail',
        'status',
        'type',
        'is_featured',
        'user_id',
        'market_id',
        'external_id',
        'source',
        'images_processing_status',
        'images_processing_status_text',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ads/'.$this->getKey());
    }
}

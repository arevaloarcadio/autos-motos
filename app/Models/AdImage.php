<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdImage extends Model
{
     use \App\Traits\TraitUuid;
     
    protected $fillable = [
        'ad_id',
        'path',
        'is_external',
        'order_index',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-images/'.$this->getKey());
    }
}

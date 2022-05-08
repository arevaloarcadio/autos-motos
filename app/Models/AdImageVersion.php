<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdImageVersion extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'ad_image_id',
        'name',
        'path',
        'is_external',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-image-versions/'.$this->getKey());
    }
}

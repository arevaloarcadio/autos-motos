<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileHomeAdOption extends Model
{
    protected $fillable = [
        'mobile_home_ad_id',
        'option_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/mobile-home-ad-options/'.$this->getKey());
    }
}

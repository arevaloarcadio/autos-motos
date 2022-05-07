<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoAdOption extends Model
{
    protected $fillable = [
        'auto_ad_id',
        'auto_option_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/auto-ad-options/'.$this->getKey());
    }
}

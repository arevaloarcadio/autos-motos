<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdMake extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'ad_type',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-makes/'.$this->getKey());
    }
}

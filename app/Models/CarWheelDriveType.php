<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarWheelDriveType extends Model
{
    protected $fillable = [
        'internal_name',
        'slug',
        'external_name',
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
        return url('/admin/car-wheel-drive-types/'.$this->getKey());
    }
}

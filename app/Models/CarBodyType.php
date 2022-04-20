<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBodyType extends Model
{
    protected $fillable = [
        'internal_name',
        'slug',
        'icon_url',
        'external_name',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/car-body-types/'.$this->getKey());
    }
}

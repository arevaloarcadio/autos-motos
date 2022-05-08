<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'name',
        'slug',
        'car_make_id',
        'external_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/car-models/'.$this->getKey());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarMake extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'name',
        'slug',
        'external_id',
        'is_active',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/car-makes/'.$this->getKey());
    }
}

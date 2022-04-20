<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarTransmissionType extends Model
{
    use \App\Traits\TraitUuid;
    
    protected $fillable = [
        'internal_name',
        'slug',
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
        return url('/admin/car-transmission-types/'.$this->getKey());
    }
}

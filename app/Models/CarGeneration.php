<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarGeneration extends Model
{
    protected $fillable = [
        'name',
        'year',
        'car_model_id',
        'external_id',
    
    ];
    
    
    protected $dates = [
        'year',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/car-generations/'.$this->getKey());
    }
}

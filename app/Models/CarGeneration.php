<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarGeneration extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
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

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id', 'id');
    }
    
    /**
     * @return HasMany
     */
    public function specs()
    {
        return $this->hasMany(CarSpec::class, 'car_generation_id', 'id');
    }
}

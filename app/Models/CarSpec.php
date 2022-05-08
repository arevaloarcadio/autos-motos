<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarSpec extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'car_make_id',
        'car_model_id',
        'car_generation_id',
        'car_body_type_id',
        'engine',
        'doors',
        'doors_min',
        'doors_max',
        'power_hp',
        'power_rpm',
        'power_rpm_min',
        'power_rpm_max',
        'engine_displacement',
        'production_start_year',
        'production_end_year',
        'car_fuel_type_id',
        'car_transmission_type_id',
        'gears',
        'car_wheel_drive_type_id',
        'battery_capacity',
        'electric_power_hp',
        'electric_power_rpm',
        'electric_power_rpm_min',
        'electric_power_rpm_max',
        'external_id',
        'last_external_update',
    
    ];
    
    
    protected $dates = [
        'production_start_year',
        'production_end_year',
        'last_external_update',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/car-specs/'.$this->getKey());
    }
}

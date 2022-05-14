<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarSpec extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
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

    
    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id', 'id');
    }
    
    /**
     * @return BelongsTo
     */
    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id', 'id');
    }
    
    /**
     * @return BelongsTo
     */
    public function generation()
    {
        return $this->belongsTo(CarGeneration::class, 'car_generation_id', 'id');
    }
    
    /**
     * @return BelongsTo
     */
    public function bodyType()
    {
        return $this->belongsTo(CarBodyType::class, 'car_body_type_id', 'id');
    }
    
    /**
     * @return BelongsTo
     */
    public function transmissionType()
    {
        return $this->belongsTo(CarTransmissionType::class, 'car_transmission_type_id', 'id');
    }
    
    /**
     * @return BelongsTo
     */
    public function fuelType()
    {
        return $this->belongsTo(CarFuelType::class, 'car_fuel_type_id', 'id');
    }
    
    /**
     * @return BelongsTo
     */
    public function wheelDriveType()
    {
        return $this->belongsTo(CarWheelDriveType::class, 'car_wheel_drive_type_id', 'id');
    }
    
    /**
     * @return string
     */
    public function getBodyTypeNameAttribute(): string
    {
        return $this->bodyType->name;
    }
    
    /**
     * @return string
     */
    public function getFuelTypeNameAttribute(): string
    {
        return $this->fuelType->name;
    }
    
    /**
     * @return string
     */
    public function getWheelDriveTypeNameAttribute(): string
    {
        return $this->wheelDriveType->name;
    }
    
    /**
     * @return string
     */
    public function getTransmissionTypeNameAttribute(): string
    {
        return $this->transmissionType->name;
    }
    
    /**
     * @return string
     */
    public function getGenerationNameAttribute(): string
    {
        return $this->generation->name;
    }
    
    /**
     * @return string|null
     */
    public function getFormattedEngineDisplacementAttribute(): ?string
    {
        if (null === $this->engine_displacement) {
            return null;
        }
        
        return number_format(floatval($this->engine_displacement));
    }
}

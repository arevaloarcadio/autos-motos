<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoAd extends Model
{
    protected $fillable = [
        'ad_id',
        'price',
        'price_contains_vat',
        'vin',
        'doors',
        'mileage',
        'exterior_color',
        'interior_color',
        'condition',
        'dealer_id',
        'dealer_show_room_id',
        'first_name',
        'last_name',
        'email_address',
        'address',
        'zip_code',
        'city',
        'country',
        'mobile_number',
        'landline_number',
        'whatsapp_number',
        'youtube_link',
        'ad_fuel_type_id',
        'ad_body_type_id',
        'ad_transmission_type_id',
        'ad_drive_type_id',
        'first_registration_month',
        'first_registration_year',
        'engine_displacement',
        'power_hp',
        'owners',
        'inspection_valid_until_month',
        'inspection_valid_until_year',
        'make_id',
        'model_id',
        'generation_id',
        'series_id',
        'trim_id',
        'equipment_id',
        'additional_vehicle_info',
        'seats',
        'fuel_consumption',
        'co2_emissions',
        'latitude',
        'longitude',
        'geocoding_status',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/auto-ads/'.$this->getKey());
    }
}

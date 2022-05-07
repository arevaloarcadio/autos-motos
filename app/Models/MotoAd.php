<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotoAd extends Model
{
    protected $fillable = [
        'ad_id',
        'make_id',
        'custom_make',
        'model_id',
        'custom_model',
        'fuel_type_id',
        'body_type_id',
        'transmission_type_id',
        'drive_type_id',
        'first_registration_month',
        'first_registration_year',
        'inspection_valid_until_month',
        'inspection_valid_until_year',
        'last_customer_service_month',
        'last_customer_service_year',
        'owners',
        'weight_kg',
        'engine_displacement',
        'mileage',
        'power_kw',
        'gears',
        'cylinders',
        'emission_class',
        'fuel_consumption',
        'co2_emissions',
        'condition',
        'color',
        'price',
        'price_contains_vat',
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
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/moto-ads/'.$this->getKey());
    }
}

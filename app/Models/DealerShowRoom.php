<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerShowRoom extends Model
{
    protected $fillable = [
        'name',
        'address',
        'zip_code',
        'city',
        'country',
        'latitude',
        'longitude',
        'email_address',
        'mobile_number',
        'landline_number',
        'whatsapp_number',
        'dealer_id',
        'market_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/dealer-show-rooms/'.$this->getKey());
    }
}

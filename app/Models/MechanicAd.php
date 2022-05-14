<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MechanicAd extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'ad_id',
        'address',
        'latitude',
        'longitude',
        'zip_code',
        'city',
        'country',
        'mobile_number',
        'whatsapp_number',
        'website_url',
        'email_address',
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
        return url('/admin/mechanic-ads/'.$this->getKey());
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return sprintf(
            '%s, %s, %s, %s',
            $this->address,
            $this->zip_code,
            $this->city,
            ucfirst(mb_strtolower($this->country))
        );
    }
}

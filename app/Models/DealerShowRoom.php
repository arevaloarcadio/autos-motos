<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerShowRoom extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
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

    /**
     * @return BelongsTo
     */
    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * @return string
     */
    public function getShortAddressAttribute(): string
    {
        return sprintf(
            '%s, %s, %s',
            $this->zip_code,
            $this->city,
            $this->country
        );
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
            $this->country
        );

    }
}
